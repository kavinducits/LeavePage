<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Leave Management</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('ma.partials.navbar')

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('ma.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">MA Dashboardl</span>
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
                                    <button type="button" class="btn btn-success px-5 py-2 rounded-pill fw-semibold" data-dismiss="modal">
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
                                    <button type="button" class="btn btn-danger px-5 py-2 rounded-pill fw-semibold" data-dismiss="modal">
                                        <i class="fas fa-times me-2"></i>Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Return Success Modal -->
                    <div class="modal fade" id="returnSuccessModal" tabindex="-1" aria-labelledby="returnSuccessModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-body text-center p-5">
                                    <div class="mb-4">
                                        <i class="fas fa-undo-alt text-warning" style="font-size: 4rem;"></i>
                                    </div>
                                    <h3 class="fw-bold text-warning mb-3">Application Returned Successfully!</h3>
                                    <p class="text-muted mb-2">The study leave application has been returned to the user for corrections.</p>
                                    @if(session('returned_reference'))
                                        <p class="mb-3">
                                            <strong>Reference Number:</strong> 
                                            <span class="badge bg-primary fs-6">{{ session('returned_reference') }}</span>
                                        </p>
                                    @endif
                                    <p class="text-muted small mb-4">The user will be notified and can make the necessary changes before resubmitting.</p>
                                    <button type="button" class="btn btn-warning px-5 py-2 rounded-pill fw-semibold" data-dismiss="modal">
                                        <i class="fas fa-check me-2"></i>OK
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @include('ma.study_leave.study_leave_accept_table')

                        @if ($acceptedApplications->count() > 0)
                            <div class="mt-3 text-muted text-center">
                                <small>
                                    <i class="fas fa-list"></i> Total Applications: {{ $acceptedApplications->count() }}
                                </small>
                            </div>
                        @endif
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
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    
    <!-- Show return success modal if session flag is set -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize DataTables
            $('.table').DataTable({
                "pageLength": 10,
                "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                "order": [[5, "desc"]], // Sort by Applied Date column (index 5) descending
                "language": {
                    "search": "Search:",
                    "lengthMenu": "Show _MENU_ entries",
                    "info": "Showing _START_ to _END_ of _TOTAL_ applications",
                    "infoEmpty": "Showing 0 to 0 of 0 applications",
                    "infoFiltered": "(filtered from _MAX_ total applications)",
                    "paginate": {
                        "first": "First",
                        "last": "Last",
                        "next": "Next",
                        "previous": "Previous"
                    }
                },
                "columnDefs": [
                    { "orderable": false, "targets": 7 } // Disable sorting on Actions column
                ]
            });

            @if(session('show_return_modal'))
                // Use Bootstrap 4 modal
                $('#returnSuccessModal').modal('show');
            @endif
            
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
</body>

</html>
