<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Admin Ticket Dashboard
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Admin Ticket Dashboard
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('home') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-home"></i> Home
                </a>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-list"></i> All Tickets
                </a>
                <a href="{{ route('admin.tickets.reports') }}" class="btn btn-sm btn-info">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        <!-- Overall Statistics -->
        <div class="row mb-4">
            <div class="col-md-10"></div>
            <div class="col-md-2">
                <small class="text-muted d-block text-end">Last updated: <span id="adminLastUpdated">now</span></small>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-2">
                <div class="card text-center bg-primary text-white">
                    <div class="card-body">
                        <h3 id="totalTicketsAdmin">{{ $totalTickets }}</h3>
                        <p class="mb-0">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-warning text-white">
                    <div class="card-body">
                        <h3 id="pendingTicketsAdmin">{{ $pendingTickets }}</h3>
                        <p class="mb-0">Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-info text-white">
                    <div class="card-body">
                        <h3 id="receivedTicketsAdmin">{{ $receivedTickets }}</h3>
                        <p class="mb-0">MIS Received</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center bg-secondary text-white">
                    <div class="card-body">
                        <h3 id="inProgressTicketsAdmin">{{ $inProgressTickets }}</h3>
                        <p class="mb-0">In Progress</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card text-center bg-success text-white">
                    <div class="card-body">
                        <h3 id="completedTicketsAdmin">{{ $completedTickets }}</h3>
                        <p class="mb-0">Completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Support Type Statistics -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-database"></i> ERP Support</h6>
                    </div>
                    <div class="card-body text-center">
                        <h2 id="erpCountAdmin">{{ $erpCount }}</h2>
                        <p class="mb-0">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-laptop"></i> IT Support</h6>
                    </div>
                    <div class="card-body text-center">
                        <h2 id="itCountAdmin">{{ $itCount }}</h2>
                        <p class="mb-0">Total Tickets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h6 class="mb-0 text-light"><i class="fas fa-code"></i> Programmer Support</h6>
                    </div>
                    <div class="card-body text-center">
                        <h2 id="programmerCountAdmin">{{ $programmerCount }}</h2>
                        <p class="mb-0">Total Tickets</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Statistics -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h3 id="totalReviewsAdmin">{{ $totalReviews }}</h3>
                        <p class="mb-0">Total Reviews</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3 id="satisfiedCountAdmin">{{ $satisfiedCount }}</h3>
                        <p class="mb-0">Satisfied</p>
                        <small id="satisfiedPercentAdmin">
                            @if ($totalReviews > 0)
                                ({{ round(($satisfiedCount / $totalReviews) * 100, 1) }}%)
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3 id="dissatisfiedCountAdmin">{{ $dissatisfiedCount }}</h3>
                        <p class="mb-0">Dissatisfied</p>
                        <small id="dissatisfiedPercentAdmin">
                            @if ($totalReviews > 0)
                                ({{ round(($dissatisfiedCount / $totalReviews) * 100, 1) }}%)
                            @endif
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h3 id="totalForwardsAdmin">{{ $totalForwards }}</h3>
                        <p class="mb-0">Total Forwarded</p>
                        <small id="forwardPercentAdmin">({{ $forwardPercentage }}%)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Support Users -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-trophy"></i> Top Performers</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Support User</th>
                                    <th>Completed</th>
                                    <th>Forwarded</th>
                                    <th>Forward %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topSupportUsers as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td><span class="badge badge-success">{{ $user->completed_count }}</span></td>
                                        <td><span class="badge badge-warning">{{ $user->forward_count }}</span></td>
                                        <td><span class="badge badge-info">{{ $user->forward_percentage }}%</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No data available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-clock"></i> Recent Tickets</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket #</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTickets as $ticket)
                                    <tr>
                                        <td>
                                            <a href="{{ route('admin.tickets.show', $ticket) }}">
                                                {{ $ticket->ticket_number }}
                                            </a>
                                        </td>
                                        <td>
                                            @switch($ticket->status)
                                                @case('Pending')
                                                    <span class="badge badge-warning">Pending</span>
                                                @break

                                                @case('MIS Receive Issue')
                                                    <span class="badge badge-info">Received</span>
                                                @break

                                                @case('Send to Logic')
                                                    <span class="badge badge-secondary">In Progress</span>
                                                @break

                                                @case('Complete')
                                                    <span class="badge badge-success">Complete</span>
                                                @break
                                            @endswitch
                                        </td>
                                        <td>{{ $ticket->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center">No tickets yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <script>
            $(document).ready(function() {
                // Function to refresh dashboard stats
                function refreshAdminDashboard() {
                    $.ajax({
                        url: "{{ route('admin.tickets.ajax.dashboard-stats') }}",
                        type: 'GET',
                        dataType: 'json',
                        success: function(data) {
                            // Update overall stats
                            $('#totalTicketsAdmin').text(data.totalTickets);
                            $('#pendingTicketsAdmin').text(data.pendingTickets);
                            $('#receivedTicketsAdmin').text(data.receivedTickets);
                            $('#inProgressTicketsAdmin').text(data.inProgressTickets);
                            $('#completedTicketsAdmin').text(data.completedTickets);

                            // Update support type stats
                            $('#erpCountAdmin').text(data.erpCount);
                            $('#itCountAdmin').text(data.itCount);
                            $('#programmerCountAdmin').text(data.programmerCount);

                            // Update review stats
                            $('#totalReviewsAdmin').text(data.totalReviews);
                            $('#satisfiedCountAdmin').text(data.satisfiedCount);
                            $('#dissatisfiedCountAdmin').text(data.dissatisfiedCount);

                            // Update satisfaction percentages
                            if (data.totalReviews > 0) {
                                let satisfiedPercent = ((data.satisfiedCount / data.totalReviews) * 100)
                                    .toFixed(1);
                                let dissatisfiedPercent = ((data.dissatisfiedCount / data.totalReviews) *
                                    100).toFixed(1);
                                $('#satisfiedPercentAdmin').text('(' + satisfiedPercent + '%)');
                                $('#dissatisfiedPercentAdmin').text('(' + dissatisfiedPercent + '%)');
                            }

                            updateAdminLastUpdated();
                        },
                        error: function(xhr, status, error) {
                            console.log('Error refreshing admin dashboard:', error);
                        }
                    });
                }

                function updateAdminLastUpdated() {
                    const now = new Date();
                    const timeString = now.toLocaleTimeString();
                    $('#adminLastUpdated').text(timeString);
                }

                // Refresh every 10 seconds
                setInterval(refreshAdminDashboard, 10000);
            });
        </script>

    </x-backend.layouts.master>
