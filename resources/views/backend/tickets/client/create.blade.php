<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Create New Ticket
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Create Support Ticket
            </x-slot>
            <x-slot name="add">
                <a href="{{ route('client.tickets.dashboard') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </x-slot>
        </x-backend.layouts.elements.breadcrumb>
    </x-slot>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">New Support Ticket</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('client.tickets.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="form-group">
                                <label for="support_type">Support Type <span class="text-danger">*</span></label>
                                <select name="support_type" id="support_type"
                                    class="form-control @error('support_type') is-invalid @enderror" required>
                                    <option value="">Select Support Type</option>
                                    <option value="ERP Support"
                                        {{ old('support_type') == 'ERP Support' ? 'selected' : '' }}>ERP Support
                                    </option>
                                    <option value="IT Support"
                                        {{ old('support_type') == 'IT Support' ? 'selected' : '' }}>IT Support</option>
                                    <option value="Programmer Support"
                                        {{ old('support_type') == 'Programmer Support' ? 'selected' : '' }}>Programmer
                                        Support</option>
                                </select>
                                @error('support_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="subject">Subject <span class="text-danger">*</span></label>
                                <select name="subject" id="subject"
                                    class="form-control @error('subject') is-invalid @enderror" required>
                                    <option value="">Select Subject</option>

                                    <!-- ERP Support Subjects -->
                                    <optgroup label="ERP Support" id="erp-subjects" class="d-none">
                                        <option value="Library Entry">Library Entry</option>
                                        <option value="Data Correction">Data Correction</option>
                                        <option value="System bug">System bug</option>
                                        <option value="New Development">New Development</option>
                                        <option value="Page Customization">Page Customization</option>
                                        <option value="Print button Customization">Print button Customization</option>
                                        <option value="Report Customization">Report Customization</option>
                                        <option value="Array Adding">Array Adding</option>
                                        <option value="Password Management">Password Management</option>
                                        <option value="Training">Training</option>
                                        <option value="Others">Others</option>
                                    </optgroup>

                                    <!-- IT Support Subjects -->
                                    <optgroup label="IT Support" id="it-subjects" class="d-none">
                                        <option value="Software Support">Software Support</option>
                                        <option value="Technical Support">Technical Support</option>
                                        <option value="Email Support">Email Support</option>
                                        <option value="Networking Support">Networking Support</option>
                                        <option value="IP Telephone">IP Telephone</option>
                                        <option value="Others">Others</option>
                                    </optgroup>

                                    <!-- Programmer Support Subjects -->
                                    <optgroup label="Programmer Support" id="programmer-subjects" class="d-none">
                                        <option value="Software Support">Software Support</option>
                                        <option value="Module Report Update">Module Report Update</option>
                                        <option value="User Bug Solve">User Bug Solve</option>
                                        <option value="New Module Create">New Module Create</option>
                                        <option value="Power BI Support">Power BI Support</option>
                                        <option value="Networking Support">Networking Support</option>
                                        <option value="Domain & Hosting Support">Domain & Hosting Support</option>
                                        <option value="Technical Support">Technical Support</option>
                                        <option value="Others">Others</option>
                                    </optgroup>
                                </select>
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" rows="6"
                                    class="form-control @error('description') is-invalid @enderror"
                                    placeholder="Provide detailed information about your issue..." required>{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="file">Attachment (Optional)</label>
                                <input type="file" name="file" id="file"
                                    class="form-control-file @error('file') is-invalid @enderror"
                                    accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip">
                                <small class="form-text text-muted">Allowed: PDF, DOC, DOCX, JPG, PNG, ZIP (Max:
                                    10MB)</small>
                                @error('file')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i> Submit Ticket
                                </button>
                                <a href="{{ route('client.tickets.dashboard') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Information -->
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Support Type Guide</h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li><strong>ERP Support:</strong> Issues related to ERP system functionality, data, or
                                processes.</li>
                            <li><strong>IT Support:</strong> Hardware, network, or general IT infrastructure issues.
                            </li>
                            <li><strong>Programmer Support:</strong> Custom development requests or software bugs.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        $(document).ready(function() {
            // Function to update subject options based on support type
            function updateSubjectOptions() {
                let supportType = $('#support_type').val();
                let oldSubject = '{{ old('subject') }}';

                // Hide all optgroups
                $('#erp-subjects, #it-subjects, #programmer-subjects').addClass('d-none');

                // Show appropriate optgroup based on support type
                if (supportType === 'ERP Support') {
                    $('#erp-subjects').removeClass('d-none');
                } else if (supportType === 'IT Support') {
                    $('#it-subjects').removeClass('d-none');
                } else if (supportType === 'Programmer Support') {
                    $('#programmer-subjects').removeClass('d-none');
                }

                // If there's an old subject value, set it
                if (oldSubject) {
                    $('#subject').val(oldSubject);
                } else {
                    $('#subject').val('');
                }
            }

            // Update on page load
            updateSubjectOptions();

            // Update when support type changes
            $('#support_type').on('change', function() {
                updateSubjectOptions();
            });
        });
    </script>

</x-backend.layouts.master>
