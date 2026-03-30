<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Progress Report Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('StudyLeave.study_leave_progress_reports.partials.progress_summary_styles')
    <style>
        .progress-summary-card {
            border: 0;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: 0 0.75rem 1.75rem rgba(13, 110, 253, 0.12);
        }

        .progress-summary-card .card-header {
            padding: 1rem 1.25rem;
        }

        .progress-summary-body {
            background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
            padding: 1.5rem;
        }

        .progress-identity-block {
            padding: 1rem 1.25rem;
            border: 1px solid #dbe7ff;
            border-radius: 0.9rem;
            background: #ffffff;
            height: 100%;
        }

        .progress-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 0.35rem;
        }

        .progress-primary-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0d6efd;
        }

        .progress-secondary-value {
            font-size: 1rem;
            font-weight: 600;
            color: #212529;
        }

        .progress-meta-text {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .progress-date-card {
            border: 1px solid #e5e7eb;
            border-radius: 0.9rem;
            background: #ffffff;
            padding: 1rem 1.1rem;
            height: 100%;
        }

        .progress-date-card.is-due {
            border-left: 4px solid #ffc107;
        }

        .progress-date-card.is-submitted {
            border-left: 4px solid #198754;
        }

        .progress-metric-card {
            border-radius: 0.9rem;
            background: linear-gradient(135deg, #0d6efd 0%, #3d8bfd 100%);
            color: #ffffff;
            padding: 1rem 1.1rem;
            height: 100%;
        }

        .progress-metric-card .metric-value {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .progress-remarks-panel,
        .progress-doc-panel {
            border: 1px solid #e9ecef;
            border-radius: 0.9rem;
            background: #ffffff;
            padding: 1rem 1.1rem;
            height: 100%;
        }

        .progress-remarks-text {
            white-space: pre-wrap;
            line-height: 1.65;
            color: #343a40;
            margin-bottom: 0;
        }

        .accordion-button.collapsed:hover {
            background-color: #e8f4f8;
            color: #0056b3;
            transition: all 0.3s ease;
        }
        .accordion-button.collapsed:hover i {
            transform: scale(1.1);
            transition: transform 0.3s ease;
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
            @php $pageName = 'Study Leave Progress' @endphp
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
                            <h2 class="mb-0 fw-bold">Progress Report Review</h2>
                            <p class="text-muted mb-0">Reference No: {{ $progressReport->reference_no }}</p>
                        </div>
                        <a href="{{ request()->get('from') == 'accepted' ? route('ma.studyleave.progress.accepted') : (request()->get('from') == 'submitted' ? route('ma.studyleave.progress.submitted') : route('ma.studyleave.progress')) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @include('StudyLeave.study_leave_progress_reports.partials.progress_summary_card', [
                        'progressItem' => $progressReport,
                        'headerClass' => 'bg-primary text-white fw-semibold',
                        'documentRouteName' => 'ma.serveProgressReport',
                    ])

                    <!-- Approved Progress Reports Section -->
                    @if ($approvedReports->count() > 0)
                        <div class="card mb-4 shadow-sm">
                            <div class="card-header bg-success text-white fw-semibold">
                                <i class="fas fa-check-circle me-2"></i> Previously Approved Progress Reports
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Due Date</th>
                                                <th>Submitted Date</th>
                                                <th>Status</th>
                                                <th class="text-center">Document</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($approvedReports as $report)
                                                <tr>
                                                    <td>{{ \Carbon\Carbon::parse($report->due_date)->format('d M Y') }}
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($report->submitted_date)->format('d M Y') }}
                                                    </td>
                                                    <td><span class="badge bg-success">{{ $report->status }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if ($report->document_path)
                                                            <a href="{{ asset('storage/' . $report->document_path) }}"
                                                                target="_blank" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-file-pdf me-1"></i>View
                                                            </a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                            $draft_study_leave = $progressReport; // Use progress report data
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

                            @if(!empty($progressReport->ma_remark) || !empty($progressReport->remark))
                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white fw-semibold">
                                    <i class="fas fa-clipboard-check me-2"></i>MA Review
                                </div>
                                <div class="card-body">
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold">MA Remarks</label>
                                        <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $progressReport->ma_remark ?? $progressReport->remark }}</div>
                                    </div>
                                </div>
                            </div>
                            @endif

                @if(optional($progressReport)->registrar_approval_status !== null)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white fw-semibold">
                        <i class="fas fa-clipboard-check me-2"></i>Head Of Academic Establishment Review
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Registrar Decision</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="registrar_decision_view" id="registrarYes" {{ $progressReport->registrar_approval_status == 1 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="registrarYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="registrar_decision_view" id="registrarNo" {{ $progressReport->registrar_approval_status == 0 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="registrarNo">No</label>
                                </div>
                            </div>
                            @if(!isset($progressReport->registrar_approval_status))
                            <div class="text-muted small mt-2">Not specified</div>
                            @endif
                        </div>
                        @if(!empty($progressReport->registrar_remarks))
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Registrar Remarks</label>
                            <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $progressReport->registrar_remarks }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if(optional($progressReport)->hod_approval_status !== null)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white fw-semibold">
                        <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">HOD Decision</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hod_decision_view" id="hodYes" {{ $progressReport->hod_approval_status == 1 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="hodYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="hod_decision_view" id="hodNo" {{ $progressReport->hod_approval_status == 0 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="hodNo">No</label>
                                </div>
                            </div>
                            @if(!isset($progressReport->hod_approval_status))
                            <div class="text-muted small mt-2">Not specified</div>
                            @endif
                        </div>
                        @if(!empty($progressReport->hod_remarks))
                        <div class="mb-0">
                            <label class="form-label fw-semibold">HOD Remarks</label>
                            <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $progressReport->hod_remarks }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if(optional($progressReport)->dean_approval_status !== null)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white fw-semibold">
                        <i class="fas fa-clipboard-check me-2"></i>Dean Review & Recommendation
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dean Decision</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="dean_decision_view" id="deanYes" {{ $progressReport->dean_approval_status == 1 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="deanYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="dean_decision_view" id="deanNo" {{ $progressReport->dean_approval_status == 0 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="deanNo">No</label>
                                </div>
                            </div>
                            @if(!isset($progressReport->dean_approval_status))
                            <div class="text-muted small mt-2">Not specified</div>
                            @endif
                        </div>
                        @if(!empty($progressReport->dean_remarks))
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Dean Remarks</label>
                            <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $progressReport->dean_remarks }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                @if(optional($progressReport)->vc_approval_status !== null)
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white fw-semibold">
                        <i class="fas fa-clipboard-check me-2"></i>VC Review & Recommendation
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">VC Decision</label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vc_decision_view" id="vcYes" {{ $progressReport->vc_approval_status == 1 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="vcYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="vc_decision_view" id="vcNo" {{ $progressReport->vc_approval_status == 0 ? 'checked' : '' }} disabled>
                                    <label class="form-check-label" for="vcNo">No</label>
                                </div>
                            </div>
                            @if(!isset($progressReport->vc_approval_status))
                            <div class="text-muted small mt-2">Not specified</div>
                            @endif
                        </div>
                        @if(!empty($progressReport->vc_remarks))
                        <div class="mb-0">
                            <label class="form-label fw-semibold">VC Remarks</label>
                            <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $progressReport->vc_remarks }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Action Section -->
                    @if((int) ($progressReport->approval_status_id ?? 0) === 4)
                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white fw-semibold">
                            <i class="fas fa-tasks me-2"></i>Review Actions
                        </div>
                        <div class="card-body">
                            <!-- Normal review actions when not in editing state -->
                            <div class="mb-3">
                                <label for="actionRemark" class="form-label fw-semibold">MA Remarks</label>
                                <textarea class="form-control" id="actionRemark" name="remark" rows="4"
                                    placeholder="Add your comments or remarks about this progress report"></textarea>
                                <div id="remarkError" class="form-text text-danger" style="display: none;">
                                    Remarks are required when returning a progress report.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-start">
                                <form id="returnForm"
                                    action="{{ route('ma.progressreport.return', $progressReport->progress_report_id) }}"
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
                                                    <div class="text-muted small">Forwarding is disabled until a head is
                                                        active.</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <form id="approveForm"
                                        action="{{ route('ma.progressreport.approve', $progressReport->progress_report_id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" id="approveRemarkInput" name="remark" value="">
                                        <button type="submit" class="btn btn-success btn-lg w-100"
                                            {{ empty($departmentHead) ? 'disabled' : '' }}>
                                            <i class="fas fa-forward me-2"></i>Forward to HOD
                                        </button>
                                    </form>
                                </div>
                            </div>
                    </div>
                    @elseif((int) ($progressReport->approval_status_id ?? 0) === 8)
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-gavel me-2"></i>Finalize Progress Report Decision
                        </div>
                        <div class="card-body d-flex justify-content-end gap-2">
                            <form id="finalizeForm" action="{{ route('ma.progressreport.finalize', $progressReport->progress_report_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check-circle me-2"></i>Finalize as Approved
                                </button>
                            </form>
                            <form id="rejectForm" action="{{ route('ma.progressreport.reject', $progressReport->progress_report_id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-times-circle me-2"></i>Finalize as Rejected
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="card mb-4">
                        <div class="card-header bg-secondary text-white fw-semibold">
                            <i class="fas fa-lock me-2"></i>Review Actions
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                This progress report is already processed by MA. Further review actions are disabled.
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

    <!-- Return Confirmation Modal -->
    <div class="modal fade" id="returnConfirmModal" tabindex="-1" aria-labelledby="returnConfirmModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="returnConfirmModalLabel">Confirm Return</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to return this progress report to the user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="returnConfirmNo"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="returnConfirmYes">Yes, Return</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Forward Confirmation Modal -->
    <div class="modal fade" id="forwardConfirmModal" tabindex="-1" aria-labelledby="forwardConfirmModalLabel"
        aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="forwardConfirmModalLabel">Confirm Forward</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to forward this progress report to HOD Academic Establishment?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="forwardConfirmNo"
                        data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success" id="forwardConfirmYes">Yes, Forward</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Finalize Confirmation Modal -->
    <div class="modal fade" id="finalizeConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Confirm Finalize</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to finalize this progress report as Approved?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-success" id="finalizeConfirmYes">Yes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Confirmation Modal -->
    <div class="modal fade" id="rejectConfirmModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Rejection</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-0">Are you sure you want to finalize this progress report as Rejected?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                    <button type="button" class="btn btn-danger" id="rejectConfirmYes">Yes</button>
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
        // Form validation and submission handling
        const approveForm = document.getElementById('approveForm');
        const returnForm = document.getElementById('returnForm');
        const actionRemark = document.getElementById('actionRemark');
        const approveRemarkInput = document.getElementById('approveRemarkInput');
        const returnRemarkInput = document.getElementById('returnRemarkInput');
        const returnConfirmModalEl = document.getElementById('returnConfirmModal');
        const returnConfirmYesBtn = document.getElementById('returnConfirmYes');
        const returnConfirmNoBtn = document.getElementById('returnConfirmNo');
        const forwardConfirmModalEl = document.getElementById('forwardConfirmModal');
        const forwardConfirmYesBtn = document.getElementById('forwardConfirmYes');
        const forwardConfirmNoBtn = document.getElementById('forwardConfirmNo');
        const finalizeForm = document.getElementById('finalizeForm');
        const rejectForm = document.getElementById('rejectForm');
        const finalizeConfirmYesBtn = document.getElementById('finalizeConfirmYes');
        const rejectConfirmYesBtn = document.getElementById('rejectConfirmYes');
        let forwardConfirmed = false;

        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
            const remarkValue = document.getElementById('actionRemark').value.trim();
            document.getElementById('approveRemarkInput').value = remarkValue;

            clearRemarkError();

            if (!forwardConfirmed) {
                e.preventDefault();
                const forwardModal = new bootstrap.Modal(document.getElementById('forwardConfirmModal'));
                forwardModal.show();
                return false;
            }
        });
        }

        if (forwardConfirmYesBtn) {
            forwardConfirmYesBtn.addEventListener('click', function() {
            forwardConfirmed = true;
            const modal = bootstrap.Modal.getInstance(document.getElementById('forwardConfirmModal'));
            if (modal) {
                modal.hide();
            }
            if (approveForm) {
                approveForm.submit();
            }
        });
        }

        if (forwardConfirmNoBtn) {
            forwardConfirmNoBtn.addEventListener('click', function() {
            forwardConfirmed = false;
        });
        }

        if (forwardConfirmModalEl) {
            forwardConfirmModalEl.addEventListener('hidden.bs.modal', function() {
            forwardConfirmed = false;
        });
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

        let returnConfirmed = false;

        if (returnForm) {
            returnForm.addEventListener('submit', function(e) {
            const remarkValue = document.getElementById('actionRemark').value.trim();

            clearRemarkError();

            if (remarkValue === '') {
                e.preventDefault();
                showRemarkError();
                return false;
            }

            document.getElementById('returnRemarkInput').value = remarkValue;

            if (!returnConfirmed) {
                e.preventDefault();
                const returnModal = new bootstrap.Modal(document.getElementById('returnConfirmModal'));
                returnModal.show();
                return false;
            }
        });
        }

        if (returnConfirmYesBtn) {
            returnConfirmYesBtn.addEventListener('click', function() {
            returnConfirmed = true;
            const modal = bootstrap.Modal.getInstance(document.getElementById('returnConfirmModal'));
            if (modal) {
                modal.hide();
            }
            document.getElementById('returnForm').submit();
        });
        }

        if (returnConfirmNoBtn) {
            returnConfirmNoBtn.addEventListener('click', function() {
            returnConfirmed = false;
        });
        }

        if (returnConfirmModalEl) {
            returnConfirmModalEl.addEventListener('hidden.bs.modal', function() {
            returnConfirmed = false;
        });
        }

        function showRemarkError() {
            document.getElementById('remarkError').style.display = 'block';
            document.getElementById('actionRemark').classList.add('is-invalid');
        }

        function clearRemarkError() {
            document.getElementById('remarkError').style.display = 'none';
            document.getElementById('actionRemark').classList.remove('is-invalid');
        }

        if (actionRemark) {
            actionRemark.addEventListener('input', clearRemarkError);
        }

        let finalizeConfirmed = false;
        if (finalizeForm) {
            finalizeForm.addEventListener('submit', function (e) {
                if (finalizeConfirmed) { return; }
                e.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('finalizeConfirmModal'));
                modal.show();
            });
        }

        if (finalizeConfirmYesBtn) {
            finalizeConfirmYesBtn.addEventListener('click', function () {
                finalizeConfirmed = true;
                const modal = bootstrap.Modal.getInstance(document.getElementById('finalizeConfirmModal'));
                if (modal) {
                    modal.hide();
                }
                finalizeForm.submit();
            });
        }

        let rejectConfirmed = false;
        if (rejectForm) {
            rejectForm.addEventListener('submit', function (e) {
                if (rejectConfirmed) { return; }
                e.preventDefault();
                const modal = new bootstrap.Modal(document.getElementById('rejectConfirmModal'));
                modal.show();
            });
        }

        if (rejectConfirmYesBtn) {
            rejectConfirmYesBtn.addEventListener('click', function () {
                rejectConfirmed = true;
                const modal = bootstrap.Modal.getInstance(document.getElementById('rejectConfirmModal'));
                if (modal) {
                    modal.hide();
                }
                rejectForm.submit();
            });
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
