@extends('layouts.app')

@section('content')

<!-- Progress Bar - Step 4 -->
@include('StudyLeave.partials.progress_bar', ['currentStep' => 4])

<div class="container py-4">

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <!-- Show remark if returned -->
    @isset($remark)
    <div class="alert alert-warning fw-semibold">
        Returned with remark:
        <pre class="mb-0">{{ $remark }}</pre>
    </div>
    @endisset
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-file-alt me-2 icon-gold"></i>
                Application for Study Leave
                
            </h2>
        </div>
        <a class="btn btn-outline-maroon" href="{{ route('StudyLeave.WorkCoveringPersons.create') }}">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <!-- Page Title -->
    <div class="text-center mb-4">
        <h2 class="fw-bold text-maroon">
            <i class="fas fa-clipboard-check me-2"></i>Application Summary
        </h2>
        <p class="text-muted">Review your study leave application before submission</p>
    </div>

    <!-- Form for Summary and Submit -->
    <form action="{{ route('StudyLeave.Submit') }}" method="POST" id="summary-form">
        @csrf

        <!-- Include Summary Layout Component -->
        @include('StudyLeave.partials.summary_layout')

    </form>
    <!--
    <div class="d-flex justify-content-end mt-4">
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-right me-2"></i>Next: Leave Details
        </a>
    </div>
-->

@endsection