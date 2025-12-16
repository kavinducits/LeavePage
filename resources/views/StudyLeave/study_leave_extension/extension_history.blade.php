<!-- Study Leave Extension History -->
<div class="card mb-4 shadow-sm">
    <div class="card-header" style="background-color: #000000; color: white;">
        <h5 class="mb-0">
            <i class="fas fa-history me-2"></i>Extension History
        </h5>
    </div>
    <div class="card-body">
        @if(isset($extensions) && $extensions->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 18%">Original End Date</th>
                            <th style="width: 18%">Extended End Date</th>
                            <th style="width: 12%">Extension Period</th>
                            <th style="width: 32%">Reason for Extension</th>
                            <th style="width: 15%">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($extensions as $index => $extension)
                            @php
                                $oldEndDate = \Carbon\Carbon::parse($extension->old_end_date);
                                $newEndDate = \Carbon\Carbon::parse($extension->new_end_date);
                                $extensionPeriod = $oldEndDate->diffInDays($newEndDate);
                                
                                // Status badge configuration
                                $statusBadge = '';
                                $statusText = '';
                                
                                switch($extension->status_id) {
                                    /*
                                    case 0:
                                        $statusBadge = 'bg-secondary';
                                        $statusText = 'Draft';
                                        break;
                                    case 1:
                                        $statusBadge = 'bg-success';
                                        $statusText = 'Approved';
                                        break;
                                    case 2:
                                        $statusBadge = 'bg-warning text-dark';
                                        $statusText = 'Pending';
                                        break;
                                    case 3:
                                        $statusBadge = 'bg-info text-dark';
                                        $statusText = 'Returned';
                                        break;
                                    case 4:
                                        $statusBadge = 'bg-danger';
                                        $statusText = 'Rejected';
                                        break;
                                    default:
                                        $statusBadge = 'bg-secondary';
                                        $statusText = 'Unknown';
                                        */
                                    case 1:
                                        $statusBadge = 'bg-success';
                                        $statusText = 'Approved';
                                        break;
                                    case 2:
                                        $statusBadge = 'bg-danger';
                                        $statusText = 'Rejected';
                                        break;
                                    default:
                                        $statusBadge = 'bg-warning text-dark';
                                        $statusText = 'Pending';
                                }
                            @endphp
                            <tr>
                                <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                                <td>
                                    <i class="fas fa-calendar-alt text-muted me-1"></i>
                                    {{ $oldEndDate->format('d M Y') }}
                                </td>
                                <td>
                                    <i class="fas fa-calendar-check text-success me-1"></i>
                                    {{ $newEndDate->format('d M Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ $extensionPeriod }} days
                                    </span>
                                </td>
                                <td>
                                    <small>{{ \Illuminate\Support\Str::limit($extension->reason_for_extension, 100) }}</small>
                                    @if(strlen($extension->reason_for_extension) > 100)
                                        <button type="button" 
                                                class="btn btn-sm btn-link p-0" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#reasonModal{{ $extension->id }}">
                                            <i class="fas fa-eye"></i> View Full
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge {{ $statusBadge }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                            </tr>

                            <!-- Reason Modal -->
                            @if(strlen($extension->reason_for_extension) > 100)
                                <div class="modal fade" id="reasonModal{{ $extension->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header" style="background-color: #800020; color: white;">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-file-alt me-2"></i>Extension Reason - Request #{{ $index + 1 }}
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="alert alert-light border">
                                                    <strong>Original End Date:</strong> {{ $oldEndDate->format('d M Y') }}<br>
                                                    <strong>Extended End Date:</strong> {{ $newEndDate->format('d M Y') }}<br>
                                                    <strong>Extension Period:</strong> {{ $extensionPeriod }} days
                                                </div>
                                                <h6 class="fw-bold mb-3">Reason for Extension:</h6>
                                                <p class="text-justify" style="white-space: pre-wrap;">{{ $extension->reason_for_extension }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Statistics -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-list-ol me-2"></i>
                        <strong>Total Extensions:</strong> {{ $extensions->count() }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-success mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Approved:</strong> {{ $extensions->where('status_id', 1)->count() }}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-hourglass-half me-2"></i>
                        <strong>Pending:</strong> {{ $extensions->where('status_id', 2)->count() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0">No extension requests found for this study leave.</p>
            </div>
        @endif
    </div>
</div>

<style>
    .btn-close-white {
        filter: brightness(0) invert(1);
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(128, 0, 32, 0.05);
    }
</style>
