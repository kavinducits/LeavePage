<!-- Personal Details (readonly) -->
        <div class="card mb-4">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-user me-2"></i>Arrangements made to cover applicants’ work during the period of leave
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="row g-3">
                    
                    <!-- Work Covering Persons Inputs -->
                    <div class="col-12">

                        <!-- Nominee Person For Teaching -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominate Person For Teaching </label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Employee No</label>
                                        <input type="text" class="form-control @error('nominee_teaching_empno') is-invalid @enderror" id="nominee_teaching_empno" name="nominee_teaching_empno" value="{{ old('nominee_teaching_empno', $draft_study_leave->nominee_teaching_empno ?? '') }}" placeholder="Employee Number" required  {{ $readonly ?? true ? 'readonly' : '' }}>
                                        @error('nominee_teaching_empno')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Employee Name</label>
                                        <input type="text" class="form-control @error('nominee_teaching_name') is-invalid @enderror" id="nominee_teaching_name" name="nominee_teaching_name" value="{{ old('nominee_teaching_name', $draft_study_leave->nominee_teaching_name ?? '') }}" placeholder="Employee Name" readonly required >
                                        @error('nominee_teaching_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Destination</label>
                                        <input type="text" class="form-control @error('nominee_teaching_destination') is-invalid @enderror" id="nominee_teaching_destination" name="nominee_teaching_destination" value="{{ old('nominee_teaching_destination', $draft_study_leave->nominee_teaching_destination ?? '') }}" placeholder="Destination" readonly required>
                                        @error('nominee_teaching_destination')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Nominee Person For Administrative Work -->

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominate Person For Administrative Work </label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Employee No</label>
                                        <input type="text" class="form-control @error('nominee_admin_empno') is-invalid @enderror" id="nominee_admin_empno" name="nominee_admin_empno" value="{{ old('nominee_admin_empno', $draft_study_leave->nominee_admin_empno ?? '') }}" placeholder="Employee Number" required  {{ $readonly ?? true ? 'readonly' : '' }}>
                                        @error('nominee_admin_empno')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Employee Name</label>
                                        <input type="text" class="form-control @error('nominee_admin_name') is-invalid @enderror" id="nominee_admin_name" name="nominee_admin_name" value="{{ old('nominee_admin_name', $draft_study_leave->nominee_admin_name ?? '') }}" placeholder="Employee Name" required readonly>
                                        @error('nominee_admin_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Destination</label>
                                        <input type="text" class="form-control @error('nominee_admin_destination') is-invalid @enderror" id="nominee_admin_destination" name="nominee_admin_destination" value="{{ old('nominee_admin_destination', $draft_study_leave->nominee_admin_destination ?? '') }}" placeholder="Destination" readonly>
                                        @error('nominee_admin_destination')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                    
                                </div>
                            </div>

                            <!--  Nominee Person For Other Work -->

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nominate Person For Other Work</label>
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Employee No</label>
                                        <input type="text" class="form-control @error('nominee_other_empno') is-invalid @enderror" id="nominee_other_empno" name="nominee_other_empno" value="{{ old('nominee_other_empno', $draft_study_leave->nominee_other_empno ?? '') }}" placeholder="Employee Number" required  {{ $readonly ?? true ? 'readonly' : '' }}>
                                        @error('nominee_other_empno')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Employee Name</label>
                                        <input type="text" class="form-control @error('nominee_other_name') is-invalid @enderror" id="nominee_other_name" name="nominee_other_name" value="{{ old('nominee_other_name', $draft_study_leave->nominee_other_name ?? '') }}" placeholder="Employee Name" required readonly>
                                        @error('nominee_other_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">Destination</label>
                                        <input type="text" class="form-control @error('nominee_other_destination') is-invalid @enderror" id="nominee_other_destination" name="nominee_other_destination" value="{{ old('nominee_other_destination', $draft_study_leave->nominee_other_destination ?? '') }}" placeholder="Destination" readonly>
                                        @error('nominee_other_destination')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                               
                                </div>
                               
                            </div>
                          
                        
                    
                    </div>

                     
            </div>

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

    function lookupEmployee(empInputSelector, nameOutputSelector,destinationOutputSelector) {
        var empno = $(empInputSelector).val() ? $(empInputSelector).val().trim() : '';
        if (!empno) {
            $(nameOutputSelector).val('');
            $(destinationOutputSelector).val('');
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
                } else {
                    $(nameOutputSelector).val('Not found');
                    $(destinationOutputSelector).val('Not found');
                    console.warn('Lookup returned no name for', empno, response);
                }
            },
            error: function (xhr, status, error) {
                console.error('Employee lookup error for', empno, status, error, xhr.responseText);
                $(nameOutputSelector).val('Lookup failed');
                $(destinationOutputSelector).val('Lookup failed');
            }
        });
    }

    $('#nominee_teaching_empno').on('change', function () {
        lookupEmployee('#nominee_teaching_empno', '#nominee_teaching_name','#nominee_teaching_destination');
    });

    $('#nominee_admin_empno').on('change', function () {
        lookupEmployee('#nominee_admin_empno', '#nominee_admin_name','#nominee_admin_destination');
    });
    $('#nominee_other_empno').on('change', function () {
        lookupEmployee('#nominee_other_empno', '#nominee_other_name','#nominee_other_destination');
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