@extends('layouts.dashborad')
@section('title', 'Dean Dashboard - Leave Management')
@section('brand-logo')
    <a href="{{ route('dean.leave.index') }}" class="brand-link">
        <span class="brand-text font-weight-light">Dean Dashboard</span>
    </a>
@endsection
@section('sidebar')
<!-- Sidebar -->
    @php($pageName = 'Study Leave Extensions')
    @include('dean.partials.sidebar')
@endsection

@section('main-content')
    @include('dean.study_leave.study_leave_extensions_index')

@endsection

@section('scripts')
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        var successMessage = @json(session('success'));
        var errorMessage = @json(session('error'));
        
        console.log('Success message:', successMessage);
        console.log('Error message:', errorMessage);
        
        // Show SweetAlert popup for success messages
        if (successMessage) {
            console.log('Showing success alert');
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: successMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        }

        // Show SweetAlert popup for error messages
        if (errorMessage) {
            console.log('Showing error alert');
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: errorMessage,
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
        }
    });
</script>
@endsection
