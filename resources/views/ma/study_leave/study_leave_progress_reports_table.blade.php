<div class="container py-4">
    @php
        $isSubmittedView = request()->routeIs('ma.studyleave.progress.submitted');
    @endphp
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">{{ $isSubmittedView ? 'Submitted Study Leave Progress Reports' : 'Study Leave Progress Reports' }}</h2>
        <div class="text-muted">{{ $isSubmittedView ? 'New Progress Reports' : 'Progress Reports In Review' }}</div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-file-alt me-2"></i>{{ $isSubmittedView ? ' Submitted Progress Reports' : ' Progress Reports In Review' }}
            <span class="badge badge-light ml-2">{{ isset($progressReportApplications) ? $progressReportApplications->count() : 0 }}</span>
            <div class="card-tools float-right">
               
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($progressReportApplications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
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
                            @foreach($progressReportApplications as $application)
                                <tr class="hoverable-row">
                                    <td class="px-3">
                                        <span class="fw-semibold text-primary">{{ $application->reference_no }}</span>
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
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = 'bg-warning';
                                            $statusText = $application->status ?? 'Processing MA';
                                            
                                            if(stripos($statusText, 'approved') !== false) {
                                                $statusBadge = 'bg-success';
                                            } elseif(stripos($statusText, 'rejected') !== false || stripos($statusText, 'not approved') !== false) {
                                                $statusBadge = 'bg-danger';
                                            } elseif(stripos($statusText, 'returned') !== false) {
                                                $statusBadge = 'bg-info';
                                            } elseif(stripos($statusText, 'processing') !== false) {
                                                $statusBadge = 'bg-warning';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('ma.show.studyleave.progressreport', ['progress_report_id' => $application->progress_report_id, 'from' => request()->routeIs('ma.studyleave.progress.submitted') ? 'submitted' : 'in_review']) }}" 
                                           class="btn btn-sm btn-outline-primary">
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
                        <i class="fas fa-clipboard-check fa-3x"></i>
                    </div>
                    @if($isSubmittedView)
                        <h5 class="text-muted">No Submitted Progress Reports</h5>
                        <p class="text-muted">There are no newly submitted progress reports.</p>
                    @else
                        <h5 class="text-muted">No Progress Reports In Review</h5>
                        <p class="text-muted">There are no progress reports currently in review.</p>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if($progressReportApplications->count() > 0)
        <div class="mt-3 text-muted text-center">
            <small>Total Progress Reports: {{ $progressReportApplications->count() }}</small>
        </div>
    @endif
</div>

<style>
    .hoverable-row:hover {
        background-color: #f8f9fa;
    }

    .table th {
        background-color: #f8f9fa;
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
</style>
