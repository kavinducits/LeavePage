@extends('layouts.dashborad')
@section('brand-logo')
   <a href="{{ route('hod.leave.index') }}" class="brand-link">
                <span class="brand-text font-weight-light">HOD Dashboard</span>
    </a>
@endsection
@section('sidebar')
    @include('hod.partials.sidebar')
@endsection

@section('main-content')
    @include('hod.study_leave.study_leave_index')

    <style>
        .card-header-dark {
            background: linear-gradient(135deg, #212529 0%, #343a40 100%);
            color: white;
            border-bottom: 3px solid #0d6efd;
        }

        .btn-outline-dark:hover {
            color: white;
            background-color: #212529;
            border-color: #212529;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.75rem;
        }

        .btn-sm {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
@endsection
