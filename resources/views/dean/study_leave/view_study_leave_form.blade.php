@extends('layouts.screen1')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-dark">
                <i class="fas fa-file-alt me-2 text-primary"></i>Study Leave Application Review
            </h2>
            <p class="text-muted mb-0">Reference No: {{ $draft_study_leave->reference_no }}</p>
        </div>
        <a href="{{ route('dean.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

     <!-- Study Leave Details (readonly) -->
    @include('StudyLeave.basic_info_form', ['readonly' => true])
    @include('StudyLeave.details_form', ['readonly' => true])
    @include('StudyLeave.working_covering_persons_form', ['readonly' => true])

    <!-- Dean Review Section -->
    <form action="{{ route('dean.view.studyLeave.approve', $draft_study_leave->id) }}" method="POST" id="deanReviewForm">
        @csrf
        
        <div class="card mt-4">
            <div class="card-header card-header-dark text-white fw-semibold">
                <i class="fas fa-clipboard-check me-2"></i>Dean Review & Recommendation
            </div>
            <div class="card-body">
                
                <!-- Question 1 - Recommendation -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Is leave recommended?
                        <span class="text-danger">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="dean_recommend" id="recommendYes" value="yes" required>
                        <label class="form-check-label" for="recommendYes">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="dean_recommend" id="recommendNo" value="no" required>
                        <label class="form-check-label" for="recommendNo">
                            No
                        </label>
                    </div>
                </div>

                <!-- Conditional: If not recommended -->
                <div class="mb-4" id="notRecommendReasonDiv" style="display: none;">
                    <label for="dean_not_recommend_reason" class="form-label fw-semibold">
                        If not recommended, please give reasons
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="dean_not_recommend_reason" name="dean_not_recommend_reason" rows="4" 
                              placeholder="Please provide detailed reasons for not recommending this leave"></textarea>
                    <div class="invalid-feedback">
                        Please provide reasons for not recommending.
                    </div>
                </div>

                <!-- Any other remarks -->
                <div class="mb-4">
                    <label for="dean_remarks" class="form-label fw-semibold">
                        Any other remarks
                    </label>
                    <textarea class="form-control" id="dean_remarks" name="dean_remarks" rows="3" 
                              placeholder="Add any additional comments or remarks (optional)"></textarea>
                </div>

            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('dean.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>

                    <div class="text-end">
                        <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>Submit Review & Forward to VC
                        </button>
                        
                        <div class="card mt-2" style="min-width: 280px;">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center">
                                    <strong>Forward to,&nbsp;</strong>
                                    <div>
                                        <div class="fw-semibold">Vice Chancellor</div>
                                        <div class="text-muted small">Final Approval</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.card-header-dark {
    background: linear-gradient(135deg, #212529 0%, #343a40 100%);
    color: white;
    border-bottom: 3px solid #0d6efd;
}

.text-dark {
    color: #212529;
}

.text-primary {
    color: #0d6efd;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-control[readonly], .form-select:disabled, select:disabled {
    background-color: #e9ecef !important;
    color: #495057 !important;
    opacity: 1 !important;
}

.btn-lg {
    padding: 0.5rem 1.5rem;
    font-size: 1rem;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recommendYes = document.getElementById('recommendYes');
    const recommendNo = document.getElementById('recommendNo');
    const notRecommendReasonDiv = document.getElementById('notRecommendReasonDiv');
    const notRecommendReasonTextarea = document.getElementById('dean_not_recommend_reason');
    const form = document.getElementById('deanReviewForm');

    // Show/hide reason textarea based on recommendation
    function toggleReasonField() {
        if (recommendNo.checked) {
            notRecommendReasonDiv.style.display = 'block';
            notRecommendReasonTextarea.setAttribute('required', 'required');
        } else {
            notRecommendReasonDiv.style.display = 'none';
            notRecommendReasonTextarea.removeAttribute('required');
            notRecommendReasonTextarea.value = '';
            notRecommendReasonTextarea.classList.remove('is-invalid');
        }
    }

    recommendYes.addEventListener('change', toggleReasonField);
    recommendNo.addEventListener('change', toggleReasonField);

    // Form validation
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Check if "No" is selected and reason is empty
        if (recommendNo.checked) {
            const reasonValue = notRecommendReasonTextarea.value.trim();
            if (!reasonValue) {
                e.preventDefault();
                notRecommendReasonTextarea.classList.add('is-invalid');
                notRecommendReasonTextarea.focus();
                isValid = false;
                alert('Please provide reasons for not recommending this leave.');
                return;
            }
        }

        // Confirm submission
        if (isValid) {
            const confirmMessage = recommendYes.checked 
                ? 'Are you sure you want to submit your review and forward this application to the Vice Chancellor?' 
                : 'Are you sure you want to submit your review with a NOT RECOMMENDED status?';
            
            if (!confirm(confirmMessage)) {
                e.preventDefault();
            }
        }
    });

    // Clear invalid state when user starts typing
    notRecommendReasonTextarea.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
        }
    });
});
</script>
@endsection
