<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReview;
use App\Models\TicketActivity;
use App\Models\TicketReviewHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class ClientTicketController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        $tickets = Ticket::where('client_id', $user->id)
            ->with(['supportUser', 'review'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $tickets->count(),
            'pending' => $tickets->where('status', 'Pending')->count(),
            'received' => $tickets->where('status', 'Receive')->count(),
            'in_progress' => $tickets->where('status', 'Send to Logic')->count(),
            'completed' => $tickets->where('status', 'Complete')->count(),
        ];

        return view('backend.tickets.client.dashboard', compact('tickets', 'stats'));
    }

    public function reports(Request $request)
    {
        $query = Ticket::with(['supportUser', 'review'])
            ->where('client_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20)->appends($request->query());

        return view('backend.tickets.client.reports', compact('tickets'));
    }

    public function downloadReport(Request $request)
    {
        $query = Ticket::with(['supportUser'])
            ->where('client_id', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        $fileName = 'my_ticket_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($tickets) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Ticket #',
                'Subject',
                'Support Type',
                'Status',
                'Created At',
                'Completed At',
            ]);

            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->ticket_number,
                    $ticket->subject,
                    $ticket->support_type,
                    $ticket->status,
                    $ticket->created_at ? $ticket->created_at->format('Y-m-d H:i') : '',
                    $ticket->completed_at ? $ticket->completed_at->format('Y-m-d H:i') : '',
                ]);
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function create()
    {
        return view('backend.tickets.client.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'support_type' => 'required|in:ERP Support,IT Support,Programmer Support',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:10240', // Max 10MB
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('ticket_attachments', 'public');
        }

        $ticket = Ticket::create([
            'client_id' => Auth::id(),
            'support_type' => $validated['support_type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'file_path' => $filePath,
            'status' => 'Pending',
        ]);

        return redirect()->route('client.tickets.dashboard')
            ->with('success', 'Ticket created successfully! Ticket Number: ' . $ticket->ticket_number);
    }

    public function show(Ticket $ticket)
    {
        // Ensure client can only see their own tickets
        if ((int) $ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $ticket->load([
            'supportUser',
            'review',
            'reviewHistories',
            'activities' => function ($query) {
                $query->with('user');
            },
        ]);

        return view('backend.tickets.client.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        // Ensure client can only edit their own tickets
        if ((int) $ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if ticket can be edited
        if (!$ticket->canBeEditedByClient()) {
            return redirect()->route('client.tickets.dashboard')
                ->with('error', 'This ticket cannot be edited as it has been received by support team.');
        }

        return view('backend.tickets.client.edit', compact('ticket'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        // Ensure client can only update their own tickets
        if ((int) $ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if ticket can be edited
        if (!$ticket->canBeEditedByClient()) {
            return redirect()->route('client.tickets.dashboard')
                ->with('error', 'This ticket cannot be updated as it has been received by support team.');
        }

        $validated = $request->validate([
            'support_type' => 'required|in:ERP Support,IT Support,Programmer Support',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,zip|max:10240',
        ]);

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($ticket->file_path) {
                Storage::disk('public')->delete($ticket->file_path);
            }
            $validated['file_path'] = $request->file('file')->store('ticket_attachments', 'public');
        }

        $ticket->update($validated);

        return redirect()->route('client.tickets.dashboard')
            ->with('success', 'Ticket updated successfully!');
    }

    public function destroy(Ticket $ticket)
    {
        // Ensure client can only delete their own tickets
        if ((int) $ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if ticket can be deleted
        if (!$ticket->canBeEditedByClient()) {
            return redirect()->route('client.tickets.dashboard')
                ->with('error', 'This ticket cannot be deleted as it has been received by support team.');
        }

        // Delete file if exists
        if ($ticket->file_path) {
            Storage::disk('public')->delete($ticket->file_path);
        }

        $ticket->delete();

        return redirect()->route('client.tickets.dashboard')
            ->with('success', 'Ticket deleted successfully!');
    }

    public function showReviewForm(Ticket $ticket)
    {
        // Ensure client can only review their own tickets
        if ((int) $ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if ticket is completed
        if (!$ticket->isCompleted()) {
            return redirect()->route('client.tickets.dashboard')
                ->with('error', 'You can only review completed tickets.');
        }

        // Allow revision only if existing review is dissatisfied
        if ($ticket->review && $ticket->review->rating !== 'Dissatisfied') {
            return redirect()->route('client.tickets.dashboard')
                ->with('info', 'You have already reviewed this ticket.');
        }

        return view('backend.tickets.client.review', [
            'ticket' => $ticket,
            'existingReview' => $ticket->review,
        ]);
    }

    public function submitReview(Request $request, Ticket $ticket)
    {
        // Ensure client can only review their own tickets
        if ((int) $ticket->client_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Check if ticket is completed
        if (!$ticket->isCompleted()) {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You can only review completed tickets.'], 400);
            }
            return redirect()->route('client.tickets.dashboard')
                ->with('error', 'You can only review completed tickets.');
        }

        $existingReview = $ticket->review;

        // Allow revision only when prior review is dissatisfied
        if ($existingReview && $existingReview->rating !== 'Dissatisfied') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'You have already reviewed this ticket.'], 400);
            }
            return redirect()->route('client.tickets.dashboard')
                ->with('info', 'You have already reviewed this ticket.');
        }

        $validated = $request->validate([
            'rating' => 'required|in:Satisfied,Dissatisfied',
            'reason' => 'nullable|string',
            'feedback' => 'required_if:rating,Dissatisfied|nullable|string',
        ]);

        if ($existingReview) {
            // Archive previous review
            TicketReviewHistory::create([
                'ticket_id' => $ticket->id,
                'client_id' => $existingReview->client_id,
                'rating' => $existingReview->rating,
                'reason' => $existingReview->reason,
                'feedback' => $existingReview->feedback,
                'reviewed_at' => $existingReview->created_at,
            ]);

            $previousRating = $existingReview->rating;

            $existingReview->update([
                'rating' => $validated['rating'],
                'reason' => $validated['reason'] ?? null,
                'feedback' => $validated['feedback'] ?? null,
            ]);

            // If client marks as dissatisfied, set ticket status to Revise so support can re-open
            if ($validated['rating'] === 'Dissatisfied') {
                $ticket->status = 'Revise';
                $ticket->save();
            }

            $activityDescription = 'Review revised: ' . $validated['rating'] . ' (previous ' . $previousRating . ') - ' . ($validated['feedback'] ?? $validated['reason'] ?? 'No comment');
            $message = 'Your review has been updated. Thank you!';
        } else {
            TicketReview::create([
                'ticket_id' => $ticket->id,
                'client_id' => Auth::id(),
                'rating' => $validated['rating'],
                'reason' => $validated['reason'] ?? null,
                'feedback' => $validated['feedback'] ?? null,
            ]);

            $activityDescription = $validated['rating'] . ' - ' . ($validated['feedback'] ?? $validated['reason'] ?? 'No comment');
            $message = 'Thank you for your review!';
        }

        // If new review is Dissatisfied, mark ticket status as Revise so support sees it
        if (!$existingReview && $validated['rating'] === 'Dissatisfied') {
            $ticket->status = 'Revise';
            $ticket->save();
        }

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'review_submitted',
            'description' => $activityDescription,
        ]);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('client.tickets.dashboard')
            ->with('success', $message);
    }

    public function getStats()
    {
        $user = Auth::user();
        $tickets = Ticket::where('client_id', $user->id)->get();

        return response()->json([
            'total' => $tickets->count(),
            'pending' => $tickets->where('status', 'Pending')->count(),
            'received' => $tickets->where('status', 'Receive')->count(),
            'in_progress' => $tickets->where('status', 'Send to Logic')->count(),
            'completed' => $tickets->where('status', 'Complete')->count(),
        ]);
    }

    public function getTicketsAjax()
    {
        $user = Auth::user();
        $tickets = Ticket::where('client_id', $user->id)
            ->with(['supportUser', 'review'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['tickets' => $tickets]);
    }
}
