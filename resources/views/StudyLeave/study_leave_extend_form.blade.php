@extends('layouts.app')

@section('content')


<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Show remark if returned -->
    @isset($remark)
    <div class="alert alert-warning fw-semibold">
        Returned with remark:
        <pre class="mb-0">{{ $remark }}</pre>
    </div>
    @endisset

    <!-- Header and Back Button -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <!-- Header -->
        <div>
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-file-alt me-2 icon-gold"></i>
                Request for Study Leave Extension
                
            </h2>
        </div>

       
    </div>





     <!-- --------------------------------------------------------------------------------------------------------Form Start -->

    <form   method="POST" enctype="multipart/form-data" id="leave-form" class="needs-validation" novalidate>
        @csrf

      
        

        <!-- ----------------Form Card -->
    
        <div class="card mb-4">

            <!-- Card Header -->
            
            <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-user me-2"></i>Details of the Study Leave Extention - Refference No: {{ $study_leave->reference_no ?? 'N/A' }}
                </span>
              
            </div>
            <div class="card-body">
                <div class="row g-3">
                
               

               
                   
                   
                <!-- Period of Study Leave Requested -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Period of Study Leave Requested <span class="text-danger">*</span></label>
                                        <div class="row g-3">
                                            <!-- From Date -->
                                            <div class="col-md-6">
                                                
                                        
                                                <label class="form-label">From </label>
                                                <input type="date" name="study_leave_from" id="study_leave_from" class="form-control" 
                                                       value="{{optional($study_leave)->study_leave_to ?? ''}}" 
                                                       min="{{ date('Y-m-d') }}" 
                                                      readonly required>
                                                <div class="invalid-feedback">
                                                    Please select a valid start date.
                                                </div>
                                            </div>

                                            <!-- To Date -->
                                            <div class="col-md-6">
                                                <label class="form-label">To <span class="text-danger">*</span></label>
                                                <input type="date" name="study_leave_to" id="study_leave_to" class="form-control" 
                                                       value=''
                                                       min="{{ date('Y-m-d') }}" 
                                                       required 
                                                       {{ $readonly ?? true ? 'readonly' : '' }}>
                                                <div class="invalid-feedback">
                                                    Please select a valid end date (must be after start date).
                                                </div>
                                            </div>
                                        </div>
                                    </div>




                


                 <!-- Type of Study Leave Requested -->
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Type of Study Leave Requested <span class="text-danger">*</span></label>
                        <select name="leave_payment_type" id="leave_payment_type" class="form-select" required {{ $readonly ?? true ? 'disabled' : '' }}>
                            <option value="" {{ $study_leave->leave_payment_type === '' ? 'selected' : '' }} >Select an option</option>
                            <option value="with Pay" {{ $study_leave->leave_payment_type === 'with Pay' ? 'selected' : '' }}>With Pay</option>
                            <option value="without Pay" {{ $study_leave->leave_payment_type === 'without Pay' ? 'selected' : '' }}>Without Pay</option>
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
                        <option value="" {{ empty(old('loan_handling', $study_leave->loan_handling ?? '')) ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="Make Arrangements" {{ old('loan_handling', $study_leave->loan_handling ?? '') === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="Not Make Arrangements" {{ old('loan_handling', $study_leave->loan_handling ?? '') === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                    <div class="invalid-feedback">
                        Please select an option for loan handling.
                    </div>
                </div>

                <!-- Funding Type -->
                <div class="col-md-6">
                        <label class="form-label fw-semibold"> Funding type <span class="text-danger">*</span></label>
                        <select name="funding_type" id="funding_type" class="form-select" required {{ $readonly ?? true ? 'disabled' : '' }} >
                            <option value="" {{ $study_leave->funding_type ?? '' ? '' : 'selected' }} disabled>Select funding type</option>
                            <option value="self" {{ $study_leave->funding_type === 'self' ? 'selected' : '' }}>Self-Funding</option>
                            <option value="scholarship" {{ $study_leave->funding_type === 'scholarship' ? 'selected' : '' }}>Scholarship</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a funding type.
                        </div>
                    </div>

                    <!-- Scholarship Details (conditional)- If Scholarship is selected in Funding Type -->
                    <div class="col-md-6" id="scholarship-details" style="display: none;">
                        <label class="form-label fw-semibold">Scholarship Source <span class="text-danger">*</span></label>
                        <select name="scholarship_source" id="scholarship_source" class="form-select" {{ $readonly ?? true ? 'disabled' : '' }} >
                            <option value="" {{ empty($study_leave->scholarship_source) ? 'selected' : '' }} disabled>Select source</option>
                            <option value="agency" {{ ($study_leave->scholarship_source ?? '') === 'agency' ? 'selected' : '' }}>Scholarship offering agency</option>
                            <option value="project" {{ ($study_leave->scholarship_source ?? '') === 'project' ? 'selected' : '' }}>Funds from a project</option>
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
                                   value="{{ $study_leave->scholarship_amount ?? '' }}" 
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
                                   value="{{ $study_leave->project_name ?? '' }}" 
                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                            <div class="invalid-feedback">
                                Please enter the project name (3-200 characters).
                            </div>
                        </div>
                    </div>
                   


                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const input = document.getElementById('self-funding-declaration-input');
                        const previewWrap = document.getElementById('self-declaration-preview-embed');
                        const embed = document.getElementById('self-declaration-embed');
                        const previewBtn = document.getElementById('preview-self-declaration-btn');
                        const removeBtn = document.getElementById('remove-self-declaration-btn');
                        const existingSelfDeclaration = document.getElementById('existing-self-declaration');
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

                        // Remove existing file
                        if (removeBtn) {
                            removeBtn.addEventListener('click', function () {
                                if (confirm('Are you sure you want to remove this file? You can upload a new one.')) {
                                    // Call the backend to delete the file
                                    fetch('{{ route("StudyLeave.deleteFile") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ type: 'self_funding_declaration' })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            existingSelfDeclaration.style.display = 'none';
                                            input.removeAttribute('disabled');
                                            input.setAttribute('required', 'required');
                                            previewWrap.style.display = 'none';
                                            embed.src = '';
                                            if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
                                        } else {
                                            alert('Failed to delete file: ' + (data.message || 'Unknown error'));
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error deleting file:', error);
                                        alert('Failed to delete file. Please try again.');
                                    });
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
                        const selfFundingInput = document.getElementById('self-funding-declaration-input');
                        const selfDeclarationRequired = document.getElementById('self-declaration-required');
                        
                        function updateSelfFundingDeclarationVisibility() {
                            if (fundingType.value === 'self') {
                                selfFundingDeclaration.style.display = 'block';
                                // Only make required if no existing file
                                @if(empty($draft_study_leave->self_funding_declaration))
                                    selfFundingInput.setAttribute('required', 'required');
                                @endif
                                if (selfDeclarationRequired) selfDeclarationRequired.style.display = 'inline';
                            } else {
                                selfFundingDeclaration.style.display = 'none';
                                selfFundingInput.removeAttribute('required');
                                selfFundingInput.value = '';
                                if (selfDeclarationRequired) selfDeclarationRequired.style.display = 'none';
                            }
                        }
                        
                        // Show on page load if funding type is already 'self'
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
                        const removePlacementBtn = document.getElementById('remove-placement-letter-btn');
                        const existingPlacementDiv = document.getElementById('existing-placement-letter');
                        let currentUrl = null;                    // Preview existing stored file on page load (if present)
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

                        // Remove existing file
                        if (removePlacementBtn) {
                            removePlacementBtn.addEventListener('click', function () {
                                if (confirm('Are you sure you want to remove this file? You can upload a new one.')) {
                                    // Call the backend to delete the file
                                    fetch('{{ route("StudyLeave.deleteFile") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ type: 'placement_letter' })
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            existingPlacementDiv.style.display = 'none';
                                            input.removeAttribute('disabled');
                                            input.setAttribute('required', 'required');
                                            embedWrap.style.display = 'none';
                                            embed.src = '';
                                            if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
                                        } else {
                                            alert('Failed to delete file: ' + (data.message || 'Unknown error'));
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error deleting file:', error);
                                        alert('Failed to delete file. Please try again.');
                                    });
                                }
                            });
                        }                    input.addEventListener('change', function () {
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
              <!-- Action Buttons -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center  ">
                    <!-- Cancel Button -->
                    <a href="{{ route('StudyLeave.create') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>

                    <!-- Extend Button -->
                    <button type="submit" class="btn btn-primary btn-lg" style="background-color: #800020; border-color: #800020;">
                        <i class="fas fa-paper-plane me-2"></i>Submit Extension Request
                    </button>
                </div>
            </div>
        </div>
        </div>


        <!------------------------------------------------------------------------------>


      
    </form>

<style>
    .custom-radio {
        border: 2px solid #6c757d !important;
        box-shadow: 0 0 2px #6c757d;
        background-color: #fff;
    }
    .custom-radio:checked {
        border-color: #800000 !important;
        box-shadow: 0 0 4px #800000;
    }
</style>
@endsection