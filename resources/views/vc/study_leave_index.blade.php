 <!-- Content Wrapper -->
 <div class="content-wrapper">
     <!-- Main content -->
     <section class="content">
         <div class="container-fluid">

             <div class="container py-4">


                 @if (session('success'))
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                         {{ session('success') }}
                         <!-- <button type="button" class="btn-close" data-bs-dismiss="alert"></button> -->
                     </div>
                 @endif

                 @if (session('error'))
                     <div class="alert alert-danger alert-dismissible fade show" role="alert">
                         {{ session('error') }}
                         <!-- <button type="button" class="btn-close" data-bs-dismiss="alert"></button> -->
                     </div>
                 @endif
             </div>
             <!-- Study Leave Applications Table -->
             @include('vc.study_leave.study_leave_table')

             <!-- Study Leave Extension Applications Table -->
             @include('vc.study_leave.study_leave_extensions_table')
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
