@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            

            <form method="POST" action="{{ route('StudyLeave.create') }}" class="my-4">
                @csrf
                    <!-- Personal Details (readonly) -->
                @include('StudyLeave.basic_info_form')
                @include('StudyLeave.details_form')
                @include('StudyLeave.working_covering_persons_form')
                @include('StudyLeave.summary_form')
                
                <div class="form-group mt-4 mb-3 d-flex justify-content-center">
                    <button type="submit" class="btn btn-primary btn-lg">Back</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
