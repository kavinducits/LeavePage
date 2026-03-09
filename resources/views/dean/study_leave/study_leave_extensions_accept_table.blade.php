<!-- Accepted Study Leave Extension Applications Table - Dean -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark"><i class="fas fa-file-alt me-2 text-primary"></i>Accepted Study Leave Extension Applications</h2>
        <div class="text-muted">Extensions Already Forwarded by Dean</div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-calendar-check me-2"></i> Accepted Extension Requests
        </div>
        <div class="card-body p-0">
            @if(isset($extensionApplications) && $extensionApplications->count() > 0)
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
                            @foreach($extensionApplications as $extension)
                                <tr class="hoverable-row">
                                    <td class="px-3">
                                        <span class="fw-semibold text-maroon">{{ $extension->reference_no }}</span>
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar-plus"></i> Extension Request
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $extension->empno }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $extension->name_with_initials }}</div>
                                    </td>
                                    <td>{{ $extension->department }}</td>
                                    <td>{{ $extension->faculty }}</td>
                                    <td>
                                        <div class="text-muted">
                                            {{ \Carbon\Carbon::parse($extension->extension_applied_date)->format('M d, Y') }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="far fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($extension->old_end_date)->format('M d, Y') }}
                                            <i class="fas fa-arrow-right mx-1"></i>
                                            {{ \Carbon\Carbon::parse($extension->new_end_date)->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = 'bg-success';
                                            $statusText = $extension->status ?? 'Forwarded';

                                            if (stripos($statusText, 'approved') !== false) {
                                                $statusBadge = 'bg-success';
                                            } elseif (stripos($statusText, 'rejected') !== false) {
                                                $statusBadge = 'bg-danger';
                                            } elseif (stripos($statusText, 'returned') !== false) {
                                                $statusBadge = 'bg-info';
                                            }
                                        @endphp
                                        <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('dean.show.extension', $extension->extension_id) }}?from=accepted"
                                            class="btn btn-sm btn-outline-danger" title="View Extension Request">
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
                    <h5 class="text-muted">No Accepted Extensions</h5>
                    <p class="text-muted">There are no forwarded extension requests yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>
