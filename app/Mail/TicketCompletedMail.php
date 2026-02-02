<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCompletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $reviewUrl = route('client.tickets.review', $this->ticket->id);

        return $this->subject('Ticket Completed - ' . $this->ticket->ticket_number)
            ->view('emails.ticket-completed')
            ->with([
                'ticketNumber' => $this->ticket->ticket_number,
                'subject' => $this->ticket->subject,
                'supportType' => $this->ticket->support_type,
                'remarks' => $this->ticket->remarks,
                'completedAt' => $this->ticket->completed_at->format('F d, Y h:i A'),
                'reviewUrl' => $reviewUrl,
            ]);
    }
}
