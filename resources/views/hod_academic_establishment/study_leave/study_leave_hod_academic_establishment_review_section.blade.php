 <!-- HOD Review Section -->

 <div class="card mt-4">
     <div class="card-header bg-primary text-white fw-semibold">
         <i class="fas fa-clipboard-check me-2"></i>Registrar Review & Recommendation
     </div>
     <div class="card-body">

         <!-- Recommendation -->
         <div class="mb-4">
             <label class="form-label fw-semibold">
                 Leave is recommended
                 <span class="text-danger">*</span>
             </label>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="registrar_recommendation" id="recommendYes" value="1"
                     {{ isset($draft_study_leave->registrar_recommendation) && $draft_study_leave->registrar_recommendation == '1' ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="recommendYes">
                     Yes
                 </label>
             </div>
             <div class="form-check">
                 <input class="form-check-input" type="radio" name="registrar_recommendation" id="recommendNo" value="0"
                     {{ isset($draft_study_leave->registrar_recommendation) && $draft_study_leave->registrar_recommendation == '0' && $draft_study_leave->registrar_recommendation !== null ? 'checked' : '' }}
                     {{ $readonly ?? false ? 'disabled' : 'required' }}>
                 <label class="form-check-label" for="recommendNo">
                     No
                 </label>
             </div>
         </div>

         <!-- Conditional: If not recommended -->
         <div class="mb-4" id="notRecommendReasonDiv"
             style="display: {{ optional($draft_study_leave)->registrar_recommendation == '0' && optional($draft_study_leave)->registrar_recommendation !== null || optional($draft_study_leave)->registrar_not_recommend_reason ? 'block' : 'none' }};">
             <label for="registrar_not_recommend_reason" class="form-label fw-semibold">
                 If not recommended, please give reasons
                 <span class="text-danger">*</span>
             </label>
             <textarea class="form-control" id="registrar_not_recommend_reason" name="registrar_not_recommend_reason" rows="4"
                 placeholder="Please provide detailed reasons for not recommending this leave"
                 {{ $readonly ?? false ? 'readonly' : '' }}>{{ optional($draft_study_leave)->registrar_not_recommend_reason ?? '' }}</textarea>
             <div class="invalid-feedback">
                 Please provide reasons for not recommending.
             </div>
         </div>

         <!-- Any other remarks -->
         <div class="mb-4">
             <label for="registrar_remarks" class="form-label fw-semibold">
                 Any other remarks
             </label>
             <textarea class="form-control" id="registrar_remarks" name="registrar_remarks" rows="3"
                 placeholder="Add any additional comments or remarks (optional)" {{ $readonly ?? false ? 'readonly' : '' }}>{{ $draft_study_leave->registrar_remarks ?? '' }}</textarea>
         </div>

     </div>
 </div>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const recommendYes = document.getElementById('recommendYes');
         const recommendNo = document.getElementById('recommendNo');
         const notRecommendReasonDiv = document.getElementById('notRecommendReasonDiv');
         const notRecommendReasonTextarea = document.getElementById('registrar_not_recommend_reason');

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
