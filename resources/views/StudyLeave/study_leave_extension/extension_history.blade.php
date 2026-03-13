<!-- Study Leave Extension History -->
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center" style="background-color: #000000; color: white;">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>Extension History
        </h5>
    </div>
    <div class="card-body">
        @if(isset($extensions) && $extensions->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 18%">Original End Date</th>
                            <th style="width: 18%">Extended End Date</th>
                            <th style="width: 12%">Payment Type</th>
                            <th style="width: 25%">Remark</th>
                            <th style="width: 12%">Status</th>
                            <th style="width: 10%" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($extensions as $index => $extension)
                            @php
                                $oldEndDate = \Carbon\Carbon::parse($extension->old_end_date);
                                $newEndDate = \Carbon\Carbon::parse($extension->new_end_date);
                                $extensionPeriod = $oldEndDate->diffInDays($newEndDate);
                                
                                // Status badge configuration
                                $statusBadge = '';
                                $statusText = $extension->status ?? 'Unknown';
                                
                                switch($extension->status_id) {
                                    case 1:
                                        $statusBadge = 'bg-success';
                                        break;
                                    case 2:
                                        $statusBadge = 'bg-danger';
                                        break;
                                    case 3:
                                        $statusBadge = 'bg-info text-dark';
                                        break;
                                    case 4:
                                    case 5:
                                    case 6:
                                    case 7:
                                    case 8:
                                    case 9:
                                    case 10:
                                        $statusBadge = 'bg-warning text-dark';
                                        break;
                                    default:
                                        $statusBadge = 'bg-secondary';
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    {{ $oldEndDate->format('d M Y') }}
                                </td>
                                <td>
                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                    {{ $newEndDate->format('d M Y') }}
                                </td>
                                <td>
                                    @if((string) $extension->extension_payment_type === '1')
                                        <span class="badge bg-success">With Pay</span>
                                    @elseif((string) $extension->extension_payment_type === '0')
                                        <span class="badge bg-danger">Without Pay</span>
                                    @else
                                        <span class="badge bg-secondary">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ \Illuminate\Support\Str::limit($extension->reason_for_extension, 100) }}</small>
                                    @if(strlen($extension->reason_for_extension) > 100)
                                        <button type="button" 
                                                class="btn btn-sm btn-link p-0" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#reasonModal{{ $extension->id }}">
                                            <i class="fas fa-eye"></i> View Full
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $statusBadge }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($extension->status_id == 3)
                                        <!-- Returned extension - Show Resubmit button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#resubmitExtensionModal{{ $extension->id }}"
                                                title="Resubmit Extension">
                                            <i class="fas fa-redo"></i> Resubmit
                                        </button>
                                    @else
                                        <!-- Regular View button -->
                                        <button type="button" 
                                                class="btn btn-sm btn-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#viewExtensionModal{{ $extension->id }}"
                                                title="View Extension Details">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    @endif
                                </td>
                            </tr>

                            <!-- View Extension Modal -->
                            <div class="modal fade" id="viewExtensionModal{{ $extension->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #800020; color: white;">
                                            <h5 class="modal-title">
                                                <i class="fas fa-calendar-plus me-2"></i>Extension Request Details - #{{ $index + 1 }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Status Badge -->
                                            <div class="alert alert-light border mb-3">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <strong>Request #:</strong> {{ $index + 1 }}
                                                    </div>
                                                    <div class="col-md-6 text-end">
                                                        <strong>Status:</strong> <span class="badge {{ $statusBadge }}">{{ $statusText }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Extension Period Details -->
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <strong><i class="fas fa-calendar me-2"></i>Extension Period</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label class="text-muted small">Original End Date:</label>
                                                            <div class="fw-semibold">{{ $oldEndDate->format('d M Y') }}</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="text-muted small">Extended End Date:</label>
                                                            <div class="fw-semibold text-success">{{ $newEndDate->format('d M Y') }}</div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="text-muted small">Extension Period:</label>
                                                            <div>
                                                                <span class="badge bg-primary">
                                                                    <i class="fas fa-clock me-1"></i>{{ $extensionPeriod }} days
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-3">
                                                        <div class="col-md-4">
                                                            <label class="text-muted small">Payment Type:</label>
                                                            <div>
                                                                @if((string) $extension->extension_payment_type === '1')
                                                                    <span class="badge bg-success">With Pay</span>
                                                                @elseif((string) $extension->extension_payment_type === '0')
                                                                    <span class="badge bg-danger">Without Pay</span>
                                                                @else
                                                                    <span class="badge bg-secondary">Not specified</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reason for Extension -->
                                            <div class="card mb-3">
                                                <div class="card-header bg-light">
                                                    <strong><i class="fas fa-file-alt me-2"></i>Reason for Extension</strong>
                                                </div>
                                                <div class="card-body">
                                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $extension->reason_for_extension }}</p>
                                                </div>
                                            </div>

                                            <!-- Leave Details if available -->
                                            @if(isset($extension->leave_type) || isset($extension->leave_payment_type))
                                                <div class="card mb-3">
                                                    <div class="card-header bg-light">
                                                        <strong><i class="fas fa-info-circle me-2"></i>Leave Details</strong>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            @if(isset($extension->leave_type))
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="text-muted small">Leave Type:</label>
                                                                    <div>{{ $extension->leave_type }}</div>
                                                                </div>
                                                            @endif
                                                            @if(isset($extension->leave_payment_type))
                                                                <div class="col-md-6 mb-2">
                                                                    <label class="text-muted small">Payment Type:</label>
                                                                    <div>{{ $extension->leave_payment_type }}</div>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            <!-- Timestamps -->
                                            <div class="card">
                                                <div class="card-header bg-light">
                                                    <strong><i class="fas fa-clock me-2"></i>Request Information</strong>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <label class="text-muted small">Submitted On:</label>
                                                            <div>{{ \Carbon\Carbon::parse($extension->created_at)->format('d M Y, h:i A') }}</div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="text-muted small">Last Updated:</label>
                                                            <div>{{ \Carbon\Carbon::parse($extension->updated_at)->format('d M Y, h:i A') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Reason Modal -->
                            @if(strlen($extension->reason_for_extension) > 100)
                                <div class="modal fade" id="reasonModal{{ $extension->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #800020; color: white;">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-file-alt me-2"></i>Extension Reason - Request #{{ $index + 1 }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-light border">
                                                    <strong>Original End Date:</strong> {{ $oldEndDate->format('d M Y') }}<br>
                                                    <strong>Extended End Date:</strong> {{ $newEndDate->format('d M Y') }}<br>
                                                    <strong>Extension Period:</strong> {{ $extensionPeriod }} days
                                                </div>
                                                <h6 class="fw-bold mb-3">Reason for Extension:</h6>
                                                <p class="text-justify" style="white-space: pre-wrap;">{{ $extension->reason_for_extension }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Resubmit Extension Modal -->
                            @if($extension->status_id == 3)
                                <div class="modal fade" id="resubmitExtensionModal{{ $extension->id }}" tabindex="-1" aria-labelledby="resubmitExtensionModalLabel{{ $extension->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #800020; color: white;">
                                                <h5 class="modal-title" id="resubmitExtensionModalLabel{{ $extension->id }}">
                                                    <i class="fas fa-redo me-2"></i>Resubmit Study Leave Extension - Request #{{ $index + 1 }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-info fw-semibold mb-3">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    <strong>Note:</strong> This extension request was returned for revision. Please update the details below and resubmit.
                                                </div>

                                                <form action="{{ route('StudyLeave.update.extension', ['id' => $extension->id]) }}" 
                                                      method="POST" 
                                                      enctype="multipart/form-data" 
                                                      id="resubmit-extension-form-{{ $extension->id }}" 
                                                      class="needs-validation" 
                                                      novalidate>
                                                    @csrf
                                                    @method('PUT')

                                                    <div class="card mb-3">
                                                        <div class="card-header" style="background-color: #800020; color: white;">
                                                            <i class="fas fa-user me-2"></i>Extension Details - Reference No: {{ $study_leave->reference_no ?? 'N/A' }}
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row g-3">
                                                                <!-- Period of Study Leave Requested -->
                                                                <div class="col-12">
                                                                    <label class="form-label fw-semibold">Period of Study Leave Extension <span class="text-danger">*</span></label>
                                                                    <div class="row g-3">
                                                                        <div class="col-md-6">
                                                                            <label class="form-label">From</label>
                                                                            <input type="date" 
                                                                                   name="old_end_date" 
                                                                                   class="form-control" 
                                                                                   value="{{ $extension->old_end_date }}" 
                                                                                   readonly 
                                                                                   required>
                                                                            <div class="invalid-feedback">
                                                                                Please select a valid start date.
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-6">
                                                                            <label class="form-label">To <span class="text-danger">*</span></label>
                                                                            <input type="date" 
                                                                                   name="new_end_date" 
                                                                                   class="form-control" 
                                                                                   value="{{ $extension->new_end_date }}"
                                                                                   min="{{ $extension->old_end_date }}" 
                                                                                   required>
                                                                            <div class="invalid-feedback">
                                                                                Please select a valid end date (must be after start date).
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <div class="col-md-6">
                                                                    <label class="form-label fw-semibold">
                                                                        Extension Payment Type <span class="text-danger">*</span>
                                                                    </label>
                                                                    <select name="extension_payment_type" class="form-select" required>
                                                                        <option value="" disabled>Select payment type</option>
                                                                        <option value="1" {{ (string) old('extension_payment_type', $extension->extension_payment_type) === '1' ? 'selected' : '' }}>With Pay</option>
                                                                        <option value="0" {{ (string) old('extension_payment_type', $extension->extension_payment_type) === '0' ? 'selected' : '' }}>Without Pay</option>
                                                                    </select>
                                                                    <div class="invalid-feedback">
                                                                        Please select extension payment type.
                                                                    </div>
                                                                </div>

                                                                <!-- Reason for Extension -->
                                                                <div class="col-12">
                                                                    <label class="form-label fw-semibold">
                                                                        Reason for Extension <span class="text-danger">*</span>
                                                                    </label>
                                                                    <textarea name="reason_for_extension" 
                                                                              class="form-control" 
                                                                              rows="4" 
                                                                              placeholder="Enter reason for requesting extension" 
                                                                              required>{{ $extension->reason_for_extension }}</textarea>
                                                                    <div class="invalid-feedback">
                                                                        Please provide a reason for the extension.
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="fas fa-times me-2"></i>Cancel
                                                        </button>
                                                        <button type="submit" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                                                            <i class="fas fa-paper-plane me-2"></i>Resubmit Extension Request
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No extension requests found for this study leave.</p>
                @if(isset($hasPendingExtension) && $hasPendingExtension)
                    <div class="alert alert-warning mt-3 d-inline-block">
                        <i class="fas fa-hourglass-half me-2"></i>You have a pending extension request
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Add Extension Modal -->
<div class="modal fade" id="addExtensionModal" tabindex="-1" aria-labelledby="addExtensionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #800020; color: white;">
                <h5 class="modal-title" id="addExtensionModalLabel">
                    <i class="fas fa-calendar-plus me-2"></i>Request for Study Leave Extension
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(isset($study_leave))
                    @if(isset($hasPendingExtension) && $hasPendingExtension)
                        <div class="alert alert-warning fw-semibold">
                            <i class="fas fa-hourglass-half me-2"></i>
                            <strong>Pending Extension Request:</strong> You already have an extension request pending approval. You cannot submit a new extension request until the current one is processed.
                        </div>
                    @endif

                    @if ((!isset($canExtend) || !$canExtend) && (!isset($hasPendingExtension) || !$hasPendingExtension))
                        <div class="alert alert-danger fw-semibold">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Extension Not Allowed:</strong> The total study leave duration (including approved extensions) has reached or exceeded the 3-year limit.
                            @if(isset($totalDurationDays))
                                <br><small>Total duration: {{ round($totalDurationDays / 365, 2) }} years ({{ $totalDurationDays }} days)</small>
                            @endif
                        </div>
                    @endif

                    @if (isset($canExtend) && $canExtend && isset($remainingDays) && $remainingDays < 365)
                        <div class="alert alert-warning fw-semibold">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Notice:</strong> You have {{ round($remainingDays / 30, 1) }} months ({{ $remainingDays }} days) remaining before reaching the 3-year limit.
                            <br><small>Current total: {{ round($totalDurationDays / 365, 2) }} years | Maximum: 3 years</small>
                        </div>
                    @endif

                    <form action="{{ route('StudyLeave.store.extension', ['id' => $study_leave->id]) }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          id="extension-form" 
                          class="needs-validation" 
                          novalidate>
                        @csrf

                        <div class="card mb-3">
                            <div class="card-header" style="background-color: #800020; color: white;">
                                <i class="fas fa-user me-2"></i>Details of the Study Leave Extension - Reference No: {{ $study_leave->reference_no ?? 'N/A' }}
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Period of Study Leave Requested -->
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Period of Study Leave Requested <span class="text-danger">*</span></label>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">From</label>
                                                <input type="date" 
                                                       name="old_end_date" 
                                                       id="modal_old_end_date" 
                                                       class="form-control" 
                                                       value="{{ $extensionStartDate ?? $study_leave->study_leave_to ?? '' }}" 
                                                       readonly 
                                                       required>
                                                <div class="invalid-feedback">
                                                    Please select a valid start date.
                                                </div>
                                                @if(isset($extensionStartDate) && $extensionStartDate != $study_leave->study_leave_to)
                                                    <small class="text-muted">
                                                        <i class="fas fa-info-circle"></i> Start date based on previous approved extension end date
                                                    </small>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">To <span class="text-danger">*</span></label>
                                                <input type="date" 
                                                       name="new_end_date" 
                                                       id="modal_new_end_date" 
                                                       class="form-control" 
                                                       value=""
                                                       min="{{ $extensionStartDate ?? $study_leave->study_leave_to ?? date('Y-m-d') }}" 
                                                       required 
                                                       {{ (!isset($canExtend) || !$canExtend || (isset($hasPendingExtension) && $hasPendingExtension)) ? 'readonly' : '' }}>
                                                <div class="invalid-feedback">
                                                    Please select a valid end date (must be after start date).
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Reason for Extension -->
                                    <div class="col-12">
                                        <label for="modal_reason_for_extension" class="form-label fw-semibold">
                                            Reason for Extension <span class="text-danger">*</span>
                                        </label>
                                        <textarea name="reason_for_extension" 
                                                  id="modal_reason_for_extension" 
                                                  class="form-control" 
                                                  rows="4" 
                                                  placeholder="Enter reason for requesting extension" 
                                                  required 
                                                  {{ (!isset($canExtend) || !$canExtend || (isset($hasPendingExtension) && $hasPendingExtension)) ? 'readonly' : '' }}>{{ old('reason_for_extension', '') }}</textarea>
                                        <div class="invalid-feedback">
                                            Please provide a reason for the extension.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            @if(isset($hasPendingExtension) && $hasPendingExtension)
                                <button type="button" class="btn btn-warning" disabled>
                                    <i class="fas fa-hourglass-half me-2"></i>Pending Request Exists
                                </button>
                            @elseif(isset($canExtend) && $canExtend)
                                <button type="submit" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Extension Request
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" disabled>
                                    <i class="fas fa-ban me-2"></i>Extension Not Allowed
                                </button>
                            @endif
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Study leave information not available.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modalOldEndDate = document.getElementById('modal_old_end_date');
        const modalNewEndDate = document.getElementById('modal_new_end_date');
        
        @if(isset($remainingDays) && isset($totalDurationDays) && isset($study_leave))
        const remainingDays = {{ $remainingDays ?? 0 }};
        const studyLeaveFrom = '{{ $study_leave->study_leave_from }}';
        const totalDurationDays = {{ $totalDurationDays ?? 0 }};
        
        function updateNewEndDateRestrictions() {
            if (modalOldEndDate && modalOldEndDate.value && studyLeaveFrom) {
                // Set minimum to the day after old_end_date
                const minDate = new Date(modalOldEndDate.value);
                minDate.setDate(minDate.getDate() + 1);
                const minDateString = minDate.toISOString().split('T')[0];
                modalNewEndDate.min = minDateString;
                
                // Calculate maximum date
                const originalStartDate = new Date(studyLeaveFrom);
                const maxDate = new Date(originalStartDate);
                maxDate.setDate(maxDate.getDate() + remainingDays + totalDurationDays);
                const maxDateString = maxDate.toISOString().split('T')[0];
                modalNewEndDate.max = maxDateString;
                
                // Clear if exceeds max
                if (modalNewEndDate.value && new Date(modalNewEndDate.value) > maxDate) {
                    modalNewEndDate.value = '';
                }
                
                // Clear if less than min
                if (modalNewEndDate.value && new Date(modalNewEndDate.value) < minDate) {
                    modalNewEndDate.value = '';
                }
            }
        }
        
        // Initialize on modal open
        document.getElementById('addExtensionModal').addEventListener('shown.bs.modal', function () {
            updateNewEndDateRestrictions();
        });
        
        if (modalOldEndDate) {
            modalOldEndDate.addEventListener('change', updateNewEndDateRestrictions);
        }
        @endif
        
        // Form validation
        const form = document.getElementById('extension-form');
        if (form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        }

        // Form validation for resubmit forms
        document.querySelectorAll('[id^="resubmit-extension-form-"]').forEach(function(resubmitForm) {
            resubmitForm.addEventListener('submit', function(event) {
                if (!resubmitForm.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                resubmitForm.classList.add('was-validated');
            }, false);
        });
    });
</script>

<style>
    .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(128, 0, 32, 0.05);
    }
    
    .modal-xl {
        max-width: 90%;
    }
    
    @media (min-width: 1200px) {
        .modal-xl {
            max-width: 1140px;
        }
    }
</style>
