 <!-- HOD Review Section -->

 <div class="card mt-4">
     <div class="card-header bg-primary text-white fw-semibold">
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
                 <input class="form-check-input" type="radio" name="hod_recommend" id="hodRecommendYes" value="1"
                     {{ isset($draft_study_leave->hod_recommend) && $draft_study_leave->hod_recommend == 1 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="hodRecommendYes">
                     Yes
                 </label>
             </div>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_recommend" id="hodRecommendNo" value="0"
                     {{ isset($draft_study_leave->hod_recommend) && $draft_study_leave->hod_recommend !== null && $draft_study_leave->hod_recommend == 0 ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="hodRecommendNo">
                     No
                 </label>
             </div>
         </div>

         <!-- Any other remarks -->
         <div class="mb-4">
             <label for="hod_remarks" class="form-label fw-semibold">
                 Any other remarks
             </label>
             <textarea class="form-control" id="hod_remarks" name="hod_remarks" rows="3"
                placeholder="Add comments. Required when recommendation is No." {{ $readonly ?? false ? 'readonly' : '' }}>{{ $draft_study_leave->hod_remarks ?? '' }}</textarea>
            <div class="invalid-feedback">
                Please provide remarks when leave is not recommended.
            </div>
         </div>

     </div>
 </div>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const recommendYes = document.getElementById('hodRecommendYes');
         const recommendNo = document.getElementById('hodRecommendNo');
         const remarksTextarea = document.getElementById('hod_remarks');

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
