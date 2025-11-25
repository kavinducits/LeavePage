@extends('layouts.app')

@section('content')

<!-- Progress Bar - Step 2 -->
@include('StudyLeave.partials.progress_bar', ['currentStep' => 2])

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

    <!-- Header and Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Header -->
        <div>
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-file-alt me-2 icon-gold"></i>
                Application for Study Leave
                
            </h2>
        </div>

        <!-- Back Button -->
        <a class="btn btn-outline-maroon" href="{{ route('StudyLeave.BasicInfo.create') }}">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>





     <!-- Form Start -->

    <form  action="{{ route('StudyLeave.Details.store') }}" method="POST" enctype="multipart/form-data" id="leave-form">
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
        

        @include('StudyLeave.details_form')

        
        
        

        <!-- Submit Button -->
<div class="d-flex justify-content-between mt-4">
    
 <button type="button" class="btn btn-outline-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm" onclick="saveAndExit()">
        <i class="fas fa-save me-2"></i>Save and Exit
    </button>
     <script>
    function saveAndExit() {
        const form = document.getElementById('leave-form');
        const originalAction = form.action;
        
        // Change form action to save and exit route
        
        form.action = "{{ route('StudyLeave.Details.exit') }}";
        form.submit();
        
        // Restore original action (optional, for safety)
        form.action = originalAction;
    }
    </script>
    <button type="submit" class="btn btn-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm">
        Next: Leave Details <i class="fas fa-arrow-right ms-2"></i>
    </button>
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
    .custom-radio {
        border: 2px solid #6c757d !important;
        box-shadow: 0 0 2px #6c757d;
        background-color: #fff;
    }
    .custom-radio:checked {
        border-color: #800000 !important;
        box-shadow: 0 0 4px #800000;
    }
</style>
@endsection