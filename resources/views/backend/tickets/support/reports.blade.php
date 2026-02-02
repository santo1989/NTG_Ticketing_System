<x-backend.layouts.master>
    <x-slot name="pageTitle">
        My Ticket Reports
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                My Ticket Reports
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('support.tickets.dashboard') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <a href="{{ route('support.tickets.reports.download', request()->query()) }}"
                    class="btn btn-sm btn-success">
                    <i class="fas fa-file-csv"></i> Download CSV
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        <div class="card mb-3">
            <div class="card-body">
                <form action="{{ route('support.tickets.reports') }}" method="GET" class="form-inline">
                    <div class="form-group mr-3 mb-2">
                        <label for="date_from" class="mr-2">From:</label>
                        <input type="date" name="date_from" id="date_from" class="form-control"
                            value="{{ request('date_from') }}">
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label for="date_to" class="mr-2">To:</label>
                        <input type="date" name="date_to" id="date_to" class="form-control"
                            value="{{ request('date_to') }}">
                    </div>
                    <div class="form-group mr-3 mb-2">
                        <label for="status" class="mr-2">Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="Receive" {{ request('status') == 'Receive' ? 'selected' : '' }}>Receive
                            </option>
                            <option value="Ticket Received"
                                {{ request('status') == 'Ticket Received' ? 'selected' : '' }}>Ticket Received (Legacy)
                            </option>
                            <option value="Send to Logic" {{ request('status') == 'Send to Logic' ? 'selected' : '' }}>
                                Send to Logic</option>
                            <option value="Complete" {{ request('status') == 'Complete' ? 'selected' : '' }}>Complete
                            </option>
                            <option value="Revise" {{ request('status') == 'Revise' ? 'selected' : '' }}>Revise
                            </option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary mb-2 mr-2">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('support.tickets.reports') }}" class="btn btn-secondary mb-2">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="myReportsTable">
                        <thead>
                            <tr>
                                <th>Ticket #</th>
                                <th>Client</th>
                                <th>Subject</th>
                                <th>Support Type</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Completed</th>
                                <th>Review</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
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
                                                <span class="badge badge-info">Ticket Received</span>
                                            @break

                                            @case('Receive')
                                                <span class="badge badge-info">Receive</span>
                                            @break

                                            @case('Send to Logic')
                                                <span class="badge badge-secondary">Send to Logic</span>
                                            @break

                                            @case('Complete')
                                                <span class="badge badge-success">Complete</span>
                                            @break

                                            @case('Revise')
                                                <span class="badge badge-warning">Revise</span>
                                            @break
                                        @endswitch
                                    </td>
                                    <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                    <td>{{ $ticket->completed_at ? $ticket->completed_at->format('M d, Y') : '-' }}
                                    </td>
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
                                        <a href="{{ route('support.tickets.show', $ticket) }}"
                                            class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
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
    </x-backend.layouts.master>
