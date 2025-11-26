 <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Personal Details
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="row g-3">

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Employee No</label>
                                            <input type="text" name="empno" class="form-control" value="{{ $user->empno }}" readonly>
                                        </div>
                                        <!-- Name with Initials - Auto Filled -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Name with Initials</label>
                                            <input type="text" name="name_with_initials" class="form-control" value="{{ $user->name_with_initials }}" readonly>
                                        </div>
                                        <!-- Designation - Auto Filled -->
                                         <div class="col-md-6">
                                            <label class="form-label fw-semibold">Designation</label>
                                            <input type="text" name="designation" class="form-control" value="{{ $user->designation }}" readonly>
                                        </div>
                                       <!-- Department - Auto Filled -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Department</label>
                                            <input type="text" name="department" class="form-control" value="{{ $user->department }}" readonly>
                                        </div>
                                        <!-- Faculty - Auto Filled -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Faculty</label>
                                            <input type="text" name="faculty" class="form-control" value="{{ $user->faculty }}" readonly>
                                        </div>
                                        <!-- Email Address - Auto Filled -->
                                      <div class="col-md-6">
                                            <label class="form-label fw-semibold">Email Address</label>
                                            <input type="text" name="email" class="form-control" value="{{ $user->email }}" readonly>
                                        </div>
                                        <!-- Passport No -->
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Passport No: <span class="text-danger">*</span></label>
                                            <input type="text" name="passport_no" class="form-control" 
                                                   value="{{ $draft_study_leave->passport_no ?? '' }}" 
                                                   pattern="[A-Z0-9]{6,15}" 
                                                   title="Passport number must be 6-15 characters (uppercase letters and numbers only)"
                                                   required 
                                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                                            <div class="invalid-feedback">
                                                Please enter a valid passport number (6-15 characters, uppercase letters and numbers only).
                                            </div>
                                        </div>
                                        <!-- Passport Validity -->
                                        
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">Validity date up to: <span class="text-danger">*</span></label>
                                            <input type="date" name="passport_validity" class="form-control" 
                                                   value="{{ $draft_study_leave->passport_validity ?? '' }}" 
                                                   min="{{ date('Y-m-d') }}" 
                                                   required 
                                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                                            <div class="invalid-feedback">
                                                Please enter a valid future date for passport validity.
                                            </div>
                                        </div>
            </div>
        </div>

<style>
.form-control:invalid, .form-select:invalid {
    border-color: #dc3545;
}
.form-control:valid, .form-select:valid {
    border-color: #198754;
}
.was-validated .form-control:invalid, .was-validated .form-select:invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
}
.invalid-feedback {
    display: none;
    font-size: 0.875em;
    color: #dc3545;
}
.was-validated .form-control:invalid ~ .invalid-feedback,
.was-validated .form-select:invalid ~ .invalid-feedback {
    display: block;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap form validation
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
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
    
    // Real-time passport number validation
    const passportInput = document.querySelector('input[name="passport_no"]');
    if (passportInput) {
        passportInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
</script>