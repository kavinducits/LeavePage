<!-- Review Actions Section for HOD -->
<div class="card">
    <div class="card-header bg-dark text-white fw-semibold">
        <i class="fas fa-tasks me-2"></i>Review Actions
    </div>
    <div class="card-body">
        <!-- HOD Recommendation Radio Buttons -->
        <div class="mb-4">
            <label class="form-label fw-semibold">HOD Recommendation <span class="text-danger">*</span></label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approval_decision" id="approvalYes" value="approved" required>
                    <label class="form-check-label" for="approvalYes">
                        <i class="fas fa-check-circle text-success me-1"></i> Recommend
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approval_decision" id="approvalNo" value="not_approved" required>
                    <label class="form-check-label" for="approvalNo">
                        <i class="fas fa-times-circle text-danger me-1"></i> Not Recommend
                    </label>
                </div>
            </div>
            <div id="approvalError" class="form-text text-danger" style="display: none;">
                Please select a recommendation.
            </div>
        </div>

        <div class="mb-3">
            <label for="actionRemark" class="form-label fw-semibold">HOD Remarks</label>
            <textarea class="form-control" id="actionRemark" name="remark" rows="4"
                placeholder="Add your comments or remarks about this progress report"></textarea>
            <div id="remarkError" class="form-text text-danger" style="display: none;">
                Remarks are required when not recommending a progress report.
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-start">
            <div class="text-right">
                @if (isset($deanInfo))
                    <div class="card mb-2">
                        <div class="card-body py-2">
                            <div class="d-flex align-items-center justify-content-end">
                                <strong>Forward to,&nbsp;</strong>
                                <div>
                                    <div class="fw-semibold">
                                        {{ $deanInfo->dean_title ?? 'Dean' }}&nbsp;{{ $deanInfo->dean_name ?? '' }}
                                    </div>
                                    <div class="text-muted small">{{ $deanInfo->dean_position ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card mb-2 border-warning">
                        <div class="card-body py-2">
                            <div class="text-danger text-end">
                                <strong>No active Faculty Dean</strong>
                                <div class="text-muted small">Forwarding is disabled until a dean is active.</div>
                            </div>
                        </div>
                    </div>
                @endif
                <form id="submitForm"
                    action="{{ route('hod.progressreport.submit', $progressReport->progress_report_id) }}"
                    method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" id="approvalDecisionInput" name="approval_decision" value="">
                    <input type="hidden" id="remarkInput" name="remark" value="">
                    <button type="button" onclick="submitHODForm()" class="btn btn-success btn-lg w-100" {{ empty($deanInfo) ? 'disabled' : '' }}>
                        <i class="fas fa-forward me-2"></i>Forward to Dean
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation and submission handling for HOD
    function submitHODForm() {
        const approvalDecision = document.querySelector('input[name="approval_decision"]:checked');
        const remarkValue = document.getElementById('actionRemark').value.trim();

        clearErrors();

        // Check if recommendation is selected
        if (!approvalDecision) {
            showApprovalError();
            return false;
        }

        // If not recommending, remarks are required
        if (approvalDecision.value === 'not_approved' && remarkValue === '') {
            showRemarkError();
            return false;
        }

        // Set form values
        document.getElementById('approvalDecisionInput').value = approvalDecision.value;
        document.getElementById('remarkInput').value = remarkValue;

        // Confirm submission
        const action = approvalDecision.value === 'approved' ? 'recommend and forward to Dean' : 'not recommend';
        if (confirm(`Are you sure you want to ${action} this progress report?`)) {
            document.getElementById('submitForm').submit();
        }
    }

    function showApprovalError() {
        document.getElementById('approvalError').style.display = 'block';
        document.querySelectorAll('input[name="approval_decision"]').forEach(radio => {
            radio.classList.add('is-invalid');
        });
    }

    function showRemarkError() {
        document.getElementById('remarkError').style.display = 'block';
        document.getElementById('actionRemark').classList.add('is-invalid');
    }

    function clearErrors() {
        document.getElementById('approvalError').style.display = 'none';
        document.getElementById('remarkError').style.display = 'none';
        document.getElementById('actionRemark').classList.remove('is-invalid');
        document.querySelectorAll('input[name="approval_decision"]').forEach(radio => {
            radio.classList.remove('is-invalid');
        });
    }

    // Clear errors on input changes
    document.getElementById('actionRemark').addEventListener('input', clearErrors);
    document.querySelectorAll('input[name="approval_decision"]').forEach(radio => {
        radio.addEventListener('change', clearErrors);
    });
</script>
