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

    <!-- Study Leave Information -->
    <div class="card mb-4 shadow-sm">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-info-circle me-2"></i>Study Leave Information
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <strong>Reference No:</strong><br>
                    {{ $study_leave->reference_no }}
                </div>
                <div class="col-md-4">
                    <strong>Degree Title:</strong><br>
                    {{ $study_leave->degree_title }}
                </div>
                <div class="col-md-4">
                    <strong>University/Institute:</strong><br>
                    {{ $study_leave->university_institute }}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <strong>Study Period:</strong><br>
                    {{ \Carbon\Carbon::parse($study_leave->study_leave_from)->format('d M Y') }} - 
                    {{ \Carbon\Carbon::parse($study_leave->study_leave_to)->format('d M Y') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Reports List -->
    <div class="card shadow-sm">
        <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-list me-2"></i>Progress Reports (Every 6 Months)
            </span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 8%">#</th>
                            <th style="width: 22%">Report Period</th>
                            <th style="width: 15%">Due Date</th>
                            <th style="width: 12%">Status</th>
                            <th style="width: 15%">Submitted Date</th>
                            <th style="width: 28%">Actions</th>
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
                                    <td>{{ $dueDate->format('d M Y') }}</td>
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
                        @php
                            // Log the next due date and related information to browser console
                            if ($nextDueDate) {
                                $nextDueDateFormatted = $nextDueDate instanceof \Carbon\Carbon 
                                    ? $nextDueDate->format('Y-m-d H:i:s') 
                                    : \Carbon\Carbon::parse($nextDueDate)->format('Y-m-d H:i:s');
                                echo "<script>console.log('Next Due Date:', '" . $nextDueDateFormatted . "');</script>";
                            } else {
                                echo "<script>console.log('Next Due Date:', null);</script>";
                            }
                            echo "<script>console.log('Can Upload Next:', " . json_encode($canUploadNext) . ");</script>";
                            echo "<script>console.log('Next Report Index:', " . ($progress_reports->count() + 1) . ");</script>";
                        @endphp

                        <!-- Show next upload option if available -->
                        @if($canUploadNext && $nextDueDate)
                        
                            @php
                                $nextIndex = $progress_reports->count() + 1;
                                $periodStart = $nextDueDate->copy()->subMonths(6);
                                $today = \Carbon\Carbon::now();
                                $isOverdue = $today->greaterThan($nextDueDate);
                            @endphp
                            <tr class="table-warning">
                                <td class="text-center fw-semibold">{{ $nextIndex }}</td>
                                <td>
                                    <strong>Progress Report {{ $nextIndex }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $periodStart->format('M Y') }} - {{ $nextDueDate->format('M Y') }}
                                    </small>
                                </td>
                                <td>
                                    {{ $nextDueDate->format('d M Y') }}
                                    @if($isOverdue)
                                        <br><span class="badge bg-danger">Overdue</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                </td>
                                <td>-</td>
                                <td>
                                    <button type="button" 
                                            class="btn btn-sm {{ $isOverdue ? 'btn-danger' : 'btn-primary' }}" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#uploadNewModal">
                                        <i class="fas fa-upload me-1"></i> Upload Report
                                    </button>
                                </td>
                            </tr>
                        @endif

                        @if($progress_reports->count() == 0 && !$canUploadNext)
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <br>
                                    <p class="text-muted mb-0">Study leave has not started yet.</p>
                                    <small class="text-muted">Upload will be available from {{ \Carbon\Carbon::parse($study_leave->study_leave_from)->format('d M Y') }}.</small>
                                </td>
                            </tr>
                        @elseif($progress_reports->count() > 0 && !$canUploadNext && !$nextDueDate)
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                    <br>
                                    <p class="text-muted mb-0">All progress reports have been submitted.</p>
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
@if($canUploadNext && $nextDueDate)
    @php
        $periodStart = $nextDueDate->copy()->subMonths(6);
        $nextIndex = $progress_reports->count() + 1;
    @endphp
    <div class="modal fade" id="uploadNewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('StudyLeave.progressReport.upload', $study_leave->id) }}" 
                      method="POST" 
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="due_date" value="{{ $nextDueDate->format('Y-m-d') }}">
                    
                    <div class="modal-header bg-maroon text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-upload me-2"></i>Upload Progress Report {{ $nextIndex }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Report Period:</strong> 
                            {{ $periodStart->format('d M Y') }} - {{ $nextDueDate->format('d M Y') }}
                            <br>
                            <strong>Due Date:</strong> {{ $nextDueDate->format('d M Y') }}
                        </div>

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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                            <i class="fas fa-check me-2"></i>Submit Report
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<style>
    .bg-maroon {
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
