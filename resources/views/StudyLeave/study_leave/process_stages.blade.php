{{-- Study Leave Process Stages Component --}}
<div class="process-stages-container mb-4" style="max-width: 100%; overflow: hidden;">
    <div class="card border-0 shadow-sm" style="overflow: hidden;">
        <div class="card-body p-4" style="overflow-x: auto; overflow-y: hidden;">
            <h5 class="mb-4 text-center fw-bold text-maroon">
                <i class="fas fa-route me-2"></i>Application Process Status
            </h5>
            
            <div class="stages-wrapper" style="overflow-x: auto; overflow-y: hidden; width: 100%;">
                <div class="stages-timeline">
                    @php
                        // Define the stages with their corresponding status_id and employee tracking field
                        $stages = [
                            ['id' => 4, 'name' => 'MA Review', 'icon' => 'fa-user-tie', 'empno_field' => 'ma_empno'],
                            ['id' => 9, 'name' => 'Registrar Review', 'icon' => 'fa-user-shield', 'empno_field' => 'registrar_empno', 'approval_field' => 'registrar_recommendation'],
                            ['id' => 5, 'name' => 'HOD Review', 'icon' => 'fa-user-check', 'empno_field' => 'hod_empno', 'approval_field' => 'hod_recommend'],
                            ['id' => 6, 'name' => 'Dean Review', 'icon' => 'fa-user-graduate', 'empno_field' => 'dean_empno', 'approval_field' => 'dean_leave_recommendation_status'],
                            ['id' => 7, 'name' => 'VC Approval', 'icon' => 'fa-stamp', 'empno_field' => 'vc_empno', 'approval_field' => 'vc_recommend_submit_to_committee'],
                            ['id' => 8, 'name' => 'Council Approval', 'icon' => 'fa-clipboard-check', 'empno_field' => null],
                            ['id' => 1, 'name' => 'Approved', 'icon' => 'fa-check-circle', 'empno_field' => null]
                        ];
                        
                        // Get current status from merged study_leaves workflow fields
                        $currentStatusId = $processStatus['current_status_id'] ?? 4;
                        $isReturned = $currentStatusId == 2; // Returned status
                        $isEditing = $currentStatusId == 3;  // Editing status
                        $isRejected = $currentStatusId == 10; // Not approved status
                    @endphp
                    
                    @foreach($stages as $index => $stage)
                        @php
                            $isCompleted = false;
                            $isCurrent = false;
                            $isPending = false;
                            $approvedBy = null;
                            $approvalStatus = null;
                            
                            // Check if this stage has been completed by checking employee number
                            if (isset($stage['empno_field']) && !empty($processStatus[$stage['empno_field']])) {
                                $approvedBy = $processStatus[$stage['empno_field']];
                                
                                // Check approval status if field exists
                                if (isset($stage['approval_field']) && isset($processStatus[$stage['approval_field']])) {
                                    $approvalStatus = $processStatus[$stage['approval_field']];
                                }
                            }
                            
                            // Handle special statuses
                            if ($isReturned || $isEditing) {
                                // All stages are pending when returned or editing
                                $isCompleted = false;
                                $isCurrent = false;
                                $isPending = true;
                            } elseif ($isRejected) {
                                // Show which stage rejected the application
                                $isCompleted = false;
                                $isCurrent = false;
                                $isPending = true;
                            } elseif ($currentStatusId == 1) {
                                // Application is fully approved - all stages completed (green)
                                $isCompleted = true;
                                $isCurrent = false;
                                $isPending = false;
                            } else {
                                // Track completion based on employee number presence and status_id
                                if ($stage['id'] == 8) {
                                    // Council Approval stage
                                    if ($currentStatusId == 8) {
                                        // Status is 8 (VC Checked) - Council Approval is in progress
                                        $isCurrent = true;
                                    } else {
                                        // Not yet reached Council stage
                                        $isPending = true;
                                    }
                                } elseif ($stage['id'] == 1) {
                                    // Final Approved stage - always pending unless status_id = 1
                                    $isPending = true;
                                } elseif ($approvedBy) {
                                    // Stage is completed if employee number exists (for MA, Registrar, HOD, Dean, VC)
                                    $isCompleted = true;
                                } elseif ($stage['id'] == $currentStatusId) {
                                    // Current processing stage
                                    $isCurrent = true;
                                } else {
                                    // Future stages are pending
                                    $isPending = true;
                                }
                            }
                            
                            $stageClass = $isCompleted ? 'completed' : ($isCurrent ? 'current' : 'pending');
                        @endphp
                        
                        <div class="stage-item {{ $stageClass }}">
                            <div class="stage-content">
                                <div class="stage-icon">
                                    <i class="fas {{ $stage['icon'] }}"></i>
                                </div>
                                <div class="stage-name">{{ $stage['name'] }}</div>
                                <div class="stage-status">
                                    @if($isCompleted)
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        <span class="text-success small">Completed</span>
                                        @if($approvedBy)
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-user me-1"></i>{{ $approvedBy }}
                                            </div>
                                        @endif
                                    @elseif($isCurrent)
                                        <i class="fas fa-spinner fa-pulse text-warning me-1"></i>
                                        <span class="text-warning small">In Progress</span>
                                    @else
                                        <i class="far fa-circle text-muted me-1"></i>
                                        <span class="text-muted small">Pending</span>
                                    @endif
                                </div>
                            </div>
                            @if($index < count($stages) - 1)
                                <div class="stage-connector {{ $stageClass }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                {{-- Special Status Indicators --}}
                @if($isReturned)
                    <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center">
                        <i class="fas fa-undo-alt me-2"></i>
                        <div>
                            <strong>Application Returned</strong>
                            <p class="mb-0 small">This application has been returned for corrections. Current Status: {{ $processStatus['status_name'] ?? 'Returned' }}</p>
                        </div>
                    </div>
                @elseif($isEditing)
                    <div class="alert alert-info mt-3 mb-0 d-flex align-items-center">
                        <i class="fas fa-edit me-2"></i>
                        <div>
                            <strong>Editing Mode</strong>
                            <p class="mb-0 small">This application is currently being edited.</p>
                        </div>
                    </div>
                @elseif($isRejected)
                    <div class="alert alert-danger mt-3 mb-0 d-flex align-items-center">
                        <i class="fas fa-times-circle me-2"></i>
                        <div>
                            <strong>Application Not Approved</strong>
                            <p class="mb-0 small">This application was not approved. Status: {{ $processStatus['status_name'] ?? 'Not Approved' }}</p>
                        </div>
                    </div>
                @elseif($currentStatusId != 1)
                    <div class="alert alert-info mt-3 mb-0 d-flex align-items-center">
                        <i class="fas fa-info-circle me-2"></i>
                        <div>
                            <strong>Current Status</strong>
                            <p class="mb-0 small">{{ $processStatus['status_name'] ?? 'In Progress' }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .process-stages-container {
        margin-top: 1rem;
        max-width: 100%;
        overflow: hidden;
    }
    
    .stages-wrapper {
        position: relative;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 0.5rem 0;
        width: 100%;
    }
    
    .stages-timeline {
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        flex-wrap: nowrap;
        white-space: nowrap;
        gap: 0;
        min-width: max-content;
    }
    
    .process-stages-container .card {
        overflow: hidden;
    }
    
    .process-stages-container .card-body {
        overflow-x: auto;
        padding: 1.5rem !important;
    }
    
    .stage-item {
        display: inline-flex;
        flex-direction: row;
        align-items: center;
        flex-shrink: 0;
        position: relative;
    }
    
    .stage-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex-shrink: 0;
        width: 120px;
        position: relative;
        z-index: 2;
    }
    
    .stage-icon {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
        margin-bottom: 0.5rem;
    }
    
    .stage-item.completed .stage-icon {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 3px 6px rgba(40, 167, 69, 0.3);
    }
    
    .stage-item.current .stage-icon {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
        box-shadow: 0 3px 8px rgba(255, 193, 7, 0.5);
        animation: pulse-stage 2s infinite;
    }
    
    .stage-item.pending .stage-icon {
        background: #e9ecef;
        color: #6c757d;
        border: 2px solid #dee2e6;
    }
    
    @keyframes pulse-stage {
        0%, 100% {
            box-shadow: 0 3px 8px rgba(255, 193, 7, 0.5);
        }
        50% {
            box-shadow: 0 3px 12px rgba(255, 193, 7, 0.8);
        }
    }
    
    .stage-connector {
        height: 3px;
        width: 70px;
        position: relative;
        top: -27px;
        z-index: 1;
        flex-shrink: 0;
    }
    
    .stage-connector.completed {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    }
    
    .stage-connector.current {
        background: linear-gradient(90deg, #ffc107 0%, #e9ecef 100%);
    }
    
    .stage-connector.pending {
        background: #e9ecef;
    }
    
    .stage-name {
        font-weight: 600;
        font-size: 0.8rem;
        color: #2d2d2d;
        margin-bottom: 0.3rem;
        white-space: normal;
        word-wrap: break-word;
        line-height: 1.2;
    }
    
    .stage-status {
        font-size: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }
    
    .stage-item.pending .stage-name {
        color: #6c757d;
    }
    
    /* Responsive Design */
    @media (max-width: 992px) {
        .stage-content {
            width: 90px;
        }
        
        .stage-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }
        
        .stage-connector {
            width: 50px;
            top: -25px;
        }
        
        .stage-name {
            font-size: 0.7rem;
        }
    }
    
    @media (max-width: 768px) {
        .stage-content {
            width: 80px;
        }
        
        .stage-icon {
            width: 35px;
            height: 35px;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
        }
        
        .stage-connector {
            width: 40px;
            top: -22px;
            height: 2px;
        }
        
        .stage-name {
            font-size: 0.65rem;
        }
        
        .stage-status {
            font-size: 0.6rem;
        }
    }
    
    /* Alert styling */
    .alert {
        border-radius: 8px;
        border-left: 4px solid;
    }
    
    .alert-warning {
        border-left-color: #ffc107;
        background-color: #fff3cd;
    }
    
    .alert-info {
        border-left-color: #0dcaf0;
        background-color: #cff4fc;
    }
</style>
