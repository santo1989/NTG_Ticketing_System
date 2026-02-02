<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminTicketController extends Controller
{
    public function dashboard()
    {
        // Overall statistics
        $totalTickets = Ticket::count();
        $pendingTickets = Ticket::where('status', 'Pending')->count();
        $receivedTickets = Ticket::where('status', 'Ticket Received')->count();
        $inProgressTickets = Ticket::where('status', 'Send to Logic')->count();
        $completedTickets = Ticket::where('status', 'Complete')->count();

        // Support type statistics
        $erpCount = Ticket::byType('ERP Support')->count();
        $itCount = Ticket::byType('IT Support')->count();
        $programmerCount = Ticket::byType('Programmer Support')->count();

        // Review statistics
        $totalReviews = TicketReview::count();
        $satisfiedCount = TicketReview::where('rating', 'Satisfied')->count();
        $dissatisfiedCount = TicketReview::where('rating', 'Dissatisfied')->count();

        // Top support users
        $topSupportUsers = User::withCount(['supportTickets as completed_count' => function ($query) {
            $query->where('status', 'Complete');
        }])
            ->get()
            ->filter(function ($user) {
                return $user->completed_count > 0;
            })
            ->sortByDesc('completed_count')
            ->take(5)
            ->values();

        // Recent tickets
        $recentTickets = Ticket::with(['client', 'supportUser'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('backend.tickets.admin.dashboard', compact(
            'totalTickets',
            'pendingTickets',
            'receivedTickets',
            'inProgressTickets',
            'completedTickets',
            'erpCount',
            'itCount',
            'programmerCount',
            'totalReviews',
            'satisfiedCount',
            'dissatisfiedCount',
            'topSupportUsers',
            'recentTickets'
        ));
    }

    public function index(Request $request)
    {
        $query = Ticket::with(['client', 'supportUser', 'review']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('support_type')) {
            $query->where('support_type', $request->support_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('backend.tickets.admin.index', compact('tickets'));
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

        return view('backend.tickets.admin.show', compact('ticket'));
    }

    public function assignSupport(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'support_user_id' => 'required|exists:users,id',
        ]);

        $ticket->update([
            'support_user_id' => $validated['support_user_id'],
            'status' => 'Ticket Received',
            'received_at' => now(),
        ]);

        return redirect()->back()
            ->with('success', 'Support user assigned successfully!');
    }

    public function reports()
    {
        $reportData = $this->buildReportData();

        return view('backend.tickets.admin.reports', $reportData);
    }

    public function getReportsData()
    {
        return response()->json($this->buildReportData());
    }

    protected function buildReportData(): array
    {
        $monthlyStats = Ticket::selectRaw("FORMAT(created_at, 'yyyy-MM') as month_year")
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'Complete' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN status != 'Complete' THEN 1 ELSE 0 END) as pending")
            ->groupByRaw("FORMAT(created_at, 'yyyy-MM')")
            ->orderBy('month_year', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($row) {
                $row->completion_rate = $row->total > 0 ? round(($row->completed / $row->total) * 100, 1) : 0;
                return $row;
            });

        $supportTypePerformance = Ticket::leftJoin('ticket_reviews', 'ticket_reviews.ticket_id', '=', 'tickets.id')
            ->select('tickets.support_type')
            ->selectRaw('COUNT(tickets.id) as total')
            ->selectRaw("SUM(CASE WHEN tickets.status = 'Complete' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("AVG(CASE WHEN tickets.completed_at IS NOT NULL THEN DATEDIFF(HOUR, tickets.created_at, tickets.completed_at) END) as avg_resolution_hours")
            ->selectRaw('COUNT(ticket_reviews.id) as total_reviews')
            ->selectRaw("SUM(CASE WHEN ticket_reviews.rating = 'Satisfied' THEN 1 ELSE 0 END) as satisfied")
            ->selectRaw("SUM(CASE WHEN ticket_reviews.rating = 'Dissatisfied' THEN 1 ELSE 0 END) as dissatisfied")
            ->groupBy('tickets.support_type')
            ->get()
            ->map(function ($row) {
                $row->satisfaction_rate = $row->total_reviews > 0 ? round(($row->satisfied / $row->total_reviews) * 100, 1) : 0;
                return $row;
            });

        $userPerformance = User::leftJoin('tickets', 'tickets.support_user_id', '=', 'users.id')
            ->leftJoin('ticket_reviews', 'ticket_reviews.ticket_id', '=', 'tickets.id')
            ->whereNotNull('tickets.id')
            ->select('users.id', 'users.name')
            ->selectRaw('COUNT(tickets.id) as total_assigned')
            ->selectRaw("SUM(CASE WHEN tickets.status = 'Complete' THEN 1 ELSE 0 END) as completed")
            ->selectRaw("SUM(CASE WHEN tickets.status != 'Complete' THEN 1 ELSE 0 END) as pending")
            ->selectRaw("AVG(CASE WHEN tickets.completed_at IS NOT NULL THEN DATEDIFF(HOUR, tickets.created_at, tickets.completed_at) END) as avg_resolution_hours")
            ->selectRaw('COUNT(ticket_reviews.id) as total_reviews')
            ->selectRaw("SUM(CASE WHEN ticket_reviews.rating = 'Satisfied' THEN 1 ELSE 0 END) as satisfied")
            ->selectRaw("SUM(CASE WHEN ticket_reviews.rating = 'Dissatisfied' THEN 1 ELSE 0 END) as dissatisfied")
            ->groupBy('users.id', 'users.name')
            ->get()
            ->map(function ($row) {
                $row->satisfaction_rate = $row->total_reviews > 0 ? round(($row->satisfied / $row->total_reviews) * 100, 1) : 0;
                return $row;
            });

        return [
            'monthlyStats' => $monthlyStats,
            'supportTypePerformance' => $supportTypePerformance,
            'userPerformance' => $userPerformance,
        ];
    }

    public function getDashboardStats()
    {
        return response()->json([
            'totalTickets' => Ticket::count(),
            'pendingTickets' => Ticket::where('status', 'Pending')->count(),
            'receivedTickets' => Ticket::where('status', 'Ticket Received')->count(),
            'inProgressTickets' => Ticket::where('status', 'Send to Logic')->count(),
            'completedTickets' => Ticket::where('status', 'Complete')->count(),
            'erpCount' => Ticket::byType('ERP Support')->count(),
            'itCount' => Ticket::byType('IT Support')->count(),
            'programmerCount' => Ticket::byType('Programmer Support')->count(),
            'totalReviews' => TicketReview::count(),
            'satisfiedCount' => TicketReview::where('rating', 'Satisfied')->count(),
            'dissatisfiedCount' => TicketReview::where('rating', 'Dissatisfied')->count(),
        ]);
    }

    public function getIndexTickets(Request $request)
    {
        $query = Ticket::with(['client', 'supportUser', 'review']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('support_type')) {
            $query->where('support_type', $request->support_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('client', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $tickets = $query->orderBy('created_at', 'desc')->limit(100)->get();

        return response()->json($tickets);
    }

    public function destroy(Ticket $ticket)
    {
        DB::transaction(function () use ($ticket) {
            if (!empty($ticket->file_path)) {
                Storage::disk('public')->delete($ticket->file_path);
            }

            $ticket->activities()->delete();
            $ticket->reviewHistories()->delete();
            $ticket->review()->delete();

            $ticket->forceDelete();
        });

        return redirect()->route('admin.tickets.index')
            ->with('success', 'Ticket deleted successfully.');
    }
}
