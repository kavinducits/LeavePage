@extends('layouts.app')

@section('content')

  @include('StudyLeave.partials.summary_layout', ['readonly' => true])
@endsection