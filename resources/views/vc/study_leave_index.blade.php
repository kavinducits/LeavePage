 <!-- Content Wrapper -->
 <div class="content-wrapper">
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

             <!-- Study Leave Applications Table -->
             @include('vc.study_leave.study_leave_table')

             
         </div>
     </section>
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
