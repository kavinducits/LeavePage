@extends('layouts.app')

@section('content')

<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(isset($hasPendingExtension) && $hasPendingExtension)
        <div class="alert alert-warning fw-semibold">
            <i class="fas fa-hourglass-half me-2"></i>
            <strong>Pending Extension Request:</strong> You already have an extension request pending approval. You cannot submit a new extension request until the current one is processed.
        </div>
    @endif

    @if (!$canExtend && (!isset($hasPendingExtension) || !$hasPendingExtension))
        <div class="alert alert-danger fw-semibold">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Extension Not Allowed:</strong> The total study leave duration (including approved extensions) has reached or exceeded the 3-year limit.
            <br><small>Total duration: {{ round($totalDurationDays / 365, 2) }} years ({{ $totalDurationDays }} days)</small>
        </div>
    @endif

    @if ($canExtend && $remainingDays < 365 && (!isset($hasPendingExtension) || !$hasPendingExtension))
        <div class="alert alert-warning fw-semibold">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Notice:</strong> You have {{ round($remainingDays / 30, 1) }} months ({{ $remainingDays }} days) remaining before reaching the 3-year limit.
            <br><small>Current total: {{ round($totalDurationDays / 365, 2) }} years | Maximum: 3 years</small>
        </div>
    @endif

    @isset($remark)
    <div class="alert alert-warning fw-semibold">
        Returned with remark:
        <pre class="mb-0">{{ $remark }}</pre>
    </div>
    @endisset

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-file-alt me-2 icon-gold"></i>
                Request for Study Leave Extension
            </h2>
        </div>
    </div>

    <form action="{{Route('StudyLeave.store.extension', ['id' => $study_leave->id])}}" method="POST" enctype="multipart/form-data" id="leave-form" class="needs-validation" novalidate>
        @csrf

        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-user me-2"></i>Details of the Study Leave Extention - Refference No: {{ $study_leave->reference_no ?? 'N/A' }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <!-- Period of Study Leave Requested -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Period of Study Leave Requested <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">From </label>
                                <input type="date" name="old_end_date" id="old_end_date" class="form-control" 
                                       value="{{ $extensionStartDate ?? optional($study_leave)->study_leave_to ?? '' }}" 
                                       min="{{ date('Y-m-d') }}" 
                                       readonly required>
                                <div class="invalid-feedback">
                                    Please select a valid start date.
                                </div>
                                @if(isset($extensionStartDate) && $extensionStartDate != $study_leave->study_leave_to)
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Start date based on previous approved extension end date
                                    </small>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">To <span class="text-danger">*</span></label>
                                <input type="date" name="new_end_date" id="new_end_date" class="form-control" 
                                       value=''
                                       min="{{ $extensionStartDate ?? optional($study_leave)->study_leave_to ?? date('Y-m-d') }}" 
                                       required 
                                       {{ ($readonly ?? true) || !$canExtend ? 'readonly' : '' }}>
                                <div class="invalid-feedback">
                                    Please select a valid end date (must be after start date).
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const oldEndDate = document.getElementById('old_end_date');
                            const newEndDate = document.getElementById('new_end_date');
                            const remainingDays = {{ $remainingDays ?? 0 }};
                            const studyLeaveFrom = '{{ $study_leave->study_leave_from }}';
                            
                            function updateNewEndDateRestrictions() {
                                if (oldEndDate.value && studyLeaveFrom) {
                                    // Set minimum to the day after old_end_date (must be greater than "From" date)
                                    const minDate = new Date(oldEndDate.value);
                                    minDate.setDate(minDate.getDate() + 1);
                                    const minDateString = minDate.toISOString().split('T')[0];
                                    newEndDate.min = minDateString;
                                    
                                    // Calculate maximum date: original study_leave_from + remainingDays
                                    // This ensures total duration doesn't exceed 3 years from original start
                                    const originalStartDate = new Date(studyLeaveFrom);
                                    const maxDate = new Date(originalStartDate);
                                    maxDate.setDate(maxDate.getDate() + remainingDays + {{ $totalDurationDays ?? 0 }});
                                    const maxDateString = maxDate.toISOString().split('T')[0];
                                    newEndDate.max = maxDateString;
                                    
                                    // If current value exceeds max date, clear it
                                    if (newEndDate.value && new Date(newEndDate.value) > maxDate) {
                                        newEndDate.value = '';
                                    }
                                    
                                    // If current value is less than min date, clear it
                                    if (newEndDate.value && new Date(newEndDate.value) < minDate) {
                                        newEndDate.value = '';
                                    }
                                }
                            }
                            
                            // Initialize on page load
                            updateNewEndDateRestrictions();
                            
                            // Update when old_end_date changes (though it's readonly, good to have for consistency)
                            oldEndDate.addEventListener('change', updateNewEndDateRestrictions);
                        });
                    </script>

                    <!-- Reason for Extension -->
                    <div class="col-12">
                        <label for="reason_for_extension" class="form-label fw-semibold">
                            Reason for Extension <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason_for_extension" id="reason_for_extension" class="form-control" 
                                  rows="4" placeholder="Enter reason for requesting extension" 
                                  required {{ ($readonly ?? true) || !$canExtend ? 'readonly' : '' }}>{{ old('reason_for_extension', $study_leave->reason_for_extension ?? '') }}</textarea>
                        <div class="invalid-feedback">
                            Please provide a reason for the extension.
                        </div>
                    </div>

                  

                    
                  
            <!-- Action Buttons -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('StudyLeave.create') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>

                        @if ($canExtend)
                            <button type="submit" class="btn btn-primary btn-lg" style="background-color: #800020; border-color: #800020;">
                                <i class="fas fa-paper-plane me-2"></i>Submit Extension Request
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-lg" disabled>
                                <i class="fas fa-ban me-2"></i>Extension Not Allowed (3-Year Limit Reached)
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fundingType = document.querySelector('select[name="funding_type"]');
            const scholarshipDetails = document.getElementById('scholarship-details');
            const scholarshipExtraDetails = document.getElementById('scholarship-extra-details');
            const scholarshipSource = document.getElementById('scholarship_source');
            const scholarshipAmountGroup = document.getElementById('scholarship-amount-group');
            const projectNameGroup = document.getElementById('project-name-group');
            const leavePaymentType = document.getElementById('leave_payment_type');
            const loanHandlingSection = document.getElementById('loan_handling_section');
            const loanHandlingDetails = document.getElementById('loan_handling_details');

            function updateLoanHandlingVisibility() {
                if (leavePaymentType.value === 'without Pay') {
                    loanHandlingSection.style.display = 'block';
                    loanHandlingDetails.setAttribute('required', 'required');
                } else {
                    loanHandlingSection.style.display = 'none';
                    loanHandlingDetails.removeAttribute('required');
                    loanHandlingDetails.value = '';
                }
            }

            function updateScholarshipVisibility() {
                if (fundingType.value === 'scholarship') {
                    scholarshipDetails.style.display = 'block';
                    scholarshipSource.setAttribute('required', 'required');
                } else {
                    scholarshipDetails.style.display = 'none';
                    scholarshipExtraDetails.style.display = 'none';
                    scholarshipAmountGroup.style.display = 'none';
                    projectNameGroup.style.display = 'none';
                    scholarshipSource.removeAttribute('required');
                }
            }

            scholarshipSource.addEventListener('change', function() {
                if (this.value === 'agency') {
                    scholarshipExtraDetails.style.display = 'block';
                    scholarshipAmountGroup.style.display = 'block';
                    projectNameGroup.style.display = 'none';
                } else if (this.value === 'project') {
                    scholarshipExtraDetails.style.display = 'block';
                    scholarshipAmountGroup.style.display = 'none';
                    projectNameGroup.style.display = 'block';
                } else {
                    scholarshipExtraDetails.style.display = 'none';
                }
            });

            leavePaymentType.addEventListener('change', updateLoanHandlingVisibility);
            fundingType.addEventListener('change', updateScholarshipVisibility);

            updateLoanHandlingVisibility();
            updateScholarshipVisibility();
            if (scholarshipSource.value) {
                scholarshipSource.dispatchEvent(new Event('change'));
            }
        });
    </script>

</div>

@endsection
