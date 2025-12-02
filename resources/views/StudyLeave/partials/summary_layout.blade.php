<!-- Summary Layout Component -->
<div class="summary-container">
    
    <!-- Summary Header -->
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Review Your Application</strong>
        <p class="mb-0 mt-2">Please review all the information below carefully before submitting your study leave application.</p>
    </div>

    <!-- Step 1: Personal Details Summary -->
   
        @include('StudyLeave.basic_info_form')
        
   

    <!-- Step 2: Study Leave Details Summary -->
    @include('StudyLeave.details_form')
    

    <!-- Step 3: Work Coverage Summary -->
    @include('StudyLeave.working_covering_persons_form')
    

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
