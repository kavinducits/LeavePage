@extends('layouts.app')

@section('content')

<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
                                       value="{{optional($study_leave)->study_leave_to ?? ''}}" 
                                       min="{{ date('Y-m-d') }}" 
                                       readonly required>
                                <div class="invalid-feedback">
                                    Please select a valid start date.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">To <span class="text-danger">*</span></label>
                                <input type="date" name="new_end_date" id="new_end_date" class="form-control" 
                                       value=''
                                       min="{{ date('Y-m-d') }}" 
                                       required 
                                       {{ $readonly ?? true ? 'readonly' : '' }}>
                                <div class="invalid-feedback">
                                    Please select a valid end date (must be after start date).
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Reason for Extension -->
                    <div class="col-12">
                        <label for="reason_for_extension" class="form-label fw-semibold">
                            Reason for Extension <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason_for_extension" id="reason_for_extension" class="form-control" 
                                  rows="4" placeholder="Enter reason for requesting extension" 
                                  required {{ $readonly ?? true ? 'readonly' : '' }}>{{ old('reason_for_extension', $study_leave->reason_for_extension ?? '') }}</textarea>
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

                        <button type="submit" class="btn btn-primary btn-lg" style="background-color: #800020; border-color: #800020;">
                            <i class="fas fa-paper-plane me-2"></i>Submit Extension Request
                        </button>
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
