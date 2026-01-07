@extends('layouts.screen1')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-calendar-plus me-2 text-primary"></i>Extension Request Review
                </h2>
                <p class="text-muted mb-0">Reference No: {{ $extension->reference_no }}</p>
            </div>
            <a href="{{ route('hod.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Extension Summary Card -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header card-header-dark text-white fw-semibold">
                <i class="fas fa-calendar-plus me-2"></i> Extension Request Details
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Study Leave Reference Number</label>
                            <div class="fw-bold fs-5 text-primary">{{ $extension->reference_no }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Employee</label>
                            <div class="fw-bold">{{ $extension->name_with_initials }}</div>
                            <div class="text-muted small">{{ $extension->empno }}</div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Original End Date</label>
                            <div class="fw-semibold text-danger">
                                <i class="far fa-calendar-alt me-1"></i>
                                {{ \Carbon\Carbon::parse($extension->old_end_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">New End Date</label>
                            <div class="fw-semibold text-success">
                                <i class="far fa-calendar-check me-1"></i>
                                {{ \Carbon\Carbon::parse($extension->new_end_date)->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="text-muted small mb-1">Extension Duration</label>
                            <div class="fw-bold text-primary fs-5">
                                <i class="fas fa-clock me-1"></i>{{ $durationDays }} days ({{ $durationMonths }} months)
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="mb-0">
                            <label class="text-muted small mb-1">Reason for Extension</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <p class="mb-0" style="white-space: pre-wrap;">{{ $extension->reason_for_extension }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MA Remarks (if any) -->
        @if (!empty($extension->ma_remarks))
            <div class="card mb-4">
                <div class="card-header bg-info text-white fw-semibold">
                    <i class="fas fa-comment-dots me-2"></i>MA Remarks
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <div style="white-space: pre-wrap;">{{ $extension->ma_remarks }}</div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Accordion for More Details -->
        <div class="accordion mb-4" id="detailsAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingDetails">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseDetails" aria-expanded="false" aria-controls="collapseDetails">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>More Details - Original Study Leave Application</strong>
                    </button>
                </h2>
                <div id="collapseDetails" class="accordion-collapse collapse" aria-labelledby="headingDetails"
                    data-bs-parent="#detailsAccordion">
                    <div class="accordion-body">
                        <!-- Include study leave forms with readonly -->
                        @php
                            $readonly = true;
                        @endphp

                        @include('StudyLeave.basic_info_form')
                        @include('StudyLeave.details_form')
                        @include('StudyLeave.working_covering_persons_form')
                    </div>
                </div>
            </div>
        </div>

        <!-- HOD Review Section -->
        <form action="{{ route('hod.extension.approve', $extension->extension_id) }}" method="POST" id="hodReviewForm">
            @csrf

            <div class="card mt-4">
                <div class="card-header card-header-dark text-white fw-semibold">
                    <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
                </div>
                <div class="card-body">

                    <!-- Question - Recommendation -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Extension is recommended
                            <span class="text-danger">*</span>
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hod_recommend" id="recommendYes"
                                value="yes" required>
                            <label class="form-check-label" for="recommendYes">
                                Yes
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="hod_recommend" id="recommendNo"
                                value="no" required>
                            <label class="form-check-label" for="recommendNo">
                                No
                            </label>
                        </div>
                    </div>

                    <!-- Conditional: If not recommended -->
                    <div class="mb-4" id="notRecommendReasonDiv" style="display: none;">
                        <label for="hod_not_recommend_reason" class="form-label fw-semibold">
                            If not recommended, please give reasons
                            <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="hod_not_recommend_reason" name="hod_not_recommend_reason" rows="4"
                            placeholder="Please provide detailed reasons for not recommending this extension"></textarea>
                        <div class="invalid-feedback">
                            Please provide reasons for not recommending.
                        </div>
                    </div>

                    <!-- Any other remarks -->
                    <div class="mb-4">
                        <label for="hod_remarks" class="form-label fw-semibold">
                            Any other remarks
                        </label>
                        <textarea class="form-control" id="hod_remarks" name="hod_remarks" rows="3"
                            placeholder="Add any additional comments or remarks (optional)"></textarea>
                    </div>

                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <!-- Return to User Button -->
                        <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal"
                            data-bs-target="#returnModal">
                            <i class="fas fa-undo me-2"></i>Return to User
                        </button>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn"
                                {{ empty($deanInfo) ? 'disabled' : '' }}>
                                <i class="fas fa-paper-plane me-2"></i>Submit Review & Forward to Dean
                            </button>

                            @if (isset($deanInfo))
                                <div class="card mt-2" style="min-width: 280px;">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center">
                                            <strong>Forward to,&nbsp;</strong>
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $deanInfo->title ?? 'Dean' }}&nbsp;{{ $deanInfo->initials ?? '' }}&nbsp;{{ $deanInfo->last_name ?? '' }}
                                                </div>
                                                <div class="text-muted small">{{ $deanInfo->faculty_name ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card mt-2 border-warning" style="min-width: 280px;">
                                    <div class="card-body py-2">
                                        <div class="text-danger">
                                            <strong>No active Dean found</strong>
                                            <div class="text-muted small">Forwarding is disabled until a Dean is active.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Return Modal -->
    <div class="modal fade" id="returnModal" tabindex="-1" aria-labelledby="returnModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('hod.extension.return', $extension->extension_id) }}" method="POST"
                    id="returnForm">
                    @csrf
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="returnModalLabel">
                            <i class="fas fa-undo me-2"></i>Return Extension Request to User
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            This extension request will be returned to the employee for revision.
                        </div>
                        <div class="mb-3">
                            <label for="returnRemarks" class="form-label fw-semibold">
                                Remarks <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="returnRemarks" name="hod_remarks" rows="4"
                                placeholder="Please provide reasons for returning this extension request" required></textarea>
                            <div class="invalid-feedback">
                                Remarks are required when returning an extension request.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-undo me-2"></i>Return to User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .card-header-dark {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            border-bottom: 3px solid #0d6efd;
        }

        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .accordion-button:not(.collapsed) {
            background-color: #e7f1ff;
            color: #0d6efd;
        }

        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        #notRecommendReasonDiv.show {
            display: block !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide not recommend reason field
            const recommendRadios = document.querySelectorAll('input[name="hod_recommend"]');
            const notRecommendDiv = document.getElementById('notRecommendReasonDiv');
            const notRecommendTextarea = document.getElementById('hod_not_recommend_reason');

            recommendRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.value === 'no') {
                        notRecommendDiv.style.display = 'block';
                        notRecommendDiv.classList.add('show');
                        notRecommendTextarea.required = true;
                    } else {
                        notRecommendDiv.style.display = 'none';
                        notRecommendDiv.classList.remove('show');
                        notRecommendTextarea.required = false;
                        notRecommendTextarea.value = '';
                    }
                });
            });

            // Form validation
            const hodReviewForm = document.getElementById('hodReviewForm');
            hodReviewForm.addEventListener('submit', function(e) {
                const recommendValue = document.querySelector('input[name="hod_recommend"]:checked')?.value;

                if (recommendValue === 'no') {
                    const reason = notRecommendTextarea.value.trim();
                    if (reason === '') {
                        e.preventDefault();
                        notRecommendTextarea.classList.add('is-invalid');
                        notRecommendTextarea.focus();
                        return false;
                    }
                }

                if (!confirm('Are you sure you want to submit this review and forward to Dean?')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Return form validation
            const returnForm = document.getElementById('returnForm');
            returnForm.addEventListener('submit', function(e) {
                const returnRemarks = document.getElementById('returnRemarks').value.trim();

                if (returnRemarks === '') {
                    e.preventDefault();
                    document.getElementById('returnRemarks').classList.add('is-invalid');
                    return false;
                }

                if (!confirm('Are you sure you want to return this extension request to the user?')) {
                    e.preventDefault();
                    return false;
                }
            });

            // Clear invalid state on input
            notRecommendTextarea.addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });

            document.getElementById('returnRemarks').addEventListener('input', function() {
                this.classList.remove('is-invalid');
            });
        });
    </script>
@endsection
