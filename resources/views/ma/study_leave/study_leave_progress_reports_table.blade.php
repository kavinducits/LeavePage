<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 fw-bold">Pending Study Leave Progress Reports</h2>
        <div class="text-muted">Progress Reports Pending Review</div>
    </div>

    <!-- Info boxes -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-file-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Reports</span>
                    <span class="info-box-number">{{ $progressReportApplications->count() ?? 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Pending Review</span>
                    <span class="info-box-number">{{ isset($progressReportApplications) ? $progressReportApplications->where('status', 'Processing MA')->count() : 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Reviewed</span>
                    <span class="info-box-number">{{ isset($progressReportApplications) ? $progressReportApplications->whereNotIn('status', ['Processing MA'])->count() : 0 }}</span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Today</span>
                    <span class="info-box-number">{{ isset($progressReportApplications) ? $progressReportApplications->where('submitted_date', '>=', now()->startOfDay())->count() : 0 }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white fw-semibold">
            <i class="fas fa-file-alt me-2"></i>Submitted Progress Reports
            <span class="badge badge-light ml-2">{{ isset($progressReportApplications) ? $progressReportApplications->where('status', 'Processing MA')->count() : 0 }}</span>
            <div class="card-tools float-right">
                <span class="text-white">
                    <i class="fas fa-clock mr-1"></i>
                    Last updated: {{ now()->format('M d, Y h:i A') }}
                </span>
            </div>
        </div>
        
        <!-- Search and Sort Controls -->
        <div class="card-body border-bottom search-sort-section">
            <form method="GET" action="{{ request()->url() }}" class="row align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Progress Reports</label>
                    <div class="input-group">
                        <input type="text"
                               class="form-control"
                               id="search"
                               name="search"
                               value="{{ request('search') ?? '' }}"
                               placeholder="Search by reference, employee, name, department...">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="sort_by" class="form-label">Sort By</label>
                    <select class="form-control" id="sort_by" name="sort_by">
                        <option value="submitted_date" {{ request('sort_by', 'submitted_date') == 'submitted_date' ? 'selected' : '' }}>Submitted Date</option>
                        <option value="reference_no" {{ request('sort_by') == 'reference_no' ? 'selected' : '' }}>Reference No</option>
                        <option value="empno" {{ request('sort_by') == 'empno' ? 'selected' : '' }}>Employee No</option>
                        <option value="name_with_initials" {{ request('sort_by') == 'name_with_initials' ? 'selected' : '' }}>Name</option>
                        <option value="department" {{ request('sort_by') == 'department' ? 'selected' : '' }}>Department</option>
                        <option value="faculty" {{ request('sort_by') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                        <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>Status</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sort_order" class="form-label">Order</label>
                    <select class="form-control" id="sort_order" name="sort_order">
                        <option value="desc" {{ request('sort_order', 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                        <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="{{ request()->url() }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>

            <!-- Quick Search Shortcuts -->
            <div class="mt-3">
                <small class="text-muted">Quick filters:</small>
                <div class="btn-group btn-group-sm ml-2" role="group">
                    <a href="{{ request()->url() }}?search=Processing MA"
                       class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-clock"></i> Processing MA
                    </a>
                    <a href="{{ request()->url() }}?sort_by=submitted_date&sort_order=desc"
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-calendar"></i> Latest First
                    </a>
                    <a href="{{ request()->url() }}?sort_by=name_with_initials&sort_order=asc"
                       class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-sort-alpha-down"></i> Name A-Z
                    </a>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <!-- Search Results Summary -->
            @if(request('search') || request('sort_by', 'submitted_date') != 'submitted_date' || request('sort_order', 'desc') != 'desc')
                <div class="alert alert-info search-results-info mx-3 mt-3">
                    <i class="fas fa-info-circle"></i>
                    <strong>Filters Applied:</strong>
                    @if(request('search'))
                        Search: "<em>{{ request('search') }}</em>"
                    @endif
                    @if(request('sort_by', 'submitted_date') != 'submitted_date' || request('sort_order', 'desc') != 'desc')
                        | Sorted by: <em>{{ ucwords(str_replace('_', ' ', request('sort_by', 'submitted_date'))) }}</em>
                        ({{ request('sort_order', 'desc') == 'asc' ? 'Ascending' : 'Descending' }})
                    @endif
                    | Showing {{ $progressReportApplications->count() ?? 0 }} result(s)
                </div>
            @endif
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
                                        <span class="badge bg-info">{{ $application->status }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('ma.show.studyleave.progressreport', $application->progress_report_id) }}" 
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
                    <h5 class="text-muted">No Progress Reports Pending</h5>
                    <p class="text-muted">There are no progress reports currently waiting for review.</p>
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
            $('#sort_by').val('submitted_date');
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
</style>
