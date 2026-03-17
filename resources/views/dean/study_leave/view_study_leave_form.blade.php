@extends('layouts.dashborad')
@section('title', 'Dean Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('dean.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">Dean Dashboard</span>
    </a>
@endsection
@section('sidebar')
    @php($pageName = 'Study Leave')
    @include('dean.partials.sidebar')
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
        <a href="{{ isset($from) && $from == 'accepted' ? route('dean.study.leave.index.accepted') : route('dean.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
    @endif

     <!-- Study Leave Details (readonly) -->
    @include('StudyLeave.basic_info_form', ['readonly' => true])
    @include('StudyLeave.details_form', ['readonly' => true])
    @include('StudyLeave.working_covering_persons_form', ['readonly' => true])
    @include('hod_academic_establishment.study_leave.study_leave_hod_academic_establishment_review_section', ['readonly' => true])
    @include('hod.study_leave.study_leave_hod_review_section', ['readonly' => true])

    @if(isset($from) && $from == 'accepted')
        @include('dean.study_leave.study_leave_dean_review_section', ['readonly' => true])
        <div class="alert alert-success mt-3">
            <i class="fas fa-check-circle me-2"></i>This application has already been forwarded.
        </div>
    @else
    <!-- Dean Review Section -->
    <form action="{{ route('dean.view.studyLeave.approve', $draft_study_leave->id) }}" method="POST" id="deanReviewForm">
        @csrf
        
        @include('dean.study_leave.study_leave_dean_review_section', ['readonly' => false])

        <!-- Action Buttons -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
        <a href="{{ isset($from) && $from == 'accepted' ? route('dean.study.leave.index.accepted') : route('dean.index') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>

                    <div class="text-end">
                        <div class="card mb-2">
                            <div class="card-body py-2">
                                <div class="d-flex align-items-center justify-content-end">
                                    <strong>Forward to,&nbsp;</strong>
                                    <div>
                                        <div class="fw-semibold">Vice Chancellor</div>
                                        <div class="text-muted small">Final Approval</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success btn-lg w-100" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>Submit Review & Forward to VC
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    @endif
</div>


<!-- Forward Confirmation Modal -->
<div class="modal fade" id="forwardConfirmModal" tabindex="-1" role="dialog" aria-labelledby="forwardConfirmModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="forwardConfirmModalLabel">Confirm Forward</h5>
            </div>
            <div class="modal-body">
                <p class="mb-0">is that ok to foward it to VC</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="forwardConfirmCancel">Cancel</button>
                <button type="button" class="btn btn-success" id="forwardConfirmOk">OK</button>
            </div>
        </div>
    </div>
</div>
<style>
.card-header-dark {
    background: linear-gradient(135deg, #212529 0%, #343a40 100%);
    color: white;
    border-bottom: 3px solid #0d6efd;
}

.text-dark {
    color: #212529;
}

.text-primary {
    color: #0d6efd;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.form-control[readonly], .form-select:disabled, select:disabled {
    background-color: #e9ecef !important;
    color: #495057 !important;
    opacity: 1 !important;
}

.btn-lg {
    padding: 0.5rem 1.5rem;
    font-size: 1rem;
}

.btn-success {
    background-color: #28a745;
    border-color: #28a745;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}
 .card-header-maroon {
            background-color: #0d6efd !important;
            color: #ffffff !important;
        }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('deanReviewForm');
    const recommendNo = document.getElementById('recommendNo');
    const notRecommendReasonTextarea = document.getElementById('dean_not_recommend_reason');
    let forwardConfirmed = false;

    if (!form) {
        return;
    }

    form.addEventListener('submit', function(e) {
        if (recommendNo && recommendNo.checked) {
            const reasonValue = notRecommendReasonTextarea ? notRecommendReasonTextarea.value.trim() : '';
            if (!reasonValue) {
                e.preventDefault();
                if (notRecommendReasonTextarea) {
                    notRecommendReasonTextarea.classList.add('is-invalid');
                    notRecommendReasonTextarea.focus();
                }
                return;
            }
        }

        if (!forwardConfirmed) {
            e.preventDefault();
            $('#forwardConfirmModal').modal('show');
        }
    });

    $('#forwardConfirmOk').on('click', function() {
        forwardConfirmed = true;
        $('#forwardConfirmModal').modal('hide');
        form.submit();
    });

    $('#forwardConfirmCancel').on('click', function() {
        forwardConfirmed = false;
        $('#forwardConfirmModal').modal('hide');
    });

    if (notRecommendReasonTextarea) {
        notRecommendReasonTextarea.addEventListener('input', function() {
            if (this.value.trim()) {
                this.classList.remove('is-invalid');
            }
        });
    }
});
</script>
        </div>
    </section>
</div>
@endsection

