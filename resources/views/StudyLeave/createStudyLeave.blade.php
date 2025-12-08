<!-- resources/views/leaves/index.blade.php -->

@extends('layouts.app')

@section('content')
<style>

    .text-warning {
    --bs-text-opacity: 1;
    color: rgb(16 16 15) !important;
    }
    
    .active-draft-alert {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
    }

    .active-draft-item {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%) !important;
        border: 2px solid !important;
        box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3) !important;
        animation: pulse-warning 2s infinite;
    }

    @keyframes pulse-warning {
        0% { box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3); }
        50% { box-shadow: 0 6px 12px rgba(255, 193, 7, 0.5); }
        100% { box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3); }
    }

    .draft-action-buttons {
        margin: 2rem 0;
    }

    .draft-action-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .draft-action-item:hover {
        transform: translateY(-2px);
        text-decoration: none;
    }

    .draft-action-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        font-size: 2.5rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }

    .draft-action-item:first-child .draft-action-icon {
        background: #fff3cd;
        border: 2px solid #ffc107;
        color: #856404;
    }

    .draft-action-item:first-child:hover .draft-action-icon {
        background: #ffc107;
        color: #212529;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
    }

    .draft-action-item:last-child .draft-action-icon {
        background: #f8d7da;
        border: 2px solid #dc3545;
        color: #721c24;
    }

    .draft-action-item:last-child:hover .draft-action-icon {
        background: #dc3545;
        color: white;
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
    }

    @media (max-width: 768px) {
        .draft-action-buttons {
            flex-direction: column;
            align-items: center;
            gap: 2rem !important;
            margin: 1rem 0;
        }

        .draft-action-item {
            margin-bottom: 1rem;
        }
    }
</style>
    <div class="container py-4">
        <h3 class="mb-4 fw-bold text-center text-maroon dashboard-header">
            <i class="fas fa-file-alt me-2 icon-gold"></i>New Study Leave Applications
        </h3>

        <!-- Flash Messages -->
        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- New Application -->
        <div class="mb-4 text-center">
            @if($hasActiveDraft)
                <!-- Show draft continuation options when user has an active draft -->
                @php
                    $activeDraft = $drafts;
                @endphp
                @if($activeDraft)
                    <div class="text-center mb-3">
                        <p class="text-muted mb-2">
                            <i class="fas fa-info-circle me-1"></i>
                            You have an active draft. Choose an option below:
                        </p>
                    </div>
                    <div class="d-flex justify-content-center gap-4 draft-action-buttons flex-wrap">
                        <a href="{{ route('StudyLeave.continue.draft', $activeDraft->id) }}" class="d-inline-block text-decoration-none draft-action-item">
                            <div class="draft-action-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div class="text-center">
                                <span class="fw-semibold text-maroon">Continue Draft</span><br>
                                <small class="text-muted">{{ $activeDraft-> reference_no?? 'N/A' }}</small>
                            </div>
                        </a>

                        <form action="{{ route('StudyLeave.DeleteDraft', $activeDraft->id) }}" method="POST" class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn p-0 border-0 bg-transparent draft-action-item" onclick="return confirm('Are you sure you want to delete this draft and start a new application?')">
                                <div class="draft-action-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                    <i class="bi bi-trash"></i>
                                </div>
                                <div class="text-center">
                                    <span class="fw-semibold text-maroon">Delete Draft & Start New</span>
                                </div>
                            </button>
                        </form>
                    </div>
                @endif
            @else
                <!-- Show new application section when no active draft -->
                @if(isset($isEnableStudyLeaveRequiste) && $isEnableStudyLeaveRequiste)
                    <div class="new-application-section">
                        <div class="mb-3">
                            <label for="academic-year" class="form-label fw-semibold text-maroon">
                                <i class="fas fa-calendar-alt me-2 icon-gold"></i>Select Academic Year
                            </label>
                            <select class="form-select" id="academic-year" name="academic_year">
                                <option value="">Choose Academic Year...</option>
                                @foreach($academicYears as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <a href="#" class="d-inline-block text-decoration-none" id="new-application-button" onclick="startNewApplication(event)">
                            <div class="new-app-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="bi bi-journal-plus"></i>
                            </div>
                            <div><span class="fw-semibold text-maroon">Start a New Application</span></div>
                        </a>
                    </div>
                @endif
            @endif
        </div>

       
        

        <!-- Previous Leaves -->
        <div class="mb-4">
            <div class="card shadow-sm rounded-3 border-0">
                <div class="card-header bg-white border-bottom-0 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-semibold text-maroon">
                        <i class="fas fa-history me-2 icon-gold"></i>Your  
                        .Study Leaves
                    </h5>
                </div>
                <div class="card-body pt-2 pb-0 px-3">
                    <table class="table table-striped table-bordered bg-white align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Applied Date</th>
                                <th>Ref No</th>
                                <th>Leave Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                                   @php
                                $iteration=0;
                            @endphp
                            
                                @forelse(($previousLeaves ?? [])->sortByDesc('id') as $leave)
                                @php
                               
                                $iteration=$iteration+1;
                            @endphp
                                    <tr class="hoverable-row">
                                        <td>
                                            {{ optional($leave->created_at) ? \Carbon\Carbon::parse($leave->created_at)->format('Y-m-d') : '' }}
                                        </td>
                                        <td>{{ $leave->reference_no ?? 'N/A' }}</td>
                                        <td>{{ $leave->leave_payment_type ?? 'Study Leave' }}</td>
                                        <td>
                                            @php
                                                $statusValue = $leave->status_id ?? 0;
                                                if ($statusValue == 1) {
                                                    $status = 'Approved';
                                                    $badgeClass = 'bg-success';
                                                } elseif ($statusValue == 2) {
                                                    $status = 'Rejected';
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($statusValue == 3) {
                                                    $status = 'Return';
                                                    $badgeClass = 'bg-warning text-dark';
                                                    
                                                } else {
                                                    $status = 'Pending';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>
                                            
                                        </td>
                                        <td>
                                           
                                            @php
                                                $statusValue = $leave->status_id ?? 0;
                                                if ($statusValue == 1) {
                                                   // $route = 'Approved';
                                                  //  $btnName = 'View';
                                                    $badgeClass = 'bg-success';
                                                    if($leave->study_leave_to >= $currentDate){
                                                        $route = 'StudyLeave.show.studyLeave';
                                                        $btnName = 'View';
                                                    }else{
                                                        if($iteration ==1 && $isEnableStudyLeaveRequiste && !$hasActiveDraft){
                                                             $route = 'StudyLeave.show.editeForm';
                                                            $btnName = 'Extend';
                                                        }else{
                                                             $route = 'StudyLeave.show.studyLeave';
                                                            $btnName = 'View';
                                                        }
                                                       // $btnName = 'Extend';
                                                    }
                                                } elseif ($statusValue == 2) {
                                                    $route = 'Rejected';
                                                     $btnName = 'Close';
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($statusValue == 3) {
                                                    $route = 'StudyLeave.show.editeForm';
                                                     $btnName = 'Edite';
                                                    $badgeClass = 'bg-warning text-dark';
                                                    
                                                } else {
                                                     $route = 'Pending';
                                                     $btnName = 'Pending';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                            @endphp
                                            <a href="{{ route($route, $leave->id) }}" class="btn btn-sm btn-primary">{{ $btnName }}</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No previous leaves found.</td>
                                    </tr>
                                @endforelse


                              
                            
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

<style>
.card-header {
    background: #f8fafc !important;
}
.hoverable-row:hover {
    background-color: #fbeed7 !important;
    transition: background 0.2s;
}
.table th {
    font-weight: 600;
    color: #2d2d2d;
    background: #f8fafc;
}
.table td {
    color: #3a3a3a;
}
    .new-app-icon {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #f0f6ff;
    border: 2px solid #0d6efd;
    font-size: 2.5rem;
    color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13,110,253,0.08);
}

.new-application-section {
    max-width: 400px;
    margin: 0 auto;
}

.new-application-section .form-select {
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.new-application-section .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.new-application-section .form-label {
    font-size: 1.1rem;
    margin-bottom: 0.75rem;
}
</style>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const toggleButton = document.querySelector('[data-bs-toggle="collapse"]');
    const toggleIcon = document.getElementById('toggleIcon');
    const draftsCollapse = document.getElementById('draftsCollapse');

    draftsCollapse.addEventListener('shown.bs.collapse', () => {
        toggleIcon.classList.replace('bi-plus', 'bi-dash');
    });

    draftsCollapse.addEventListener('hidden.bs.collapse', () => {
        toggleIcon.classList.replace('bi-dash', 'bi-plus');
    });

   
    function startNewApplication(event) {
        event.preventDefault();

        const academicYear = document.getElementById('academic-year').value;

        if (!academicYear) {
            Swal.fire({
                icon: 'warning',
                title: 'Academic Year Required',
                text: 'Please select an academic year before starting a new application.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            }).then(() => {
                document.getElementById('academic-year').focus();
            });
            return false;
        }

        // Create and submit a POST form to the StudyLeave.store route
        const form = document.createElement('form');
        form.method = 'POST';
        form.setAttribute('action', '{{ route("StudyLeave.store") }}');

        // CSRF token
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        form.appendChild(tokenInput);

        // academic_year input
        const yearInput = document.createElement('input');
        yearInput.type = 'hidden';
        yearInput.name = 'academic_year';
        yearInput.value = academicYear;
        form.appendChild(yearInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>