@extends('layouts.dashborad')
@section('title', 'Head Of Academic Establishment Review - Leave Management')
@section('brand-logo')
    <a href="{{ route('hod.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">Head Of Academic Establishment Review</span>
    </a>
@endsection
@section('sidebar')
    @php($pageName = 'Study Leave')
    @include('hod_academic_establishment.partials.sidebar')
@endsection

@section('main-content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-file-alt me-2 text-primary"></i>Study Leave Application Review
                </h2>
                <p class="text-muted mb-0">Reference No: {{ $draft_study_leave->reference_no }}</p>
            </div>
            <a href="{{ isset($from) && $from == 'accepted' ? route('hodacademicestablishment.studyLeave.accepted') : route('hodacademicestablishment.studyLeave') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        @if (session('success'))
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif

        <!-- Study Leave Summary -->
        @include('ma.study_leave.study_leave_summary_table')

        <!-- Study Leave Details (readonly) -->
        @include('StudyLeave.basic_info_form', ['readonly' => true])
        @include('StudyLeave.details_form', ['readonly' => true])
        @include('StudyLeave.working_covering_persons_form', ['readonly' => true])

        @if(isset($from) && $from == 'accepted')
            @include('hod_academic_establishment.study_leave.study_leave_hod_academic_establishment_review_section', ['readonly' => true])
            <div class="alert alert-success mt-3">
                <i class="fas fa-check-circle me-2"></i>This application has already been forwarded.
            </div>
        @else
        <!-- HOD Review Section -->
        <form action="{{ route('hodacademicestablishment.studyLeave.approve', $draft_study_leave->id) }}" method="POST" id="hodReviewForm">
            @csrf

            @include('hod_academic_establishment.study_leave.study_leave_hod_academic_establishment_review_section', ['readonly' => false])



            <!-- Action Buttons -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ isset($from) && $from == 'accepted' ? route('hodacademicestablishment.studyLeave.accepted') : route('hodacademicestablishment.studyLeave') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>Cancel
                        </a>

                        <div class="text-end">
                            @if (isset($departmentHead))
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex align-items-center justify-content-end">
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
                                <div class="card mb-2 border-warning">
                                    <div class="card-body py-2">
                                        <div class="text-danger text-end">
                                            <strong>No active HOD found</strong>
                                            <div class="text-muted small">Please contact administrator.</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn">
                                <i class="fas fa-paper-plane me-2"></i>Submit Review & Forward to HOD
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        @endif
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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('hodReviewForm');
            const recommendYes = document.getElementById('recommendYes');
            const recommendNo = document.getElementById('recommendNo');
            const remarksTextarea = document.getElementById('registrar_remarks');
            const remarksError = document.getElementById('registrarRemarksError');

            if (!form) {
                return;
            }

            form.addEventListener('submit', function(e) {
                if (recommendNo && recommendNo.checked) {
                    const remarksValue = remarksTextarea ? remarksTextarea.value.trim() : '';
                    if (!remarksValue) {
                        e.preventDefault();
                        if (remarksTextarea) {
                            remarksTextarea.classList.add('is-invalid');
                            remarksTextarea.focus();
                        }
                        if (remarksError) {
                            remarksError.style.display = 'block';
                        }
                        return;
                    }
                }

                if (remarksTextarea) {
                    remarksTextarea.classList.remove('is-invalid');
                }

                if (remarksError) {
                    remarksError.style.display = 'none';
                }

                e.preventDefault();
                AppPopup.confirm({
                    title: 'Confirm Forward',
                    text: 'Are you sure you want to submit this review and forward to HOD?',
                    icon: 'question',
                    confirmButtonText: 'Yes, Forward'
                }).then(function(result) {
                    if (result && result.isConfirmed) {
                        form.submit();
                    }
                });
            });

            if (remarksTextarea) {
                remarksTextarea.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                        if (remarksError) {
                            remarksError.style.display = 'none';
                        }
                    }
                });
            }
        });

        @if (session('success'))
        AppPopup.success(@json(session('success')), 'Success', 1600);
        @endif
    </script>
     <style>
                        .card-header-dark {
                            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
                            color: white;
                            border-bottom: 3px solid #0d6efd;
                        }

                        /* Override AdminLTE dark theme styles for summary layout */
                   
                        .card-header.card-header-maroon {
                            background-color: #007bff !important;
                            color: #ffffff !important;
                        }

                        .card-header.bg-primary {
                            background-color: #007bff !important;
                            color: #ffffff !important;
                        }

                       
                    </style>
        </div>
    </section>
</div>
@endsection

