<!-- Accepted Study Leave Progress Reports Table - HOD Academic Establishment -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="fas fa-file-alt me-2 text-success"></i>Accepted Study Leave Progress Reports
        </h2>
        <div class="text-muted">Progress Reports Already Processed by HOD Academic Establishment</div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-dark fw-semibold">
            <i class="fas fa-calendar-check me-2"></i> Accepted Progress Reports
        </div>
        <div class="card-body p-0">
            @if (isset($progressReportApplications) && $progressReportApplications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 dt-enable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Reference No</th>
                                <th>Employee No</th>
                                <th>Name with Initials</th>
                                <th>Department</th>
                                <th>Faculty</th>
                                <th>Submitted Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($progressReportApplications as $application)
                                <tr>
                                    <td class="px-3">
                                        <span class="fw-semibold text-dark">{{ $application->reference_no }}</span>
                                        <div class="text-muted small">
                                            <i class="fas fa-file-alt"></i> Progress Report
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $application->empno }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $application->name_with_initials }}</div>
                                    </td>
                                    <td>{{ $application->department ?? 'N/A' }}</td>
                                    <td>{{ $application->faculty ?? 'N/A' }}</td>
                                    <td>
                                        <div class="text-muted">
                                            {{ \Carbon\Carbon::parse($application->submitted_date)->format('M d, Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($application->submitted_date)->format('h:i A') }}
                                        </small>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = 'bg-success';
                                            $statusText = $application->status ?? 'Forwarded';
                                            if (stripos($statusText, 'returned') !== false) {
                                                $statusBadge = 'bg-info';
                                            } elseif (stripos($statusText, 'rejected') !== false) {
                                                $statusBadge = 'bg-danger';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('hodacademicestablishment.studyLeaveProgress.view', $application->progress_report_id) }}?from=accepted"
                                            class="btn btn-sm btn-outline-dark" title="View Progress Report">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted mb-3">
                        <i class="fas fa-calendar-check fa-3x"></i>
                    </div>
                    <h5 class="text-muted">No Accepted Progress Reports</h5>
                    <p class="text-muted">There are no processed progress reports yet.</p>
                </div>
            @endif
        </div>
    </div>

    @if (isset($progressReportApplications) && $progressReportApplications->count() > 0)
        <div class="mt-3 text-muted text-center">
            <small>Total Accepted Progress Reports: {{ $progressReportApplications->count() }}</small>
        </div>
    @endif
</div>

<style>
    .card-header-dark {
        background: linear-gradient(135deg, #212529 0%, #343a40 100%);
        color: white;
        border-bottom: 3px solid #0d6efd;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .table td {
        vertical-align: middle;
    }

    .badge {
        font-size: 0.75rem;
    }

    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
    }

    .btn-outline-dark:hover {
        color: white;
        background-color: #212529;
        border-color: #212529;
    }
</style>
