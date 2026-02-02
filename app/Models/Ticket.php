<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use App\Models\TicketReviewHistory;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'solving_time' => 'datetime',
        'received_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Ensure a provisional ticket_number is present before insert to satisfy NOT NULL constraint
        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TMP-' . date('ym') . '-' . substr(uniqid('', true), -4);
            }
        });

        // After the ticket is created (and has an ID), finalize the formatted ticket number
        static::created(function ($ticket) {
            // Determine company code from the client's company name
            $companyCode = 'CLI';
            $user = User::find($ticket->client_id);
            if ($user && $user->company && !empty($user->company->name)) {
                $companyCode = self::makeCompanyCode($user->company->name);
            }

            $yy = date('y');
            $mm = date('m');
            $idPart = str_pad((string) $ticket->id, 2, '0', STR_PAD_LEFT);

            $finalNumber = $companyCode . '-' . $yy . '-' . $mm . '-' . $idPart;
            // Update directly to avoid firing model events
            DB::table('tickets')->where('id', $ticket->id)->update([
                'ticket_number' => $finalNumber,
            ]);
        });
    }

    protected static function makeCompanyCode(string $name): string
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            return 'CLI';
        }
        $words = preg_split('/\s+/', $trimmed);
        if (is_array($words) && count($words) > 1) {
            $code = '';
            foreach (array_slice($words, 0, 3) as $w) {
                $code .= strtoupper(substr($w, 0, 1));
            }
            return $code !== '' ? $code : 'CLI';
        }
        // Single word company name: take first 3 letters
        return strtoupper(substr($trimmed, 0, 3));
    }

    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function supportUser()
    {
        return $this->belongsTo(User::class, 'support_user_id');
    }

    public function review()
    {
        return $this->hasOne(TicketReview::class);
    }

    public function reviewHistories()
    {
        return $this->hasMany(TicketReviewHistory::class)->orderBy('created_at', 'desc');
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class)->orderBy('created_at', 'desc');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeReceived($query)
    {
        return $query->where('status', 'Receive');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Complete');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('support_type', $type);
    }

    // Check if ticket can be edited by client
    public function canBeEditedByClient()
    {
        return $this->status === 'Pending' && is_null($this->received_at);
    }

    // Check if ticket is completed
    public function isCompleted()
    {
        return $this->status === 'Complete';
    }
}
