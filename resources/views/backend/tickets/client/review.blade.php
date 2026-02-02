<x-backend.layouts.master>
    <x-slot name="pageTitle">
        Review Ticket
    </x-slot>

    <x-slot name='breadCrumb'>
        <x-backend.layouts.elements.breadcrumb>
            <x-slot name="pageHeader">
                Review Ticket #{{ $ticket->ticket_number }}
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
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Rate Your Support Experience</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>Ticket:</strong> {{ $ticket->subject }}<br>
                            <strong>Completed:</strong> {{ $ticket->completed_at->format('F d, Y h:i A') }}<br>
                            @if (!empty($existingReview) && $existingReview->rating === 'Dissatisfied')
                                <strong>Notice:</strong> You can revise your dissatisfied review. Previous responses
                                will be kept in history.
                            @endif
                        </div>

                        <form action="{{ route('client.tickets.submit-review', $ticket) }}" method="POST">
                            @csrf

                            <div class="form-group">
                                <label>How satisfied are you with the support? <span
                                        class="text-danger">*</span></label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="satisfied"
                                            value="Satisfied"
                                            {{ old('rating', $existingReview->rating ?? '') == 'Satisfied' ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label" for="satisfied">
                                            <span class="text-success"><i class="fas fa-smile"></i> Satisfied</span>
                                        </label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="rating" id="dissatisfied"
                                            value="Dissatisfied"
                                            {{ old('rating', $existingReview->rating ?? '') == 'Dissatisfied' ? 'checked' : '' }}
                                            required>
                                        <label class="form-check-label" for="dissatisfied">
                                            <span class="text-danger"><i class="fas fa-frown"></i> Dissatisfied</span>
                                        </label>
                                    </div>
                                </div>
                                @error('rating')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group" id="reason-group" style="display: none;">
                                <label for="reason">Reason for Dissatisfaction <span
                                        class="text-danger">*</span></label>
                                <textarea name="reason" id="reason" rows="4" class="form-control @error('reason') is-invalid @enderror"
                                    placeholder="Please tell us why you were dissatisfied...">{{ old('reason', $existingReview->reason ?? '') }}</textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="feedback">Additional Feedback (Optional)</label>
                                <textarea name="feedback" id="feedback" rows="4" class="form-control @error('feedback') is-invalid @enderror"
                                    placeholder="Any additional comments or suggestions...">{{ old('feedback', $existingReview->feedback ?? '') }}</textarea>
                                @error('feedback')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-paper-plane"></i> Submit Review
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


    <script>
        $(document).ready(function() {
            $('input[name="rating"]').change(function() {
                if ($(this).val() === 'Dissatisfied') {
                    $('#reason-group').show();
                    $('#reason').prop('required', true);
                } else {
                    $('#reason-group').hide();
                    $('#reason').prop('required', false);
                }
            });

            // Trigger on page load if old value exists
            if ($('input[name="rating"]:checked').val() === 'Dissatisfied') {
                $('#reason-group').show();
                $('#reason').prop('required', true);
            }
        });
    </script>

</x-backend.layouts.master>
