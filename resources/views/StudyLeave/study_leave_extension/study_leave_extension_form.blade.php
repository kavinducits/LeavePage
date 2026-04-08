@extends('layouts.app')

@section('content')

<div class="container-fluid px-4 py-3">

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Error:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="dashboard-header text-maroon mb-1">
                <i class="fas fa-calendar-plus icon-gold me-2"></i>Request for Study Leave Extension
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('StudyLeave.create') }}">Study Leaves</a></li>
                    <li class="breadcrumb-item active">Extension Requests</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('StudyLeave.create') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Study Leaves
        </a>
    </div>

    @if(!$canExtend && $hasPendingExtension)
        <div class="alert alert-warning fw-semibold">
            <i class="fas fa-hourglass-half me-2"></i>
            <strong>Extension Request In Process:</strong> You already have an extension request pending approval. You cannot submit a new request until the current one is approved or rejected.
        </div>
    @elseif(!$canExtend)
        <div class="alert alert-danger fw-semibold">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Extension Not Allowed:</strong> The total study leave duration has reached or exceeded the 3-year limit.
            <br><small>Total duration: {{ round($totalDurationDays / 365, 2) }} years ({{ $totalDurationDays }} days)</small>
        </div>
    @elseif($remainingDays < 365)
        <div class="alert alert-warning fw-semibold">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Notice:</strong> You have {{ round($remainingDays / 30, 1) }} months ({{ $remainingDays }} days) remaining before reaching the 3-year limit.
        </div>
    @endif

    <!-- Extensions Table Card -->
    <div class="card shadow-sm">
        <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
            <span>
                <i class="fas fa-list me-2"></i>Extension Requests — Ref: {{ $study_leave->reference_no ?? 'N/A' }}
            </span>
            @if($canExtend)
                <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#newExtensionModal">
                    <i class="fas fa-plus me-1"></i>New Extension Request
                </button>
            @endif
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 6%">#</th>
                            <th style="width: 24%">Extension From</th>
                            <th style="width: 24%">Extension To</th>
                            <th style="width: 12%">Payment Type</th>
                            <th style="width: 14%">Status</th>
                            <th style="width: 20%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($extensions as $index => $extension)
                            @php
                                $oldEnd = \Carbon\Carbon::parse($extension->old_end_date);
                                $newEnd = \Carbon\Carbon::parse($extension->new_end_date);
                                $sid    = $extension->approval_status_id ?? $extension->status_id;
                                $icon       = 'fa-clock';
                                if ($sid == 1)                        { $badgeClass = 'bg-success';           $icon = 'fa-check-circle'; }
                                elseif ($sid == 2)                    { $badgeClass = 'bg-danger';            $icon = 'fa-times-circle'; }
                                elseif ($sid == 3)                    { $badgeClass = 'bg-warning text-dark'; $icon = 'fa-undo'; }
                                elseif ($sid == 4)                    { $badgeClass = 'bg-info';              $icon = 'fa-spinner'; }
                                elseif (in_array($sid, [5,6,7,9]))   { $badgeClass = 'bg-primary';           $icon = 'fa-hourglass-half'; }
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    {{ $oldEnd->format('d M Y') }}
                                </td>
                                <td>
                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                    {{ $newEnd->format('d M Y') }}
                                </td>
                                <td>
                                    @if((string) $extension->extension_payment_type === '1')
                                        <span class="badge bg-success">With Pay</span>
                                    @elseif((string) $extension->extension_payment_type === '0')
                                        <span class="badge bg-danger">Without Pay</span>
                                    @elseif((string) $extension->extension_payment_type === '2')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-secondary">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $badgeClass }}">
                                        <i class="fas {{ $icon }} me-1"></i>{{ $extension->status_name ?? 'Submitted' }}
                                    </span>
                                </td>
                                <td>
                                    @if($extension->reason_for_extension)
                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reasonModal{{ $extension->id }}">
                                            <i class="fas fa-eye me-1"></i>View Reason
                                        </button>
                                    @endif
                                    @if($sid == 3)
                                        <button type="button"
                                                class="btn btn-sm btn-warning"
                                                data-bs-toggle="modal"
                                                data-bs-target="#resubmitModal{{ $extension->id }}">
                                            <i class="fas fa-redo me-1"></i>Resubmit
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                                    <br>
                                    <p class="text-muted mb-0">No extension requests submitted yet.</p>
                                    @if($canExtend)
                                        <small class="text-muted">Click <strong>New Extension Request</strong> above to submit your first request.</small>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- ───── Reason View Modals ───── -->
@foreach($extensions as $extension)
    @if($extension->reason_for_extension)
        <div class="modal fade" id="reasonModal{{ $extension->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-maroon text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-file-alt me-2"></i>Reason for Extension #{{ $loop->iteration }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="p-3 bg-light border rounded" style="white-space: pre-wrap;">{{ $extension->reason_for_extension }}</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Resubmit modal for returned extensions --}}
    @php $sid = $extension->approval_status_id ?? $extension->status_id; @endphp
    @if($sid == 3)
        <div class="modal fade" id="resubmitModal{{ $extension->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <form action="{{ route('StudyLeave.update.extension', $extension->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="old_end_date" value="{{ $extension->old_end_date }}">
                        <div class="modal-header bg-maroon text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-redo me-2"></i>Resubmit Extension Request #{{ $loop->iteration }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">New End Date <span class="text-danger">*</span></label>
                                <input type="date" name="new_end_date" class="form-control"
                                       value="{{ $extension->new_end_date }}"
                                       min="{{ \Carbon\Carbon::parse($extension->old_end_date)->addDay()->format('Y-m-d') }}"
                                       required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Reason for Extension <span class="text-danger">*</span></label>
                                <textarea name="reason_for_extension" class="form-control" rows="4" required>{{ $extension->reason_for_extension }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Cancel
                            </button>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-redo me-2"></i>Resubmit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- ───── New Extension Request Modal ───── -->
@if($canExtend)
<div class="modal fade" id="newExtensionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="extensionForm" action="{{ route('StudyLeave.store.extension', ['id' => $study_leave->id]) }}" method="POST">
                @csrf
                <input type="hidden" name="old_end_date" value="{{ $extensionStartDate }}">
                <div class="modal-header bg-maroon text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-calendar-plus me-2"></i>New Extension Request
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Extension from:</strong> {{ \Carbon\Carbon::parse($extensionStartDate)->format('d M Y') }}
                    </div>

                    <!-- New End Date -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            New End Date <span class="text-danger">*</span>
                        </label>
                        <input type="date"
                               name="new_end_date"
                               id="ext_new_end_date"
                               class="form-control"
                               min="{{ \Carbon\Carbon::parse($extensionStartDate)->addDay()->format('Y-m-d') }}"
                               required>
                        <small class="text-muted">Must be after {{ \Carbon\Carbon::parse($extensionStartDate)->format('d M Y') }}</small>
                    </div>

                    <!-- Reason -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Reason for Extension <span class="text-danger">*</span>
                        </label>
                        <textarea name="reason_for_extension"
                                  id="ext_reason"
                                  class="form-control"
                                  rows="4"
                                  maxlength="2000"
                                  placeholder="Enter reason for requesting extension"
                                  required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="button" id="submitExtensionBtn" class="btn btn-primary" style="background-color:#800020;border-color:#800020;">
                        <i class="fas fa-paper-plane me-2"></i>Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endif

<script>
document.addEventListener('DOMContentLoaded', function () {
    @if($canExtend)
    const submitBtn     = document.getElementById('submitExtensionBtn');
    const newExtModal   = new bootstrap.Modal(document.getElementById('newExtensionModal'));

    submitBtn.addEventListener('click', function () {
        const form = document.getElementById('extensionForm');
        if (!form.checkValidity()) { form.reportValidity(); return; }
        AppPopup.confirm({
            title: 'Confirm Submission',
            text: 'Are you sure you want to submit this extension request? It will be forwarded to MA for review.',
            icon: 'question',
            confirmButtonText: 'Yes, Submit'
        }).then(function(result) {
            if (result && result.isConfirmed) {
                newExtModal.hide();
                form.submit();
            }
        });
    });
    @endif

    @if(session('upload_success'))
        AppPopup.success(@json(session('upload_success')), 'Submitted Successfully', 1600);
    @endif

    @if(session('error'))
        AppPopup.error(@json(session('error')));
    @endif
});
</script>

<style>
    .bg-maroon          { background-color: #800020 !important; }
    .card-header-maroon { background-color: #800020; color: #ffffff; }
    .text-maroon        { color: #800020 !important; }
    .icon-gold          { color: #FFD700; }
    .dashboard-header   { font-size: 1.75rem; }
    .btn-close-white    { filter: brightness(0) invert(1); }
    .table-hover tbody tr:hover { background-color: rgba(128,0,32,0.05); }
</style>

@endsection
