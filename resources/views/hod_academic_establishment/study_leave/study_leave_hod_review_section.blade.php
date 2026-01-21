 <!-- HOD Review Section -->

 <div class="card mt-4">
     <div class="card-header card-header-dark text-white fw-semibold">
         <i class="fas fa-clipboard-check me-2"></i>Deputy Register Review & Recommendation
     </div>
     <div class="card-body">


         <!-- Question 4 - Recommendation -->
         <div class="mb-4">
             <label class="form-label fw-semibold">
                 Leave is recommended
                 <span class="text-danger">*</span>
             </label>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_recommend" id="recommendYes" value="yes"
                     {{ isset($draft_study_leave->hod_recommend) && $draft_study_leave->hod_recommend == 'yes' ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="recommendYes">
                     Yes
                 </label>
             </div>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="hod_recommend" id="recommendNo" value="no"
                     {{ isset($draft_study_leave->hod_recommend) && $draft_study_leave->hod_recommend == 'no' ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="recommendNo">
                     No
                 </label>
             </div>
         </div>

         <!-- Conditional: If not recommended -->
         <div class="mb-4" id="notRecommendReasonDiv"
             style="display: {{ optional($draft_study_leave)->hod_recommend == 'no' || optional($draft_study_leave)->hod_not_recommend_reason ? 'block' : 'none' }};">
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
