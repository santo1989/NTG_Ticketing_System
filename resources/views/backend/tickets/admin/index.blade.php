<x-backend.layouts.master>
    <x-slot name="pageTitle">
        All Tickets
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                All Support Tickets
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('admin.tickets.dashboard') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        <!-- Filters -->
        <div class="card mb-3">
            <div class="card-body">

                <form action="{{ route('admin.tickets.index') }}" method="GET" class="form-inline" id="filterForm">
                    <!--back to home button-->
                    <div class="form-group mr-3 mb-2">
                        <a href="{{ route('home') }}" class="btn btn-outline-danger">
                            <i class="fas fa-home"></i> Home
                        </a>
                    </div>

                    <div class="form-group mr-3 mb-2">
                        <label for="status" class="mr-2">Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="Ticket Received"
                                {{ request('status') == 'Ticket Received' ? 'selected' : '' }}>Ticket Received
                            </option>
                            <option value="MIS Receive Issue"
                                {{ request('status') == 'MIS Receive Issue' ? 'selected' : '' }}>MIS Receive Issue
                            </option>
                            <option value="Send to Logic" {{ request('status') == 'Send to Logic' ? 'selected' : '' }}>
                                Send to Logic</option>
                            <option value="Complete" {{ request('status') == 'Complete' ? 'selected' : '' }}>Complete
                            </option>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label for="support_type" class="mr-2">Type:</label>
                        <select name="support_type" id="support_type" class="form-control">
                            <option value="">All Types</option>
                            <option value="ERP Support"
                                {{ request('support_type') == 'ERP Support' ? 'selected' : '' }}>ERP Support</option>
                            <option value="IT Support" {{ request('support_type') == 'IT Support' ? 'selected' : '' }}>
                                IT Support</option>
                            <option value="Programmer Support"
                                {{ request('support_type') == 'Programmer Support' ? 'selected' : '' }}>Programmer
                                Support</option>
                        </select>
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <input type="text" name="search" class="form-control" id="searchInput"
                            placeholder="Search by ticket # or subject" value="{{ request('search') }}">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 mr-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary mb-2">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </form>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="allTicketsTable">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Support Type</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Created</th>
                                <th>Review</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="allTicketsBody">
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                    <td>{{ $ticket->client->name }}</td>
                                    <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                    <td><span class="badge badge-info">{{ $ticket->support_type }}</span></td>
                                    <td>
                                        @switch($ticket->status)
                                            @case('Pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @break

                                            @case('Ticket Received')
                                                <span class="badge badge-info">Ticket Received by
                                                    {{ $ticket->supportUser ? $ticket->supportUser->name : 'Unknown' }}</span>
                                            @break

                                            @case('MIS Receive Issue')
                                                <span class="badge badge-info">Ticket Received by
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
                                    <td>
                                        @if ($ticket->supportUser)
                                            {{ $ticket->supportUser->name }}
                                        @else
                                            <span class="text-muted">Unassigned</span>
                                        @endif
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if ($ticket->review)
                                            @if ($ticket->review->rating === 'Satisfied')
                                                <span class="badge badge-success"><i class="fas fa-smile"></i></span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-frown"></i></span>
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this ticket and all related data?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No tickets found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $tickets->links() }}
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                let table = $('#allTicketsTable').DataTable({
                    paging: false,
                    searching: false,
                    order: [
                        [6, 'desc']
                    ]
                });

                // Function to get status badge
                function getStatusBadge(status, supportUser) {
                    switch (status) {
                        case 'Pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        case 'Ticket Received':
                        case 'MIS Receive Issue':
                            let receivedByName = supportUser ? supportUser.name : 'Unknown';
                            return '<span class="badge badge-info">Ticket Received by ' + receivedByName + '</span>';
                        case 'Send to Logic':
                            return '<span class="badge badge-secondary">In Progress</span>';
                        case 'Complete':
                            return '<span class="badge badge-success">Complete</span>';
                        default:
                            return '<span class="badge badge-secondary">' + status + '</span>';
                    }
                }

                // Function to refresh tickets based on current filters
                function refreshIndexTickets() {
                    let status = $('#status').val();
                    let support_type = $('#support_type').val();
                    let search = $('#searchInput').val();

                    $.ajax({
                        url: "{{ route('admin.tickets.ajax.index-tickets') }}",
                        type: 'GET',
                        data: {
                            status: status,
                            support_type: support_type,
                            search: search
                        },
                        dataType: 'json',
                        success: function(data) {
                            table.clear();

                            data.tickets.forEach(ticket => {
                                let reviewBadge = '';
                                if (ticket.review) {
                                    if (ticket.review.status === 'Satisfied') {
                                        reviewBadge =
                                            '<span class="badge badge-success">Satisfied</span>';
                                    } else {
                                        reviewBadge =
                                            '<span class="badge badge-danger">Dissatisfied</span>';
                                    }
                                } else {
                                    reviewBadge = '-';
                                }

                                let assignedName = ticket.support_user ? ticket.support_user.name :
                                    '<span class="text-muted">Unassigned</span>';
                                let createdDate = new Date(ticket.created_at).toLocaleDateString();

                                table.row.add([
                                    '<strong>' + ticket.ticket_number + '</strong>',
                                    ticket.client.name,
                                    ticket.subject.substring(0, 40),
                                    '<span class="badge badge-info">' + ticket
                                    .support_type + '</span>',
                                    getStatusBadge(ticket.status, ticket.support_user),
                                    assignedName,
                                    createdDate,
                                    reviewBadge,
                                    '<a href="{{ route('admin.tickets.show', '') }}/' +
                                    ticket.id +
                                    '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>'
                                ]);
                            });

                            table.draw();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error refreshing tickets:', error);
                        }
                    });
                }

                // Refresh every 10 seconds
                setInterval(refreshIndexTickets, 10000);
            });
        </script>

    </x-backend.layouts.master>
