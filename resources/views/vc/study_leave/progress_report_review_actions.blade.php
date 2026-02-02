<!-- Review Actions Section for VC -->
<div class="card">
    <div class="card-header bg-dark text-white fw-semibold">
        <i class="fas fa-tasks me-2"></i>Review Actions
    </div>
    <div class="card-body">
        <!-- VC Approval Radio Buttons -->
        <div class="mb-4">
            <label class="form-label fw-semibold">VC Approval Decision <span class="text-danger">*</span></label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approval_decision" id="approvalYes" value="approved" required>
                    <label class="form-check-label" for="approvalYes">
                        <i class="fas fa-check-circle text-success me-1"></i> Yes - Approve (Final)
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approval_decision" id="approvalNo" value="not_approved" required>
                    <label class="form-check-label" for="approvalNo">
                        <i class="fas fa-times-circle text-danger me-1"></i> No - Return to Dean
                    </label>
                </div>
            </div>
            <div id="approvalError" class="form-text text-danger" style="display: none;">
                Please select an approval decision.
            </div>
        </div>

        <div class="mb-3">
            <label for="actionRemark" class="form-label fw-semibold">VC Remarks</label>
            <textarea class="form-control" id="actionRemark" name="remark" rows="4"
                placeholder="Add your comments or remarks about this progress report"></textarea>
            <div id="remarkError" class="form-text text-danger" style="display: none;">
                Remarks are required when returning a progress report.
            </div>
        </div>

        <div class="alert alert-info">
            <strong><i class="fas fa-info-circle me-2"></i>Note:</strong>
            <div class="mt-2">
                Approving this progress report will mark it as <strong>Final Approved</strong>.
            </div>
        </div>

        <div class="d-flex flex-column align-items-end">
            <form id="submitForm"
                action="{{ route('vc.progressreport.submit', $progressReport->progress_report_id) }}"
                method="POST" class="d-inline">
                @csrf
                <input type="hidden" id="approvalDecisionInput" name="approval_decision" value="">
                <input type="hidden" id="remarkInput" name="remark" value="">
                <button type="button" onclick="submitVCForm()" class="btn btn-primary btn-lg">
                    <i class="fas fa-paper-plane me-2"></i>Submit Review
                </button>
            </form>
            
        </div>
    </div>
</div>

<script>
    // Form validation and submission handling for VC
    function submitVCForm() {
        const approvalDecision = document.querySelector('input[name="approval_decision"]:checked');
        const remarkValue = document.getElementById('actionRemark').value.trim();

        clearErrors();

        // Check if approval decision is selected
        if (!approvalDecision) {
            showApprovalError();
            return false;
        }

        // If returning to Dean (not approved), remarks are required
        if (approvalDecision.value === 'not_approved' && remarkValue === '') {
            showRemarkError();
            return false;
        }

        // Set form values
        document.getElementById('approvalDecisionInput').value = approvalDecision.value;
        document.getElementById('remarkInput').value = remarkValue;

        // Confirm submission
        const action = approvalDecision.value === 'approved' ? 'give final approval to' : 'return to Dean';
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
