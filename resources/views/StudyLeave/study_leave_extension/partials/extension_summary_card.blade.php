@php
    $summaryHeaderClass = $headerClass ?? 'bg-primary text-white fw-semibold';
    $summaryCardMarginClass = $cardMarginClass ?? 'mb-4';
    $summaryTitle = $title ?? 'Extension Request Details';
    $summaryDurationDays = $durationDays ?? \Carbon\Carbon::parse($extension->old_end_date)->diffInDays(\Carbon\Carbon::parse($extension->new_end_date));
    $summaryDurationMonths = $durationMonths ?? max(1, (int) ceil($summaryDurationDays / 30));
    $showPaymentHelpText = $showPaymentHelpText ?? false;
@endphp

<div class="card {{ $summaryCardMarginClass }} extension-summary-card">
    <div class="card-header {{ $summaryHeaderClass }}">
        <i class="fas fa-calendar-plus me-2"></i> {{ $summaryTitle }}
    </div>
    <div class="card-body extension-summary-body">
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="extension-identity-block">
                    <span class="extension-label">Study Leave Reference</span>
                    <div class="extension-primary-value">{{ $extension->reference_no }}</div>
                    <div class="extension-meta-text mt-1">Extension request linked to the original approved application</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="extension-identity-block">
                    <span class="extension-label">Applicant</span>
                    <div class="extension-secondary-value">{{ $extension->name_with_initials }}</div>
                    <div class="extension-meta-text mt-1">
                        <i class="fas fa-id-badge me-1"></i>{{ $extension->empno }}
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="extension-date-card is-old">
                    <span class="extension-label">Original End Date</span>
                    <div class="fw-semibold text-danger fs-5">
                        <i class="far fa-calendar-alt me-1"></i>
                        {{ \Carbon\Carbon::parse($extension->old_end_date)->format('d M Y') }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-date-card is-new">
                    <span class="extension-label">Requested New End Date</span>
                    <div class="fw-semibold text-success fs-5">
                        <i class="far fa-calendar-check me-1"></i>
                        {{ \Carbon\Carbon::parse($extension->new_end_date)->format('d M Y') }}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="extension-metric-card">
                    <span class="extension-label text-white-50">Extension Duration</span>
                    <div class="metric-value">
                        <i class="fas fa-clock me-1"></i>{{ $summaryDurationDays }} days
                    </div>
                    <div class="mt-1">Approx. {{ $summaryDurationMonths }} month{{ $summaryDurationMonths == 1 ? '' : 's' }}</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="extension-payment-panel">
                    <span class="extension-label">Extension Payment Type</span>
                    <div>
                        @if((string) $extension->extension_payment_type === '1')
                            <span class="badge bg-success fs-6">With Pay</span>
                        @elseif((string) $extension->extension_payment_type === '0')
                            <span class="badge bg-danger fs-6">Without Pay</span>
                        @elseif((string) $extension->extension_payment_type === '2')
                            <span class="badge bg-warning text-dark fs-6">Pending</span>
                        @else
                            <span class="badge bg-secondary fs-6">Not specified</span>
                        @endif
                    </div>
                    @if($showPaymentHelpText)
                        <div class="extension-meta-text mt-2">Review this before forwarding the application to the next stage.</div>
                    @endif
                </div>
            </div>
            <div class="col-lg-8">
                <div class="extension-reason-panel">
                    <span class="extension-label">Reason for Extension</span>
                    <p class="extension-reason-text">{{ $extension->reason_for_extension }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
