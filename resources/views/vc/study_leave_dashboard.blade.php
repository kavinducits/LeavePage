@extends('layouts.dashborad')
@section('title', 'VC Dashboard - Leave Management')
@section('brand-logo')
   <a href="{{ route('vc.leave.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">VC Dashboard</span>
    </a>
@endsection
@section('sidebar')
    @include('vc.partials.sidebar')
@endsection

@section('main-content')
    @include('vc.study_leave_index')

@endsection
