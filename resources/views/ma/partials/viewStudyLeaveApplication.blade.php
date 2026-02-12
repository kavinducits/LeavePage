

    <!-- Section 1: Personal Details -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-user me-2"></i>Personal Detailss
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Employee Number -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->employee_no ?? '' }}" readonly>
                </div>
                <!-- Name with Initials -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name with Initials</label>
                    <input type="text" class="form-control" value="{{ $application->name_with_initials ?? '' }}" readonly>
                </div>
                <!-- Designation -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Designation</label>
                    <input type="text" class="form-control" value="{{ $application->designation ?? '' }}" readonly>
                </div>
                <!-- Department -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" class="form-control" value="{{ $application->department ?? '' }}" readonly>
                </div>
                <!-- Faculty -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Faculty</label>
                    <input type="text" class="form-control" value="{{ $application->faculty ?? '' }}" readonly>
                </div>
                <!-- Email Address -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="text" class="form-control" value="{{ $application->email ?? '' }}" readonly>
                </div>
                <!-- Passport No -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Passport No</label>
                    <input type="text" class="form-control" value="{{ $application->passport_no ?? '' }}" readonly>
                </div>
                <!-- Passport Validity -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Passport Validity Date</label>
                    <input type="text" class="form-control" value="{{ $application->passport_validity ?? '' }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Details of the Study Leave -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-graduation-cap me-2"></i>Details of the Study Leave
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Leave Type -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Leave Type</label>
                    <input type="text" class="form-control" value="{{ $application->leave_type ?? '' }}" readonly>
                </div>
                <!-- Type of Study Leave Requested -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Type of Study Leave Requested</label>
                    <input type="text" class="form-control" value="{{ $application->leave_payment_type ?? '' }}" readonly>
                </div>
                <!-- Period From -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Period of Study Leave (From)</label>
                    <input type="text" class="form-control" value="{{ $application->study_leave_from ?? '' }}" readonly>
                </div>
                <!-- Period To -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Period of Study Leave (To)</label>
                    <input type="text" class="form-control" value="{{ $application->study_leave_to ?? '' }}" readonly>
                </div>
                <!-- Degree Title -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Title of the Degree</label>
                    <input type="text" class="form-control" value="{{ $application->degree_title ?? '' }}" readonly>
                </div>
                <!-- University/Institute -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">University or Institute</label>
                    <input type="text" class="form-control" value="{{ $application->university_institute ?? '' }}" readonly>
                </div>
                <!-- Country -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" class="form-control" value="{{ $application->country ?? '' }}" readonly>
                </div>
                <!-- Field of Study -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Field of Study</label>
                    <input type="text" class="form-control" value="{{ $application->field_of_study ?? '' }}" readonly>
                </div>
                <!-- Study Program Details -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Relevancy and Details of the Study Program</label>
                    <textarea class="form-control" rows="4" readonly>{{ $application->study_program_details ?? '' }}</textarea>
                </div>
                <!-- Funding Type -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Funding Type</label>
                    <input type="text" class="form-control" value="{{ $application->funding_type ?? '' }}" readonly>
                </div>
                <!-- Scholarship Source -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Scholarship Source</label>
                    <input type="text" class="form-control" value="{{ $application->scholarship_source ?? '' }}" readonly>
                </div>
                <!-- Scholarship Amount -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Scholarship Amount</label>
                    <input type="text" class="form-control" value="{{ $application->scholarship_amount ?? '' }}" readonly>
                </div>
                <!-- Project Name -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Project Name</label>
                    <input type="text" class="form-control" value="{{ $application->project_name ?? '' }}" readonly>
                </div>
                <!-- Other Details -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Any Other Details</label>
                    <textarea class="form-control" rows="3" readonly>{{ $application->any_other_details ?? '' }}</textarea>
                </div>
                <!-- Air Passage Request -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Air Passage Request</label>
                    <input type="text" class="form-control" value="{{ ($application->air_passage_request ?? '') == '1' ? 'Yes' : (($application->air_passage_request ?? '') == '0' ? 'No' : '') }}" readonly>
                </div>
                <!-- Warm Cloth Allowance -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Warm Cloth Allowance Request</label>
                    <input type="text" class="form-control" value="{{ ($application->warm_cloth_allowance_request ?? '') == '1' ? 'Yes' : (($application->warm_cloth_allowance_request ?? '') == '0' ? 'No' : '') }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Previous Study Leave Records -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-history me-2"></i>Previous Study Leave Records
        </div>
        <div class="card-body">
            @if(isset($previousLeaves) && count($previousLeaves) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Degree</th>
                                <th>University/Institute</th>
                                <th>Duration (From - To)</th>
                                <th>With Pay/No Pay</th>
                                <th>Completion Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previousLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->prev_leave_type ?? '' }}</td>
                                    <td>{{ $leave->prev_university ?? '' }}</td>
                                    <td>{{ $leave->prev_duration_from ?? '' }} - {{ $leave->prev_duration_to ?? '' }}</td>
                                    <td>{{ $leave->prev_with_pay ?? '' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $leave->prev_completed == 'Completed' ? 'success' : 'warning' }}">
                                            {{ $leave->prev_completed ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">No previous study leave records found.</p>
            @endif
        </div>
    </div>

    <!-- Section 4: Work Covering Arrangements -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-users me-2"></i>Arrangements Made to Cover Applicant's Work During Leave
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Teaching Work Nominee -->
                <div class="col-md-12">
                    <h6 class="fw-bold text-secondary mb-3">Nominee for Teaching Work</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_teaching_empno ?? '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee Name</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_teaching_name ?? '' }}" readonly>
                </div>

                <!-- Administrative Work Nominee -->
                <div class="col-md-12 mt-4">
                    <h6 class="fw-bold text-secondary mb-3">Nominee for Administrative Work</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_admin_empno ?? '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee Name</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_admin_name ?? '' }}" readonly>
                </div>

                <!-- Other Work Nominee -->
                <div class="col-md-12 mt-4">
                    <h6 class="fw-bold text-secondary mb-3">Nominee for Other Work</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_other_empno ?? '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee Name</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_other_name ?? '' }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 5: Handling of Properties and Loans -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>Handling of Library Books, Properties, and Loans
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Library and Property Handling -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Library Books, Computer or Other Properties</label>
                    <input type="text" class="form-control" value="{{ $application->library_and_property_handling ?? '' }}" readonly>
                </div>
                <!-- Loan Handling -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Paying of Loans from University/UPF</label>
                    <input type="text" class="form-control" value="{{ $application->loan_handling ?? '' }}" readonly>
                </div>
            </div>
        </div>
    </div>

   
   

   

</div>

<!-- Print Styles -->
<style>
    @media print {
        .btn, .card-header, .alert {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .btn {
            display: none;
        }
    }
</style>


