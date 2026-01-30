@extends('layouts.dashborad')
@section('title', 'HOD Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('hod.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">HOD Academic Establishment Dashboard</span>
    </a>
@endsection
@section('sidebar')
<!-- Sidebar -->
    @php($pageName = 'Study Leave')
    @include('hod_academic_establishment.partials.sidebar')
    
@endsection

@section('main-content')
    @include('hod_academic_establishment.study_leave.study_leave_index')
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            // Show success modal
            $('#successModal').modal('show');
        @endif
        
        @if(session('error'))
            // Show error modal
            $('#errorModal').modal('show');
        @endif
    });
</script>
@endsection
