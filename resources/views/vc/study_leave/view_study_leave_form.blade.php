@extends('layouts.screen1')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-crown me-2 text-primary"></i>Study Leave Application Review
                </h2>
                <p class="text-muted mb-0">Reference No: {{ $draft_study_leave->reference_no }}</p>
            </div>
            <a href="{{ isset($from) && $from == 'accepted' ? route('vc.study.leave.index.accepted') : route('vc.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Study Leave Details (readonly) -->
        @include('StudyLeave.basic_info_form', ['readonly' => true])
        @include('StudyLeave.details_form', ['readonly' => true])
        @include('StudyLeave.working_covering_persons_form', ['readonly' => true])
        @include('hod_academic_establishment.study_leave.study_leave_hod_academic_establishment_review_section', ['readonly' => true])
        @include('hod.study_leave.study_leave_hod_review_section', ['readonly' => true])
        @include('dean.study_leave.study_leave_dean_review_section', ['readonly' => true])

        @if(isset($from) && $from == 'accepted')
            <div class="alert alert-success mt-3">
                <i class="fas fa-check-circle me-2"></i>This application has already been processed by the VC.
            </div>
        @else
        <!-- VC Review Section -->
        <form action="{{ route('vc.view.studyLeave.approve', $draft_study_leave->id) }}" method="POST" id="vcReviewForm">
            @csrf

            <div class="card mt-4">
                <div class="card-header card-header-dark text-white fw-semibold">
                    <i class="fas fa-clipboard-check me-2"></i>Vice Chancellor Review & Recommendation
                </div>
                <div class="card-body">

                    <!-- Question 1 - Recommend to Committee -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Recommended to submit to Leave and Awards Committee?
                            <span class="text-danger">*</span>
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vc_recommend_committee"
                                id="recommendCommitteeYes" value="1" required>
                            <label class="form-check-label" for="recommendCommitteeYes">
                                Yes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vc_recommend_committee"
                                id="recommendCommitteeNo" value="0" required>
                            <label class="form-check-label" for="recommendCommitteeNo">
                                No
                            </label>
                        </div>
                    </div>

                    <!-- Question 2 - Council Approval Decision -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Council Approval Decision
                            <span class="text-danger">*</span>
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vc_approved_council"
                                id="approvedCouncilYes" value="1" required>
                            <label class="form-check-label" for="approvedCouncilYes">
                                Yes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="vc_approved_council"
                                id="approvedCouncilNo" value="0" required>
                            <label class="form-check-label" for="approvedCouncilNo">
                                No
                            </label>
                        </div>
                    </div>

                    <!-- Conditional: If not approved -->
                    <div class="mb-4" id="notApproveReasonDiv" style="display: none;">
                        <label for="vc_not_approve_reason" class="form-label fw-semibold">
                            If not recommended, please give reasons
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="vc_not_approve_reason" name="vc_not_approve_reason" rows="4"
                            placeholder="Please provide detailed reasons for not recommending this leave"></textarea>
                        <div class="invalid-feedback">
                            Please provide reasons for not approving.
                        </div>
                    </div>

                    <!-- Any other remarks -->
                    <div class="mb-4">
                        <label for="vc_remarks" class="form-label fw-semibold">
                            Any other remarks
                        </label>
                        <textarea class="form-control" id="vc_remarks" name="vc_remarks" rows="3"
                            placeholder="Add any additional comments or remarks (optional)"></textarea>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ isset($from) && $from == 'accepted' ? route('vc.study.leave.index.accepted') : route('vc.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>

                        <div class="text-end">
                            <div class="card mb-2">
                                <div class="card-body py-2">
                                    <div class="alert alert-info mb-0 py-2">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Final Approval Stage</strong>
                                        <div class="small">This is the final decision on the study leave application.</div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn">
                                <i class="fas fa-check-circle me-2"></i>Submit Final Decision
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @endif
    </div>
    <!-- Forward Confirmation Modal -->
    <div class="modal fade" id="forwardConfirmModal" tabindex="-1" role="dialog" aria-labelledby="forwardConfirmModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="forwardConfirmModalLabel">Confirm Submit</h5>
                </div>
                <div class="modal-body">
                    <p class="mb-0">is that ok to foward it to MA for finlize</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="forwardConfirmCancel">Cancel</button>
                    <button type="button" class="btn btn-success" id="forwardConfirmOk">OK</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .card-header-dark {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            border-bottom: 3px solid #0d6efd;
        }

        .text-dark {
            color: #212529;
        }

        .text-primary {
            color: #0d6efd;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .form-control[readonly],
        .form-select:disabled,
        select:disabled {
            background-color: #e9ecef !important;
            color: #495057 !important;
            opacity: 1 !important;
        }

        .btn-lg {
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recommendCommitteeYes = document.getElementById('recommendCommitteeYes');
            const recommendCommitteeNo = document.getElementById('recommendCommitteeNo');
            const notApproveReasonDiv = document.getElementById('notApproveReasonDiv');
            const notApproveReasonTextarea = document.getElementById('vc_not_approve_reason');
            const form = document.getElementById('vcReviewForm');
            let forwardConfirmed = false;

            // Show/hide reason textarea based on recommendation
            function toggleReasonField() {
                if (recommendCommitteeNo.checked) {
                    notApproveReasonDiv.style.display = 'block';
                    notApproveReasonTextarea.setAttribute('required', 'required');
                } else {
                    notApproveReasonDiv.style.display = 'none';
                    notApproveReasonTextarea.removeAttribute('required');
                    notApproveReasonTextarea.value = '';
                    notApproveReasonTextarea.classList.remove('is-invalid');
                }
            }

            recommendCommitteeYes.addEventListener('change', toggleReasonField);
            recommendCommitteeNo.addEventListener('change', toggleReasonField);

            // Form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Check if "No" is selected and reason is empty
                if (recommendCommitteeNo.checked) {
                    const reasonValue = notApproveReasonTextarea.value.trim();
                    if (!reasonValue) {
                        e.preventDefault();
                        notApproveReasonTextarea.classList.add('is-invalid');
                        notApproveReasonTextarea.focus();
                        isValid = false;
                        alert('Please provide reasons for not recommending this leave.');
                        return;
                    }
                }

                // Confirm submission using modal
                if (isValid) {
                    if (!forwardConfirmed) {
                        e.preventDefault();
                        $('#forwardConfirmModal').modal('show');
                    }
                }
            });

            // Clear invalid state when user starts typing
            notApproveReasonTextarea.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });

            $('#forwardConfirmOk').on('click', function() {
                forwardConfirmed = true;
                $('#forwardConfirmModal').modal('hide');
                form.submit();
            });

            $('#forwardConfirmCancel').on('click', function() {
                forwardConfirmed = false;
                $('#forwardConfirmModal').modal('hide');
            });
        });
    </script>
@endsection

