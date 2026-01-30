@extends('layouts.screen1')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0 fw-bold text-maroon dashboard-header">
                <i class="fas fa-graduation-cap me-2 icon-gold"></i>Dean Dashboard
            </h2>
            <div class="text-muted">
                <i class="fas fa-clock me-1"></i>Applications Pending Dean Review
            </div>
        </div>

        <!-- Success Modal -->
        @if(session('success'))
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold text-success mb-3">Success!</h3>
                        <p class="text-muted mb-4">{{ session('success') }}</p>
                        <button type="button" class="btn btn-success px-5 py-2 rounded-pill fw-semibold" data-bs-dismiss="modal">
                            <i class="fas fa-check me-2"></i>OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Error Modal -->
        @if(session('error'))
        <div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold text-danger mb-3">Error</h3>
                        <p class="text-muted mb-4">{{ session('error') }}</p>
                        <button type="button" class="btn btn-danger px-5 py-2 rounded-pill fw-semibold" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="card">
            <div class="card-header card-header-maroon fw-semibold">
                <i class="fas fa-list me-2"></i>Submitted Applications
            </div>
            <div class="card-body p-0">
                @if ($applications->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-3">Reference No</th>
                                    <th>Name with Initials</th>
                                    <th>Department</th>
                                    <th>Faculty</th>
                                    <th>Leave Type</th>
                                    <th>Applied Date</th>
                                    <th>Status</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($applications as $app)
                                    <tr>
                                        <td class="px-3">
                                            <span class="fw-semibold text-maroon">{{ $app->reference_no }}</span>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $app->name_with_initials }}</div>
                                        </td>
                                        <td>{{ $app->department }}</td>
                                        <td>{{ $app->faculty }}</td>
                                        <td>
                                            <span class="badge badge-gold">{{ $app->leave_type }}</span>
                                        </td>
                                        <td>
                                            <div class="text-muted">
                                                {{ \Carbon\Carbon::parse($app->applied_date)->format('M d, Y') }}
                                            </div>
                                            <small class="text-muted">
                                                {{ \Carbon\Carbon::parse($app->applied_date)->format('h:i A') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge status-pending">{{ $app->status }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('dean.show', $app->id) }}"
                                                class="btn btn-sm btn-outline-maroon">
                                                <i class="fas fa-eye me-1"></i>View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="text-muted mb-3">
                            <i class="fas fa-inbox fa-3x"></i>
                        </div>
                        <h5 class="text-muted">No Applications Pending</h5>
                        <p class="text-muted">There are no applications currently waiting for review.</p>
                    </div>
                @endif
            </div>
        </div>

        @if ($applications->count() > 0)
            <div class="mt-3 text-muted text-center">
                <small>Total Applications: {{ $applications->count() }}</small>
            </div>
        @endif

        <!-- Study Leave Applications Table -->
        @include('dean.study_leave.study_leave_table')

        <!-- Study Leave Extension Applications Table -->
        @include('dean.study_leave.study_leave_extensions_table')
    </div>

    <style>
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

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('success'))
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        @endif
        @if(session('error'))
            var errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        @endif
    });
</script>
@endsection
