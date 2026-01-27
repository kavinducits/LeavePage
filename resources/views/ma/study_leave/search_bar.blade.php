{{-- Search Bar Component for Study Leave Applications --}}
<form action="{{ $action }}" method="GET" class="form-inline">
    <div class="input-group input-group-sm" style="width: 300px;">
        <input type="text" 
               name="search" 
               class="form-control" 
               placeholder="{{ $placeholder ?? 'Search by Emp No or Reference No...' }}" 
               value="{{ $search ?? '' }}"
               style="background-color: white;">
        <div class="input-group-append">
            @if($search ?? false)
                <a href="{{ $action }}" 
                   class="btn btn-default" 
                   title="Clear search">
                    <i class="fas fa-times"></i>
                </a>
            @endif
            <button type="submit" class="btn btn-default">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
</form>

<style>
    /* Search bar styling */
    .input-group-sm .form-control {
        border-radius: 0.2rem 0 0 0.2rem;
    }
    
    .input-group-sm .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
