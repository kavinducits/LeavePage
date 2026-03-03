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
    <style>
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
            font-size: 0.8rem;
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
                        <a href="{{ request()->get('from') == 'accepted' ? route('ma.studyleave.progress.accepted') : route('ma.studyleave.progress') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
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

                    <!-- Progress Report Summary Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-file-alt me-2"></i> Progress Report Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Study Leave Reference Number</label>
                                        <div class="fw-bold fs-5 text-primary">{{ $progressReport->reference_no }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Employee</label>
                                        <div class="fw-bold">{{ $progressReport->name_with_initials }}</div>
                                        <div class="text-muted small">{{ $progressReport->empno }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Due Date</label>
                                        <div class="fw-semibold text-warning">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($progressReport->due_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Submitted Date</label>
                                        <div class="fw-semibold text-success">
                                            <i class="far fa-calendar-check me-1"></i>
                                            {{ $progressReport->submitted_date ? \Carbon\Carbon::parse($progressReport->submitted_date)->format('d M Y') : 'Not Submitted' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if ($progressReport->remark)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="text-muted small mb-1">Employee Remarks</label>
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <p class="mb-0" style="white-space: pre-wrap;">
                                                        {{ $progressReport->remark }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($progressReport->document_path)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-0">
                                            <label class="text-muted small mb-1">Progress Report Document</label>
                                            <div class="card border-primary">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file-pdf fa-3x text-danger me-3"></i>
                                                            <div>
                                                                <div class="fw-bold">Progress Report PDF</div>
                                                                <div class="text-muted small">Click to view the
                                                                    submitted document</div>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('ma.serveProgressReport', ['filename' => basename($progressReport->document_path)]) }}"
                                                            target="_blank" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye me-1"></i>View PDF
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

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
    </script>
</body>

</html>
