<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HOD Dashboard - Extension Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('hod.partials.navbar')

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('hod.leave.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">HOD Dashboard</span>
            </a>
            <!-- Sidebar -->
            @php $pageName = 'Study Leave Extensions' @endphp
            @include('hod.partials.sidebar')
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @include('hod.partials.header')

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-calendar-plus me-2 text-primary"></i>Extension Request Review
                </h2>
                <p class="text-muted mb-0">Reference No: {{ $extension->reference_no }}</p>
            </div>
            <a href="{{ isset($from) && $from == 'accepted' ? route('hod.study.leave.extensions.accepted') : route('hod.study.leave.extensions') }}" class="btn btn-outline-secondary">
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

        <!-- Extension Summary Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header card-header-dark text-white fw-semibold">
                <i class="fas fa-calendar-plus me-2"></i> Extension Request Details
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Study Leave Reference Number</label>
                            <div class="fw-bold fs-5 text-primary">{{ $extension->reference_no }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Employee</label>
                            <div class="fw-bold">{{ $extension->name_with_initials }}</div>
                            <div class="text-muted small">{{ $extension->empno }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Original End Date</label>
                            <div class="fw-semibold text-danger">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($extension->old_end_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">New End Date</label>
                            <div class="fw-semibold text-success">
                                <i class="far fa-calendar-check me-1"></i>
                                {{ \Carbon\Carbon::parse($extension->new_end_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Extension Duration</label>
                            <div class="fw-bold text-primary fs-5">
                                <i class="fas fa-clock me-1"></i>{{ $durationDays }} days ({{ $durationMonths }} months)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-0">
                            <label class="text-muted small mb-1">Reason for Extension</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $extension->reason_for_extension }}
                                    </p>
                                </div>
                            </div>
                        </div>
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
                <div class="mb-3">
                    <label class="form-label fw-semibold">MA Recommendation</label>
                    <div>
                        @if(isset($extension->ma_recommend))
                            @if($extension->ma_recommend == 1)
                                <span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i>Recommended</span>
                            @else
                                <span class="badge bg-danger fs-6"><i class="fas fa-times-circle me-1"></i>Not Recommended</span>
                            @endif
                        @else
                            <span class="badge bg-secondary fs-6">Not specified</span>
                        @endif
                    </div>
                </div>
                @if(isset($extension->ma_recommend) && $extension->ma_recommend == 0 && !empty($extension->ma_not_recommend_reason))
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Not Recommending</label>
                        <div class="card bg-light"><div class="card-body"><p class="mb-0" style="white-space: pre-wrap;">{{ $extension->ma_not_recommend_reason }}</p></div></div>
                    </div>
                @endif
                @if(!empty($extension->ma_remarks))
                    <div class="mb-0">
                        <label class="form-label fw-semibold">MA Remarks</label>
                        <div class="alert alert-info mb-0"><div style="white-space: pre-wrap;">{{ $extension->ma_remarks }}</div></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- HOD Academic Establishment Review & Recommendation (Read-only) -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white fw-semibold">
                <i class="fas fa-clipboard-check me-2"></i>HOD Academic Establishment Review & Recommendation
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">HOD Academic Establishment Recommendation</label>
                    <div>
                        @if(isset($extension->acad_est_head_recommend))
                            @if($extension->acad_est_head_recommend == 1)
                                <span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i>Recommended</span>
                            @else
                                <span class="badge bg-danger fs-6"><i class="fas fa-times-circle me-1"></i>Not Recommended</span>
                            @endif
                        @else
                            <span class="badge bg-secondary fs-6">Not specified</span>
                        @endif
                    </div>
                </div>
                @if(isset($extension->acad_est_head_recommend) && $extension->acad_est_head_recommend == 0 && !empty($extension->acad_est_head_not_recommend_reason))
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Reason for Not Recommending</label>
                        <div class="card bg-light"><div class="card-body"><p class="mb-0" style="white-space: pre-wrap;">{{ $extension->acad_est_head_not_recommend_reason }}</p></div></div>
                    </div>
                @endif
                @if(!empty($extension->acad_est_head_remarks))
                    <div class="mb-0">
                        <label class="form-label fw-semibold">Remarks</label>
                        <div class="alert alert-secondary mb-0"><div style="white-space: pre-wrap;">{{ $extension->acad_est_head_remarks }}</div></div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Accordion for More Details -->
        <div class="accordion mb-4" id="detailsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingDetails">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>More Details - Original Study Leave Application</strong>
                    </button>
                </h2>
                <div id="collapseDetails" class="accordion-collapse collapse" aria-labelledby="headingDetails">
                    <div class="accordion-body">
                        <!-- Include study leave forms with readonly -->
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

        <!-- HOD Review Section -->
        @if(!isset($from) || $from != 'accepted')
        <form action="{{ route('hod.extension.approve', $extension->extension_id) }}" method="POST" id="hodReviewForm">
            @csrf

            <div class="card mt-4">
                <div class="card-header card-header-dark text-white fw-semibold">
                    <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
                </div>
                <div class="card-body">

                    <!-- Question - Recommendation -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Extension is recommended
                            <span class="text-danger">*</span>
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hod_recommend" id="recommendYes"
                                value="1" required>
                            <label class="form-check-label" for="recommendYes">
                                Yes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hod_recommend" id="recommendNo"
                                value="0" required>
                            <label class="form-check-label" for="recommendNo">
                                No
                            </label>
                        </div>
                    </div>

                    <!-- Conditional: If not recommended -->
                    <div class="mb-4" id="notRecommendReasonDiv" style="display: none;">
                        <label for="hod_not_recommend_reason" class="form-label fw-semibold">
                            If not recommended, please give reasons
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="hod_not_recommend_reason" name="hod_not_recommend_reason" rows="4"
                            placeholder="Please provide detailed reasons for not recommending this extension"></textarea>
                        <div class="invalid-feedback">
                            Please provide reasons for not recommending.
                        </div>
                    </div>

                    <!-- Any other remarks -->
                    <div class="mb-4">
                        <label for="hod_remarks" class="form-label fw-semibold">
                            Any other remarks
                        </label>
                        <textarea class="form-control" id="hod_remarks" name="hod_remarks" rows="3"
                            placeholder="Add any additional comments or remarks (optional)"></textarea>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-end align-items-center">
                        <div class="text-end">
                            @if (isset($deanInfo))
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <strong>Forward to,&nbsp;</strong>
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $deanInfo->title ?? 'Dean' }}&nbsp;{{ $deanInfo->initials ?? '' }}&nbsp;{{ $deanInfo->last_name ?? '' }}
                                                </div>
                                                <div class="text-muted small">{{ $deanInfo->faculty_name ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card mb-2 border-warning">
                                    <div class="card-body py-2">
                                        <div class="text-danger text-end">
                                            <strong>No active Dean found</strong>
                                            <div class="text-muted small">Forwarding is disabled until a Dean is active.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn"
                                {{ empty($deanInfo) ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane me-2"></i>Submit Review & Forward to Dean
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
                    This extension has already been forwarded by HOD.
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
                <p class="mb-0 fs-6">Are you sure you want to submit this review and forward to Dean?</p>
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

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        #notRecommendReasonDiv.show {
            display: block !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide not recommend reason field
            const recommendRadios = document.querySelectorAll('input[name="hod_recommend"]');
            const notRecommendDiv = document.getElementById('notRecommendReasonDiv');
            const notRecommendTextarea = document.getElementById('hod_not_recommend_reason');

            recommendRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === '0') {
                        notRecommendDiv.style.display = 'block';
                        notRecommendDiv.classList.add('show');
                        notRecommendTextarea.required = true;
                    } else {
                        notRecommendDiv.style.display = 'none';
                        notRecommendDiv.classList.remove('show');
                        notRecommendTextarea.required = false;
                        notRecommendTextarea.value = '';
                    }
                });
            });

            // Form validation
            let forwardConfirmed = false;

            const hodReviewForm = document.getElementById('hodReviewForm');
            const confirmForwardModal = new bootstrap.Modal(document.getElementById('confirmForwardModal'));
            hodReviewForm.addEventListener('submit', function(e) {
                if (forwardConfirmed) { return; }

                const recommendValue = document.querySelector('input[name="hod_recommend"]:checked')?.value;

                if (recommendValue === '0') {
                    const reason = notRecommendTextarea.value.trim();
                    if (reason === '') {
                        e.preventDefault();
                        notRecommendTextarea.classList.add('is-invalid');
                        notRecommendTextarea.focus();
                        return false;
                    }
                }

                e.preventDefault();
                confirmForwardModal.show();
            });

            document.getElementById('confirmForwardYes').addEventListener('click', function () {
                forwardConfirmed = true;
                confirmForwardModal.hide();
                hodReviewForm.submit();
            });

            // Clear invalid state on input
            notRecommendTextarea.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
            </section>
        </div>
        <!-- Footer -->
        @include('hod.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>

</html>
