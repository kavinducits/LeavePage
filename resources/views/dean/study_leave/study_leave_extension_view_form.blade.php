<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dean Dashboard - Extension Review</title>
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
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('dean.partials.navbar')

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('dean.leave.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">Dean Dashboard</span>
            </a>
            <!-- Sidebar -->
            @php $pageName = 'Study Leave Extensions' @endphp
            @include('dean.partials.sidebar')
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @include('dean.partials.header')

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-dark">
                <i class="fas fa-calendar-plus me-2 text-primary"></i>Extension Request Review
            </h2>
            <p class="text-muted mb-0">Reference No: {{ $extension->reference_no }}</p>
        </div>
        <a href="{{ isset($from) && $from == 'accepted' ? route('dean.study.leave.extensions.accepted') : route('dean.study.leave.extensions') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @include('StudyLeave.study_leave_extension.partials.extension_summary_card', [
        'headerClass' => 'bg-primary text-white fw-semibold',
        'durationDays' => $durationDays,
        'durationMonths' => $durationMonths,
    ])

    <!-- Accordion for More Details -->
    <div class="accordion mb-4" id="detailsAccordion">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingDetails">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
                    <i class="fas fa-info-circle me-2"></i>
                    <span class="accordion-details-text">More Details - Original Study Leave Application</span>
                    <i id="detailsChevron" class="fas fa-chevron-down accordion-state-icon"></i>
                </button>
            </h2>
            <div id="collapseDetails" class="accordion-collapse collapse" aria-labelledby="headingDetails">
                <div class="accordion-body">
                    @php
                        $readonly = true;
                    @endphp
                    
                    @include('StudyLeave.basic_info_form')
                    @include('StudyLeave.details_form')
                    @include('StudyLeave.working_covering_persons_form')
                </div>
            </div>
        </div>
    </div>

    <!-- MA Review & Recommendation (Read-only) -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>MA Review & Recommendation
        </div>
        <div class="card-body">
            @if(isset($extension->ma_recommend) && $extension->ma_recommend == 0 && !empty($extension->ma_not_recommend_reason))
                <div class="mb-3">
                    <label class="form-label fw-semibold">Reason for Not Recommending</label>
                    <div class="card bg-light"><div class="card-body"><p class="mb-0" style="white-space: pre-wrap;">{{ $extension->ma_not_recommend_reason }}</p></div></div>
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

    <!-- HOD Academic Establishment Review & Recommendation (Read-only) -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>HOD Academic Establishment Review & Recommendation
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">HOD Academic Establishment Recommendation</label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="acad_est_head_recommend_view" id="deanAcadEstRecommendViewYes"
                            {{ isset($extension->acad_est_head_recommend) && $extension->acad_est_head_recommend == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="deanAcadEstRecommendViewYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="acad_est_head_recommend_view" id="deanAcadEstRecommendViewNo"
                            {{ isset($extension->acad_est_head_recommend) && $extension->acad_est_head_recommend == 0 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="deanAcadEstRecommendViewNo">No</label>
                    </div>
                </div>
                @if(!isset($extension->acad_est_head_recommend))
                    <div class="text-muted small mt-2">Not specified</div>
                @endif
            </div>
            @if(!empty($extension->acad_est_head_remarks))
                <div class="mb-0">
                    <label class="form-label fw-semibold">Remarks</label>
                    <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $extension->acad_est_head_remarks }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- HOD Review & Recommendation (Read-only) -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">HOD Recommendation</label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="extension_hod_recommend_view" id="deanHodRecommendViewYes"
                            {{ isset($extension->extension_hod_recommend) && $extension->extension_hod_recommend == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="deanHodRecommendViewYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="extension_hod_recommend_view" id="deanHodRecommendViewNo"
                            {{ isset($extension->extension_hod_recommend) && $extension->extension_hod_recommend == 0 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="deanHodRecommendViewNo">No</label>
                    </div>
                </div>
                @if(!isset($extension->extension_hod_recommend))
                    <div class="text-muted small mt-2">Not specified</div>
                @endif
            </div>
            @if(!empty($extension->extension_hod_remarks))
                <div class="mb-0">
                    <label class="form-label fw-semibold">HOD Remarks</label>
                    <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $extension->extension_hod_remarks }}</div>
                </div>
            @endif
        </div>
    </div>

    @if(optional($extension)->extension_dean_recommend !== null)
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>Dean Review & Recommendation
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">Dean Recommendation</label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="extension_dean_recommend_view" id="deanRecommendViewYes"
                            {{ $extension->extension_dean_recommend == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="deanRecommendViewYes">Yes</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="extension_dean_recommend_view" id="deanRecommendViewNo"
                            {{ $extension->extension_dean_recommend == 0 ? 'checked' : '' }} disabled>
                        <label class="form-check-label" for="deanRecommendViewNo">No</label>
                    </div>
                </div>
            </div>
            @if(!empty($extension->extension_dean_remarks))
            <div class="mb-0">
                <label class="form-label fw-semibold">Dean Remarks</label>
                <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $extension->extension_dean_remarks }}</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Dean Review Section -->
    @if(!isset($from) || $from != 'accepted')
    <form action="{{ route('dean.extension.approve', $extension->extension_id) }}" method="POST" id="deanReviewForm">
        @csrf
        
        <div class="card mt-4">
            <div class="card-header card-header-dark text-white fw-semibold">
                <i class="fas fa-clipboard-check me-2"></i>Dean Review & Recommendation
            </div>
            <div class="card-body">
                
                <!-- Question - Recommendation -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Is extension recommended?
                        <span class="text-danger">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="dean_recommend" id="recommendYes" value="1" required>
                        <label class="form-check-label" for="recommendYes">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="dean_recommend" id="recommendNo" value="0" required>
                        <label class="form-check-label" for="recommendNo">
                            No
                        </label>
                    </div>
                </div>

                <!-- Any other remarks -->
                <div class="mb-4">
                    <label for="dean_remarks" class="form-label fw-semibold">
                        Any other remarks
                    </label>
                    <textarea class="form-control" id="dean_remarks" name="dean_remarks" rows="3" 
                              placeholder="Add any additional comments or remarks (optional)"></textarea>
                </div>

            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-end align-items-center">
                    <div class="text-end">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center justify-content-end">
                                    <strong>Forward to Vice-Chancellor</strong>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>Submit Review & Forward to VC
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @else
    <div class="card mt-4">
        <div class="card-body">
            <div class="alert alert-success mb-0">
                <i class="fas fa-check-circle me-2"></i>
                This extension has already been forwarded by Dean.
            </div>
        </div>
    </div>
    @endif
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
                <p class="mb-0 fs-6">Are you sure you want to submit this review and forward to VC?</p>
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

<style>
.card-header-dark {
    background: linear-gradient(135deg, #212529 0%, #343a40 100%);
    color: white;
    border-bottom: 3px solid #0d6efd;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0d6efd;
}

.accordion-button:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

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

.btn-outline-secondary:hover {
    background-color: #6c757d;
    color: white;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recommendRadios = document.querySelectorAll('input[name="dean_recommend"]');
    const remarksTextarea = document.getElementById('dean_remarks');

    recommendRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === '0') {
                remarksTextarea.required = true;
            } else {
                remarksTextarea.required = false;
                remarksTextarea.classList.remove('is-invalid');
            }
        });
    });

    // Form validation
    let forwardConfirmed = false;

    const deanReviewForm = document.getElementById('deanReviewForm');
    const confirmForwardModal = new bootstrap.Modal(document.getElementById('confirmForwardModal'));
    deanReviewForm.addEventListener('submit', function(e) {
        if (forwardConfirmed) { return; }

        const recommendValue = document.querySelector('input[name="dean_recommend"]:checked')?.value;
        
        if (recommendValue === '0') {
            const remarks = remarksTextarea.value.trim();
            if (remarks === '') {
                e.preventDefault();
                remarksTextarea.classList.add('is-invalid');
                remarksTextarea.focus();
                return false;
            }
        }

        e.preventDefault();
        confirmForwardModal.show();
    });

    document.getElementById('confirmForwardYes').addEventListener('click', function () {
        forwardConfirmed = true;
        confirmForwardModal.hide();
        deanReviewForm.submit();
    });

    // Clear invalid state on input
    remarksTextarea.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });

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
});
</script>
            </section>
        </div>
        <!-- Footer -->
        @include('dean.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>

</html>
