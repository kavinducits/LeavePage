@extends('layouts.dashborad')
@section('title', 'VC Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('vc.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">VC Dashboard</span>
    </a>
@endsection
@section('sidebar')
<!-- Sidebar -->
    @php($pageName = 'Study Leave Extensions')
    @include('vc.partials.sidebar')
@endsection

@section('main-content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            @include('vc.study_leave.study_leave_extensions_accept_table')
        </div>
    </section>
</div>
@endsection
