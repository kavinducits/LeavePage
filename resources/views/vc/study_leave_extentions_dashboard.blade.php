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
    @include('vc.study_leave.study_leave_extensions_index')

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
        AppPopup.success(@json(session('success')), 'Success', 1600);
        @endif

        @if(session('error'))
        AppPopup.error(@json(session('error')));
        @endif
    });
</script>
@endsection
