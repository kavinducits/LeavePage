@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

<!-- Progress Bar - Step 1 -->
@include('StudyLeave.partials.progress_bar', ['currentStep' => 1])

<div class="container py-4">

    
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
        <a class="btn btn-outline-maroon" href="{{ route('StudyLeave.create') }}">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>




    <div class="mb-4">
        <div class="card shadow-sm rounded-3 border-0">
            <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold text-maroon">
                    <i class="fas fa-table me-2 icon-gold"></i>Study Leave Summary
                </h5>
            </div>
            <div class="card-body pt-2 pb-0 px-3">
                <div class="table-responsive">
                    <table id="basicinfo-study-leave-summary-table" class="table table-hover table-bordered align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 34%;">Reference No</th>
                                <th class="text-center" style="width: 33%;">Start Date</th>
                                <th class="text-center" style="width: 33%;">End Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($studyLeaveSummaryRows ?? collect()) as $summaryRow)
                                <tr>
                                    <td class="text-center fw-semibold">{{ $summaryRow->reference_no ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        {{ $summaryRow->start_date ? \Carbon\Carbon::parse($summaryRow->start_date)->format('Y-m-d') : 'N/A' }}
                                    </td>
                                    <td class="text-center">
                                        {{ $summaryRow->end_date ? \Carbon\Carbon::parse($summaryRow->end_date)->format('Y-m-d') : 'N/A' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No study leave summary records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



    <!-- Form for Basic Information -->

    <form  action="{{ route('StudyLeave.BasicInfo.store') }}" method="POST" enctype="multipart/form-data" id="leave-form" class="needs-validation" novalidate>
        @csrf

        @if(isset($leave))
            <input type="hidden" name="leave_id" value="{{ $leave->id }}">
            <input type="hidden" name="reference_no" value="{{ $leave->reference_no }}">
        @else
            <input type="hidden" name="reference_no" value="">
            @if(isset($academicYear))
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">
            @endif
        @endif

        @include('StudyLeave.basic_info_form')

       

<div class="d-flex justify-content-between mt-4">
    <button type="button" class="btn btn-outline-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm" onclick="saveAndExit()">
        <i class="fas fa-save me-2"></i>Save and Exit
    </button>
    <button type="submit" class="btn btn-maroon px-4 py-2 rounded-pill fw-semibold shadow-sm">
        Next: Leave Details <i class="fas fa-arrow-right ms-2"></i>
    </button>
</div>
    </form>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
    $(function() {
        const hasSummaryRows = @json(($studyLeaveSummaryRows ?? collect())->count() > 0);

        if (hasSummaryRows && $.fn.DataTable && $('#basicinfo-study-leave-summary-table').length) {
            $('#basicinfo-study-leave-summary-table').DataTable({
                pageLength: 5,
                lengthMenu: [[5, 10, 25, -1], [5, 10, 25, 'All']],
                order: [[1, 'desc']],
                language: {
                    search: 'Search:',
                    lengthMenu: 'Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    infoEmpty: 'Showing 0 to 0 of 0 entries',
                    infoFiltered: '(filtered from _MAX_ total entries)',
                    paginate: {
                        first: 'First',
                        last: 'Last',
                        next: 'Next',
                        previous: 'Previous'
                    }
                }
            });
        }
    });
    </script>

    <script>
    function saveAndExit() {
        const form = document.getElementById('leave-form');
        const originalAction = form.action;
        
        // Change form action to save and exit route
        
        form.action = "{{ route('StudyLeave.BasicInfo.exit') }}";
        form.submit();
        
        // Restore original action (optional, for safety)
        form.action = originalAction;
    }
    </script>
-
    <!--
    <div class="d-flex justify-content-end mt-4">
        <a class="btn btn-outline-maroon">
            <i class="fas fa-arrow-right me-2"></i>Next: Leave Details
        </a>
    </div>
-->

@endsection