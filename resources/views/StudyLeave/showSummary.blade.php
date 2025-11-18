@extends('layouts.app')

@section('content')
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

    <form  action="{{ route('StudyLeave.Submit') }}" method="POST" enctype="multipart/form-data" id="leave-form">
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

                <!-- Handling of Library Books and Other Properties -->
                
                <div class="col-12 mb-3">
                    <label for="library_and_property_handling" class="form-label fw-semibold d-block">
                         Library book, Computer or any other properties?
                    </label>
                    <select class="form-select w-auto d-inline-block align-middle ms-2" id="library_and_property_handling" name="library_and_property_handling" required>
                        <option value="" {{ empty(old('library_and_property_handling', $draft_study_leave->library_and_property_handling ?? '')) ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="Make Arrangements" {{ old('library_and_property_handling', $draft_study_leave->library_and_property_handling ?? '') === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="Not Make Arrangements" {{ old('library_and_property_handling', $draft_study_leave->library_and_property_handling ?? '') === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                </div>

                <!-- Handling of Paying Loans   -->

                <div class="col-12 mb-3">
                    <label for="loan_handling" class="form-label fw-semibold d-block">
                       Paying of Loans taken from University of UPF?
                        <br>
                        <small class="text-muted">(Applicable only when taking no pay leave)</small>
                    </label>
                    <select class="form-select w-auto d-inline-block align-middle ms-2" id="loan_handling" name="loan_handling" required>
                        <option value="" {{ empty(old('loan_handling', $draft_study_leave->loan_handling ?? '')) ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="Make Arrangements" {{ old('loan_handling', $draft_study_leave->loan_handling ?? '') === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="Not Make Arrangements" {{ old('loan_handling', $draft_study_leave->loan_handling ?? '') === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                </div>
            </div>
        </div>

            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <p class="mb-0">Please review all the information you have provided before submitting your application for study leave. Ensure that all details are accurate and complete.</p>
                        <p class="mb-0">Once you submit the application, you will not be able to make any further changes.</p>
                        <p class="mb-0">Click the "Submit Application" button below to finalize your study leave request.</p>
                    </div>
                </div>
                <!-- Declaration Checkbox -->
                    <div class="col-12">
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="declaration" name="declaration" required>
                            <label class="form-check-label fw-semibold" for="declaration">
                                I, undersigned, certify that the details provided in this form are accurate. Details of the programme and other relevant documents are attached.
                            </label>
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
    </script>
            <button type="submit" class="btn btn-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm">
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

@endsection