@extends('layouts.dashborad')
@section('title', 'Dean Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('dean.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">Dean Dashboard</span>
    </a>
@endsection
@section('sidebar')
<!-- Sidebar -->
    @php($pageName = 'Study Leave Progress')
    @include('dean.partials.sidebar')
@endsection

@section('main-content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            @include('dean.study_leave.study_leave_progress_reports_accept_table')
        </div>
    </section>
</div>
@endsection
