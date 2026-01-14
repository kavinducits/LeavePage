<!-- Study Leave Extension Applications Table -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">
            <i class="fas fa-calendar-plus me-2  text-primary"></i>Study Leave Extension Applications
        </h2>
        <div class="text-muted">Extension Requests for VC Review</div>
    </div>

    <div class="card">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-calendar-plus me-2"></i> Extension Requests
        </div>
        <div class="card-body p-0">
            @if (isset($extensionApplications) && $extensionApplications->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="px-3">Reference No</th>
                                <th>Employee No</th>
                                <th>Name with Initials</th>
                                <th>Department</th>
                                <th>Faculty</th>
                                <th>Extension Details</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($extensionApplications as $extension)
                                <tr>
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
                                        <div class="small">
                                            <div class="text-muted mb-1">
                                                <i class="far fa-calendar"></i> Applied:
                                                {{ \Carbon\Carbon::parse($extension->extension_applied_date)->format('M d, Y') }}
                                            </div>
                                            <div class="fw-semibold">
                                                <span
                                                    class="text-danger">{{ \Carbon\Carbon::parse($extension->old_end_date)->format('M d, Y') }}</span>
                                                <i class="fas fa-arrow-right mx-1 text-maroon"></i>
                                                <span
                                                    class="text-success">{{ \Carbon\Carbon::parse($extension->new_end_date)->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $statusBadge = 'bg-warning';
                                            $statusText = $extension->status ?? 'Processing VC';

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
                                        <a href="{{ route('vc.show.extension', $extension->extension_id) }}"
                                            class="btn btn-sm btn-outline-maroon" title="View Extension Request">
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
                        <i class="fas fa-calendar-check fa-3x icon-gold"></i>
                    </div>
                    <h5 class="text-muted">No Extension Requests Pending</h5>
                    <p class="text-muted">There are no extension requests currently waiting for VC review.</p>
                </div>
            @endif
        </div>
    </div>

    @if (isset($extensionApplications) && $extensionApplications->count() > 0)
        <div class="mt-3 text-muted text-center">
            <small>Total Extension Requests: {{ $extensionApplications->count() }}</small>
        </div>
    @endif
</div>
