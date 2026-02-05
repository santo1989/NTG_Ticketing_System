<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Support Dashboard
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Support Ticket Dashboard
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('home') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="{{ route('support.tickets.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle"></i> Create Ticket
                </a>
                <a href="{{ route('support.tickets.my-tickets') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-ticket-alt"></i> My Tickets
                </a>
                <a href="{{ route('support.tickets.reports') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-chart-line"></i> Reports
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('success') }}
            </div>
        @endif

        <!-- My Statistics -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="mb-3 text-light"><i class="fas fa-user-circle"></i> My Performance
                            <span class="float-end" style="font-size: 0.75rem; opacity: 0.8;">Last updated: <span
                                    id="statsLastUpdated">now</span></span>
                        </h5>
                        <div class="row">
                            <div class="col-md-2 text-center">
                                <h2 id="solveCount">{{ $stats['solve_count'] }}</h2>
                                <p class="mb-0">Tickets Solved</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h2 id="reviewCount">{{ $stats['review_count'] }}</h2>
                                <p class="mb-0">Reviews Received</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h2 class="text-success" id="satisfiedCount">{{ $stats['satisfied_count'] }}</h2>
                                <p class="mb-0">Satisfied</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h2 class="text-danger" id="dissatisfiedCount">{{ $stats['dissatisfied_count'] }}</h2>
                                <p class="mb-0">Dissatisfied</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h2 class="text-warning" id="forwardCount">{{ $stats['forward_count'] }}</h2>
                                <p class="mb-0">Forwarded</p>
                            </div>
                            <div class="col-md-2 text-center">
                                <h2 class="text-info" id="forwardPercentage">{{ $stats['forward_percentage'] }}%</h2>
                                <p class="mb-0">Forward Rate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ERP Support Tickets -->
        @if ($showERP)
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-database"></i> ERP Support - Latest Tickets</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm" id="erpTicketsTable">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Client</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Queue Position</th>
                                    <th>Created</th>
                                    <th>Solving Time</th>
                                    <th>Closed</th>
                                    <th>Received By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="erpTicketsBody">
                                @forelse($erpTickets as $ticket)
                                    <tr>
                                        <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                        <td>{{ $ticket->client->name }}</td>
                                        <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                        <td>
                                            @switch($ticket->status)
                                                @case('Pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @break

                                                @case('Receive')
                                                    <span class="badge badge-info">Receive by
                                                        {{ $ticket->supportUser ? $ticket->supportUser->name : 'Unknown' }}</span>
                                                @break

                                                @case('Revise')
                                                    <span class="badge badge-warning">Revise</span>
                                                @break

                                                @case('Send to Logic')
                                                    <span class="badge badge-secondary">In Progress</span>
                                                @break

                                                @case('Complete')
                                                    <span class="badge badge-success">Complete</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                        <td>{{ $ticket->solving_time ? $ticket->solving_time->format('M d, Y h:i A') : '-' }}
                                        </td>
                                        <td>{{ $ticket->completed_at ? $ticket->completed_at->format('M d, Y h:i A') : '-' }}
                                        </td>
                                        <td>
                                            @if ($ticket->supportUser)
                                                {{ $ticket->supportUser->name }}
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('support.tickets.show', $ticket) }}"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if ($ticket->status === 'Pending')
                                                <form
                                                    action="{{ route('support.tickets.receive', ['ticket' => $ticket->id]) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-hand-paper"></i> Receive
                                                    </button>
                                                </form>
                                            @elseif($ticket->status !== 'Complete' && $ticket->support_user_id == Auth::id())
                                                <button type="button" class="btn btn-sm btn-warning"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#forwardModal{{ $ticket->id }}">
                                                    <i class="fas fa-share"></i> Forward
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Forward Modal for Each Ticket -->
                                    <div class="modal fade" id="forwardModal{{ $ticket->id }}" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-warning">
                                                    <h5 class="modal-title">Forward Ticket</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form
                                                    action="{{ route('support.tickets.forward', ['ticket' => $ticket->id]) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label>Forward To</label>
                                                            <select name="support_user_id" class="form-control"
                                                                required>
                                                                <option value="">Select Support User</option>
                                                                @foreach (App\Models\User::whereIn('role_id', [3, 4])->get() as $user)
                                                                    @if ($user->id !== Auth::id())
                                                                        <option value="{{ $user->id }}">
                                                                            {{ $user->name }}</option>
                                                                    @endif
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Remarks</label>
                                                            <textarea name="remarks" class="form-control" rows="3" placeholder="Why are you forwarding this ticket?"
                                                                required></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-warning">
                                                            <i class="fas fa-share"></i> Forward
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10" class="text-center">No ERP support tickets</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- IT Support Tickets -->
            @if ($showIT)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-laptop"></i> IT Support - Latest Tickets</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-sm" id="itTicketsTable">
                                <thead>
                                    <tr>
                                        <th>Ticket #</th>
                                        <th>Client</th>
                                        <th>Subject</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Solving Time</th>
                                        <th>Closed</th>
                                        <th>Received By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="itTicketsBody">
                                    @forelse($itTickets as $ticket)
                                        <tr>
                                            <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                            <td>{{ $ticket->client->name }}</td>
                                            <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                            <td>
                                                @switch($ticket->status)
                                                    @case('Pending')
                                                        <span class="badge badge-warning">Pending</span>
                                                    @break

                                                    @case('Ticket Received')
                                                        <span class="badge badge-info">Ticket Received by
                                                            {{ $ticket->supportUser ? $ticket->supportUser->name : 'Unknown' }}</span>
                                                    @break

                                                    @case('Receive')
                                                        <span class="badge badge-info">Receive by
                                                            {{ $ticket->supportUser ? $ticket->supportUser->name : 'Unknown' }}</span>
                                                    @break

                                                    @case('Send to Logic')
                                                        <span class="badge badge-secondary">In Progress</span>
                                                    @break

                                                    @case('Complete')
                                                        <span class="badge badge-success">Complete</span>
                                                    @break
                                                @endswitch
                                            </td>
                                            <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                            <td>{{ $ticket->solving_time ? $ticket->solving_time->format('M d, Y h:i A') : '-' }}
                                            </td>
                                            <td>{{ $ticket->completed_at ? $ticket->completed_at->format('M d, Y h:i A') : '-' }}
                                            </td>
                                            <td>
                                                @if ($ticket->supportUser)
                                                    {{ $ticket->supportUser->name }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('support.tickets.show', $ticket) }}"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if ($ticket->status === 'Pending')
                                                    <form
                                                        action="{{ route('support.tickets.receive', ['ticket' => $ticket->id]) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-hand-paper"></i> Receive
                                                        </button>
                                                    </form>
                                                @elseif($ticket->status !== 'Complete' && $ticket->support_user_id == Auth::id())
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#forwardModal{{ $ticket->id }}">
                                                        <i class="fas fa-share"></i> Forward
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Forward Modal for Each IT Ticket -->
                                        <div class="modal fade" id="forwardModal{{ $ticket->id }}" tabindex="-1"
                                            role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-warning">
                                                        <h5 class="modal-title">Forward Ticket</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <form
                                                        action="{{ route('support.tickets.forward', ['ticket' => $ticket->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <div class="modal-body">
                                                            <div class="form-group">
                                                                <label>Forward To</label>
                                                                <select name="support_user_id" class="form-control"
                                                                    required>
                                                                    <option value="">Select Support User</option>
                                                                    @foreach (App\Models\User::whereIn('role_id', [3, 4])->get() as $user)
                                                                        @if ($user->id !== Auth::id())
                                                                            <option value="{{ $user->id }}">
                                                                                {{ $user->name }}</option>
                                                                        @endif
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Remarks</label>
                                                                <textarea name="remarks" class="form-control" rows="3" placeholder="Why are you forwarding this ticket?"
                                                                    required></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-warning">
                                                                <i class="fas fa-share"></i> Forward
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                            <tr>
                                                <td colspan="10" class="text-center">No IT support tickets</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Programmer Support Tickets -->
                @if ($showProgrammer)
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-light">
                            <h5 class="mb-0 text-light"><i class="fas fa-code"></i> Programmer Support - Latest Tickets</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-sm" id="programmerTicketsTable">
                                    <thead>
                                        <tr>
                                            <th>Ticket #</th>
                                            <th>Client</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Created</th>
                                            <th>Solving Time</th>
                                            <th>Closed</th>
                                            <th>Received By</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="programmerTicketsBody">
                                        @forelse($programmerTickets as $ticket)
                                            <tr>
                                                <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                                <td>{{ $ticket->client->name }}</td>
                                                <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                                <td>
                                                    @switch($ticket->status)
                                                        @case('Pending')
                                                            <span class="badge badge-warning">Pending</span>
                                                        @break

                                                        @case('Ticket Received')
                                                            <span class="badge badge-info">Ticket Received by
                                                                {{ $ticket->supportUser ? $ticket->supportUser->name : 'Unknown' }}</span>
                                                        @break

                                                        @case('Receive')
                                                            <span class="badge badge-info">Receive by
                                                                {{ $ticket->supportUser ? $ticket->supportUser->name : 'Unknown' }}</span>
                                                        @break

                                                        @case('Send to Logic')
                                                            <span class="badge badge-secondary">In Progress</span>
                                                        @break

                                                        @case('Complete')
                                                            <span class="badge badge-success">Complete</span>
                                                        @break
                                                    @endswitch
                                                </td>
                                                <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                                <td>{{ $ticket->solving_time ? $ticket->solving_time->format('M d, Y h:i A') : '-' }}
                                                </td>
                                                <td>{{ $ticket->completed_at ? $ticket->completed_at->format('M d, Y h:i A') : '-' }}
                                                </td>
                                                <td>
                                                    @if ($ticket->supportUser)
                                                        {{ $ticket->supportUser->name }}
                                                    @else
                                                        <span class="text-muted">Unassigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('support.tickets.show', $ticket) }}"
                                                        class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if ($ticket->status === 'Pending')
                                                        <form
                                                            action="{{ route('support.tickets.receive', ['ticket' => $ticket->id]) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-success">
                                                                <i class="fas fa-hand-paper"></i> Receive
                                                            </button>
                                                        </form>
                                                    @elseif($ticket->status !== 'Complete' && $ticket->support_user_id == Auth::id())
                                                        <button type="button" class="btn btn-sm btn-warning"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#forwardModal{{ $ticket->id }}">
                                                            <i class="fas fa-share"></i> Forward
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>

                                            <!-- Forward Modal for Each Programmer Ticket -->
                                            <div class="modal fade" id="forwardModal{{ $ticket->id }}" tabindex="-1"
                                                role="dialog">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-warning">
                                                            <h5 class="modal-title">Forward Ticket</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <form
                                                            action="{{ route('support.tickets.forward', ['ticket' => $ticket->id]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="form-group">
                                                                    <label>Forward To</label>
                                                                    <select name="support_user_id" class="form-control"
                                                                        required>
                                                                        <option value="">Select Support User</option>
                                                                        @foreach (App\Models\User::whereIn('role_id', [3, 4])->get() as $user)
                                                                            @if ($user->id !== Auth::id())
                                                                                <option value="{{ $user->id }}">
                                                                                    {{ $user->name }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Remarks</label>
                                                                    <textarea name="remarks" class="form-control" rows="3" placeholder="Why are you forwarding this ticket?"
                                                                        required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-warning">
                                                                    <i class="fas fa-share"></i> Forward
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center">No Programmer support tickets</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif


                    <script>
                        $(document).ready(function() {
                            // Current authenticated user id for JS logic
                            var CURRENT_USER_ID = {{ Auth::id() }};
                            // Function to render status badge
                            function getStatusBadge(status, supportUser) {
                                switch (status) {
                                    case 'Pending':
                                        return '<span class="badge badge-warning">Pending</span>';
                                    case 'Receive':
                                        let receivedByName = supportUser ? supportUser.name : 'Unknown';
                                        return '<span class="badge badge-info">Receive by ' + receivedByName + '</span>';
                                    case 'Revise':
                                        return '<span class="badge badge-warning">Revise</span>';
                                    case 'Send to Logic':
                                        return '<span class="badge badge-secondary">In Progress</span>';
                                    case 'Complete':
                                        return '<span class="badge badge-success">Complete</span>';
                                    default:
                                        return '<span class="badge badge-secondary">' + status + '</span>';
                                }
                            }

                            // Function to render table rows
                            function renderTicketRows(tickets, tableBodyId) {
                                let html = '';
                                if (tickets.length === 0) {
                                    html = '<tr><td colspan="10" class="text-center">No tickets available</td></tr>';
                                } else {
                                    tickets.forEach(ticket => {
                                        let assignedName = ticket.support_user ? ticket.support_user.name :
                                            '<span class="text-muted">Unassigned</span>';
                                        let actionBtn = '';
                                        if (ticket.status === 'Pending') {
                                            actionBtn = '<form action="{{ url('support-tickets') }}/' + ticket.id +
                                                '/receive" method="POST" class="d-inline"><input type="hidden" name="_token" value="{{ csrf_token() }}"><button type="submit" class="btn btn-sm btn-success"><i class="fas fa-hand-paper"></i> Receive</button></form>';
                                        } else if (ticket.status !== 'Complete' && ticket.support_user && ticket
                                            .support_user.id == CURRENT_USER_ID) {
                                            // For AJAX-updated rows we link to the ticket show page where forward/complete actions are available
                                            actionBtn = '<a href="{{ url('support-tickets') }}/' + ticket.id +
                                                '" class="btn btn-sm btn-warning"><i class="fas fa-share"></i> Forward</a>';
                                        }

                                        let completedAtStr = ticket.completed_at ? new Date(ticket.completed_at)
                                            .toLocaleString('en-US', {
                                                year: 'numeric',
                                                month: 'short',
                                                day: '2-digit',
                                                hour: '2-digit',
                                                minute: '2-digit'
                                            }) : '-';

                                        // Calculate queue position for pending tickets
                                        let queueHtml = '-';
                                        if (ticket.status === 'Pending') {
                                            // Count how many pending tickets of the same support type were created before this one
                                            let queuePosition = tickets.filter(t =>
                                                t.status === 'Pending' &&
                                                t.support_type === ticket.support_type &&
                                                new Date(t.created_at) < new Date(ticket.created_at)
                                            ).length + 1;

                                            let queueTotal = tickets.filter(t =>
                                                t.status === 'Pending' &&
                                                t.support_type === ticket.support_type
                                            ).length;

                                            queueHtml =
                                                '<div class="text-center"><span class="badge bg-primary" style="font-size: 1.1em;"><i class="fas fa-list-ol"></i> #' +
                                                queuePosition + '</span><br><small class="text-muted">of ' + queueTotal +
                                                '</small><br><small class="text-info">' + ticket.support_type +
                                                '</small></div>';
                                        }

                                        html += `<tr>
                                    <td><strong>${ticket.ticket_number}</strong></td>
                                    <td>${ticket.client.name}</td>
                                    <td>${ticket.subject.substring(0, 40)}</td>
                                    <td>${getStatusBadge(ticket.status, ticket.support_user)}</td>
                                    <td>${queueHtml}</td>
                                    <td>${new Date(ticket.created_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: '2-digit'})}</td>
                                        <td>${ticket.solving_time ? new Date(ticket.solving_time).toLocaleString('en-US', {year: 'numeric', month: 'short', day: '2-digit', hour: '2-digit', minute: '2-digit'}) : '-'}</td>
                                    <td>${completedAtStr}</td>
                                    <td>${ticket.support_user ? ticket.support_user.name : '<span class="text-muted">Unassigned</span>'}</td>
                                    <td>
                                        <a href="{{ route('support.tickets.show', '') }}/${ticket.id}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        ${actionBtn}
                                    </td>
                                </tr>`;
                                    });
                                }
                                $('#' + tableBodyId).html(html);
                            }

                            // Function to refresh all data
                            function refreshDashboard() {
                                $.ajax({
                                    url: "{{ route('support.tickets.ajax.stats') }}",
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function(data) {
                                        $('#solveCount').text(data.solve_count);
                                        $('#reviewCount').text(data.review_count);
                                        $('#satisfiedCount').text(data.satisfied_count);
                                        $('#dissatisfiedCount').text(data.dissatisfied_count);
                                        $('#forwardCount').text(data.forward_count);
                                        $('#forwardPercentage').text(data.forward_percentage + '%');
                                        updateStatsLastUpdated();
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('Error refreshing stats:', error);
                                    }
                                });

                                $.ajax({
                                    url: "{{ route('support.tickets.ajax.dashboard-tickets') }}",
                                    type: 'GET',
                                    dataType: 'json',
                                    success: function(data) {
                                        renderTicketRows(data.erp || data.erp_tickets || [], 'erpTicketsBody');
                                        renderTicketRows(data.it || data.it_tickets || [], 'itTicketsBody');
                                        renderTicketRows(data.programmer || data.programmer_tickets || [],
                                            'programmerTicketsBody');
                                    },
                                    error: function(xhr, status, error) {
                                        console.log('Error refreshing tickets:', error);
                                    }
                                });
                            }

                            function updateStatsLastUpdated() {
                                const now = new Date();
                                const timeString = now.toLocaleTimeString();
                                $('#statsLastUpdated').text(timeString);
                            }

                            // Refresh every 10 seconds
                            setInterval(refreshDashboard, 10000);
                        });
                    </script>

            </x-backend.layouts.master>
