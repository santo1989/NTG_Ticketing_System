<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Manage User Assignments - {{ $user->name }}
    </x-slot>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Manage Support Assignments for {{ $user->name }}</h5>
            <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Note:</strong> Assign specific companies and/or support types to restrict this user's ticket
                visibility.
                If no companies are assigned, the user will see tickets from all companies.
                If no support types are assigned, the user will see tickets of all support types.
            </div>

            <form action="{{ route('users.assignments.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <!-- Company Assignments -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-building"></i> Company Assignments</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="company_ids">Assign Companies (Leave empty for all companies)</label>
                                    <select name="company_ids[]" id="company_ids" class="form-select" multiple
                                        size="10">
                                        @foreach ($companies as $company)
                                            <option value="{{ $company->id }}"
                                                {{ in_array($company->id, $assignedCompanyIds) ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">
                                        Hold Ctrl (Windows) or Cmd (Mac) to select multiple companies
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Support Type Assignments -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-headset"></i> Support Type Assignments</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Assign Support Types (Leave empty for all types)</label>
                                    @foreach ($supportTypes as $type)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="support_types[]"
                                                id="support_type_{{ str_replace(' ', '_', $type) }}"
                                                value="{{ $type }}"
                                                {{ in_array($type, $assignedSupportTypes) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="support_type_{{ str_replace(' ', '_', $type) }}">
                                                {{ $type }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-eye"></i> Current Assignment Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Assigned Companies:</strong>
                                        @if (count($assignedCompanyIds) > 0)
                                            <ul class="mb-0">
                                                @foreach ($companies->whereIn('id', $assignedCompanyIds) as $company)
                                                    <li>{{ $company->name }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="mb-0 text-muted">All companies (no restriction)</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Assigned Support Types:</strong>
                                        @if (count($assignedSupportTypes) > 0)
                                            <ul class="mb-0">
                                                @foreach ($assignedSupportTypes as $type)
                                                    <li>{{ $type }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="mb-0 text-muted">All support types (no restriction)</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex justify-content-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Assignments
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Enhance form behavior
            document.getElementById('company_ids').addEventListener('change', function() {
                updateSummary();
            });

            document.querySelectorAll('input[name="support_types[]"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    updateSummary();
                });
            });

            function updateSummary() {
                // This could be enhanced to show real-time preview
                // For now, the summary will update on page load after save
            }
        </script>
    @endpush
</x-backend.layouts.master>
