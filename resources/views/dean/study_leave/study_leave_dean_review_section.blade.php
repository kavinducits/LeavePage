<div class="card mt-4">
    <div class="card-header bg-primary text-white fw-semibold">
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
                <input class="form-check-input" type="radio" name="dean_recommend" id="dean_recommendYes" value="1" 
                       {{ optional($draft_study_leave)->dean_leave_recommendation_status == 1 ? 'checked' : '' }}
                       {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="dean_recommendYes">
                    Yes
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="dean_recommend" id="dean_recommendNo" value="0" 
                       {{ optional($draft_study_leave)->dean_leave_recommendation_status !== null && optional($draft_study_leave)->dean_leave_recommendation_status == 0 ? 'checked' : '' }}
                       {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="dean_recommendNo">
                    No
                </label>
            </div>
        </div>

        <!-- Any other remarks -->
        <div class="mb-4">
            <label for="dean_remarks" class="form-label fw-semibold">
                Any other remarks
            </label>
            <textarea class="form-control" id="dean_remarks" name="dean_remarks" rows="3" 
                      placeholder="Add comments. Required when recommendation is No." {{ ($readonly ?? false) ? 'readonly' : '' }}>{{ optional($draft_study_leave)->dean_remarks ?? '' }}</textarea>
            <div class="invalid-feedback">
                Please provide remarks when leave is not recommended.
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recommendYes = document.getElementById('dean_recommendYes');
    const recommendNo = document.getElementById('dean_recommendNo');
    const remarksTextarea = document.getElementById('dean_remarks');

    if (!recommendYes || !recommendNo || !remarksTextarea) {
        return;
    }

    function toggleRemarksRequirement() {
        if (recommendNo.checked) {
            remarksTextarea.setAttribute('required', 'required');
        } else {
            remarksTextarea.removeAttribute('required');
            remarksTextarea.classList.remove('is-invalid');
        }
    }

    recommendYes.addEventListener('change', toggleRemarksRequirement);
    recommendNo.addEventListener('change', toggleRemarksRequirement);
    toggleRemarksRequirement();
});
</script>
        
