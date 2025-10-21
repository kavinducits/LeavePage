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

    <!-- Form for Work Covering Persons -->

    <form  action="{{ route('StudyLeave.WorkCoveringPersons.store') }}" method="POST" enctype="multipart/form-data" id="leave-form">
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
                <i class="fas fa-user me-2"></i>Arrangements made to cover applicants’ work during the period of leave
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row g-3">
                    
                    <!-- Work Covering Persons Inputs -->
                    <div class="col-12">

                        <!-- Nominee Person For Teaching -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominate Person For Teaching (Emp No. & Name)</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control @error('nominee_emp_no') is-invalid @enderror" id="nominee_emp_no" name="nominee_emp_no" value="{{ old('nominee_emp_no', $leave->nominee_emp_no ?? '') }}" placeholder="Employee Number" required>
                                        @error('nominee_emp_no')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control @error('nominee_name') is-invalid @enderror" id="nominee_name" name="nominee_name" value="{{ old('nominee_name', $leave->nominee_name ?? '') }}" placeholder="Employee Name" required>
                                        @error('nominee_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Nominee Person For Administrative Work -->

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominate Person For Administrative Work (Emp No. & Name)</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control @error('admin_nominee_emp_no') is-invalid @enderror" id="admin_nominee_emp_no" name="admin_nominee_emp_no" value="{{ old('admin_nominee_emp_no', $leave->admin_nominee_emp_no ?? '') }}" placeholder="Employee Number" required>
                                        @error('admin_nominee_emp_no')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control @error('nominee_name') is-invalid @enderror" id="nominee_name" name="nominee_name" value="{{ old('nominee_name', $leave->nominee_name ?? '') }}" placeholder="Employee Name" required>
                                        @error('nominee_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!--  Nominee Person For Other Work -->

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominate Person For Other Work (Emp No. & Name)</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" class="form-control @error('other_nominee_emp_no') is-invalid @enderror" id="other_nominee_emp_no" name="other_nominee_emp_no" value="{{ old('other_nominee_emp_no', $leave->other_nominee_emp_no ?? '') }}" placeholder="Employee Number" required>
                                        @error('other_nominee_emp_no')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control @error('nominee_name') is-invalid @enderror" id="nominee_name" name="nominee_name" value="{{ old('nominee_name', $leave->nominee_name ?? '') }}" placeholder="Employee Name" required>
                                        @error('nominee_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        
                    
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