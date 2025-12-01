 <!-- Form Card -->
    
        <div class="card mb-4">

            <!-- Card Header -->
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Details of the Study Leave
            </div>
            <div class="card-body">
                <div class="row g-3">
                
                <!-- Study Leave Location -->
                <div class="col-12 mb-3">
                    <label class="form-label fw-semibold d-block">Is your study leave in Sri Lanka or abroad? <span class="text-danger">*</span></label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="study_location" id="location_sri_lanka" value="Sri Lanka"
                                @checked(old('study_location', $draft_study_leave->study_location ?? '') === 'Sri Lanka') 
                                required 
                                {{ $readonly ?? true ? 'disabled' : '' }}>
                            <label class="form-check-label" for="location_sri_lanka">In Sri Lanka</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="study_location" id="location_abroad" value="Abroad"
                                @checked(old('study_location', $draft_study_leave->study_location ?? '') === 'Abroad') 
                                required 
                                {{ $readonly ?? true ? 'disabled' : '' }}>
                            <label class="form-check-label" for="location_abroad">Abroad</label>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block" id="study_location_error" style="display: none !important;">
                        Please select study leave location.
                    </div>
                </div>
                
                          <!-- University or the Institute -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">University or the Institute <span class="text-danger">*</span></label>
                    <input type="text" name="university_institute" class="form-control" 
                           value="{{ $draft_study_leave->university_institute ?? '' }}" 
                           minlength="3" 
                           maxlength="200" 
                           required 
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    <div class="invalid-feedback">
                        Please enter the university or institute name (3-200 characters).
                    </div>
                </div>
                
                <!-- Country and Field of Study -->
                <div class="col-md-6" id="country_field" style="transition: all 0.3s ease;">
                    <label class="form-label fw-semibold">Country <span class="text-danger" id="country_required">*</span></label>
                    <input type="text" name="country" id="country" class="form-control" 
                           value="{{ $draft_study_leave->country ?? '' }}" 
                           minlength="2" 
                           maxlength="100" 
                           pattern="[A-Za-z\s\-]+" 
                           title="Country name should contain only letters, spaces, and hyphens"
                           required 
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    <div class="invalid-feedback">
                        Please enter a valid country name (letters only, 2-100 characters).
                    </div>
                </div>

                <!-- Passport Details (conditional - shown only for "Abroad") -->
                <div class="col-md-6" id="passport_no_field" style="display: none;">
                    <label class="form-label fw-semibold">Passport Number <span class="text-danger">*</span></label>
                    <input type="text" name="passport_no" id="passport_no" class="form-control" 
                           value="{{ $draft_study_leave->passport_no ?? '' }}" 
                           minlength="6" 
                           maxlength="20" 
                           pattern="[A-Z0-9]+" 
                           title="Passport number should contain only uppercase letters and numbers"
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    <div class="invalid-feedback">
                        Please enter a valid passport number (6-20 characters, uppercase letters and numbers only).
                    </div>
                </div>

                <div class="col-md-6" id="passport_validity_field" style="display: none;">
                    <label class="form-label fw-semibold">Passport Validity Date <span class="text-danger">*</span></label>
                    <input type="date" name="passport_validity" id="passport_validity" class="form-control" 
                           value="{{ $draft_study_leave->passport_validity ?? '' }}" 
                           min="{{ date('Y-m-d') }}" 
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    <div class="invalid-feedback">
                        Please enter a valid passport expiry date (must be a future date).
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const studyLocationRadios = document.querySelectorAll('input[name="study_location"]');
                    const passportNoField = document.getElementById('passport_no_field');
                    const passportValidityField = document.getElementById('passport_validity_field');
                    const passportNoInput = document.getElementById('passport_no');
                    const passportValidityInput = document.getElementById('passport_validity');
                    
                    function updatePassportFields() {
                        const selectedLocation = document.querySelector('input[name="study_location"]:checked');
                        
                        if (selectedLocation && selectedLocation.value === 'Abroad') {
                            passportNoField.style.display = 'block';
                            passportValidityField.style.display = 'block';
                            passportNoInput.setAttribute('required', 'required');
                            passportValidityInput.setAttribute('required', 'required');
                        } else {
                            passportNoField.style.display = 'none';
                            passportValidityField.style.display = 'none';
                            passportNoInput.removeAttribute('required');
                            passportValidityInput.removeAttribute('required');
                            passportNoInput.value = '';
                            passportValidityInput.value = '';
                        }
                    }
                    
                    studyLocationRadios.forEach(radio => {
                        radio.addEventListener('change', updatePassportFields);
                    });
                    
                    // Initialize on page load
                    updatePassportFields();
                });
                </script>

                 <div class="col-md-6">
                    <label class="form-label fw-semibold">Field of study <span class="text-danger">*</span></label>
                    <input type="text" name="field_of_study" class="form-control" 
                           value="{{ $draft_study_leave->field_of_study ?? '' }}" 
                           minlength="3" 
                           maxlength="200" 
                           required 
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    <div class="invalid-feedback">
                        Please enter the field of study (3-200 characters).
                    </div>
                </div>
                     <!-- Details of the Study Program -->
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Degree Title</label>
                        <select name="degree_title" class="form-select" required {{ $readonly ?? true ? 'disabled' : '' }}>
                            <option value="" {{ ($draft_study_leave->degree_title ?? '') === '' ? 'selected' : '' }}>Select degree title</option>
                            <option value="MA" {{ ($draft_study_leave->degree_title ?? '') === 'MA' ? 'selected' : '' }}>M.A.</option>
                            <option value="MSc" {{ ($draft_study_leave->degree_title ?? '') === 'MSc' ? 'selected' : '' }}>M.Sc</option>
                            <option value="MBA" {{ ($draft_study_leave->degree_title ?? '') === 'MBA' ? 'selected' : '' }}>MBA</option>
                            <option value="MPhil" {{ ($draft_study_leave->degree_title ?? '') === 'MPhil' ? 'selected' : '' }}>M.Phil.</option>
                            <option value="MD" {{ ($draft_study_leave->degree_title ?? '') === 'MD' ? 'selected' : '' }}>M.D.</option>
                            <option value="PhD" {{ ($draft_study_leave->degree_title ?? '') === 'PhD' ? 'selected' : '' }}>PhD</option>
                            <option value="Other" {{ ($draft_study_leave->degree_title ?? '') === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <!-- Leave Type -->
                    <!--
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Leave Type</label>
                        
                        <select name="leave_type" class="form-select" required>
                            <option value=""  {{ $draft_study_leave->leave_type === '' ? 'selected' : '' }}>Select an option</option>
                            <option value="fresh" {{ $draft_study_leave->leave_type === 'fresh' ? 'selected' : '' }}>Fresh Study Leave</option>
                            <option value="extension" {{ $draft_study_leave->leave_type === 'extension' ? 'selected' : '' }}>Extension</option>
                        </select>
                    </div>
                -->
                   

                    <!-- Period of Study Leave Requested (From) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">From <span class="text-danger">*</span></label>
                        <input type="date" name="study_leave_from" id="study_leave_from" class="form-control" 
                               value="{{optional($draft_study_leave)->study_leave_from ?? ''}}" 
                               min="{{ date('Y-m-d') }}" 
                               required 
                               {{ $readonly ?? true ? 'readonly' : '' }}>
                        <div class="invalid-feedback">
                            Please select a valid start date.
                        </div>
                    </div>

                    <!-- Period of Study Leave Requested (To) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">To <span class="text-danger">*</span></label>
                        <input type="date" name="study_leave_to" id="study_leave_to" class="form-control" 
                               value="{{optional($draft_study_leave)->study_leave_to ?? ''}}" 
                               min="{{ date('Y-m-d') }}" 
                               required 
                               {{ $readonly ?? true ? 'readonly' : '' }}>
                        <div class="invalid-feedback">
                            Please select a valid end date (must be after start date).
                        </div>
                    </div>
                  




                <!-- Relevancy and Details of the Study Program -->

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Relevancy and Details of the Study Program</label>
                    <textarea name="study_program_details" class="form-control" rows="4" 
                              minlength="10" 
                              maxlength="1000" 
                              {{ $readonly ?? true ? 'readonly' : '' }}>{{ $draft_study_leave->study_program_details ?? '' }}</textarea>
                    <div class="invalid-feedback">
                        Please provide details of the study program (10-1000 characters).
                    </div>
                </div>

                 <!-- Attachment Instructions -->
                <div class="col-md-12">
                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> The placement offering letter and scholarship details should be attached to this application document as a PDF.
                    </div>
                    <label class="form-label fw-semibold mt-2">Attach PDF Documents <span class="text-danger">*</span></label>
                    <input type="file" name="placement_letter" id="attachments-input" class="form-control" 
                           accept="application/pdf" 
                           multiple 
                           {{ !empty($draft_study_leave->placement_letter) ? '' : 'required' }}
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    <div class="invalid-feedback">
                        Please upload a PDF document (placement letter/scholarship details).
                    </div>

                    <!-- Show previously uploaded file (when editing) -->
                    @if(!empty($draft_study_leave->placement_letter))
                        <div class="mt-2 d-flex justify-content-between align-items-center" id="existing-placement-letter">
                            <div>
                                <strong>Existing file:</strong>
                                <span class="ms-2">{{ basename($draft_study_leave->placement_letter) }}</span>
                            </div>
                            <div>
                                <a href="{{ route('StudyLeave.serveFile', ['type' => 'placement_letter', 'filename' => basename($draft_study_leave->placement_letter)]) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Open</a>
                                <button type="button" id="preview-existing-btn" class="btn btn-sm btn-outline-primary">Preview</button>
                            </div>
                        </div>
                    @endif

                    <div id="pdf-preview-list" class="mt-3"></div>

                    <div id="pdf-preview-embed" class="mt-3" style="display:none;">
                        <label class="form-label fw-semibold">Preview</label>
                        <div style="border:1px solid #dee2e6;">
                            <embed id="pdf-embed" src="" type="application/pdf" width="100%" height="600px">
                        </div>
                    </div>
                </div>


                 <!-- Type of Study Leave Requested -->
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Type of Study Leave Requested <span class="text-danger">*</span></label>
                        <select name="leave_payment_type" id="leave_payment_type" class="form-select" required {{ $readonly ?? true ? 'disabled' : '' }}>
                            <option value="" {{ $draft_study_leave->leave_payment_type === '' ? 'selected' : '' }} disabled>Select an option</option>
                            <option value="with Pay" {{ $draft_study_leave->leave_payment_type === 'with Pay' ? 'selected' : '' }}>With Pay</option>
                            <option value="without Pay" {{ $draft_study_leave->leave_payment_type === 'without Pay' ? 'selected' : '' }}>Without Pay</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select the type of study leave.
                        </div>
                    </div>
                    
                <!-- Loan Handling (conditional) - shown only for "without Pay" -->
                <div class="col-md-6" id="loan_handling_section">
                    <label for="loan_handling_details" class="form-label fw-semibold">
                        Paying of Loans taken from University of UPF? <span class="text-danger">*</span>
                       
                    </label>
                    <select class="form-select" id="loan_handling_details" name="loan_handling" {{ $readonly ?? true ? 'disabled' : '' }}>
                        <option value="" {{ empty(old('loan_handling', $draft_study_leave->loan_handling ?? '')) ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="Make Arrangements" {{ old('loan_handling', $draft_study_leave->loan_handling ?? '') === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="Not Make Arrangements" {{ old('loan_handling', $draft_study_leave->loan_handling ?? '') === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select an option for loan handling.
                    </div>
                </div>

                <!-- Funding Type -->
                <div class="col-md-6">
                        <label class="form-label fw-semibold"> Funding type <span class="text-danger">*</span></label>
                        <select name="funding_type" id="funding_type" class="form-select" required {{ $readonly ?? true ? 'disabled' : '' }} >
                            <option value="" {{ $draft_study_leave->funding_type ?? '' ? '' : 'selected' }} disabled>Select funding type</option>
                            <option value="self" {{ $draft_study_leave->funding_type === 'self' ? 'selected' : '' }}>Self-Funding</option>
                            <option value="scholarship" {{ $draft_study_leave->funding_type === 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a funding type.
                        </div>
                    </div>

                    <!-- Scholarship Details (conditional)- If Scholarship is selected in Funding Type -->
                    <div class="col-md-6" id="scholarship-details" style="display: none;">
                        <label class="form-label fw-semibold">Scholarship Source <span class="text-danger">*</span></label>
                        <select name="scholarship_source" id="scholarship_source" class="form-select" {{ $readonly ?? true ? 'disabled' : '' }} >
                            <option value="" {{ empty($draft_study_leave->scholarship_source) ? 'selected' : '' }} disabled>Select source</option>
                            <option value="agency" {{ ($draft_study_leave->scholarship_source ?? '') === 'agency' ? 'selected' : '' }}>Scholarship offering agency</option>
                            <option value="project" {{ ($draft_study_leave->scholarship_source ?? '') === 'project' ? 'selected' : '' }}>Funds from a project</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a scholarship source.
                        </div>
                    </div>

                    <!-- Additional fields based on Scholarship Source -->
                    <div class="col-md-6" id="scholarship-extra-details" style="display: none;">

                        <!-- Scholarship Amount (conditional) - If Scholarship offering agency is selected -->
                        <div id="scholarship-amount-group" style="display: none;">
                            <label class="form-label fw-semibold">Scholarship Amount <span class="text-danger">*</span></label>
                            <input type="number" name="scholarship_amount" id="scholarship_amount" class="form-control" 
                                   min="0.01" 
                                   step="0.01" 
                                   placeholder="Enter amount" 
                                   value="{{ $draft_study_leave->scholarship_amount ?? '' }}" 
                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                            <div class="invalid-feedback">
                                Please enter a valid scholarship amount (must be greater than 0).
                            </div>
                        </div>

                        <!-- Project Name (conditional) - If Funds from a project is selected -->
                        <div id="project-name-group" style="display: none;">
                            <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="project_name" class="form-control" 
                                   placeholder="Enter project name" 
                                   minlength="3" 
                                   maxlength="200" 
                                   value="{{ $draft_study_leave->project_name ?? '' }}" 
                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                            <div class="invalid-feedback">
                                Please enter the project name (3-200 characters).
                            </div>
                        </div>
                    </div>
                    <!-- Additional fields (conditional) - If Self-Funding is selected -->

                <!-- Air Passage Request -->
                <div class="col-md-6" id="self-funding-extra" style="display: none;">
                    <label class="form-label fw-semibold d-inline-block me-3">Requesting Air Passage from this University? <span class="text-danger">*</span></label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_yes" value="yes"
                                @checked(old('air_passage_request', $draft_study_leave->air_passage_request ?? '') === 'yes') {{ $readonly ?? true ? 'readonly' : '' }}>
                            <label class="form-check-label" for="air_passage_yes">YES</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_no" value="no" @checked(old('air_passage_request', $draft_study_leave->air_passage_request ?? '') === 'no') {{ $readonly ?? true ? 'readonly' : '' }}>
                            <label class="form-check-label" for="air_passage_no">NO</label>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block" id="air_passage_error" style="display: none !important;">
                        Please select an option for air passage request.
                    </div>
                </div>

                <!-- Warm Cloth Allowance Request -->
                <div class="col-md-6" id="self-funding-extra2" style="display: none;">
                    <label class="form-label fw-semibold d-inline-block me-3">Requesting Warm Cloth Allowance from this University? <span class="text-danger">*</span></label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_yes" value="yes"
                                @checked(old('warm_cloth_allowance_request', $draft_study_leave->warm_cloth_allowance_request ?? '') === 'yes')  {{ $readonly ?? true ? 'readonly' : '' }}>
                            <label class="form-check-label" for="warm_cloth_yes">YES</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_no" value="no"
                                @checked(old('warm_cloth_allowance_request', $draft_study_leave->warm_cloth_allowance_request ?? '') === 'no') {{ $readonly ?? true ? 'readonly' : '' }}>
                            <label class="form-check-label" for="warm_cloth_no">NO</label>
                        </div>
                    </div>
                    <div class="invalid-feedback d-block" id="warm_cloth_error" style="display: none !important;">
                        Please select an option for warm cloth allowance request.
                    </div>
                </div>

                    <!-- Any Other Details -->
                    <div class="col-md-12">
                    <label class="form-label fw-semibold">Any Other Details of Funding</label>
                    <textarea name="any_other_details" class="form-control" rows="4"  {{ $readonly ?? true ? 'readonly' : '' }}>{{ $draft_study_leave->any_other_details ?? '' }} </textarea>
                </div>


                                                
                                                <!-- Self-Funding Declaration (conditional) -->
                                                <div class="col-md-12" id="self-funding-declaration" style="display: none;">
                                                    <div class="alert alert-warning mt-3">
                                                        <strong>Note:</strong> If you are not receiving any scholarship, airfare or warm cloth allowance from any University, Institute, agency or project, please attach a separate document certifying that you will not be receiving any funds mentioned above from the placement offering University, Institute or any other agency.
                                                    </div>

                                                    <label class="form-label fw-semibold">Self-Funding Declaration <span class="text-danger" id="self-declaration-required">*</span></label>
                                                    <input type="file" name="self_funding_declaration" id="self-funding-declaration-input" class="form-control" accept="application/pdf" {{ !empty($draft_study_leave->self_funding_declaration) ? '' : 'required' }} {{ $readonly ?? true ? 'disabled' : '' }}>
                                                    <div class="invalid-feedback">
                                                        Please upload a self-funding declaration PDF document.
                                                    </div>

                                                    <!-- Show previously uploaded file (when editing) -->
                                                    @if(!empty($draft_study_leave->self_funding_declaration))
                                                        <div class="mt-2 d-flex justify-content-between align-items-center" id="existing-self-declaration">
                                                            <div>
                                                                <strong>Existing file:</strong>
                                                                <span class="ms-2">{{ basename($draft_study_leave->self_funding_declaration) }}</span>
                                                            </div>
                                                            <div>
                                                                <a href="{{ route('StudyLeave.serveFile', ['type' => 'self_funding_declaration', 'filename' => basename($draft_study_leave->self_funding_declaration)]) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Open</a>
                                                                <button type="button" id="preview-self-declaration-btn" class="btn btn-sm btn-outline-primary">Preview</button>
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <div id="self-declaration-preview-embed" class="mt-3" style="display:none;">
                                                        <label class="form-label fw-semibold">Preview</label>
                                                        <div style="border:1px solid #dee2e6;">
                                                            <embed id="self-declaration-embed" src="" type="application/pdf" width="100%" height="600px">
                                                        </div>
                                                    </div>



                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const input = document.getElementById('self-funding-declaration-input');
                        const previewWrap = document.getElementById('self-declaration-preview-embed');
                        const embed = document.getElementById('self-declaration-embed');
                        const previewBtn = document.getElementById('preview-self-declaration-btn');
                        let currentUrl = null;

                        // Preview existing stored file on page load (if present)
                        @if(!empty($draft_study_leave->self_funding_declaration))
                            const existingFileUrl = "{{ route('StudyLeave.serveFile', ['type' => 'self_funding_declaration', 'filename' => basename($draft_study_leave->self_funding_declaration)]) }}";
                            embed.src = existingFileUrl;
                            previewWrap.style.display = 'block';
                        @endif

                        // Preview newly selected file
                        input.addEventListener('change', function () {
                            if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
                            const file = this.files && this.files[0];
                            if (!file) {
                                previewWrap.style.display = 'none';
                                embed.src = '';
                                return;
                            }
                            currentUrl = URL.createObjectURL(file);
                            embed.src = currentUrl;
                            previewWrap.style.display = 'block';
                        });

                        // Preview existing stored file when clicking Preview button
                        if (previewBtn) {
                            previewBtn.addEventListener('click', function () {
                                if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
                                const openLink = document.querySelector('#existing-self-declaration a[target="_blank"]');
                                if (openLink) {
                                    embed.src = openLink.href;
                                    previewWrap.style.display = 'block';
                                    previewWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                }
                            });
                        }

                        // cleanup on form submit/navigation
                        const form = document.getElementById('leave-form');
                        if (form) {
                            form.addEventListener('submit', () => {
                                if (currentUrl) URL.revokeObjectURL(currentUrl);
                            });
                        }
                    });
                    </script>
                </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const fundingType = document.querySelector('select[name="funding_type"]');
                        const selfFundingDeclaration = document.getElementById('self-funding-declaration');
                        
                        function updateSelfFundingDeclarationVisibility() {
                            if (fundingType.value === 'self') {
                                selfFundingDeclaration.style.display = 'block';
                            } else {
                                selfFundingDeclaration.style.display = 'none';
                                selfFundingDeclaration.querySelector('input[type="file"]').value = '';
                            }
                        }
                        
                        // Show on page load if funding type is already 'Full'
                        updateSelfFundingDeclarationVisibility();
                        
                        // Update when user changes funding type
                        fundingType.addEventListener('change', updateSelfFundingDeclarationVisibility);
                    });
                </script>

                <!-- Attachment Instructions -->
              

                    <!-- Show previously uploaded file (when editing) -->
                   

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const input = document.getElementById('attachments-input');
                    const list = document.getElementById('pdf-preview-list');
                    const embedWrap = document.getElementById('pdf-preview-embed');
                    const embed = document.getElementById('pdf-embed');
                    const previewExistingBtn = document.getElementById('preview-existing-btn');
                    let currentUrl = null;

                    // Preview existing stored file on page load (if present)
                    @if(!empty($draft_study_leave->placement_letter))
                        const existingFileUrl = "{{ route('StudyLeave.serveFile', ['type' => 'placement_letter', 'filename' => basename($draft_study_leave->placement_letter)]) }}";
                        embed.src = existingFileUrl;
                        embedWrap.style.display = 'block';
                    @endif

                    // Preview existing file when clicking Preview button
                    if (previewExistingBtn) {
                        previewExistingBtn.addEventListener('click', function () {
                            if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
                            const openLink = document.querySelector('#existing-placement-letter a[target="_blank"]');
                            if (openLink) {
                                embed.src = openLink.href;
                                embedWrap.style.display = 'block';
                                embedWrap.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        });
                    }

                    input.addEventListener('change', function () {
                        // cleanup previous
                        if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
                        list.innerHTML = '';
                        embed.src = '';
                        embedWrap.style.display = 'none';

                        const files = Array.from(this.files || []);
                        if (files.length === 0) return;

                        files.forEach((file, idx) => {
                            const item = document.createElement('div');
                            item.className = 'd-flex justify-content-between align-items-center py-1 border-bottom';

                            const name = document.createElement('div');
                            name.textContent = file.name;
                            name.style.cursor = 'pointer';
                            name.title = 'Click Preview';

                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'btn btn-sm btn-outline-secondary';
                            btn.textContent = 'Preview';

                            btn.addEventListener('click', () => {
                                if (currentUrl) URL.revokeObjectURL(currentUrl);
                                currentUrl = URL.createObjectURL(file);
                                embed.src = currentUrl;
                                embedWrap.style.display = 'block';
                            });

                            name.addEventListener('click', () => btn.click());

                            item.appendChild(name);
                            item.appendChild(btn);
                            list.appendChild(item);

                            // auto-preview first file
                            if (idx === 0) {
                                btn.click();
                            }
                        });
                    });

                    // revoke object URL on form submit/navigation to free memory
                    const form = document.getElementById('leave-form');
                    if (form) {
                        form.addEventListener('submit', () => {
                            if (currentUrl) URL.revokeObjectURL(currentUrl);
                        });
                    }
                });
                </script>


                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const fundingType = document.querySelector('select[name="funding_type"]');
                        const scholarshipDetails = document.getElementById('scholarship-details');
                        const scholarshipExtraDetails = document.getElementById('scholarship-extra-details');
                        const scholarshipSource = document.getElementById('scholarship_source');
                        const scholarshipAmountGroup = document.getElementById('scholarship-amount-group');
                        const projectNameGroup = document.getElementById('project-name-group');
                        const scholarshipAmount = document.getElementById('scholarship_amount');
                        const projectName = document.getElementById('project_name');
                        const selfFundingExtra = document.getElementById('self-funding-extra');
                        const selfFundingExtra2 = document.getElementById('self-funding-extra2');
                        const airPassageRadios = document.querySelectorAll('input[name="air_passage_request"]');
                        const warmClothRadios = document.querySelectorAll('input[name="warm_cloth_allowance_request"]');
                        
                        // Study location elements
                        const studyLocationRadios = document.querySelectorAll('input[name="study_location"]');
                        const countryField = document.getElementById('country_field');
                        const countryInput = document.getElementById('country');
                        const countryRequired = document.getElementById('country_required');
                        const studyLocationError = document.getElementById('study_location_error');
                        
                        console.log('Study location elements:', {
                            radios: studyLocationRadios.length,
                            countryField: countryField,
                            countryInput: countryInput
                        });
                        
                        // Leave payment type elements
                        const leavePaymentType = document.getElementById('leave_payment_type');
                        const loanHandlingSection = document.getElementById('loan_handling_section');
                        const loanHandlingDetails = document.getElementById('loan_handling_details');
                        
                        // Handle leave payment type change
                        function updateLoanHandlingVisibility() {
                            if (!leavePaymentType || !loanHandlingSection || !loanHandlingDetails) {
                                console.log('Loan handling elements not found, skipping');
                                return;
                            }
                            
                            if (leavePaymentType.value === 'without Pay') {
                                // Show loan handling field for "without Pay"
                                loanHandlingSection.style.display = 'block';
                                loanHandlingDetails.setAttribute('required', 'required');
                            } else {
                                // Hide loan handling field for "with Pay" or empty
                                loanHandlingSection.style.display = 'none';
                                loanHandlingDetails.removeAttribute('required');
                                loanHandlingDetails.value = ''; // Clear value when hidden
                            }
                        }
                        
                        // Add event listener to leave payment type
                        if (leavePaymentType && loanHandlingSection && loanHandlingDetails) {
                            leavePaymentType.addEventListener('change', updateLoanHandlingVisibility);
                            // Initialize on page load
                            updateLoanHandlingVisibility();
                        }
                        
                        // Handle study location change
                        function updateCountryField() {
                            const selectedLocation = document.querySelector('input[name="study_location"]:checked');
                            console.log('updateCountryField called, selected:', selectedLocation ? selectedLocation.value : 'none');
                            
                            if (!countryField || !countryInput) {
                                console.error('Country field elements not found!');
                                return;
                            }
                            
                            if (selectedLocation && selectedLocation.value === 'Sri Lanka') {
                                // Hide country field and set default value to Sri Lanka
                                console.log('Setting country to Sri Lanka and hiding field');
                                countryField.style.display = 'none';
                                countryInput.value = 'Sri Lanka';
                                countryInput.removeAttribute('required');
                                if (countryRequired) countryRequired.style.display = 'none';
                            } else if (selectedLocation && selectedLocation.value === 'Abroad') {
                                // Show country field and make it required
                                console.log('Showing country field for abroad');
                                countryField.style.display = 'block';
                                // Clear Sri Lanka value if it was set
                                if (countryInput.value === 'Sri Lanka') {
                                    countryInput.value = '';
                                }
                                countryInput.setAttribute('required', 'required');
                                if (countryRequired) countryRequired.style.display = 'inline';
                            } else {
                                // No selection - hide country field but don't set default value
                                console.log('No selection - hiding country field');
                                countryField.style.display = 'none';
                                countryInput.removeAttribute('required');
                            }
                        }
                        
                        // Add event listeners to study location radios
                        if (studyLocationRadios && studyLocationRadios.length > 0) {
                            studyLocationRadios.forEach(radio => {
                                radio.addEventListener('change', function() {
                                    console.log('Radio changed to:', this.value);
                                    updateCountryField();
                                    if (studyLocationError) studyLocationError.style.display = 'none';
                                });
                            });
                        }
                        
                        // Initialize country field on page load
                        console.log('Initializing country field on page load');
                        updateCountryField();
                        
                        function updateConditionalValidation() {
                            if (fundingType.value === 'self') {
                                // Self-Funding: Show air passage and warm cloth fields
                                selfFundingExtra.style.display = 'block';
                                selfFundingExtra2.style.display = 'block';
                                
                                // Make air passage and warm cloth required
                                airPassageRadios.forEach(radio => radio.setAttribute('required', 'required'));
                                warmClothRadios.forEach(radio => radio.setAttribute('required', 'required'));
                                
                                // Hide and remove scholarship requirements
                                scholarshipDetails.style.display = 'none';
                                scholarshipExtraDetails.style.display = 'none';
                                scholarshipAmountGroup.style.display = 'none';
                                projectNameGroup.style.display = 'none';
                                scholarshipSource.removeAttribute('required');
                                scholarshipAmount.removeAttribute('required');
                                projectName.removeAttribute('required');
                                
                            } else if (fundingType.value === 'scholarship') {
                                // Scholarship: Show scholarship fields
                                scholarshipDetails.style.display = 'block';
                                scholarshipSource.setAttribute('required', 'required');
                                
                                // Hide and remove self-funding requirements
                                selfFundingExtra.style.display = 'none';
                                selfFundingExtra2.style.display = 'none';
                                airPassageRadios.forEach(radio => radio.removeAttribute('required'));
                                warmClothRadios.forEach(radio => radio.removeAttribute('required'));
                                
                                // Clear radio selections when hiding
                                document.querySelectorAll('input[name="air_passage_request"]').forEach(el => el.checked = false);
                                document.querySelectorAll('input[name="warm_cloth_allowance_request"]').forEach(el => el.checked = false);
                                
                            } else {
                                // No funding type selected: Hide all conditional fields
                                selfFundingExtra.style.display = 'none';
                                selfFundingExtra2.style.display = 'none';
                                scholarshipDetails.style.display = 'none';
                                scholarshipExtraDetails.style.display = 'none';
                                scholarshipAmountGroup.style.display = 'none';
                                projectNameGroup.style.display = 'none';
                                
                                // Remove all conditional requirements
                                airPassageRadios.forEach(radio => radio.removeAttribute('required'));
                                warmClothRadios.forEach(radio => radio.removeAttribute('required'));
                                scholarshipSource.removeAttribute('required');
                                scholarshipAmount.removeAttribute('required');
                                projectName.removeAttribute('required');
                            }
                        }
                        
                        // Handle scholarship source changes
                        if (scholarshipSource) {
                            scholarshipSource.addEventListener('change', function() {
                                if (this.value === 'agency') {
                                    scholarshipAmountGroup.style.display = 'block';
                                    projectNameGroup.style.display = 'none';
                                    scholarshipAmount.setAttribute('required', 'required');
                                    projectName.removeAttribute('required');
                                    scholarshipExtraDetails.style.display = 'block';
                                } else if (this.value === 'project') {
                                    projectNameGroup.style.display = 'block';
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectName.setAttribute('required', 'required');
                                    scholarshipAmount.removeAttribute('required');
                                    scholarshipExtraDetails.style.display = 'block';
                                } else {
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'none';
                                    scholarshipExtraDetails.style.display = 'none';
                                    scholarshipAmount.removeAttribute('required');
                                    projectName.removeAttribute('required');
                                }
                            });
                        }
                        
                        // Initialize on page load
                        updateConditionalValidation();
                        if (scholarshipSource && scholarshipSource.value) {
                            scholarshipSource.dispatchEvent(new Event('change'));
                        }
                        
                        // Update when funding type changes
                        fundingType.addEventListener('change', updateConditionalValidation);
                        
                        // Date range validation
                        const fromDate = document.getElementById('study_leave_from');
                        const toDate = document.getElementById('study_leave_to');
                        
                        function validateDateRange() {
                            if (fromDate.value && toDate.value) {
                                if (new Date(toDate.value) <= new Date(fromDate.value)) {
                                    toDate.setCustomValidity('End date must be after start date');
                                } else {
                                    toDate.setCustomValidity('');
                                }
                            }
                        }
                        
                        if (fromDate && toDate) {
                            fromDate.addEventListener('change', function() {
                                toDate.min = this.value;
                                validateDateRange();
                            });
                            toDate.addEventListener('change', validateDateRange);
                        }
                        
                        // Custom radio button validation
                        function validateRadioGroups() {
                            let isValid = true;
                            const airPassageError = document.getElementById('air_passage_error');
                            const warmClothError = document.getElementById('warm_cloth_error');
                            
                            // Check study location
                            const studyLocationChecked = document.querySelector('input[name="study_location"]:checked');
                            if (!studyLocationChecked) {
                                studyLocationError.style.display = 'block';
                                isValid = false;
                            } else {
                                studyLocationError.style.display = 'none';
                            }
                            
                            // Check loan handling if required (for "without Pay")
                            if (leavePaymentType.value === 'without Pay' && !loanHandlingDetails.value) {
                                loanHandlingDetails.setCustomValidity('Please select an option');
                                isValid = false;
                            } else {
                                loanHandlingDetails.setCustomValidity('');
                            }
                            
                            // Check air passage if required
                            if (fundingType.value === 'self') {
                                const airPassageChecked = document.querySelector('input[name="air_passage_request"]:checked');
                                const warmClothChecked = document.querySelector('input[name="warm_cloth_allowance_request"]:checked');
                                
                                if (!airPassageChecked) {
                                    airPassageError.style.display = 'block';
                                    isValid = false;
                                } else {
                                    airPassageError.style.display = 'none';
                                }
                                
                                if (!warmClothChecked) {
                                    warmClothError.style.display = 'block';
                                    isValid = false;
                                } else {
                                    warmClothError.style.display = 'none';
                                }
                            }
                            
                            return isValid;
                        }
                        
                        // Bootstrap form validation
                        const forms = document.querySelectorAll('.needs-validation');
                        Array.from(forms).forEach(form => {
                            form.addEventListener('submit', event => {
                                validateDateRange();
                                const radioValid = validateRadioGroups();
                                
                                if (!form.checkValidity() || !radioValid) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                    
                                    // Scroll to first invalid field
                                    const firstInvalid = form.querySelector(':invalid');
                                    if (firstInvalid) {
                                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        firstInvalid.focus();
                                    }
                                }
                                form.classList.add('was-validated');
                            }, false);
                        });
                        
                        // Clear radio error messages on selection
                        airPassageRadios.forEach(radio => {
                            radio.addEventListener('change', () => {
                                document.getElementById('air_passage_error').style.display = 'none';
                            });
                        });
                        
                        warmClothRadios.forEach(radio => {
                            radio.addEventListener('change', () => {
                                document.getElementById('warm_cloth_error').style.display = 'none';
                            });
                        });
                    });
                </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const fundingType = document.querySelector('select[name="funding_type"]');
                            const scholarshipDetails = document.getElementById('scholarship-details');
                            const scholarshipSource = scholarshipDetails.querySelector('select[name="scholarship_source"]');
                            const scholarshipExtraDetails = document.getElementById('scholarship-extra-details');
                            const scholarshipAmountGroup = document.getElementById('scholarship-amount-group');
                            const projectNameGroup = document.getElementById('project-name-group');

                            function updateScholarshipVisibility() {
                                if (fundingType.value === 'scholarship') {
                                    scholarshipDetails.style.display = 'block';
                                    // Also trigger scholarship source visibility on page load
                                    updateScholarshipSourceVisibility();
                                } else {
                                    scholarshipDetails.style.display = 'none';
                                    if (fundingType.value !== '') {
                                        scholarshipDetails.querySelector('select').value = '';
                                    }
                                    scholarshipExtraDetails.style.display = 'none';
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'none';
                                }
                            }

                            function updateScholarshipSourceVisibility() {
                                if (scholarshipSource.value) {
                                    scholarshipExtraDetails.style.display = 'block';
                                    if (scholarshipSource.value === 'agency') {
                                        scholarshipAmountGroup.style.display = 'block';
                                        projectNameGroup.style.display = 'none';
                                    } else if (scholarshipSource.value === 'project') {
                                        scholarshipAmountGroup.style.display = 'none';
                                        projectNameGroup.style.display = 'block';
                                    } else {
                                        scholarshipAmountGroup.style.display = 'none';
                                        projectNameGroup.style.display = 'none';
                                        scholarshipExtraDetails.style.display = 'none';
                                    }
                                }
                            }

                            // Show fields on page load based on current values
                            updateScholarshipVisibility();

                            // Update when user changes funding type
                            fundingType.addEventListener('change', updateScholarshipVisibility);

                            // Update when user changes scholarship source
                            scholarshipSource.addEventListener('change', updateScholarshipSourceVisibility);
                        });
                    </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const fundingType = document.querySelector('select[name="funding_type"]');
                            const scholarshipDetails = document.getElementById('scholarship-details');

                            fundingType.addEventListener('change', function () {
                                if (this.value === 'scholarship') {
                                    scholarshipDetails.style.display = 'block';
                                } else {
                                    scholarshipDetails.style.display = 'none';
                                    scholarshipDetails.querySelector('select').value = '';
                                }
                            });
                        });
                    </script>
                       
            </div>
        </div>