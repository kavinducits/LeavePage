<!-- Study Leave Progress Reports History -->
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #000000; color: white;">
        <h5 class="mb-0">
            <i class="fas fa-chart-line me-2"></i>Progress Reports History
        </h5>
        <!-- DEBUG: canUpload={{ isset($canUploadProgressReport) ? ($canUploadProgressReport ? 'YES' : 'NO') : 'NOT SET' }}, hasPending={{ isset($hasPendingProgressReport) ? ($hasPendingProgressReport ? 'YES' : 'NO') : 'NOT SET' }} -->
        
        @if(isset($canUploadProgressReport) && $canUploadProgressReport)
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#uploadProgressReportModal">
                <i class="fas fa-upload me-1"></i>Upload Progress Report
            </button>
        @elseif(isset($hasPendingProgressReport) && $hasPendingProgressReport)
            <button type="button" class="btn btn-sm btn-warning" disabled title="You have a pending progress report">
                <i class="fas fa-hourglass-half me-1"></i>Pending Report
            </button>
        @else
            <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#uploadProgressReportModal">
                <i class="fas fa-upload me-1"></i>Upload Progress Report (Test)
            </button>
        @endif
    </div>
    <div class="card-body">
        @if(isset($progressReports) && $progressReports->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 13%">Due Date</th>
                            <th style="width: 13%">Submitted Date</th>
                            <th style="width: 10%">Submission Status</th>
                            <th style="width: 30%">Remarks</th>
                            <th style="width: 13%">Status</th>
                            <th style="width: 16%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($progressReports as $index => $report)
                            @php
                                $dueDate = \Carbon\Carbon::parse($report->due_date);
                                $submittedDate = $report->submitted_date ? \Carbon\Carbon::parse($report->submitted_date) : null;
                                
                                // Calculate period start (6 months before due date)
                                $periodStart = $dueDate->copy()->subMonths(6);
                                
                                // Calculate submission status
                                $submissionStatus = '';
                                $submissionBadge = '';
                                if ($submittedDate) {
                                    if ($submittedDate->gt($dueDate)) {
                                        $daysLate = $dueDate->diffInDays($submittedDate);
                                        $submissionStatus = "Late ({$daysLate} days)";
                                        $submissionBadge = 'bg-warning text-dark';
                                    } else {
                                        $submissionStatus = 'On Time';
                                        $submissionBadge = 'bg-success';
                                    }
                                } else {
                                    $submissionStatus = 'Not Submitted';
                                    $submissionBadge = 'bg-secondary';
                                }
                                
                                // Status badge configuration
                                $statusBadge = '';
                                $statusText = $report->status ?? 'Unknown';
                                
                                switch($report->status_id) {
                                    case 1:
                                        $statusBadge = 'bg-success';
                                        break;
                                    case 2:
                                        $statusBadge = 'bg-danger';
                                        break;
                                    case 3:
                                        $statusBadge = 'bg-info text-dark';
                                        break;
                                    case 4:
                                    case 5:
                                    case 6:
                                    case 7:
                                    case 8:
                                    case 9:
                                    case 10:
                                        $statusBadge = 'bg-warning text-dark';
                                        break;
                                    default:
                                        $statusBadge = 'bg-secondary';
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    {{ $dueDate->format('d M Y') }}
                                    <br>
                                    <small class="text-muted">
                                        {{ $periodStart->format('M Y') }} - {{ $dueDate->format('M Y') }}
                                    </small>
                                </td>
                                <td>
                                    @if($submittedDate)
                                        <i class="fas fa-calendar-check text-success me-1"></i>
                                        {{ $submittedDate->format('d M Y') }}
                                    @else
                                        <span class="text-muted">
                                            <i class="fas fa-minus me-1"></i>Not Submitted
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $submissionBadge }}">
                                        @if($submittedDate)
                                            @if($submittedDate->gt($dueDate))
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                            @else
                                                <i class="fas fa-check-circle me-1"></i>
                                            @endif
                                        @else
                                            <i class="fas fa-clock me-1"></i>
                                        @endif
                                        {{ $submissionStatus }}
                                    </span>
                                </td>
                                <td>
                                    @if($report->remark)
                                        <small>{{ \Illuminate\Support\Str::limit($report->remark, 80) }}</small>
                                        @if(strlen($report->remark) > 80)
                                            <button type="button" 
                                                    class="btn btn-sm btn-link p-0" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#remarkModal{{ $report->id }}">
                                                <i class="fas fa-eye"></i> View Full
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted"><i class="fas fa-minus"></i> No remarks</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $statusBadge }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" 
                                            class="btn btn-sm btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#viewReportModal{{ $report->id }}"
                                            title="View Progress Report Details">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>

                            <!-- View Progress Report Modal -->
                            <div class="modal fade" id="viewReportModal{{ $report->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #800020; color: white;">
                                            <h5 class="modal-title">
                                                <i class="fas fa-chart-line me-2"></i>Progress Report Details - #{{ $index + 1 }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Status Badge -->
                                            <div class="alert alert-light border mb-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Report #:</strong> {{ $index + 1 }}
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <strong>Status:</strong> <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Report Dates -->
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <strong><i class="fas fa-calendar me-2"></i>Report Dates</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="text-muted small">Due Date:</label>
                                                            <div class="fw-semibold">{{ $dueDate->format('d M Y') }}</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="text-muted small">Submitted Date:</label>
                                                            <div class="fw-semibold {{ $submittedDate ? 'text-success' : 'text-muted' }}">
                                                                {{ $submittedDate ? $submittedDate->format('d M Y') : 'Not Submitted' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if($submittedDate)
                                                        <div class="row mt-2">
                                                            <div class="col-md-12">
                                                                <label class="text-muted small">Submission Status:</label>
                                                                <div>
                                                                    @if($submittedDate->gt($dueDate))
                                                                        <span class="badge bg-warning text-dark">
                                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                                            Submitted Late ({{ $dueDate->diffInDays($submittedDate) }} days)
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-success">
                                                                            <i class="fas fa-check-circle me-1"></i>
                                                                            Submitted On Time
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Remarks -->
                                            @if($report->status_id == 3)
                                                <!-- Show return message for returned reports -->
                                                <div class="alert alert-warning mb-3">
                                                    <h6 class="alert-heading mb-2">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>Report Returned by MA
                                                    </h6>
                                                    <p class="mb-0">This progress report has been returned by the Management Assistant. Please review the remarks below, make necessary corrections, and re-upload the document.</p>
                                                </div>
                                                @if($report->remark)
                                                    <div class="card mb-3 border-warning">
                                                        <div class="card-header bg-warning">
                                                            <strong><i class="fas fa-comment-dots me-2"></i>Return Remarks from MA</strong>
                                                        </div>
                                                        <div class="card-body">
                                                            <p class="mb-0" style="white-space: pre-wrap;">{{ $report->remark }}</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            @elseif($report->remark)
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light">
                                                        <strong><i class="fas fa-comment me-2"></i>Remarks</strong>
                                                    </div>
                                                    <div class="card-body">
                                                        <p class="mb-0" style="white-space: pre-wrap;">{{ $report->remark }}</p>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Document -->
                                            @if($report->document_path)
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light">
                                                        <strong><i class="fas fa-file-pdf me-2"></i>Attached Document</strong>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <a href="{{ Storage::url($report->document_path) }}" 
                                                           target="_blank" 
                                                           class="btn btn-primary">
                                                            <i class="fas fa-download me-2"></i>Download/View Document
                                                        </a>
                                                        
                                                        @if($report->status_id == 3)
                                                            <!-- Show Remove & Re-upload button for Returned reports -->
                                                            <button type="button" 
                                                                    class="btn btn-warning ms-2"
                                                                    onclick="removeAndReupload({{ $report->id }})">
                                                                <i class="fas fa-sync-alt me-2"></i>Remove & Re-upload
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @elseif($report->status_id == 3)
                                                <!-- Show upload button for Returned reports without document -->
                                                <div class="card mb-3 border-warning">
                                                    <div class="card-header bg-warning">
                                                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Action Required</strong>
                                                    </div>
                                                    <div class="card-body text-center">
                                                        <p class="text-danger mb-3">This report was returned. Please upload a new progress report document.</p>
                                                        <button type="button" 
                                                                class="btn btn-success"
                                                                data-bs-dismiss="modal"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#reuploadProgressReportModal{{ $report->id }}">
                                                            <i class="fas fa-upload me-2"></i>Upload New Document
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Timestamps -->
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <strong><i class="fas fa-clock me-2"></i>Record Information</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="text-muted small">Created On:</label>
                                                            <div>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, h:i A') }}</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="text-muted small">Last Updated:</label>
                                                            <div>{{ \Carbon\Carbon::parse($report->updated_at)->format('d M Y, h:i A') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Remark Modal (for long remarks) -->
                            @if($report->remark && strlen($report->remark) > 100)
                                <div class="modal fade" id="remarkModal{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #800020; color: white;">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-comment me-2"></i>Progress Report Remarks - Report #{{ $index + 1 }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-light border">
                                                    <strong>Due Date:</strong> {{ $dueDate->format('d M Y') }}<br>
                                                    <strong>Submitted Date:</strong> {{ $submittedDate ? $submittedDate->format('d M Y') : 'Not Submitted' }}
                                                </div>
                                                <h6 class="fw-bold mb-3">Remarks:</h6>
                                                <p class="text-justify" style="white-space: pre-wrap;">{{ $report->remark }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Reupload Progress Report Modal (for returned reports) -->
                            @if($report->status_id == 3)
                                <div class="modal fade" id="reuploadProgressReportModal{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #800020; color: white;">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-upload me-2"></i>Re-upload Progress Report - Report #{{ $index + 1 }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('StudyLeave.progressReport.reupload', ['report_id' => $report->id]) }}" 
                                                  method="POST" 
                                                  enctype="multipart/form-data" 
                                                  id="reupload-form-{{ $report->id }}"
                                                  class="needs-validation" 
                                                  novalidate>
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        <strong>Report Returned:</strong> {{ $report->remark ?? 'Please upload a revised document.' }}
                                                    </div>

                                                    <div class="alert alert-light border">
                                                        <strong>Due Date:</strong> {{ $dueDate->format('d M Y') }}<br>
                                                        <strong>Original Submission:</strong> {{ $submittedDate ? $submittedDate->format('d M Y') : 'N/A' }}
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="document{{ $report->id }}" class="form-label fw-bold">
                                                            Upload New Document <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="file" 
                                                               class="form-control" 
                                                               id="document{{ $report->id }}" 
                                                               name="document" 
                                                               accept=".pdf" 
                                                               required>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle me-1"></i>
                                                            PDF only, Maximum 10MB
                                                        </div>
                                                        <div class="invalid-feedback">
                                                            Please select a PDF document.
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="reupload_notes{{ $report->id }}" class="form-label fw-bold">
                                                            Notes (Optional)
                                                        </label>
                                                        <textarea class="form-control" 
                                                                  id="reupload_notes{{ $report->id }}" 
                                                                  name="notes" 
                                                                  rows="3" 
                                                                  placeholder="Add any notes about changes made..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">
                                                        <i class="fas fa-upload me-2"></i>Upload Document
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No progress reports found for this study leave.</p>
                @if(isset($canUploadProgressReport) && $canUploadProgressReport)
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#uploadProgressReportModal">
                        <i class="fas fa-upload me-2"></i>Upload Your First Progress Report
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Upload New Progress Report Modal -->
<div class="modal fade" id="uploadProgressReportModal" tabindex="-1" aria-labelledby="uploadProgressReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #800020; color: white;">
                <h5 class="modal-title" id="uploadProgressReportModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Progress Report
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(isset($study_leave))
                    @if(isset($hasPendingProgressReport) && $hasPendingProgressReport)
                        <div class="alert alert-warning fw-semibold">
                            <i class="fas fa-hourglass-half me-2"></i>
                            <strong>Pending Progress Report:</strong> You already have a progress report pending approval. You cannot submit a new progress report until the current one is processed.
                        </div>
                    @endif

                    @if(isset($canUploadProgressReport) && !$canUploadProgressReport && (!isset($hasPendingProgressReport) || !$hasPendingProgressReport))
                        <div class="alert alert-danger fw-semibold">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Upload Not Allowed:</strong> You cannot upload a progress report at this time. Please ensure:
                            <ul class="mb-0 mt-2">
                                <li>Your study leave period has started</li>
                                <li>At least 6 months have passed since the last report</li>
                                <li>Your study leave period has not ended</li>
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('StudyLeave.progressReport.upload', ['study_leave_id' => $study_leave->id]) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          id="upload-progress-report-form" 
                          class="needs-validation" 
                          novalidate>
                        @csrf

                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Study Leave Reference:</strong> {{ $study_leave->reference_no ?? 'N/A' }}
                        </div>

                        @php
                            $nextDueDate = isset($nextProgressReportDueDate) && $nextProgressReportDueDate ? \Carbon\Carbon::parse($nextProgressReportDueDate) : null;
                            $periodStart = $nextDueDate ? $nextDueDate->copy()->subMonths(6) : null;
                        @endphp

                        @if($nextDueDate)
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Report Period:</strong> 
                                {{ $periodStart->format('d M Y') }} - {{ $nextDueDate->format('d M Y') }}
                                <br>
                                <strong>Due Date:</strong> {{ $nextDueDate->format('d M Y') }}
                            </div>
                        @endif

                        <input type="hidden" name="due_date" value="{{ $nextProgressReportDueDate ?? '' }}">

                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-file-upload me-2"></i>Progress Report Details</strong>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">

                                    <!-- Remarks -->
                                    <div class="col-12">
                                        <label for="new_remark" class="form-label fw-semibold">
                                            Remarks/Notes
                                        </label>
                                        <textarea name="remark" 
                                                  id="new_remark" 
                                                  class="form-control" 
                                                  rows="4" 
                                                  placeholder="Enter any remarks or notes about this progress report"
                                                  {{ (!isset($canUploadProgressReport) || !$canUploadProgressReport || (isset($hasPendingProgressReport) && $hasPendingProgressReport)) ? 'readonly' : '' }}></textarea>
                                    </div>

                                    <!-- Document Upload -->
                                    <div class="col-12">
                                        <label for="new_progress_report" class="form-label fw-semibold">
                                            Upload Progress Report Document (PDF Only) <span class="text-danger">*</span>
                                        </label>
                                        <input type="file" 
                                               name="progress_report" 
                                               id="new_progress_report" 
                                               class="form-control" 
                                               accept=".pdf,application/pdf" 
                                               required
                                               {{ (!isset($canUploadProgressReport) || !$canUploadProgressReport || (isset($hasPendingProgressReport) && $hasPendingProgressReport)) ? 'disabled' : '' }}>
                                        <small class="text-muted">Accepted format: PDF only (Max size: 10MB)</small>
                                        <div class="invalid-feedback">
                                            Please upload a PDF document.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            @if(isset($hasPendingProgressReport) && $hasPendingProgressReport)
                                <button type="button" class="btn btn-warning" disabled>
                                    <i class="fas fa-hourglass-half me-2"></i>Pending Report Exists
                                </button>
                            @elseif(isset($canUploadProgressReport) && $canUploadProgressReport)
                                <button type="submit" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                                    <i class="fas fa-paper-plane me-2"></i>Upload Progress Report
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-2"></i>Upload Not Allowed
                                </button>
                            @endif
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Study leave information not available.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Form validation for progress report upload form
        const uploadForm = document.getElementById('upload-progress-report-form');
        
        if (uploadForm) {
            uploadForm.addEventListener('submit', function(event) {
                if (!uploadForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                uploadForm.classList.add('was-validated');
            }, false);
        }

        // Form validation for all reupload forms
        const reuploadForms = document.querySelectorAll('[id^="reupload-form-"]');
        reuploadForms.forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });

        // File size validation
        const fileInputs = document.querySelectorAll('input[type="file"][accept*=".pdf"]');
        fileInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                const maxSize = 10 * 1024 * 1024; // 10MB for progress reports
                if (this.files[0] && this.files[0].size > maxSize) {
                    alert('File size must be less than 10MB');
                    this.value = '';
                }
            });
        });
    });

    // Function to handle remove and reupload
    function removeAndReupload(reportId) {
        Swal.fire({
            title: 'Remove Existing Document?',
            text: "This will remove your current progress report document. You'll need to upload a new one.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Removing document...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form to remove document
                fetch(`{{ url('studyleave/progressreport/remove') }}/${reportId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Document Removed',
                            text: 'You can now upload a new document.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Failed to remove document'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while removing the document'
                    });
                });
            }
        });
    }
</script>

<style>
    .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(128, 0, 32, 0.05);
    }
    
    .modal-lg {
        max-width: 800px;
    }
</style>
