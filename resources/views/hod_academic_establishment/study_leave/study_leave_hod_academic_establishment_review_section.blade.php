 <!-- HOD Review Section -->

 <div class="card mt-4">
     <div class="card-header bg-primary text-white fw-semibold">
         <i class="fas fa-clipboard-check me-2"></i>Head Of Academic Establishment Review
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

         <!-- Any other remarks -->
         <div class="mb-4">
             <label for="registrar_remarks" class="form-label fw-semibold">
                 Any other remarks
             </label>
             <textarea class="form-control" id="registrar_remarks" name="registrar_remarks" rows="3"
                placeholder="Add comments. Required when recommendation is No." {{ $readonly ?? false ? 'readonly' : '' }}>{{ $draft_study_leave->registrar_remarks ?? '' }}</textarea>
            <div class="invalid-feedback">
                Please provide remarks when leave is not recommended.
            </div>
         </div>

     </div>
 </div>
 <script>
     document.addEventListener('DOMContentLoaded', function() {
         const recommendYes = document.getElementById('recommendYes');
         const recommendNo = document.getElementById('recommendNo');
         const remarksTextarea = document.getElementById('registrar_remarks');

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
