<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Ticket #{{ $ticket->ticket_number }}
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Ticket Details
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
            <div class="alert alert-success alert-dismissible fade show mb-4">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <!-- Header Card with Quick Info -->
        <div class="card mb-4 border-left-primary card-dynamic shadow-lg animate-slide-in">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="mb-3 font-weight-bold text-dark">{{ $ticket->subject }}</h3>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge badge-lg badge-dark badge-animated">
                                <i class="fas fa-ticket-alt mr-1"></i>{{ $ticket->ticket_number }}
                            </span>
                            <span class="badge badge-lg badge-info badge-animated">
                                <i class="fas fa-headset mr-1"></i>{{ $ticket->support_type }}
                            </span>
                            @switch($ticket->status)
                                @case('Pending')
                                    <span class="badge badge-lg badge-warning badge-animated badge-pulse">
                                        <i class="fas fa-clock mr-1"></i>Pending
                                    </span>
                                @break

                                @case('Receive')
                                    <span class="badge badge-lg badge-info badge-animated">
                                        <i class="fas fa-check mr-1"></i>Receive
                                    </span>
                                @break

                                @case('Revise')
                                    <span class="badge badge-lg badge-warning badge-animated">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>Revise
                                    </span>
                                @break

                                @case('Send to Logic')
                                    <span class="badge badge-lg badge-secondary badge-animated">
                                        <i class="fas fa-cogs mr-1"></i>In Progress
                                    </span>
                                @break

                                @case('Complete')
                                    <span class="badge badge-lg badge-success badge-animated">
                                        <i class="fas fa-check-circle mr-1"></i>Complete
                                    </span>
                                @break
                            @endswitch
                        </div>
                    </div>
                    <div class="col-md-4 text-md-right">
                        <div class="info-box">
                            <div class="mb-2">
                                <i class="fas fa-calendar-plus text-primary"></i>
                                <strong>Created:</strong>
                                <span class="text-dark">{{ $ticket->created_at->format('M d, Y') }}</span>
                            </div>
                            @if ($ticket->completed_at)
                                <div>
                                    <i class="fas fa-calendar-check text-success"></i>
                                    <strong>Closed:</strong>
                                    <span class="text-dark">{{ $ticket->completed_at->format('M d, Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Main Content Area -->
            <div class="col-lg-8">
                <!-- Ticket Information Card -->
                <div class="card mb-4 card-dynamic shadow-hover animate-fade-in">
                    <div
                        class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-info-circle mr-2"></i> Ticket Information</h6>
                        <button class="btn btn-sm btn-light btn-toggle" data-bs-toggle="collapse"
                            data-bs-target="#ticketInfoCollapse">
                            <i class="fas fa-chevron-down transition-rotate"></i>
                        </button>
                    </div>
                    <div class="collapse show" id="ticketInfoCollapse">
                        <div class="card-body p-4">
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase font-weight-bold">Client</label>
                                    <p class="mb-0">{{ $ticket->client->name }}</p>
                                    <small class="text-muted">{{ $ticket->client->email }}</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase font-weight-bold">Assigned To</label>
                                    @if ($ticket->supportUser)
                                        <p class="mb-0">{{ $ticket->supportUser->name }}</p>
                                    @else
                                        <p class="mb-0"><span class="badge badge-secondary">Unassigned</span></p>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase font-weight-bold">Created Date</label>
                                    <p class="mb-0">{{ $ticket->created_at->format('F d, Y \a\t h:i A') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase font-weight-bold">Received
                                        Date</label>
                                    <p class="mb-0">
                                        {{ $ticket->received_at ? $ticket->received_at->format('F d, Y \a\t h:i A') : '-' }}
                                    </p>
                                </div>
                            </div>
                            @if ($ticket->solving_time)
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="text-muted small text-uppercase font-weight-bold">Solving
                                            Time</label>
                                        <p class="mb-0">{{ $ticket->solving_time->format('F d, Y \a\t h:i A') }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small text-uppercase font-weight-bold">Completed
                                            At</label>
                                        <p class="mb-0">
                                            {{ $ticket->completed_at ? $ticket->completed_at->format('F d, Y \a\t h:i A') : '-' }}
                                        </p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="card mb-4 card-dynamic shadow-hover animate-fade-in" style="animation-delay: 0.1s">
                    <div
                        class="card-header bg-gradient-info text-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-file-alt mr-2"></i> Description</h6>
                        <button class="btn btn-sm btn-light btn-toggle" data-bs-toggle="collapse"
                            data-bs-target="#descriptionCollapse">
                            <i class="fas fa-chevron-down transition-rotate"></i>
                        </button>
                    </div>
                    <div class="collapse show" id="descriptionCollapse">
                        <div class="card-body p-4 bg-light-gradient">
                            <p class="text-dark line-height-xl mb-0">{{ $ticket->description }}</p>
                        </div>
                    </div>
                </div>

                <!-- Remarks Card -->
                @if ($ticket->remarks)
                    <div class="card mb-4 card-dynamic shadow-hover animate-fade-in" style="animation-delay: 0.2s">
                        <div
                            class="card-header bg-gradient-warning text-white d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-comments mr-2"></i> Remarks</h6>
                            <button class="btn btn-sm btn-light btn-toggle" data-bs-toggle="collapse"
                                data-bs-target="#remarksCollapse">
                                <i class="fas fa-chevron-down transition-rotate"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="remarksCollapse">
                            <div class="card-body p-4 bg-light-gradient">
                                <p class="text-dark line-height-xl mb-0">{{ $ticket->remarks }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Attachment Card -->
                @if ($ticket->file_path)
                    <div class="card mb-4 card-dynamic shadow-hover animate-fade-in" style="animation-delay: 0.3s">
                        <div class="card-header bg-gradient-success text-white">
                            <h6 class="mb-0"><i class="fas fa-paperclip mr-2"></i> Attachment</h6>
                        </div>
                        <div class="card-body p-4 text-center">
                            <div class="attachment-icon mb-3">
                                <i class="fas fa-file-download fa-3x text-success pulse-icon"></i>
                            </div>
                            <a href="{{ Storage::url($ticket->file_path) }}" target="_blank"
                                class="btn btn-success btn-lg btn-icon-split shadow-sm">
                                <span class="icon"><i class="fas fa-download"></i></span>
                                <span class="text">Download File</span>
                            </a>
                            <small class="text-muted d-block mt-3">
                                <i class="fas fa-paperclip"></i> {{ basename($ticket->file_path) }}
                            </small>
                        </div>
                    </div>
                @endif

                <!-- Client Review Card -->
                @if ($ticket->review)
                    <div
                        class="card mb-4 shadow-sm border-left-{{ $ticket->review->rating == 'Satisfied' ? 'success' : 'danger' }}">
                        <div
                            class="card-header {{ $ticket->review->rating == 'Satisfied' ? 'bg-gradient-success' : 'bg-gradient-danger' }} text-white">
                            <h6 class="mb-0"><i class="fas fa-star"></i> Client Review</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase font-weight-bold">Rating</label>
                                    @if ($ticket->review->rating == 'Satisfied')
                                        <p class="mb-0"><span class="badge badge-success"><i
                                                    class="fas fa-thumbs-up"></i> Satisfied</span></p>
                                    @else
                                        <p class="mb-0"><span class="badge badge-danger"><i
                                                    class="fas fa-thumbs-down"></i> Dissatisfied</span></p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small text-uppercase font-weight-bold">Reviewed
                                        Date</label>
                                    <p class="mb-0">{{ $ticket->review->created_at->format('F d, Y \a\t h:i A') }}
                                    </p>
                                </div>
                            </div>
                            @if ($ticket->review->reason)
                                <div class="mb-3">
                                    <label class="text-muted small text-uppercase font-weight-bold">Reason</label>
                                    <p class="mb-0">{{ $ticket->review->reason }}</p>
                                </div>
                            @endif
                            @if ($ticket->review->feedback)
                                <div>
                                    <label class="text-muted small text-uppercase font-weight-bold">Feedback</label>
                                    <p class="mb-0">{{ $ticket->review->feedback }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Previous Reviews Table -->
                    @if ($ticket->reviewHistories && $ticket->reviewHistories->count() > 0)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-gradient-secondary text-white">
                                <h6 class="mb-0"><i class="fas fa-history"></i> Review History</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Rating</th>
                                                <th>Reason</th>
                                                <th>Feedback</th>
                                                <th>Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($ticket->reviewHistories as $history)
                                                <tr>
                                                    <td>
                                                        @if ($history->rating === 'Satisfied')
                                                            <span class="badge badge-success"><i
                                                                    class="fas fa-thumbs-up"></i> Satisfied</span>
                                                        @else
                                                            <span class="badge badge-danger"><i
                                                                    class="fas fa-thumbs-down"></i> Dissatisfied</span>
                                                        @endif
                                                    </td>
                                                    <td><small>{{ $history->reason ?? '-' }}</small></td>
                                                    <td><small>{{ Str::limit($history->feedback ?? '-', 30) }}</small>
                                                    </td>
                                                    <td><small>{{ $history->reviewed_at ? $history->reviewed_at->format('M d, Y') : $history->created_at->format('M d, Y') }}</small>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                <!-- Feedback Section (for Send to Logic status) -->
                @if ($ticket->status === 'Send to Logic' && $ticket->support_user_id == Auth::id())
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-gradient-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-comments"></i> Add Feedback</h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('support.tickets.feedback.store', $ticket) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="feedback">Feedback to Client</label>
                                    <textarea name="feedback" id="feedback" class="form-control" rows="4"
                                        placeholder="Provide feedback to the client about the ticket progress..." required></textarea>
                                    <small class="form-text text-muted">
                                        This feedback will be visible to the client in their dashboard.
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Send Feedback
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Feedback History -->
                @if ($ticket->feedbacks && $ticket->feedbacks->count() > 0)
                    <div class="card shadow-sm mb-3">
                        <div class="card-header bg-gradient-info text-white">
                            <h6 class="mb-0"><i class="fas fa-comment-dots"></i> Feedback History</h6>
                        </div>
                        <div class="card-body">
                            @foreach ($ticket->feedbacks as $feedback)
                                <div class="feedback-item mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <strong class="text-primary">
                                                <i class="fas fa-user-circle"></i> {{ $feedback->supportUser->name }}
                                            </strong>
                                            <span class="text-muted small">
                                                ({{ $feedback->supportUser->role->name }})
                                            </span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="fas fa-clock"></i> {{ $feedback->created_at->diffForHumans() }}
                                            <br>
                                            <span
                                                class="text-muted small">{{ $feedback->created_at->format('M d, Y h:i A') }}</span>
                                        </small>
                                    </div>
                                    <div class="feedback-content">
                                        <p class="mb-0">{{ $feedback->feedback }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Activity Timeline -->
                @if ($ticket->activities && $ticket->activities->count() > 0)
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-dark text-white">
                            <h6 class="mb-0"><i class="fas fa-history"></i> Activity Timeline</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @foreach ($ticket->activities as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-marker">
                                            @switch($activity->action)
                                                @case('received')
                                                    <span class="badge badge-success"><i class="fas fa-hand-paper"></i></span>
                                                @break

                                                @case('forwarded')
                                                    <span class="badge badge-warning"><i class="fas fa-share"></i></span>
                                                @break

                                                @case('status_changed')
                                                    <span class="badge badge-info"><i class="fas fa-sync"></i></span>
                                                @break

                                                @case('completed')
                                                    <span class="badge badge-success"><i
                                                            class="fas fa-check-circle"></i></span>
                                                @break

                                                @case('review_submitted')
                                                    <span class="badge badge-primary"><i class="fas fa-star"></i></span>
                                                @break
                                            @endswitch
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1 text-dark">
                                                @switch($activity->action)
                                                    @case('received')
                                                        <i class="fas fa-hand-paper text-success"></i> Ticket Received
                                                    @break

                                                    @case('forwarded')
                                                        <i class="fas fa-share text-warning"></i> Forwarded from
                                                        {{ $activity->from_user }} to {{ $activity->to_user }}
                                                    @break

                                                    @case('status_changed')
                                                        <i class="fas fa-sync text-info"></i> Status Changed:
                                                        {{ $activity->old_status }} → {{ $activity->new_status }}
                                                    @break

                                                    @case('completed')
                                                        <i class="fas fa-check-circle text-success"></i> Ticket Completed
                                                    @break

                                                    @case('review_submitted')
                                                        <i class="fas fa-star text-primary"></i> Client Review Submitted
                                                    @break
                                                @endswitch
                                            </h6>
                                            <p class="text-muted mb-1 small">
                                                <strong>{{ $activity->user->name }}</strong>
                                            </p>
                                            @if ($activity->description)
                                                <p class="text-muted mb-1 small">{{ $activity->description }}</p>
                                            @endif
                                            <p class="text-muted mb-0 small">
                                                {{ $activity->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="col-lg-4">
                <!-- Receive Ticket Action -->
                @if ($ticket->status !== 'Receive' && $ticket->status !== 'Complete')
                    <div class="card mb-3 card-dynamic shadow-hover border-left-success animate-slide-right">
                        <div class="card-header bg-gradient-success text-white">
                            <h6 class="mb-0"><i class="fas fa-hand-paper mr-2"></i> Receive Ticket</h6>
                        </div>
                        <div class="card-body p-4">
                            <p class="text-muted small mb-3">Click below to take ownership of this ticket</p>
                            <!-- DEBUG: Ticket ID is {{ $ticket->id }} -->
                            <form action="{{ route('support.tickets.receive', ['ticket' => $ticket->id]) }}"
                                method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block btn-lg shadow-sm btn-action">
                                    <i class="fas fa-hand-paper mr-2"></i> Receive This Ticket
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Update Status Action -->
                @if ($ticket->support_user_id == Auth::id() && $ticket->status !== 'Complete')
                    <div class="card mb-3 card-dynamic shadow-hover border-left-primary animate-slide-right"
                        style="animation-delay: 0.1s">
                        <div class="card-header bg-gradient-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-edit mr-2"></i> Update Status</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('support.tickets.update-status', ['ticket' => $ticket->id]) }}"
                                method="POST">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="status" class="small font-weight-bold">Status <span
                                            class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control form-control-sm"
                                        required>
                                        <option value="">Select Status</option>
                                        @if ($ticket->status === 'Ticket Received')
                                            <option value="Send to Logic">Send to Logic</option>
                                        @elseif ($ticket->status === 'Receive')
                                            <option value="Send to Logic">Send to Logic</option>
                                        @else
                                            <option value="Ticket Received"
                                                {{ $ticket->status == 'Ticket Received' ? 'selected' : '' }}>Ticket
                                                Received</option>
                                            <option value="Send to Logic">Send to Logic</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="remarks" class="small font-weight-bold">Remarks <span
                                            class="text-danger">*</span></label>
                                    <textarea name="remarks" id="remarks" rows="3" class="form-control form-control-sm" required>{{ old('remarks', $ticket->remarks) }}</textarea>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="solving_time" class="small font-weight-bold">Expected Solving
                                        Time</label>
                                    <input type="datetime-local" name="solving_time" id="solving_time"
                                        class="form-control form-control-sm"
                                        value="{{ old('solving_time', $ticket->solving_time ? $ticket->solving_time->format('Y-m-d\TH:i') : '') }}">
                                </div>
                                <button type="button" class="btn btn-primary btn-block btn-action"
                                    id="updateStatusBtn">
                                    <i class="fas fa-save mr-2"></i> Update Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Complete Ticket Action -->
                    <div class="card mb-3 card-dynamic shadow-hover border-left-success animate-slide-right"
                        style="animation-delay: 0.2s">
                        <div class="card-header bg-gradient-success text-white">
                            <h6 class="mb-0"><i class="fas fa-check-circle mr-2"></i> Complete Ticket</h6>
                        </div>
                        <div class="card-body p-4">
                            <form action="{{ route('support.tickets.complete', ['ticket' => $ticket->id]) }}"
                                method="POST" class="complete-ticket-form">
                                @csrf
                                <div class="form-group mb-3">
                                    <label for="complete_remarks" class="small font-weight-bold">Solution/Final
                                        Remarks <span class="text-danger">*</span></label>
                                    <textarea name="remarks" id="complete_remarks" rows="3" class="form-control"
                                        placeholder="Describe the solution provided..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-success btn-block btn-action"
                                    id="completeTicketBtn">
                                    <i class="fas fa-check-circle mr-2"></i> Mark Complete & Send Email
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Quick Status Card -->
                <div class="card shadow-sm">
                    <div class="card-header bg-gradient-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-tachometer-alt"></i> Quick Status</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted text-uppercase font-weight-bold d-block mb-1">Current
                                Status</small>
                            @switch($ticket->status)
                                @case('Pending')
                                    <span class="badge badge-warning px-3 py-2">Pending</span>
                                @break

                                @case('Ticket Received')
                                    <span class="badge badge-info px-3 py-2">Ticket Received</span>
                                @break

                                @case('Receive')
                                    <span class="badge badge-info px-3 py-2">Receive</span>
                                @break

                                @case('Send to Logic')
                                    <span class="badge badge-secondary px-3 py-2">In Progress</span>
                                @break

                                @case('Complete')
                                    <span class="badge badge-success px-3 py-2">Complete</span>
                                @break
                            @endswitch
                        </div>
                        <hr>
                        <div class="mb-2">
                            <small class="text-muted">Priority: <strong class="text-dark">Normal</strong></small>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Type: <strong
                                    class="text-dark">{{ $ticket->support_type }}</strong></small>
                        </div>
                        <div>
                            <small class="text-muted">Days Open: <strong
                                    class="text-dark">{{ now()->diffInDays($ticket->created_at) }}</strong></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <style>
        /* Border Effects */
        .border-left-primary {
            border-left: 5px solid #007bff !important;
        }

        .border-left-success {
            border-left: 5px solid #28a745 !important;
        }

        .border-left-danger {
            border-left: 5px solid #dc3545 !important;
        }

        /* Gradient Backgrounds */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        }

        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%) !important;
        }

        .bg-gradient-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%) !important;
        }

        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8 0%, #0c5460 100%) !important;
        }

        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%) !important;
        }

        .bg-gradient-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #545b62 100%) !important;
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #343a40 0%, #23272b 100%) !important;
        }

        .bg-light-gradient {
            background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
        }

        /* Badge Enhancements */
        .badge-lg {
            font-size: 0.95rem;
            padding: 0.6rem 0.85rem !important;
            font-weight: 600;
            border-radius: 0.5rem;
        }

        .badge-animated {
            transition: all 0.3s ease;
            cursor: default;
        }

        .badge-animated:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .badge-pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        /* Card Enhancements */
        .card-dynamic {
            border: none;
            border-radius: 0.75rem;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .shadow-hover {
            box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.08);
        }

        .shadow-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
        }

        .shadow-lg {
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.12) !important;
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.6s ease-out;
        }

        .animate-fade-in {
            animation: fadeIn 0.8s ease-out;
            animation-fill-mode: both;
        }

        .animate-slide-right {
            animation: slideRight 0.6s ease-out;
            animation-fill-mode: both;
        }

        /* Button Enhancements */
        .btn-action {
            transition: all 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-action:hover {
            transform: scale(1.05);
        }

        .btn-toggle {
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            transition: all 0.3s ease;
        }

        .btn-toggle:hover {
            transform: rotate(180deg);
        }

        .btn-icon-split {
            border-radius: 0.5rem;
            font-weight: 600;
            padding: 0.75rem 1.5rem;
        }

        .btn-icon-split .icon {
            padding-right: 0.75rem;
            border-right: 1px solid rgba(255, 255, 255, 0.3);
            margin-right: 0.75rem;
        }

        /* Icon Animations */
        .pulse-icon {
            animation: pulseIcon 2s infinite;
        }

        @keyframes pulseIcon {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        .transition-rotate {
            transition: transform 0.3s ease;
        }

        .collapsed .transition-rotate {
            transform: rotate(-90deg);
        }

        /* Typography */
        .gap-2 {
            gap: 0.5rem;
        }

        .line-height-lg {
            line-height: 1.6;
        }

        .line-height-xl {
            line-height: 1.8;
        }

        .font-weight-500 {
            font-weight: 500;
        }

        .info-box {
            background: linear-gradient(to right, #f8f9fa, #ffffff);
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 3px solid #007bff;
        }

        .info-item {
            padding: 0.5rem;
            border-radius: 0.375rem;
            transition: background-color 0.3s ease;
        }

        .info-item:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        /* Timeline Enhancements */
        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 25px;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: -22px;
            top: 30px;
            bottom: -25px;
            width: 2px;
            background: linear-gradient(to bottom, #007bff, #dee2e6);
        }

        .timeline-marker {
            position: absolute;
            left: -32px;
            top: 0;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        .timeline-marker .badge {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .timeline-content {
            padding-left: 15px;
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
            border-left: 3px solid #007bff;
            transition: all 0.3s ease;
        }

        .timeline-content:hover {
            background: #ffffff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateX(5px);
        }

        /* Table Enhancements */
        .table-hover tbody tr {
            transition: all 0.3s ease;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: scale(1.01);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .col-md-right {
                text-align: left !important;
                margin-top: 1rem;
            }

            .badge-lg {
                font-size: 0.8rem;
                padding: 0.4rem 0.6rem !important;
            }

            .shadow-hover:hover {
                transform: none;
            }
        }

        /* Collapse Arrow Rotation */
        .btn-toggle[aria-expanded="false"] .transition-rotate {
            transform: rotate(-90deg);
        }

        .btn-toggle[aria-expanded="true"] .transition-rotate {
            transform: rotate(0deg);
        }
    </style>



    <script>
        $(document).ready(function() {
            // ========== Collapse Toggle Rotation ==========
            $('[data-bs-toggle="collapse"]').on('click', function(e) {
                e.preventDefault();
                var targetSelector = $(this).attr('data-bs-target') || $(this).attr('data-target');
                var targetEl = document.querySelector(targetSelector);
                if (targetEl) {
                    var bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetEl);
                    bsCollapse.toggle();
                }
                $(this).find('.transition-rotate').toggleClass('rotate-icon');
            });

            // ========== Smooth Scroll for Internal Links ==========
            $('a[href^="#"]').on('click', function(e) {
                var target = $(this.getAttribute('href'));
                if (target.length) {
                    e.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 100
                    }, 800);
                }
            });

            // ========== Add Animation Delay to Cards ==========
            $('.animate-fade-in').each(function(index) {
                $(this).css('animation-delay', (index * 0.1) + 's');
            });

            // ========== Form Submission Handlers ==========

            // Update Status Form Handler
            $('#updateStatusBtn').on('click', function(e) {
                e.preventDefault();
                console.log('Update Status button clicked');
                var form = $(this).closest('form');
                var btn = $(this);

                // Get form values
                var status = form.find('#status').val();
                var remarks = form.find('#remarks').val();

                console.log('Status:', status);
                console.log('Remarks:', remarks);

                // Basic validation
                if (!status) {
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please select a status',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }

                if (!remarks || remarks.trim() === '') {
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please enter remarks',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }

                // Show confirmation
                Swal.fire({
                    title: 'Update Ticket Status',
                    text: 'Are you sure you want to update this ticket status to "' + status + '"?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#667eea',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, update it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-popup-custom',
                        confirmButton: 'swal-btn-custom',
                        cancelButton: 'swal-btn-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true);
                        btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Updating...');
                        form.data('submitting', true);
                        form.submit();
                    }
                });
            });

            // Complete Ticket Form Handler
            $('#completeTicketBtn').on('click', function(e) {
                e.preventDefault();
                var form = $(this).closest('form');
                var btn = $(this);
                var remarks = form.find('#complete_remarks').val();

                // Basic validation
                if (!remarks || remarks.trim() === '') {
                    Swal.fire({
                        title: 'Validation Error',
                        text: 'Please provide the solution/final remarks',
                        icon: 'error',
                        confirmButtonColor: '#667eea'
                    });
                    return false;
                }

                // Show confirmation
                Swal.fire({
                    title: 'Complete Ticket',
                    html: 'Mark this ticket as complete and send email to the client?<br><small class="text-muted">This action cannot be undone.</small>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, complete it!',
                    cancelButtonText: 'Cancel',
                    customClass: {
                        popup: 'swal-popup-custom',
                        confirmButton: 'swal-btn-custom',
                        cancelButton: 'swal-btn-custom'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.prop('disabled', true);
                        btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> Completing...');
                        form.data('submitting', true);
                        form.submit();
                    }
                });
            });

            // Receive Ticket Form Handler
            $('.card-body form').each(function() {
                if ($(this).find('button[type="submit"]').text().includes('Receive')) {
                    $(this).on('submit', function(e) {
                        e.preventDefault();
                        var btn = $(this).find('button[type="submit"]');
                        var form = $(this);

                        Swal.fire({
                            title: 'Receive Ticket',
                            text: 'Are you sure you want to receive this ticket?',
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#28a745',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, receive it!',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                popup: 'swal-popup-custom',
                                confirmButton: 'swal-btn-custom',
                                cancelButton: 'swal-btn-custom'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                btn.prop('disabled', true);
                                btn.html(
                                    '<i class="fas fa-spinner fa-spin mr-2"></i> Receiving...'
                                );
                                form.data('submitting', true);
                                form.unbind('submit').submit();
                            }
                        });
                    });
                }
            });

            // ========== Enhanced Form Controls ==========
            $('select, textarea, input[type="text"], input[type="datetime-local"]').on('focus', function() {
                $(this).closest('.form-group').find('label').css({
                    'color': '#667eea',
                    'font-weight': '700'
                });
            }).on('blur', function() {
                $(this).closest('.form-group').find('label').css({
                    'color': '#4a5568',
                    'font-weight': '600'
                });
            });

            // ========== Button Ripple Effect (Already in Master) ==========
            // This is handled in master.blade.php

            // ========== Collapse Persistence ==========
            $('[data-bs-toggle="collapse"]').on('click', function() {
                var targetSelector = $(this).attr('data-bs-target') || $(this).attr('data-target');
                var target = document.querySelector(targetSelector);
                var isCollapsed = target ? target.classList.contains('show') : false;

                // Store state in sessionStorage
                sessionStorage.setItem(targetSelector, !isCollapsed);
            });

            // Restore collapse state on page load
            $('[data-bs-toggle="collapse"]').each(function() {
                var targetSelector = $(this).attr('data-bs-target') || $(this).attr('data-target');
                var target = document.querySelector(targetSelector);
                var wasCollapsed = sessionStorage.getItem(targetSelector);

                if (target && wasCollapsed === 'false') {
                    target.classList.remove('show');
                    $(this).find('.transition-rotate').addClass('rotate-icon');
                }
            });
        });

        // ========== Prevent Double Form Submission ==========
        $('form').on('submit', function(e) {
            // Allow programmatic confirmed submissions
            if ($(this).data('submitting')) {
                return true;
            }

            var submitBtn = $(this).find('button[type="submit"]');

            if (submitBtn.prop('disabled')) {
                e.preventDefault();
                return false;
            }
        });
    </script>

</x-backend.layouts.master>
