<!-- Personal Details (readonly) -->
        <div class="card mb-4">
            
            <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-user me-2"></i>Arrangements made to cover applicants' work during the period of leave
                </span>
                @if($displayEditeBtn ?? false)
                    <a href="{{ route('StudyLeave.WorkCoveringPersons.create') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endif
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row g-3">
                    
                    <!-- Work Covering Persons Inputs -->
                    <div class="col-12">

                        <!-- Nominee Person For Teaching -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nominate Person For Teaching </label>
                                @if(!($readonly ?? true))
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Search Employee <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                            <input type="text" class="form-control search-employee @error('nominee_teaching_empno') is-invalid @enderror" 
                                                   id="nominee_teaching_empno" 
                                                   name="nominee_teaching_empno" 
                                                   value="{{ old('nominee_teaching_empno', $draft_study_leave->nominee_teaching_empno ?? '') }}" 
                                                   placeholder="Search by Employee Number or Name..." 
                                                   title="Search by employee number or name"
                                                   required>
                                        </div>
                                        @error('nominee_teaching_empno')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback" id="nominee_teaching_error" style="display: none;">
                                            <i class="fas fa-exclamation-circle"></i> Not found
                                        </div>
                                       
                                    </div>
                                </div>
                                @else
                                <input type="hidden" id="nominee_teaching_empno" name="nominee_teaching_empno" value="{{ old('nominee_teaching_empno', $draft_study_leave->nominee_teaching_empno ?? '') }}">
                                @endif
                                <input type="hidden" id="nominee_teaching_name" name="nominee_teaching_name" value="{{ old('nominee_teaching_name', $draft_study_leave->nominee_teaching_name ?? '') }}">
                                <!-- Employee Details Card -->
                                <div id="nominee_teaching_details" class="nominee-details-card mt-2" style="display: none;">
                                    <div class="card bg-light border">
                                        <div class="card-body p-3">
                                            <div class="mb-2 pb-2 border-bottom">
                                                <h6 class="mb-0 fw-bold">
                                                    <span id="nominee_teaching_title">-</span> <span id="nominee_teaching_name_display">-</span>
                                                </h6>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Employee No</small>
                                                    <span class="fw-semibold" id="nominee_teaching_empno_display2">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Designation</small>
                                                    <span class="fw-semibold" id="nominee_teaching_destination_display">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Department</small>
                                                    <span class="fw-semibold" id="nominee_teaching_department">-</span>
                                                </div>
                                            </div>
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-12">
                                                    <small class="text-muted d-block">Faculty</small>
                                                    <span class="fw-semibold" id="nominee_teaching_faculty">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="nominee_teaching_destination" name="nominee_teaching_destination" value="{{ old('nominee_teaching_destination', $draft_study_leave->nominee_teaching_destination ?? '') }}">
                                
                                @if(!($readonly ?? true))
                                <!-- Consent Letter Section for Teaching -->
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-semibold mb-0">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>Consent Letter
                                        </label>
                                        <a href="{{ route('StudyLeave.consentLetter.download') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>Download Template
                                        </a>
                                    </div>
                                    <input type="file" class="form-control" 
                                           id="consent_letter_teaching" 
                                           name="consent_letter_teaching" 
                                           accept="application/pdf">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Upload signed consent letter (PDF only)
                                    </small>
                                    @if(isset($draft_study_leave->consent_letter_teaching_path) && $draft_study_leave->consent_letter_teaching_path)
                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#previewTeachingModal">
                                                <i class="fas fa-eye me-1"></i>Preview Uploaded Letter
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="removeConsentLetter('teaching', {{ $draft_study_leave->id }})">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @else
                                @if(isset($draft_study_leave->consent_letter_teaching_path) && $draft_study_leave->consent_letter_teaching_path)
                                    <div class="mt-2">
                                        <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'teaching', 'id' => $draft_study_leave->id]) }}"
                                           target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye me-1"></i>Preview Consent Letter
                                        </a>
                                    </div>
                                @endif
                                @endif
                            </div>

                            <!-- Nominee Person For Administrative Work -->

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nominate Person For Administrative Work </label>
                                @if(!($readonly ?? true))
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Search Employee <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                            <input type="text" class="form-control search-employee @error('nominee_admin_empno') is-invalid @enderror" 
                                                   id="nominee_admin_empno" 
                                                   name="nominee_admin_empno" 
                                                   value="{{ old('nominee_admin_empno', $draft_study_leave->nominee_admin_empno ?? '') }}" 
                                                   placeholder="Search by Employee Number or Name..." 
                                                   title="Search by employee number or name"
                                                   required>
                                        </div>
                                        @error('nominee_admin_empno')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback" id="nominee_admin_error" style="display: none;">
                                            <i class="fas fa-exclamation-circle"></i> Not found
                                        </div>
                                       
                                    </div>
                                </div>
                                @else
                                <input type="hidden" id="nominee_admin_empno" name="nominee_admin_empno" value="{{ old('nominee_admin_empno', $draft_study_leave->nominee_admin_empno ?? '') }}">
                                @endif
                                <input type="hidden" id="nominee_admin_name" name="nominee_admin_name" value="{{ old('nominee_admin_name', $draft_study_leave->nominee_admin_name ?? '') }}">
                                <!-- Employee Details Card -->
                                <div id="nominee_admin_details" class="nominee-details-card mt-2" style="display: none;">
                                    <div class="card bg-light border">
                                        <div class="card-body p-3">
                                            <div class="mb-2 pb-2 border-bottom">
                                                <h6 class="mb-0 fw-bold">
                                                    <span id="nominee_admin_title">-</span> <span id="nominee_admin_name_display">-</span>
                                                </h6>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Employee No</small>
                                                    <span class="fw-semibold" id="nominee_admin_empno_display2">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Designation</small>
                                                    <span class="fw-semibold" id="nominee_admin_destination_display">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Department</small>
                                                    <span class="fw-semibold" id="nominee_admin_department">-</span>
                                                </div>
                                            </div>
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-12">
                                                    <small class="text-muted d-block">Faculty</small>
                                                    <span class="fw-semibold" id="nominee_admin_faculty">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="nominee_admin_destination" name="nominee_admin_destination" value="{{ old('nominee_admin_destination', $draft_study_leave->nominee_admin_destination ?? '') }}">    
                                
                                @if(!($readonly ?? true))
                                <!-- Consent Letter Section for Administrative -->
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-semibold mb-0">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>Consent Letter
                                        </label>
                                        <a href="{{ route('StudyLeave.consentLetter.download') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>Download Template
                                        </a>
                                    </div>
                                    <input type="file" class="form-control" 
                                           id="consent_letter_admin" 
                                           name="consent_letter_admin" 
                                           accept="application/pdf">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Upload signed consent letter (PDF only)
                                    </small>
                                    @if(isset($draft_study_leave->consent_letter_admin_path) && $draft_study_leave->consent_letter_admin_path)
                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#previewAdminModal">
                                                <i class="fas fa-eye me-1"></i>Preview Uploaded Letter
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="removeConsentLetter('administrative', {{ $draft_study_leave->id }})">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @else
                                @if(isset($draft_study_leave->consent_letter_admin_path) && $draft_study_leave->consent_letter_admin_path)
                                    <div class="mt-2">
                                        <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'administrative', 'id' => $draft_study_leave->id]) }}"
                                           target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye me-1"></i>Preview Consent Letter
                                        </a>
                                    </div>
                                @endif
                                @endif
                            </div>

                            <!--  Nominee Person For Other Work -->

                            <div class="mb-4">
                                <label class="form-label fw-semibold">Nominate Person For Other Work</label>
                                @if(!($readonly ?? true))
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Search Employee <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="fas fa-search text-muted"></i></span>
                                            <input type="text" class="form-control search-employee @error('nominee_other_empno') is-invalid @enderror" 
                                                   id="nominee_other_empno" 
                                                   name="nominee_other_empno" 
                                                   value="{{ old('nominee_other_empno', $draft_study_leave->nominee_other_empno ?? '') }}" 
                                                   placeholder="Search by Employee Number or Name..." 
                                                   title="Search by employee number or name"
                                                   required>
                                        </div>
                                        @error('nominee_other_empno')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        <div class="invalid-feedback" id="nominee_other_error" style="display: none;">
                                            <i class="fas fa-exclamation-circle"></i> Not found
                                        </div>
                                       
                                    </div>
                                </div>
                                @else
                                <input type="hidden" id="nominee_other_empno" name="nominee_other_empno" value="{{ old('nominee_other_empno', $draft_study_leave->nominee_other_empno ?? '') }}">
                                @endif
                                <input type="hidden" id="nominee_other_name" name="nominee_other_name" value="{{ old('nominee_other_name', $draft_study_leave->nominee_other_name ?? '') }}">
                                <!-- Employee Details Card -->
                                <div id="nominee_other_details" class="nominee-details-card mt-2" style="display: none;">
                                    <div class="card bg-light border">
                                        <div class="card-body p-3">
                                            <div class="mb-2 pb-2 border-bottom">
                                                <h6 class="mb-0 fw-bold">
                                                    <span id="nominee_other_title">-</span> <span id="nominee_other_name_display">-</span>
                                                </h6>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Employee No</small>
                                                    <span class="fw-semibold" id="nominee_other_empno_display2">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Designation</small>
                                                    <span class="fw-semibold" id="nominee_other_destination_display">-</span>
                                                </div>
                                                <div class="col-md-4">
                                                    <small class="text-muted d-block">Department</small>
                                                    <span class="fw-semibold" id="nominee_other_department">-</span>
                                                </div>
                                            </div>
                                            <div class="row g-2 mt-2">
                                                <div class="col-md-12">
                                                    <small class="text-muted d-block">Faculty</small>
                                                    <span class="fw-semibold" id="nominee_other_faculty">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="nominee_other_destination" name="nominee_other_destination" value="{{ old('nominee_other_destination', $draft_study_leave->nominee_other_destination ?? '') }}">
                               
                                @if(!($readonly ?? true))
                                <!-- Consent Letter Section for Other -->
                                <div class="mt-3 p-3 bg-light border rounded">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-semibold mb-0">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>Consent Letter
                                        </label>
                                        <a href="{{ route('StudyLeave.consentLetter.download') }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fas fa-download me-1"></i>Download Template
                                        </a>
                                    </div>
                                    <input type="file" class="form-control" 
                                           id="consent_letter_other" 
                                           name="consent_letter_other" 
                                           accept="application/pdf">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> Upload signed consent letter (PDF only)
                                    </small>
                                    @if(isset($draft_study_leave->consent_letter_other_path) && $draft_study_leave->consent_letter_other_path)
                                        <div class="mt-2 d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-secondary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#previewOtherModal">
                                                <i class="fas fa-eye me-1"></i>Preview Uploaded Letter
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" 
                                                    onclick="removeConsentLetter('other', {{ $draft_study_leave->id }})">
                                                <i class="fas fa-trash me-1"></i>Remove
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                @else
                                @if(isset($draft_study_leave->consent_letter_other_path) && $draft_study_leave->consent_letter_other_path)
                                    <div class="mt-2">
                                        <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'other', 'id' => $draft_study_leave->id]) }}"
                                           target="_blank" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-eye me-1"></i>Preview Consent Letter
                                        </a>
                                    </div>
                                @endif
                                @endif
                            </div>
                          
                        
                    
                    </div>

                     
            </div>

            <style>
    /* Search Employee Input Styling */
    .search-employee {
        border-left: none;
        padding-left: 0.5rem;
    }
    
    .search-employee:focus {
        border-color: #80bdff;
        box-shadow: none;
    }
    
    .search-employee.is-invalid {
        border-color: #dc3545;
    }
    
    .input-group:focus-within .input-group-text {
        border-color: #80bdff;
    }
    
    .input-group.border-danger {
        border: 1px solid #dc3545 !important;
        border-radius: 0.25rem;
    }
    
    .input-group.border-danger .input-group-text,
    .input-group.border-danger .search-employee {
        border-color: #dc3545 !important;
    }
    
    .input-group-text {
        border-right: none;
    }
    
    /* Error message styling */
    .invalid-feedback {
        display: block;
        margin-top: 0.25rem;
        font-size: 0.875rem;
        color: #dc3545;
    }
    
    /* Shake animation for error */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .shake-animation {
        animation: shake 0.5s ease-in-out;
    }
    
    .autocomplete-wrapper {
        position: relative;
    }
    .autocomplete-results {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1000;
        max-height: 200px;
        overflow-y: auto;
        background: white;
        border: 1px solid #ced4da;
        border-top: none;
        border-radius: 0 0 0.25rem 0.25rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: none;
    }
    .autocomplete-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #f0f0f0;
    }
    .autocomplete-item:hover {
        background-color: #f8f9fa;
    }
    .autocomplete-item:last-child {
        border-bottom: none;
    }
    .autocomplete-item .employee-no {
        font-weight: 600;
        color: #495057;
    }
    .autocomplete-item .employee-name {
        font-size: 0.875rem;
        color: #6c757d;
        margin-left: 8px;
    }
    
    /* Nominee Details Card Styling */
    .nominee-details-card {
        animation: slideDown 0.3s ease-out;
    }
    
    .nominee-details-card .card {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    
    .nominee-details-card .card-body {
        padding: 1rem;
    }
    
    .nominee-details-card small {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .nominee-details-card .fw-semibold {
        font-size: 0.95rem;
        color: #212529;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .nominee-details-card .col-md-4 {
        border-right: 1px solid rgba(0, 0, 0, 0.1);
    }
    
    .nominee-details-card .col-md-4:last-child {
        border-right: none;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Prevent Enter key from submitting the form when focused in single-line text inputs
    // This avoids accidental submits when a user presses Enter after filling the last field.
    $('#leave-form').on('keydown', 'input[type="text"]', function (e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    // Debounce function to limit API calls
    let searchTimeout = null;

    function searchEmployees(query, resultsSelector) {
        if (query.length < 2) {
            $(resultsSelector).hide().empty();
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            $.ajax({
                url: '{{ route("StudyLeave.searchAcademicEmployees") }}',
                type: 'GET',
                data: { query: query },
                dataType: 'json',
                success: function(employees) {
                    $(resultsSelector).empty();
                    
                    if (employees && employees.length > 0) {
                        employees.forEach(function(employee) {
                            const item = $('<div class="autocomplete-item"></div>')
                                .html('<span class="employee-no">' + employee.employee_no + '</span>' +
                                      '<span class="employee-name">' + employee.name + '</span>')
                                .data('employee', employee);
                            $(resultsSelector).append(item);
                        });
                        $(resultsSelector).show();
                    } else {
                        $(resultsSelector).html('<div class="autocomplete-item">No results found</div>').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', error);
                    $(resultsSelector).hide().empty();
                }
            });
        }, 300); // 300ms debounce
    }

    function selectEmployee(employee, empnoSelector, nameSelector, destinationSelector, resultsSelector) {
        $(empnoSelector).val(employee.employee_no);
        $(nameSelector).val(employee.name);
        $(resultsSelector).hide().empty();
        
        // Lookup full details including destination
        lookupEmployee(empnoSelector, nameSelector, destinationSelector);
    }

    function lookupEmployee(empInputSelector, nameOutputSelector, destinationOutputSelector) {
        var empno = $(empInputSelector).val() ? $(empInputSelector).val().trim() : '';
        var prefix = empInputSelector.replace('#nominee_', '').replace('_empno', '');
        
        if (!empno) {
            $(nameOutputSelector).val('');
            $(destinationOutputSelector).val('');
            // Hide details card and clear error
            $('#nominee_' + prefix + '_details').hide();
            clearEmployeeError(prefix);
            return;
        }

        $.ajax({
            url: '{{ url("StudyLeave/get-employee-info") }}/' + encodeURIComponent(empno),
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response && response.success && response.data && response.data.name) {
                    $(nameOutputSelector).val(response.data.name);
                    $(destinationOutputSelector).val(response.data.designation);
                    
                    // Update the details card with all information
                    $('#nominee_' + prefix + '_empno_display').text(empno || '-');
                    $('#nominee_' + prefix + '_empno_display2').text(empno || '-');
                    $('#nominee_' + prefix + '_title').text(response.data.title || '-');
                    $('#nominee_' + prefix + '_name_display').text(response.data.name || '-');
                    $('#nominee_' + prefix + '_destination_display').text(response.data.designation || '-');
                    $('#nominee_' + prefix + '_department').text(response.data.department || '-');
                    $('#nominee_' + prefix + '_faculty').text(response.data.faculty || '-');
                    
                    // Show the details card with animation
                    $('#nominee_' + prefix + '_details').slideDown(300);
                    
                    // Clear any error states
                    clearEmployeeError(prefix);
                } else {
                    $(nameOutputSelector).val('Not found');
                    $(destinationOutputSelector).val('Not found');
                    // Hide details card and show error
                    $('#nominee_' + prefix + '_details').slideUp(300);
                    showEmployeeError(prefix, 'Not found');
                    console.warn('Lookup returned no name for', empno, response);
                }
            },
            error: function (xhr, status, error) {
                console.error('Employee lookup error for', empno, status, error, xhr.responseText);
                $(nameOutputSelector).val('Lookup failed');
                $(destinationOutputSelector).val('Lookup failed');
                // Hide details card and show error
                $('#nominee_' + prefix + '_details').slideUp(300);
                showEmployeeError(prefix, 'Not found');
            }
        });
    }
    
    // Function to show employee lookup error
    function showEmployeeError(prefix, message) {
        const $input = $('#nominee_' + prefix + '_empno');
        const $inputGroup = $input.closest('.input-group');
        const $error = $('#nominee_' + prefix + '_error');
        const $helper = $('#nominee_' + prefix + '_helper');
        
        // Add error styling
        $input.addClass('is-invalid');
        $inputGroup.addClass('border-danger');
        
        // Update and show error message
        if (message) {
            $error.html('<i class="fas fa-exclamation-circle"></i> ' + message);
        }
        $error.show();
        $helper.hide();
        
        // Add shake animation
        $inputGroup.addClass('shake-animation');
        setTimeout(() => {
            $inputGroup.removeClass('shake-animation');
        }, 500);
    }
    
    // Function to clear employee lookup error
    function clearEmployeeError(prefix) {
        const $input = $('#nominee_' + prefix + '_empno');
        const $inputGroup = $input.closest('.input-group');
        const $error = $('#nominee_' + prefix + '_error');
        const $helper = $('#nominee_' + prefix + '_helper');
        
        // Remove error styling
        $input.removeClass('is-invalid');
        $inputGroup.removeClass('border-danger');
        
        // Hide error and show helper
        $error.hide();
        $helper.show();
    }

    // Setup autocomplete for each employee field
    function setupAutocomplete(empnoSelector, nameSelector, destinationSelector, resultsSelector) {
        // Wrap input-group in autocomplete wrapper if not already wrapped
        const $empnoInput = $(empnoSelector);
        const $inputGroup = $empnoInput.closest('.input-group');
        
        if ($inputGroup.length && !$inputGroup.parent().hasClass('autocomplete-wrapper')) {
            $inputGroup.wrap('<div class="autocomplete-wrapper"></div>');
        } else if (!$empnoInput.parent().hasClass('autocomplete-wrapper')) {
            $empnoInput.wrap('<div class="autocomplete-wrapper"></div>');
        }
        
        // Add results div after wrapper
        const $wrapper = $inputGroup.length ? $inputGroup.parent('.autocomplete-wrapper') : $empnoInput.parent('.autocomplete-wrapper');
        if ($wrapper.find(resultsSelector).length === 0) {
            $wrapper.append('<div class="autocomplete-results" id="' + resultsSelector.substring(1) + '"></div>');
        }

        // Handle input event for search
        $empnoInput.on('input', function() {
            const query = $(this).val().trim();
            searchEmployees(query, resultsSelector);
        });

        // Handle name field input for search
        $(nameSelector).on('input', function() {
            const query = $(this).val().trim();
            searchEmployees(query, resultsSelector);
        });

        // Handle clicking on autocomplete item
        $(document).on('click', resultsSelector + ' .autocomplete-item', function() {
            const employee = $(this).data('employee');
            if (employee) {
                selectEmployee(employee, empnoSelector, nameSelector, destinationSelector, resultsSelector);
            }
        });

        // Hide results when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.autocomplete-wrapper').length) {
                $(resultsSelector).hide();
            }
        });
    }

    // Setup autocomplete for all three employee fields
    setupAutocomplete('#nominee_teaching_empno', '#nominee_teaching_name', '#nominee_teaching_destination', '#teaching-results');
    setupAutocomplete('#nominee_admin_empno', '#nominee_admin_name', '#nominee_admin_destination', '#admin-results');
    setupAutocomplete('#nominee_other_empno', '#nominee_other_name', '#nominee_other_destination', '#other-results');

    $('#nominee_teaching_empno').on('change', function () {
        lookupEmployee('#nominee_teaching_empno', '#nominee_teaching_name','#nominee_teaching_destination');
    });

    $('#nominee_admin_empno').on('change', function () {
        lookupEmployee('#nominee_admin_empno', '#nominee_admin_name','#nominee_admin_destination');
    });
    
    $('#nominee_other_empno').on('change', function () {
        lookupEmployee('#nominee_other_empno', '#nominee_other_name','#nominee_other_destination');
    });
    
    // Clear error when user starts typing
    $('#nominee_teaching_empno, #nominee_admin_empno, #nominee_other_empno').on('input', function() {
        const prefix = $(this).attr('id').replace('nominee_', '').replace('_empno', '');
        clearEmployeeError(prefix);
    });
    
    // Validation for employee lookup - ensure employee is found before submission
    // Only validate forms that are not readonly
    $('form').on('submit', function(e) {
        // Skip validation for readonly forms (e.g., in view/accordion mode)
        @if($readonly ?? false)
            return true;
        @endif
        
        let isValid = true;
        let firstInvalidField = null;
        
        // Check teaching nominee
        if ($('#nominee_teaching_empno').val() && 
            ($('#nominee_teaching_name').val() === 'Not found' || 
             $('#nominee_teaching_name').val() === 'Lookup failed' || 
             $('#nominee_teaching_name').val() === '')) {
            showEmployeeError('teaching', 'Not found');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = $('#nominee_teaching_empno');
        }
        
        // Check admin nominee
        if ($('#nominee_admin_empno').val() && 
            ($('#nominee_admin_name').val() === 'Not found' || 
             $('#nominee_admin_name').val() === 'Lookup failed' || 
             $('#nominee_admin_name').val() === '')) {
            showEmployeeError('admin', 'Not found');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = $('#nominee_admin_empno');
        }
        
        // Check other nominee
        if ($('#nominee_other_empno').val() && 
            ($('#nominee_other_name').val() === 'Not found' || 
             $('#nominee_other_name').val() === 'Lookup failed' || 
             $('#nominee_other_name').val() === '')) {
            showEmployeeError('other', 'Not found');
            isValid = false;
            if (!firstInvalidField) firstInvalidField = $('#nominee_other_empno');
        }
        
        if (!isValid) {
            e.preventDefault();
            alert('Please ensure all employee numbers are valid and found in the system.');
            
            // Scroll to first invalid field
            if (firstInvalidField) {
                firstInvalidField[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalidField.focus();
            }
            return false;
        }
    });
    
    // Real-time uppercase conversion for employee numbers (don't convert during autocomplete)
    $('#nominee_teaching_empno, #nominee_admin_empno, #nominee_other_empno').on('input', function() {
        // Only uppercase if not actively searching (length < 2 or manual input)
        const val = this.value;
        if (val.length < 2 || /^[A-Z0-9]+$/.test(val)) {
            this.value = this.value.toUpperCase();
        }
    });
    
    // Bootstrap form validation
    $('.needs-validation').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
            
            // Scroll to first invalid field
            const firstInvalid = this.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
        $(this).addClass('was-validated');
    });

    // Auto-lookup employee names on page load if employee numbers exist
    ['#nominee_teaching_empno', '#nominee_admin_empno', '#nominee_other_empno'].forEach(function(selector) {
        var empInput = $(selector);
        if (empInput.val() && empInput.val().trim() !== '') {
            var nameSelector = selector.replace('_empno', '_name');
            var destinationSelector = selector.replace('_empno', '_destination');
            lookupEmployee(selector, nameSelector, destinationSelector);
        }
    });
});
</script>

<script>
function removeConsentLetter(type, studyLeaveId) {
    if (confirm('Are you sure you want to remove this consent letter? This action cannot be undone.')) {
        // Show loading state
        const button = event.target.closest('button');
        const originalHtml = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';
        
        // Send delete request
        fetch(`/StudyLeave/consent-letter/remove/${type}/${studyLeaveId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload the page to show updated state
                window.location.reload();
            } else {
                alert(data.message || 'Failed to remove consent letter.');
                button.disabled = false;
                button.innerHTML = originalHtml;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the consent letter.');
            button.disabled = false;
            button.innerHTML = originalHtml;
        });
    }
}
</script>

<!-- Preview Modals for Consent Letters -->
@if(isset($draft_study_leave))
    <!-- Teaching Consent Letter Preview Modal -->
    @if(isset($draft_study_leave->consent_letter_teaching_path) && $draft_study_leave->consent_letter_teaching_path)
    <div class="modal fade" id="previewTeachingModal" tabindex="-1" aria-labelledby="previewTeachingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewTeachingModalLabel">
                        <i class="fas fa-file-pdf text-danger me-2"></i>Teaching Nominee - Consent Letter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ route('StudyLeave.consentLetter.view', ['type' => 'teaching', 'id' => $draft_study_leave->id]) }}" 
                            style="width: 100%; height: 80vh; border: none;"
                            onerror="this.style.display='none'; document.getElementById('teaching-error').style.display='block';">
                    </iframe>
                    <div id="teaching-error" style="display: none; padding: 20px; text-align: center;">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                        <p class="mt-3">Unable to preview PDF. <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'teaching', 'id' => $draft_study_leave->id]) }}" target="_blank">Click here to open in new tab</a></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'teaching', 'id' => $draft_study_leave->id]) }}" 
                       class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Administrative Consent Letter Preview Modal -->
    @if(isset($draft_study_leave->consent_letter_admin_path) && $draft_study_leave->consent_letter_admin_path)
    <div class="modal fade" id="previewAdminModal" tabindex="-1" aria-labelledby="previewAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewAdminModalLabel">
                        <i class="fas fa-file-pdf text-danger me-2"></i>Administrative Nominee - Consent Letter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ route('StudyLeave.consentLetter.view', ['type' => 'administrative', 'id' => $draft_study_leave->id]) }}" 
                            style="width: 100%; height: 80vh; border: none;"
                            onerror="this.style.display='none'; document.getElementById('admin-error').style.display='block';">
                    </iframe>
                    <div id="admin-error" style="display: none; padding: 20px; text-align: center;">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                        <p class="mt-3">Unable to preview PDF. <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'administrative', 'id' => $draft_study_leave->id]) }}" target="_blank">Click here to open in new tab</a></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'administrative', 'id' => $draft_study_leave->id]) }}" 
                       class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Other Consent Letter Preview Modal -->
    @if(isset($draft_study_leave->consent_letter_other_path) && $draft_study_leave->consent_letter_other_path)
    <div class="modal fade" id="previewOtherModal" tabindex="-1" aria-labelledby="previewOtherModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewOtherModalLabel">
                        <i class="fas fa-file-pdf text-danger me-2"></i>Other Work Nominee - Consent Letter
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe src="{{ route('StudyLeave.consentLetter.view', ['type' => 'other', 'id' => $draft_study_leave->id]) }}" 
                            style="width: 100%; height: 80vh; border: none;"
                            onerror="this.style.display='none'; document.getElementById('other-error').style.display='block';">
                    </iframe>
                    <div id="other-error" style="display: none; padding: 20px; text-align: center;">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                        <p class="mt-3">Unable to preview PDF. <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'other', 'id' => $draft_study_leave->id]) }}" target="_blank">Click here to open in new tab</a></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('StudyLeave.consentLetter.view', ['type' => 'other', 'id' => $draft_study_leave->id]) }}" 
                       class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-1"></i>Open in New Tab
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endif