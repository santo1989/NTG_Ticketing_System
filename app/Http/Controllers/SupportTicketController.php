<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use App\Models\TicketActivity;
use App\Models\TicketFeedback;
use App\Mail\TicketCompletedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;

class SupportTicketController extends Controller
{
    protected function applyUserFilters($query)
    {
        $user = Auth::user();

        // Filter by assigned companies
        $assignedCompanyIds = $user->assignedCompanies()->pluck('companies.id')->toArray();
        if (!empty($assignedCompanyIds)) {
            $query->whereHas('client', function ($q) use ($assignedCompanyIds) {
                $q->whereIn('company_id', $assignedCompanyIds);
            });
        }

        // Filter by assigned support types
        $assignedSupportTypes = $user->assignedSupportTypes()->pluck('support_type')->toArray();
        if (!empty($assignedSupportTypes)) {
            $query->whereIn('support_type', $assignedSupportTypes);
        }

        return $query;
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Check if user has assigned support types
        $assignedSupportTypes = $user->assignedSupportTypes()->pluck('support_type')->toArray();

        // Determine which support types to show
        $showERP = empty($assignedSupportTypes) || in_array('ERP Support', $assignedSupportTypes);
        $showIT = empty($assignedSupportTypes) || in_array('IT Support', $assignedSupportTypes);
        $showProgrammer = empty($assignedSupportTypes) || in_array('Programmer Support', $assignedSupportTypes);

        // Get latest tickets by support type (only if should be shown)
        $erpTickets = $showERP
            ? $this->applyUserFilters(Ticket::byType('ERP Support'))
            ->with(['client', 'supportUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            : collect();

        $itTickets = $showIT
            ? $this->applyUserFilters(Ticket::byType('IT Support'))
            ->with(['client', 'supportUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            : collect();

        $programmerTickets = $showProgrammer
            ? $this->applyUserFilters(Ticket::byType('Programmer Support'))
            ->with(['client', 'supportUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            : collect();

        // Get support user's statistics
        $stats = $this->getUserStats($user->id);

        return view('backend.tickets.support.dashboard', compact(
            'erpTickets',
            'itTickets',
            'programmerTickets',
            'stats',
            'showERP',
            'showIT',
            'showProgrammer'
        ));
    }

    public function myTickets()
    {
        $user = Auth::user();

        $tickets = Ticket::where('support_user_id', $user->id)
            ->with(['client', 'review'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = $this->getUserStats($user->id);

        return view('backend.tickets.support.my-tickets', compact('tickets', 'stats'));
    }

    public function reports(Request $request)
    {
        $query = Ticket::with(['client', 'supportUser', 'review'])
            ->where('support_user_id', Auth::id());

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

        return view('backend.tickets.support.reports', compact('tickets'));
    }

    public function downloadReport(Request $request)
    {
        $query = Ticket::with(['client', 'supportUser'])
            ->where('support_user_id', Auth::id());

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

        $fileName = 'my_tickets_report_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $callback = function () use ($tickets) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Ticket #',
                'Subject',
                'Client',
                'Support Type',
                'Status',
                'Created At',
                'Completed At',
            ]);

            foreach ($tickets as $ticket) {
                fputcsv($handle, [
                    $ticket->ticket_number,
                    $ticket->subject,
                    $ticket->client ? $ticket->client->name : '',
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

    public function show(Ticket $ticket)
    {
        $ticket->load([
            'client',
            'supportUser',
            'review',
            'reviewHistories',
            'activities' => function ($query) {
                $query->with('user');
            },
        ]);

        return view('backend.tickets.support.show', compact('ticket'));
    }

    public function receive(Ticket $ticket)
    {
        // Block if already received/assigned or completed
        if ($ticket->status === 'Receive') {
            return redirect()->back()
                ->with('error', 'This ticket has already been received by someone.');
        }

        if ($ticket->status === 'Complete') {
            return redirect()->back()
                ->with('error', 'Completed tickets cannot be reassigned.');
        }

        $ticket->update([
            'support_user_id' => Auth::id(),
            'status' => 'Receive',
            'received_at' => now(),
        ]);

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'received',
            'new_status' => 'Receive',
            'description' => 'Ticket received by ' . Auth::user()->name,
        ]);

        return redirect()->route('support.tickets.show', $ticket)
            ->with('success', 'Ticket received successfully!');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        // Ensure only assigned support user can update (cast to int to avoid type mismatches)
        if ((int) $ticket->support_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'status' => 'required|in:Receive,Send to Logic,Complete,Revise',
            'remarks' => 'required|string',
            'solving_time' => 'nullable|date',
        ]);

        $oldStatus = $ticket->status;
        $ticket->update($validated);

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'status_changed',
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'description' => 'Remarks: ' . $validated['remarks'],
        ]);

        return redirect()->back()
            ->with('success', 'Ticket status updated successfully!');
    }

    public function complete(Request $request, Ticket $ticket)
    {
        // Ensure only assigned support user can complete (cast to int to avoid type mismatches)
        if ((int) $ticket->support_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'remarks' => 'required|string',
        ]);

        $ticket->update([
            'status' => 'Complete',
            'remarks' => $validated['remarks'],
            'completed_at' => now(),
        ]);

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'completed',
            'new_status' => 'Complete',
            'description' => 'Solution: ' . $validated['remarks'],
        ]);

        // Send email to client
        try {
            Mail::to($ticket->client->email)->send(new TicketCompletedMail($ticket));
        } catch (\Exception $e) {
            // Log error but don't fail the completion
            Log::error('Failed to send ticket completion email: ' . $e->getMessage());
        }

        return redirect()->route('support.tickets.my-tickets')
            ->with('success', 'Ticket completed successfully! Email sent to client.');
    }

    protected function getUserStats($userId)
    {
        $solveCount = Ticket::where('support_user_id', $userId)
            ->where('status', 'Complete')
            ->count();

        $reviewCount = Ticket::where('support_user_id', $userId)
            ->whereHas('review')
            ->count();

        $satisfiedCount = Ticket::where('support_user_id', $userId)
            ->whereHas('review', function ($query) {
                $query->where('rating', 'Satisfied');
            })
            ->count();

        $dissatisfiedCount = Ticket::where('support_user_id', $userId)
            ->whereHas('review', function ($query) {
                $query->where('rating', 'Dissatisfied');
            })
            ->count();

        // Forward count - tickets that were forwarded by this user
        $forwardCount = TicketActivity::where('user_id', $userId)
            ->where('action', 'forwarded')
            ->count();

        // Total tickets handled (received) by this user
        $totalTicketsHandled = Ticket::where('support_user_id', $userId)->count();

        // Forward percentage
        $forwardPercentage = $totalTicketsHandled > 0
            ? round(($forwardCount / $totalTicketsHandled) * 100, 2)
            : 0;

        return [
            'solve_count' => $solveCount,
            'review_count' => $reviewCount,
            'satisfied_count' => $satisfiedCount,
            'dissatisfied_count' => $dissatisfiedCount,
            'forward_count' => $forwardCount,
            'forward_percentage' => $forwardPercentage,
        ];
    }

    public function getStatsAjax()
    {
        $stats = $this->getUserStats(Auth::id());
        return response()->json($stats);
    }

    public function getDashboardTickets()
    {
        $erpTickets = Ticket::byType('ERP Support')
            ->with(['client', 'supportUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $itTickets = Ticket::byType('IT Support')
            ->with(['client', 'supportUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $programmerTickets = Ticket::byType('Programmer Support')
            ->with(['client', 'supportUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'erp' => $erpTickets,
            'it' => $itTickets,
            'programmer' => $programmerTickets,
        ]);
    }

    public function forward(Request $request, Ticket $ticket)
    {
        // Ensure only assigned support user can forward (cast to int to avoid type mismatches)
        if ((int) $ticket->support_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $validated = $request->validate([
            'support_user_id' => 'required|exists:users,id',
            'remarks' => 'required|string',
        ]);

        // Verify the target user has appropriate role (Support, Supervisor, or Admin)
        $targetUser = User::with('role')->find($validated['support_user_id']);
        $allowedRoles = ['Support', 'Supervisor', 'Admin'];

        if (!$targetUser || !$targetUser->role || !in_array($targetUser->role->name, $allowedRoles)) {
            return redirect()->back()
                ->with('error', 'Can only forward to Support, Supervisor, or Admin users.');
        }

        $oldUserId = $ticket->support_user_id;
        $oldUserName = $ticket->supportUser ? $ticket->supportUser->name : 'Unknown';

        $ticket->update([
            'support_user_id' => $validated['support_user_id'],
        ]);

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'forwarded',
            'from_user' => $oldUserName,
            'to_user' => $targetUser->name,
            'description' => $validated['remarks'],
        ]);

        return redirect()->back()
            ->with('success', 'Ticket forwarded successfully!');
    }

    public function getMyTickets()
    {
        $tickets = Ticket::where('support_user_id', Auth::id())
            ->with(['client', 'review'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($tickets);
    }

    public function storeFeedback(Request $request, Ticket $ticket)
    {
        // Ensure only assigned support user can provide feedback
        if ((int) $ticket->support_user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Only allow feedback when status is "Send to Logic"
        if ($ticket->status !== 'Send to Logic') {
            return redirect()->back()
                ->with('error', 'Feedback can only be provided when ticket status is "Send to Logic".');
        }

        $validated = $request->validate([
            'feedback' => 'required|string|max:5000',
        ]);

        TicketFeedback::create([
            'ticket_id' => $ticket->id,
            'support_user_id' => Auth::id(),
            'feedback' => $validated['feedback'],
        ]);

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'feedback_added',
            'description' => 'Added feedback: ' . substr($validated['feedback'], 0, 100) . (strlen($validated['feedback']) > 100 ? '...' : ''),
        ]);

        return redirect()->back()
            ->with('success', 'Feedback added successfully!');
    }

    public function create()
    {
        $users = User::where('role_id', 2)->orderBy('name')->get(); // General role users
        $companies = \App\Models\Company::orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('name')->get();
        $designations = \App\Models\Designation::orderBy('name')->get();

        return view('backend.tickets.support.create', compact('users', 'companies', 'departments', 'designations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'support_type' => 'required|in:ERP Support,IT Support,Programmer Support',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240', // 10MB max per file
        ]);

        // Generate ticket number
        $lastTicket = Ticket::orderBy('id', 'desc')->first();
        $ticketNumber = 'TKT-' . str_pad(($lastTicket ? $lastTicket->id + 1 : 1), 6, '0', STR_PAD_LEFT);

        // Create ticket with selected client as creator
        $ticket = Ticket::create([
            'ticket_number' => $ticketNumber,
            'client_id' => $validated['client_id'],
            'support_type' => $validated['support_type'],
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'status' => 'Receive', // Auto-receive
            'support_user_id' => Auth::id(), // Auto-assign to current support user
            'received_at' => now(),
        ]);

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('attachments/tickets'), $filename);

                \App\Models\TicketAttachment::create([
                    'ticket_id' => $ticket->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => 'attachments/tickets/' . $filename,
                    'uploaded_by' => Auth::id(),
                ]);
            }
        }

        // Log activity
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'created_and_received',
            'new_status' => 'Receive',
            'description' => 'Ticket created on behalf of ' . User::find($validated['client_id'])->name . ' and auto-received by ' . Auth::user()->name,
        ]);

        return redirect()->route('support.tickets.show', $ticket)
            ->with('success', 'Ticket created and auto-received successfully!');
    }
}
