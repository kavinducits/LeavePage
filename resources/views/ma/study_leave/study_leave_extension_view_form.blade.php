<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MA Dashboard - Extension Review</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>

<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        @include('ma.partials.navbar')

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="{{ route('ma.dashboard') }}" class="brand-link">
                <span class="brand-text font-weight-light">MA Dashboard</span>
            </a>
            <!-- Sidebar -->
            @php $pageName = 'Study Leave Extensions' @endphp
            @include('ma.partials.sidebar')
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <!-- Content Header -->
            @include('ma.partials.header')

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0 fw-bold">Extension Request Review</h2>
                            <p class="text-muted mb-0">Reference No: {{ $extension->reference_no }}</p>
                        </div>
                        <a href="{{ request()->get('from') == 'accepted' ? route('ma.studyleave.extensions.accepted') : route('ma.studyleave.extensions') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Extension Summary Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-primary text-white fw-semibold">
                            <i class="fas fa-calendar-plus me-2"></i> Extension Request Details
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Study Leave Reference Number</label>
                                        <div class="fw-bold fs-5 text-primary">{{ $extension->reference_no }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Employee</label>
                                        <div class="fw-bold">{{ $extension->name_with_initials }}</div>
                                        <div class="text-muted small">{{ $extension->empno }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Original End Date</label>
                                        <div class="fw-semibold text-danger">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ \Carbon\Carbon::parse($extension->old_end_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">New End Date</label>
                                        <div class="fw-semibold text-success">
                                            <i class="far fa-calendar-check me-1"></i>
                                            {{ \Carbon\Carbon::parse($extension->new_end_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label class="text-muted small mb-1">Extension Duration</label>
                                        <div class="fw-bold text-primary fs-5">
                                            <i class="fas fa-clock me-1"></i>{{ $durationDays }} days
                                            ({{ $durationMonths }} months)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-0">
                                        <label class="text-muted small mb-1">Reason for Extension</label>
                                        <div class="card bg-light">
                                            <div class="card-body">
                                                <p class="mb-0" style="white-space: pre-wrap;">
                                                    {{ $extension->reason_for_extension }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion for More Details -->
                    <div class="accordion mb-4" id="detailsAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingDetails">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseDetails" aria-expanded="false"
                                    aria-controls="collapseDetails">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <span class="small">More Details - Original Study Leave Application</span>
                                </button>
                            </h2>
                            <div id="collapseDetails" class="accordion-collapse collapse"
                                aria-labelledby="headingDetails">
                                <div class="accordion-body">
                                    <form method="POST" class="my-4">
                                        @csrf
                                        <!-- Include study leave forms with readonly -->
                                        @php
                                            $draft_study_leave = $extension; // Use extension data as draft_study_leave
                                            $readonly = true;
                                        @endphp

                                        @include('StudyLeave.basic_info_form')
                                        @include('StudyLeave.details_form')
                                        @include('StudyLeave.working_covering_persons_form')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Section -->
                    @if($extension->extension_status_id != 3 && $extension->extension_status_id != 1)
                    <div class="card">
                        <div class="card-header bg-dark text-white fw-semibold">
                            <i class="fas fa-clipboard-check me-2"></i>MA Review & Recommendation
                        </div>
                        <div class="card-body">
                            <!-- Recommendation Radio Buttons -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Extension is recommended
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ma_recommend" id="maRecommendYes"
                                        value="1" required>
                                    <label class="form-check-label" for="maRecommendYes">
                                        Yes
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="ma_recommend" id="maRecommendNo"
                                        value="0" required>
                                    <label class="form-check-label" for="maRecommendNo">
                                        No
                                    </label>
                                </div>
                                <div id="recommendError" class="form-text text-danger" style="display: none;">
                                    Please select a recommendation.
                                </div>
                            </div>

                            <!-- Not Recommend Reason (shown when No is selected) -->
                            <div class="mb-4" id="maNotRecommendReasonDiv" style="display: none;">
                                <label for="ma_not_recommend_reason" class="form-label fw-semibold">
                                    If not recommended, please give reasons
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" id="ma_not_recommend_reason" name="ma_not_recommend_reason" rows="4"
                                    placeholder="Please provide detailed reasons for not recommending this extension"></textarea>
                                <div id="notRecommendReasonError" class="form-text text-danger" style="display: none;">
                                    Please provide reasons for not recommending.
                                </div>
                            </div>

                            <!-- MA Remarks -->
                            <div class="mb-3">
                                <label for="actionRemark" class="form-label fw-semibold">Any other remarks</label>
                                <textarea class="form-control" id="actionRemark" name="remark" rows="4"
                                    placeholder="Add your comments or remarks about this extension request (optional)"></textarea>
                                <div id="remarkError" class="form-text text-danger" style="display: none;">
                                    Remarks are required when returning an application.
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-start">
                                <form id="returnForm"
                                    action="{{ route('ma.extension.return', $extension->extension_id) }}"
                                    method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" id="returnRemarkInput" name="remark" value="">
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-undo me-2"></i>Return to User
                                    </button>
                                </form>

                                <div class="text-right">
                                    @if (isset($departmentHead))
                                        <div class="card mb-2">
                                            <div class="card-body py-2">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <strong>Forward to,&nbsp;</strong>
                                                    <div>
                                                        <div class="fw-semibold">
                                                            {{ $departmentHead->head_title ?? 'Head' }}&nbsp;{{ $departmentHead->head_name ?? '' }}
                                                        </div>
                                                        <div class="text-muted small">
                                                            {{ $departmentHead->head_position ?? '' }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="card mb-2 border-warning">
                                            <div class="card-body py-2">
                                                <div class="text-danger text-end">
                                                    <strong>No active Department Head</strong>
                                                    <div class="text-muted small">Forwarding is disabled until a head
                                                        is active.</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    <form id="approveForm"
                                        action="{{ route('ma.extension.forward', $extension->extension_id) }}"
                                        method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" id="approveRemarkInput" name="remark" value="">
                                        <input type="hidden" id="approveRecommendInput" name="ma_recommend" value="">
                                        <input type="hidden" id="approveNotRecommendReasonInput" name="ma_not_recommend_reason" value="">
                                        <button type="submit" class="btn btn-success btn-lg w-100"
                                            {{ empty($departmentHead) ? 'disabled' : '' }}>
                                            <i class="fas fa-forward me-2"></i>Forward to HOD
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </section>
        </div>

        <!-- Footer -->
        @include('ma.partials.footer')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

    <script>
        // Show/hide not recommend reason field based on radio selection
        document.querySelectorAll('input[name="ma_recommend"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const notRecommendDiv = document.getElementById('maNotRecommendReasonDiv');
                if (this.value === '0') {
                    notRecommendDiv.style.display = 'block';
                } else {
                    notRecommendDiv.style.display = 'none';
                    document.getElementById('ma_not_recommend_reason').value = '';
                    clearNotRecommendReasonError();
                }
                clearRecommendError();
            });
        });

        // Form validation and submission handling - Forward
        document.getElementById('approveForm').addEventListener('submit', function(e) {
            const remarkValue = document.getElementById('actionRemark').value.trim();
            const recommendRadio = document.querySelector('input[name="ma_recommend"]:checked');

            clearAllErrors();

            // Validate recommendation is selected
            if (!recommendRadio) {
                e.preventDefault();
                showRecommendError();
                return false;
            }

            // If not recommended, validate reason is provided
            if (recommendRadio.value === '0') {
                const notRecommendReason = document.getElementById('ma_not_recommend_reason').value.trim();
                if (notRecommendReason === '') {
                    e.preventDefault();
                    showNotRecommendReasonError();
                    return false;
                }
                document.getElementById('approveNotRecommendReasonInput').value = notRecommendReason;
            }

            document.getElementById('approveRemarkInput').value = remarkValue;
            document.getElementById('approveRecommendInput').value = recommendRadio.value;

            if (!confirm('Are you sure you want to forward this extension request to HOD?')) {
                e.preventDefault();
                return false;
            }
        });

        // Return form
        document.getElementById('returnForm').addEventListener('submit', function(e) {
            const remarkValue = document.getElementById('actionRemark').value.trim();

            clearAllErrors();

            if (remarkValue === '') {
                e.preventDefault();
                showRemarkError();
                return false;
            }

            document.getElementById('returnRemarkInput').value = remarkValue;

            if (!confirm('Are you sure you want to return this extension request to the user?')) {
                e.preventDefault();
                return false;
            }
        });

        function showRemarkError() {
            document.getElementById('remarkError').style.display = 'block';
            document.getElementById('actionRemark').classList.add('is-invalid');
        }

        function clearRemarkError() {
            document.getElementById('remarkError').style.display = 'none';
            document.getElementById('actionRemark').classList.remove('is-invalid');
        }

        function showRecommendError() {
            document.getElementById('recommendError').style.display = 'block';
        }

        function clearRecommendError() {
            document.getElementById('recommendError').style.display = 'none';
        }

        function showNotRecommendReasonError() {
            document.getElementById('notRecommendReasonError').style.display = 'block';
            document.getElementById('ma_not_recommend_reason').classList.add('is-invalid');
        }

        function clearNotRecommendReasonError() {
            document.getElementById('notRecommendReasonError').style.display = 'none';
            document.getElementById('ma_not_recommend_reason').classList.remove('is-invalid');
        }

        function clearAllErrors() {
            clearRemarkError();
            clearRecommendError();
            clearNotRecommendReasonError();
        }

        document.getElementById('actionRemark').addEventListener('input', clearRemarkError);
        document.getElementById('ma_not_recommend_reason').addEventListener('input', clearNotRecommendReasonError);
    </script>
</body>

</html>
