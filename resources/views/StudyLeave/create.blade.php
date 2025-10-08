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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-file-alt me-2 icon-gold"></i>
                Application for Study Leave
                
            </h2>
        </div>
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <form  method="POST" enctype="multipart/form-data" id="leave-form">
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

        <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Personal Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Employee No</label>
                        <input type="text" name="empno" class="form-control" value="{{ $user->empno }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">NIC</label>
                        <input type="text" name="nic" class="form-control" value="{{ $user->nic }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Name with Initials</label>
                        <input type="text" class="form-control" value="{{ $user->name_with_initials }}" readonly>
                    </div>
                                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Names Denoted by Initials</label>
                        <input type="text" class="form-control" value="{{ $user->names_denoted_by_initials }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Department</label>
                        <input type="text" class="form-control" value="{{ $user->department }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Faculty</label>
                        <input type="text" class="form-control" value="{{ $user->faculty }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Designation</label>
                        <input type="text" class="form-control" value="{{ $user->designation }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Mobile</label>
                        <input type="text" class="form-control" value="{{ $user->mobile }}" readonly>
                    </div>
            </div>
        </div>

    </form>

    <script>
        document.getElementById('cancel-btn').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: 'Are you sure you want to cancel and delete this draft?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-draft-form').submit();
                }
            });
        });
    </script>

</div>

<script>
    const today = new Date().toISOString().split('T')[0];
    document.getElementById("fromDate").setAttribute('min', today);
    document.getElementById("toDate").setAttribute('min', today);

    function setFormStatus(status) {
        document.getElementById("formStatus").value = status;
    }

    // Calculate duration excluding weekends
    document.getElementById("fromDate").addEventListener('change', calculateDuration);
    document.getElementById("toDate").addEventListener('change', calculateDuration);

    const previousLeaves = @json($previousLeaves);

    function calculateDuration() {
        const fromVal = document.getElementById("fromDate").value;
        const toVal = document.getElementById("toDate").value;

        if (!fromVal || !toVal) return;

        const from = new Date(fromVal);
        const to = new Date(toVal);

        if (to < from) {
            document.getElementById("duration").value = '';
            return;
        }

        for (let leave of previousLeaves) {
            let prevFrom = new Date(leave.from_date);
            let prevTo = new Date(leave.to_date);

            if ((from >= prevFrom && from <= prevTo) || (to >= prevFrom && to <= prevTo)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Date Conflict',
                    text: 'Selected range overlaps with an already approved leave.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#ffc107'
                });
                document.getElementById("fromDate").value = '';
                document.getElementById("toDate").value = '';
                document.getElementById("duration").value = '';
                return;
            }
        }

        let count = 0;
        let current = new Date(from);
        while (current <= to) {
            if (current.getDay() !== 0 && current.getDay() !== 6) count++;
            current.setDate(current.getDate() + 1);
        }
        document.getElementById("duration").value = count;
    }

    // Calculate duration on page load if editing
    @if(isset($leave))
        calculateDuration();
    @endif

    // Validation function for compulsory fields
    function validateAndSubmit(formStatus) {
        console.log('validateAndSubmit called with formStatus:', formStatus);

        // Hide all previous error messages
        hideAllErrors();

        let isValid = true;

        // Validate Leave Type
        const leaveType = document.querySelector('[name="leave_type"]');
        if (!leaveType || !leaveType.value) {
            showError('leave_type_error');
            isValid = false;
        }

        // Validate Start Date
        const fromDate = document.querySelector('[name="from_date"]');
        if (!fromDate || !fromDate.value) {
            showError('from_date_error');
            isValid = false;
        }

        // Validate End Date
        const toDate = document.querySelector('[name="to_date"]');
        if (!toDate || !toDate.value) {
            showError('to_date_error');
            isValid = false;
        }

        // Validate Duration
        const duration = document.querySelector('[name="duration"]');
        if (!duration || !duration.value || duration.value <= 0) {
            showError('duration_error');
            isValid = false;
        }

        // Leave Request Document is optional; no validation enforced here
        // Validate Consent Letter (check if files are uploaded or temporary files exist)
        const consentLetterTags = document.getElementById('consent_letter_tags');
        const hasConsentLetters = (consentLetterTags && consentLetterTags.children.length > 0) ||
                                 (typeof tempConsentFiles !== 'undefined' && tempConsentFiles.length > 0);
        if (!hasConsentLetters) {
            showError('consent_letter_required');
            isValid = false;
        }

        // Validate Confirmation Checkbox
        const confirmCheckbox = document.getElementById('confirm_checkbox');
        if (!confirmCheckbox || !confirmCheckbox.checked) {
            showError('confirm_error');
            isValid = false;
        }

        // Travel details validation is handled by the backend
        // Frontend only validates basic required fields

        // If all validations pass, submit the form
        if (isValid) {
            console.log('Validation passed, submitting form');
            setFormStatus(formStatus);
            document.querySelector('form').submit();
        } else {
            console.log('Validation failed');
            // Scroll to the first error in document order
            scrollToFirstError();
        }
    }

    // Helper function to show error message
    function showError(errorId) {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.classList.remove('d-none');
        }
    }

    // Helper function to hide all error messages
    function hideAllErrors() {
        const errorElements = [
            'leave_type_error',
            'from_date_error',
            'to_date_error',
            'duration_error',
            'leave_document_error',
            'consent_letter_required',
            'confirm_error'
        ];

        errorElements.forEach(function(errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.add('d-none');
            }
        });
    }

    // Helper function to scroll to first error in document order
    function scrollToFirstError() {
        const errorSelectors = [
            '#leave_type_error:not(.d-none)',
            '#from_date_error:not(.d-none)',
            '#to_date_error:not(.d-none)',
            '#duration_error:not(.d-none)',
            '#leave_document_error:not(.d-none)',
            '#consent_letter_required:not(.d-none)',
            '#confirm_error:not(.d-none)'
        ];

        for (let selector of errorSelectors) {
            const errorElement = document.querySelector(selector);
            if (errorElement) {
                // Add a small delay to ensure the error is visible
                setTimeout(function() {
                    errorElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });
                    // Add a highlight effect
                    errorElement.style.fontWeight = 'bold';
                    setTimeout(function() {
                        errorElement.style.fontWeight = '500';
                    }, 2000);
                }, 100);
                break;
            }
        }
    }

    // Hide errors when user starts filling the fields
    document.querySelector('[name="leave_type"]').addEventListener('change', function() {
        document.getElementById('leave_type_error').classList.add('d-none');
    });

    document.querySelector('[name="from_date"]').addEventListener('change', function() {
        document.getElementById('from_date_error').classList.add('d-none');
    });

    document.querySelector('[name="to_date"]').addEventListener('change', function() {
        document.getElementById('to_date_error').classList.add('d-none');
    });

    document.querySelector('[name="duration"]').addEventListener('input', function() {
        document.getElementById('duration_error').classList.add('d-none');
    });

    document.getElementById('confirm_checkbox').addEventListener('change', function() {
        document.getElementById('confirm_error').classList.add('d-none');
    });

    // Add event listener for submit button with validation
    document.getElementById('submit-btn').addEventListener('click', function(e) {
        e.preventDefault();

        // Validate required fields
        if (validateRequiredFields()) {
            // If validation passes, submit with form_status = 2
            setFormStatus(2);
            document.querySelector('form').submit();
        }
    });

    // Function to validate only required fields
    function validateRequiredFields() {
        console.log('Validating required fields for submit');

        // Hide all previous error messages
        hideAllErrors();

        let isValid = true;

        // Validate Leave Type
        const leaveType = document.querySelector('[name="leave_type"]');
        if (!leaveType || !leaveType.value) {
            showError('leave_type_error');
            isValid = false;
        }

        // Validate Start Date
        const fromDate = document.querySelector('[name="from_date"]');
        if (!fromDate || !fromDate.value) {
            showError('from_date_error');
            isValid = false;
        }

        // Validate End Date
        const toDate = document.querySelector('[name="to_date"]');
        if (!toDate || !toDate.value) {
            showError('to_date_error');
            isValid = false;
        }

        // Validate Duration
        const duration = document.querySelector('[name="duration"]');
        if (!duration || !duration.value || duration.value <= 0) {
            showError('duration_error');
            isValid = false;
        }

        // Validate Consent Letter (check if files are uploaded or temporary files exist)
        const consentLetterTags = document.getElementById('consent_letter_tags');
        const hasConsentLetters = (consentLetterTags && consentLetterTags.children.length > 0) ||
                                 (typeof tempConsentFiles !== 'undefined' && tempConsentFiles.length > 0);
        if (!hasConsentLetters) {
            showError('consent_letter_required');
            isValid = false;
        }

        // Validate Confirmation Checkbox
        const confirmCheckbox = document.getElementById('confirm_checkbox');
        if (!confirmCheckbox || !confirmCheckbox.checked) {
            showError('confirm_error');
            isValid = false;
        }

        // Validate Travel Details - check if at least one entry exists
        const travelDetailsTable = document.getElementById('travel-details-tbody');
        let hasValidTravelDetails = false;

        if (travelDetailsTable && travelDetailsTable.children.length > 0) {
            // Count rows that are not the "no-travel-details" placeholder
            const validRows = Array.from(travelDetailsTable.children).filter(row =>
                row.id !== 'no-travel-details'
            );
            if (validRows.length > 0) {
                hasValidTravelDetails = true;
            }
        }

        // Also check temporary travel details (newly added)
        if (!hasValidTravelDetails && typeof tempTravelDetails !== 'undefined' && tempTravelDetails.length > 0) {
            hasValidTravelDetails = true;
        }

        if (!hasValidTravelDetails) {
            showError('travel_details_error');
            isValid = false;
        }

        // If validation fails, show popup with missing fields and then scroll to first error
        if (!isValid) {
            console.log('Required field validation failed');
            showMissingFieldsPopup();
            scrollToFirstError();
        }

        return isValid;
    }

    // Function to show popup with missing fields
    function showMissingFieldsPopup() {
        let missingFields = [];

        // Check each field and add to missing list if invalid
        const leaveType = document.querySelector('[name="leave_type"]');
        if (!leaveType || !leaveType.value) {
            missingFields.push("• Leave Type");
        }

        const fromDate = document.querySelector('[name="from_date"]');
        if (!fromDate || !fromDate.value) {
            missingFields.push("• Start Date");
        }

        const toDate = document.querySelector('[name="to_date"]');
        if (!toDate || !toDate.value) {
            missingFields.push("• End Date");
        }

        const duration = document.querySelector('[name="duration"]');
        if (!duration || !duration.value || duration.value <= 0) {
            missingFields.push("• Duration");
        }

        const consentLetterTags = document.getElementById('consent_letter_tags');
        const hasConsentLetters = (consentLetterTags && consentLetterTags.children.length > 0) ||
                                 (typeof tempConsentFiles !== 'undefined' && tempConsentFiles.length > 0);
        if (!hasConsentLetters) {
            missingFields.push("• Consent Letter");
        }

        const confirmCheckbox = document.getElementById('confirm_checkbox');
        if (!confirmCheckbox || !confirmCheckbox.checked) {
            missingFields.push("• Confirmation Checkbox");
        }

        // Check travel details
        const travelDetailsTable = document.getElementById('travel-details-tbody');
        let hasValidTravelDetails = false;

        if (travelDetailsTable && travelDetailsTable.children.length > 0) {
            const validRows = Array.from(travelDetailsTable.children).filter(row =>
                row.id !== 'no-travel-details'
            );
            if (validRows.length > 0) {
                hasValidTravelDetails = true;
            }
        }

        if (!hasValidTravelDetails && typeof tempTravelDetails !== 'undefined' && tempTravelDetails.length > 0) {
            hasValidTravelDetails = true;
        }

        if (!hasValidTravelDetails) {
            missingFields.push("• Travel Details (at least one entry required)");
        }

        // Show popup with missing fields
        if (missingFields.length > 0) {
            const message = "Please fill in the following required fields:\n\n" + missingFields.join("\n");
            Swal.fire({
                icon: 'error',
                title: 'Missing Required Fields',
                html: message.replace(/\n/g, '<br>'),
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
        }
    }

    // For draft saving, remove required validation
    document.querySelector('button.btn-gold').addEventListener('click', function(e) {
        // Remove required for all fields when saving as draft
        document.querySelector('[name="leave_type"]').required = false;
        document.querySelector('[name="from_date"]').required = false;
        document.querySelector('[name="to_date"]').required = false;
        document.querySelector('[name="duration"]').required = false;
        document.querySelector('[name="confirm"]').required = false;
    });

    let leaveId = {{ isset($leave) ? $leave->id : 'null' }};

    // AJAX upload for temporary files (no draft required)
    function uploadFilesAJAX(inputId, type, tagsId) {
        const input = document.getElementById(inputId);
        const files = input.files;

        // Use temporary upload if no leaveId exists
        if (!leaveId) {
            uploadTempFiles(inputId, type, tagsId);
            return;
        }

        // Use existing draft upload if leaveId exists
        for (let i = 0; i < files.length; i++) {
            const formData = new FormData();
            formData.append('file', files[i]);
            formData.append('type', type);
            formData.append('leave_id', leaveId);
            formData.append('_token', '{{ csrf_token() }}');
            fetch("{{ route('leaves.uploadFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderFileTags(tagsId, data.files, type);
                    input.value = '';

                    // Hide error messages when files are uploaded
                    if (type === 'leave_document') {
                        document.getElementById('leave_document_error').classList.add('d-none');
                    } else if (type === 'consent_letter') {
                        document.getElementById('consent_letter_required').classList.add('d-none');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: data.error || 'Upload failed',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    }

    // In-memory storage for temporary files and travel details
    let tempLeaveFiles = [];
    let tempConsentFiles = [];
    let tempTravelDetails = [];

    // Upload temporary files (for new applications)
    function uploadTempFiles(inputId, type, tagsId) {
        const input = document.getElementById(inputId);
        const files = input.files;

        for (let i = 0; i < files.length; i++) {
            const formData = new FormData();
            formData.append('file', files[i]);
            formData.append('type', type);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('leaves.uploadTempFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Store in memory
                    if (type === 'leave_document') {
                        tempLeaveFiles.push(data.file_path);
                        updateHiddenInput('temp_leave_documents', tempLeaveFiles);
                    } else if (type === 'consent_letter') {
                        tempConsentFiles.push(data.file_path);
                        updateHiddenInput('temp_consent_letters', tempConsentFiles);
                    }

                    addTempFileTag(tagsId, data.file_path, data.file_name, type);

                    // Hide error messages when files are uploaded
                    if (type === 'leave_document') {
                        document.getElementById('leave_document_error').classList.add('d-none');
                    } else if (type === 'consent_letter') {
                        document.getElementById('consent_letter_required').classList.add('d-none');
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: data.error || 'Upload failed',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: 'Upload failed',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
            });
        }

        input.value = ''; // Clear the input
    }

    // Update hidden input with file paths
    function updateHiddenInput(inputId, fileArray) {
        document.getElementById(inputId).value = JSON.stringify(fileArray);
    }
    // Auto-upload when files are selected for leave documents
    document.getElementById('leave_document_input').addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadFilesAJAX('leave_document_input', 'leave_document', 'leave_document_tags');
        }
    });

    // Auto-upload when files are selected for consent letters
    document.getElementById('consent_letter_input').addEventListener('change', function() {
        if (this.files.length > 0) {
            uploadFilesAJAX('consent_letter_input', 'consent_letter', 'consent_letter_tags');
        }
    });

    // Function to add temporary file tag
    function addTempFileTag(tagsId, filePath, fileName, type) {
        const tagsDiv = document.getElementById(tagsId);
        const span = document.createElement('span');
        span.className = 'badge bg-secondary me-1';
        span.innerHTML = `
            <span class="text-white">${fileName}</span>
            <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-temp-file-btn"
                    data-type="${type}" data-file-path="${filePath}" aria-label="Delete"></button>
        `;
        tagsDiv.appendChild(span);
    }

    // Handle temporary file deletion
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-temp-file-btn')) {
            const type = e.target.getAttribute('data-type');
            const filePath = e.target.getAttribute('data-file-path');

            const formData = new FormData();
            formData.append('type', type);
            formData.append('file_path', filePath);
            formData.append('_token', '{{ csrf_token() }}');

            fetch("{{ route('leaves.deleteTempFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove from in-memory arrays
                    if (type === 'leave_document') {
                        tempLeaveFiles = tempLeaveFiles.filter(file => file !== filePath);
                        updateHiddenInput('temp_leave_documents', tempLeaveFiles);
                    } else if (type === 'consent_letter') {
                        tempConsentFiles = tempConsentFiles.filter(file => file !== filePath);
                        updateHiddenInput('temp_consent_letters', tempConsentFiles);
                    }

                    e.target.closest('.badge').remove();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: data.error || 'Delete failed',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Delete error:', error);
                Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: 'Delete failed',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
            });
        }
    });

    // Add drag and drop functionality for leave documents and consent letters
    function setupSpecificDragAndDrop(uploadArea, inputId, type, tagsId) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const input = document.getElementById(inputId);
                input.files = files;
                uploadFilesAJAX(inputId, type, tagsId);
            }
        });
    }

    // Setup drag and drop for specific upload areas
    const leaveDocUploadArea = document.querySelector('#leave_document_input').closest('.upload-area');
    const consentLetterUploadArea = document.querySelector('#consent_letter_input').closest('.upload-area');

    if (leaveDocUploadArea) {
        setupSpecificDragAndDrop(leaveDocUploadArea, 'leave_document_input', 'leave_document', 'leave_document_tags');
    }

    if (consentLetterUploadArea) {
        setupSpecificDragAndDrop(consentLetterUploadArea, 'consent_letter_input', 'consent_letter', 'consent_letter_tags');
    }
    // Render file tags
    function renderFileTags(tagsId, files, type) {
        const tagsDiv = document.getElementById(tagsId);
        tagsDiv.innerHTML = '';
        files.forEach(file => {
            const span = document.createElement('span');
            span.className = 'badge bg-secondary me-1';
            span.innerHTML = `<a href="/storage/${file}" target="_blank" class="text-white text-decoration-none">${file.split('/').pop()}</a> <button type="button" class="btn-close btn-close-white btn-sm ms-1 delete-file-btn" data-type="${type}" data-file="${file}" aria-label="Delete"></button>`;
            tagsDiv.appendChild(span);
        });
    }
    // AJAX delete for already uploaded files in drafts
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-file-btn')) {
            const type = e.target.getAttribute('data-type');
            const file = e.target.getAttribute('data-file');
            if (!leaveId) return;
            const formData = new FormData();
            formData.append('type', type);
            formData.append('file', file);
            formData.append('leave_id', leaveId);
            formData.append('_token', '{{ csrf_token() }}');
            fetch("{{ route('leaves.deleteFile') }}", {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderFileTags(type === 'leave_document' ? 'leave_document_tags' : 'consent_letter_tags', data.files, type);

                    // Show error messages if no files remain after deletion
                    if (data.files.length === 0) {
                        if (type === 'leave_document') {
                            // Don't show error for leave document as it's not always required
                        } else if (type === 'consent_letter') {
                            // Don't auto-show error, let validation handle it
                        }
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Delete Failed',
                        text: data.error || 'Delete failed',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });

    // Note: updateRequiredState function removed as validation is now handled by validateAndSubmit function
</script>

<style>
/* Travel Details Enhanced Styles */
.travel-details-card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.bg-gradient-primary {
    background: linear-gradient(135deg,rgb(4, 4, 4) 100%,rgb(4, 4, 4) 100%);
}

.travel-entry-card .card {
    transition: all 0.3s ease;
    border: 2px solid #e9ecef;
}

.travel-entry-card .card:hover {
    border-color: #00ff37;
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
}

.bg-info {
    --bs-bg-opacity: 1;
    background-color: rgb(4 4 4) !important;
}

.bg-success {
    --bs-bg-opacity: 1;
    background-color: rgb(4 4 4) !important;
}


.upload-area {
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    min-height: 150px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.upload-area:hover {
    border-color: #007bff !important;
    background-color: #f8f9ff !important;
}

.upload-area.dragover {
    border-color: #28a745 !important;
    background-color: #f8fff8 !important;
    transform: scale(1.02);
}

.upload-content {
    text-align: center;
    pointer-events: none;
}

.upload-content .btn-upload-trigger {
    pointer-events: auto;
}

.upload-content .btn {
    pointer-events: auto;
}

.uploaded-file-item {
    background-color: #f8f9fa;
    transition: all 0.2s ease;
}

.uploaded-file-item:hover {
    background-color: #e9ecef;
    border-color: #090a5f !important;
}

.file-name {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.btn-upload-trigger {
    transition: all 0.3s ease;
}

.btn-upload-trigger:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
}

.travel-entry-card .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Animation for new entries */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.travel-entry-card {
    animation: slideIn 0.3s ease-out;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .upload-area {
        min-height: 120px;
    }

    .upload-content i {
        font-size: 2rem !important;
    }

    .file-name {
        max-width: 150px;
    }
}

/* Loading spinner */
.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Alert positioning */
.alert.position-fixed {
    animation: slideInRight 0.3s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100%);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Travel Details Table Styles */
#travel-details-table-container {
    background-color: #f8f9fa;
    border-radius: 0.375rem;
    padding: 1.5rem;
    border: 1px solid #dee2e6;
}

#travel-details-table {
    margin-bottom: 0;
}

#travel-details-table th {
    background-color:rgb(3, 3, 3);
    color: white;
    font-weight: 600;
    border: none;
}

#travel-details-table td {
    vertical-align: middle;
    border-color: #dee2e6;
}

#travel-details-table tbody tr:hover {
    background-color: #e3f2fd;
}

.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

/* Validation Error Styling */
.text-danger.small {
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
    display: block;
    padding: 0.25rem 0;
    border-radius: 0.25rem;
}

.text-danger.small:not(.d-none) {
    animation: fadeIn 0.3s ease-in;
}

/* Scroll target highlighting */
.text-danger.small:target,
.text-danger.small:focus {
    background-color: rgba(220, 53, 69, 0.1);
    border-left: 3px solid #dc3545;
    padding-left: 0.5rem;
}

/* Header color change */

.text-maroon {
    color: rgba(0, 0, 0) !important;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Highlight required fields when validation fails */
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Enhanced checkbox container */
.form-check.enhanced {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 30px;
            background-color: #f8f9fa;
            margin-top: 1.5rem;
            transition: all 0.3s ease;
        }
        
        .form-check.enhanced:hover {
            border-color: #007bff;
            background-color: #e7f3ff;
        }
        
        .form-check.enhanced.checked {
            border-color: #28a745;
            background-color: #d4edda;
        }
        
        .form-check.enhanced.error {
            border-color: #dc3545;
            background-color: #f8d7da;
            animation: shake 0.5s ease-in-out;
        }
        
        /* Enhanced checkbox input */
        .form-check-input.enhanced {
            width: 25px;
            height: 25px;
            cursor: pointer;
            margin-right: 10px;
        }
        
        /* Enhanced label */
        .form-check-label.enhanced {
            cursor: pointer;
            font-weight: 600;
            color: #495057;
            font-size: 1.1rem;
        }

</style>

<script>
// No form data persistence - clean slate on every refresh
</script>

@endsection