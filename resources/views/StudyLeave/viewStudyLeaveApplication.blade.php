

    <!-- Section 1: Personal Details -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-user me-2"></i>Personal Details
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Employee Number -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->employee_no ?? '' }}" readonly>
                </div>
                <!-- Name with Initials -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Name with Initials</label>
                    <input type="text" class="form-control" value="{{ $application->name_with_initials ?? '' }}" readonly>
                </div>
                <!-- Designation -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Designation</label>
                    <input type="text" class="form-control" value="{{ $application->designation ?? '' }}" readonly>
                </div>
                <!-- Department -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Department</label>
                    <input type="text" class="form-control" value="{{ $application->department ?? '' }}" readonly>
                </div>
                <!-- Faculty -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Faculty</label>
                    <input type="text" class="form-control" value="{{ $application->faculty ?? '' }}" readonly>
                </div>
                <!-- Email Address -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="text" class="form-control" value="{{ $application->email ?? '' }}" readonly>
                </div>
                <!-- Passport No -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Passport No</label>
                    <input type="text" class="form-control" value="{{ $application->passport_no ?? '' }}" >
                </div>
                <!-- Passport Validity -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Passport Validity Date</label>
                    <input type="date" class="form-control" value="{{ $application->passport_validity ?? '' }}" >
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Details of the Study Leave -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-graduation-cap me-2"></i>Details of the Study Leave
        </div>
        <div class="card-body">
            <div class="row g-3">
                 <!-- University/Institute -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">University or Institute</label>
                    <input type="text" class="form-control" value="{{ $application->university_institute ?? '' }}" >
                </div>
                <!-- Country -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" class="form-control" value="{{ $application->country ?? '' }}" r>
                </div>
                  <!-- Field of Study -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Field of Study</label>
                    <input type="text" class="form-control" value="{{ $application->field_of_study ?? '' }}" >
                </div>
               
                                     <!-- Details of the Study Program -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title of the Degree (e.g. M.A., M.Sc, MBA, M.Phil., M.D., PhD)</label>
                        <select name="degree_title" class="form-select" required>
                            <option value="" {{ $application->degree_title === '' ? 'selected' : '' }}>Select degree title</option>
                            <option value="MA" {{ $application->degree_title === 'MA' ? 'selected' : '' }}>M.A.</option>
                            <option value="MSc" {{ $application->degree_title === 'MSc' ? 'selected' : '' }}>M.Sc</option>
                            <option value="MBA" {{ $application->degree_title === 'MBA' ? 'selected' : '' }}>MBA</option>
                            <option value="MPhil" {{ $application->degree_title === 'MPhil' ? 'selected' : '' }}>M.Phil.</option>
                            <option value="MD" {{ $application->degree_title === 'MD' ? 'selected' : '' }}>M.D.</option>
                            <option value="PhD" {{ $application->degree_title === 'PhD' ? 'selected' : '' }}>PhD</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                 <!-- Period From -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">From</label>
                    <input type="date" class="form-control" value="{{ $application->study_leave_from ?? '' }}" >
                </div>
                <!-- Period To -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">To</label>
                    <input type="date" class="form-control" value="{{ $application->study_leave_to ?? '' }}" >
                </div>
                
                <!-- Study Program Details -->
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Relevancy and Details of the Study Program</label>
                    <textarea class="form-control" rows="4" readonly>{{ $application->study_program_details ?? '' }}</textarea>
                </div>
               
                

                 <!-- Type of Study Leave Requested -->
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Type of Study Leave Requested</label>
                        <select name="leave_payment_type" class="form-select" required>
                            <option value="" {{ $application->leave_payment_type == '' ? 'selected' : '' }}>Select an option</option>
                            <option value="1" {{ $application->leave_payment_type == '1' ? 'selected' : '' }}>With Pay</option>
                            <option value="2" {{ $application->leave_payment_type == '2' ? 'selected' : '' }}>Without Pay</option>
                        </select>
                    </div>
               

                 <!-- Funding Type -->
                <div class="col-md-6">
                        <label class="form-label fw-semibold"> Funding type</label>
                        <select name="funding_type" class="form-select" >
                            <option value="" {{ $application->funding_type ?? '' ? '' : 'selected' }}>Select funding type</option>
                            <option value="1" {{ $application->funding_type == '1' ? 'selected' : '' }}>Self-Funding</option>
                            <option value="2" {{ $application->funding_type == '2' ? 'selected' : '' }}>Scholarship</option>
                        </select>
                    </div>
               

 <!-- Scholarship Details (conditional)- If Scholarship is selected in Funding Type -->
                    <div class="col-md-6" id="scholarship-details" style="display: none;">
                        <label class="form-label fw-semibold">Scholarship Source</label>
                        <select name="scholarship_source" class="form-select" >
                            <option value="" {{ empty($application->scholarship_source) ? 'selected' : '' }}>Select source</option>
                            <option value="1" {{ ($application->scholarship_source ?? '') == '1' ? 'selected' : '' }}>Scholarship offering agency</option>
                            <option value="2" {{ ($application->scholarship_source ?? '') == '2' ? 'selected' : '' }}>Funds from a project</option>
                        </select>
                    </div>

                    <!-- Additional fields based on Scholarship Source -->
                    <div class="col-md-6" id="scholarship-extra-details" style="display: none;">

                        <!-- Scholarship Amount (conditional) - If Scholarship offering agency is selected -->
                        <div id="scholarship-amount-group" style="display: none;">
                            <label class="form-label fw-semibold">Scholarship Amount</label>
                            <input type="number" name="scholarship_amount" class="form-control" min="0" step="0.01" placeholder="Enter amount" value="{{ $application->scholarship_amount ?? '' }}" >
                        </div>

                        <!-- Project Name (conditional) - If Funds from a project is selected -->
                        <div id="project-name-group" style="display: none;">
                            <label class="form-label fw-semibold">Project Name</label>
                            <input type="text" name="project_name" class="form-control" placeholder="Enter project name" value="{{ $application->project_name ?? '' }}" >
                        </div>
                    </div>
                    <!-- Additional fields (conditional) - If Self-Funding is selected -->

                <!-- Air Passage Request -->
                <div class="col-md-6" id="self-funding-extra" style="display: none;">
                    <label class="form-label fw-semibold d-inline-block me-3">Requesting Air Passage from this University?</label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_yes" value="1"
                                @checked(old('air_passage_request', $application->air_passage_request ?? '') == '1')>
                            <label class="form-check-label" for="air_passage_yes">YES</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_no" value="0" @checked(old('air_passage_request', $application->air_passage_request ?? '') == '0' && old('air_passage_request', $application->air_passage_request ?? '') !== '')>
                            <label class="form-check-label" for="air_passage_no">NO</label>
                        </div>
                    </div>
                </div>

                <!-- Warm Cloth Allowance Request -->
                <div class="col-md-6" id="self-funding-extra2" style="display: none;">
                    <label class="form-label fw-semibold d-inline-block me-3">Requesting Warm Cloth Allowance from this University?</label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_yes" value="1"
                                @checked(old('warm_cloth_allowance_request', $application->warm_cloth_allowance_request ?? '') == '1')>
                            <label class="form-check-label" for="warm_cloth_yes">YES</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_no" value="0"
                                @checked(old('warm_cloth_allowance_request', $application->warm_cloth_allowance_request ?? '') == '0' && old('warm_cloth_allowance_request', $application->warm_cloth_allowance_request ?? '') !== '')>
                            <label class="form-check-label" for="warm_cloth_no">NO</label>
                        </div>
                    </div>
                </div>

                    <!-- Any Other Details -->
                    <div class="col-md-12">
                    <label class="form-label fw-semibold">Any Other Details of Funding</label>
                    <textarea name="any_other_details" class="form-control" rows="4" >{{ $application->any_other_details ?? '' }} </textarea>
                </div>


                
                <!-- Self-Funding Declaration (conditional) -->
                <div class="col-md-12" id="self-funding-declaration" style="display: none;">
                    <div class="alert alert-warning mt-3">
                        <strong>Note:</strong> If you are not receiving any scholarship, airfare or warm cloth allowance from any University, Institute, agency or project, please attach a separate document certifying that you will not be receiving any funds mentioned above from the placement offering University, Institute or any other agency.
                    </div>

                    <label class="form-label fw-semibold mt-2">Attach Declaration PDF</label>
                    <input type="file" name="self_funding_declaration" id="self-funding-declaration-input" class="form-control" accept="application/pdf">

                    <!-- Show previously uploaded file (when editing) -->
                    @if(!empty($application->self_funding_declaration))
                        <div class="mt-2 d-flex justify-content-between align-items-center" id="existing-self-declaration">
                            <div>
                                <strong>Existing file:</strong>
                                <span class="ms-2">{{ basename($application->self_funding_declaration) }}</span>
                            </div>
                            <div>
                                <a href="{{ route('StudyLeave.serveFile', ['type' => 'self_funding_declaration', 'filename' => basename($application->self_funding_declaration)]) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Open</a>
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

                    
                       
            </div>
        </div>

            </div>
        </div>
    </div>

    <!-- Section 3: Previous Study Leave Records -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-history me-2"></i>Previous Study Leave Records
        </div>
        <div class="card-body">
            @if(isset($previousLeaves) && count($previousLeaves) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Degree</th>
                                <th>University/Institute</th>
                                <th>Duration (From - To)</th>
                                <th>With Pay/No Pay</th>
                                <th>Completion Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previousLeaves as $leave)
                                <tr>
                                    <td>{{ $leave->prev_leave_type ?? '' }}</td>
                                    <td>{{ $leave->prev_university ?? '' }}</td>
                                    <td>{{ $leave->prev_duration_from ?? '' }} - {{ $leave->prev_duration_to ?? '' }}</td>
                                    <td>{{ $leave->prev_with_pay ?? '' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $leave->prev_completed == 'Completed' ? 'success' : 'warning' }}">
                                            {{ $leave->prev_completed ?? '' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-3">No previous study leave records found.</p>
            @endif
        </div>
    </div>

    <!-- Section 4: Work Covering Arrangements -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-users me-2"></i>Arrangements Made to Cover Applicant's Work During Leave
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Teaching Work Nominee -->
                <div class="col-md-12">
                    <h6 class="fw-bold text-secondary mb-3">Nominee for Teaching Work</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_teaching_empno ?? '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee Name</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_teaching_name ?? '' }}" readonly>
                </div>

                <!-- Administrative Work Nominee -->
                <div class="col-md-12 mt-4">
                    <h6 class="fw-bold text-secondary mb-3">Nominee for Administrative Work</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_admin_empno ?? '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee Name</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_admin_name ?? '' }}" readonly>
                </div>

                <!-- Other Work Nominee -->
                <div class="col-md-12 mt-4">
                    <h6 class="fw-bold text-secondary mb-3">Nominee for Other Work</h6>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee No</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_other_empno ?? '' }}" readonly>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Employee Name</label>
                    <input type="text" class="form-control" value="{{ $application->nominee_other_name ?? '' }}" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 5: Handling of Properties and Loans -->
    <div class="card mb-4">
        <div class="card-header card-header-maroon fw-semibold">
            <i class="fas fa-clipboard-check me-2"></i>Handling of Library Books, Properties, and Loans
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Library and Property Handling -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Library Books, Computer or Other Properties --</label>
                    <input type="text" class="form-control" value="{{ $application->library_and_property_handling ?? '' }}" readonly>
                </div>
                <!-- Loan Handling -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Paying of Loans from University/UPF</label>
                    <input type="text" class="form-control" value="{{ $application->loan_handling ?? '' }}" readonly>
                </div>
            </div>
        </div>
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
                            if (fundingType.value === '1') {
                                selfFundingDeclaration.style.display = 'block';
                            } else {
                                selfFundingDeclaration.style.display = 'none';
                                selfFundingDeclaration.querySelector('input[type="file"]').value = '';
                            }
                        }
                        
                        // Show on page load if funding type is already '1' (Self-Funding)
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
                        const selfFundingExtra = document.getElementById('self-funding-extra');
                        const selfFundingExtra2 = document.getElementById('self-funding-extra2');
                        
                        function updateSelfFundingVisibility() {
                            if (fundingType.value === '1') {
                                selfFundingExtra.style.display = 'block';
                                selfFundingExtra2.style.display = 'block';
                            } else {
                                selfFundingExtra.style.display = 'none';
                                selfFundingExtra2.style.display = 'none';
                                // Only clear radio buttons when changing away from Full funding
                                if (fundingType.value !== '') {
                                    document.querySelectorAll('input[name="air_passage_request"]').forEach(el => el.checked = false);
                                    document.querySelectorAll('input[name="warm_cloth_allowance_request"]').forEach(el => el.checked = false);
                                }
                            }
                        }
                        
                        // Show fields on page load if funding type is already set to '1' (Self-Funding)
                        updateSelfFundingVisibility();
                        
                        // Update visibility when funding type changes
                        fundingType.addEventListener('change', updateSelfFundingVisibility);
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
                                if (fundingType.value === '2') {
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
                                    if (scholarshipSource.value === '1') {
                                        scholarshipAmountGroup.style.display = 'block';
                                        projectNameGroup.style.display = 'none';
                                    } else if (scholarshipSource.value === '2') {
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
                                if (this.value === '2') {
                                    scholarshipDetails.style.display = 'block';
                                } else {
                                    scholarshipDetails.style.display = 'none';
                                    scholarshipDetails.querySelector('select').value = '';
                                }
                            });
                        });
                    </script>

<!-- Print Styles -->
<style>
    @media print {
        .btn, .card-header, .alert {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .btn {
            display: none;
        }
    }
</style>


