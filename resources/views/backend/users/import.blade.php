<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Import Users
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Import Users from Excel
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('users.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
                <a href="{{ route('users.import.template') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-download"></i> Download Template
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-warning alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <h5><i class="fas fa-exclamation-triangle"></i> Validation Errors:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-file-excel"></i> Upload User List Excel File</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle"></i> Instructions:</h6>
                            <ul class="mb-0">
                                <li>Download the template file and fill in user data</li>
                                <li>Required columns: <strong>email</strong>, <strong>name</strong></li>
                                <li>Optional columns: <strong>Company</strong>, <strong>Department</strong>,
                                    <strong>Designation</strong>
                                </li>
                                <li>Default password for all users will be: <strong>123</strong></li>
                                <li>Users will be assigned to <strong>General</strong> role by default</li>
                                <li>If Company/Department/Designation doesn't exist, it will be created automatically
                                </li>
                                <li><strong>Recommended:</strong> Use CSV format (.csv) - No dependencies needed</li>
                                <li>Excel formats (.xlsx, .xls) require PhpSpreadsheet library</li>
                                <li>Maximum file size: 10MB</li>
                            </ul>
                        </div>

                        <form action="{{ route('users.import.process') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label for="file" class="form-label">
                                    <i class="fas fa-upload"></i> Select Excel File <span class="text-danger">*</span>
                                </label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror"
                                    id="file" name="file" accept=".xlsx,.xls,.csv" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    Accepted formats: Excel (.xlsx, .xls) or CSV (.csv)
                                </small>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm" required>
                                    <label class="form-check-label" for="confirm">
                                        I confirm that the Excel file is properly formatted and reviewed
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-upload"></i> Import Users
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-table"></i> Expected Excel Format</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>email</th>
                                        <th>name</th>
                                        <th>Group</th>
                                        <th>Company</th>
                                        <th>Department</th>
                                        <th>Designation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>a.admin@ntg.com.bd</td>
                                        <td>Md. Al Amin</td>
                                        <td>NCL</td>
                                        <td>NCL Corporate</td>
                                        <td>Merchandising</td>
                                        <td>Executive</td>
                                    </tr>
                                    <tr>
                                        <td>abir.bashir@ntg.com.bd</td>
                                        <td>Md Bashir Sarker</td>
                                        <td>TIL</td>
                                        <td>TIL Corporate</td>
                                        <td>Washing</td>
                                        <td>Junior Executive</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-lightbulb"></i>
                            The "Group" column is optional and for reference only. It won't be imported.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Preview file name
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const label = document.querySelector('label[for="file"]');
                label.innerHTML = `<i class="fas fa-file-excel text-success"></i> ${fileName}`;
            }
        });

        // Form submission confirmation
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.getElementById('file');
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Please select a file to upload');
                return;
            }

            const confirmed = confirm('Are you sure you want to import users from this file?');
            if (!confirmed) {
                e.preventDefault();
            }
        });
    </script>
</x-backend.layouts.master>
