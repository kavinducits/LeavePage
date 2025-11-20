 <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Personal Details
            </div>

            <!-- Card Body -->
            <div class="card-body">
                <div class="row g-3">
                    <!-- Employee Number - Auto Filled -->
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
                        <label class="form-label fw-semibold">Passport No:</label>
                        <input type="text" name="passport_no" class="form-control" value="{{ $draft_study_leave->passport_no ?? '' }}" >
                    </div>
                    <!-- Passport Validity -->
                    
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Validity date up to:</label>
                        <input type="date" name="passport_validity" class="form-control" value="{{ $draft_study_leave->passport_validity ?? '' }}" >
                    </div>
            </div>
        </div>