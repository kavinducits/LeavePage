@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            @if(isset($draft_study_leave->ma_remarks) && $draft_study_leave->ma_remarks)
                <div class="alert alert-warning alert-dismissible fade show mb-3 border-warning" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Important Notice</h5>
                    <strong>Management Assistant Remarks:</strong>
                    <p class="mb-0 mt-2">{{ $draft_study_leave->ma_remarks }}</p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            

            <form method="POST" action="{{ route('StudyLeave.update.edite.application', ['id' => $draft_study_leave->id]) }}" enctype="multipart/form-data" class="my-4">
                @csrf
                    <!-- Personal Details (readonly) -->
                @include('StudyLeave.basic_info_form', ['readonly' => false])
                @include('StudyLeave.details_form', ['readonly' => false])
                @include('StudyLeave.working_covering_persons_form', ['readonly' => false])
               
                
                <div class="form-group mt-4 mb-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg">Re-Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
