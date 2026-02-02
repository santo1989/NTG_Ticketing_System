<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Ticket Details
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Admin - Ticket #{{ $ticket->ticket_number }}
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>


    <style>
        .timeline {
            position: relative;
            padding: 10px 0;
        }

        .timeline-item {
            display: flex;
            margin-bottom: 20px;
            position: relative;
        }

        .timeline-item:not(:last-child)::before {
            content: '';
            position: absolute;
            left: 12px;
            top: 35px;
            width: 2px;
            height: calc(100% - 35px);
            background: #dee2e6;
        }

        .timeline-badge {
            flex-shrink: 0;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .timeline-badge .badge {
            width: 26px;
            height: 26px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .timeline-content {
            flex: 1;
            margin-left: 15px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 3px solid #dee2e6;
        }

        .timeline-content h6 {
            margin-bottom: 5px;
            font-weight: 600;
            font-size: 14px;
        }

        .timeline-content small {
            display: block;
            color: #999;
            font-size: 12px;
        }

        .timeline-content p {
            margin: 5px 0 0;
            font-size: 13px;
            color: #333;
        }
    </style>


    <div class="container-fluid">
        <div class="mb-3">
            <a href="{{ route('admin.tickets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Tickets
            </a>
            <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Delete this ticket and all related data?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete Ticket
                </button>
            </form>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                {{ session('success') }}
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <tr>
                                <th width="200">Ticket Number</th>
                                <td><strong>{{ $ticket->ticket_number }}</strong></td>
                            </tr>
                            <tr>
                                <th>Client</th>
                                <td>{{ $ticket->client->name }} ({{ $ticket->client->email }})</td>
                            </tr>
                            <tr>
                                <th>Support Type</th>
                                <td><span class="badge badge-info">{{ $ticket->support_type }}</span></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    @switch($ticket->status)
                                        @case('Pending')
                                            <span class="badge badge-warning">Pending</span>
                                        @break

                                        @case('Ticket Received')
                                            <span class="badge badge-info">Ticket Received</span>
                                        @break

                                        @case('MIS Receive Issue')
                                            <span class="badge badge-info">MIS Receive Issue</span>
                                        @break

                                        @case('Send to Logic')
                                            <span class="badge badge-secondary">Send to Logic</span>
                                        @break

                                        @case('Complete')
                                            <span class="badge badge-success">Complete</span>
                                        @break
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <th>Created At</th>
                                <td>{{ $ticket->created_at->format('F d, Y h:i A') }}</td>
                            </tr>
                            @if ($ticket->supportUser)
                                <tr>
                                    <th>Assigned To</th>
                                    <td>{{ $ticket->supportUser->name }}</td>
                                </tr>
                                <tr>
                                    <th>Received At</th>
                                    <td>{{ $ticket->received_at ? $ticket->received_at->format('F d, Y h:i A') : '-' }}
                                    </td>
                                </tr>
                            @endif
                            @if ($ticket->solving_time)
                                <tr>
                                    <th>Expected Solving Time</th>
                                    <td>{{ $ticket->solving_time->format('F d, Y h:i A') }}</td>
                                </tr>
                            @endif
                            @if ($ticket->completed_at)
                                <tr>
                                    <th>Completed At</th>
                                    <td>{{ $ticket->completed_at->format('F d, Y h:i A') }}</td>
                                </tr>
                            @endif
                        </table>

                        <h6 class="mt-4">Description:</h6>
                        <div class="p-3 bg-light">
                            {{ $ticket->description }}
                        </div>

                        @if ($ticket->remarks)
                            <h6 class="mt-4">Remarks:</h6>
                            <div class="p-3 bg-light">
                                {{ $ticket->remarks }}
                            </div>
                        @endif

                        @if ($ticket->file_path)
                            <h6 class="mt-4">Attachment:</h6>
                            <a href="{{ Storage::url($ticket->file_path) }}" target="_blank"
                                class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i> Download Attachment
                            </a>
                        @endif
                    </div>
                </div>

                @if ($ticket->review)
                    <div class="card mt-3">
                        <div
                            class="card-header {{ $ticket->review->rating == 'Satisfied' ? 'bg-success' : 'bg-danger' }} text-white">
                            <h6 class="mb-0"><i class="fas fa-star"></i> Client Review</h6>
                        </div>
                        <div class="card-body">
                            <p><strong>Rating:</strong>
                                @if ($ticket->review->rating == 'Satisfied')
                                    <span class="badge badge-success">Satisfied</span>
                                @else
                                    <span class="badge badge-danger">Dissatisfied</span>
                                @endif
                            </p>
                            @if ($ticket->review->reason)
                                <p><strong>Reason:</strong> {{ $ticket->review->reason }}</p>
                            @endif
                            @if ($ticket->review->feedback)
                                <p><strong>Feedback:</strong> {{ $ticket->review->feedback }}</p>
                            @endif
                            <small class="text-muted">Submitted on
                                {{ $ticket->review->created_at->format('F d, Y h:i A') }}</small>
                        </div>
                    </div>
                @endif

                @if ($ticket->reviewHistories && $ticket->reviewHistories->count() > 0)
                    <div class="card mt-3">
                        <div class="card-header bg-secondary text-white">
                            <h6 class="mb-0"><i class="fas fa-archive"></i> Previous Reviews</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered mb-0">
                                    <thead>
                                        <tr>
                                            <th>Rating</th>
                                            <th>Reason</th>
                                            <th>Feedback</th>
                                            <th>Reviewed At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($ticket->reviewHistories as $history)
                                            <tr>
                                                <td>
                                                    @if ($history->rating === 'Satisfied')
                                                        <span class="badge badge-success">Satisfied</span>
                                                    @else
                                                        <span class="badge badge-danger">Dissatisfied</span>
                                                    @endif
                                                </td>
                                                <td>{{ $history->reason ?? '-' }}</td>
                                                <td>{{ $history->feedback ?? '-' }}</td>
                                                <td>{{ $history->reviewed_at ? $history->reviewed_at->format('F d, Y h:i A') : $history->created_at->format('F d, Y h:i A') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Ticket Activity History -->
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-history"></i> Ticket Activity History</h6>
                    </div>
                    <div class="card-body">
                        @if ($ticket->activities && $ticket->activities->count() > 0)
                            <div class="timeline">
                                @foreach ($ticket->activities as $activity)
                                    <div class="timeline-item">
                                        <div class="timeline-badge">
                                            @switch($activity->action)
                                                @case('received')
                                                    <span class="badge badge-success"><i class="fas fa-check-circle"></i></span>
                                                @break

                                                @case('forwarded')
                                                    <span class="badge badge-warning"><i class="fas fa-share"></i></span>
                                                @break

                                                @case('status_changed')
                                                    <span class="badge badge-info"><i class="fas fa-exchange-alt"></i></span>
                                                @break

                                                @case('completed')
                                                    <span class="badge badge-success"><i
                                                            class="fas fa-flag-checkered"></i></span>
                                                @break

                                                @case('review_submitted')
                                                    <span class="badge badge-secondary"><i class="fas fa-star"></i></span>
                                                @break

                                                @default
                                                    <span class="badge badge-secondary"><i class="fas fa-circle"></i></span>
                                            @endswitch
                                        </div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">
                                                @switch($activity->action)
                                                    @case('received')
                                                        Ticket Received
                                                    @break

                                                    @case('forwarded')
                                                        Forwarded
                                                    @break

                                                    @case('status_changed')
                                                        Status Changed
                                                    @break

                                                    @case('completed')
                                                        Completed
                                                    @break

                                                    @case('review_submitted')
                                                        Review Submitted
                                                    @break

                                                    @default
                                                        {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                                @endswitch
                                            </h6>
                                            <small class="text-muted">{{ $activity->user->name ?? 'System' }} •
                                                {{ $activity->created_at->diffForHumans() }}</small>
                                            @if ($activity->description)
                                                <p class="mb-0 mt-1">{{ $activity->description }}</p>
                                            @endif
                                            @if ($activity->action === 'forwarded')
                                                <p class="mb-0 mt-1"><small>From:
                                                        <strong>{{ $activity->from_user }}</strong> To:
                                                        <strong>{{ $activity->to_user }}</strong></small></p>
                                            @endif
                                            @if ($activity->old_status && $activity->new_status)
                                                <p class="mb-0 mt-1"><small>Status: {{ $activity->old_status }} →
                                                        {{ $activity->new_status }}</small></p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No activity recorded yet.</p>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    @if (!$ticket->supportUser)
                        <div class="card mb-3">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">Assign Support User</h6>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.tickets.assign', $ticket) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="support_user_id">Support User <span
                                                class="text-danger">*</span></label>
                                        <select name="support_user_id" id="support_user_id" class="form-control"
                                            required>
                                            <option value="">Select Support User</option>
                                            @foreach (\App\Models\User::where('role_id', '!=', 1)->get() as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} -
                                                    {{ $user->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-warning btn-block">
                                        <i class="fas fa-user-plus"></i> Assign
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">Ticket Timeline</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Created:</strong><br>
                                    <small>{{ $ticket->created_at->format('M d, Y h:i A') }}</small>
                                </li>
                                @if ($ticket->received_at)
                                    <li class="mb-2">
                                        <strong>Received:</strong><br>
                                        <small>{{ $ticket->received_at->format('M d, Y h:i A') }}</small>
                                    </li>
                                @endif
                                @if ($ticket->completed_at)
                                    <li class="mb-2">
                                        <strong>Completed:</strong><br>
                                        <small>{{ $ticket->completed_at->format('M d, Y h:i A') }}</small>
                                    </li>
                                @endif
                                @if ($ticket->review)
                                    <li class="mb-2">
                                        <strong>Reviewed:</strong><br>
                                        <small>{{ $ticket->review->created_at->format('M d, Y h:i A') }}</small>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</x-backend.layouts.master>
