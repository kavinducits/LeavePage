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
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form  method="POST" enctype="multipart/form-data" id="leave-form">
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

        <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Personal Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Employee No</label>
                        <input type="text" class="form-control" value="{{ $user->empno }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name with Initials</label>
                        <input type="text" class="form-control" value="{{ $user->name_with_initials }}" readonly>
                    </div>
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Designation</label>
                        <input type="text" class="form-control" value="{{ $user->designation }}" readonly>
                    </div>
                   
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" class="form-control" value="{{ $user->department }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Faculty</label>
                        <input type="text" class="form-control" value="{{ $user->faculty }}" readonly>
                    </div>
                  <div class="col-md-6">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="text" class="form-control" value="{{ $user->email }}" readonly>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Passport No:</label>
                        <input type="text" class="form-control" value="" >
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"> Validity date up to:</label>
                        <input type="text" class="form-control" value="" >
                    </div>
            </div>
        </div>

<div class="d-flex justify-content-end mt-4">
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

@endsection