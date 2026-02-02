<!-- Progress Bar Component -->
<div class="study-leave-progress-bar" id="studyLeaveProgressBar">
    <div class="progress-container">
        <div class="progress-steps">
            <!-- Step 1: Personal Details -->
            <div class="progress-step {{ $currentStep >= 1 ? 'active' : '' }} {{ $currentStep > 1 ? 'completed' : '' }}" data-step="1">
                <div class="step-circle">
                    @if($currentStep > 1)
                        <i class="fas fa-check"></i>
                    @else
                        <span>1</span>
                    @endif
                </div>
                <div class="step-label">Personal Details</div>
            </div>
            <!-- Connector Line -->
            <!-- Connector Line -->
            <div class="progress-line {{ $currentStep > 1 ? 'completed' : '' }}"></div>
            
            <!-- Step 2: Study Leave Details -->
            <div class="progress-step {{ $currentStep >= 2 ? 'active' : '' }} {{ $currentStep > 2 ? 'completed' : '' }}" data-step="2">
                <div class="step-circle">
                    @if($currentStep > 2)
                        <i class="fas fa-check"></i>
                    @else
                        <span>2</span>
                    @endif
                </div>
                <div class="step-label">Study Leave Details</div>
            </div>
            
            <!-- Connector Line -->
            <div class="progress-line {{ $currentStep > 2 ? 'completed' : '' }}"></div>
            
            <!-- Step 3: Work Coverage -->
            <div class="progress-step {{ $currentStep >= 3 ? 'active' : '' }} {{ $currentStep > 3 ? 'completed' : '' }}" data-step="3">
                <div class="step-circle">
                    @if($currentStep > 3)
                        <i class="fas fa-check"></i>
                    @else
                        <span>3</span>
                    @endif
                </div>
                <div class="step-label">Work Coverage</div>
            </div>
            
            <!-- Connector Line -->
            <div class="progress-line {{ $currentStep > 3 ? 'completed' : '' }}"></div>
            
            <!-- Step 4: Summary & Submit -->
            <div class="progress-step {{ $currentStep >= 4 ? 'active' : '' }} {{ $currentStep > 4 ? 'completed' : '' }}" data-step="4">
                <div class="step-circle">
                    @if($currentStep > 4)
                        <i class="fas fa-check"></i>
                    @else
                        <span>4</span>
                    @endif
                </div>
                <div class="step-label">Summary & Submit</div>
            </div>
        </div>
        
        <!-- Progress Percentage -->
        <div class="progress-bar-track">
            <div class="progress-bar-fill" style="width: {{ (($currentStep - 1) / 3) * 100 }}%"></div>
        </div>
    </div>
</div>

<style>
/* Floating Progress Bar Styles */
.study-leave-progress-bar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    background: linear-gradient(135deg, #e8e8e8 0%, #d3d3d3 100%);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    padding: 6px 20px;
    animation: slideDown 0.4s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.progress-container {
    max-width: 1200px;
    margin: 0 auto;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    position: relative;
}

.progress-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 0 0 auto;
    transition: all 0.3s ease;
}

.step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(0, 0, 0, 0.1);
    border: 2px solid rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #666;
    font-size: 14px;
    transition: all 0.3s ease;
    margin-bottom: 4px;
}

.progress-step.active .step-circle {
    background: #0d6efd;
    border-color: #0d6efd;
    color: white;
    box-shadow: 0 0 15px rgba(13, 110, 253, 0.5);
    transform: scale(1.1);
}

.progress-step.completed .step-circle {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.step-label {
    font-size: 11px;
    color: #666;
    font-weight: 500;
    text-align: center;
    max-width: 120px;
    line-height: 1.1;
    transition: all 0.3s ease;
}

.progress-step.active .step-label {
    color: #0d6efd;
    font-weight: 600;
    text-shadow: none;
}

.progress-line {
    flex: 1;
    height: 3px;
    background: rgba(0, 0, 0, 0.15);
    position: relative;
    margin: 0 10px;
    margin-bottom: 36px; /* Align with circles */
    transition: all 0.5s ease;
}

.progress-line.completed {
    background: #28a745;
}

.progress-bar-track {
    width: 100%;
    height: 4px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 10px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    border-radius: 10px;
    transition: width 0.5s ease;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
}

/* Add spacing for the fixed progress bar */
body {
    padding-top: 85px !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .study-leave-progress-bar {
        padding: 10px 15px;
    }
    
    .step-circle {
        width: 35px;
        height: 35px;
        font-size: 14px;
    }
    
    .step-label {
        font-size: 11px;
        max-width: 80px;
    }
    
    .progress-line {
        margin: 0 5px;
        margin-bottom: 43px;
    }
    
    .progress-bar-track {
        height: 6px;
    }
    
    body {
        padding-top: 120px !important;
    }
}

@media (max-width: 576px) {
    .step-label {
        font-size: 10px;
        max-width: 60px;
    }
    
    .step-circle {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
    
    .progress-line {
        margin-bottom: 38px;
    }
}

/* Print Styles */
@media print {
    .study-leave-progress-bar {
        display: none;
    }
    
    body {
        padding-top: 0 !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll behavior when progress bar is present
    const progressBar = document.getElementById('studyLeaveProgressBar');
    
    if (progressBar) {
        // Add smooth entrance animation
        setTimeout(() => {
            progressBar.style.opacity = '1';
        }, 100);
    }
});
</script>
