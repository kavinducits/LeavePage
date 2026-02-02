@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center">
    @include('StudyLeave.study_leave.process_stages')
</div>

<div class="container">
    
    <div class="row justify-content-center">
        
        <div class="col-md-11">
            
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                    <i class="fas fa-file-alt me-2 icon-gold"></i>
                    View Study Leave Application
                </h2>
                <a href="{{ route('StudyLeave.create') }}" class="btn btn-outline-primary">
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
@endsection
