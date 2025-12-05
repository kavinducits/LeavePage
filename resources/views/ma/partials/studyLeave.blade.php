@extends('layouts.app')

@section('content')


  @include('StudyLeave.basic_info_form', ['readonly' => $readonly ?? true])
  @include('StudyLeave.details_form', ['readonly' => $readonly ?? true])
  @include('StudyLeave.working_covering_persons_form', ['readonly' => $readonly ?? true])
@endsection