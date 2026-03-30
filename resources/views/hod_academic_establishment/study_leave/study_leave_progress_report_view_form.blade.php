<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HOD Academic Establishment Dashboard - Progress Report Review</title>
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
        @include('hod_academic_establishment.partials.navbar')
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="{{ route('hod.leave.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">HOD Academic Establishment Dashboard</span>
            </a>
            @php $pageName = 'Study Leave Progress' @endphp
            @include('hod_academic_establishment.partials.sidebar')
        </aside>
        <div class="content-wrapper">
            @include('hod_academic_establishment.partials.header')
            <section class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0 fw-bold">Progress Report Review</h2>
                            <p class="text-muted mb-0">Reference No: {{ $progressReport->reference_no }}</p>
                        </div>
                        <a href="{{ isset($from) && $from === 'accepted' ? route('hodacademicestablishment.studyLeaveProgress.accepted') : route('hodacademicestablishment.studyLeaveProgress') }}" class="btn btn-outline-secondary">
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
                        <i id="detailsChevron" class="fas fa-chevron-down accordion-state-icon"></i>
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

    @if(!empty($progressReport->ma_remark))
    <div class="card mb-4">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>MA Review
        </div>
        <div class="card-body">
            <div class="mb-0">
                <label class="form-label fw-semibold">MA Remarks</label>
                <div class="remarks-display rounded p-3 mb-0" style="white-space: pre-wrap;">{{ $progressReport->ma_remark }}</div>
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
                <label class="form-label fw-semibold">Decision</label>
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

    @if(!isset($from) || $from !== 'accepted')
    <!-- Include Review Actions Section -->
    @include('hod_academic_establishment.study_leave.progress_report_review_actions')
    @endif

                </div>
            </section>
        </div>
        @include('hod_academic_establishment.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
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
    </script>

</body>

</html>
