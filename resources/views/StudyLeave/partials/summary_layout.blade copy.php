<!-- Summary Layout Component -->
<div class="summary-container">
    
    <!-- Summary Header -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Review Your Application</strong>
        <p class="mb-0 mt-2">Please review all the information below carefully before submitting your study leave application.</p>
    </div>

    <!-- Step 1: Personal Details Summary -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-maroon">
                <i class="fas fa-user me-2"></i>Step 1: Personal Details
            </h6>
            <a href="{{ route('StudyLeave.BasicInfo.create') }}" class="btn btn-sm btn-outline-maroon">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @php
                    $sessionData = session('study_leave', []);
                @endphp
                
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Employee No:</span>
                        <span class="summary-value">{{ $sessionData['employee_no'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Passport No:</span>
                        <span class="summary-value">{{ $sessionData['passport_no'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Passport Validity:</span>
                        <span class="summary-value">{{ $sessionData['passport_validity'] ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Study Leave Details Summary -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-maroon">
                <i class="fas fa-graduation-cap me-2"></i>Step 2: Study Leave Details
            </h6>
            <a href="{{ route('StudyLeave.Details.create') }}" class="btn btn-sm btn-outline-maroon">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
        <div class="card-body">
            <div class="row g-2">
               
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Payment Type:</span>
                        <span class="summary-value">{{ $sessionData['leave_payment_type'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Period From:</span>
                        <span class="summary-value">{{ $sessionData['study_leave_from'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Period To:</span>
                        <span class="summary-value">{{ $sessionData['study_leave_to'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Degree Title:</span>
                        <span class="summary-value">{{ $sessionData['degree_title'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">University:</span>
                        <span class="summary-value">{{ $sessionData['university_institute'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Country:</span>
                        <span class="summary-value">{{ $sessionData['country'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Field of Study:</span>
                        <span class="summary-value">{{ $sessionData['field_of_study'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="summary-item">
                        <span class="summary-label">Study Program Details:</span>
                        <span class="summary-value">{{ $sessionData['study_program_details'] ?? 'N/A' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Funding Type:</span>
                        <span class="summary-value">{{ $sessionData['funding_type'] ?? 'N/A' }}</span>
                    </div>
                </div>
                @if(isset($sessionData['scholarship_source']))
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Scholarship Source:</span>
                        <span class="summary-value">{{ $sessionData['scholarship_source'] ?? 'N/A' }}</span>
                    </div>
                </div>
                @endif
                @if(isset($sessionData['scholarship_amount']))
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Scholarship Amount:</span>
                        <span class="summary-value">{{ $sessionData['scholarship_amount'] ?? 'N/A' }}</span>
                    </div>
                </div>
                @endif
                @if(isset($sessionData['project_name']))
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Project Name:</span>
                        <span class="summary-value">{{ $sessionData['project_name'] ?? 'N/A' }}</span>
                    </div>
                </div>
                @endif
                @if(isset($sessionData['air_passage_request']))
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Air Passage Request:</span>
                        <span class="summary-value">
                            <span class="badge bg-{{ $sessionData['air_passage_request'] === 'yes' ? 'success' : 'secondary' }}">
                                {{ strtoupper($sessionData['air_passage_request'] ?? 'N/A') }}
                            </span>
                        </span>
                    </div>
                </div>
                @endif
                @if(isset($sessionData['warm_cloth_allowance_request']))
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Warm Cloth Allowance:</span>
                        <span class="summary-value">
                            <span class="badge bg-{{ $sessionData['warm_cloth_allowance_request'] === 'yes' ? 'success' : 'secondary' }}">
                                {{ strtoupper($sessionData['warm_cloth_allowance_request'] ?? 'N/A') }}
                            </span>
                        </span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Step 3: Work Coverage Summary -->
    <div class="card mb-3 shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-maroon">
                <i class="fas fa-users me-2"></i>Step 3: Work Coverage Arrangements
            </h6>
            <a href="{{ route('StudyLeave.WorkCoveringPersons.create') }}" class="btn btn-sm btn-outline-maroon">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <!-- Teaching Nominee -->
                @if(isset($sessionData['nominee_teaching_empno']))
                <div class="col-md-12">
                    <div class="nominee-block p-3 bg-light rounded">
                        <h6 class="fw-bold text-secondary mb-2">
                            <i class="fas fa-chalkboard-teacher me-2"></i>Teaching Work Nominee
                        </h6>
                        <div class="summary-item mb-1">
                            <span class="summary-label">Employee No:</span>
                            <span class="summary-value">{{ $sessionData['nominee_teaching_empno'] ?? 'N/A' }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Name:</span>
                            <span class="summary-value">{{ $sessionData['nominee_teaching_name'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Admin Nominee -->
                @if(isset($sessionData['nominee_admin_empno']))
                <div class="col-md-12">
                    <div class="nominee-block p-3 bg-light rounded">
                        <h6 class="fw-bold text-secondary mb-2">
                            <i class="fas fa-user-tie me-2"></i>Administrative Work Nominee
                        </h6>
                        <div class="summary-item mb-1">
                            <span class="summary-label">Employee No:</span>
                            <span class="summary-value">{{ $sessionData['nominee_admin_empno'] ?? 'N/A' }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Name:</span>
                            <span class="summary-value">{{ $sessionData['nominee_admin_name'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Other Work Nominee -->
                @if(isset($sessionData['nominee_other_empno']))
                <div class="col-md-12">
                    <div class="nominee-block p-3 bg-light rounded">
                        <h6 class="fw-bold text-secondary mb-2">
                            <i class="fas fa-briefcase me-2"></i>Other Work Nominee
                        </h6>
                        <div class="summary-item mb-1">
                            <span class="summary-label">Employee No:</span>
                            <span class="summary-value">{{ $sessionData['nominee_other_empno'] ?? 'N/A' }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Name:</span>
                            <span class="summary-value">{{ $sessionData['nominee_other_name'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Handling Information -->
                @if(isset($sessionData['library_and_property_handling']))
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Library/Property Handling:</span>
                        <span class="summary-value">{{ $sessionData['library_and_property_handling'] ?? 'N/A' }}</span>
                    </div>
                </div>
                @endif
                @if(isset($sessionData['loan_handling']))
                <div class="col-md-6">
                    <div class="summary-item">
                        <span class="summary-label">Loan Handling:</span>
                        <span class="summary-value">{{ $sessionData['loan_handling'] ?? 'N/A' }}</span>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Declaration -->
    <!--
    <div class="card mb-4 border-warning">
        <div class="card-body">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="declaration" required>
                <label class="form-check-label" for="declaration">
                    <strong>I hereby declare that</strong> all the information provided above is true and accurate to the best of my knowledge. 
                    I understand that providing false information may result in the rejection of my application or disciplinary action.
                </label>
            </div>
        </div>
    </div>
    -->
    <!-- Action Buttons -->
    <!--
    <div class="d-flex justify-content-between align-items-center">
        <a href="{{ route('StudyLeave.Handeling.create') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Previous
        </a>
        
        <div>
            <button type="button" class="btn btn-secondary me-2" onclick="saveDraft()">
                <i class="fas fa-save me-2"></i>Save as Draft
            </button>
            <button type="submit" class="btn btn-maroon" id="submitBtn" disabled>
                <i class="fas fa-paper-plane me-2"></i>Submit Application
            </button>
        </div>
    </div>
</div>
-->
<!-- Styles -->
<style>
.summary-container {
    max-width: 1200px;
    margin: 0 auto;
}

.summary-item {
    margin-bottom: 10px;
}

.summary-label {
    font-weight: 600;
    color: #6c757d;
    display: inline-block;
    min-width: 180px;
    font-size: 0.9rem;
}

.summary-value {
    color: #212529;
    font-weight: 500;
    font-size: 0.95rem;
}

.nominee-block {
    border-left: 4px solid #800000;
    transition: all 0.3s ease;
}

.nominee-block:hover {
    transform: translateX(5px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header h6 {
    font-size: 1rem;
}

.btn-outline-maroon {
    color: #800000;
    border-color: #800000;
}

.btn-outline-maroon:hover {
    background-color: #800000;
    color: white;
}

.btn-maroon {
    background-color: #800000;
    border-color: #800000;
    color: white;
}

.btn-maroon:hover {
    background-color: #a52a2a;
    border-color: #a52a2a;
}

.btn-maroon:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
    cursor: not-allowed;
}

.text-maroon {
    color: #800000;
}

@media (max-width: 768px) {
    .summary-label {
        min-width: 120px;
        font-size: 0.85rem;
    }
    
    .summary-value {
        font-size: 0.9rem;
    }
    
    .card-header .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Print Styles */
@media print {
    .btn, .card-header a {
        display: none !important;
    }
    
    .card {
        page-break-inside: avoid;
    }
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const declaration = document.getElementById('declaration');
    const submitBtn = document.getElementById('submitBtn');
    
    // Enable submit button only when declaration is checked
    if (declaration) {
        declaration.addEventListener('change', function() {
            submitBtn.disabled = !this.checked;
        });
    }
});

function saveDraft() {
    if (confirm('Are you sure you want to save this application as a draft? You can continue editing it later.')) {
        // Add your draft saving logic here
        alert('Draft saved successfully!');
        window.location.href = "{{ route('StudyLeave.create') }}";
    }
}
</script>
