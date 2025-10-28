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
                Application for Study Leave
                
            </h2>
        </div>

        <!-- Back Button -->
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>





     <!-- Form Start -->

    <form  action="{{ route('StudyLeave.Details.store') }}" method="POST" enctype="multipart/form-data" id="leave-form">
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
            <input type="hidden" name="reference_no" value="{{ $leave->reference_no }}">
        @else
            <input type="hidden" name="reference_no" value="">
            @if(isset($academicYear))
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">
            @endif
        @endif
        
        <!-- Form Card -->
    
        <div class="card mb-4">

            <!-- Card Header -->
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Details of the Study Leave
            </div>
            <div class="card-body">
                <div class="row g-3">
                    
                    <!-- Leave Type -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Leave Type</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="" disabled selected>Select an option</option>
                            <option value="fresh">Fresh Study Leave</option>
                            <option value="extension">Extension</option>
                        </select>
                    </div>

                    <!-- Type of Study Leave Requested -->
                     <div class="col-md-6">
                        <label class="form-label fw-semibold">Type Of Study Leave Requested</label>
                        <select name="leave_payment_type" class="form-select" required>
                            <option value="" disabled selected>Select an option</option>
                            <option value="with Pay">With Pay</option>
                            <option value="without Pay">Without Pay</option>
                        </select>
                    </div>

                    <!-- Period of Study Leave Requested (From) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period of Study Leave Requested (From)</label>
                        <input type="date" name="study_leave_from" class="form-control" required>
                    </div>

                    <!-- Period of Study Leave Requested (To) -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Period of Study Leave Requested (To)</label>
                        <input type="date" name="study_leave_to" class="form-control" required>
                    </div>


                    <!-- Details of the Study Program -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title of the Degree (e.g. M.A., M.Sc, MBA, M.Phil., M.D., PhD)</label>
                        <select name="degree_title" class="form-select" required>
                            <option value="" disabled selected>Select degree title</option>
                            <option value="MA">M.A.</option>
                            <option value="MSc">M.Sc</option>
                            <option value="MBA">MBA</option>
                            <option value="MPhil">M.Phil.</option>
                            <option value="MD">M.D.</option>
                            <option value="PhD">PhD</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- University or the Institute -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">University or the Institute</label>
                    <input type="text" name="university_institute" class="form-control" required>
                </div>

                <!-- Country and Field of Study -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Country</label>
                    <input type="text" name="country" class="form-control" required>
                </div>

                 <div class="col-md-6">
                    <label class="form-label fw-semibold">Field of study</label>
                    <input type="text" name="field_of_study" class="form-control" required>
                </div>


                <!-- Relevancy and Details of the Study Program -->

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Relevancy and Details of the Study Program</label>
                    <textarea name="study_program_details" class="form-control" rows="4" ></textarea>
                </div>

                <!-- Funding Type -->
                <div class="col-md-6">
                        <label class="form-label fw-semibold"> Funding type</label>
                        <select name="funding_type" class="form-select" >
                            <option value="" disabled selected>Select funding type</option>
                            <option value="Full">Self-Funding</option>
                            <option value="Partial">Scholarship</option>
                        </select>
                    </div>

                    <!-- Scholarship Details (conditional)- If Scholarship is selected in Funding Type -->
                    <div class="col-md-6" id="scholarship-details" style="display: none;">
                        <label class="form-label fw-semibold">Scholarship Source</label>
                        <select name="scholarship_source" class="form-select" >
                            <option value="" disabled selected>Select source</option>
                            <option value="agency">Scholarship offering agency</option>
                            <option value="project">Funds from a project</option>
                        </select>
                    </div>

                    <!-- Additional fields based on Scholarship Source -->
                    <div class="col-md-6" id="scholarship-extra-details" style="display: none;">

                        <!-- Scholarship Amount (conditional) - If Scholarship offering agency is selected -->
                        <div id="scholarship-amount-group" style="display: none;">
                            <label class="form-label fw-semibold">Scholarship Amount</label>
                            <input type="number" name="scholarship_amount" class="form-control" min="0" step="0.01" placeholder="Enter amount" >
                        </div>

                        <!-- Project Name (conditional) - If Funds from a project is selected -->
                        <div id="project-name-group" style="display: none;">
                            <label class="form-label fw-semibold">Project Name</label>
                            <input type="text" name="project_name" class="form-control" placeholder="Enter project name" >
                        </div>
                    </div>

                    <!-- Any Other Details -->
                    <div class="col-md-12">
                    <label class="form-label fw-semibold">Any Other Details</label>
                    <textarea name="any_other_details" class="form-control" rows="4" ></textarea>
                </div>


                <!-- Additional fields (conditional) - If Self-Funding is selected -->

                <!-- Air Passage Request -->
                <div class="col-md-6" id="self-funding-extra" style="display: none;">
                    <label class="form-label fw-semibold d-inline-block me-3">Requesting Air Passage from this University?</label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_yes" value="yes">
                            <label class="form-check-label" for="air_passage_yes">YES</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_no" value="no">
                            <label class="form-check-label" for="air_passage_no">NO</label>
                        </div>
                    </div>
                </div>

                <!-- Warm Cloth Allowance Request -->
                <div class="col-md-6" id="self-funding-extra2" style="display: none;">
                    <label class="form-label fw-semibold d-inline-block me-3">Requesting Warm Cloth Allowance from this University?</label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_yes" value="yes">
                            <label class="form-check-label" for="warm_cloth_yes">YES</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_no" value="no">
                            <label class="form-check-label" for="warm_cloth_no">NO</label>
                        </div>
                    </div>
                </div>
                <!-- Self-Funding Declaration (conditional) -->
                <div class="col-md-12" id="self-funding-declaration" style="display: none;">
                    <div class="alert alert-warning mt-3">
                        <strong>Note:</strong> If you are not receiving any scholarship, airfare or warm cloth allowance from any University, Institute, agency or project, please attach a separate document certifying that you will not be receiving any funds mentioned above from the placement offering University, Institute or any other agency.
                    </div>

                    <label class="form-label fw-semibold mt-2">Attach Declaration PDF</label>
                    <input type="file" name="self_funding_declaration" id="self-funding-declaration-input" class="form-control" accept="application/pdf">

                    <!-- Show previously uploaded file (when editing) -->
                    @isset($leave)
                        @if(!empty($leave->self_funding_declaration))
                            <div class="mt-2 d-flex justify-content-between align-items-center" id="existing-self-declaration">
                                <div>
                                    <strong>Existing file:</strong>
                                    <span class="ms-2">{{ basename($leave->self_funding_declaration) }}</span>
                                </div>
                                <div>
                                    <a href="{{ asset('storage/' . $leave->self_funding_declaration) }}" target="_blank" class="btn btn-sm btn-outline-secondary me-2">Open</a>
                                    <button type="button" id="preview-self-declaration-btn" class="btn btn-sm btn-outline-primary">Preview</button>
                                </div>
                            </div>
                        @endif
                    @endisset

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

                        // Preview existing stored file (if present)
                        if (previewBtn) {
                            previewBtn.addEventListener('click', function () {
                                // clear any object url first
                                if (currentUrl) { URL.revokeObjectURL(currentUrl); currentUrl = null; }
                                // use the storage URL from the anchor (Open button)
                                const openLink = document.querySelector('#existing-self-declaration a[target="_blank"]');
                                if (openLink) {
                                    embed.src = openLink.href;
                                    previewWrap.style.display = 'block';
                                    // scroll into view if desired
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

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const fundingType = document.querySelector('select[name="funding_type"]');
                        const selfFundingDeclaration = document.getElementById('self-funding-declaration');
                        fundingType.addEventListener('change', function () {
                            if (this.value === 'Full') {
                                selfFundingDeclaration.style.display = 'block';
                            } else {
                                selfFundingDeclaration.style.display = 'none';
                                selfFundingDeclaration.querySelector('input[type="file"]').value = '';
                            }
                        });
                    });
                </script>

                <!-- Attachment Instructions -->
                <div class="col-md-12">
                    <div class="alert alert-info mt-3">
                        <strong>Note:</strong> The placement offering letter and scholarship details should be attached to this application document as a PDF.
                    </div>
                    <label class="form-label fw-semibold mt-2">Attach PDF Documents</label>
                    <input type="file" name="attachments[]" id="attachments-input" class="form-control" accept="application/pdf" multiple >

                    <div id="pdf-preview-list" class="mt-3"></div>

                    <div id="pdf-preview-embed" class="mt-3" style="display:none;">
                        <label class="form-label fw-semibold">Preview</label>
                        <div style="border:1px solid #dee2e6;">
                            <embed id="pdf-embed" src="" type="application/pdf" width="100%" height="600px">
                        </div>
                    </div>
                </div>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const input = document.getElementById('attachments-input');
                    const list = document.getElementById('pdf-preview-list');
                    const embedWrap = document.getElementById('pdf-preview-embed');
                    const embed = document.getElementById('pdf-embed');
                    let currentUrl = null;

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
                        fundingType.addEventListener('change', function () {
                            if (this.value === 'Full') {
                                selfFundingExtra.style.display = 'block';
                                selfFundingExtra2.style.display = 'block';
                            } else {
                                selfFundingExtra.style.display = 'none';
                                selfFundingExtra2.style.display = 'none';
                                document.querySelectorAll('input[name="air_passage_request"]').forEach(el => el.checked = false);
                                document.querySelectorAll('input[name="warm_cloth_allowance_request"]').forEach(el => el.checked = false);
                            }
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

                            fundingType.addEventListener('change', function () {
                                if (this.value === 'Partial') {
                                    scholarshipDetails.style.display = 'block';
                                } else {
                                    scholarshipDetails.style.display = 'none';
                                    scholarshipDetails.querySelector('select').value = '';
                                    scholarshipExtraDetails.style.display = 'none';
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'none';
                                }
                            });

                            scholarshipSource.addEventListener('change', function () {
                                scholarshipExtraDetails.style.display = 'block';
                                if (this.value === 'agency') {
                                    scholarshipAmountGroup.style.display = 'block';
                                    projectNameGroup.style.display = 'none';
                                } else if (this.value === 'project') {
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'block';
                                } else {
                                    scholarshipAmountGroup.style.display = 'none';
                                    projectNameGroup.style.display = 'none';
                                    scholarshipExtraDetails.style.display = 'none';
                                }
                            });
                        });
                    </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const fundingType = document.querySelector('select[name="funding_type"]');
                            const scholarshipDetails = document.getElementById('scholarship-details');

                            fundingType.addEventListener('change', function () {
                                if (this.value === 'Partial') {
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

        <!-- Submit Button -->
<div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm">
        Next: Leave Details <i class="fas fa-arrow-right ms-2"></i>
    </button>
</div>
    </form>
    <!--
    <div class="d-flex justify-content-end mt-4">
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-right me-2"></i>Next: Leave Details
        </a>
    </div>
-->

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