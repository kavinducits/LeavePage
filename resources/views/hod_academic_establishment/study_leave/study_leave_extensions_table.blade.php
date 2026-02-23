u<!-- Study Leave Extension Applications Table -->
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold text-dark">Study Leave Extension Applications</h2>
        <div class="text-muted">Extension Requests for Review</div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header card-header-dark fw-semibold">
            <i class="fas fa-calendar-plus me-2"></i> Extension Requests - Processing HOD
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
                                <th>Applied Date</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($extensionApplications as $extension)
                                <tr>
                                    <td class="px-3">
                                        <span class="fw-semibold text-dark">{{ $extension->reference_no }}</span>
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
                                            $statusBadge = 'bg-warning';
                                            $statusText = $extension->status ?? 'Processing HOD';

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
                                        <a href="{{ route('hodacademicestablishment.show.extension', $extension->extension_id) }}"
                                            class="btn btn-sm btn-outline-dark" title="View Extension Request">
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
                    <h5 class="text-muted">No Extension Requests Pending</h5>
                    <p class="text-muted">There are no extension requests currently waiting for review.</p>
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

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Custom JavaScript for Search and Sort -->
<script>
    $(document).ready(function() {
        // Auto-submit form when sort options change
        $('#sort_by, #sort_order').change(function() {
            $(this).closest('form').submit();
        });

        // Enter key search
        $('#search').keypress(function(e) {
            if (e.which == 13) {
                $(this).closest('form').submit();
                return false;
            }
        });

        // Clear search when clear button is clicked
        $('.btn-secondary').click(function(e) {
            e.preventDefault();
            $('#search').val('');
            $('#sort_by').val('extension_applied_date');
            $('#sort_order').val('desc');
            window.location.href = '{{ request()->url() }}';
        });
    });
</script>

<style>
    .info-box {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        border-radius: .25rem;
        background: #fff;
        display: flex;
        margin-bottom: 1rem;
        min-height: 80px;
        padding: .5rem;
        position: relative;
    }

    .info-box-icon {
        border-radius: .25rem;
        align-items: center;
        display: flex;
        font-size: 1.875rem;
        justify-content: center;
        text-align: center;
        width: 70px;
    }

    .info-box-icon > i {
        color: rgba(0,0,0,.15);
    }

    .bg-info .info-box-icon > i {
        color: rgba(255,255,255,.6);
    }

    .bg-warning .info-box-icon > i {
        color: rgba(255,255,255,.6);
    }

    .bg-success .info-box-icon > i {
        color: rgba(255,255,255,.6);
    }

    .bg-primary .info-box-icon > i {
        color: rgba(255,255,255,.6);
    }

    .info-box-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        line-height: 1.2;
        flex: 1;
        padding: 0 10px;
    }

    .info-box-number {
        display: block;
        font-weight: 700;
        font-size: 1.5rem;
    }

    .info-box-text {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .search-sort-section {
        background-color: #f8f9fa;
        border-radius: 0.25rem;
        padding: 1rem;
        margin-bottom: 0;
    }

    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }

    .search-results-info {
        font-size: 0.9rem;
    }

    .card-tools {
        float: right;
    }

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

    .btn-outline-dark:hover {
        color: #fff;
        background-color: #212529;
        border-color: #212529;
    }
</style>

