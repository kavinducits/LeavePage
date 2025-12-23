@extends('layouts.app')

@section('content')

<!-- Progress Bar - Step 4 -->
@include('StudyLeave.partials.progress_bar', ['currentStep' => 4])

<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Show remark if returned -->
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
                Application for Study Leave
                
            </h2>
        </div>
        <a class="btn btn-outline-maroon" href="{{ route('StudyLeave.WorkCoveringPersons.create') }}">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <!-- Form for Summary and Submit -->

    <form  action="{{ route('StudyLeave.Submit') }}" method="POST" enctype="multipart/form-data" id="leave-form" class="needs-validation" novalidate>
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
            <input type="hidden" name="reference_no" value="{{ $leave->reference_no }}">
        @else
            <input type="hidden" name="reference_no" value="">
            @if(isset($academicYear))
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">
            @endif
        @endif

        <div class="card mb-4">
            
       
        <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Summary and Submit
            </div>
             <div class="card mb-4">
            
            <!-- Card Body -->
            <div class="card-body">
            <div class="row g-3">

                
        <!-- Page Title -->
    <div class="text-center mb-4">
        <h2 class="fw-bold text-maroon">
            <i class="fas fa-clipboard-check me-2"></i>Application Summary
        </h2>
        <p class="text-muted">Review your study leave application before submission</p>
    </div>

    <!-- Form for Summary and Submit -->
    <form action="{{ route('StudyLeave.Submit') }}" method="POST" id="summary-form">
        @csrf

        <!-- Include Summary Layout Component -->
        @include('StudyLeave.partials.summary_layout')

   

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <p class="mb-0">Please review all the information you have provided before submitting your application for study leave. Ensure that all details are accurate and complete.</p>
                        <p class="mb-0">Once you submit the application, you will not be able to make any further changes.</p>
                        <p class="mb-0">Click the "Submit Application" button below to finalize your study leave request.</p>
                    </div>
                </div>
                

                    <!-- Declaration -->
    <div class="card mb-4 border-warning">
        <div class="card-body">
            <div class="form-check">
               <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                <label class="form-check-label fw-semibold" for="declaration">
                                I, undersigned, certify that the details provided in this form are accurate. Details of the programme and other relevant documents are attached.
                            </label>
                <div class="invalid-feedback">
                    You must agree to the declaration before submitting.
                </div>
            </div>
        </div>
    </div>
            </div>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-outline-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm" onclick="saveAndExit()">
        <i class="fas fa-save me-2"></i>Save and Exit
    </button>
     <script>
    function saveAndExit() {
        const form = document.getElementById('leave-form');
        const originalAction = form.action;
        
        // Change form action to save and exit route
        
        form.action = "{{ route('StudyLeave.Summary.exit') }}";
        form.submit();
        
        // Restore original action (optional, for safety)
        form.action = originalAction;
    }

     function submiteStudyLeave() {
        const form = document.getElementById('leave-form');
        const declaration = document.getElementById('declaration');
        
        // Check if declaration is checked
        if (!declaration.checked) {
            // Add validation class to show error
            form.classList.add('was-validated');
            declaration.focus();
            
            // Scroll to declaration
            declaration.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Show alert
            alert('Please agree to the declaration before submitting.');
            return false;
        }
        
        const originalAction = form.action;
        
        // Change form action to save and exit route
        
        form.action = "{{ route('StudyLeave.Submit') }}";
        form.submit();
        
        // Restore original action (optional, for safety)
        form.action = originalAction;
    }
    </script>
            <button type="submit" class="btn btn-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm" onclick="submiteStudyLeave()">
                Submit for Recommendation of Department Head <i class="fas fa-paper-plane ms-2"></i>
            </button>
        </div>
</div>
    </form>
    <!--
    <div class="d-flex justify-content-end mt-4">
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-right me-2"></i>Next: Leave Details
        </a>
    </div>
-->

<style>
.form-select:invalid {
    border-color: #dc3545 !important;
}
.was-validated .form-select:invalid {
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1rem;
    padding-right: 2.5rem;
}
.invalid-feedback {
    display: none;
    font-size: 0.875em;
    color: #dc3545;
    margin-top: 0.25rem;
}
.was-validated .form-select:invalid ~ .invalid-feedback {
    display: block !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('leave-form');
    const libraryHandling = document.getElementById('library_and_property_handling');
    const loanHandling = document.getElementById('loan_handling');
    const declaration = document.getElementById('declaration');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Check declaration
            if (!declaration.checked) {
                declaration.setCustomValidity('You must agree to the declaration');
                isValid = false;
            } else {
                declaration.setCustomValidity('');
            }
            
            // Check library and property handling
            if (libraryHandling && !libraryHandling.value) {
                libraryHandling.setCustomValidity('Please select an option');
                isValid = false;
            } else if (libraryHandling) {
                libraryHandling.setCustomValidity('');
            }
            
            // Check loan handling
            if (loanHandling && !loanHandling.value) {
                loanHandling.setCustomValidity('Please select an option');
                isValid = false;
            } else if (loanHandling) {
                loanHandling.setCustomValidity('');
            }
            
            // Validate form
            if (!form.checkValidity() || !isValid) {
                event.preventDefault();
                event.stopPropagation();
                
                // Scroll to first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
            
            form.classList.add('was-validated');
        });
        
        // Clear validation on declaration change
        if (declaration) {
            declaration.addEventListener('change', function() {
                this.setCustomValidity('');
                if (form.classList.contains('was-validated')) {
                    this.classList.remove('is-invalid');
                }
            });
        }
        
        // Clear validation on selection
        if (libraryHandling) {
            libraryHandling.addEventListener('change', function() {
                this.setCustomValidity('');
                if (form.classList.contains('was-validated')) {
                    this.classList.remove('is-invalid');
                }
            });
        }
        
        if (loanHandling) {
            loanHandling.addEventListener('change', function() {
                this.setCustomValidity('');
                if (form.classList.contains('was-validated')) {
                    this.classList.remove('is-invalid');
                }
            });
        }
    }
});
</script>

@endsection