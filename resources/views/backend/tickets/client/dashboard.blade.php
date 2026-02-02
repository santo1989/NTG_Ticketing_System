<x-backend.layouts.master>
    <x-slot name="pageTitle">
        My Tickets
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Ticket Dashboard
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('home') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="{{ route('client.tickets.create') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Create New Ticket
                </a>
                <a href="{{ route('client.tickets.reports') }}" class="btn btn-sm btn-info">
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

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('error') }}
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-primary" id="totalTickets">{{ $stats['total'] }}</h3>
                        <p class="mb-0">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-warning" id="pendingTickets">{{ $stats['pending'] }}</h3>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-info" id="receivedTickets">{{ $stats['received'] }}</h3>
                        <p class="mb-0">Receive</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-secondary" id="inProgressTickets">{{ $stats['in_progress'] }}</h3>
                        <p class="mb-0">Send to Logic</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center">
                    <div class="card-body">
                        <h3 class="text-success" id="completedTickets">{{ $stats['completed'] }}</h3>
                        <p class="mb-0">Completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tickets Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">My Tickets</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="ticketsTable">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Subject</th>
                                <th>Support Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Solving Time</th>
                                <th>Closed</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $ticket)
                                <tr>
                                    <td><strong>{{ $ticket->ticket_number }}</strong></td>
                                    <td>{{ Str::limit($ticket->subject, 40) }}</td>
                                    <td><span class="badge bg-info">{{ $ticket->support_type }}</span></td>
                                    <td>
                                        @switch($ticket->status)
                                            @case('Pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @break

                                            @case('Receive')
                                                <span class="badge bg-info">Receive by
                                                    {{ $ticket->supportUser ? $ticket->supportUser->name : 'Unknown' }}</span>
                                            @break

                                            @case('Revise')
                                                <span class="badge bg-warning text-dark">Revise</span>
                                            @break

                                            @case('Send to Logic')
                                                <span class="badge bg-secondary">Send to Logic</span>
                                            @break

                                            @case('Complete')
                                                <span class="badge bg-success">Complete</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @if ($ticket->solving_time)
                                            {{ $ticket->solving_time->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($ticket->completed_at)
                                            {{ $ticket->completed_at->format('M d, Y H:i') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($ticket->supportUser)
                                            {{ $ticket->supportUser->name }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('client.tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if ($ticket->canBeEditedByClient())
                                            <a href="{{ route('client.tickets.edit', $ticket) }}"
                                                class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('client.tickets.destroy', $ticket) }}"
                                                method="POST" class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this ticket?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if ($ticket->isCompleted() && !$ticket->review)
                                            <button type="button" class="btn btn-sm btn-success"
                                                onclick="submitSatisfiedReview({{ $ticket->id }})" title="Satisfied">
                                                <i class="fas fa-thumbs-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger"
                                                onclick="showDissatisfiedModal({{ $ticket->id }})"
                                                title="Dissatisfied">
                                                <i class="fas fa-thumbs-down"></i>
                                            </button>
                                        @endif
                                        @if ($ticket->review)
                                            @if ($ticket->review->rating === 'Satisfied')
                                                <span class="badge bg-success" title="Satisfied">
                                                    <i class="fas fa-thumbs-up"></i> Satisfied
                                                </span>
                                            @else
                                                <span class="badge bg-danger" title="Dissatisfied">
                                                    <i class="fas fa-thumbs-down"></i> Dissatisfied
                                                </span>
                                                @if ($ticket->review->rating === 'Dissatisfied')
                                                    <button type="button" class="btn btn-sm btn-warning"
                                                        onclick="openReviseReviewModal({{ $ticket->id }})"
                                                        title="Revise Review">
                                                        <i class="fas fa-edit"></i> Revise
                                                    </button>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No tickets found. <a
                                                href="{{ route('client.tickets.create') }}">Create your first ticket</a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dissatisfied Review Modal -->
        <div class="modal fade" id="dissatisfiedModal" tabindex="-1" role="dialog"
            aria-labelledby="dissatisfiedModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="dissatisfiedModalLabel">
                            <i class="fas fa-thumbs-down"></i> Dissatisfied Review
                        </h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="dissatisfiedReviewForm">
                        @csrf
                        <input type="hidden" id="dissatisfied_ticket_id" name="ticket_id">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="feedback">Feedback <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="feedback" name="feedback" rows="4"
                                    placeholder="Please provide your feedback..." required></textarea>
                                <small class="form-text text-muted">This field is mandatory for dissatisfied
                                    reviews.</small>
                            </div>
                            <div class="form-group">
                                <label for="reason">Reason (Optional)</label>
                                <input type="text" class="form-control" id="reason" name="reason"
                                    placeholder="Brief reason for dissatisfaction">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-paper-plane"></i> Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Revise Review Modal -->
        <div class="modal fade" id="reviseReviewModal" tabindex="-1" role="dialog"
            aria-labelledby="reviseReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-warning text-white">
                        <h5 class="modal-title" id="reviseReviewModalLabel">
                            <i class="fas fa-edit"></i> Revise Your Review
                        </h5>
                        <button type="button" class="btn-close text-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form id="reviseReviewForm">
                        @csrf
                        <input type="hidden" id="revise_ticket_id" name="ticket_id">
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <strong><i class="fas fa-info-circle"></i> Note:</strong> You are revising your
                                dissatisfied review. Your previous feedback will be kept in history.
                            </div>
                            <div class="form-group">
                                <label>Rating</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="revise_satisfied"
                                            name="rating" value="Satisfied">
                                        <label class="form-check-label" for="revise_satisfied">
                                            <i class="fas fa-thumbs-up text-success"></i> Satisfied
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" id="revise_dissatisfied"
                                            name="rating" value="Dissatisfied" checked>
                                        <label class="form-check-label" for="revise_dissatisfied">
                                            <i class="fas fa-thumbs-down text-danger"></i> Dissatisfied
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="revise_reason_group">
                                <label for="revise_reason">Reason (Optional)</label>
                                <input type="text" class="form-control" id="revise_reason" name="reason"
                                    placeholder="Brief reason for dissatisfaction">
                            </div>
                            <div class="form-group">
                                <label for="revise_feedback">Feedback <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="revise_feedback" name="feedback" rows="4"
                                    placeholder="Please provide your feedback..." required></textarea>
                                <small class="form-text text-muted">This field is mandatory for dissatisfied
                                    reviews.</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Update Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <script>
            // Open Revise Review Modal (global scope)
            window.openReviseReviewModal = function(ticketId) {
                console.log('Opening revise review modal for ticket:', ticketId);
                $('#revise_ticket_id').val(ticketId);
                $('#revise_feedback').val('');
                $('#revise_reason').val('');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('reviseReviewModal')).show();
            };

            // Submit Satisfied Review (global scope)
            window.submitSatisfiedReview = function(ticketId) {
                if (!confirm('Are you satisfied with this ticket resolution?')) {
                    return;
                }

                $.ajax({
                    url: '/my-tickets/' + ticketId + '/review',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        rating: 'Satisfied'
                    },
                    success: function(response) {
                        if (response.success) {
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        console.log('Error submitting review:', xhr);
                        alert('Error submitting review. Please try again.');
                    }
                });
            };

            // Show Dissatisfied Modal (global scope)
            window.showDissatisfiedModal = function(ticketId) {
                console.log('Opening dissatisfied modal for ticket:', ticketId);
                $('#dissatisfied_ticket_id').val(ticketId);
                $('#feedback').val('');
                $('#reason').val('');
                bootstrap.Modal.getOrCreateInstance(document.getElementById('dissatisfiedModal')).show();
            };

            $(document).ready(function() {
                // Function to render ticket rows
                function renderTicketRows(tickets) {
                    let html = '';
                    if (tickets.length === 0) {
                        html =
                            '<tr><td colspan="9" class="text-center">No tickets found. <a href="{{ route('client.tickets.create') }}">Create your first ticket</a></td></tr>';
                    } else {
                        tickets.forEach(ticket => {
                            let statusHtml = '';
                            if (ticket.status === 'Pending') {
                                statusHtml = '<span class="badge bg-warning text-dark">Pending</span>';
                            } else if (ticket.status === 'Receive') {
                                let supportUserName = ticket.support_user ? ticket.support_user.name :
                                    'Unknown';
                                statusHtml = '<span class="badge bg-info">Receive by ' +
                                    supportUserName + '</span>';
                            } else if (ticket.status === 'Revise') {
                                statusHtml = '<span class="badge bg-warning text-dark">Revise</span>';
                            } else if (ticket.status === 'Send to Logic') {
                                statusHtml = '<span class="badge bg-secondary">Send to Logic</span>';
                            } else if (ticket.status === 'Complete') {
                                statusHtml = '<span class="badge bg-success">Complete</span>';
                            }

                            let createdDate = new Date(ticket.created_at).toLocaleDateString('en-US', {
                                year: 'numeric',
                                month: 'short',
                                day: '2-digit'
                            });

                            let solvingTime = ticket.solving_time ? new Date(ticket.solving_time)
                                .toLocaleString('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) : '-';

                            let closedTime = ticket.completed_at ? new Date(ticket.completed_at).toLocaleString(
                                'en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: '2-digit',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                }) : '-';

                            let assignedTo = ticket.support_user ? ticket.support_user.name :
                                '<span class="text-muted">Not assigned</span>';

                            let reviewBadge = '';
                            if (ticket.review) {
                                if (ticket.review.rating === 'Satisfied') {
                                    reviewBadge =
                                        '<span class="badge bg-success" title="Satisfied"><i class="fas fa-thumbs-up"></i> Satisfied</span>';
                                } else {
                                    reviewBadge =
                                        '<span class="badge bg-danger" title="Dissatisfied"><i class="fas fa-thumbs-down"></i> Dissatisfied</span><button type="button" class="btn btn-sm btn-warning ms-1" onclick="openReviseReviewModal(' +
                                        ticket.id +
                                        ')" title="Revise Review"><i class="fas fa-edit"></i> Revise</button>';
                                }
                            } else if (ticket.status === 'Complete') {
                                reviewBadge =
                                    '<button type="button" class="btn btn-sm btn-success" onclick="submitSatisfiedReview(' +
                                    ticket.id +
                                    ')" title="Satisfied"><i class="fas fa-thumbs-up"></i></button><button type="button" class="btn btn-sm btn-danger" onclick="showDissatisfiedModal(' +
                                    ticket.id +
                                    ')" title="Dissatisfied"><i class="fas fa-thumbs-down"></i></button>';
                            }

                            html += `<tr>
                                    <td><strong>${ticket.ticket_number}</strong></td>
                                    <td>${ticket.subject.substring(0, 40)}</td>
                                    <td><span class="badge bg-info">${ticket.support_type}</span></td>
                                    <td>${statusHtml}</td>
                                    <td>${createdDate}</td>
                                    <td>${solvingTime}</td>
                                    <td>${closedTime}</td>
                                    <td>${assignedTo}</td>
                                    <td>
                                        <a href="{{ route('client.tickets.show', '') }}/${ticket.id}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        ${ticket.canBeEditedByClient ? '<a href="{{ route('client.tickets.edit', '') }}/' + ticket.id + '" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>' : ''}
                                        ${reviewBadge}
                                    </td>
                                </tr>`;
                        });
                    }
                    $('#ticketsTable tbody').html(html);
                }

                // Function to refresh all dashboard data
                function refreshDashboard() {
                    $.ajax({
                        url: "{{ route('client.tickets.ajax.stats') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            $('#totalTickets').text(data.total);
                            $('#pendingTickets').text(data.pending);
                            $('#receivedTickets').text(data.received);
                            $('#inProgressTickets').text(data.in_progress);
                            $('#completedTickets').text(data.completed);
                        },
                        error: function(xhr, status, error) {
                            console.log('Error refreshing stats:', error);
                        }
                    });

                    $.ajax({
                        url: "{{ route('client.tickets.ajax.tickets') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            renderTicketRows(data.tickets);
                        },
                        error: function(xhr, status, error) {
                            console.log('Error refreshing tickets:', error);
                        }
                    });
                }

                // Refresh every 10 seconds
                // COMMENTED OUT: Auto-refresh was preventing smooth scrolling
                // setInterval(refreshDashboard, 10000);

                // Handle Dissatisfied Review Form Submission
                $('#dissatisfiedReviewForm').on('submit', function(e) {
                    e.preventDefault();
                    console.log('Dissatisfied form submitted');

                    let ticketId = $('#dissatisfied_ticket_id').val();
                    let feedback = $('#feedback').val().trim();
                    let reason = $('#reason').val().trim();

                    console.log('Ticket ID:', ticketId, 'Feedback:', feedback, 'Reason:', reason);

                    if (!feedback) {
                        alert('Feedback is required for dissatisfied reviews.');
                        return;
                    }

                    $.ajax({
                        url: '/my-tickets/' + ticketId + '/review',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rating: 'Dissatisfied',
                            feedback: feedback,
                            reason: reason
                        },
                        success: function(response) {
                            console.log('Success response:', response);
                            if (response.success) {
                                bootstrap.Modal.getOrCreateInstance(document.getElementById(
                                    'dissatisfiedModal')).hide();
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            console.log('Error submitting review:', xhr);
                            console.log('Response text:', xhr.responseText);
                            alert('Error submitting review. Please try again.');
                        }
                    });
                });

                // Handle Revise Review Form Submission
                $('#reviseReviewForm').on('submit', function(e) {
                    e.preventDefault();
                    console.log('Revise review form submitted');

                    let ticketId = $('#revise_ticket_id').val();
                    let rating = $('input[name="rating"]:checked').val();
                    let feedback = $('#revise_feedback').val().trim();
                    let reason = $('#revise_reason').val().trim();

                    console.log('Ticket ID:', ticketId, 'Rating:', rating, 'Feedback:', feedback, 'Reason:',
                        reason);

                    if (!feedback) {
                        alert('Feedback is required for reviews.');
                        return;
                    }

                    $.ajax({
                        url: '/my-tickets/' + ticketId + '/review',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            rating: rating,
                            feedback: feedback,
                            reason: reason
                        },
                        success: function(response) {
                            console.log('Success response:', response);
                            if (response.success) {
                                bootstrap.Modal.getOrCreateInstance(document.getElementById(
                                    'reviseReviewModal')).hide();
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            console.log('Error revising review:', xhr);
                            console.log('Response text:', xhr.responseText);
                            alert('Error revising review. Please try again.');
                        }
                    });
                });

                // Toggle reason field based on rating selection in revise modal
                $('#revise_satisfied, #revise_dissatisfied').on('change', function() {
                    let rating = $('input[name="rating"]:checked').val();
                    if (rating === 'Dissatisfied') {
                        $('#revise_reason_group').show();
                    } else {
                        $('#revise_reason_group').hide();
                    }
                });
            });
        </script>

    </x-backend.layouts.master>
