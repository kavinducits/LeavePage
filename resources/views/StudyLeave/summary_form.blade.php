 <!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Summary and Submit
            </div>
             <div class="card mb-4">
            
            <!-- Card Body -->
            <div class="card-body">
            <div class="row g-3">

                <!-- Handling of Library Books and Other Properties -->
                
                <div class="col-12 mb-3">
                    <label for="library_and_property_handling" class="form-label fw-semibold d-block">
                         Library book, Computer or any other properties?
                    </label>
                    <select class="form-select w-auto d-inline-block align-middle ms-2" id="library_and_property_handling" name="library_and_property_handling" required>
                        <option value="" {{ empty(old('library_and_property_handling', $draft_study_leave->library_and_property_handling ?? '')) ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="Make Arrangements" {{ old('library_and_property_handling', $draft_study_leave->library_and_property_handling ?? '') === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="Not Make Arrangements" {{ old('library_and_property_handling', $draft_study_leave->library_and_property_handling ?? '') === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                </div>

                <!-- Handling of Paying Loans   -->

                <div class="col-12 mb-3">
                    <label for="loan_handling" class="form-label fw-semibold d-block">
                       Paying of Loans taken from University of UPF?
                        <br>
                        <small class="text-muted">(Applicable only when taking no pay leave)</small>
                    </label>
                    <select class="form-select w-auto d-inline-block align-middle ms-2" id="loan_handling" name="loan_handling" required>
                        <option value="" {{ empty(old('loan_handling', $draft_study_leave->loan_handling ?? '')) ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="Make Arrangements" {{ old('loan_handling', $draft_study_leave->loan_handling ?? '') === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="Not Make Arrangements" {{ old('loan_handling', $draft_study_leave->loan_handling ?? '') === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                </div>
            </div>
        </div>