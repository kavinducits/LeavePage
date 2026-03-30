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
            0% {
                box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
            }

            50% {
                box-shadow: 0 6px 12px rgba(255, 193, 7, 0.5);
            }

            100% {
                box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
            }
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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
        
        /* Success Modal Styles */
        .success-checkmark {
            width: 80px;
            height: 80px;
            margin: 0 auto;
        }
        
        .success-checkmark .check-icon {
            width: 80px;
            height: 80px;
            position: relative;
            border-radius: 50%;
            box-sizing: content-box;
            border: 4px solid #4caf50;
        }
        
        .success-checkmark .check-icon::before {
            top: 3px;
            left: -2px;
            width: 30px;
            transform-origin: 100% 50%;
            border-radius: 100px 0 0 100px;
        }
        
        .success-checkmark .check-icon::after {
            top: 0;
            left: 30px;
            width: 60px;
            transform-origin: 0 50%;
            border-radius: 0 100px 100px 0;
            animation: rotate-circle 4.25s ease-in;
        }
        
        .success-checkmark .check-icon::before,
        .success-checkmark .check-icon::after {
            content: '';
            height: 100px;
            position: absolute;
            background: #fff;
            transform: rotate(-45deg);
        }
        
        .success-checkmark .check-icon .icon-line {
            height: 5px;
            background-color: #4caf50;
            display: block;
            border-radius: 2px;
            position: absolute;
            z-index: 10;
        }
        
        .success-checkmark .check-icon .icon-line.line-tip {
            top: 46px;
            left: 14px;
            width: 25px;
            transform: rotate(45deg);
            animation: icon-line-tip 0.75s;
        }
        
        .success-checkmark .check-icon .icon-line.line-long {
            top: 38px;
            right: 8px;
            width: 47px;
            transform: rotate(-45deg);
            animation: icon-line-long 0.75s;
        }
        
        .success-checkmark .check-icon .icon-circle {
            top: -4px;
            left: -4px;
            z-index: 10;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            position: absolute;
            box-sizing: content-box;
            border: 4px solid rgba(76, 175, 80, 0.5);
        }
        
        .success-checkmark .check-icon .icon-fix {
            top: 8px;
            width: 5px;
            left: 26px;
            z-index: 1;
            height: 85px;
            position: absolute;
            transform: rotate(-45deg);
            background-color: #fff;
        }
        
        @keyframes rotate-circle {
            0% {
                transform: rotate(-45deg);
            }
            5% {
                transform: rotate(-45deg);
            }
            12% {
                transform: rotate(-405deg);
            }
            100% {
                transform: rotate(-405deg);
            }
        }
        
        @keyframes icon-line-tip {
            0% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            54% {
                width: 0;
                left: 1px;
                top: 19px;
            }
            70% {
                width: 50px;
                left: -8px;
                top: 37px;
            }
            84% {
                width: 17px;
                left: 21px;
                top: 48px;
            }
            100% {
                width: 25px;
                left: 14px;
                top: 46px;
            }
        }
        
        @keyframes icon-line-long {
            0% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            65% {
                width: 0;
                right: 46px;
                top: 54px;
            }
            84% {
                width: 55px;
                right: 0;
                top: 35px;
            }
            100% {
                width: 47px;
                right: 8px;
                top: 38px;
            }
        }
    </style>
    <div class="container py-4">
        
        <!-- Success Modal for Application Submission -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            <div class="success-checkmark">
                                <div class="check-icon">
                                    <span class="icon-line line-tip"></span>
                                    <span class="icon-line line-long"></span>
                                    <div class="icon-circle"></div>
                                    <div class="icon-fix"></div>
                                </div>
                            </div>
                        </div>
                        <h3 class="fw-bold text-success mb-3">Submission Successful!</h3>
                        <p class="text-muted mb-2">Your study leave application has been submitted successfully.</p>
                        @if(session('reference_number'))
                            <p class="mb-3">
                                <strong>Reference Number:</strong> 
                                <span class="badge bg-primary fs-6">{{ session('reference_number') }}</span>
                            </p>
                        @endif
                        <p class="text-muted small mb-4">Your application is now under review and will be processed by the relevant authorities.</p>
                        <button type="button" class="btn btn-success px-5 py-2 rounded-pill fw-semibold" data-bs-dismiss="modal">
                            <i class="fas fa-check me-2"></i>OK
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Returned Application Alert Modal -->
        @php
            $hasReturnedApplication = false;
            $returnedApplication = null;
            if(isset($previousLeaves)) {
                foreach($previousLeaves as $leave) {
                    if($leave->status_id == 3) {
                        $hasReturnedApplication = true;
                        $returnedApplication = $leave;
                        break;
                    }
                }
            }
        @endphp

        @if($hasReturnedApplication && $returnedApplication)
        <div class="modal fade" id="returnedAppModal" tabindex="-1" aria-labelledby="returnedAppModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-exclamation-triangle text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="fw-bold text-warning mb-3">Application Returned</h3>
                        <p class="text-muted mb-2">Your study leave application has been returned for corrections.</p>
                        <p class="mb-3">
                            <strong>Reference Number:</strong> 
                            <span class="badge bg-primary fs-6">{{ $returnedApplication->reference_no }}</span>
                        </p>
                        @if(isset($returnedApplication->ma_remarks) && !empty($returnedApplication->ma_remarks))
                            <div class="alert alert-warning text-start mb-4">
                                <strong><i class="fas fa-comment-dots me-2"></i>Remarks:</strong>
                                <p class="mb-0 mt-2" style="white-space: pre-wrap;">{{ $returnedApplication->ma_remarks }}</p>
                            </div>
                        @endif
                        <p class="text-muted small mb-4">Please review the remarks and make necessary corrections before resubmitting your application.</p>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('StudyLeave.show.editeForm', $returnedApplication->id) }}" class="btn btn-warning px-4 py-2 rounded-pill fw-semibold">
                                <i class="fas fa-edit me-2"></i>Edit Application
                            </a>
                            <button type="button" class="btn btn-outline-secondary px-4 py-2 rounded-pill fw-semibold" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i>Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <h3 class="mb-4 fw-bold text-center text-maroon dashboard-header">
            <i class="fas fa-file-alt me-2 icon-gold"></i>New Study Leave Applications
        </h3>

        <!-- Notification: Returned Extensions and Progress Reports -->
        @if($hasReturnedExtensions || $hasReturnedProgressReports)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-circle me-3 mt-1" style="font-size: 1.2rem;"></i>
                    <div>
                        <strong>Action Required!</strong>
                        @if($hasReturnedExtensions && $hasReturnedProgressReports)
                            <p class="mb-2">You have returned extension requests and progress reports from MA. Please review and re-submit them.</p>
                        @elseif($hasReturnedExtensions)
                            <p class="mb-2">You have returned extension request(s) from MA. Please review the remarks and re-submit your extension request.</p>
                        @else
                            <p class="mb-2">You have returned progress report(s) from MA. Please review the remarks and re-upload your progress report.</p>
                        @endif
                        <p class="text-muted small mb-0">Your MA has provided feedback. Check the remarks and make necessary corrections before resubmitting.</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- New Application -->
        <div class="mb-4 text-center">
            @if ($hasActiveDraft)
                <!-- Show draft continuation options when user has an active draft -->
                @php
                    $activeDraft = $drafts;
                @endphp
                @if ($activeDraft)
                    <div class="text-center mb-3">
                        <p class="text-muted mb-2">
                            <i class="fas fa-info-circle me-1"></i>
                            You have an active draft. Choose an option below:
                        </p>
                    </div>
                    <div class="d-flex justify-content-center gap-4 draft-action-buttons flex-wrap">
                        <a href="{{ route('StudyLeave.continue.draft', $activeDraft->id) }}"
                            class="d-inline-block text-decoration-none draft-action-item">
                            <div class="draft-action-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="bi bi-pencil-square"></i>
                            </div>
                            <div class="text-center">
                                <span class="fw-semibold text-maroon">Continue Draft</span><br>
                                <small class="text-muted">{{ $activeDraft->reference_no ?? 'N/A' }}</small>
                            </div>
                        </a>

                        <form action="{{ route('StudyLeave.DeleteDraft', $activeDraft->id) }}" method="POST"
                            class="d-inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn p-0 border-0 bg-transparent draft-action-item"
                                onclick="return confirm('Are you sure you want to delete this draft and start a new application?')">
                                <div
                                    class="draft-action-icon d-flex align-items-center justify-content-center mx-auto mb-2">
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
                @php
                    // Check if the latest study leave is approved (status_id = 1)
                    $latestLeave = isset($previousLeaves) ? $previousLeaves->sortByDesc('id')->first() : null;
                    $latestLeaveApproved = $latestLeave && $latestLeave->status_id == 1;
                @endphp

                @if($latestLeaveApproved)
                    <!-- Two tiles: Progress Report & Extension Request -->
                    <div class="d-flex justify-content-center gap-5 flex-wrap">
                        <a href="{{ route('StudyLeave.progressReports.show', $latestLeave->id) }}" 
                           class="d-inline-block text-decoration-none tile-action-item">
                            <div class="tile-icon progress-tile-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="bi bi-file-earmark-bar-graph"></i>
                            </div>
                            <div class="text-center">
                                <span class="fw-semibold text-maroon">Upload Progress Report</span><br>
                                <small class="text-muted">{{ $latestLeave->reference_no ?? '' }}</small>
                            </div>
                        </a>

                        <a href="{{ route('StudyLeave.show.extensionForm', $latestLeave->id) }}" 
                           class="d-inline-block text-decoration-none tile-action-item">
                            <div class="tile-icon extension-tile-icon d-flex align-items-center justify-content-center mx-auto mb-2">
                                <i class="bi bi-calendar-plus"></i>
                            </div>
                            <div class="text-center">
                                <span class="fw-semibold text-maroon">Study Leave Extension</span><br>
                                <small class="text-muted">{{ $latestLeave->reference_no ?? '' }}</small>
                            </div>
                        </a>
                    </div>
                @else
                    <!-- Show Start a New Application tile -->
                    <div class="new-application-section">
                        @if (false)
                            { <!-- tempory dissable the academic year selection -->
                            <div class="mb-3">
                                <label for="academic-year" class="form-label fw-semibold text-maroon">
                                    <i class="fas fa-calendar-alt me-2 icon-gold"></i>Select Academic Year
                                </label>
                                <select class="form-select" id="academic-year" name="academic_year">
                                    <option value="">Choose Academic Year..</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                            }
                        @endif
                        <a href="#" class="d-inline-block text-decoration-none" id="new-application-button"
                            onclick="startNewApplication(event, {{ $hasActiveDraft ? 'true' : 'false' }}, {{ isset($isEnableStudyLeaveRequiste) && $isEnableStudyLeaveRequiste ? 'true' : 'false' }})">
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
                        <i class="fas fa-history me-2 icon-gold"></i>Your Study Leaves
                    </h5>
                </div>
                <div class="card-body pt-2 pb-0 px-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 12%;">Applied Date</th>
                                    <th class="text-center" style="width: 18%;">Reference No</th>
                                    <th class="text-center" style="width: 15%;">Leave Type</th>
                                    <th class="text-center" style="width: 12%;">Status</th>
                                    <th class="text-center" style="width: 43%;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @php
                                    $iteration = 0;
                                @endphp

                                @forelse(($previousLeaves ?? [])->sortByDesc('id') as $leave)
                                    @php

                                        $iteration = $iteration + 1;
                                    @endphp
                                    <tr class="hoverable-row">
                                        <td class="text-center">
                                            {{ optional($leave->created_at) ? \Carbon\Carbon::parse($leave->created_at)->format('Y-m-d') : '' }}
                                        </td>
                                        <td class="text-center fw-semibold">{{ $leave->reference_no ?? 'N/A' }}</td>
                                        <td class="text-center">
                                            @php
                                                $paymentType = $leave->leave_payment_type;
                                                if ($paymentType === null) {
                                                    $displayText = 'Select an option';
                                                    $badgeClass = 'bg-secondary';
                                                } elseif ($paymentType == 0) {
                                                    $displayText = 'Without Pay';
                                                    $badgeClass = 'bg-danger';
                                                } elseif ($paymentType == 1) {
                                                    $displayText = 'With Pay';
                                                    $badgeClass = 'bg-success';
                                                } elseif ($paymentType == 2) {
                                                    $displayText = 'Pending';
                                                    $badgeClass = 'bg-warning text-dark';
                                                } else {
                                                    $displayText = 'Study Leave';
                                                    $badgeClass = 'bg-info';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $displayText }}</span>
                                        </td>
                                        <td class="text-center">
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
                                                } elseif( $statusValue == 4) {
                                                    $status = 'Processing MA';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                                elseif( $statusValue == 5) {
                                                    $status = 'Processing HOD';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                                elseif( $statusValue == 6) {
                                                    $status = 'Processing Dean';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                                elseif( $statusValue == 7) {
                                                    $status = 'Processing VC';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                                 elseif( $statusValue == 8) {
                                                    $status = 'VC Checked';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                                 elseif( $statusValue == 9) {
                                                    $status = 'Processing Registrar';
                                                    $badgeClass = 'bg-secondary';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $status }}</span>

                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusValue = $leave->status_id ?? 0;
                                                $extensionStatus = $leave->extension_status_id ?? null;
                                                $leaveFrom = \Carbon\Carbon::parse($leave->study_leave_from);
                                                $leaveTo = \Carbon\Carbon::parse($leave->study_leave_to);
                                                $today = \Carbon\Carbon::parse($currentDate);

                                                // Calculate total duration to check 3-year limit
                                                $originalDuration = $leaveFrom->diffInDays($leaveTo);
                                                $totalExtensionDays = $leave->total_extension_days ?? 0;
                                                $totalDurationDays = $originalDuration + $totalExtensionDays;
                                                $threeYearsInDays = 3 * 365; // 1095 days
                                                $canExtendByDuration = $totalDurationDays < $threeYearsInDays;

                                                // Check if leave is in progress (between start and end date)
                                                $isInProgress =
                                                    $today->greaterThanOrEqualTo($leaveFrom) &&
                                                    $today->lessThanOrEqualTo($leaveTo);

                                                // Check if leave has ended
                                                $hasEnded = $today->greaterThan($leaveTo);
                                                $isBeforeThreeMonthsToEnd = $today->lessThan(
                                                    $leaveTo->copy()->subMonths(3),
                                                );
                                                // Check if leave ended within last 3 months
                                                $threeMonthsAfterEnd = $leaveTo->copy()->addMonths(3);
                                                // $isWithinThreeMonths = $hasEnded && $today->lessThanOrEqualTo($threeMonthsAfterEnd);
                                                $isWithinThreeMonths =
                                                    $today->greaterThan($leaveTo->copy()->subMonths(3)) &&
                                                    $today->lessThanOrEqualTo($leaveTo);

                                                // Check if beyond 3 months after end
                                                // $isBeyondThreeMonths = $hasEnded && $today->greaterThan($threeMonthsAfterEnd);
                                                //$isBeyondEnd = $hasEnded && $today->greaterThan($threeMonthsAfterEnd);

                                                // $isBeforeThreeMonthsToEnd =$today->lessThan($leaveTo->copy()->subMonths(3));

                                                // Determine which buttons to show
                                                $showView = false;
                                                $showEdit = false;
                                                $showExtend = false;
                                                $showProgress = false;
                                                $showReturnedExtend = false;

                                                // Check extension status first (takes priority)
                                                if ($extensionStatus !== null) {
                                                    if ($extensionStatus == 3) {
                                                        // Extension returned by MA - show View, Return Extend, Progress
                                                        $showView = true;
                                                        $showReturnedExtend = $canExtendByDuration; // Only if under 3 years
                                                        $showProgress = true;
                                                    } elseif (in_array($extensionStatus, [4, 5, 6, 7, 8])) {
                                                        // Extension in processing - show View, Progress only
                                                        $showView = true;
                                                        $showProgress = true;
                                                    } elseif (in_array($extensionStatus, [1, 2])) {
                                                        // Extension approved or pending - show View, Extend, Progress
                                                        $showView = true;
                                                        $showExtend = $canExtendByDuration; // Only if under 3 years
                                                        $showProgress = true;
                                                    }
                                                } else {
                                                    // No extension - use original study leave logic
                                                    if ($statusValue == 1) {
                                                        // Approved
                                                        if ($isInProgress && $isBeforeThreeMonthsToEnd) {
                                                            // During leave period: show Progress, View, Extend (if under 3 years)
                                                            $showProgress = true;
                                                            $showView = true;
                                                            $showExtend = $canExtendByDuration;
                                                        } elseif ($isWithinThreeMonths) {
                                                            // Within 3 months after end: show Progress, View
                                                            $showProgress = true;
                                                            $showView = true;
                                                        } elseif ($hasEnded) {
                                                            // After leave ended (beyond 3 months): show View only
                                                            $showView = true;
                                                        }
                                                    } elseif ($statusValue == 3) {
                                                        // Returned by MA
                                                        $showView = true;
                                                        $showEdit = true;
                                                    }
                                                }
                                            @endphp

                                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                @if ($showView)
                                                    <a href="{{ route('StudyLeave.show.studyLeave', $leave->id) }}"
                                                        class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @endif

                                                @if ($showEdit)
                                                    <a href="{{ route('StudyLeave.show.editeForm', $leave->id) }}"
                                                        class="btn btn-sm btn-warning" title="Edit Application">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                @endif


                                                @if (!$showView && !$showEdit && !$showExtend && !$showProgress && !$showReturnedExtend)
                                                     <a href="{{ route('StudyLeave.show.studyLeave', $leave->id) }}"
                                                        class="btn btn-sm btn-info" title="View Details">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                @endif
                                            </div>
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
    </div>

    <!-- Progress Report Upload Modals -->
    @if(isset($previousLeaves))
        @foreach($previousLeaves as $leave)
            @php
                $statusValue = $leave->status_id ?? 0;
                $extensionStatus = $leave->extension_status_id ?? null;
                $leaveFrom = \Carbon\Carbon::parse($leave->study_leave_from);
                $leaveTo = \Carbon\Carbon::parse($leave->study_leave_to);
                $today = \Carbon\Carbon::parse($currentDate);
                
                $isInProgress = $today->greaterThanOrEqualTo($leaveFrom) && $today->lessThanOrEqualTo($leaveTo);
                $hasEnded = $today->greaterThan($leaveTo);
                $isBeforeThreeMonthsToEnd = $today->lessThan($leaveTo->copy()->subMonths(3));
                $isWithinThreeMonths = $today->greaterThan($leaveTo->copy()->subMonths(3)) && $today->lessThanOrEqualTo($leaveTo);
                
                $showProgressModal = false;
                if ($extensionStatus !== null) {
                    if (in_array($extensionStatus, [1, 2, 3, 4, 5, 6, 7, 8])) {
                        $showProgressModal = true;
                    }
                } else {
                    if ($statusValue == 1 && ($isInProgress || $isWithinThreeMonths || $hasEnded)) {
                        $showProgressModal = true;
                    }
                }
                
                // Get progress report data from controller
                $progressData = isset($leaveProgressData[$leave->id]) ? $leaveProgressData[$leave->id] : null;
                $canUploadProgressReport = $progressData['canUpload'] ?? false;
                $hasPendingProgressReport = $progressData['hasPending'] ?? false;
                $nextProgressReportDueDate = $progressData['nextDueDate'] ?? null;
                
                // Calculate period for display
                $nextDueDate = $nextProgressReportDueDate ? \Carbon\Carbon::parse($nextProgressReportDueDate) : null;
                $periodStart = $nextDueDate ? $nextDueDate->copy()->subMonths(6) : null;
            @endphp
            
            @if($showProgressModal)
            <div class="modal fade" id="uploadProgressReportModal{{ $leave->id }}" tabindex="-1" aria-labelledby="uploadProgressReportModalLabel{{ $leave->id }}" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #800020; color: white;">
                            <h5 class="modal-title" id="uploadProgressReportModalLabel{{ $leave->id }}">
                                <i class="fas fa-upload me-2"></i>Upload Progress Report
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($hasPendingProgressReport)
                                <div class="alert alert-warning fw-semibold">
                                    <i class="fas fa-hourglass-half me-2"></i>
                                    <strong>Pending Progress Report:</strong> You already have a progress report pending approval. You cannot submit a new progress report until the current one is processed.
                                </div>
                            @endif

                            @if(!$canUploadProgressReport && !$hasPendingProgressReport)
                                <div class="alert alert-danger fw-semibold">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Upload Not Allowed:</strong> You cannot upload a progress report at this time. Please ensure:
                                    <ul class="mb-0 mt-2">
                                        <li>Your study leave period has started</li>
                                        <li>At least 6 months have passed since the last report</li>
                                        <li>You are within 3 months after study leave end date</li>
                                    </ul>
                                </div>
                            @endif

                            <form action="{{ route('StudyLeave.progressReport.upload', ['study_leave_id' => $leave->id]) }}" 
                                  method="POST" 
                                  enctype="multipart/form-data" 
                                  class="needs-validation" 
                                  novalidate>
                                @csrf

                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Study Leave Reference:</strong> {{ $leave->reference_no ?? 'N/A' }}
                                </div>

                                @if($nextDueDate)
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <strong>Report Period:</strong> 
                                        {{ $periodStart->format('d M Y') }} - {{ $nextDueDate->format('d M Y') }}
                                        <br>
                                        <strong>Due Date:</strong> {{ $nextDueDate->format('d M Y') }}
                                    </div>
                                @endif

                                <input type="hidden" name="due_date" value="{{ $nextProgressReportDueDate ?? '' }}">

                                <div class="card mb-3">
                                    <div class="card-header bg-light">
                                        <strong><i class="fas fa-file-upload me-2"></i>Progress Report Details</strong>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Remarks -->
                                            <div class="col-12">
                                                <label for="remark{{ $leave->id }}" class="form-label fw-semibold">
                                                    Remarks/Notes
                                                </label>
                                                <textarea name="remark" 
                                                          id="remark{{ $leave->id }}" 
                                                          class="form-control" 
                                                          rows="4" 
                                                          placeholder="Enter any remarks or notes about this progress report"
                                                          {{ (!$canUploadProgressReport || $hasPendingProgressReport) ? 'readonly' : '' }}></textarea>
                                            </div>

                                            <!-- Document Upload -->
                                            <div class="col-12">
                                                <label for="progress_report{{ $leave->id }}" class="form-label fw-semibold">
                                                    Upload Progress Report Document (PDF Only) <span class="text-danger">*</span>
                                                </label>
                                                <input type="file" 
                                                       name="progress_report" 
                                                       id="progress_report{{ $leave->id }}" 
                                                       class="form-control" 
                                                       accept=".pdf,application/pdf" 
                                                       required
                                                       {{ (!$canUploadProgressReport || $hasPendingProgressReport) ? 'disabled' : '' }}>
                                                <small class="text-muted">Accepted format: PDF only (Max size: 10MB)</small>
                                                <div class="invalid-feedback">
                                                    Please upload a PDF document.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('StudyLeave.progressReports.show', $leave->id) }}" class="btn btn-info">
                                        <i class="fas fa-history me-2"></i>View All Reports
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </button>
                                        @if($hasPendingProgressReport)
                                            <button type="button" class="btn btn-warning" disabled>
                                                <i class="fas fa-hourglass-half me-2"></i>Pending Report Exists
                                            </button>
                                        @elseif($canUploadProgressReport)
                                            <button type="submit" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                                                <i class="fas fa-paper-plane me-2"></i>Upload Progress Report
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-secondary" disabled>
                                                <i class="fas fa-ban me-2"></i>Upload Not Allowed
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    <!-- Extension Request Modals -->
    @if(isset($previousLeaves))
        @foreach($previousLeaves as $leave)
            @php
                $statusValue = $leave->status_id ?? 0;
                $extensionStatus = $leave->extension_status_id ?? null;
                $leaveFrom = \Carbon\Carbon::parse($leave->study_leave_from);
                $leaveTo = \Carbon\Carbon::parse($leave->study_leave_to);
                $today = \Carbon\Carbon::parse($currentDate);
                
                // Calculate extension data
                $originalDuration = $leaveFrom->diffInDays($leaveTo);
                $totalExtensionDays = $leave->total_extension_days ?? 0;
                $totalDurationDays = $originalDuration + $totalExtensionDays;
                $threeYearsInDays = 3 * 365;
                $canExtendByDuration = $totalDurationDays < $threeYearsInDays;
                $remainingDays = $threeYearsInDays - $totalDurationDays;
                
                // Get the last approved extension to determine start date
                $lastApprovedExtension = \App\Models\StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $leave->id)
                    ->where('study_leave_extensions.status_id', 1)
                    ->orderBy('study_leave_extensions.created_at', 'desc')
                    ->select('study_leave_extensions.*')
                    ->first();
                
                $extensionStartDate = $lastApprovedExtension 
                    ? $lastApprovedExtension->new_end_date 
                    : $leave->study_leave_to;
                
                // Check for pending extension
                $hasPendingExtension = \App\Models\StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $leave->id)
                    ->whereNotIn('study_leave_extensions.status_id', [1, 2])
                    ->exists();
                
                // Check for returned extension
                $returnedExtension = \App\Models\StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $leave->id)
                    ->where('study_leave_extensions.status_id', 3)
                    ->select('study_leave_extensions.*')
                    ->first();
                
                $showAddExtensionModal = $statusValue == 1 && $canExtendByDuration && !$hasPendingExtension;
                $showResubmitExtensionModal = $returnedExtension !== null;
            @endphp
            
            <!-- Add New Extension Modal -->
            @if($showAddExtensionModal)
            <div class="modal fade" id="addExtensionModal{{ $leave->id }}" tabindex="-1" aria-labelledby="addExtensionModalLabel{{ $leave->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #800020; color: white;">
                            <h5 class="modal-title" id="addExtensionModalLabel{{ $leave->id }}">
                                <i class="fas fa-calendar-plus me-2"></i>Request for Study Leave Extension
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            @if($canExtendByDuration && $remainingDays < 365)
                                <div class="alert alert-warning fw-semibold">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Notice:</strong> You have {{ round($remainingDays / 30, 1) }} months ({{ $remainingDays }} days) remaining before reaching the 3-year limit.
                                    <br><small>Current total: {{ round($totalDurationDays / 365, 2) }} years | Maximum: 3 years</small>
                                </div>
                            @endif

                            <form action="{{ route('StudyLeave.store.extension', ['id' => $leave->id]) }}" 
                                  method="POST" 
                                  enctype="multipart/form-data" 
                                  class="needs-validation" 
                                  novalidate>
                                @csrf

                                <div class="card mb-3">
                                    <div class="card-header" style="background-color: #800020; color: white;">
                                        <i class="fas fa-user me-2"></i>Details of the Study Leave Extension - Reference No: {{ $leave->reference_no ?? 'N/A' }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Period of Study Leave Requested -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Period of Study Leave Requested <span class="text-danger">*</span></label>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">From</label>
                                                        <input type="date" 
                                                               name="old_end_date" 
                                                               class="form-control" 
                                                               value="{{ $extensionStartDate }}" 
                                                               readonly 
                                                               required>
                                                        <div class="invalid-feedback">
                                                            Please select a valid start date.
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">To <span class="text-danger">*</span></label>
                                                        <input type="date" 
                                                               name="new_end_date" 
                                                               class="form-control" 
                                                               value=""
                                                               min="{{ $extensionStartDate }}" 
                                                               required>
                                                        <div class="invalid-feedback">
                                                            Please select a valid end date (must be after start date).
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reason for Extension -->
                                            <div class="col-12">
                                                <label for="reason_for_extension{{ $leave->id }}" class="form-label fw-semibold">
                                                    Reason for Extension <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="reason_for_extension" 
                                                          id="reason_for_extension{{ $leave->id }}" 
                                                          class="form-control" 
                                                          rows="4" 
                                                          placeholder="Enter reason for requesting extension" 
                                                          required></textarea>
                                                <div class="invalid-feedback">
                                                    Please provide a reason for the extension.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('StudyLeave.show.studyLeave', $leave->id) }}" class="btn btn-info">
                                        <i class="fas fa-eye me-2"></i>View Study Leave Details
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                                            <i class="fas fa-paper-plane me-2"></i>Submit Extension Request
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Resubmit Extension Modal -->
            @if($showResubmitExtensionModal)
            <div class="modal fade" id="resubmitExtensionModal{{ $leave->id }}" tabindex="-1" aria-labelledby="resubmitExtensionModalLabel{{ $leave->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #800020; color: white;">
                            <h5 class="modal-title" id="resubmitExtensionModalLabel{{ $leave->id }}">
                                <i class="fas fa-redo me-2"></i>Resubmit Study Leave Extension
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info fw-semibold mb-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> This extension request was returned for revision. Please update the details below and resubmit.
                            </div>

                            @if($returnedExtension->ma_remarks)
                                <div class="alert alert-warning mb-3">
                                    <strong><i class="fas fa-comment-dots me-2"></i>Remarks:</strong>
                                    <p class="mb-0 mt-2" style="white-space: pre-wrap;">{{ $returnedExtension->ma_remarks }}</p>
                                </div>
                            @endif

                            <form action="{{ route('StudyLeave.update.extension', ['id' => $returnedExtension->id]) }}" 
                                  method="POST" 
                                  enctype="multipart/form-data" 
                                  class="needs-validation" 
                                  novalidate>
                                @csrf
                                @method('PUT')

                                <div class="card mb-3">
                                    <div class="card-header" style="background-color: #800020; color: white;">
                                        <i class="fas fa-user me-2"></i>Extension Details - Reference No: {{ $leave->reference_no ?? 'N/A' }}
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Period of Study Leave Requested -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">Period of Study Leave Extension <span class="text-danger">*</span></label>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">From</label>
                                                        <input type="date" 
                                                               name="old_end_date" 
                                                               class="form-control" 
                                                               value="{{ $returnedExtension->old_end_date }}" 
                                                               readonly 
                                                               required>
                                                        <div class="invalid-feedback">
                                                            Please select a valid start date.
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">To <span class="text-danger">*</span></label>
                                                        <input type="date" 
                                                               name="new_end_date" 
                                                               class="form-control" 
                                                               value="{{ $returnedExtension->new_end_date }}"
                                                               min="{{ $returnedExtension->old_end_date }}" 
                                                               required>
                                                        <div class="invalid-feedback">
                                                            Please select a valid end date (must be after start date).
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Reason for Extension -->
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">
                                                    Extension Payment Type <span class="text-danger">*</span>
                                                </label>
                                                <select name="extension_payment_type"
                                                        class="form-select"
                                                        required>
                                                    <option value="" disabled>Select payment type</option>
                                                    <option value="1" {{ (string) old('extension_payment_type', $returnedExtension->extension_payment_type) === '1' ? 'selected' : '' }}>With Pay</option>
                                                    <option value="0" {{ (string) old('extension_payment_type', $returnedExtension->extension_payment_type) === '0' ? 'selected' : '' }}>Without Pay</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please select extension payment type.
                                                </div>
                                            </div>

                                            <!-- Reason for Extension -->
                                            <div class="col-12">
                                                <label class="form-label fw-semibold">
                                                    Reason for Extension <span class="text-danger">*</span>
                                                </label>
                                                <textarea name="reason_for_extension" 
                                                          class="form-control" 
                                                          rows="4" 
                                                          placeholder="Enter reason for requesting extension" 
                                                          required>{{ $returnedExtension->reason_for_extension }}</textarea>
                                                <div class="invalid-feedback">
                                                    Please provide a reason for the extension.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('StudyLeave.show.studyLeave', $leave->id) }}" class="btn btn-info">
                                        <i class="fas fa-eye me-2"></i>View Study Leave Details
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-2"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary" style="background-color: #800020; border-color: #800020;">
                                            <i class="fas fa-paper-plane me-2"></i>Resubmit Extension Request
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    <!-- JavaScript for Success Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @php
                $shouldShowSuccessModal = session('show_success_modal') || (session('success') && str_contains(session('success'), 'submitted successfully'));
            @endphp

            @if($shouldShowSuccessModal)
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
            @endif
            
            // Show returned application modal if user has a returned application
            @if($hasReturnedApplication && $returnedApplication && !$shouldShowSuccessModal)
                const returnedAppModal = new bootstrap.Modal(document.getElementById('returnedAppModal'));
                returnedAppModal.show();
            @endif
        });
    </script>
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
        vertical-align: middle;
    }

    .table td {
        color: #3a3a3a;
        vertical-align: middle;
    }

    .table-responsive {
        overflow-x: auto;
    }

    @media (max-width: 768px) {
        .table {
            font-size: 0.875rem;
        }

        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }

    .new-app-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #f0f6ff;
        border: 2px solid #0d6efd;
        font-size: 2.5rem;
        color: #0d6efd;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.08);
    }

    .tile-action-item {
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .tile-action-item:hover {
        transform: translateY(-4px);
        text-decoration: none;
    }

    .tile-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        font-size: 2.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .progress-tile-icon {
        background: #e8f5e9;
        border: 2px solid #4caf50;
        color: #2e7d32;
    }

    .tile-action-item:hover .progress-tile-icon {
        background: #4caf50;
        color: white;
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
    }

    .extension-tile-icon {
        background: #e3f2fd;
        border: 2px solid #2196f3;
        color: #1565c0;
    }

    .tile-action-item:hover .extension-tile-icon {
        background: #2196f3;
        color: white;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.4);
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


    function startNewApplication(event, hasActiveDraft = false, isEnableStudyLeaveRequiste = true) {
        event.preventDefault();

        // Check if there's an active draft
        if (hasActiveDraft) {
            Swal.fire({
                icon: 'error',
                title: 'Active Draft Exists',
                text: 'You already have an active draft. Please continue with the existing draft or delete it before starting a new application.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
            return false;
        }

        // Check if user is allowed to start a new study leave application
        if (!isEnableStudyLeaveRequiste) {
            Swal.fire({
                icon: 'warning',
                title: 'Application Not Allowed',
                text: 'You are not allowed to start a new study leave application since your previous study leave request application is processing.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#ffc107',
                showClass: {
                    popup: 'animate__animated animate__fadeInDown'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp'
                }
            });
            return false;
        }

        /*
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
            */
        console.log("Starting new application without academic year selection.");

        // Create and submit a POST form to the StudyLeave.store route
        const form = document.createElement('form');
        form.method = 'POST';
        form.setAttribute('action', '{{ route('StudyLeave.store') }}');

        // CSRF token
        const tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = '{{ csrf_token() }}';
        form.appendChild(tokenInput);

        // academic_year input
        /*
        const yearInput = document.createElement('input');
        yearInput.type = 'hidden';
        yearInput.name = 'academic_year';
        yearInput.value = academicYear;
        form.appendChild(yearInput);
        */

        document.body.appendChild(form);
        form.submit();
    }
</script>
