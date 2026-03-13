<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Application Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="hold-transition sidebar-mini">
  
    @php
        $statusId = $draft_study_leave->approval_status_id ?? null;
    @endphp

    <div class="wrapper">
        <!-- Navbar -->
        @include('ma.partials.navbar')

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('ma.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">MA Dashboard</span>
            </a>
            <!-- Sidebar -->
            @php($pageName = 'Study Leave')
            @include('ma.partials.sidebar')
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @include('ma.partials.header')
            

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0 fw-bold">Application Reviews</h2>
                            <p class="text-muted mb-0">Reference No: {{ $draft_study_leave->reference_no }}</p>
                        </div>
                        <a href="{{ request()->get('from') == 'accepted' ? route('ma.studyleave.accepted') : route('ma.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>

                    <!-- Study Leave Duration Card -->
                    @include('ma.study_leave.study_leave_duration_card')

                    <!-- Personal Details (readonly) -->
                    @include('ma.partials.studyLeave', ['readonly' => true])
                  
                    
                  
                    <!-- Completed Review Sections (shown whenever data exists) -->
                    @if(optional($draft_study_leave)->registrar_recommendation !== null)
                        @include('hod_academic_establishment.study_leave.study_leave_hod_academic_establishment_review_section', ['readonly' => true])
                    @endif
                    @if(optional($draft_study_leave)->hod_recommend !== null)
                        @include('hod.study_leave.study_leave_hod_review_section', ['readonly' => true])
                    @endif
                    @if(optional($draft_study_leave)->dean_leave_recommendation_status !== null)
                        @include('dean.study_leave.study_leave_dean_review_section', ['readonly' => true])
                    @endif
                    @if(optional($draft_study_leave)->vc_recommend_submit_to_committee !== null)
                        @include('vc.study_leave.study_leave_vc_review_section', ['readonly' => true])
                    @endif

                    <!-- Review Section based on Status -->
                    @if($statusId == 3)
                        <!-- Status: Editing (Returned to User) -->
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-undo me-2"></i>
                            <strong>Application Returned to User</strong>
                            <p class="mb-0 mt-2">This application has been returned to the user for corrections.</p>
                        </div>
                    @elseif($statusId == 5)
                        <!-- Status: Processing HOD Academic Establishment -->
                        <div class="alert alert-success mt-4">
                            <i class="fas fa-paper-plane me-2"></i>
                            <strong>Application Forwarded</strong>
                            <p class="mb-0 mt-2">This application has been forwarded to HOD Academic Establishment for review.</p>
                        </div>
                    @elseif($statusId == 4)
                        <!-- Status: Processing MA - Show Action Section -->
                        <div class="card">
                            <div class="card-header bg-primary text-white fw-semibold">
                                <i class="fas fa-tasks me-2"></i>Review Actions
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="actionRemark" class="form-label fw-semibold">Remarks</label>
                                    <textarea class="form-control" id="actionRemark" name="remark" rows="3"
                                              placeholder="Add any comments or remarks"></textarea>
                                    <div id="remarkError" class="form-text text-danger" style="display: none;">
                                        Remarks are required when returning an application.
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-start">
                                    <form id="returnForm" action="{{ route('ma.studyleave.return', $draft_study_leave->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" id="returnRemarkInput" name="remark" value="">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-undo me-2"></i>Return to User
                                        </button>
                                    </form>

                                    <div class="text-right">
                                        @if(isset($departmentHead))
                                            <div class="card mb-2">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <strong>Forward to,&nbsp;</strong>
                                                        <div>
                                                            <div class="fw-semibold">{{ $departmentHead->head_name ?? ''}}</div>
                                                            <!-- <div class="text-muted small">{{ $departmentHead->head_position ?? '' }}</div>-->
                                                            <div class="text-muted small">Head of Academic Establishment</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card mb-2 border-warning">
                                                <div class="card-body py-2">
                                                    <div class="text-danger text-end">
                                                        <strong>No active Department Head</strong>
                                                        <div class="text-muted small">Forwarding is disabled until a head is active.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        <form id="approveForm" action="{{ route('StudyLeave.approve', $draft_study_leave->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" id="approveRemarkInput" name="remark" value="">
                                            <button type="submit" class="btn btn-success w-100" {{ empty($departmentHead) ? 'disabled' : '' }}>
                                                <i class="fas fa-check me-2"></i>Forward
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($statusId == 8)
                        <!-- Status: VC Checked - Finalizing -->
                        <div class="card">
                            <div class="card-header bg-primary text-white fw-semibold">
                                <i class="fas fa-gavel me-2"></i>Finalizing
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>VC Recommendation Completed</strong>
                                    <p class="mb-0 mt-2">This application has been reviewed by the VC. Please finalize the study leave decision.</p>
                                </div>

                                <form id="councilApprovalForm" action="{{ route('ma.studyleave.council.approve', $draft_study_leave->id) }}" method="POST">
                                    @csrf
                                    
                                    <!-- Study Leave Decision Radio -->
                                    <div class="mb-4">
                                        <label class="form-label fw-semibold">
                                            Study Leave Decision
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="study_leave_decision" id="decisionApproved" value="approved" required>
                                            <label class="form-check-label" for="decisionApproved">
                                                <i class="fas fa-check-circle text-success me-1"></i> Approved
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="study_leave_decision" id="decisionNotApproved" value="not_approved" required>
                                            <label class="form-check-label" for="decisionNotApproved">
                                                <i class="fas fa-times-circle text-danger me-1"></i> Not Approved
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Approved Fields (shown only when Approved is selected) -->
                                    <div id="approvedFields" style="display: none;">
                                        <!-- Committee Section -->
                                        <div class="card mb-3 border-success">
                                            <div class="card-header bg-light fw-semibold">
                                                <i class="fas fa-users me-2 text-success"></i>Committee Approval Details
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Approved by Committee <span class="text-danger">*</span></label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="ma_approve_leave_committee" id="committeeYes" value="1">
                                                            <label class="form-check-label" for="committeeYes">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="ma_approve_leave_committee" id="committeeNo" value="0">
                                                            <label class="form-check-label" for="committeeNo">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="ma_leave_committee_number" class="form-label fw-semibold">Committee Number <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="ma_leave_committee_number" name="ma_leave_committee_number" placeholder="Enter committee number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="ma_leave_committee_date" class="form-label fw-semibold">Committee Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="ma_leave_committee_date" name="ma_leave_committee_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Council Section -->
                                        <div class="card mb-3 border-primary">
                                            <div class="card-header bg-light fw-semibold">
                                                <i class="fas fa-landmark me-2 text-primary"></i>Council Approval Details
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">Approved by Council <span class="text-danger">*</span></label>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="ma_approve_council" id="councilYes" value="1">
                                                            <label class="form-check-label" for="councilYes">Yes</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="ma_approve_council" id="councilNo" value="0">
                                                            <label class="form-check-label" for="councilNo">No</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="ma_council_number" class="form-label fw-semibold">Council Number <span class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="ma_council_number" name="ma_council_number" placeholder="Enter council number">
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label for="ma_council_date" class="form-label fw-semibold">Council Date <span class="text-danger">*</span></label>
                                                        <input type="date" class="form-control" id="ma_council_date" name="ma_council_date">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="button" id="finalizeBtn" class="btn btn-primary btn-lg" disabled>
                                            <i class="fas fa-check-double me-2"></i>Finalize
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Confirmation Modal -->
                        <div class="modal fade" id="finalizeConfirmModal" tabindex="-1" role="dialog" aria-labelledby="finalizeConfirmModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header" id="finalizeModalHeader">
                                        <h5 class="modal-title" id="finalizeConfirmModalLabel"></h5>
                                    </div>
                                    <div class="modal-body" id="finalizeModalBody"></div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" id="finalizeConfirmNo">
                                            <i class="fas fa-times me-1"></i>No
                                        </button>
                                        <button type="button" class="btn" id="finalizeConfirmYes">
                                            <i class="fas fa-check me-1"></i>Yes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($statusId == 1)
                        <!-- Accepted - all review sections shown above -->
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle me-2"></i>This study leave application has been fully processed and approved.
                        </div>
                    @else
                        <!-- Other statuses - Read-only view -->
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-eye"></i> This application is in a different workflow stage. You have read-only access.
                        </div>
                    @endif

                    <!-- Return Confirmation Modal -->
                    @if($statusId == 4)
                    <div class="modal fade" id="returnConfirmModal" tabindex="-1" role="dialog" aria-labelledby="returnConfirmModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="returnConfirmModalLabel">Confirm Return</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to <strong>return</strong> this study leave application to the user?</p>
                                    <p class="text-muted mb-0">The user will be notified and can make changes before resubmitting.</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="returnConfirmNo">No</button>
                                    <button type="button" class="btn btn-danger" id="returnConfirmYes">Yes, Return</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Forward Confirmation Modal -->
                    <div class="modal fade" id="forwardConfirmModal" tabindex="-1" role="dialog" aria-labelledby="forwardConfirmModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title" id="forwardConfirmModalLabel">Confirm Forward</h5>
                                </div>
                                <div class="modal-body">
                                    <p>Are you sure you want to <strong>forward</strong> this study leave application to the <strong>Head of Academic Establishment</strong>?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" id="forwardConfirmNo">Cancel</button>
                                    <button type="button" class="btn btn-success" id="forwardConfirmYes">OK</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('ma.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    @if($statusId == 4)
    <script>
        var forwardConfirmed = false;

        // Form validation and submission handling
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            // Get the remark value and set it to the hidden input
            const remarkValue = document.getElementById('actionRemark').value.trim();
            document.getElementById('approveRemarkInput').value = remarkValue;

            // Clear any previous error highlighting
            clearRemarkError();

            // If not yet confirmed, show modal instead of submitting
            if (!forwardConfirmed) {
                e.preventDefault();
                $('#forwardConfirmModal').modal('show');
                return;
            }
        });

        var returnConfirmed = false;

        document.getElementById('returnForm').addEventListener('submit', function(e) {
            const remarkValue = document.getElementById('actionRemark').value.trim();

            // Validate that remarks are provided for return action
            if (!remarkValue) {
                e.preventDefault();
                showRemarkError();
                return;
            }

            // Set the remark value to the hidden input
            document.getElementById('returnRemarkInput').value = remarkValue;

            // Clear any previous error highlighting
            clearRemarkError();

            // If not yet confirmed, show modal instead of submitting
            if (!returnConfirmed) {
                e.preventDefault();
                $('#returnConfirmModal').modal('show');
                return;
            }
        });

        // Yes button - set flag and submit form
        $('#returnConfirmYes').on('click', function() {
            returnConfirmed = true;
            $('#returnConfirmModal').modal('hide');
            document.getElementById('returnForm').submit();
        });

        // No button - just close modal
        $('#returnConfirmNo').on('click', function() {
            returnConfirmed = false;
            $('#returnConfirmModal').modal('hide');
        });

        // Forward modal Yes - set flag and submit form
        $('#forwardConfirmYes').on('click', function() {
            forwardConfirmed = true;
            $('#forwardConfirmModal').modal('hide');
            document.getElementById('approveForm').submit();
        });

        // Forward modal No - cancel forwarding
        $('#forwardConfirmNo').on('click', function() {
            forwardConfirmed = false;
            $('#forwardConfirmModal').modal('hide');
        });

        // Helper functions for error handling
        function showRemarkError() {
            const remarkTextarea = document.getElementById('actionRemark');
            const errorDiv = document.getElementById('remarkError');

            // Highlight the textarea
            remarkTextarea.classList.add('is-invalid');
            remarkTextarea.style.borderColor = '#dc3545';
            remarkTextarea.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';

            // Add shake animation
            remarkTextarea.classList.add('shake-animation');
            setTimeout(() => {
                remarkTextarea.classList.remove('shake-animation');
            }, 500);

            // Show error message
            errorDiv.style.display = 'block';

            // Focus on the textarea
            remarkTextarea.focus();


        }

        function clearRemarkError() {
            const remarkTextarea = document.getElementById('actionRemark');
            const errorDiv = document.getElementById('remarkError');

            // Remove highlighting
            remarkTextarea.classList.remove('is-invalid');
            remarkTextarea.style.borderColor = '';
            remarkTextarea.style.boxShadow = '';

            // Hide error message
            errorDiv.style.display = 'none';
        }

        // Clear error highlighting when user starts typing
        document.getElementById('actionRemark').addEventListener('input', function() {
            if (this.value.trim()) {
                clearRemarkError();
            }
        });
    </script>
    @endif

    @if($statusId == 8)
    <script>
        $(document).ready(function() {
            var approvedRadio = document.getElementById('decisionApproved');
            var notApprovedRadio = document.getElementById('decisionNotApproved');
            var approvedFields = document.getElementById('approvedFields');
            var finalizeBtn = document.getElementById('finalizeBtn');
            var form = document.getElementById('councilApprovalForm');
            var confirmed = false;

            // Block all form submissions unless confirmed via modal Yes
            $(form).on('submit', function(e) {
                if (!confirmed) {
                    e.preventDefault();
                    return false;
                }
            });

            // Toggle approved fields and finalize button based on radio selection
            $(approvedRadio).on('change', function() {
                if (this.checked) {
                    $(approvedFields).show();
                    $(finalizeBtn).prop('disabled', false).removeClass('btn-danger').addClass('btn-primary');
                }
            });

            $(notApprovedRadio).on('change', function() {
                if (this.checked) {
                    $(approvedFields).hide();
                    // Clear approved fields
                    $('input[name="ma_approve_leave_committee"]').prop('checked', false);
                    $('#ma_leave_committee_number').val('');
                    $('#ma_leave_committee_date').val('');
                    $('input[name="ma_approve_council"]').prop('checked', false);
                    $('#ma_council_number').val('');
                    $('#ma_council_date').val('');
                    $(finalizeBtn).prop('disabled', false).removeClass('btn-primary').addClass('btn-danger');
                }
            });

            // Finalize button click - validate then show confirmation modal
            $(finalizeBtn).on('click', function(e) {
                e.preventDefault();
                var isApproved = approvedRadio.checked;

                // If approved, validate required fields
                if (isApproved) {
                    var missing = [];

                    if (!$('input[name="ma_approve_leave_committee"]:checked').length) {
                        missing.push('Approved by Committee');
                    }
                    if (!$('#ma_leave_committee_number').val().trim()) {
                        $('#ma_leave_committee_number').addClass('is-invalid');
                        missing.push('Committee Number');
                    } else {
                        $('#ma_leave_committee_number').removeClass('is-invalid');
                    }
                    if (!$('#ma_leave_committee_date').val()) {
                        $('#ma_leave_committee_date').addClass('is-invalid');
                        missing.push('Committee Date');
                    } else {
                        $('#ma_leave_committee_date').removeClass('is-invalid');
                    }
                    if (!$('input[name="ma_approve_council"]:checked').length) {
                        missing.push('Approved by Council');
                    }
                    if (!$('#ma_council_number').val().trim()) {
                        $('#ma_council_number').addClass('is-invalid');
                        missing.push('Council Number');
                    } else {
                        $('#ma_council_number').removeClass('is-invalid');
                    }
                    if (!$('#ma_council_date').val()) {
                        $('#ma_council_date').addClass('is-invalid');
                        missing.push('Council Date');
                    } else {
                        $('#ma_council_date').removeClass('is-invalid');
                    }

                    if (missing.length > 0) {
                        alert('Please fill in all required fields: ' + missing.join(', '));
                        return;
                    }
                }

                // Set modal content based on decision
                if (isApproved) {
                    $('#finalizeModalHeader').attr('class', 'modal-header bg-success text-white');
                    $('#finalizeConfirmModalLabel').text('Confirm Approval');
                    $('#finalizeModalBody').html('<p>Are you sure you want to <strong>approve</strong> this study leave application?</p><p class="text-muted mb-0">This action will finalize the application as approved.</p>');
                    $('#finalizeConfirmYes').attr('class', 'btn btn-success');
                } else {
                    $('#finalizeModalHeader').attr('class', 'modal-header bg-danger text-white');
                    $('#finalizeConfirmModalLabel').text('Confirm Rejection');
                    $('#finalizeModalBody').html('<p>Are you sure you want to <strong>reject</strong> this study leave application?</p><p class="text-muted mb-0">This action will finalize the application as not approved.</p>');
                    $('#finalizeConfirmYes').attr('class', 'btn btn-danger');
                }

                $('#finalizeConfirmModal').modal('show');
            });

            // Yes button - set flag and submit form
            $('#finalizeConfirmYes').on('click', function() {
                confirmed = true;
                $('#finalizeConfirmModal').modal('hide');
                form.submit();
            });

            // No button - just close modal, do nothing
            $('#finalizeConfirmNo').on('click', function() {
                confirmed = false;
                $('#finalizeConfirmModal').modal('hide');
            });
        });
    </script>
    @endif
      <style>
                        .card-header-dark {
                            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
                            color: white;
                            border-bottom: 3px solid #0d6efd;
                        }

                        /* Override AdminLTE dark theme styles for summary layout */
                   
                        .card-header.card-header-maroon {
                            background-color: #007bff !important;
                            color: #ffffff !important;
                        }

                        .card-header.bg-primary {
                            background-color: #007bff !important;
                            color: #ffffff !important;
                        }

                       
                    </style>

    <style>
        .form-control[readonly], .form-select:disabled, select:disabled {
            background-color: #e9ecef !important;
            color: #495057 !important;
            opacity: 1 !important;
        }

        /* Error highlighting for remarks */
        .form-control.is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        /* Animation for error highlighting */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .shake-animation {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</body>
</html>

