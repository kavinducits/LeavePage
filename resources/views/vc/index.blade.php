@extends('layouts.screen1')

@section('content')
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-0 fw-bold text-dark dashboard-header">
                    <i class="fas fa-crown me-2  text-primary"></i>Applications for VC Recommendation
                </h2>
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
                <i class="fas fa-list me-2"></i>Applications List
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Reference No</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Faculty</th>
                            <th>Applied Date</th>
                            <th>Leave Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                            <tr>
                                <td><span class="fw-semibold text-maroon">{{ $app->reference_no }}</span></td>
                                <td><strong>{{ $app->name_with_initials }}</strong></td>
                                <td>{{ $app->department }}</td>
                                <td>{{ $app->faculty }}</td>
                                <td>{{ \Carbon\Carbon::parse($app->applied_date)->format('Y-m-d') }}</td>
                                <td><span class="badge badge-gold">{{ $app->leave_type }}</span></td>
                                <td><span class="badge status-pending">{{ $app->status }}</span></td>
                                <td>
                                    <a href="{{ route('vc.show', $app->id) }}" class="btn btn-sm btn-outline-maroon">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Study Leave Applications Table -->
        @include('vc.study_leave.study_leave_table')

        <!-- Study Leave Extension Applications Table -->
        @include('vc.study_leave.study_leave_extensions_table')
    </div>
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
