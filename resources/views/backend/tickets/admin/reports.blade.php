<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Ticket Reports
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Ticket Reports & Analytics
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('admin.tickets.dashboard') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
                <button type="button" class="btn btn-sm btn-success" id="downloadXlsBtn">
                    <i class="fas fa-file-excel"></i> Download XLS
                </button>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        <!-- Monthly Statistics -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-calendar"></i> Monthly Statistics (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="monthlyStatsTable">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Tickets</th>
                                <th>Completed</th>
                                <th>Pending</th>
                                <th>Completion Rate</th>
                            </tr>
                        </thead>
                        <tbody id="monthlyStatsBody">
                            @forelse($monthlyStats as $stat)
                                <tr>
                                    <td>{{ $stat->month_year }}</td>
                                    <td><span class="badge badge-primary">{{ $stat->total }}</span></td>
                                    <td><span class="badge badge-success">{{ $stat->completed }}</span></td>
                                    <td><span class="badge badge-warning">{{ $stat->pending }}</span></td>
                                    <td>{{ $stat->completion_rate ?? ($stat->total > 0 ? round(($stat->completed / $stat->total) * 100, 1) : 0) }}%
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

        <!-- Support Type Performance -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-chart-pie"></i> Support Type Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="supportTypeTable">
                        <thead>
                            <tr>
                                <th>Support Type</th>
                                <th>Total Tickets</th>
                                <th>Completed</th>
                                <th>Avg. Resolution Time</th>
                                <th>Satisfaction Rate</th>
                            </tr>
                        </thead>
                        <tbody id="supportTypeBody">
                            @forelse($supportTypePerformance as $perf)
                                <tr>
                                    <td><span class="badge badge-info">{{ $perf->support_type }}</span></td>
                                    <td>{{ $perf->total }}</td>
                                    <td>{{ $perf->completed }}</td>
                                    <td>
                                        @if ($perf->avg_resolution_hours)
                                            {{ number_format($perf->avg_resolution_hours, 1) }} hours
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if ($perf->total_reviews > 0)
                                            <div class="progress">
                                                <div class="progress-bar bg-success"
                                                    style="width: {{ ($perf->satisfied / $perf->total_reviews) * 100 }}%">
                                                    {{ $perf->satisfaction_rate ?? round(($perf->satisfied / $perf->total_reviews) * 100, 1) }}%
                                                </div>
                                            </div>
                                        @else
                                            No reviews yet
                                        @endif
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

        <!-- User Performance -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-users"></i> Support Team Performance</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="userPerformanceTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Support User</th>
                                <th>Total Assigned</th>
                                <th>Completed</th>
                                <th>Pending</th>
                                <th>Avg. Resolution Time</th>
                                <th>Reviews</th>
                                <th>Satisfied</th>
                                <th>Dissatisfied</th>
                                <th>Satisfaction Rate</th>
                            </tr>
                        </thead>
                        <tbody id="userPerformanceBody">
                            @forelse($userPerformance as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td><span class="badge badge-primary">{{ $user->total_assigned }}</span></td>
                                    <td><span class="badge badge-success">{{ $user->completed }}</span></td>
                                    <td><span class="badge badge-warning">{{ $user->pending }}</span></td>
                                    <td>
                                        @if ($user->avg_resolution_hours)
                                            {{ number_format($user->avg_resolution_hours, 1) }} hrs
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $user->total_reviews }}</td>
                                    <td><span class="badge badge-success">{{ $user->satisfied }}</span></td>
                                    <td><span class="badge badge-danger">{{ $user->dissatisfied }}</span></td>
                                    <td>
                                        @if ($user->total_reviews > 0)
                                            <div class="progress">
                                                <div class="progress-bar {{ $user->satisfaction_rate >= 80 ? 'bg-success' : 'bg-warning' }}"
                                                    style="width: {{ $user->satisfaction_rate ?? round(($user->satisfied / $user->total_reviews) * 100, 1) }}%">
                                                    {{ $user->satisfaction_rate ?? round(($user->satisfied / $user->total_reviews) * 100, 1) }}%
                                                </div>
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>


    <script>
        let userPerformanceDataTable = null;
        let currentReportData = null;

        function renderMonthlyStats(data) {
            const tbody = document.getElementById('monthlyStatsBody');
            if (!tbody) return;
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
                return;
            }

            const rows = data.map(row => {
                const completion = row.completion_rate ?? (row.total > 0 ? ((row.completed / row.total) * 100)
                    .toFixed(1) : 0);
                return `<tr>
                        <td>${row.month_year}</td>
                        <td><span class="badge badge-primary">${row.total}</span></td>
                        <td><span class="badge badge-success">${row.completed}</span></td>
                        <td><span class="badge badge-warning">${row.pending}</span></td>
                        <td>${completion}%</td>
                    </tr>`;
            }).join('');

            tbody.innerHTML = rows;
        }

        function renderSupportType(data) {
            const tbody = document.getElementById('supportTypeBody');
            if (!tbody) return;
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center">No data available</td></tr>';
                return;
            }

            const rows = data.map(row => {
                const satisfaction = row.satisfaction_rate ?? (row.total_reviews > 0 ? ((row.satisfied / row
                    .total_reviews) * 100).toFixed(1) : 0);
                const progress = row.total_reviews > 0 ?
                    `<div class="progress"><div class="progress-bar bg-success" style="width: ${satisfaction}%">${satisfaction}%</div></div>` :
                    'No reviews yet';

                const avgHours = row.avg_resolution_hours ? `${Number(row.avg_resolution_hours).toFixed(1)} hours` :
                    '-';

                return `<tr>
                        <td><span class="badge badge-info">${row.support_type}</span></td>
                        <td>${row.total}</td>
                        <td>${row.completed}</td>
                        <td>${avgHours}</td>
                        <td>${progress}</td>
                    </tr>`;
            }).join('');

            tbody.innerHTML = rows;
        }

        function ensureUserTable() {
            if ($.fn.dataTable.isDataTable('#userPerformanceTable')) {
                return $('#userPerformanceTable').DataTable();
            }
            return $('#userPerformanceTable').DataTable({
                order: [
                    [2, 'desc']
                ],
                pageLength: 10
            });
        }

        function renderUserPerformance(data) {
            if (!userPerformanceDataTable) {
                userPerformanceDataTable = ensureUserTable();
            }

            userPerformanceDataTable.clear();

            if (!data || data.length === 0) {
                userPerformanceDataTable.draw();
                return;
            }

            data.forEach((user, idx) => {
                const satisfaction = user.satisfaction_rate ?? (user.total_reviews > 0 ? ((user.satisfied / user
                    .total_reviews) * 100).toFixed(1) : 0);
                const satisfactionBar = user.total_reviews > 0 ?
                    `<div class="progress"><div class="progress-bar ${satisfaction >= 80 ? 'bg-success' : 'bg-warning'}" style="width: ${satisfaction}%">${satisfaction}%</div></div>` :
                    '-';

                userPerformanceDataTable.row.add([
                    idx + 1,
                    user.name,
                    `<span class="badge badge-primary">${user.total_assigned}</span>`,
                    `<span class="badge badge-success">${user.completed}</span>`,
                    `<span class="badge badge-warning">${user.pending}</span>`,
                    user.avg_resolution_hours ? `${Number(user.avg_resolution_hours).toFixed(1)} hrs` : '-',
                    user.total_reviews,
                    `<span class="badge badge-success">${user.satisfied}</span>`,
                    `<span class="badge badge-danger">${user.dissatisfied}</span>`,
                    satisfactionBar
                ]);
            });

            userPerformanceDataTable.draw();
        }

        function fetchReports() {
            fetch('{{ route('admin.tickets.ajax.reports') }}')
                .then(response => response.json())
                .then(data => {
                    currentReportData = data;
                    renderMonthlyStats(data.monthlyStats || []);
                    renderSupportType(data.supportTypePerformance || []);
                    renderUserPerformance(data.userPerformance || []);
                })
                .catch(() => {
                    // Silent fail to avoid user disruption
                });
        }

        function tableToHtml(headers, rows) {
            const headerRow = headers.map(h => `<th>${h}</th>`).join('');
            const bodyRows = rows.map(r =>
                `<tr>${r.map(v => `<td>${v !== null && v !== undefined ? v : ''}</td>`).join('')}</tr>`).join('');
            return `<table border="1"><thead><tr>${headerRow}</tr></thead><tbody>${bodyRows}</tbody></table>`;
        }

        function downloadXls() {
            if (!currentReportData) {
                fetchReports();
                return;
            }

            const monthlyRows = (currentReportData.monthlyStats || []).map(r => [r.month_year, r.total, r.completed, r
                .pending, `${r.completion_rate ?? 0}%`
            ]);
            const supportRows = (currentReportData.supportTypePerformance || []).map(r => {
                const satisfaction = r.satisfaction_rate ?? (r.total_reviews > 0 ? ((r.satisfied / r
                    .total_reviews) * 100).toFixed(1) : 0);
                const avg = r.avg_resolution_hours ? Number(r.avg_resolution_hours).toFixed(1) + ' hours' : '-';
                return [r.support_type, r.total, r.completed, avg, `${satisfaction}%`, r.total_reviews, r.satisfied,
                    r.dissatisfied
                ];
            });
            const userRows = (currentReportData.userPerformance || []).map((u, idx) => {
                const satisfaction = u.satisfaction_rate ?? (u.total_reviews > 0 ? ((u.satisfied / u
                    .total_reviews) * 100).toFixed(1) : 0);
                const avg = u.avg_resolution_hours ? Number(u.avg_resolution_hours).toFixed(1) + ' hrs' : '-';
                return [idx + 1, u.name, u.total_assigned, u.completed, u.pending, avg, u.total_reviews, u
                    .satisfied, u.dissatisfied, `${satisfaction}%`
                ];
            });

            const html = [
                '<html><head><meta charset="UTF-8"></head><body>',
                '<h3>Monthly Statistics</h3>',
                tableToHtml(['Month', 'Total', 'Completed', 'Pending', 'Completion %'], monthlyRows),
                '<br><h3>Support Type Performance</h3>',
                tableToHtml(['Support Type', 'Total', 'Completed', 'Avg Hours', 'Satisfaction %', 'Total Reviews',
                    'Satisfied', 'Dissatisfied'
                ], supportRows),
                '<br><h3>User Performance</h3>',
                tableToHtml(['#', 'Name', 'Total Assigned', 'Completed', 'Pending', 'Avg Hours', 'Total Reviews',
                    'Satisfied', 'Dissatisfied', 'Satisfaction %'
                ], userRows),
                '</body></html>'
            ].join('');

            const blob = new Blob([html], {
                type: 'application/vnd.ms-excel'
            });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `ticket-reports-${new Date().toISOString().slice(0, 10)}.xls`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        $(document).ready(function() {
            userPerformanceDataTable = ensureUserTable();
            fetchReports();
            setInterval(fetchReports, 30000);

            document.getElementById('downloadXlsBtn')?.addEventListener('click', function() {
                downloadXls();
            });
        });
    </script>

</x-backend.layouts.master>
