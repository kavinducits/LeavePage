<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HOD Dashboard - Progress Report Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('StudyLeave.study_leave_progress_reports.partials.progress_summary_styles')
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
        @include('hod.partials.navbar')
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('hod.leave.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">HOD Dashboard</span>
            </a>
            @php $pageName = 'Study Leave Progress' @endphp
            @include('hod.partials.sidebar')
        </aside>
        <div class="content-wrapper">
            @include('hod.partials.header')
            <section class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0 fw-bold">Progress Report Review</h2>
                            <p class="text-muted mb-0">Reference No: {{ $progressReport->reference_no }}</p>
                        </div>
                        <a href="{{ isset($from) && $from === 'accepted' ? route('hod.study.leave.progress.accepted') : route('hod.study.leave.progress') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                        <td>{{ \Carbon\Carbon::parse($report->due_date)->format('d M Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($report->submitted_date)->format('d M Y') }}</td>
                                        <td><span class="badge bg-success">{{ $report->status }}</span></td>
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
                        data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
                        <i class="fas fa-info-circle me-2"></i>
                        <span class="accordion-details-text">More Details - Original Study Leave Application</span>
                    </button>
                </h2>
                <div id="collapseDetails" class="accordion-collapse collapse" aria-labelledby="headingDetails"
                    data-parent="#detailsAccordion">
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
    </div>

    <!-- Registrar Review Section (Disabled/Read-only for HOD) -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>Registrar Review & Recommendation (For Information)
        </div>
        <div class="card-body bg-light">
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> This section shows the Registrar's review. You cannot modify these fields.
            </div>

            @if ($progressReport->registrar_approval_status)
                <!-- Recommendation -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Leave is recommended
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="registrar_recommendation_disabled" value="yes" 
                            {{ $progressReport->registrar_approval_status == 1 ? 'checked' : '' }} disabled>
                        <label class="form-check-label text-muted">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="registrar_recommendation_disabled" value="no" 
                            {{ $progressReport->registrar_approval_status == 2 ? 'checked' : '' }} disabled>
                        <label class="form-check-label text-muted">
                            No
                        </label>
                    </div>
                </div>

                <!-- If not recommended -->
                @if ($progressReport->registrar_not_approve_reason)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Reason for not recommending
                        </label>
                        <div class="card bg-white">
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $progressReport->registrar_not_approve_reason }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Any other remarks -->
                @if ($progressReport->registrar_remarks)
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Registrar Remarks
                        </label>
                        <div class="card bg-white">
                            <div class="card-body">
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $progressReport->registrar_remarks }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Registrar has not yet reviewed this progress report.
                </div>
            @endif
        </div>
    </div>

    @if(optional($progressReport)->hod_approval_status !== null)
    <div class="card mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">HOD Decision</label>
                <div>
                    @if($progressReport->hod_approval_status == 1)
                        <span class="badge bg-success fs-6"><i class="fas fa-check-circle me-1"></i>Approved</span>
                    @else
                        <span class="badge bg-danger fs-6"><i class="fas fa-times-circle me-1"></i>Not Approved</span>
                    @endif
                </div>
            </div>
            @if(!empty($progressReport->hod_not_approve_reason))
            <div class="mb-3">
                <label class="form-label fw-semibold">Reason for Not Approving</label>
                <div class="card bg-light"><div class="card-body"><p class="mb-0" style="white-space: pre-wrap;">{{ $progressReport->hod_not_approve_reason }}</p></div></div>
            </div>
            @endif
            @if(!empty($progressReport->hod_remarks))
            <div class="mb-0">
                <label class="form-label fw-semibold">HOD Remarks</label>
                <div class="alert alert-dark mb-0"><div style="white-space: pre-wrap;">{{ $progressReport->hod_remarks }}</div></div>
            </div>
            @endif
        </div>
    </div>
    @endif

    @if(!isset($from) || $from !== 'accepted')
    <!-- Include HOD Review Actions Section -->
    @include('hod.study_leave.progress_report_review_actions')
    @endif

                </div>
            </section>
        </div>
        @include('hod.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
    // Form validation and submission handling
    document.getElementById('approveForm').addEventListener('submit', function(e) {
        const remarkValue = document.getElementById('actionRemark').value.trim();
        document.getElementById('approveRemarkInput').value = remarkValue;

        clearRemarkError();

        if (!confirm('Are you sure you want to forward this progress report to the Department Head?')) {
            e.preventDefault();
            return false;
        }
    });

    document.getElementById('returnForm').addEventListener('submit', function(e) {
        const remarkValue = document.getElementById('actionRemark').value.trim();

        clearRemarkError();

        if (remarkValue === '') {
            e.preventDefault();
            showRemarkError();
            return false;
        }

        document.getElementById('returnRemarkInput').value = remarkValue;

        if (!confirm('Are you sure you want to return this progress report to the user?')) {
            e.preventDefault();
            return false;
        }
    });

    function showRemarkError() {
        document.getElementById('remarkError').style.display = 'block';
        document.getElementById('actionRemark').classList.add('is-invalid');
    }

    function clearRemarkError() {
        document.getElementById('remarkError').style.display = 'none';
        document.getElementById('actionRemark').classList.remove('is-invalid');
    }

    document.getElementById('actionRemark').addEventListener('input', clearRemarkError);
</script>
</body>

</html>
