@php
    $degrees = $degrees ?? ['MA', 'MSc', 'MBA', 'MPhil', 'MD', 'PhD'];
@endphp

 <!-- Form Card -->
    
        <div class="card mb-4">

            <!-- Card Header -->
            
            <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-user me-2"></i>Details of the Study Leave
                </span>
                @if($displayEditeBtn ?? false)
                    <a href="{{ route('StudyLeave.Details.create') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3">
                
                <!-- Study Leave Location -->
                <div class="col-12 mb-3">
                    <label class="form-label fw-semibold d-block">Is your study leave in Sri Lanka or abroad? <span class="text-danger">*</span></label>
                    <div class="d-inline-block">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="study_location" id="location_sri_lanka" value="Sri Lanka"
                                @checked(old('country', $draft_study_leave->country ?? '') === 'Sri Lanka') 
                                required 
                                {{ $readonly ?? true ? 'disabled' : '' }}>
                            <label class="form-check-label" for="location_sri_lanka">In Sri Lanka</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="study_location" id="location_abroad" value="Abroad"
                                @checked(old('country', $draft_study_leave->country ?? '') !== 'Sri Lanka' && !empty(old('country', $draft_study_leave->country ?? ''))) 
                                required 
                                {{ $readonly ?? true ? 'disabled' : '' }}>
                            <label class="form-check-label" for="location_abroad">Abroad</label>
                        </div>
                    </div>
                    @if(!($readonly ?? true))
                    @error('study_location')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror   
                    <div class="invalid-feedback" id="study_location_errors" style="display: none;">
                        Please select study leave location.
                    </div>
                    @endif
                </div>
                <script>
                // Client-side validation for Study Leave Location
                document.addEventListener('DOMContentLoaded', function () {
                    const studyLocationRadios = document.querySelectorAll('input[name="study_location"]');
                    const studyLocationError = document.getElementById('study_location_errors');
                    
                    studyLocationRadios.forEach(radio => {
                        radio.addEventListener('change', function () {
                            if (studyLocationError) {
                                studyLocationError.style.display = 'none';
                                studyLocationRadios.forEach(r => r.classList.remove('is-invalid'));
                            }
                        });
                    });
                });
                </script>
                
                          <!-- University or the Institute -->
                <div class="col-md-6">
                    <label class="form-label fw-semibold">University or the Institute <span class="text-danger">*</span></label>
                    <input type="text" name="university_institute" class="form-control @if(!($readonly ?? true)) @error('university_institute') is-invalid @enderror @endif" 
                           value="{{ $draft_study_leave->university_institute ?? old('university_institute') }}" 
                           @if(!($readonly ?? true)) minlength="3" maxlength="200" required @endif
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    @if(!($readonly ?? true))
                    @error('university_institute')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="university_institute_error" style="display: none;">
                        Please enter the university or institute name (3-200 characters).
                    </div>
                    @endif
                </div>
                <script>
                // Client-side validation for University/Institute field
                document.addEventListener('DOMContentLoaded', function () {
                    const universityInput = document.querySelector('input[name="university_institute"]');
                    const universityError = document.getElementById('university_institute_error');
                    universityInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        if (value.length < 3 || value.length > 200) {
                            universityInput.classList.add('is-invalid');
                            universityError.style.display = 'block';
                        } else {
                            universityInput.classList.remove('is-invalid');
                            universityError.style.display = 'none';
                        }
                    });
                });
                </script>

                                                                <!-- Country and Field of Study -->
                                                                <div class="col-md-6" id="country_field" style="transition: all 0.3s ease;">
                                                                    <label class="form-label fw-semibold">Country <span class="text-danger" id="country_required">*</span></label>
                                                                    <select name="country" id="country" class="form-select @if(!($readonly ?? true)) @error('country') is-invalid @enderror @endif" 
                                                                           @if(!($readonly ?? true)) required @endif
                                                                           {{ $readonly ?? true ? 'disabled' : '' }}>
                                                                        <option value="">Select a country</option>
                                                                        @if(!empty($draft_study_leave->country))
                                                                            <option value="{{ $draft_study_leave->country }}" selected>{{ $draft_study_leave->country }}</option>
                                                                        @endif
                                                                    </select>
                                                                    @if(!($readonly ?? true))
                                                                    @error('country')
                                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                    @enderror
                                                                    <div class="invalid-feedback" >
                                                                        Please select a country.
                                                                    </div>
                                                                    @endif
                                                                </div>

                                                                <script>
                                                                document.addEventListener('DOMContentLoaded', function () {
                                                                    const countrySelect = document.getElementById('country');
                                                                    const savedCountry = "{{ $draft_study_leave->country ?? '' }}";
                                                                    
                                                                    // Fetch countries from REST Countries API
                                                                    fetch('https://restcountries.com/v3.1/all?fields=name')
                                                                        .then(response => response.json())
                                                                        .then(data => {
                                                                            // Sort countries alphabetically and exclude Sri Lanka
                                                                            const countries = data
                                                                                .map(country => country.name.common)
                                                                                .filter(country => country !== 'Sri Lanka')
                                                                                .sort((a, b) => a.localeCompare(b));
                                                                            
                                                                            // Clear existing options except the first one
                                                                            countrySelect.innerHTML = '<option value="">Select a country</option>';
                                                                            
                                                                            // Add countries to select
                                                                            countries.forEach(country => {
                                                                                const option = document.createElement('option');
                                                                                option.value = country;
                                                                                option.textContent = country;
                                                                                if (savedCountry && country === savedCountry) {
                                                                                    option.selected = true;
                                                                                }
                                                                                countrySelect.appendChild(option);
                                                                            });
                                                                        })
                                                                        .catch(error => {
                                                                            console.error('Error fetching countries:', error);
                                                                            // Fallback: keep the saved value if API fails
                                                                            if (savedCountry && savedCountry !== 'Sri Lanka') {
                                                                                countrySelect.innerHTML = `
                                                                                    <option value="">Select a country</option>
                                                                                    <option value="${savedCountry}" selected>${savedCountry}</option>
                                                                                `;
                                                                            }
                                                                        });
                                                                });
                                                                </script>

                <!-- Passport Details (conditional - shown only for "Abroad") -->
                <div class="col-md-6" id="passport_no_field" style="display: none;">
                    <label class="form-label fw-semibold">Passport Number <span class="text-danger">*</span></label>
                    <input type="text" name="passport_no" id="passport_no" class="form-control @if(!($readonly ?? true)) @error('passport_no') is-invalid @enderror @endif" 
                           value="{{ $draft_study_leave->passport_no ?? old('passport_no') }}" 
                           @if(!($readonly ?? true)) minlength="6" maxlength="20" pattern="[A-Z0-9]+" @endif
                           title="Passport number should contain only uppercase letters and numbers"
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    @if(!($readonly ?? true))
                    @error('passport_no')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="passport_no_error" style="display: none;">
                        Please enter a valid passport number (6-20 characters, uppercase letters and numbers only).
                    </div>
                    @endif
                </div>
                <script>
                // Client-side validation for Passport Number field
                document.addEventListener('DOMContentLoaded', function () {
                    const passportInput = document.getElementById('passport_no');
                    const passportError = document.getElementById('passport_no_error');
                    passportInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        const pattern = /^[A-Z0-9]{6,20}$/;
                        if (!pattern.test(value)) {
                            passportInput.classList.add('is-invalid');
                            passportError.style.display = 'block';
                        } else {
                            passportInput.classList.remove('is-invalid');
                            passportError.style.display = 'none';
                        }
                    });
                });
                </script>

                <div class="col-md-6" id="passport_validity_field" style="display: none;">
                    <label class="form-label fw-semibold">Passport Validity Date <span class="text-danger">*</span></label>
                    <input type="date" name="passport_validity" id="passport_validity" class="form-control @if(!($readonly ?? true)) @error('passport_validity') is-invalid @enderror @endif" 
                           value="{{ $draft_study_leave->passport_validity ?? old('passport_validity') }}" 
                           @if(!($readonly ?? true)) min="{{ date('Y-m-d') }}" @endif
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    @if(!($readonly ?? true))
                    @error('passport_validity')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="passport_validity_error" style="display: none;">
                        Please enter a valid passport expiry date (must be a future date).
                    </div>
                    @endif
                </div>
                <script>
                // Client-side validation for Passport Validity Date field
                document.addEventListener('DOMContentLoaded', function () {
                    const validityInput = document.getElementById('passport_validity');
                    const validityError = document.getElementById('passport_validity_error');
                    validityInput.addEventListener('change', function () {
                        const selectedDate = new Date(this.value);
                        const today = new Date();
                        today.setHours(0, 0, 0, 0); // Set to start of the day
                        if (selectedDate <= today) {
                            validityInput.classList.add('is-invalid');
                            validityError.style.display = 'block';
                        } else {
                            validityInput.classList.remove('is-invalid');
                            validityError.style.display = 'none';
                        }
                    });
                });
                </script>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const studyLocationRadios = document.querySelectorAll('input[name="study_location"]');
                    const passportNoField = document.getElementById('passport_no_field');
                    const passportValidityField = document.getElementById('passport_validity_field');
                    const passportNoInput = document.getElementById('passport_no');
                    const passportValidityInput = document.getElementById('passport_validity');
                    
                    function updatePassportFields() {
                        const selectedLocation = document.querySelector('input[name="study_location"]:checked');
                        
                        if (selectedLocation && selectedLocation.value === 'Abroad') {
                            passportNoField.style.display = 'block';
                            passportValidityField.style.display = 'block';
                            passportNoInput.setAttribute('required', 'required');
                            passportValidityInput.setAttribute('required', 'required');
                        } else {
                            passportNoField.style.display = 'none';
                            passportValidityField.style.display = 'none';
                            passportNoInput.removeAttribute('required');
                            passportValidityInput.removeAttribute('required');
                            passportNoInput.value = '';
                            passportValidityInput.value = '';
                        }
                    }
                    
                    studyLocationRadios.forEach(radio => {
                        radio.addEventListener('change', updatePassportFields);
                    });
                    
                    // Initialize on page load
                    updatePassportFields();
                });
                </script>

                <!-- Field of Study and Degree Title -->
                <div  class="col-12">
                    <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Field of study <span class="text-danger">*</span></label>
                    <input type="text" name="field_of_study" class="form-control @if(!($readonly ?? true)) @error('field_of_study') is-invalid @enderror @endif" 
                           value="{{ $draft_study_leave->field_of_study ?? old('field_of_study') }}" 
                           @if(!($readonly ?? true)) minlength="3" maxlength="200" required @endif
                           {{ $readonly ?? true ? 'readonly' : '' }}>
                    @if(!($readonly ?? true))
                    @error('field_of_study')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="field_of_study_error" style="display: none;">
                        Please enter the field of study (3-200 characters).
                    </div>
                    @endif
                </div>
                <script>
                // Client-side validation for Field of Study field
                document.addEventListener('DOMContentLoaded', function () {
                    const fieldInput = document.querySelector('input[name="field_of_study"]');
                    const fieldError = document.getElementById('field_of_study_error');
                    fieldInput.addEventListener('input', function () {
                        const value = this.value.trim();
                        if (value.length < 3 || value.length > 200) {
                            fieldInput.classList.add('is-invalid');
                            fieldError.style.display = 'block';
                        } else {
                            fieldInput.classList.remove('is-invalid');
                            fieldError.style.display = 'none';
                        }
                    });
                });
                </script>

                <div class="col-md-6">
                    <label class="form-label fw-semibold d-block">Degree Titles <span class="text-danger">*</span></label>
                    @php
                        $selectedOtherDegree = (string) old('other_degree_title', $draft_study_leave->other_degree_title ?? '');
                        $selectedDegree = (string) old('degree_title', $draft_study_leave->degree_title ?? '');
                        if ($selectedDegree === '' && $selectedOtherDegree !== '' && old('degree_title') === null) {
                            $selectedDegree = 'Other';
                        }
                        $isOtherDegreeSelected = strtolower($selectedDegree) === 'other';
                    @endphp
                    <select name="degree_title" class="form-select study-leave-select @if(!($readonly ?? true)) @error('degree_title') is-invalid @enderror @endif" @if(!($readonly ?? true)) required @endif {{ $readonly ?? true ? 'disabled' : '' }}>
                        <option value="" {{ $selectedDegree === '' ? 'selected' : '' }}>Select degree title</option>
                       @foreach ($degrees as $degree)
                            @php
                                $degreeValue = is_object($degree) ? (string) $degree->id : (string) $degree;
                                $degreeLabel = is_object($degree) ? $degree->category_name : $degree;
                            @endphp
                            <option value="{{ $degreeValue }}" {{ $selectedDegree === $degreeValue ? 'selected' : '' }}>{{ $degreeLabel }}</option>
                       @endforeach
                        <option value="Other" {{ $selectedDegree === 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @if(!($readonly ?? true))
                    @error('degree_title')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="degree_title_error" style="display: none;">
                        Please select a degree title.
                    </div>
                    @endif

                    <div id="other_degree_title_wrapper" class="mt-3" style="display: {{ $isOtherDegreeSelected ? 'block' : 'none' }};">
                        <label class="form-label fw-semibold d-block study-leave-aligned-label">Other Degree Title <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="other_degree_title"
                            id="other_degree_title"
                            class="form-control @if(!($readonly ?? true)) @error('other_degree_title') is-invalid @enderror @endif"
                            value="{{ $selectedOtherDegree }}"
                            @if(!($readonly ?? true) && $isOtherDegreeSelected) required @endif
                            @if(!($readonly ?? true)) minlength="2" maxlength="255" @endif
                            {{ $readonly ?? true ? 'readonly' : '' }}>

                        @if(!($readonly ?? true))
                        @error('other_degree_title')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="other_degree_title_error" style="display: none;">
                            Please enter the other degree title.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
                </div>
                <script>
                // Client-side validation for Degree Title field
                document.addEventListener('DOMContentLoaded', function () {
                    const degreeSelect = document.querySelector('select[name="degree_title"]');
                    const degreeError = document.getElementById('degree_title_error');
                    const otherDegreeWrapper = document.getElementById('other_degree_title_wrapper');
                    const otherDegreeInput = document.getElementById('other_degree_title');
                    const otherDegreeError = document.getElementById('other_degree_title_error');

                    if (!degreeSelect) {
                        return;
                    }

                    function updateOtherDegreeVisibility() {
                        const isOtherSelected = (degreeSelect.value || '').toLowerCase() === 'other';

                        if (otherDegreeWrapper) {
                            otherDegreeWrapper.style.display = isOtherSelected ? 'block' : 'none';
                        }

                        if (otherDegreeInput) {
                            if (isOtherSelected) {
                                otherDegreeInput.setAttribute('required', 'required');
                            } else {
                                otherDegreeInput.removeAttribute('required');
                                otherDegreeInput.classList.remove('is-invalid');
                                if (otherDegreeError) {
                                    otherDegreeError.style.display = 'none';
                                }
                                otherDegreeInput.value = '';
                            }
                        }
                    }

                    function validateOtherDegree() {
                        if (!otherDegreeInput || !otherDegreeError) {
                            return;
                        }

                        const isOtherSelected = (degreeSelect.value || '').toLowerCase() === 'other';
                        if (!isOtherSelected) {
                            otherDegreeInput.classList.remove('is-invalid');
                            otherDegreeError.style.display = 'none';
                            return;
                        }

                        const value = otherDegreeInput.value.trim();
                        if (value.length < 2) {
                            otherDegreeInput.classList.add('is-invalid');
                            otherDegreeError.style.display = 'block';
                        } else {
                            otherDegreeInput.classList.remove('is-invalid');
                            otherDegreeError.style.display = 'none';
                        }
                    }

                    degreeSelect.addEventListener('change', function () {
                        if (this.value === '') {
                            degreeSelect.classList.add('is-invalid');
                            degreeError.style.display = 'block';
                        } else {
                            degreeSelect.classList.remove('is-invalid');
                            degreeError.style.display = 'none';
                        }

                        updateOtherDegreeVisibility();
                        validateOtherDegree();
                    });

                    if (otherDegreeInput) {
                        otherDegreeInput.addEventListener('input', validateOtherDegree);
                    }

                    updateOtherDegreeVisibility();
                    validateOtherDegree();
                });
                </script>
                   
                   
                <!-- Period of Study Leave Requested -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Period of Study Leave Requested <span class="text-danger">*</span></label>
                                        <div class="row g-3">
                                            <!-- From Date -->
                                            <div class="col-md-6">
                                                                                            
                                                                                    
                                                                                            <label class="form-label">From <span class="text-danger">*</span></label>
                                                                                            <input type="date" name="study_leave_from" id="study_leave_from" class="form-control @if(!($readonly ?? true)) @error('study_leave_from') is-invalid @enderror @endif" 
                                                                                                   value="{{optional($draft_study_leave)->study_leave_from ?? old('study_leave_from')}}" 
                                                                                                   @if(!($readonly ?? true)) min="{{ date('Y-m-d') }}" required @endif
                                                                                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                                                                                            @if(!($readonly ?? true))
                                                                                            @error('study_leave_from')
                                                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                                            @enderror
                                                                                            <div class="invalid-feedback" id="study_leave_from_error" style="display: none;">
                                                                                                Please select a valid start date.
                                                                                            </div>
                                                                                            @endif
                                                                                        </div>
                                                                                        <script>
                                                                                        // Client-side validation for Study Leave From Date field
                                                                                        document.addEventListener('DOMContentLoaded', function () {
                                                                                            const fromDateInput = document.getElementById('study_leave_from');
                                                                                            const fromDateError = document.getElementById('study_leave_from_error');
                                                                                            fromDateInput.addEventListener('change', function () {
                                                                                                const selectedDate = new Date(this.value);
                                                                                                const today = new Date();
                                                                                                today.setHours(0, 0, 0, 0); // Set to start of the day
                                                                                                if (selectedDate < today) {
                                                                                                    fromDateInput.classList.add('is-invalid');
                                                                                                    fromDateError.style.display = 'block';
                                                                                                } else {
                                                                                                    fromDateInput.classList.remove('is-invalid');
                                                                                                    fromDateError.style.display = 'none';
                                                                                                }
                                                                                            });
                                                                                        });
                                                                                        </script>

                                                                                        <!-- To Date -->
                                                                                        <div class="col-md-6">
                                                                                            <label class="form-label">To <span class="text-danger">*</span></label>
                                                                                            <input type="date" name="study_leave_to" id="study_leave_to" class="form-control @if(!($readonly ?? true)) @error('study_leave_to') is-invalid @enderror @endif" 
                                                                                                   value="{{optional($draft_study_leave)->study_leave_to ?? old('study_leave_to')}}" 
                                                                                                   @if(!($readonly ?? true)) required @endif
                                                                                                   {{ $readonly ?? true ? 'readonly' : '' }}
                                                                                                   disabled>
                                                                                            @if(!($readonly ?? true))
                                                                                            @error('study_leave_to')
                                                                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                                                            @enderror
                                                                                            <div class="invalid-feedback" id="study_leave_to_error" style="display: none;">
                                                                                                Please select a valid end date (must be on or after start date).
                                                                                            </div>
                                                                                            @endif
                                                                                        </div>
                                                                                    </div>
                                                                                </div>

                                                                                <script>
                                                                                document.addEventListener('DOMContentLoaded', function () {
                                                                                    const fromDate = document.getElementById('study_leave_from');
                                                                                    const toDate = document.getElementById('study_leave_to');
                                                                                    
                                                                                    const toDateError = document.getElementById('study_leave_to_error');
                                                                                    const totalDaysStudyLeave = {{ $totalDaysStydyLeave ?? 0 }};
                                                                                    
                                                                                    fromDate.addEventListener('change', function() {
                                                                                        if (this.value) {
                                                                                            toDate.disabled = false;
                                                                                            toDate.min = this.value;
                                                                                            
                                                                                            // Calculate max date: From date + totalDaysStudyLeave
                                                                                            const selectedDate = new Date(this.value);
                                                                                            const maxDate = new Date(selectedDate);
                                                                                            maxDate.setDate(maxDate.getDate() + 1095 - totalDaysStudyLeave);
                                                                                            
                                                                                            // Format max date as YYYY-MM-DD
                                                                                            const maxDateString = maxDate.toISOString().split('T')[0];
                                                                                            toDate.max = maxDateString;
                                                                                            
                                                                                            // If current "To" value exceeds max date, clear it
                                                                                            if (toDate.value && new Date(toDate.value) > maxDate) {
                                                                                                toDate.value = '';
                                                                                            }
                                                                                        } else {
                                                                                            toDate.disabled = true;
                                                                                            toDate.value = '';
                                                                                            toDate.removeAttribute('max');
                                                                                        }
                                                                                    });
                                                                                    
                                                                                    // Initialize on page load
                                                                                    if (fromDate.value) {
                                                                                        toDate.disabled = false;
                                                                                        
                                                                                        // Set max date on page load if From date exists
                                                                                        const selectedDate = new Date(fromDate.value);
                                                                                        const maxDate = new Date(selectedDate);
                                                                                        maxDate.setDate(maxDate.getDate() + 1095 - totalDaysStudyLeave);
                                                                                        const maxDateString = maxDate.toISOString().split('T')[0];
                                                                                        toDate.max = maxDateString;
                                                                                        toDate.min = fromDate.value;
                                                                                    }

                                                                                    toDate.addEventListener('change', function () {
                                                                                        const fromDateValue = new Date(fromDate.value);
                                                                                        const toDateValue = new Date(this.value);
                                                                                        
                                                                                        if (toDateValue < fromDateValue) {
                                                                                            toDate.classList.add('is-invalid');
                                                                                            toDateError.style.display = 'block';
                                                                                        } else {
                                                                                            toDate.classList.remove('is-invalid');
                                                                                            toDateError.style.display = 'none';
                                                                                        }
                                                                                    });
                                                                                });
                                                                                </script>



                <!-- Relevancy and Details of the Study Program -->

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Relevancy and Details of the Study Program</label>
                    <textarea name="study_program_details" class="form-control @if(!($readonly ?? true)) @error('study_program_details') is-invalid @enderror @endif" rows="4" 
                              {{ $readonly ?? true ? 'readonly' : '' }}>{{ $draft_study_leave->study_program_details ?? old('study_program_details') }}</textarea>
                    @if(!($readonly ?? true))
                    @error('study_program_details')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @endif
                </div>
                @php
                    $placementDocuments = collect();
                    $selfFundingDocuments = collect();

                    if (!empty($draft_study_leave->id)) {
                        $placementDocuments = \App\Models\StudyLeaveDocument::where('study_leave_id', $draft_study_leave->id)
                            ->where('document_type', \App\Models\StudyLeaveDocument::TYPE_PLACEMENT_LETTER)
                            ->orderByDesc('id')
                            ->get();

                        $selfFundingDocuments = \App\Models\StudyLeaveDocument::where('study_leave_id', $draft_study_leave->id)
                            ->where('document_type', \App\Models\StudyLeaveDocument::TYPE_SELF_FUNDING_DECLARATION)
                            ->orderByDesc('id')
                            ->get();
                    }

                    $hasPlacementDocs = $placementDocuments->isNotEmpty() || !empty($draft_study_leave->placement_letter);
                    $hasSelfFundingDocs = $selfFundingDocuments->isNotEmpty() || !empty($draft_study_leave->self_funding_declaration);
                @endphp

                 <!-- Attachment Instructions -->
                <div class="col-md-12">
                    @if(!($readonly ?? true))
                        <div class="alert alert-info mt-3">
                            <strong>Note:</strong> The placement offering letter and scholarship details should be attached to this application document as a PDF.
                        </div>
                    @endif
                    <label class="form-label fw-semibold mt-2">Attach PDF Documents <span class="text-danger">*</span></label>
                    <input type="file" name="placement_letter[]" id="attachments-input" class="form-control @if(!($readonly ?? true)) @error('placement_letter') is-invalid @enderror @endif" 
                           accept="application/pdf" 
                           multiple 
                           {{ $hasPlacementDocs ? '' : 'required' }}
                           {{ $readonly ?? true ? 'disabled' : '' }}>
                          <small class="text-muted d-block mt-1">You can select files multiple times to add more documents before submitting.</small>
                    @if(!($readonly ?? true))
                        @if(!$hasPlacementDocs)
                            @error('placement_letter')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('placement_letter.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="attachments_error" >
                                Please upload a PDF document (placement letter/scholarship details).
                            </div>
                        @endif
                    @endif

                    @if($placementDocuments->isNotEmpty())
                        <div class="mt-2">
                            <strong>All uploaded files:</strong>
                            <ul class="mb-0 mt-2">
                                @foreach($placementDocuments as $document)
                                    <li id="document-item-{{ $document->id }}" class="d-flex align-items-center gap-2">
                                        <a href="{{ route('StudyLeave.serveFile', ['type' => 'placement_letter', 'filename' => basename($document->document_path)]) }}" target="_blank" class="me-2">
                                            {{ basename($document->document_path) }}
                                        </a>
                                        @if(!($readonly ?? true))
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger remove-document-btn"
                                                data-document-id="{{ $document->id }}"
                                                data-document-type="{{ \App\Models\StudyLeaveDocument::TYPE_PLACEMENT_LETTER }}"
                                            >
                                                Remove
                                            </button>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

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
                    const removeButtons = document.querySelectorAll('.remove-document-btn');

                    removeButtons.forEach(function (button) {
                        button.addEventListener('click', function () {
                            const documentId = this.dataset.documentId;
                            const documentType = this.dataset.documentType;
                            const studyLeaveId = {{ $draft_study_leave->id ?? 'null' }};

                            if (!documentId || !documentType || !studyLeaveId) {
                                alert('Unable to remove this file right now.');
                                return;
                            }

                            if (!confirm('Are you sure you want to remove this file?')) {
                                return;
                            }

                            fetch('{{ route("StudyLeave.deleteFile") }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    type: documentType,
                                    study_leave_id: studyLeaveId,
                                    document_id: documentId
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    const item = document.getElementById('document-item-' + documentId);
                                    if (item) {
                                        item.remove();
                                    }
                                } else {
                                    alert(data.message || 'Failed to remove file.');
                                }
                            })
                            .catch(() => {
                                alert('Failed to remove file. Please try again.');
                            });
                        });
                    });
                });
                </script>
              
                <!-- Library and Property Handling -->
                <div class="col-md-6">
                    <label for="library_and_property_handling" class="form-label fw-semibold d-block study-leave-aligned-label">
                        Handling of Library book, Computer or any other properties <span class="text-danger">*</span>
                    </label>
                    @php
                        $currentLibraryAndPropertyHandling = old('library_and_property_handling', $draft_study_leave->library_and_property_handling ?? '');
                    @endphp
                    <select class="form-select study-leave-select @if(!($readonly ?? true)) @error('library_and_property_handling') is-invalid @enderror @endif" id="library_and_property_handling" name="library_and_property_handling" @if(!($readonly ?? true)) required @endif {{ $readonly ?? true ? 'disabled' : '' }}>
                        <option value="" {{ $currentLibraryAndPropertyHandling === '' || $currentLibraryAndPropertyHandling === null ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="1" {{ (string)$currentLibraryAndPropertyHandling === '1' || $currentLibraryAndPropertyHandling === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="0" {{ (string)$currentLibraryAndPropertyHandling === '0' || $currentLibraryAndPropertyHandling === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                    @if(!($readonly ?? true))
                    @error('library_and_property_handling')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <div class="invalid-feedback" id="library_and_property_handling_error" style="display: none;">
                        Please select an option for library and property handling.
                    </div>
                    @endif
                </div>
                <script>
                // Client-side validation for Library and Property Handling field
                document.addEventListener('DOMContentLoaded', function () {
                    const libraryHandlingSelect = document.getElementById('library_and_property_handling');
                    const libraryHandlingError = document.getElementById('library_and_property_handling_error');

                    if (!libraryHandlingSelect || !libraryHandlingError) {
                        return;
                    }

                    libraryHandlingSelect.addEventListener('change', function () {
                        if (this.value === '') {
                            libraryHandlingSelect.classList.add('is-invalid');
                            libraryHandlingError.style.display = 'block';
                        } else {
                            libraryHandlingSelect.classList.remove('is-invalid');
                            libraryHandlingError.style.display = 'none';
                        }
                    });
                });
                </script>

                <!-- Loan Handling (conditional) - shown only for "without Pay" -->
                <div class="col-md-6" id="loan_handling_section">
                    <label for="loan_handling_details" class="form-label fw-semibold d-block study-leave-aligned-label">
                        Paying of Loans taken from University of UPF? <span class="text-danger">*</span>
                    </label>
                    @php
                        $currentLoanHandling = old('loan_handling', $draft_study_leave->loan_handling ?? '');
                    @endphp
                    <select class="form-select study-leave-select" id="loan_handling_details" name="loan_handling" {{ $readonly ?? true ? 'disabled' : '' }}>
                        <option value="" {{ $currentLoanHandling === '' || $currentLoanHandling === null ? 'selected' : '' }} disabled>Select an option</option>
                        <option value="1" {{ (string)$currentLoanHandling === '1' || $currentLoanHandling === 'Make Arrangements' ? 'selected' : '' }}>Make Arrangements</option>
                        <option value="0" {{ (string)$currentLoanHandling === '0' || $currentLoanHandling === 'Not Make Arrangements' ? 'selected' : '' }}>Not Make Arrangements</option>
                    </select>
                    @if(!($readonly ?? true))
                    <div class="invalid-feedback" id="loan_handling_error" style="display: none;">
                        Please select an option for loan handling.
                    </div>
                    @endif
                </div>
                <script>
                // Client-side validation for Loan Handling field
                document.addEventListener('DOMContentLoaded', function () {
                    const loanHandlingSelect = document.getElementById('loan_handling_details');
                    const loanHandlingError = document.getElementById('loan_handling_error');
                    loanHandlingSelect.addEventListener('change', function () {
                        if (this.value === '') {
                            loanHandlingSelect.classList.add('is-invalid');
                            loanHandlingError.style.display = 'block';
                        } else {
                            loanHandlingSelect.classList.remove('is-invalid');
                            loanHandlingError.style.display = 'none';
                        }
                    });
                });
                </script>

                <!-- Funding Type -->
                <div class="col-md-6">
                        <label class="form-label fw-semibold d-block study-leave-aligned-label">Funding type <span class="text-danger">*</span></label>
                        <select name="funding_type" id="funding_type" class="form-select study-leave-select @if(!($readonly ?? true)) @error('funding_type') is-invalid @enderror @endif" @if(!($readonly ?? true)) required @endif {{ $readonly ?? true ? 'disabled' : '' }} >
                            <option value="" {{ ($draft_study_leave->funding_type ?? old('funding_type')) ? '' : 'selected' }} disabled>Select funding type</option>
                            <option value="1" {{ ($draft_study_leave->funding_type ?? old('funding_type')) == '1' ? 'selected' : '' }}>Self-Funding</option>
                            <option value="2" {{ ($draft_study_leave->funding_type ?? old('funding_type')) == '2' ? 'selected' : '' }}>Scholarship</option>
                        </select>
                        @if(!($readonly ?? true))
                        @error('funding_type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="funding_type_error" style="display: none;">
                            Please select a funding type.
                        </div>
                        @endif
                    </div>
                    <script>
                    // Client-side validation for Funding Type field
                    document.addEventListener('DOMContentLoaded', function () {
                        const fundingTypeSelect = document.getElementById('funding_type');
                        const fundingTypeError = document.getElementById('funding_type_error');
                        fundingTypeSelect.addEventListener('change', function () {
                            if (this.value === '') {
                                fundingTypeSelect.classList.add('is-invalid');
                                fundingTypeError.style.display = 'block';
                            } else {
                                fundingTypeSelect.classList.remove('is-invalid');
                                fundingTypeError.style.display = 'none';
                            }
                        });
                    });
                    </script>

                    <!-- Scholarship Details (conditional)- If Scholarship is selected in Funding Type -->
                    <div class="col-md-6" id="scholarship-details" style="display: none;">
                        <label class="form-label fw-semibold d-block study-leave-aligned-label">Scholarship Source <span class="text-danger">*</span></label>
                        <select name="scholarship_source" id="scholarship_source" class="form-select study-leave-select @if(!($readonly ?? true)) @error('scholarship_source') is-invalid @enderror @endif" {{ $readonly ?? true ? 'disabled' : '' }} >
                            <option value="" {{ empty($draft_study_leave->scholarship_source ?? old('scholarship_source')) ? 'selected' : '' }} disabled>Select source</option>
                            <option value="1" {{ ($draft_study_leave->scholarship_source ?? old('scholarship_source')) == '1' ? 'selected' : '' }}>Scholarship offering agency</option>
                            <option value="2" {{ ($draft_study_leave->scholarship_source ?? old('scholarship_source')) == '2' ? 'selected' : '' }}>Funds from a project</option>
                        </select>
                        @if(!($readonly ?? true))
                        @error('scholarship_source')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <div class="invalid-feedback" id="scholarship_source_error" style="display: none;">
                            Please select a scholarship source.
                        </div>
                        @endif
                    </div>
                    <script>
                    // Client-side validation for Scholarship Source field
                    document.addEventListener('DOMContentLoaded', function () {
                        const scholarshipSourceSelect = document.getElementById('scholarship_source');
                        const scholarshipSourceError = document.getElementById('scholarship_source_error');
                        scholarshipSourceSelect.addEventListener('change', function () {
                            if (this.value === '') {
                                scholarshipSourceSelect.classList.add('is-invalid');
                                scholarshipSourceError.style.display = 'block';
                            } else {
                                scholarshipSourceSelect.classList.remove('is-invalid');
                                scholarshipSourceError.style.display = 'none';
                            }
                        });
                    });
                    </script>

                    <!-- Additional fields based on Scholarship Source -->
                    <div class="col-md-6" id="scholarship-extra-details" style="display: none;">

                        <!-- Scholarship Amount (conditional) - If Scholarship offering agency is selected -->
                        <div id="scholarship-amount-group" style="display: none;">
                            <label class="form-label fw-semibold">Scholarship Amount <span class="text-danger">*</span></label>
                            <input type="number" name="scholarship_amount" id="scholarship_amount" class="form-control @if(!($readonly ?? true)) @error('scholarship_amount') is-invalid @enderror @endif" 
                                   @if(!($readonly ?? true)) min="0.01" step="0.01" @endif
                                   placeholder="Enter amount" 
                                   value="{{ $draft_study_leave->scholarship_amount ?? old('scholarship_amount') }}" 
                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                            @if(!($readonly ?? true))
                            @error('scholarship_amount')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="invalid-feedback" id="scholarship_amount_error" style="display: none;">
                                Please enter a valid scholarship amount (must be greater than 0).
                            </div>
                            @endif
                        </div>
                        <script>
                        // Client-side validation for Scholarship Amount field
                        document.addEventListener('DOMContentLoaded', function () {
                            const scholarshipAmountInput = document.getElementById('scholarship_amount');
                            const scholarshipAmountError = document.getElementById('scholarship_amount_error');
                            scholarshipAmountInput.addEventListener('input', function () {
                                if (this.value === '' || parseFloat(this.value) <= 0) {
                                    scholarshipAmountInput.classList.add('is-invalid');
                                    scholarshipAmountError.style.display = 'block';
                                } else {
                                    scholarshipAmountInput.classList.remove('is-invalid');
                                    scholarshipAmountError.style.display = 'none';
                                }
                            });
                        });
                        </script>   

                        <!-- Project Name (conditional) - If Funds from a project is selected -->
                        <div id="project-name-group" style="display: none;">
                            <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="project_name" id="project_name" class="form-control" 
                                   placeholder="Enter project name" 
                                   minlength="3" 
                                   maxlength="200" 
                                   value="{{ $draft_study_leave->project_name ?? '' }}" 
                                   {{ $readonly ?? true ? 'readonly' : '' }}>
                            @if(!($readonly ?? true))
                            <div class="invalid-feedback" id="project_name_error" style="display: none;">
                                Please enter the project name (3-200 characters).
                            </div>
                            @endif
                        </div>
                    </div>
                    <script>
                    // Client-side validation for Project Name field
                    document.addEventListener('DOMContentLoaded', function () {
                        const projectNameInput = document.getElementById('project_name');
                        const projectNameError = document.getElementById('project_name_error');
                        projectNameInput.addEventListener('input', function () {
                            if (this.value.length < 3 || this.value.length > 200) {
                                projectNameInput.classList.add('is-invalid');
                                projectNameError.style.display = 'block';
                            } else {
                                projectNameInput.classList.remove('is-invalid');
                                projectNameError.style.display = 'none';
                            }
                        });
                    });
                    </script>   

                    <!-- Additional fields (conditional) - If Self-Funding is selected -->
                    <div class="col-12">
                        <div class="row g-3">
                            <!-- Air Passage Request -->
                            <div class="col-md-6" id="self-funding-extra" style="display: none;">
                                <label class="form-label fw-semibold d-block">Requesting Air Passage from this University? <span class="text-danger">*</span></label>
                                <div class="d-inline-block">
                                    <div class="form-check">
                                        <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_yes" value="1"
                                            @checked(old('air_passage_request', $draft_study_leave->air_passage_request ?? '') == '1') {{ $readonly ?? true ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="air_passage_yes">YES</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input custom-radio" type="radio" name="air_passage_request" id="air_passage_no" value="0" @checked(old('air_passage_request', $draft_study_leave->air_passage_request ?? '') == '0' && old('air_passage_request', $draft_study_leave->air_passage_request ?? '') !== '') {{ $readonly ?? true ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="air_passage_no">NO</label>
                                    </div>
                                </div>
                                <div class="invalid-feedback" id="air_passage_error" style="display: none;">
                                    Please select an option for air passage request.
                                </div>
                            </div>

                            <!-- Warm Cloth Allowance Request -->
                            <div class="col-md-6" id="self-funding-extra2" style="display: none;">
                                <label class="form-label fw-semibold d-block">Requesting Warm Cloth Allowance from this University? <span class="text-danger">*</span></label>
                                <div class="d-inline-block">
                                    <div class="form-check">
                                        <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_yes" value="1"
                                            @checked(old('warm_cloth_allowance_request', $draft_study_leave->warm_cloth_allowance_request ?? '') == '1')  {{ $readonly ?? true ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="warm_cloth_yes">YES</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input custom-radio" type="radio" name="warm_cloth_allowance_request" id="warm_cloth_no" value="0"
                                            @checked(old('warm_cloth_allowance_request', $draft_study_leave->warm_cloth_allowance_request ?? '') == '0' && old('warm_cloth_allowance_request', $draft_study_leave->warm_cloth_allowance_request ?? '') !== '') {{ $readonly ?? true ? 'disabled' : '' }}>
                                        <label class="form-check-label" for="warm_cloth_no">NO</label>
                                    </div>
                                </div>
                                <div class="invalid-feedback" id="warm_cloth_error" style="display: none;">
                                    Please select an option for warm cloth allowance request.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Any Other Details -->
                    <div class="col-md-12">
                    <label class="form-label fw-semibold">Any Other Details of Funding</label>
                    <textarea name="any_other_details" class="form-control" rows="4"  {{ $readonly ?? true ? 'readonly' : '' }}>{{ $draft_study_leave->any_other_details ?? '' }} </textarea>
                </div>


                                                
                                                <!-- Self-Funding Declaration (conditional) -->
                                                <div class="col-md-12" id="self-funding-declaration" style="display: none;">
                                                    @if(!($readonly ?? true))
                                                       
                                                    <div class="alert alert-warning mt-3">
                                                        <strong>Note:</strong> If you are not receiving any scholarship, airfare or warm cloth allowance from any University, Institute, agency or project, please attach a separate document certifying that you will not be receiving any funds mentioned above from the placement offering University, Institute or any other agency.
                                                    </div>
                                                    @endif

                                                    <label class="form-label fw-semibold">Self-Funding Declaration <span class="text-danger" id="self-declaration-required">*</span></label>
                                                    <input type="file" name="self_funding_declaration[]" id="self-funding-declaration-input" class="form-control" accept="application/pdf" multiple {{ $hasSelfFundingDocs ? '' : 'required' }} {{ $readonly ?? true ? 'disabled' : '' }}>
                                                    <small class="text-muted d-block mt-1">You can select files multiple times to add more documents before submitting.</small>
                                                    @if(!$hasSelfFundingDocs)
                                                        <div class="invalid-feedback">
                                                            Please upload a self-funding declaration PDF document.
                                                        </div>
                                                    @endif
                                                    @error('self_funding_declaration')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                    @error('self_funding_declaration.*')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror

                                                    <div id="self-funding-selected-files" class="mt-2"></div>

                                                    @if($selfFundingDocuments->isNotEmpty())
                                                        <div class="mt-2">
                                                            <strong>All uploaded files:</strong>
                                                            <ul class="mb-0 mt-2">
                                                                @foreach($selfFundingDocuments as $document)
                                                                    <li id="document-item-{{ $document->id }}" class="d-flex align-items-center gap-2">
                                                                        <a href="{{ route('StudyLeave.serveFile', ['type' => 'self_funding_declaration', 'filename' => basename($document->document_path)]) }}" target="_blank" class="me-2">
                                                                            {{ basename($document->document_path) }}
                                                                        </a>
                                                                        @if(!($readonly ?? true))
                                                                            <button
                                                                                type="button"
                                                                                class="btn btn-sm btn-outline-danger remove-document-btn"
                                                                                data-document-id="{{ $document->id }}"
                                                                                data-document-type="{{ \App\Models\StudyLeaveDocument::TYPE_SELF_FUNDING_DECLARATION }}"
                                                                            >
                                                                                Remove
                                                                            </button>
                                                                        @endif
                                                                    </li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                    @endif

                </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const fundingType = document.querySelector('select[name="funding_type"]');
                        const selfFundingDeclaration = document.getElementById('self-funding-declaration');
                        const selfFundingInput = document.getElementById('self-funding-declaration-input');
                        const selfDeclarationRequired = document.getElementById('self-declaration-required');
                        
                        function updateSelfFundingDeclarationVisibility() {
                            if (fundingType.value === '1') {
                                selfFundingDeclaration.style.display = 'block';
                                // Only make required if no existing files are stored
                                @if(!$hasSelfFundingDocs)
                                    selfFundingInput.setAttribute('required', 'required');
                                @endif
                                if (selfDeclarationRequired) selfDeclarationRequired.style.display = 'inline';
                            } else {
                                selfFundingDeclaration.style.display = 'none';
                                selfFundingInput.removeAttribute('required');
                                if (typeof selfFundingInput._clearPendingFiles === 'function') {
                                    selfFundingInput._clearPendingFiles();
                                } else {
                                    selfFundingInput.value = '';
                                }
                                if (selfDeclarationRequired) selfDeclarationRequired.style.display = 'none';
                            }
                        }
                        
                        // Show on page load if funding type is already '1' (Self-Funding)
                        updateSelfFundingDeclarationVisibility();
                        
                        // Update when user changes funding type
                        fundingType.addEventListener('change', updateSelfFundingDeclarationVisibility);
                    });
                </script>

                <!-- Attachment Instructions -->
              
                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const input = document.getElementById('attachments-input');
                        const list = document.getElementById('pdf-preview-list');
                        const embedWrap = document.getElementById('pdf-preview-embed');
                        const embed = document.getElementById('pdf-embed');
                        let currentUrl = null;

                        if (!input || !list || !embedWrap || !embed) {
                            return;
                        }

                        const fileKey = (file) => `${file.name}::${file.size}::${file.lastModified}`;
                        let selectedFiles = [];

                        function syncInputFiles(files) {
                            try {
                                const dt = new DataTransfer();
                                files.forEach(file => dt.items.add(file));
                                input.files = dt.files;
                            } catch (error) {
                                // Keep native behavior if DataTransfer assignment is not supported.
                            }
                        }

                        function renderSelectedPlacementFiles() {
                            list.innerHTML = '';
                            embed.src = '';
                            embedWrap.style.display = 'none';

                            if (selectedFiles.length === 0) {
                                return;
                            }

                            selectedFiles.forEach((file, idx) => {
                                const item = document.createElement('div');
                                item.className = 'd-flex justify-content-between align-items-center py-1 border-bottom gap-2';

                                const name = document.createElement('div');
                                name.textContent = file.name;
                                name.className = 'flex-grow-1';
                                name.style.cursor = 'pointer';
                                name.title = 'Click Preview';

                                const actionWrap = document.createElement('div');
                                actionWrap.className = 'd-flex gap-1';

                                const previewBtn = document.createElement('button');
                                previewBtn.type = 'button';
                                previewBtn.className = 'btn btn-sm btn-outline-secondary';
                                previewBtn.textContent = 'Preview';
                                previewBtn.addEventListener('click', () => {
                                    if (currentUrl) URL.revokeObjectURL(currentUrl);
                                    currentUrl = URL.createObjectURL(file);
                                    embed.src = currentUrl;
                                    embedWrap.style.display = 'block';
                                });

                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.className = 'btn btn-sm btn-outline-danger';
                                removeBtn.textContent = 'Remove';
                                removeBtn.addEventListener('click', () => {
                                    selectedFiles.splice(idx, 1);
                                    syncInputFiles(selectedFiles);
                                    renderSelectedPlacementFiles();
                                });

                                name.addEventListener('click', () => previewBtn.click());

                                actionWrap.appendChild(previewBtn);
                                actionWrap.appendChild(removeBtn);
                                item.appendChild(name);
                                item.appendChild(actionWrap);
                                list.appendChild(item);

                                if (idx === 0) {
                                    previewBtn.click();
                                }
                            });
                        }

                        input.addEventListener('change', function () {
                            const newFiles = Array.from(this.files || []);
                            if (newFiles.length === 0) {
                                return;
                            }

                            const filesMap = new Map(selectedFiles.map(file => [fileKey(file), file]));
                            newFiles.forEach(file => filesMap.set(fileKey(file), file));
                            selectedFiles = Array.from(filesMap.values());

                            syncInputFiles(selectedFiles);
                            renderSelectedPlacementFiles();
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

                <style>
                    .study-leave-aligned-label {
                        min-height: 3rem;
                        line-height: 1.25;
                        display: flex;
                        align-items: flex-end;
                    }

                    .study-leave-select {
                        min-height: calc(1.5em + 0.75rem + 2px);
                        width: 100%;
                    }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const input = document.getElementById('self-funding-declaration-input');
                    const selectedFilesWrap = document.getElementById('self-funding-selected-files');

                    if (!input || !selectedFilesWrap) {
                        return;
                    }

                    const fileKey = (file) => `${file.name}::${file.size}::${file.lastModified}`;
                    let selectedFiles = [];

                    function syncInputFiles(files) {
                        try {
                            const dt = new DataTransfer();
                            files.forEach(file => dt.items.add(file));
                            input.files = dt.files;
                        } catch (error) {
                            // Keep native behavior if DataTransfer assignment is not supported.
                        }
                    }

                    function renderSelectedSelfFundingFiles() {
                        selectedFilesWrap.innerHTML = '';

                        if (selectedFiles.length === 0) {
                            return;
                        }

                        const title = document.createElement('strong');
                        title.textContent = 'New files selected (not yet submitted):';
                        selectedFilesWrap.appendChild(title);

                        const list = document.createElement('ul');
                        list.className = 'mb-0 mt-2';

                        selectedFiles.forEach((file, idx) => {
                            const item = document.createElement('li');
                            item.className = 'd-flex align-items-center gap-2';

                            const name = document.createElement('span');
                            name.className = 'flex-grow-1';
                            name.textContent = file.name;

                            const removeBtn = document.createElement('button');
                            removeBtn.type = 'button';
                            removeBtn.className = 'btn btn-sm btn-outline-danger';
                            removeBtn.textContent = 'Remove';
                            removeBtn.addEventListener('click', () => {
                                selectedFiles.splice(idx, 1);
                                syncInputFiles(selectedFiles);
                                renderSelectedSelfFundingFiles();
                            });

                            item.appendChild(name);
                            item.appendChild(removeBtn);
                            list.appendChild(item);
                        });

                        selectedFilesWrap.appendChild(list);
                    }

                    input._clearPendingFiles = function () {
                        selectedFiles = [];
                        syncInputFiles(selectedFiles);
                        renderSelectedSelfFundingFiles();
                    };

                    input.addEventListener('change', function () {
                        const newFiles = Array.from(this.files || []);
                        if (newFiles.length === 0) {
                            return;
                        }

                        const filesMap = new Map(selectedFiles.map(file => [fileKey(file), file]));
                        newFiles.forEach(file => filesMap.set(fileKey(file), file));
                        selectedFiles = Array.from(filesMap.values());

                        syncInputFiles(selectedFiles);
                        renderSelectedSelfFundingFiles();
                    });
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
                        const studyLocationError = document.getElementById('study_location_errors');
                        
                        console.log('Study location elements:', {
                            radios: studyLocationRadios.length,
                            countryField: countryField,
                            countryInput: countryInput
                        });
                        
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
                                    if (studyLocationError) {
                                        
                                        studyLocationError.style.display = 'none';
                                    }
                                });
                            });
                        }
                        
                        // Initialize country field on page load
                        console.log('Initializing country field on page load');
                        updateCountryField();
                        
                        function updateConditionalValidation() {
                            if (fundingType.value === '1') {
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
                                
                            } else if (fundingType.value === '2') {
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
                                if (this.value === '1') {
                                    scholarshipAmountGroup.style.display = 'block';
                                    projectNameGroup.style.display = 'none';
                                    scholarshipAmount.setAttribute('required', 'required');
                                    projectName.removeAttribute('required');
                                    scholarshipExtraDetails.style.display = 'block';
                                } else if (this.value === '2') {
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
                        
                        // Make validation functions global so they can be called from saveAndExit()
                        window.validateDateRange = function() {
                            if (fromDate && toDate && fromDate.value && toDate.value) {
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
                                window.validateDateRange();
                            });
                            toDate.addEventListener('change', window.validateDateRange);
                        }
                        
                        // Custom radio button validation - Make global
                        window.validateRadioGroups = function() {
                            let isValid = true;
                            const airPassageError = document.getElementById('air_passage_error');
                            const warmClothError = document.getElementById('warm_cloth_error');
                            const studyLocationError = document.getElementById('study_location_errors');
                            const fundingType = document.querySelector('select[name="funding_type"]');
                            
                            // Check study location
                            const studyLocationChecked = document.querySelector('input[name="study_location"]:checked');
                            if (studyLocationError) {
                                if (!studyLocationChecked) {
                                    studyLocationError.style.display = 'block';
                                    isValid = false;
                                } else {
                                    studyLocationError.style.display = 'none';
                                }
                            }
                            
                            // Check air passage if required
                            if (fundingType && fundingType.value === '1') {
                                const airPassageChecked = document.querySelector('input[name="air_passage_request"]:checked');
                                const warmClothChecked = document.querySelector('input[name="warm_cloth_allowance_request"]:checked');
                                
                                if (airPassageError) {
                                    if (!airPassageChecked) {
                                        airPassageError.style.display = 'block';
                                        isValid = false;
                                    } else {
                                        airPassageError.style.display = 'none';
                                    }
                                }
                                
                                if (warmClothError) {
                                    if (!warmClothChecked) {
                                        warmClothError.style.display = 'block';
                                        isValid = false;
                                    } else {
                                        warmClothError.style.display = 'none';
                                    }
                                }
                            }
                            
                            return isValid;
                        }
                        
                        // Bootstrap form validation
                        const forms = document.querySelectorAll('.needs-validation');
                        Array.from(forms).forEach(form => {
                            form.addEventListener('submit', event => {
                                window.validateDateRange();
                                const radioValid = window.validateRadioGroups();
                                
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
                       
            </div>
        </div>