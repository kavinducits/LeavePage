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

    <form  action="{{ route('StudyLeave.BasicInfo.store') }}" method="POST" enctype="multipart/form-data" id="leave-form">
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
                <i class="fas fa-user me-2"></i>Details of the Study Leave
            </div>
            <div class="card-body">
                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Leave Type</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="" disabled selected>Select an option</option>
                            <option value="fresh">Fresh Study Leave</option>
                            <option value="extension">Extension</option>
                        </select>
                    </div>
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Type Of Study Leave Requested</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="" disabled selected>Select an option</option>
                            <option value="fresh">With Pay</option>
                            <option value="extension">Without Pay</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period of Study Leave Requested (From)</label>
                        <input type="date" name="study_leave_from" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period of Study Leave Requested (To)</label>
                        <input type="date" name="study_leave_to" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title of the Degree (e.g. M.A., M.Sc, MBA, M.Phil., M.D., PhD)</label>
                        <select name="degree_title" class="form-select" required>
                            <option value="" disabled selected>Select degree title</option>
                            <option value="MA">M.A.</option>
                            <option value="MSc">M.Sc</option>
                            <option value="MBA">MBA</option>
                            <option value="MPhil">M.Phil.</option>
                            <option value="MD">M.D.</option>
                            <option value="PhD">PhD</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">University or the Institute</label>
                    <input type="text" name="university_institute" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" name="country" class="form-control" required>
                </div>
                 <div class="col-md-6">
                    <label class="form-label fw-semibold">Field of study</label>
                    <input type="text" name="field_of_study" class="form-control" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Relevancy and Details of the Study Program</label>
                    <textarea name="study_program_details" class="form-control" rows="4" required></textarea>
                </div>
                <div class="col-md-6">
                        <label class="form-label fw-semibold"> Funding type</label>
                        <select name="funding_type" class="form-select" required>
                            <option value="" disabled selected>Select funding type</option>
                            <option value="Full">Self-Funding</option>
                            <option value="Partial">Scholarship</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="scholarship-details" style="display: none;">
                        <label class="form-label fw-semibold">Scholarship Source</label>
                        <select name="scholarship_source" class="form-select">
                            <option value="" disabled selected>Select source</option>
                            <option value="agency">Scholarship offering agency</option>
                            <option value="project">Funds from a project</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="scholarship-extra-details" style="display: none;">
                        <div id="scholarship-amount-group" style="display: none;">
                            <label class="form-label fw-semibold">Scholarship Amount</label>
                            <input type="number" name="scholarship_amount" class="form-control" min="0" step="0.01" placeholder="Enter amount">
                        </div>
                        <div id="project-name-group" style="display: none;">
                            <label class="form-label fw-semibold">Project Name</label>
                            <input type="text" name="project_name" class="form-control" placeholder="Enter project name">
                        </div>
                    </div>
                    <div class="col-md-12">
                    <label class="form-label fw-semibold">Any Other Details</label>
                    <textarea name="any_other_details" class="form-control" rows="4" required></textarea>
                </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const fundingType = document.querySelector('select[name="funding_type"]');
                            const scholarshipDetails = document.getElementById('scholarship-details');
                            const scholarshipSource = scholarshipDetails.querySelector('select[name="scholarship_source"]');
                            const scholarshipExtraDetails = document.getElementById('scholarship-extra-details');
                            const scholarshipAmountGroup = document.getElementById('scholarship-amount-group');
                            const projectNameGroup = document.getElementById('project-name-group');

                            fundingType.addEventListener('change', function () {
                                if (this.value === 'Partial') {
                                    scholarshipDetails.style.display = 'block';
                                } else {
                                    scholarshipDetails.style.display = 'none';
                                    scholarshipDetails.querySelector('select').value = '';
                                    scholarshipExtraDetails.style.display = 'none';
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'none';
                                }
                            });

                            scholarshipSource.addEventListener('change', function () {
                                scholarshipExtraDetails.style.display = 'block';
                                if (this.value === 'agency') {
                                    scholarshipAmountGroup.style.display = 'block';
                                    projectNameGroup.style.display = 'none';
                                } else if (this.value === 'project') {
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'block';
                                } else {
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'none';
                                    scholarshipExtraDetails.style.display = 'none';
                                }
                            });
                        });
                    </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const fundingType = document.querySelector('select[name="funding_type"]');
                            const scholarshipDetails = document.getElementById('scholarship-details');

                            fundingType.addEventListener('change', function () {
                                if (this.value === 'Partial') {
                                    scholarshipDetails.style.display = 'block';
                                } else {
                                    scholarshipDetails.style.display = 'none';
                                    scholarshipDetails.querySelector('select').value = '';
                                }
                            });
                        });
                    </script>
                       
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