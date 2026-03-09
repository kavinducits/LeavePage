<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="fas fa-graduation-cap me-2 text-primary"></i>Pending Study Leave Applications
        </h2>
        <div class="text-muted">
            <i class="fas fa-clock me-1"></i>Applications Pending Review
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-dark fw-semibold">
            <i class="fas fa-list me-2"></i>Study Leave Applications - Status: Processing HOD
        </div>
        <div class="card-body p-0">
            @if ($studyLeaveApplications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0 dt-enable">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Reference No</th>
                                <th>Employee No</th>
                                <th>Name with Initials</th>
                                <th>Department</th>
                                <th>Faculty</th>
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($studyLeaveApplications as $application)
                                <tr class="hoverable-row">
                                    <td class="px-3">
                                        <span class="fw-semibold text-dark">{{ $application->reference_no }}</span>
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
                                            {{ \Carbon\Carbon::parse($application->applied_date)->format('M d, Y') }}
                                        </div>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($application->applied_date)->format('h:i A') }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-hourglass-half me-1"></i>{{ $application->status }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('hod.view.studyLeave', $application->id) }}"
                                            class="btn btn-sm btn-outline-dark" title="View Details">
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
                        <i class="fas fa-inbox fa-3x text-dark opacity-50"></i>
                    </div>
                    <h5 class="text-muted">No Study Leave Applications Pending</h5>
                    <p class="text-muted">There are no study leave applications currently waiting for your review.</p>
                </div>
            @endif
        </div>
        @if ($studyLeaveApplications->count() > 0)
            <div class="card-footer bg-light">
                <div class="text-muted text-center">
                    <small><i class="fas fa-info-circle me-1"></i>Total Applications:
                        <strong>{{ $studyLeaveApplications->count() }}</strong></small>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .hoverable-row {
        transition: background-color 0.2s ease;
    }

    .hoverable-row:hover {
        background-color: #f8f9fa;
    }

    .shadow-sm {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
    }

    .card {
        border: 1px solid rgba(0, 0, 0, 0.1);
    }
</style>
