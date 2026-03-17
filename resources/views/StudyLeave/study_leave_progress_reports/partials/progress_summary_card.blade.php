@php
    $progressItem = $progressItem ?? $progressReport;
    $summaryHeaderClass = $headerClass ?? 'bg-primary text-white fw-semibold';
    $summaryCardMarginClass = $cardMarginClass ?? 'mb-4';
    $summaryTitle = $title ?? 'Progress Report Details';
    $showRemarks = $showRemarks ?? true;
    $showDocument = $showDocument ?? true;
    $remarksLabel = $remarksLabel ?? 'Employee Remarks';
    $documentRouteName = $documentRouteName ?? 'ma.serveProgressReport';
    $applicantName = $applicantName ?? ($progressItem->name_with_initials ?? null);
    $applicantEmpno = $applicantEmpno ?? ($progressItem->empno ?? null);
    $statusText = $statusText ?? ($progressItem->status ?? 'N/A');

    $submittedDate = !empty($progressItem->submitted_date) ? \Carbon\Carbon::parse($progressItem->submitted_date) : null;
@endphp

<div class="card {{ $summaryCardMarginClass }} progress-summary-card">
    <div class="card-header {{ $summaryHeaderClass }}">
        <i class="fas fa-file-alt me-2"></i> {{ $summaryTitle }}
    </div>
    <div class="card-body progress-summary-body">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="progress-identity-block">
                    <span class="progress-label">Study Leave Reference</span>
                    <div class="progress-primary-value">{{ $progressItem->reference_no }}</div>
                    <div class="progress-meta-text mt-1">Progress report submitted under the approved study leave</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="progress-identity-block">
                    @if(!empty($applicantName) || !empty($applicantEmpno))
                        <span class="progress-label">Applicant</span>
                        <div class="progress-secondary-value">{{ $applicantName }}</div>
                        <div class="progress-meta-text mt-1">
                            <i class="fas fa-id-badge me-1"></i>{{ $applicantEmpno }}
                        </div>
                    @else
                        <span class="progress-label">Current Status</span>
                        <div class="progress-secondary-value">{{ $statusText }}</div>
                        <div class="progress-meta-text mt-1">Applicant details are not available in this view.</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="progress-date-card is-submitted">
                    <span class="progress-label">Submitted Date</span>
                    <div class="fw-semibold text-success fs-5">
                        <i class="far fa-calendar-check me-1"></i>
                        {{ $submittedDate ? $submittedDate->format('d M Y') : 'Not Submitted' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @if ($showRemarks && !empty($progressItem->remark))
                <div class="col-lg-8">
                    <div class="progress-remarks-panel">
                        <span class="progress-label">{{ $remarksLabel }}</span>
                        <p class="progress-remarks-text">{{ $progressItem->remark }}</p>
                    </div>
                </div>
            @endif

            @if ($showDocument && !empty($progressItem->document_path))
                <div class="{{ ($showRemarks && !empty($progressItem->remark)) ? 'col-lg-4' : 'col-12' }}">
                    <div class="progress-doc-panel">
                        <span class="progress-label">Progress Report Document</span>
                        <div class="d-flex align-items-center justify-content-between gap-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                <div>
                                    <div class="fw-bold">Progress Report PDF</div>
                                    <div class="text-muted small">Open the submitted document</div>
                                </div>
                            </div>
                            <a href="{{ route($documentRouteName, ['filename' => basename($progressItem->document_path)]) }}"
                                target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-eye me-1"></i>View PDF
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
