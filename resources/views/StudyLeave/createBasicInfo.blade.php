@extends('layouts.app')

@section('content')

<!-- Progress Bar - Step 1 -->
@include('StudyLeave.partials.progress_bar', ['currentStep' => 1])

<div class="container py-4">

    
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
        <a class="btn btn-outline-maroon" href="{{ route('StudyLeave.create') }}">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>





    <!-- Form for Basic Information -->

    <form  action="{{ route('StudyLeave.BasicInfo.store') }}" method="POST" enctype="multipart/form-data" id="leave-form" class="needs-validation" novalidate>
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

        @include('StudyLeave.basic_info_form')

       

<div class="d-flex justify-content-between mt-4">
    <button type="button" class="btn btn-outline-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm" onclick="saveAndExit()">
        <i class="fas fa-save me-2"></i>Save and Exit
    </button>
    <button type="submit" class="btn btn-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm">
        Next: Leave Details <i class="fas fa-arrow-right ms-2"></i>
    </button>
</div>
    </form>

    <script>
    function saveAndExit() {
        const form = document.getElementById('leave-form');
        const originalAction = form.action;
        
        // Change form action to save and exit route
        
        form.action = "{{ route('StudyLeave.BasicInfo.exit') }}";
        form.submit();
        
        // Restore original action (optional, for safety)
        form.action = originalAction;
    }
    </script>
-
    <!--
    <div class="d-flex justify-content-end mt-4">
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-right me-2"></i>Next: Leave Details
        </a>
    </div>
-->

@endsection