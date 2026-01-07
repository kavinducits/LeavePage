@extends('layouts.dashborad')
@section('title', 'Dean Dashboard - Leave Management')
@section('brand-logo')
   <a href="{{ route('dean.leave.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">Dean Dashboard</span>
    </a>
@endsection
@section('sidebar')
    @include('dean.partials.sidebar')
@endsection

@section('main-content')
    @include('dean.study_leave_index')

@endsection
