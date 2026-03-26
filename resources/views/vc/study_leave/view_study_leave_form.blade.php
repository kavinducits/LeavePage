@extends('layouts.dashborad')
@section('title', 'VC Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('vc.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">VC Dashboard</span>
    </a>
@endsection
@section('sidebar')
    @php($pageName = 'Study Leave')
    @include('vc.partials.sidebar')
@endsection

@section('main-content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
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
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                <div class="card-header bg-primary text-white fw-semibold">
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

                    <!-- Any other remarks -->
                    <div class="mb-4">
                        <label for="vc_remarks" class="form-label fw-semibold">
                            Any other remarks
                        </label>
                        <textarea class="form-control" id="vc_remarks" name="vc_remarks" rows="3"
                            placeholder="Add comments. Required when recommendation is No."></textarea>
                        <div class="invalid-feedback">
                            Please provide remarks when leave is not recommended.
                        </div>
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
         .card-header-maroon {
            background-color: #0d6efd !important;
            color: #ffffff !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const recommendCommitteeYes = document.getElementById('recommendCommitteeYes');
            const recommendCommitteeNo = document.getElementById('recommendCommitteeNo');
            const remarksTextarea = document.getElementById('vc_remarks');
            const form = document.getElementById('vcReviewForm');
            let forwardConfirmed = false;

            function toggleRemarksRequirement() {
                if (recommendCommitteeNo.checked) {
                    remarksTextarea.setAttribute('required', 'required');
                } else {
                    remarksTextarea.removeAttribute('required');
                    remarksTextarea.classList.remove('is-invalid');
                }
            }

            recommendCommitteeYes.addEventListener('change', toggleRemarksRequirement);
            recommendCommitteeNo.addEventListener('change', toggleRemarksRequirement);
            toggleRemarksRequirement();

            // Form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;

                // Check if "No" is selected and remarks are empty
                if (recommendCommitteeNo.checked) {
                    const remarksValue = remarksTextarea.value.trim();
                    if (!remarksValue) {
                        e.preventDefault();
                        remarksTextarea.classList.add('is-invalid');
                        remarksTextarea.focus();
                        isValid = false;
                        alert('Please provide remarks when not recommending this leave.');
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
            remarksTextarea.addEventListener('input', function() {
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
        </div>
    </section>
</div>
@endsection

