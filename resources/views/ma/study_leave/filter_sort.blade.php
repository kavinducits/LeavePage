{{-- Filter and Sort Component for Study Leave Applications --}}
<div class="card-body border-bottom" style="background-color: #f8f9fa; padding: 1rem;">
    <form method="GET" action="{{ $action }}" class="row align-items-end">
        <!-- Search Input -->
        <div class="col-md-4">
            <label for="search" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 0.25rem;">
                <i class="fas fa-search"></i> Search Applications
            </label>
            <div class="input-group">
                <input type="text"
                       class="form-control"
                       id="search"
                       name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Search by reference, employee, name...">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sort By -->
        <div class="col-md-3">
            <label for="sort_by" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 0.25rem;">
                <i class="fas fa-sort"></i> Sort By
            </label>
            <select class="form-control" id="sort_by" name="sort_by">
                <option value="applied_date" {{ ($sortBy ?? 'applied_date') == 'applied_date' ? 'selected' : '' }}>Applied Date</option>
                <option value="reference_no" {{ ($sortBy ?? '') == 'reference_no' ? 'selected' : '' }}>Reference No</option>
                <option value="empno" {{ ($sortBy ?? '') == 'empno' ? 'selected' : '' }}>Employee No</option>
                <option value="name" {{ ($sortBy ?? '') == 'name' ? 'selected' : '' }}>Name</option>
                <option value="department" {{ ($sortBy ?? '') == 'department' ? 'selected' : '' }}>Department</option>
                <option value="faculty" {{ ($sortBy ?? '') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                <option value="status" {{ ($sortBy ?? '') == 'status' ? 'selected' : '' }}>Status</option>
            </select>
        </div>

        <!-- Sort Order -->
        <div class="col-md-2">
            <label for="sort_order" class="form-label" style="font-weight: 600; color: #495057; margin-bottom: 0.25rem;">
                <i class="fas fa-arrows-alt-v"></i> Order
            </label>
            <select class="form-control" id="sort_order" name="sort_order">
                <option value="desc" {{ ($sortOrder ?? 'desc') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ ($sortOrder ?? '') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary mr-2">
                <i class="fas fa-filter"></i> Apply
            </button>
            <a href="{{ $action }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Clear
            </a>
        </div>
    </form>

    <!-- Quick Filters -->
    <div class="mt-3">
        <small class="text-muted"><i class="fas fa-bolt"></i> Quick filters:</small>
        <div class="btn-group btn-group-sm ml-2" role="group">
            <a href="{{ $action }}?sort_by=applied_date&sort_order=desc"
               class="btn btn-outline-info btn-sm">
                <i class="fas fa-calendar"></i> Latest First
            </a>
            <a href="{{ $action }}?sort_by=name&sort_order=asc"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-sort-alpha-down"></i> Name A-Z
            </a>
            <a href="{{ $action }}?search=Pending"
               class="btn btn-outline-warning btn-sm">
                <i class="fas fa-clock"></i> Pending Only
            </a>
        </div>
    </div>
</div>

<!-- Filter Results Summary -->
@if($search || ($sortBy ?? 'applied_date') != 'applied_date' || ($sortOrder ?? 'desc') != 'desc')
    <div class="alert alert-info mb-0 rounded-0 border-0" style="font-size: 0.9rem;">
        <i class="fas fa-info-circle"></i>
        <strong>Filters Applied:</strong>
        @if($search)
            Search: "<em>{{ $search }}</em>"
        @endif
        @if(($sortBy ?? 'applied_date') != 'applied_date' || ($sortOrder ?? 'desc') != 'desc')
            | Sorted by: <em>{{ ucwords(str_replace('_', ' ', $sortBy ?? 'applied_date')) }}</em>
            ({{ ($sortOrder ?? 'desc') == 'asc' ? 'Ascending' : 'Descending' }})
        @endif
        | Showing {{ $totalResults ?? 0 }} result(s)
        <a href="{{ $action }}" class="alert-link float-right">Clear all filters</a>
    </div>
@endif

<style>
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.25rem;
    }
</style>
