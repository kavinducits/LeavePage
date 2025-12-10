@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            

            <!-- Extension History Section (Top) -->
            @include('StudyLeave.study_leave_extension.extension_history')

            <form method="POST" class="my-4">
                @csrf
                    <!-- Personal Details (readonly) -->
                @include('StudyLeave.basic_info_form')
                @include('StudyLeave.details_form')
                @include('StudyLeave.working_covering_persons_form')
             
                
                <div class="form-group mt-4 mb-3 d-flex justify-content-center">
                    <a href="{{ route('StudyLeave.create') }}" class="btn btn-primary btn-lg">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
