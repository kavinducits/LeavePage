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
                <input class="form-check-input" type="radio" name="dean_recommend" id="dean_recommendYes" value="yes" 
                       {{ optional($draft_study_leave)->dean_leave_recommendation_status == 'yes' ? 'checked' : '' }}
                       {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="recommendYes">
                    Yes
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="dean_recommend" id="dean_recommendNo" value="no" 
                       {{ optional($draft_study_leave)->dean_leave_recommendation_status == 'no' ? 'checked' : '' }}
                       {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="recommendNo">
                    No
                </label>
            </div>
        </div>

        <!-- Conditional: If not recommended -->
        <div class="mb-4" id="dean_notRecommendReasonDiv" style="display: {{ optional($draft_study_leave)->dean_leave_recommendation_status == 'no' ? 'block' : 'none' }};">
            <label for="dean_not_recommend_reason" class="form-label fw-semibold">
                If not recommended, please give reasons
                <span class="text-danger">*</span>
            </label>
            <textarea class="form-control" id="dean_not_recommend_reason" name="dean_not_recommend_reason" rows="4" 
                      placeholder="Please provide detailed reasons for not recommending this leave" {{ ($readonly ?? false) ? 'readonly' : '' }}>{{ optional($draft_study_leave)->dean_not_recommended_reason ?? '' }}</textarea>
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
                      placeholder="Add any additional comments or remarks (optional)" {{ ($readonly ?? false) ? 'readonly' : '' }}>{{ optional($draft_study_leave)->dean_remarks ?? '' }}</textarea>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recommendYes = document.getElementById('dean_recommendYes');
    const recommendNo = document.getElementById('dean_recommendNo');
    const notRecommendReasonDiv = document.getElementById('dean_notRecommendReasonDiv');
    const notRecommendReasonTextarea = document.getElementById('dean_not_recommend_reason');

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
});
</script>
        
