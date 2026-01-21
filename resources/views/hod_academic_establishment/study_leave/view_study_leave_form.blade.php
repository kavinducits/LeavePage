@extends('layouts.screen1')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Study Leave Application Review
                </h2>
                <p class="text-muted mb-0">Reference No: {{ $draft_study_leave->reference_no }}</p>
            </div>
            <a href="{{ route('hod.show.studyleaves') }}" class="btn btn-outline-secondary">
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

        <!-- Study Leave Details (readonly) -->
        @include('StudyLeave.basic_info_form', ['readonly' => true])
        @include('StudyLeave.details_form', ['readonly' => true])
        @include('StudyLeave.working_covering_persons_form', ['readonly' => true])

        <!-- HOD Review Section -->
        <form action="{{ route('hodacademicestablishment.studyLeave.approve', $draft_study_leave->id) }}" method="POST" id="hodReviewForm">
            @csrf

            @include('hod_academic_establishment.study_leave.study_leave_hod_academic_establishment_review_section', ['readonly' => false])



            <!-- Action Buttons -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('hod.show.studyleaves') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>

                        <div class="text-end">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Submit Review & Forward to HOD Academic Establishment
                            </button>

                            @if (isset($departmentHead))
                                <div class="card mt-2" style="min-width: 280px;">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center">
                                            <strong>Forward to,&nbsp;</strong>
                                            <div>
                                                <div class="fw-semibold">
                                                    {{ $departmentHead->head_title ?? 'Head' }}&nbsp;{{ $departmentHead->head_name ?? '' }}
                                                </div>
                                                <div class="text-muted small">{{ $departmentHead->faculty_name ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="card mt-2 border-warning" style="min-width: 280px;">
                                    <div class="card-body py-2">
                                        <div class="text-danger">
                                            <strong>No active HOD found</strong>
                                            <div class="text-muted small">Please contact administrator.</div>
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

        .form-control[readonly],
        .form-select:disabled,
        select:disabled {
            background-color: #e9ecef !important;
            color: #495057 !important;
            opacity: 1 !important;
        }

        .btn-lg {
            padding: 0.5rem 1.5rem;
            font-size: 1rem;
        }
    </style>

    <script>
        // Form validation
        form.addEventListener('submit', function(e) {
            let isValid = true;

            // Check if "No" is selected and reason is empty
            if (recommendNo.checked) {
                const reasonValue = notRecommendReasonTextarea.value.trim();
                if (!reasonValue) {
                    e.preventDefault();
                    notRecommendReasonTextarea.classList.add('is-invalid');
                    notRecommendReasonTextarea.focus();
                    isValid = false;
                    alert('Please provide reasons for not recommending this leave.');
                    return;
                }
            }

            // Confirm submission
            if (isValid) {
                const confirmMessage = recommendYes.checked ?
                    'Are you sure you want to submit your review and forward this application to the Dean?' :
                    'Are you sure you want to submit your review with a NOT RECOMMENDED status?';

                if (!confirm(confirmMessage)) {
                    e.preventDefault();
                }
            }
        });

        // Clear invalid state when user starts typing
        notRecommendReasonTextarea.addEventListener('input', function() {
        if (this.value.trim()) {
            this.classList.remove('is-invalid');
        }
        });
        });
    </script>
@endsection
