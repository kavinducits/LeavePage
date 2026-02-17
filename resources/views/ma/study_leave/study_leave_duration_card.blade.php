<!-- Study Leave Duration Card Component -->
<div class="card shadow-sm rounded-3 border-0 mb-4">
    <div class="card-body">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="duration-info-card h-100 border rounded-3 p-3 text-center bg-light">
                    <div class="duration-icon mb-2">
                        <i class="fas fa-calendar-check fa-2x text-primary"></i>
                    </div>
                    <h6 class="text-muted mb-2 small">Taken Study Leave</h6>
                    <h3 class="mb-0 fw-bold text-primary">
                        {{ $totalStudyLeaveDuration['months'] ?? 0 }}
                        <small class="fw-normal">months</small>
                        {{ $totalStudyLeaveDuration['days'] ?? 0 }}
                        <small class="fw-normal">days</small>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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
</style>
