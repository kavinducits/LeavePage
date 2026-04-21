<!-- Study Leave Summary Table -->
<div class="card shadow-sm rounded-3 border-0 mb-4">
    <div class="card-header card-header-maroon fw-semibold d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-table me-2"></i>Study Leave Summary
        </span>
    </div>
    <div class="card-body pt-2 pb-0 px-3">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0">
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