<x-backend.layouts.master>
    <x-slot name="pageTitle">
        My Tickets
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                My Assigned Tickets
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('support.tickets.dashboard') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
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

        <!-- Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h3 id="mySolveCount">{{ $stats['solve_count'] }}</h3>
                        <p class="mb-0">Solved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h3 id="myReviewCount">{{ $stats['review_count'] }}</h3>
                        <p class="mb-0">Reviews</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h3 id="mySatisfiedCount">{{ $stats['satisfied_count'] }}</h3>
                        <p class="mb-0">Satisfied</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-danger text-white">
                    <div class="card-body">
                        <h3 id="myDissatisfiedCount">{{ $stats['dissatisfied_count'] }}</h3>
                        <p class="mb-0">Dissatisfied</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">My Assigned Tickets</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="myTicketsTable">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Support Type</th>
                                <th>Status</th>
                                <th>Received</th>
                                <th>Solving Time</th>
                                <th>Review</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="myTicketsBody">
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                    <td>{{ $ticket->client->name }}</td>
                                    <td>{{ Str::limit($ticket->subject, 30) }}</td>
                                    <td><span class="badge badge-info">{{ $ticket->support_type }}</span></td>
                                    <td>
                                        @switch($ticket->status)
                                            @case('Pending')
                                                <span class="badge badge-warning">Pending</span>
                                            @break

                                            @case('Receive')
                                                <span class="badge badge-info">Receive</span>
                                            @break

                                            @case('Send to Logic')
                                                <span class="badge badge-secondary">In Progress</span>
                                            @break

                                            @case('Complete')
                                                <span class="badge badge-success">Complete</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $ticket->received_at ? $ticket->received_at->format('M d, Y') : '-' }}</td>
                                    <td>{{ $ticket->solving_time ? $ticket->solving_time->format('M d, H:i') : '-' }}
                                    </td>
                                    <td>
                                        @if ($ticket->review)
                                            @if ($ticket->review->rating === 'Satisfied')
                                                <span class="badge badge-success"><i class="fas fa-smile"></i>
                                                    Satisfied</span>
                                            @else
                                                <span class="badge badge-danger"><i class="fas fa-frown"></i>
                                                    Dissatisfied</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('support.tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No tickets assigned to you yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                let table = $('#myTicketsTable').DataTable({
                    order: [
                        [5, 'desc']
                    ]
                });

                // Function to get status badge HTML
                function getStatusBadge(status) {
                    switch (status) {
                        case 'Pending':
                            return '<span class="badge badge-warning">Pending</span>';
                        case 'Receive':
                            return '<span class="badge badge-info">Receive</span>';
                        case 'Send to Logic':
                            return '<span class="badge badge-secondary">In Progress</span>';
                        case 'Complete':
                            return '<span class="badge badge-success">Complete</span>';
                        default:
                            return '<span class="badge badge-secondary">' + status + '</span>';
                    }
                }

                // Function to calculate solving time
                function calculateSolvingTime(ticket) {
                    if (!ticket.received_date) return '-';

                    let completedDate = ticket.completed_date ? new Date(ticket.completed_date) : new Date();
                    let receivedDate = new Date(ticket.received_date);
                    let diffMs = completedDate - receivedDate;
                    let diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
                    let diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

                    if (diffDays > 0) {
                        return diffDays + 'd ' + diffHours + 'h';
                    } else {
                        return diffHours + ' hours';
                    }
                }

                // Function to refresh my tickets
                function refreshMyTickets() {
                    $.ajax({
                        url: "{{ route('support.tickets.ajax.stats') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#mySolveCount').text(data.solve_count);
                            $('#myReviewCount').text(data.review_count);
                            $('#mySatisfiedCount').text(data.satisfied_count);
                            $('#myDissatisfiedCount').text(data.dissatisfied_count);
                        }
                    });

                    $.ajax({
                        url: "{{ route('support.tickets.ajax.my-tickets') }}",
                        type: 'GET',
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

                                let receivedDate = ticket.received_date ? new Date(ticket
                                    .received_date).toLocaleDateString() : '-';

                                table.row.add([
                                    '<strong>' + ticket.ticket_number + '</strong>',
                                    ticket.client.name,
                                    ticket.subject.substring(0, 30),
                                    '<span class="badge badge-info">' + ticket
                                    .support_type + '</span>',
                                    getStatusBadge(ticket.status),
                                    receivedDate,
                                    calculateSolvingTime(ticket),
                                    reviewBadge,
                                    '<a href="{{ route('support.tickets.show', '') }}/' +
                                    ticket.id +
                                    '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>'
                                ]);
                            });

                            table.draw();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error refreshing my tickets:', error);
                        }
                    });
                }

                // Refresh every 10 seconds
                setInterval(refreshMyTickets, 10000);
            });
        </script>

    </x-backend.layouts.master>
