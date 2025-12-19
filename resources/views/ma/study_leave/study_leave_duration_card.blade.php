<!-- Study Leave Duration Card Component -->
<div class="card shadow-sm rounded-3 border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold text-maroon">
            <i class="fas fa-clock me-2 icon-gold"></i>Study Leave Duration Summary
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <!-- Total Study Leave Days Applied -->
            <div class="col-md-4">
                <div class="duration-info-card h-100 border rounded-3 p-3 text-center bg-light">
                    <div class="duration-icon mb-2">
                        <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-2 small">Total Study Leave Days Applied</h6>
                    <h3 class="mb-0 fw-bold text-primary">{{ $totalStudyLeaveDays ?? 0 }}</h3>
                    <small class="text-muted">days</small>
                </div>
            </div>

            <!-- Current Request Duration -->
            <div class="col-md-4">
                <div class="duration-info-card h-100 border rounded-3 p-3 text-center bg-light">
                    <div class="duration-icon mb-2">
                        <i class="fas fa-hourglass-half fa-2x text-warning"></i>
                    </div>
                    <h6 class="text-muted mb-2 small">Current Request Duration</h6>
                    <h3 class="mb-0 fw-bold text-warning">{{ $requistedStudyLeaveDays ?? 0 }}</h3>
                    <small class="text-muted">days</small>
                </div>
            </div>

            <!-- Remaining Study Leave Duration -->
            <div class="col-md-4">
                <div class="duration-info-card h-100 border rounded-3 p-3 text-center bg-light">
                    <div class="duration-icon mb-2">
                        @php
                            $maxStudyLeaveDays = 1095; // 3 years = 1095 days
                            $remainingDays = $maxStudyLeaveDays - ($totalStudyLeaveDays ?? 0) - ($requistedStudyLeaveDays ?? 0);
                            $iconClass = $remainingDays > 365 ? 'text-success' : ($remainingDays > 0 ? 'text-warning' : 'text-danger');
                        @endphp
                        <i class="fas fa-calendar-plus fa-2x {{ $iconClass }}"></i>
                    </div>
                    <h6 class="text-muted mb-2 small">Remaining Study Leave Available</h6>
                    <h3 class="mb-0 fw-bold {{ $iconClass }}">{{ $remainingDays }}</h3>
                    <small class="text-muted">days (out of {{ $maxStudyLeaveDays }} total)</small>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-muted">Study Leave Utilization</span>
                @php
                    $utilizationPercentage = (($totalStudyLeaveDays ?? 0) + ($requistedStudyLeaveDays ?? 0)) / $maxStudyLeaveDays * 100;
                    $utilizationPercentage = min($utilizationPercentage, 100); // Cap at 100%
                @endphp
                <span class="small fw-semibold">{{ number_format($utilizationPercentage, 1) }}%</span>
            </div>
            <div class="progress" style="height: 20px;">
                @php
                    $appliedPercentage = ($totalStudyLeaveDays ?? 0) / $maxStudyLeaveDays * 100;
                    $requestedPercentage = ($requistedStudyLeaveDays ?? 0) / $maxStudyLeaveDays * 100;
                    $progressBarClass = $utilizationPercentage > 90 ? 'bg-danger' : ($utilizationPercentage > 70 ? 'bg-warning' : 'bg-success');
                @endphp
                <!-- Already Applied -->
                <div class="progress-bar bg-primary" role="progressbar" 
                     style="width: {{ $appliedPercentage }}%"
                     aria-valuenow="{{ $appliedPercentage }}" aria-valuemin="0" aria-valuemax="100"
                     data-bs-toggle="tooltip" title="Applied: {{ $totalStudyLeaveDays ?? 0 }} days">
                </div>
                <!-- Current Request -->
                <div class="progress-bar bg-warning" role="progressbar" 
                     style="width: {{ $requestedPercentage }}%"
                     aria-valuenow="{{ $requestedPercentage }}" aria-valuemin="0" aria-valuemax="100"
                     data-bs-toggle="tooltip" title="Requesting: {{ $requistedStudyLeaveDays ?? 0 }} days">
                </div>
            </div>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-muted">
                    <i class="fas fa-square text-primary me-1"></i>Applied: {{ $totalStudyLeaveDays ?? 0 }} days
                </small>
                <small class="text-muted">
                    <i class="fas fa-square text-warning me-1"></i>Requesting: {{ $requistedStudyLeaveDays ?? 0 }} days
                </small>
                <small class="text-muted">
                    <i class="fas fa-square text-success me-1"></i>Remaining: {{ $remainingDays }} days
                </small>
            </div>
        </div>

        <!-- Warning if exceeding limit -->
        @if($remainingDays < 0)
            <div class="alert alert-danger mt-3 mb-0">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Warning:</strong> This request exceeds the maximum study leave duration of {{ $maxStudyLeaveDays }} days (3 years).
            </div>
        @elseif($remainingDays < 180)
            <div class="alert alert-warning mt-3 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Notice:</strong> Only {{ $remainingDays }} days of study leave remaining.
            </div>
        @endif
    </div>
</div>

<style>
    .text-maroon {
        color: #800000;
    }
    
    .icon-gold {
        color: #ffd700;
    }
    
    .duration-info-card {
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .duration-info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .duration-icon i {
        transition: all 0.3s ease;
    }
    
    .duration-info-card:hover .duration-icon i {
        transform: scale(1.1);
    }
    
    .progress {
        border-radius: 10px;
        overflow: hidden;
        background-color: #e9ecef;
    }
    
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>
