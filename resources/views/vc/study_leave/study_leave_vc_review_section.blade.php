<div class="card mt-4">
    <div class="card-header bg-primary text-white fw-semibold">
        <i class="fas fa-clipboard-check me-2"></i>VC Review & Recommendation
    </div>
    <div class="card-body">

        <!-- Question 1 - Recommend to Committee -->
        <div class="mb-4">
            <label class="form-label fw-semibold">
                Recommended to submit to Leave and Awards Committee?
                <span class="text-danger">*</span>
            </label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="vc_recommend_submit_to_committee"
                    id="vcRecommendCommitteeYes" value="1"
                    {{ optional($draft_study_leave)->vc_recommend_submit_to_committee == 1 ? 'checked' : '' }}
                    {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="vcRecommendCommitteeYes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="vc_recommend_submit_to_committee"
                    id="vcRecommendCommitteeNo" value="0"
                    {{ optional($draft_study_leave)->vc_recommend_submit_to_committee !== null && optional($draft_study_leave)->vc_recommend_submit_to_committee == 0 ? 'checked' : '' }}
                    {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="vcRecommendCommitteeNo">No</label>
            </div>
        </div>

        <!-- Question 2 - Council Approval Decision -->
        <div class="mb-4">
            <label class="form-label fw-semibold">
                Council Covering Approval Status
                <span class="text-danger">*</span>
            </label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="vc_council_covering_approval_status"
                    id="vcCouncilApprovalYes" value="1"
                    {{ optional($draft_study_leave)->vc_council_covering_approval_status == 1 ? 'checked' : '' }}
                    {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="vcCouncilApprovalYes">Yes</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="vc_council_covering_approval_status"
                    id="vcCouncilApprovalNo" value="0"
                    {{ optional($draft_study_leave)->vc_council_covering_approval_status !== null && optional($draft_study_leave)->vc_council_covering_approval_status == 0 ? 'checked' : '' }}
                    {{ ($readonly ?? false) ? 'disabled' : 'required' }}>
                <label class="form-check-label" for="vcCouncilApprovalNo">No</label>
            </div>
        </div>

        <!-- Any other remarks -->
        <div class="mb-4">
            <label for="vc_remarks" class="form-label fw-semibold">
                Any other remarks
            </label>
            <textarea class="form-control" id="vc_remarks" name="vc_remarks" rows="3"
                placeholder="Add comments. Required when recommendation is No."
                {{ ($readonly ?? false) ? 'readonly' : '' }}>{{ optional($draft_study_leave)->vc_remarks ?? '' }}</textarea>
            <div class="invalid-feedback">
                Please provide remarks when leave is not recommended.
            </div>
        </div>

    </div>
</div>
