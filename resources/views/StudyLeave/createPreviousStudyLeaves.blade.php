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

    <!-- Form for Previous Study Leaves -->

    <form  action="{{ route('StudyLeave.PreviousStudyLeaves.store') }}" method="POST" enctype="multipart/form-data" id="leave-form">
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
                <i class="fas fa-user me-2"></i>Previous Study Leave Records
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-12">
                        <!-- Table for Previous Study Leave Records -->
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Degree</th>
                                    <th>University/Institute</th>
                                    <th>Duration (with dates)</th>
                                    <th>With Pay/No Pay</th>
                                    <th>Completion</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="prev_degree[]" class="form-select">
                                            <option value="">Select Degree</option>
                                            <option value="M.A.">M.A.</option>
                                            <option value="M.Sc">M.Sc</option>
                                            <option value="MBA">MBA</option>
                                            <option value="M.Phil.">M.Phil.</option>
                                            <option value="M.D.">M.D.</option>
                                            <option value="PhD">PhD</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="prev_university[]" class="form-control"></td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <span class="align-self-center">from</span>
                                            <input type="date" name="prev_duration_from[]" class="form-control" placeholder="From">
                                            <span class="align-self-center">to</span>
                                            <input type="date" name="prev_duration_to[]" class="form-control" placeholder="To">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <select name="prev_with_pay[]" class="form-select">
                                            <option value="">Select</option>
                                            <option value="With Pay">With Pay</option>
                                            <option value="No Pay">No Pay</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <select name="prev_completed[]" class="form-select">
                                            <option value="">Select</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Not Completed">Not Completed</option>
                                        </select>
                                    </td>
                                   
                                </tr>
                            </tbody>
                        </table>
                        <small class="text-muted">Add details of previous study leave records, if any.</small>
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