<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Edit Ticket
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Edit Ticket #{{ $ticket->ticket_number }}
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('client.tickets.dashboard') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('client.tickets.update', $ticket) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="support_type">Support Type <span class="text-danger">*</span></label>
                                <select name="support_type" id="support_type"
                                    class="form-control @error('support_type') is-invalid @enderror" required>
                                    <option value="ERP Support"
                                        {{ old('support_type', $ticket->support_type) == 'ERP Support' ? 'selected' : '' }}>
                                        ERP Support</option>
                                    <option value="IT Support"
                                        {{ old('support_type', $ticket->support_type) == 'IT Support' ? 'selected' : '' }}>
                                        IT Support</option>
                                    <option value="Programmer Support"
                                        {{ old('support_type', $ticket->support_type) == 'Programmer Support' ? 'selected' : '' }}>
                                        Programmer Support</option>
                                </select>
                                @error('support_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="subject">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" id="subject"
                                    class="form-control @error('subject') is-invalid @enderror"
                                    value="{{ old('subject', $ticket->subject) }}" required>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="6"
                                    class="form-control @error('description') is-invalid @enderror" required>{{ old('description', $ticket->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            @if ($ticket->file_path)
                                <div class="form-group">
                                    <label>Current Attachment:</label>
                                    <div>
                                        <a href="{{ Storage::url($ticket->file_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download"></i> View Current File
                                        </a>
                                    </div>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="file">New Attachment (Optional)</label>
                                <input type="file" name="file" id="file"
                                    class="form-control-file @error('file') is-invalid @enderror">
                                <small class="form-text text-muted">Upload new file to replace existing
                                    attachment</small>
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Ticket
                                </button>
                                <a href="{{ route('client.tickets.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-backend.layouts.master>
