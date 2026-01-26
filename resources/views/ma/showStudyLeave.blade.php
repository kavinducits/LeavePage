<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Application Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="hold-transition sidebar-mini">
  
    @php
        $statusId = $draft_study_leave->approval_status_id ?? null;
    @endphp

    <div class="wrapper">
        <!-- Navbar -->
        @include('ma.partials.navbar')

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('ma.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">MA Dashboard</span>
            </a>
            <!-- Sidebar -->
            @php($pageName = 'Study Leave')
            @include('ma.partials.sidebar')
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @include('ma.partials.header')
            

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0 fw-bold">Application Review</h2>
                            <p class="text-muted mb-0">Reference No: {{ $draft_study_leave->reference_no }}</p>
                        </div>
                        <a href="{{ route('ma.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>

                    <!-- Study Leave Duration Card -->
                    @include('ma.study_leave.study_leave_duration_card')

                    <!-- Personal Details (readonly) -->
                    @include('ma.partials.studyLeave', ['readonly' => true])
                  
                    
                  
                    <!-- Review Section based on Status -->
                    @if($statusId == 3)
                        <!-- Status: Editing (Returned to User) -->
                        <div class="alert alert-warning mt-4">
                            <i class="fas fa-undo me-2"></i>
                            <strong>Application Returned to User</strong>
                            <p class="mb-0 mt-2">This application has been returned to the user for corrections.</p>
                        </div>
                    @elseif($statusId == 5)
                        <!-- Status: Processing HOD Academic Establishment -->
                        <div class="alert alert-success mt-4">
                            <i class="fas fa-paper-plane me-2"></i>
                            <strong>Application Forwarded</strong>
                            <p class="mb-0 mt-2">This application has been forwarded to HOD Academic Establishment for review.</p>
                        </div>
                    @elseif($statusId == 4)
                        <!-- Status: Processing MA - Show Action Section -->
                        <div class="card">
                            <div class="card-header bg-dark text-white fw-semibold">
                                <i class="fas fa-tasks me-2"></i>Review Actions
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="actionRemark" class="form-label fw-semibold">Remarks</label>
                                    <textarea class="form-control" id="actionRemark" name="remark" rows="3"
                                              placeholder="Add any comments or remarks"></textarea>
                                    <div id="remarkError" class="form-text text-danger" style="display: none;">
                                        Remarks are required when returning an application.
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-start">
                                    <form id="returnForm" action="{{ route('ma.studyleave.return', $draft_study_leave->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" id="returnRemarkInput" name="remark" value="">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-undo me-2"></i>Return to User
                                        </button>
                                    </form>

                                    <div class="text-right">
                                        <form id="approveForm" action="{{ route('StudyLeave.approve', $draft_study_leave->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <input type="hidden" id="approveRemarkInput" name="remark" value="">
                                            <button type="submit" class="btn btn-success" {{ empty($departmentHead) ? 'disabled' : '' }}>
                                                <i class="fas fa-check me-2"></i>Forward
                                            </button>
                                        </form>

                                        @if(isset($departmentHead))
                                            <div class="card mt-2" style="min-width: 260px;">
                                                <div class="card-body py-2">
                                                    <div class="d-flex align-items-center">
                                                        <strong>Forward to,&nbsp;</strong>
                                                        <div>
                                                            <div class="fw-semibold">{{ $departmentHead->head_title ?? 'Head' }}&nbsp;{{ $departmentHead->head_name ?? ''}}</div>
                                                            <div class="text-muted small">{{ $departmentHead->head_position ?? '' }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="card mt-2 border-warning" style="min-width: 260px;">
                                                <div class="card-body py-2">
                                                    <div class="text-danger">
                                                        <strong>No active Department Head</strong>
                                                        <div class="text-muted small">Forwarding is disabled until a head is active.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Other statuses - Read-only view -->
                        <div class="alert alert-info mt-4">
                            <i class="fas fa-eye"></i> This application is in a different workflow stage. You have read-only access.
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('ma.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    @if($statusId == 4)
    <script>
        // Form validation and submission handling
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            // Get the remark value and set it to the hidden input
            const remarkValue = document.getElementById('actionRemark').value.trim();
            document.getElementById('approveRemarkInput').value = remarkValue;

            // Clear any previous error highlighting
            clearRemarkError();

            // Confirm action
            if (!confirm('Are you sure you want to forward this application to HOD?')) {
                e.preventDefault();
            }
        });

        document.getElementById('returnForm').addEventListener('submit', function(e) {
            const remarkValue = document.getElementById('actionRemark').value.trim();

            // Validate that remarks are provided for return action
            if (!remarkValue) {
                e.preventDefault();
                showRemarkError();
                return;
            }

            // Set the remark value to the hidden input
            document.getElementById('returnRemarkInput').value = remarkValue;

            // Clear any previous error highlighting
            clearRemarkError();

            // Confirm action
            if (!confirm('Are you sure you want to return this application to the user?')) {
                e.preventDefault();
            }
        });

        // Helper functions for error handling
        function showRemarkError() {
            const remarkTextarea = document.getElementById('actionRemark');
            const errorDiv = document.getElementById('remarkError');

            // Highlight the textarea
            remarkTextarea.classList.add('is-invalid');
            remarkTextarea.style.borderColor = '#dc3545';
            remarkTextarea.style.boxShadow = '0 0 0 0.2rem rgba(220, 53, 69, 0.25)';

            // Add shake animation
            remarkTextarea.classList.add('shake-animation');
            setTimeout(() => {
                remarkTextarea.classList.remove('shake-animation');
            }, 500);

            // Show error message
            errorDiv.style.display = 'block';

            // Focus on the textarea
            remarkTextarea.focus();

            // Show alert
            alert('Please provide remarks when returning an application.');
        }

        function clearRemarkError() {
            const remarkTextarea = document.getElementById('actionRemark');
            const errorDiv = document.getElementById('remarkError');

            // Remove highlighting
            remarkTextarea.classList.remove('is-invalid');
            remarkTextarea.style.borderColor = '';
            remarkTextarea.style.boxShadow = '';

            // Hide error message
            errorDiv.style.display = 'none';
        }

        // Clear error highlighting when user starts typing
        document.getElementById('actionRemark').addEventListener('input', function() {
            if (this.value.trim()) {
                clearRemarkError();
            }
        });
    </script>
    @endif
      <style>
                        /* Override AdminLTE dark theme styles for summary layout */
                   
                        .card-header.card-header-maroon {
                            background-color: #007bff !important;
                            color: #ffffff !important;
                        }

                        .card-header.bg-primary {
                            background-color: #007bff !important;
                            color: #ffffff !important;
                        }

                       
                    </style>

    <style>
        .form-control[readonly], .form-select:disabled, select:disabled {
            background-color: #e9ecef !important;
            color: #495057 !important;
            opacity: 1 !important;
        }

        /* Error highlighting for remarks */
        .form-control.is-invalid {
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }

        /* Animation for error highlighting */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .shake-animation {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</body>
</html>
