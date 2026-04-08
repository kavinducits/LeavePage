@extends('layouts.dashborad')
@section('title', 'HOD Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('hod.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">HOD Dashboard</span>
    </a>
@endsection
@section('sidebar')
<!-- Sidebar -->
    @php($pageName = 'Study Leave')
    @include('hod.partials.sidebar')
    
@endsection

@section('main-content')
    @include('hod.study_leave.study_leave_index')
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            AppPopup.success(@json(session('success')), 'Success', 1600);
        @endif
        
        @if(session('error'))
            // Show error modal
            $('#errorModal').modal('show');
        @endif
    });
</script>
@endsection
