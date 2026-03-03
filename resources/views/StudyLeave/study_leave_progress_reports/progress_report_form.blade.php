@extends('layouts.app')

@section('content')

<div class="container-fluid px-4 py-3">

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Upload Failed:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="dashboard-header text-maroon mb-1">
                <i class="fas fa-chart-line icon-gold me-2"></i>Study Leave Progress Reports
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('StudyLeave.create') }}">Study Leaves</a></li>
                    <li class="breadcrumb-item active">Progress Reports</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('StudyLeave.create') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Study Leaves
        </a>
    </div>

    <!-- Progress Reports List -->
    <div class="card shadow-sm">
        <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-list me-2"></i>Progress Reports
            </span>
            <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#uploadNewModal">
                <i class="fas fa-upload me-1"></i>Upload New Report
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 8%">#</th>
                            <th style="width: 30%">Report Period</th>
                            <th style="width: 14%">Status</th>
                            <th style="width: 18%">Submitted Date</th>
                            <th style="width: 30%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($progress_reports->count() > 0)
                            @foreach($progress_reports as $index => $report)
                                @php
                                    $dueDate = \Carbon\Carbon::parse($report->due_date);
                                    $submittedDate = $report->submitted_date ? \Carbon\Carbon::parse($report->submitted_date) : null;
                                    
                                    // Calculate period start (6 months before due date)
                                    $periodStart = $dueDate->copy()->subMonths(6);
                                @endphp
                                <tr>
                                    <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                    <td>
                                        <strong>Progress Report {{ $index + 1 }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            {{ $periodStart->format('M Y') }} - {{ $dueDate->format('M Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                            // Determine badge color based on status_id
                                            $badgeClass = 'bg-secondary';
                                            $icon = 'fa-clock';
                                            
                                            if($report->status_id == 1) { // Approved
                                                $badgeClass = 'bg-success';
                                                $icon = 'fa-check-circle';
                                            } elseif($report->status_id == 2) { // Rejected
                                                $badgeClass = 'bg-danger';
                                                $icon = 'fa-times-circle';
                                            } elseif($report->status_id == 3) { // Returned
                                                $badgeClass = 'bg-warning text-dark';
                                                $icon = 'fa-undo';
                                            } elseif($report->status_id == 4) { // Processing MA
                                                $badgeClass = 'bg-info';
                                                $icon = 'fa-spinner';
                                            } elseif(in_array($report->status_id, [5, 6, 7, 9])) { // Processing at different levels
                                                $badgeClass = 'bg-primary';
                                                $icon = 'fa-hourglass-half';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            <i class="fas {{ $icon }} me-1"></i>{{ $report->status_name ?? 'Submitted' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $submittedDate ? $submittedDate->format('d M Y') : '-' }}
                                    </td>
                                    <td>
                                        @if($report->document_path)
                                            <a href="{{ route('StudyLeave.serveProgressReport', ['filename' => basename($report->document_path)]) }}" 
                                               target="_blank" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-file-pdf me-1"></i> View Report
                                            </a>
                                        @endif
                                        @if($report->remark)
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#remarksModal{{ $report->id }}">
                                                <i class="fas fa-comment"></i> Remarks
                                            </button>
                                        @endif
                                        @if($report->status_id == 3)
                                            <form action="{{ route('StudyLeave.progressReport.delete', $report->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this progress report? You will need to upload a new one.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash me-1"></i> Delete & Re-upload
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif


                        @if($progress_reports->count() == 0)
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                                    <br>
                                    <p class="text-muted mb-0">No progress reports uploaded yet.</p>
                                    <small class="text-muted">Click <strong>Upload New Report</strong> above to submit your first report.</small>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Remarks Modals -->
@if($progress_reports->count() > 0)
    @foreach($progress_reports as $report)
        @if($report->remark)
            <div class="modal fade" id="remarksModal{{ $report->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-maroon text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-comment me-2"></i>Progress Report Remarks
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Report Period:</strong> 
                                @php
                                    $dueDate = \Carbon\Carbon::parse($report->due_date);
                                    $periodStart = $dueDate->copy()->subMonths(6);
                                @endphp
                                {{ $periodStart->format('M Y') }} - {{ $dueDate->format('M Y') }}
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">MA Remarks:</label>
                                <div class="p-3 bg-light border rounded" style="white-space: pre-wrap;">{{ $report->remark }}</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

<!-- Upload New Report Modal -->
@php
    $nextIndex = $progress_reports->count() + 1;
@endphp
<div class="modal fade" id="uploadNewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="uploadReportForm" action="{{ route('StudyLeave.progressReport.upload', $study_leave->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-maroon text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-upload me-2"></i>Upload Progress Report {{ $nextIndex }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="due_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Progress Report Document <span class="text-danger">*</span>
                        </label>
                        <input type="file" 
                               name="progress_report" 
                               class="form-control" 
                               accept=".pdf,application/pdf"
                               required>
                        <small class="text-muted">Only PDF files are allowed (Max: 10MB)</small>
                        @error('progress_report')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Optional Remark -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Remark <span class="text-muted">(optional)</span></label>
                        <textarea name="remark" class="form-control" rows="3" maxlength="1000" placeholder="Add any notes about this report..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" id="submitReportBtn" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                        <i class="fas fa-check me-2"></i>Submit Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-maroon text-white">
                <h5 class="modal-title">
                    <i class="fas fa-question-circle me-2"></i>Confirm Submission
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-file-upload fa-3x mb-3" style="color:#800020"></i>
                <p class="mb-0 fs-6">Are you sure you want to submit this progress report?</p>
                <small class="text-muted">This will be forwarded to MA for review.</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" id="cancelConfirmBtn">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmYesBtn" style="background-color:#800020;border-color:#800020;">
                    <i class="fas fa-check me-2"></i>Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#28a745;color:white;">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Submitted Successfully
                </h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                <p class="mb-0 fs-6">Your progress report has been submitted successfully!</p>
                <small class="text-muted">It has been forwarded to MA for review.</small>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                    <i class="fas fa-check me-2"></i>OK
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const submitBtn = document.getElementById('submitReportBtn');
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
        const uploadModal = bootstrap.Modal.getInstance(document.getElementById('uploadNewModal'));
        const cancelBtn = document.getElementById('cancelConfirmBtn');
        const yesBtn = document.getElementById('confirmYesBtn');

        // Open confirmation when Submit is clicked
        submitBtn.addEventListener('click', function () {
            const form = document.getElementById('uploadReportForm');
            const fileInput = form.querySelector('input[name="progress_report"]');
            if (!fileInput.value) {
                fileInput.reportValidity();
                return;
            }
            confirmModal.show();
        });

        // Cancel: close confirm modal, reopen upload modal
        cancelBtn.addEventListener('click', function () {
            confirmModal.hide();
            document.getElementById('uploadNewModal').addEventListener('hidden.bs.modal', function reopenUpload() {
                const upload = new bootstrap.Modal(document.getElementById('uploadNewModal'));
                upload.show();
                document.getElementById('uploadNewModal').removeEventListener('hidden.bs.modal', reopenUpload);
            }, { once: true });
        });

        // Yes: submit the form
        yesBtn.addEventListener('click', function () {
            confirmModal.hide();
            document.getElementById('uploadReportForm').submit();
        });

        // Show success modal if redirected back with success flag
        @if(session('upload_success'))
            const successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
    });
</script>

<style>
        background-color: #800020 !important;
    }
    
    .card-header-maroon {
        background-color: #800020;
        color: #ffffff;
    }
    
    .text-maroon {
        color: #800020 !important;
    }
    
    .icon-gold {
        color: #FFD700;
    }
    
    .dashboard-header {
        font-size: 1.75rem;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(128, 0, 32, 0.05);
    }
    
    .btn-close-white {
        filter: brightness(0) invert(1);
    }
</style>

@endsection
