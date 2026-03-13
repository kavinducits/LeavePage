@extends('layouts.app')

@section('content')
<style>
    .study-leave-black-theme {
        background: #0b0b0d;
        color: #e9ecef;
        min-height: calc(100vh - 60px);
        padding: 1.5rem 0;
    }

    .study-leave-black-theme .dashboard-header,
    .study-leave-black-theme .text-maroon,
    .study-leave-black-theme .text-muted {
        color: #f8f9fa !important;
    }

    .study-leave-black-theme .card,
    .study-leave-black-theme .process-stages-container .card {
        background-color: #15171a !important;
        border: 1px solid #2b3035 !important;
        color: #e9ecef;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
    }

    .study-leave-black-theme .card-header,
    .study-leave-black-theme .document-header,
    .study-leave-black-theme .bg-info {
        background: #1f2328 !important;
        color: #f8f9fa !important;
        border-bottom: 1px solid #343a40 !important;
    }

    .study-leave-black-theme .bg-primary,
    .study-leave-black-theme .btn-primary,
    .study-leave-black-theme .badge-primary,
    .study-leave-black-theme .alert-primary {
        background-color: #23272b !important;
        border-color: #3b4148 !important;
        color: #f8f9fa !important;
    }

    .study-leave-black-theme .btn-primary:hover,
    .study-leave-black-theme .btn-primary:focus {
        background-color: #2f3439 !important;
        border-color: #4a5058 !important;
        color: #ffffff !important;
    }

    .study-leave-black-theme .text-primary,
    .study-leave-black-theme .text-info,
    .study-leave-black-theme .link-primary {
        color: #cfd4da !important;
    }

    .study-leave-black-theme a,
    .study-leave-black-theme a.text-primary {
        color: #d7dce2 !important;
    }

    .study-leave-black-theme a:hover {
        color: #ffffff !important;
    }

    .study-leave-black-theme .table {
        color: #e9ecef;
    }

    .study-leave-black-theme .table thead th {
        background-color: #1f2328;
        color: #f8f9fa;
        border-color: #343a40;
    }

    .study-leave-black-theme .table td,
    .study-leave-black-theme .table th {
        border-color: #343a40;
    }

    .study-leave-black-theme .form-control,
    .study-leave-black-theme .form-select,
    .study-leave-black-theme textarea {
        background-color: #101214 !important;
        color: #f8f9fa !important;
        border: 1px solid #3b4148 !important;
    }

    .study-leave-black-theme .form-control:focus,
    .study-leave-black-theme .form-select:focus,
    .study-leave-black-theme textarea:focus {
        border-color: #5a626b !important;
        box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25) !important;
    }

    .study-leave-black-theme .form-control[readonly],
    .study-leave-black-theme .form-select:disabled,
    .study-leave-black-theme textarea[readonly] {
        background-color: #161a1d !important;
        color: #dfe4ea !important;
    }

    .study-leave-black-theme .btn-outline-secondary {
        color: #e9ecef;
        border-color: #6c757d;
    }

    .study-leave-black-theme .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    .study-leave-black-theme .alert {
        border-color: transparent;
    }

    .study-leave-black-theme .badge,
    .study-leave-black-theme .bg-light,
    .study-leave-black-theme .table-light {
        background-color: #2a2f34 !important;
        color: #f1f3f5 !important;
        border-color: #3b4148 !important;
    }

    .study-leave-black-theme label,
    .study-leave-black-theme .form-label,
    .study-leave-black-theme p,
    .study-leave-black-theme small,
    .study-leave-black-theme span,
    .study-leave-black-theme li,
    .study-leave-black-theme h1,
    .study-leave-black-theme h2,
    .study-leave-black-theme h3,
    .study-leave-black-theme h4,
    .study-leave-black-theme h5,
    .study-leave-black-theme h6 {
        color: #e9ecef;
    }

    .study-leave-black-theme .icon-gold {
        color: #ffd166 !important;
    }
</style>

<div class="study-leave-black-theme">
<div class="d-flex justify-content-center">
    @include('StudyLeave.study_leave.process_stages')
</div>

<div class="container">
    
    <div class="row justify-content-center">
        
        <div class="col-md-11">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                    <i class="fas fa-file-alt me-2 icon-gold"></i>
                    View Study Leave Application
                </h2>
                <a href="{{ route('StudyLeave.create') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back
                </a>
            </div>

            <!-- Process Stages Section -->
           

            <!-- Extension History Section (Top) -->
            @if((isset($extensions) && $extensions->count() > 0 )|| true)
            
            @include('StudyLeave.study_leave_extension.extension_history')
            
            @endif

            <!-- Progress Reports History Section -->
            @include('StudyLeave.study_leave_progress_reports.progress_reports_history')

            <form method="POST" class="my-4">
                @csrf
                    <!-- Personal Details (readonly) -->
                @include('StudyLeave.basic_info_form', ['readonly' => true])
                @include('StudyLeave.details_form', ['readonly' => true])
                @include('StudyLeave.working_covering_persons_form', ['readonly' => true])
             
            </form>
        </div>
    </div>
</div>
</div>
@endsection
