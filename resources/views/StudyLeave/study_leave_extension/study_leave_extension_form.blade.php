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
                                <input type="date" name="study_leave_from" id="study_leave_from" class="form-control" 
                                       value="{{optional($study_leave)->study_leave_to ?? ''}}" 
                                       min="{{ date('Y-m-d') }}" 
                                       readonly required>
                                <div class="invalid-feedback">
                                    Please select a valid start date.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">To <span class="text-danger">*</span></label>
                                <input type="date" name="study_leave_to" id="study_leave_to" class="form-control" 
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

                    <!-- Type of Study Leave Requested -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Type of Study Leave Requested <span class="text-danger">*</span></label>
                        <select name="leave_payment_type" id="leave_payment_type" class="form-select" required {{ $readonly ?? true ? 'disabled' : '' }}>
                            <option value="" {{ $study_leave->leave_payment_type === '' ? 'selected' : '' }}>Select an option</option>
                            <option value="with Pay" {{ $study_leave->leave_payment_type === 'with Pay' ? 'selected' : '' }}>With Pay</option>
                            <option value="without Pay" {{ $study_leave->leave_payment_type === 'without Pay' ? 'selected' : '' }}>Without Pay</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select the type of study leave.
                        </div>
                    </div>
                    
                    <!-- Loan Handling -->
                    <div class="col-md-6" id="loan_handling_section">
                        <label for="loan_handling_details" class="form-label fw-semibold">
                            Paying of Loans taken from University of UPF? <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="loan_handling_details" name="loan_handling" {{ $readonly ?? true ? 'disabled' : '' }}>
                            <option value="" {{ empty(old('loan_handling', $study_leave->loan_handling ?? '')) ? 'selected' : '' }} disabled>Select an option</option>
                            <option value="Make Arrangements" {{ old('loan_handling', $study_leave->loan_handling ?? '') === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                            <option value="Not Make Arrangements" {{ old('loan_handling', $study_leave->loan_handling ?? '') === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select an option for loan handling.
                        </div>
                    </div>

                    <!-- Funding Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Funding type <span class="text-danger">*</span></label>
                        <select name="funding_type" id="funding_type" class="form-select" required {{ $readonly ?? true ? 'disabled' : '' }}>
                            <option value="" {{ $study_leave->funding_type ?? '' ? '' : 'selected' }} disabled>Select funding type</option>
                            <option value="self" {{ $study_leave->funding_type === 'self' ? 'selected' : '' }}>Self-Funding</option>
                            <option value="scholarship" {{ $study_leave->funding_type === 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a funding type.
                        </div>
                    </div>

                    <!-- Scholarship Details -->
                    <div class="col-md-6" id="scholarship-details" style="display: none;">
                        <label class="form-label fw-semibold">Scholarship Source <span class="text-danger">*</span></label>
                        <select name="scholarship_source" id="scholarship_source" class="form-select" {{ $readonly ?? true ? 'disabled' : '' }}>
                            <option value="" {{ empty($study_leave->scholarship_source) ? 'selected' : '' }} disabled>Select source</option>
                            <option value="agency" {{ ($study_leave->scholarship_source ?? '') === 'agency' ? 'selected' : '' }}>Scholarship offering agency</option>
                            <option value="project" {{ ($study_leave->scholarship_source ?? '') === 'project' ? 'selected' : '' }}>Funds from a project</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a scholarship source.
                        </div>
                    </div>

                    <div class="col-md-6" id="scholarship-extra-details" style="display: none;">
                        <div id="scholarship-amount-group" style="display: none;">
                            <label class="form-label fw-semibold">Scholarship Amount <span class="text-danger">*</span></label>
                            <input type="number" name="scholarship_amount" id="scholarship_amount" class="form-control" 
                                   min="0.01" step="0.01" placeholder="Enter amount" 
                                   value="{{ $study_leave->scholarship_amount ?? '' }}" 
                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                            <div class="invalid-feedback">
                                Please enter a valid scholarship amount (must be greater than 0).
                            </div>
                        </div>

                        <div id="project-name-group" style="display: none;">
                            <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="project_name" class="form-control" 
                                   placeholder="Enter project name" minlength="3" maxlength="200" 
                                   value="{{ $study_leave->project_name ?? '' }}" 
                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                            <div class="invalid-feedback">
                                Please enter the project name (3-200 characters).
                            </div>
                        </div>
                    </div>

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
