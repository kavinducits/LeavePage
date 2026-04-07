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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    @include('StudyLeave.study_leave_extension.partials.extension_summary_styles')
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

        @include('StudyLeave.study_leave_extension.partials.extension_summary_card', [
            'headerClass' => 'bg-primary text-white fw-semibold',
            'durationDays' => $durationDays,
            'durationMonths' => $durationMonths,
        ])

        <div class="card mb-4">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="fas fa-file-alt me-2"></i>Approved Progress Reports
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="approved-progress-reports-table" class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Due Date</th>
                                <th>Submitted Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($approvedProgressReports ?? collect()) as $report)
                                <tr>
                                    <td class="px-3">
                                        {{ $report->due_date ? \Carbon\Carbon::parse($report->due_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $report->submitted_date ? \Carbon\Carbon::parse($report->submitted_date)->format('M d, Y') : 'N/A' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ $report->status ?? 'Approved' }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('hod.show.studyleave.progressreport', $report->progress_report_id) }}?from={{ $from ?? 'submitted' }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No approved progress reports found for this study leave.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

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
                @if(!empty($extension->extension_ma_remarks))
                    <div class="mb-0">
                        <label class="form-label fw-semibold">MA Remarks</label>
                        <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $extension->extension_ma_remarks }}</div>
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
                            <input class="form-check-input" type="radio" name="acad_est_head_recommend_view" id="hodAcadEstRecommendViewYes"
                                {{ isset($extension->acad_est_head_recommend) && $extension->acad_est_head_recommend == 1 ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="hodAcadEstRecommendViewYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="acad_est_head_recommend_view" id="hodAcadEstRecommendViewNo"
                                {{ isset($extension->acad_est_head_recommend) && $extension->acad_est_head_recommend == 0 ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="hodAcadEstRecommendViewNo">No</label>
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

        @if(optional($extension)->extension_hod_recommend !== null)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white fw-semibold">
                <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">HOD Recommendation</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="extension_hod_recommend_view" id="hodRecommendReadonlyYes"
                                {{ $extension->extension_hod_recommend == 1 ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="hodRecommendReadonlyYes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="extension_hod_recommend_view" id="hodRecommendReadonlyNo"
                                {{ $extension->extension_hod_recommend == 0 ? 'checked' : '' }} disabled>
                            <label class="form-check-label" for="hodRecommendReadonlyNo">No</label>
                        </div>
                    </div>
                </div>
                @if(!empty($extension->extension_hod_remarks))
                <div class="mb-0">
                    <label class="form-label fw-semibold">HOD Remarks</label>
                    <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $extension->extension_hod_remarks }}</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- HOD Review Section -->
        @if(!isset($from) || $from != 'accepted')
        <form action="{{ route('hod.extension.approve', $extension->extension_id) }}" method="POST" id="hodReviewForm">
            @csrf

            <div class="card mt-4">
                <div class="card-header bg-primary text-white fw-semibold">
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
            const hasApprovedProgressReports = @json(($approvedProgressReports ?? collect())->count() > 0);

            if (hasApprovedProgressReports && $.fn.DataTable && $('#approved-progress-reports-table').length) {
                $('#approved-progress-reports-table').DataTable({
                    pageLength: 5,
                    lengthMenu: [[5, 10, 25, -1], [5, 10, 25, 'All']],
                    order: [[0, 'desc']],
                    language: {
                        search: 'Search:',
                        lengthMenu: 'Show _MENU_ entries',
                        info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                        infoEmpty: 'Showing 0 to 0 of 0 entries',
                        infoFiltered: '(filtered from _MAX_ total entries)',
                        paginate: {
                            first: 'First',
                            last: 'Last',
                            next: 'Next',
                            previous: 'Previous'
                        }
                    },
                    columnDefs: [
                        { orderable: false, targets: -1 }
                    ]
                });
            }

            const recommendRadios = document.querySelectorAll('input[name="hod_recommend"]');
            const remarksTextarea = document.getElementById('hod_remarks');

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
            const hodReviewForm = document.getElementById('hodReviewForm');
            hodReviewForm.addEventListener('submit', function(e) {
                const recommendValue = document.querySelector('input[name="hod_recommend"]:checked')?.value;

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
                AppPopup.confirm({
                    title: 'Confirm Forward',
                    text: 'Are you sure you want to submit this review and forward to Dean?',
                    icon: 'question',
                    confirmButtonText: 'Yes, Forward'
                }).then(function(result) {
                    if (result && result.isConfirmed) {
                        hodReviewForm.submit();
                    }
                });
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
        @include('hod.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('partials.popup_helpers')
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

</body>

</html>
