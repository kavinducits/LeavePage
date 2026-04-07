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
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
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
                        <a href="{{ request()->get('from') == 'accepted' ? route('ma.studyleave.extensions.accepted') : (request()->get('from') == 'submitted' ? route('ma.studyleave.extensions.submitted') : route('ma.studyleave.extensions')) }}" class="btn btn-outline-secondary">
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
                                                    <a href="{{ route('ma.show.studyleave.progressreport', ['progress_report_id' => $report->progress_report_id]) }}?from={{ $from ?? 'submitted' }}"
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
                            @if(!empty($extension->acad_est_head_remarks))
                            <div class="mb-0">
                                <label class="form-label fw-semibold">Any other remarks</label>
                                <div class="remarks-display rounded p-3 mb-0" style="white-space:pre-wrap">{{ $extension->acad_est_head_remarks }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if(($from ?? null) === 'accepted' && optional($extension)->hod_recommend !== null)
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('partials.popup_helpers')
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(function() {
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
        });

        const approveForm = document.getElementById('approveForm');
        const returnForm = document.getElementById('returnForm');
        const finalizeForm = document.getElementById('finalizeForm');
        const rejectForm = document.getElementById('rejectForm');
        const actionRemarkInput = document.getElementById('actionRemark');
        const extensionPaymentTypeInput = document.getElementById('ma_extension_payment_type');

        // Forward form
        if (approveForm) {
            approveForm.addEventListener('submit', function(e) {
                const remarkValue = actionRemarkInput ? actionRemarkInput.value.trim() : '';
                const extensionPaymentTypeValue = extensionPaymentTypeInput ? extensionPaymentTypeInput.value : '';

                clearAllErrors();

                if (!extensionPaymentTypeValue) {
                    e.preventDefault();
                    showExtensionPaymentTypeError();
                    return false;
                }

                document.getElementById('approveRemarkInput').value = remarkValue;
                document.getElementById('approveRecommendInput').value = '1';
                document.getElementById('approveExtensionPaymentTypeInput').value = extensionPaymentTypeValue;

                e.preventDefault();
                AppPopup.confirm({
                    title: 'Confirm Forward',
                    text: 'Are you sure you want to forward this extension request to Head of Academic Establishment?',
                    icon: 'question',
                    confirmButtonText: 'Yes, Forward'
                }).then(function(result) {
                    if (result && result.isConfirmed) {
                        approveForm.submit();
                    }
                });
            });
        }

        // Return form
        if (returnForm) {
            returnForm.addEventListener('submit', function(e) {
                const remarkValue = actionRemarkInput ? actionRemarkInput.value.trim() : '';
                clearAllErrors();

                if (remarkValue === '') {
                    e.preventDefault();
                    showRemarkError();
                    return false;
                }

                document.getElementById('returnRemarkInput').value = remarkValue;
                e.preventDefault();
                AppPopup.confirm({
                    title: 'Confirm Return',
                    text: 'Are you sure you want to return this extension request to the user?',
                    icon: 'warning',
                    confirmButtonText: 'Yes, Return'
                }).then(function(result) {
                    if (result && result.isConfirmed) {
                        returnForm.submit();
                    }
                });
            });
        }

        // Finalize extension form
        if (finalizeForm) {
            finalizeForm.addEventListener('submit', function (e) {
                e.preventDefault();
                AppPopup.confirm({
                    title: 'Confirm Finalize',
                    text: 'Are you sure you want to finalize this extension as Approved?',
                    icon: 'question',
                    confirmButtonText: 'Yes, Finalize'
                }).then(function(result) {
                    if (result && result.isConfirmed) {
                        finalizeForm.submit();
                    }
                });
            });
        }

        // Reject extension form
        if (rejectForm) {
            rejectForm.addEventListener('submit', function (e) {
                e.preventDefault();
                AppPopup.confirm({
                    title: 'Confirm Rejection',
                    text: 'Are you sure you want to reject this extension request?',
                    icon: 'warning',
                    confirmButtonText: 'Yes, Reject'
                }).then(function(result) {
                    if (result && result.isConfirmed) {
                        rejectForm.submit();
                    }
                });
            });
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
        }

        function clearNotRecommendReasonError() {
            const reasonError = document.getElementById('notRecommendReasonError');
            if (reasonError) {
                reasonError.style.display = 'none';
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
        AppPopup.success(@json(session('success')), 'Success', 1600);
        @endif
    </script>

</body>

</html>
