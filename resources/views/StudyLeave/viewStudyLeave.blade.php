@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-center">
    @include('StudyLeave.study_leave.process_stages')
</div>

<div class="container">
    
    <div class="row justify-content-center">
        
        <div class="col-md-11">
            
            <div>
                <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                    <i class="fas fa-file-alt me-2 icon-gold"></i>
                    View Study Leave Application
                </h2>
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
             
                
                <div class="form-group mt-4 mb-3 d-flex justify-content-center">
                    <a href="{{ route('StudyLeave.create') }}" class="btn btn-primary btn-lg">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
