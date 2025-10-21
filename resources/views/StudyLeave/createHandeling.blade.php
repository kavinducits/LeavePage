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

    <!-- Form for Handling -->

    <form  action="{{ route('StudyLeave.Handeling.store') }}" method="POST" enctype="multipart/form-data" id="leave-form">
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
                <i class="fas fa-user me-2"></i>Handling of
            </div>
            <!-- Card Body -->
            <div class="card-body">
            <div class="row g-3">
                <div class="col-12 mb-3">
                    <label for="arrangement_made" class="form-label fw-semibold d-block">
                         Library book, Computer or any other properties?
                    </label>
                    <select class="form-select w-auto d-inline-block align-middle ms-2" id="arrangement_made" name="arrangement_made" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="Make Arrangements">Make Arrangements</option>
                        <option value="Not Make Arrangements">Not Make Arrangements</option>
                    </select>
                </div>

                <div class="col-12 mb-3">
                    <label for="loan_arrangement" class="form-label fw-semibold d-block">
                       Paying of Loans taken from University of UPF?
                        <br>
                        <small class="text-muted">(Applicable only when taking no pay leave)</small>
                    </label>
                    <select class="form-select w-auto d-inline-block align-middle ms-2" id="loan_arrangement" name="loan_arrangement" required>
                        <option value="" selected disabled>Select an option</option>
                        <option value="Make Arrangements">Make Arrangements</option>
                        <option value="Not Make Arrangements">Not Make Arrangements</option>
                    </select>
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