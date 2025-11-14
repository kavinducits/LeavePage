@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold">Application Review (Dean)</h2>
            <p class="text-muted mb-0">Reference: {{ $application->reference_no }}</p>
        </div>
        <a href="{{ route('dean.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
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

<!-- study leave application details section -->

 <div class="content-wrapper">
            <!-- Content Header -->
            @include('ma.partials.header')

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0 fw-bold">Application Review</h2>
                            <p class="text-muted mb-0">Reference: {{ $application->id }}</p>
                        </div>
                        <a href="{{ route('ma.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
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



                    @include('hod.viewStudyLeaveApplication')


   
    <!-- HOD Remarks Section -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-comments me-2"></i>HOD Remarks
        </div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-7">Whether adequate staff available for the continuation of academic programs during the period of applicant's leave:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_adequate_staff === 1 ? 'bg-success' : ($application->hod_adequate_staff === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_adequate_staff === 1 ? 'Yes' : ($application->hod_adequate_staff === 0 ? 'No' : 'N/A') }}
                    </span>
                </dd>
                <dt class="col-sm-7">Whether satisfactory agreements can be made to cover applicant's teaching activities and other commitments:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_teaching_covered === 1 ? 'bg-success' : ($application->hod_teaching_covered === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_teaching_covered === 1 ? 'Yes' : ($application->hod_teaching_covered === 0 ? 'No' : 'N/A') }}
                    </span>
                </dd>
                <dt class="col-sm-7">Whether the applicant has completed all requirements regarding examinations-related work:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_exam_work_completed === 1 ? 'bg-success' : ($application->hod_exam_work_completed === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_exam_work_completed === 1 ? 'Yes' : ($application->hod_exam_work_completed === 0 ? 'No' : 'N/A') }}
                    </span>
                </dd>
                <dt class="col-sm-7">Recommendation:</dt>
                <dd class="col-sm-5">
                    <span class="badge {{ $application->hod_recommend === 1 ? 'bg-success' : ($application->hod_recommend === 0 ? 'bg-danger' : 'bg-secondary') }}">
                        {{ $application->hod_recommend === 1 ? 'Recommended' : ($application->hod_recommend === 0 ? 'Not Recommended' : 'N/A') }}
                    </span>
                </dd>
                @if($application->hod_recommend === 0)
                    <dt class="col-sm-7">Reason (if not recommended):</dt>
                    <dd class="col-sm-5">{{ $application->hod_not_recommend_reason }}</dd>
                @endif
                <dt class="col-sm-7">Other Remarks:</dt>
                <dd class="col-sm-5">{{ $application->hod_other_remarks }}</dd>
                <dt class="col-sm-7">Reviewed By:</dt>
                <dd class="col-sm-5">{{ $application->hod_reviewed_by }}</dd>
                <dt class="col-sm-7">Reviewed At:</dt>
                <dd class="col-sm-5">{{ $application->hod_reviewed_at }}</dd>
            </dl>
        </div>
    </div>
    <!-- Dean Review Section -->
    @if(empty($readonly) || !$readonly)
    <div class="card">
        <div class="card-header bg-dark text-white fw-semibold">
            <i class="fas fa-tasks me-2"></i>Dean Review Actions
        </div>
        <div class="card-body">
            <form id="recommendForm" action="{{ route('dean.recommend', $application->id) }}" method="POST" class="mb-3">
                @csrf
                <!-- Add your Dean review fields here -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Dean's Recommendation *</label><br>
                    <input type="radio" id="recommend_yes" name="dean_recommend" value="1" required> Recommend
                    <input type="radio" id="recommend_no" name="dean_recommend" value="0"> Not Recommend
                    <div id="dean_recommend_error" class="text-danger small d-none">Please select whether to recommend or not recommend.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" id="remarks-label">Remarks (optional)</label>
                    <textarea name="dean_remarks" id="dean_remarks" class="form-control"></textarea>
                </div>
                <button type="button" class="btn btn-success me-2" id="dean-submit-btn">
                    <i class="fas fa-check me-2"></i>Forward
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-info mt-4">
        <i class="fas fa-eye"></i> This application is in a different workflow stage. You have read-only access.
    </div>
    @endif

</div>
<script>
    // Dean Form Validation
    function validateDeanForm() {
        hideAllDeanErrors();
        let isValid = true;

        // Validate recommendation
        const recommend = document.querySelector('input[name="dean_recommend"]:checked');
        if (!recommend) {
            showDeanError('dean_recommend_error');
            isValid = false;
        }

        return isValid;
    }

    function showDeanError(errorId) {
        const errorElement = document.getElementById(errorId);
        if (errorElement) {
            errorElement.classList.remove('d-none');
        }
    }

    function hideAllDeanErrors() {
        const errorIds = ['dean_recommend_error'];

        errorIds.forEach(function(errorId) {
            const errorElement = document.getElementById(errorId);
            if (errorElement) {
                errorElement.classList.add('d-none');
            }
        });
    }

    function scrollToFirstDeanError() {
        const errorSelectors = [
            '#dean_recommend_error:not(.d-none)'
        ];

        for (let selector of errorSelectors) {
            const errorElement = document.querySelector(selector);
            if (errorElement) {
                // Add a small delay to ensure the error is visible
                setTimeout(function() {
                    errorElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });
                    // Add a highlight effect
                    errorElement.style.fontWeight = 'bold';
                    setTimeout(function() {
                        errorElement.style.fontWeight = '500';
                    }, 2000);
                }, 100);
                break;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Submit button event listener
        document.getElementById('dean-submit-btn').addEventListener('click', function(e) {
            e.preventDefault();
            if (validateDeanForm()) {
                document.getElementById('recommendForm').submit();
            } else {
                // Scroll to first error in document order
                scrollToFirstDeanError();
            }
        });

        // Hide errors when fields are filled
        document.querySelectorAll('input[name="dean_recommend"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                document.getElementById('dean_recommend_error').classList.add('d-none');
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const recommendYes = document.getElementById('recommend_yes');
        const recommendNo = document.getElementById('recommend_no');
        const remarks = document.getElementById('dean_remarks');
        const remarksLabel = document.getElementById('remarks-label');

        function toggleRemarks() {
            if (recommendNo.checked) {
                remarks.required = true;
                remarksLabel.innerHTML = "Remarks <span class='text-danger'>*</span>";
            } else {
                remarks.required = false;
                remarksLabel.textContent = "Remarks (optional)";
            }
        }
        recommendYes.addEventListener('change', toggleRemarks);
        recommendNo.addEventListener('change', toggleRemarks);
        toggleRemarks();
    });
</script>

<style>
.form-control[readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

.card-header {
    border-bottom: none;
}

.btn {
    font-weight: 500;
}

.badge {
    font-size: 0.875rem;
}

.pdf-frame-container {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
    background-color: #f8f9fa;
}

.pdf-frame {
    width: 100%;
    height: 300px;
    border: none;
    display: block;
}

.pdf-frame-container:hover {
    border-color: #adb5bd;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.travel-detail-entry {
    position: relative;
}

.travel-detail-entry .border-top {
    border-top: 1px solid #dee2e6 !important;
}

.travel-documents-container {
    max-height: 600px;
    overflow-y: auto;
}

.document-item {
    background-color: #f8f9fa;
    transition: all 0.3s ease;
}

.document-item:hover {
    background-color: #e9ecef;
    border-color: #007bff !important;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
}

.document-actions .btn {
    transition: all 0.2s ease;
}

.document-actions .btn:hover {
    transform: translateY(-1px);
}

.image-preview-container {
    text-align: center;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}

.document-preview-container {
    min-height: 120px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.travel-documents-container::-webkit-scrollbar {
    width: 6px;
}

.travel-documents-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.travel-documents-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.travel-documents-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Validation Error Styling */
.text-danger.small {
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 0.25rem;
    display: block;
    padding: 0.25rem 0;
    border-radius: 0.25rem;
}

.text-danger.small:not(.d-none) {
    animation: fadeIn 0.3s ease-in;
}

/* Scroll target highlighting */
.text-danger.small:target,
.text-danger.small:focus {
    background-color: rgba(220, 53, 69, 0.1);
    border-left: 3px solid #dc3545;
    padding-left: 0.5rem;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-5px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

@endsection