{{-- Statistics Cards Component for Study Leave Applications --}}
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-info"><i class="fas fa-graduation-cap"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Applications</span>
                <span class="info-box-number">{{ $statistics['total'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-warning"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending Review</span>
                <span class="info-box-number">{{ $statistics['pending'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="fas fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Reviewed</span>
                <span class="info-box-number">{{ $statistics['reviewed'] ?? 0 }}</span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="fas fa-calendar-day"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">This Month</span>
                <span class="info-box-number">{{ $statistics['this_month'] ?? 0 }}</span>
            </div>
        </div>
    </div>
</div>
