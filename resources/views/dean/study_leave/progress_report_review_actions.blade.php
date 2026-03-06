<!-- Review Actions Section for Dean -->
<div class="card">
    <div class="card-header bg-dark text-white fw-semibold">
        <i class="fas fa-tasks me-2"></i>Review Actions
    </div>
    <div class="card-body">
        <!-- Dean Approval Radio Buttons -->
        <div class="mb-4">
            <label class="form-label fw-semibold">Dean Approval Decision <span class="text-danger">*</span></label>
            <div class="d-flex gap-4">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approval_decision" id="approvalYes" value="approved" required>
                    <label class="form-check-label" for="approvalYes">
                        <i class="fas fa-check-circle text-success me-1"></i> Yes - Approve
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="approval_decision" id="approvalNo" value="not_approved" required>
                    <label class="form-check-label" for="approvalNo">
                        <i class="fas fa-times-circle text-danger me-1"></i> No - Return to HOD
                    </label>
                </div>
            </div>
            <div id="approvalError" class="form-text text-danger" style="display: none;">
                Please select an approval decision.
            </div>
        </div>

        <div class="mb-3">
            <label for="actionRemark" class="form-label fw-semibold">Dean Remarks</label>
            <textarea class="form-control" id="actionRemark" name="remark" rows="4"
                placeholder="Add your comments or remarks about this progress report"></textarea>
            <div id="remarkError" class="form-text text-danger" style="display: none;">
                Remarks are required when returning a progress report.
            </div>
        </div>

        <div class="d-flex flex-column align-items-end">
            @if (isset($vcInfo))
                <div class="card mb-2 w-100">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center justify-content-end">
                            <strong>Forward to,&nbsp;</strong>
                            <div>
                                <div class="fw-semibold">{{ $vcInfo->vc_title ?? 'Vice Chancellor' }} {{ $vcInfo->vc_name ?? '' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card mb-2 w-100">
                    <div class="card-body py-2">
                        <div class="d-flex align-items-center justify-content-end">
                            <strong>Forward to,&nbsp;</strong>
                            <div>
                                <div class="fw-semibold">Vice Chancellor</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            <form id="submitForm"
                action="{{ route('dean.progressreport.submit', $progressReport->progress_report_id) }}"
                method="POST" class="d-inline w-100">
                @csrf
                <input type="hidden" id="approvalDecisionInput" name="approval_decision" value="">
                <input type="hidden" id="remarkInput" name="remark" value="">
                <button type="button" onclick="submitDeanForm()" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-paper-plane me-2"></i>Submit Review
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Submit Modal -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-question-circle me-2"></i>Confirm Submission</h5>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-paper-plane fa-3x mb-3 text-primary"></i>
                <p class="mb-0 fs-6" id="confirmSubmitMessage">Are you sure you want to submit this review?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmSubmitYes">
                    <i class="fas fa-check me-2"></i>Yes, Submit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Form validation and submission handling for Dean
    function submitDeanForm() {
        const approvalDecision = document.querySelector('input[name="approval_decision"]:checked');
        const remarkValue = document.getElementById('actionRemark').value.trim();

        clearErrors();

        // Check if approval decision is selected
        if (!approvalDecision) {
            showApprovalError();
            return false;
        }

        // If returning to HOD (not approved), remarks are required
        if (approvalDecision.value === 'not_approved' && remarkValue === '') {
            showRemarkError();
            return false;
        }

        // Set form values
        document.getElementById('approvalDecisionInput').value = approvalDecision.value;
        document.getElementById('remarkInput').value = remarkValue;

        // Show confirm modal
        const action = approvalDecision.value === 'approved' ? 'approve and forward to VC' : 'return to HOD';
        document.getElementById('confirmSubmitMessage').textContent = `Are you sure you want to ${action} this progress report?`;
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmSubmitModal'));
        confirmModal.show();
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

    document.getElementById('confirmSubmitYes').addEventListener('click', function () {
        document.getElementById('submitForm').submit();
    });
</script>
