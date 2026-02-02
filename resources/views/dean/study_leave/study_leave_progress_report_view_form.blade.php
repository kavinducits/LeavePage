@extends('layouts.screen1')
<!-- Main content -->
<section class="content">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold">Progress Report Review</h2>
                <p class="text-muted mb-0">Reference No: {{ $progressReport->reference_no }}</p>
            </div>
            <a href="{{ route('dean.study.leave.index') }}" class="btn btn-outline-secondary">
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
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $progressReport->remark }}</p>
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
                                                    <div class="text-muted small">Click to view the submitted document</div>
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

        <!-- Registrar Review Section (Disabled/Read-only for Dean) -->
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

        <!-- HOD Review Section -->
        @if(isset($progressReport->hod_remarks) || isset($progressReport->hod_approval_status))
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-secondary text-white fw-semibold">
                <i class="fas fa-user-tie me-2"></i> HOD Review
            </div>
            <div class="card-body">
                @if(isset($progressReport->hod_approval_status))
                <div class="mb-3">
                    <label class="text-muted small mb-1">HOD Decision</label>
                    <div>
                        @if($progressReport->hod_approval_status == 1)
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Approved</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Not Approved</span>
                        @endif
                    </div>
                </div>
                @endif
                
                @if($progressReport->hod_remarks)
                <div class="mb-0">
                    <label class="text-muted small mb-1">HOD Remarks</label>
                    <div class="card bg-light">
                        <div class="card-body">
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $progressReport->hod_remarks }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

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
                        <strong>More Details - Original Study Leave Application</strong>
                    </button>
                </h2>
                <div id="collapseDetails" class="accordion-collapse collapse" aria-labelledby="headingDetails"
                    data-bs-parent="#detailsAccordion">
                    <div class="accordion-body">
                        <form method="POST" class="my-4">
                            @csrf
                            <!-- Include study leave forms with readonly -->
                            @php
                                $draft_study_leave = $progressReport;
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

    <!-- Include Dean Review Actions Section -->
    @include('dean.study_leave.progress_report_review_actions')

</section>
</div>

<!-- Footer -->

</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

</body>

</html>
