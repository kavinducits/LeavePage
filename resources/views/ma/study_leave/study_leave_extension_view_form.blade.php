<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Extension Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('StudyLeave.study_leave_extension.partials.extension_summary_styles')
    <style>
        .accordion-details-text {
            font-size: 0.95rem;
            font-weight: 600;
        }

        #detailsAccordion .accordion-button::after {
            display: none;
        }

        #detailsAccordion .accordion-button,
        #detailsAccordion .accordion-button:not(.collapsed) {
            background-color: #0d6efd;
            color: #ffffff;
        }

        #detailsAccordion .accordion-button i {
            color: #ffffff;
        }

        .accordion-state-icon {
            margin-left: auto;
            transition: transform 0.2s ease;
        }

        .remarks-display {
            background-color: #ffffff;
            color: #212529;
            border: 1px solid #dee2e6;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini">
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
            @php $pageName = 'Study Leave Extensions' @endphp
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
                            <h2 class="mb-0 fw-bold">Extension Request Review</h2>
                            <p class="text-muted mb-0">Reference No: {{ $extension->reference_no }}</p>
                        </div>
                        <a href="{{ request()->get('from') == 'accepted' ? route('ma.studyleave.extensions.accepted') : route('ma.studyleave.extensions') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @include('StudyLeave.study_leave_extension.partials.extension_summary_card', [
                        'headerClass' => 'bg-primary text-white fw-semibold',
                        'durationDays' => $durationDays,
                        'durationMonths' => $durationMonths,
                        'showPaymentHelpText' => true,
                    ])

                    <!-- Accordion for More Details -->
                    <div class="accordion mb-4" id="detailsAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDetails">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDetails" aria-expanded="false"
                                    aria-controls="collapseDetails">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span class="accordion-details-text">More Details - Original Study Leave Application</span>
                                    <i id="detailsChevron" class="fas fa-chevron-down accordion-state-icon"></i>
                                </button>
                            </h2>
                            <div id="collapseDetails" class="accordion-collapse collapse"
                                aria-labelledby="headingDetails">
                                <div class="accordion-body">
                                    <form method="POST" class="my-4">
                                        @csrf
                                        <!-- Include study leave forms with readonly -->
                                        @php
                                            $draft_study_leave = $extension;
                                            $readonly = true;
                                        @endphp

                                        @include('StudyLeave.basic_info_form')
                                        @include('StudyLeave.details_form')
                                        @include('StudyLeave.working_covering_persons_form')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review Sections (shown whenever data exists) -->
                    @if(optional($extension)->acad_est_head_recommend !== null)
                    <div class="card mt-4 mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-clipboard-check me-2"></i>Head Of Academic Establishment Review
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Recommendation</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="acad_est_head_recommend_view" id="acadEstYes" {{ $extension->acad_est_head_recommend == 1 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="acadEstYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="acad_est_head_recommend_view" id="acadEstNo" {{ $extension->acad_est_head_recommend == 0 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="acadEstNo">No</label>
                                    </div>
                                </div>
                                @if(!isset($extension->acad_est_head_recommend))
                                <div class="text-muted small mt-2">Not specified</div>
                                @endif
                            </div>
                            @if($extension->acad_est_head_recommend == 0 && !empty($extension->acad_est_head_not_recommend_reason))
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reason for Not Recommending</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="mb-0" style="white-space:pre-wrap">{{ $extension->acad_est_head_not_recommend_reason }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(!empty($extension->acad_est_head_remarks))
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Any other remarks</label>
                                <div class="remarks-display rounded p-3 mb-0" style="white-space:pre-wrap">{{ $extension->acad_est_head_remarks }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(optional($extension)->hod_recommend !== null)
                    <div class="card mt-4 mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Recommendation</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="hod_recommend_view" id="hodYes" {{ $extension->hod_recommend == 1 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="hodYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="hod_recommend_view" id="hodNo" {{ $extension->hod_recommend == 0 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="hodNo">No</label>
                                    </div>
                                </div>
                                @if(!isset($extension->hod_recommend))
                                <div class="text-muted small mt-2">Not specified</div>
                                @endif
                            </div>
                            @if($extension->hod_recommend == 0 && !empty($extension->hod_not_recommend_reason))
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reason for Not Recommending</label>
                                <div class="card bg-light"><div class="card-body"><p class="mb-0" style="white-space:pre-wrap">{{ $extension->hod_not_recommend_reason }}</p></div></div>
                            </div>
                            @endif
                            @if(!empty($extension->hod_remarks))
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Any other remarks</label>
                                <div class="remarks-display rounded p-3 mb-0" style="white-space:pre-wrap">{{ $extension->hod_remarks }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(optional($extension)->dean_recommend !== null)
                    <div class="card mt-4 mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-clipboard-check me-2"></i>Dean Review & Recommendation
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Recommendation</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="dean_recommend_view" id="deanYes" {{ $extension->dean_recommend == 1 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="deanYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="dean_recommend_view" id="deanNo" {{ $extension->dean_recommend == 0 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="deanNo">No</label>
                                    </div>
                                </div>
                                @if(!isset($extension->dean_recommend))
                                <div class="text-muted small mt-2">Not specified</div>
                                @endif
                            </div>
                            @if($extension->dean_recommend == 0 && !empty($extension->dean_not_recommended_reason))
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reason for Not Recommending</label>
                                <div class="card bg-light"><div class="card-body"><p class="mb-0" style="white-space:pre-wrap">{{ $extension->dean_not_recommended_reason }}</p></div></div>
                            </div>
                            @endif
                            @if(!empty($extension->dean_remark))
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Any other remarks</label>
                                <div class="remarks-display rounded p-3 mb-0" style="white-space:pre-wrap">{{ $extension->dean_remark }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(optional($extension)->vc_recommend !== null)
                    <div class="card mt-4 mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-clipboard-check me-2"></i>VC Review & Recommendation
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Recommendation</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="vc_recommend_view" id="vcYes" {{ $extension->vc_recommend == 1 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="vcYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="vc_recommend_view" id="vcNo" {{ $extension->vc_recommend == 0 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="vcNo">No</label>
                                    </div>
                                </div>
                                @if(!isset($extension->vc_recommend))
                                <div class="text-muted small mt-2">Not specified</div>
                                @endif
                            </div>
                            @if($extension->vc_recommend == 0 && !empty($extension->vc_not_recommend_reason))
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reason for Not Recommending</label>
                                <div class="card bg-light"><div class="card-body"><p class="mb-0" style="white-space:pre-wrap">{{ $extension->vc_not_recommend_reason }}</p></div></div>
                            </div>
                            @endif
                            @if(!empty($extension->vc_remarks))
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Any other remarks</label>
                                <div class="remarks-display rounded p-3 mb-0" style="white-space:pre-wrap">{{ $extension->vc_remarks }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Action Section -->
                    @if($extension->ma_recommend !== null)
                    {{-- MA has already submitted their review — show readonly card --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-clipboard-check me-2"></i>MA Review & Recommendation
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">MA Recommendation</label>
                                <div class="d-flex gap-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ma_recommend_view" id="maRecommendViewYes"
                                            {{ $extension->ma_recommend == 1 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="maRecommendViewYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="ma_recommend_view" id="maRecommendViewNo"
                                            {{ $extension->ma_recommend == 0 ? 'checked' : '' }} disabled>
                                        <label class="form-check-label" for="maRecommendViewNo">No</label>
                                    </div>
                                </div>
                            </div>
                            @if($extension->ma_recommend == 0 && !empty($extension->ma_not_recommend_reason))
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reason for Not Recommending</label>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $extension->ma_not_recommend_reason }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            @if(!empty($extension->ma_remarks))
                            <div class="mb-0">
                                <label class="form-label fw-semibold">MA Remarks</label>
                                <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $extension->ma_remarks }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @if($extension->extension_status_id == 8)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-gavel me-2"></i>Finalize Extension Decision
                        </div>
                        <div class="card-body d-flex justify-content-end gap-2">
                            <form id="finalizeForm" action="{{ route('ma.extension.finalize', $extension->extension_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle me-2"></i>Finalize as Approved
                                </button>
                            </form>
                            <form id="rejectForm" action="{{ route('ma.extension.reject', $extension->extension_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-times-circle me-2"></i>Reject Extension
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif
                    @elseif($extension->extension_status_id != 3 && $extension->extension_status_id != 1)
                    <div class="card">
                        <div class="card-header bg-dark text-white fw-semibold">
                            <i class="fas fa-clipboard-check me-2"></i>MA Review & Recommendation
                        </div>
                        <div class="card-body">
                            <!-- Recommendation Radio Buttons -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Extension is recommended
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ma_recommend" id="maRecommendYes"
                                        value="1" required>
                                    <label class="form-check-label" for="maRecommendYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ma_recommend" id="maRecommendNo"
                                        value="0" required>
                                    <label class="form-check-label" for="maRecommendNo">
                                        No
                                    </label>
                                </div>
                                <div id="recommendError" class="form-text text-danger" style="display: none;">
                                    Please select a recommendation.
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="ma_extension_payment_type" class="form-label fw-semibold">
                                    Extension Payment Type
                                    <span class="text-danger">*</span>
                                </label>
                                <select id="ma_extension_payment_type" class="form-select" required>
                                    <option value="" {{ !in_array((string) $extension->extension_payment_type, ['0', '1'], true) ? 'selected' : '' }} disabled>Select payment type</option>
                                    <option value="1" {{ (string) $extension->extension_payment_type === '1' ? 'selected' : '' }}>With Pay</option>
                                    <option value="0" {{ (string) $extension->extension_payment_type === '0' ? 'selected' : '' }}>Without Pay</option>
                                </select>
                                <div id="extensionPaymentTypeError" class="form-text text-danger" style="display: none;">
                                    Please select an extension payment type.
                                </div>
                            </div>

                            <!-- Not Recommend Reason (shown when No is selected) -->
                            <div class="mb-4" id="maNotRecommendReasonDiv" style="display: none;">
                                <label for="ma_not_recommend_reason" class="form-label fw-semibold">
                                    If not recommended, please give reasons
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="ma_not_recommend_reason" name="ma_not_recommend_reason" rows="4"
                                    placeholder="Please provide detailed reasons for not recommending this extension"></textarea>
                                <div id="notRecommendReasonError" class="form-text text-danger" style="display: none;">
                                    Please provide reasons for not recommending.
                                </div>
                            </div>

                            <!-- MA Remarks -->
                            <div class="mb-3">
                                <label for="actionRemark" class="form-label fw-semibold">Any other remarks</label>
                                <textarea class="form-control" id="actionRemark" name="remark" rows="4"
                                    placeholder="Add your comments or remarks about this extension request (optional)"></textarea>
                                <div id="remarkError" class="form-text text-danger" style="display: none;">
                                    Remarks are required when returning an application.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-start">
                                <form id="returnForm"
                                    action="{{ route('ma.extension.return', $extension->extension_id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" id="returnRemarkInput" name="remark" value="">
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-undo me-2"></i>Return to User
                                    </button>
                                </form>

                                <div class="text-right">
                                    @if (isset($departmentHead))
                                        <div class="card mb-2">
                                            <div class="card-body py-2">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <strong>Forward to,&nbsp;</strong>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $departmentHead->head_title ?? 'Head' }}&nbsp;{{ $departmentHead->head_name ?? '' }}
                                                        </div>
                                                        <div class="text-muted small">
                                                            {{ $departmentHead->head_position ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="card mb-2 border-warning">
                                            <div class="card-body py-2">
                                                <div class="text-danger text-end">
                                                    <strong>No active Department Head</strong>
                                                    <div class="text-muted small">Forwarding is disabled until a head
                                                        is active.</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <form id="approveForm"
                                        action="{{ route('ma.extension.forward', $extension->extension_id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" id="approveRemarkInput" name="remark" value="">
                                        <input type="hidden" id="approveRecommendInput" name="ma_recommend" value="">
                                        <input type="hidden" id="approveNotRecommendReasonInput" name="ma_not_recommend_reason" value="">
                                        <input type="hidden" id="approveExtensionPaymentTypeInput" name="extension_payment_type" value="">
                                        <button type="submit" class="btn btn-success btn-lg w-100"
                                            {{ empty($departmentHead) ? 'disabled' : '' }}>
                                            <i class="fas fa-forward me-2"></i>Forward to HOD
                                        </button>
                                    </form>
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

<!-- Confirm Forward Modal -->
<div class="modal fade" id="confirmForwardModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#800020;color:white;">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Confirm Forward</h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-forward fa-3x mb-3" style="color:#800020"></i>
                <p class="mb-0 fs-6">Are you sure you want to forward this extension request to Head of Academic Establishment?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="confirmForwardYes">
                    <i class="fas fa-check me-2"></i>Yes, Forward
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Return Modal -->
<div class="modal fade" id="confirmReturnModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#800020;color:white;">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Confirm Return</h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-undo fa-3x mb-3" style="color:#800020"></i>
                <p class="mb-0 fs-6">Are you sure you want to return this extension request to the user?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-warning" id="confirmReturnYes">
                    <i class="fas fa-check me-2"></i>Yes, Return
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Finalize Modal -->
<div class="modal fade" id="confirmFinalizeModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#800020;color:white;">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Confirm Finalize</h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-3x mb-3" style="color:#198754"></i>
                <p class="mb-0 fs-6">Are you sure you want to finalize this extension as Approved?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>No
                </button>
                <button type="button" class="btn btn-success" id="confirmFinalizeYes">
                    <i class="fas fa-check me-2"></i>Yes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Reject Modal -->
<div class="modal fade" id="confirmRejectModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#800020;color:white;">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Confirm Reject</h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-times-circle fa-3x mb-3" style="color:#dc3545"></i>
                <p class="mb-0 fs-6">Are you sure you want to reject this extension request?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>No
                </button>
                <button type="button" class="btn btn-danger" id="confirmRejectYes">
                    <i class="fas fa-check me-2"></i>Yes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Operation Success Modal -->
<div class="modal fade" id="operationSuccessModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#198754;color:white;">
                <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Success</h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-3x mb-3" style="color:#198754"></i>
                <p class="mb-0 fs-6">{{ session('success') }}</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i>OK
                </button>
            </div>
        </div>
    </div>
</div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        const approveForm = document.getElementById('approveForm');
        const returnForm = document.getElementById('returnForm');
        const finalizeForm = document.getElementById('finalizeForm');
        const rejectForm = document.getElementById('rejectForm');
        const actionRemarkInput = document.getElementById('actionRemark');
        const notRecommendReasonInput = document.getElementById('ma_not_recommend_reason');
        const extensionPaymentTypeInput = document.getElementById('ma_extension_payment_type');

        // Show/hide not recommend reason field based on radio selection
        document.querySelectorAll('input[name="ma_recommend"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const notRecommendDiv = document.getElementById('maNotRecommendReasonDiv');
                if (!notRecommendDiv || !notRecommendReasonInput) {
                    return;
                }

                if (this.value === '0') {
                    notRecommendDiv.style.display = 'block';
                } else {
                    notRecommendDiv.style.display = 'none';
                    notRecommendReasonInput.value = '';
                    clearNotRecommendReasonError();
                }
                clearRecommendError();
            });
        });

        // Forward form
        const forwardModalElement = document.getElementById('confirmForwardModal');
        const confirmForwardModal = forwardModalElement ? new bootstrap.Modal(forwardModalElement) : null;
        let forwardConfirmed = false;

        if (approveForm && confirmForwardModal) {
            approveForm.addEventListener('submit', function(e) {
                if (forwardConfirmed) { return; }

                const remarkValue = actionRemarkInput ? actionRemarkInput.value.trim() : '';
                const recommendRadio = document.querySelector('input[name="ma_recommend"]:checked');
                const extensionPaymentTypeValue = extensionPaymentTypeInput ? extensionPaymentTypeInput.value : '';

                clearAllErrors();

                if (!recommendRadio) {
                    e.preventDefault();
                    showRecommendError();
                    return false;
                }

                if (!extensionPaymentTypeValue) {
                    e.preventDefault();
                    showExtensionPaymentTypeError();
                    return false;
                }

                if (recommendRadio.value === '0') {
                    const notRecommendReason = notRecommendReasonInput ? notRecommendReasonInput.value.trim() : '';
                    if (notRecommendReason === '') {
                        e.preventDefault();
                        showNotRecommendReasonError();
                        return false;
                    }
                    document.getElementById('approveNotRecommendReasonInput').value = notRecommendReason;
                }

                document.getElementById('approveRemarkInput').value = remarkValue;
                document.getElementById('approveRecommendInput').value = recommendRadio.value;
                document.getElementById('approveExtensionPaymentTypeInput').value = extensionPaymentTypeValue;

                e.preventDefault();
                confirmForwardModal.show();
            });

            const confirmForwardYes = document.getElementById('confirmForwardYes');
            if (confirmForwardYes) {
                confirmForwardYes.addEventListener('click', function () {
                    forwardConfirmed = true;
                    confirmForwardModal.hide();
                    approveForm.submit();
                });
            }
        }

        // Return form
        const returnModalElement = document.getElementById('confirmReturnModal');
        const returnConfirmModal = returnModalElement ? new bootstrap.Modal(returnModalElement) : null;
        let returnConfirmed = false;

        if (returnForm && returnConfirmModal) {
            returnForm.addEventListener('submit', function(e) {
                if (returnConfirmed) { return; }

                const remarkValue = actionRemarkInput ? actionRemarkInput.value.trim() : '';
                clearAllErrors();

                if (remarkValue === '') {
                    e.preventDefault();
                    showRemarkError();
                    return false;
                }

                document.getElementById('returnRemarkInput').value = remarkValue;
                e.preventDefault();
                returnConfirmModal.show();
            });

            const confirmReturnYes = document.getElementById('confirmReturnYes');
            if (confirmReturnYes) {
                confirmReturnYes.addEventListener('click', function () {
                    returnConfirmed = true;
                    returnConfirmModal.hide();
                    returnForm.submit();
                });
            }
        }

        // Finalize extension form
        const finalizeModalElement = document.getElementById('confirmFinalizeModal');
        const confirmFinalizeModal = finalizeModalElement ? new bootstrap.Modal(finalizeModalElement) : null;
        let finalizeConfirmed = false;

        if (finalizeForm && confirmFinalizeModal) {
            finalizeForm.addEventListener('submit', function (e) {
                if (finalizeConfirmed) { return; }
                e.preventDefault();
                confirmFinalizeModal.show();
            });

            const confirmFinalizeYes = document.getElementById('confirmFinalizeYes');
            if (confirmFinalizeYes) {
                confirmFinalizeYes.addEventListener('click', function () {
                    finalizeConfirmed = true;
                    confirmFinalizeModal.hide();
                    finalizeForm.submit();
                });
            }
        }

        // Reject extension form
        const rejectModalElement = document.getElementById('confirmRejectModal');
        const confirmRejectModal = rejectModalElement ? new bootstrap.Modal(rejectModalElement) : null;
        let rejectConfirmed = false;

        if (rejectForm && confirmRejectModal) {
            rejectForm.addEventListener('submit', function (e) {
                if (rejectConfirmed) { return; }
                e.preventDefault();
                confirmRejectModal.show();
            });

            const confirmRejectYes = document.getElementById('confirmRejectYes');
            if (confirmRejectYes) {
                confirmRejectYes.addEventListener('click', function () {
                    rejectConfirmed = true;
                    confirmRejectModal.hide();
                    rejectForm.submit();
                });
            }
        }

        function showRemarkError() {
            const remarkError = document.getElementById('remarkError');
            if (remarkError) {
                remarkError.style.display = 'block';
            }
            if (actionRemarkInput) {
                actionRemarkInput.classList.add('is-invalid');
            }
        }

        function clearRemarkError() {
            const remarkError = document.getElementById('remarkError');
            if (remarkError) {
                remarkError.style.display = 'none';
            }
            if (actionRemarkInput) {
                actionRemarkInput.classList.remove('is-invalid');
            }
        }

        function showRecommendError() {
            const recommendError = document.getElementById('recommendError');
            if (recommendError) {
                recommendError.style.display = 'block';
            }
        }

        function clearRecommendError() {
            const recommendError = document.getElementById('recommendError');
            if (recommendError) {
                recommendError.style.display = 'none';
            }
        }

        function showNotRecommendReasonError() {
            const reasonError = document.getElementById('notRecommendReasonError');
            if (reasonError) {
                reasonError.style.display = 'block';
            }
            if (notRecommendReasonInput) {
                notRecommendReasonInput.classList.add('is-invalid');
            }
        }

        function clearNotRecommendReasonError() {
            const reasonError = document.getElementById('notRecommendReasonError');
            if (reasonError) {
                reasonError.style.display = 'none';
            }
            if (notRecommendReasonInput) {
                notRecommendReasonInput.classList.remove('is-invalid');
            }
        }

        function showExtensionPaymentTypeError() {
            const extensionPaymentTypeError = document.getElementById('extensionPaymentTypeError');
            if (extensionPaymentTypeError) {
                extensionPaymentTypeError.style.display = 'block';
            }
            if (extensionPaymentTypeInput) {
                extensionPaymentTypeInput.classList.add('is-invalid');
            }
        }

        function clearExtensionPaymentTypeError() {
            const extensionPaymentTypeError = document.getElementById('extensionPaymentTypeError');
            if (extensionPaymentTypeError) {
                extensionPaymentTypeError.style.display = 'none';
            }
            if (extensionPaymentTypeInput) {
                extensionPaymentTypeInput.classList.remove('is-invalid');
            }
        }

        function clearAllErrors() {
            clearRemarkError();
            clearRecommendError();
            clearNotRecommendReasonError();
            clearExtensionPaymentTypeError();
        }

        if (actionRemarkInput) {
            actionRemarkInput.addEventListener('input', clearRemarkError);
        }

        if (notRecommendReasonInput) {
            notRecommendReasonInput.addEventListener('input', clearNotRecommendReasonError);
        }

        if (extensionPaymentTypeInput) {
            extensionPaymentTypeInput.addEventListener('change', clearExtensionPaymentTypeError);
        }

        const collapseDetails = document.getElementById('collapseDetails');
        const detailsChevron = document.getElementById('detailsChevron');

        function updateDetailsChevron() {
            if (!collapseDetails || !detailsChevron) {
                return;
            }

            const isOpen = collapseDetails.classList.contains('show');
            detailsChevron.classList.toggle('fa-chevron-up', isOpen);
            detailsChevron.classList.toggle('fa-chevron-down', !isOpen);
        }

        if (collapseDetails && detailsChevron) {
            updateDetailsChevron();
            collapseDetails.addEventListener('shown.bs.collapse', updateDetailsChevron);
            collapseDetails.addEventListener('hidden.bs.collapse', updateDetailsChevron);
        }

        @if (session('success'))
        const successModalElement = document.getElementById('operationSuccessModal');
        if (successModalElement) {
            const successModal = new bootstrap.Modal(successModalElement);
            successModal.show();
        }
        @endif
    </script>

</body>

</html>
