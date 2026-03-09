@extends('layouts.dashborad')
@section('title', 'HOD Academic Establishment Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('hod.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">HOD Academic Establishment Dashboard</span>
    </a>
@endsection
@section('sidebar')
<!-- Sidebar -->
    @php($pageName = 'Study Leave Extensions')
    @include('hod_academic_establishment.partials.sidebar')
@endsection

@section('main-content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            @include('hod_academic_establishment.study_leave.study_leave_extensions_accept_table')
        </div>
    </section>
</div>
@endsection
