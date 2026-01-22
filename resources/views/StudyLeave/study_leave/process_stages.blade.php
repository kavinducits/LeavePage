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
                        $stages = [
                            ['id' => 4, 'name' => 'MA Review', 'icon' => 'fa-user-tie'],
                            ['id' => 5, 'name' => 'HOD Review', 'icon' => 'fa-user-check'],
                            ['id' => 6, 'name' => 'Dean Review', 'icon' => 'fa-user-graduate'],
                            ['id' => 7, 'name' => 'VC Approval', 'icon' => 'fa-stamp'],
                            ['id' => 8, 'name' => 'Final Processing', 'icon' => 'fa-clipboard-check'],
                            ['id' => 1, 'name' => 'Approved', 'icon' => 'fa-check-circle']
                        ];
                        
                        $currentStatusId = $processStatus['current_status_id'] ?? 4;
                        $isReturned = $currentStatusId == 2;
                        $isEditing = $currentStatusId == 3;
                    @endphp
                    
                    @foreach($stages as $index => $stage)
                        @php
                            $isCompleted = false;
                            $isCurrent = false;
                            $isPending = false;
                            
                            if ($isReturned) {
                                // If returned, show all as inactive except returned status
                                $isCompleted = false;
                                $isCurrent = false;
                                $isPending = true;
                            } elseif ($isEditing) {
                                // If editing, show all as inactive
                                $isCompleted = false;
                                $isCurrent = false;
                                $isPending = true;
                            } else {
                                // Normal flow
                                if ($currentStatusId == 1) {
                                    // If approved, all stages are completed
                                    $isCompleted = true;
                                } elseif ($stage['id'] < $currentStatusId) {
                                    $isCompleted = true;
                                } elseif ($stage['id'] == $currentStatusId) {
                                    $isCurrent = true;
                                } else {
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
                            <p class="mb-0 small">This application has been returned for corrections.</p>
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
        padding: 1rem 0;
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
        width: 140px;
        position: relative;
        z-index: 2;
    }
    
    .stage-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
        margin-bottom: 0.75rem;
    }
    
    .stage-item.completed .stage-icon {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .stage-item.current .stage-icon {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.5);
        animation: pulse-stage 2s infinite;
    }
    
    .stage-item.pending .stage-icon {
        background: #e9ecef;
        color: #6c757d;
        border: 2px solid #dee2e6;
    }
    
    @keyframes pulse-stage {
        0%, 100% {
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.5);
        }
        50% {
            box-shadow: 0 4px 20px rgba(255, 193, 7, 0.8);
        }
    }
    
    .stage-connector {
        height: 4px;
        width: 80px;
        position: relative;
        top: -35px;
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
        font-size: 0.9rem;
        color: #2d2d2d;
        margin-bottom: 0.5rem;
        white-space: normal;
        word-wrap: break-word;
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
            width: 120px;
        }
        
        .stage-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        
        .stage-connector {
            width: 60px;
            top: -30px;
        }
        
        .stage-name {
            font-size: 0.85rem;
        }
    }
    
    @media (max-width: 768px) {
        .stage-content {
            width: 100px;
        }
        
        .stage-icon {
            width: 45px;
            height: 45px;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .stage-connector {
            width: 50px;
            top: -27px;
            height: 3px;
        }
        
        .stage-name {
            font-size: 0.8rem;
        }
        
        .stage-status {
            font-size: 0.7rem;
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

                
                {{-- Special Status Indicators --}}
                @if($isReturned)
                    <div class="alert alert-warning mt-3 mb-0 d-flex align-items-center">
                        <i class="fas fa-undo-alt me-2"></i>
                        <div>
                            <strong>Application Returned</strong>
                            <p class="mb-0 small">This application has been returned for corrections.</p>
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
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .process-stages-container {
        margin-top: 1rem;
    }
    
    .stages-wrapper {
        position: relative;
    }
    
        overflow-x: auto;
        padding: 1rem 0;
    }
    
    .stages-timeline {
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        justify-content: space-between;
        min-width: 100%;
        gap: 0;
    }
    
    .stage-item {
        display: flex;
        flex-direction: row;
        align-items: center;
        flex: 1;
        position: relative;
    }
    
    .stage-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        flex-shrink: 0;
        min-width: 120px;
        position: relative;
        z-index: 2;
    }
    
    .stage-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        position: relative;
        z-index: 2;
        transition: all 0.3s ease;
        margin-bottom: 0.75rem;
    }
    
    .stage-item.completed .stage-icon {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }
    
    .stage-item.current .stage-icon {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.5);
        animation: pulse-stage 2s infinite;
    }
    
    .stage-item.pending .stage-icon {
        background: #e9ecef;
        color: #6c757d;
        border: 2px solid #dee2e6;
    }
    
    @keyframes pulse-stage {
        0%, 100% {
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.5);
        }
        50% {
            box-shadow: 0 4px 20px rgba(255, 193, 7, 0.8);
        }
    }
    
    .stage-connector {
        height: 4px;
        flex: 1;
        position: relative;
        top: -35px;
        z-index: 1;
        margin: 0 -10px;
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
        font-size: 0.9rem;
        color: #2d2d2d;
        margin-bottom: 0.5rem;
        white-space: nowrap;
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
        .stages-timeline {
            min-width: 800px;
        }
        
        .stage-content {
            min-width: 100px;
        }
        
        .stage-icon {
            width: 50px;
            height: 50px;
            font-size: 1.25rem;
        }
        
        .stage-connector {
            top: -30px;
        }
        
        .stage-name {
            font-size: 0.85rem;
        }
    }
    
    @media (max-width: 768px) {
        .stages-timeline {
            min-width: 700px;
        }
        
        .stage-content {
            min-width: 90px;
        }
        
        .stage-icon {
            width: 45px;
            height: 45px;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .stage-connector {
            top: -27px;
            height: 3px;
        }
        
        .stage-name {
            font-size: 0.8rem;
        }
        
        .stage-status {
            font-size: 0.7
    
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
