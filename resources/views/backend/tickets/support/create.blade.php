<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Create Ticket on Behalf of User
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Create New Ticket
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Create Ticket on Behalf of User</h5>
                        <small>This ticket will be created by the selected user and auto-received by you</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('support.tickets.store') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <!-- Client Selection -->
                                <div class="col-md-6 mb-3">
                                    <label for="client_id" class="form-label">
                                        <i class="fas fa-user"></i> Select User (Ticket Creator)
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="client_id" id="client_id" class="form-select select2" required>
                                        <option value="">-- Select User --</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ old('client_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">The ticket will be created under this user's name</small>
                                </div>

                                <!-- Support Type -->
                                <div class="col-md-6 mb-3">
                                    <label for="support_type" class="form-label">
                                        <i class="fas fa-headset"></i> Support Type
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="support_type" id="support_type" class="form-select" required>
                                        <option value="">-- Select Support Type --</option>
                                        <option value="ERP Support"
                                            {{ old('support_type') == 'ERP Support' ? 'selected' : '' }}>
                                            ERP Support
                                        </option>
                                        <option value="IT Support"
                                            {{ old('support_type') == 'IT Support' ? 'selected' : '' }}>
                                            IT Support
                                        </option>
                                        <option value="Programmer Support"
                                            {{ old('support_type') == 'Programmer Support' ? 'selected' : '' }}>
                                            Programmer Support
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">
                                    <i class="fas fa-heading"></i> Subject
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="subject" id="subject" class="form-control"
                                    value="{{ old('subject') }}" placeholder="Brief summary of the issue" required>
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left"></i> Description
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea name="description" id="description" class="form-control" rows="8"
                                    placeholder="Detailed description of the issue..." required>{{ old('description') }}</textarea>
                            </div>

                            <!-- Attachments -->
                            <div class="mb-3">
                                <label for="attachments" class="form-label">
                                    <i class="fas fa-paperclip"></i> Attachments (Optional)
                                </label>
                                <input type="file" name="attachments[]" id="attachments" class="form-control"
                                    multiple>
                                <small class="text-muted">You can upload multiple files (Max 10MB per file)</small>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> This ticket will be created by the selected user, but you will be
                                automatically assigned as the support user with status "Receive".
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('support.tickets.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Create & Receive Ticket
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize Select2 for user selection
                $('#client_id').select2({
                    placeholder: '-- Select User --',
                    allowClear: true,
                    width: '100%',
                    theme: 'bootstrap-5'
                });
            });
        </script>
    @endpush
</x-backend.layouts.master>
