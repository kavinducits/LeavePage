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
           
            @include('hod.study_leave.study_leave_progress_reports_table')
        </div>
    </section>
</div>


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
