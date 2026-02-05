<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketFeedback extends Model
{
    use HasFactory;

    protected $table = 'ticket_feedbacks';
    protected $guarded = [];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function supportUser()
    {
        return $this->belongsTo(User::class, 'support_user_id');
    }
}
