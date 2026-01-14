@extends('layouts.screen1')
<!-- Main content -->
<section class="content">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold">Progress Report Review</h2>
                <p class="text-muted mb-0">Reference No: {{ $progressReport->reference_no }}</p>
            </div>
            <a href="{{ route('hod.study.leave.index') }}" class="btn btn-outline-secondary">
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
                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $progressReport->remark }}
                                        </p>
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
                                                    <div class="text-muted small">Click to view the submitted document
                                                    </div>
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

    <!-- Action Section -->
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-tasks me-2"></i>Review Actions
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label for="actionRemark" class="form-label fw-semibold">HOD Remarks</label>
                <textarea class="form-control" id="actionRemark" name="remark" rows="4"
                    placeholder="Add your comments or remarks about this progress report"></textarea>
                <div id="remarkError" class="form-text text-danger" style="display: none;">
                    Remarks are required when approve a progress report.
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-start">
                <form id="returnForm"
                    action="{{ route('ma.progressreport.return', $progressReport->progress_report_id) }}"
                    method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" id="returnRemarkInput" name="remark" value="">

                </form>

                <div class="text-right">
                    <form id="approveForm"
                        action="{{ route('ma.progressreport.approve', $progressReport->progress_report_id) }}"
                        method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" id="approveRemarkInput" name="remark" value="">
                        <button type="submit" class="btn btn-success btn-lg"
                            {{ empty($departmentHead) ? 'disabled' : '' }}>
                            <i class="fas fa-forward me-2"></i>Approve
                        </button>
                    </form>

                    @if (isset($departmentHead))
                        <div class="card mt-2" style="min-width: 260px;">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <strong>Forward to,&nbsp;</strong>
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $departmentHead->head_title ?? 'Head' }}&nbsp;{{ $departmentHead->head_name ?? '' }}
                                        </div>
                                        <div class="text-muted small">{{ $departmentHead->head_position ?? '' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="card mt-2 border-warning" style="min-width: 260px;">
                            <div class="card-body py-2">
                                <div class="text-danger">
                                    <strong>No active Department Head</strong>
                                    <div class="text-muted small">Forwarding is disabled until a head is active.</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <!-- </div>-->

    </div>
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
