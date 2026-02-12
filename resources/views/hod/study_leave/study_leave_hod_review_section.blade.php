 <!-- HOD Review Section -->

 <div class="card mt-4">
     <div class="card-header card-header-dark text-white fw-semibold">
         <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
     </div>
     <div class="card-body">

         <!-- Question 1 -->
         <div class="mb-4">
             <label class="form-label fw-semibold">
                 Whether adequate staff available for the continuation of academic programs during the period of
                 applicant's leave?
                 <span class="text-danger">*</span>
             </label>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_adequate_staff_available" id="adequateStaffYes"
                     value="1"
                     {{ isset($draft_study_leave->hod_adequate_staff_available) && $draft_study_leave->hod_adequate_staff_available == 1 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="adequateStaffYes">
                     Yes
                 </label>
             </div>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_adequate_staff_available" id="adequateStaffNo"
                     value="0"
                     {{ isset($draft_study_leave->hod_adequate_staff_available) && $draft_study_leave->hod_adequate_staff_available !== null && $draft_study_leave->hod_adequate_staff_available == 0 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="adequateStaffNo">
                     No
                 </label>
             </div>
         </div>

         <!-- Question 2 -->
         <div class="mb-4">
             <label class="form-label fw-semibold">
                 Whether satisfactory agreements can be made to cover applicant's teaching activities and other
                 commitments?
                 <span class="text-danger">*</span>
             </label>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_teaching_covered" id="teachingCoveredYes"
                     value="1"
                     {{ isset($draft_study_leave->hod_teaching_covered) && $draft_study_leave->hod_teaching_covered == 1 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="teachingCoveredYes">
                     Yes
                 </label>
             </div>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_teaching_covered" id="teachingCoveredNo"
                     value="0"
                     {{ isset($draft_study_leave->hod_teaching_covered) && $draft_study_leave->hod_teaching_covered !== null && $draft_study_leave->hod_teaching_covered == 0 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="teachingCoveredNo">
                     No
                 </label>
             </div>
         </div>

         <!-- Question 3 -->
         <div class="mb-4">
             <label class="form-label fw-semibold">
                 Whether the applicant has served at least one (01) year in the Department?
                 <span class="text-danger">*</span>
             </label>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_service_period" id="servicePeriodYes"
                     value="1"
                     {{ isset($draft_study_leave->hod_service_period) && $draft_study_leave->hod_service_period == 1 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="servicePeriodYes">
                     Yes
                 </label>
             </div>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_service_period" id="servicePeriodNo"
                     value="0"
                     {{ isset($draft_study_leave->hod_service_period) && $draft_study_leave->hod_service_period !== null && $draft_study_leave->hod_service_period == 0 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="servicePeriodNo">
                     No
                 </label>
             </div>
         </div>

         <!-- Question 4 - Recommendation -->
         <div class="mb-4">
             <label class="form-label fw-semibold">
                 Leave is recommended
                 <span class="text-danger">*</span>
             </label>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_recommend" id="recommendYes" value="1"
                     {{ isset($draft_study_leave->hod_recommend) && $draft_study_leave->hod_recommend == 1 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="recommendYes">
                     Yes
                 </label>
             </div>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_recommend" id="recommendNo" value="0"
                     {{ isset($draft_study_leave->hod_recommend) && $draft_study_leave->hod_recommend !== null && $draft_study_leave->hod_recommend == 0 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="recommendNo">
                     No
                 </label>
             </div>
         </div>

         <!-- Conditional: If not recommended -->
         <div class="mb-4" id="notRecommendReasonDiv"
             style="display: {{ (optional($draft_study_leave)->hod_recommend !== null && optional($draft_study_leave)->hod_recommend == 0) || optional($draft_study_leave)->hod_not_recommend_reason ? 'block' : 'none' }};">
             <label for="hod_not_recommend_reason" class="form-label fw-semibold">
                 If not recommended, please give reasons
                 <span class="text-danger">*</span>
             </label>
             <textarea class="form-control" id="hod_not_recommend_reason" name="hod_not_recommend_reason" rows="4"
                 placeholder="Please provide detailed reasons for not recommending this leave"
                 {{ $readonly ?? false ? 'readonly' : '' }}>{{ optional($draft_study_leave)->hod_not_recommend_reason ?? '' }}</textarea>
             <div class="invalid-feedback">
                 Please provide reasons for not recommending.
             </div>
         </div>

         <!-- Any other remarks -->
         <div class="mb-4">
             <label for="hod_remarks" class="form-label fw-semibold">
                 Any other remarks
             </label>
             <textarea class="form-control" id="hod_remarks" name="hod_remarks" rows="3"
                 placeholder="Add any additional comments or remarks (optional)" {{ $readonly ?? false ? 'readonly' : '' }}>{{ $draft_study_leave->hod_remarks ?? '' }}</textarea>
         </div>

     </div>
 </div>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const recommendYes = document.getElementById('recommendYes');
         const recommendNo = document.getElementById('recommendNo');
         const notRecommendReasonDiv = document.getElementById('notRecommendReasonDiv');
         const notRecommendReasonTextarea = document.getElementById('hod_not_recommend_reason');

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
