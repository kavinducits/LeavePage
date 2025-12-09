<?php

namespace App\Http\Controllers;

use App\Models\StudyLeaveProgressReports;
use App\Models\StudyLeave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StudyLeaveProgressReportsController extends Controller
{
    /**
     * Display progress reports for a specific study leave
     */
    public function showProgressReports($study_leave_id)
    {
        // Get the study leave details
        $study_leave = StudyLeave::where('id', $study_leave_id)
            ->select('id', 'reference_no', 'degree_title', 'university_institute', 'study_leave_from', 'study_leave_to', 'empno')
            ->first();

        if (!$study_leave) {
            return redirect()->route('StudyLeave.create')->with('error', 'Study leave not found.');
        }

        // Check if user is authorized to view this study leave
        if ($study_leave->empno != session('empno')) {
            return redirect()->route('StudyLeave.create')->with('error', 'Unauthorized access.');
        }

        // Get existing progress reports
        $progress_reports = StudyLeaveProgressReports::where('study_leave_id', $study_leave->id)
            ->orderBy('due_date', 'asc')
            ->get();

        // Calculate if user can upload next report
        $canUploadNext = $this->canUploadNextReport($study_leave, $progress_reports);
        $nextDueDate = $this->calculateNextDueDate($study_leave, $progress_reports);

        return view('StudyLeave.study_leave_progress_reports.progress_report_form', compact('study_leave', 'progress_reports', 'canUploadNext', 'nextDueDate'));
    }

    /**
     * Check if user can upload next progress report
     */
    private function canUploadNextReport($study_leave, $progress_reports)
    {
        $today = Carbon::now();
        $leaveStart = Carbon::parse($study_leave->study_leave_from);
        $leaveEnd = Carbon::parse($study_leave->study_leave_to);

        // Can't upload if study leave hasn't started
        if ($today->lessThan($leaveStart)) {
            return false;
        }

        // Can't upload if study leave has ended
        if ($today->greaterThan($leaveEnd)) {
            return false;
        }

        // If no reports yet, can upload first one
        if ($progress_reports->count() == 0) {
            return true;
        }

        // Get the last submitted report
        $lastReport = $progress_reports->sortByDesc('due_date')->first();

        // Check if last report's due date has passed
        $lastDueDate = Carbon::parse($lastReport->due_date);
        
        return $today->greaterThan($lastDueDate);
    }

    /**
     * Calculate next due date for progress report
     */
    private function calculateNextDueDate($study_leave, $progress_reports)
    {
        $leaveStart = Carbon::parse($study_leave->study_leave_from);
        $leaveEnd = Carbon::parse($study_leave->study_leave_to);

        // If no reports yet, first due date is 6 months from start
        if ($progress_reports->count() == 0) {
            return $leaveStart->copy()->addMonths(6);
        }

        // Get the last report's due date and add 6 months
        $lastReport = $progress_reports->sortByDesc('due_date')->first();
        $nextDueDate = Carbon::parse($lastReport->due_date)->addMonths(6);

        // Don't set due date beyond leave end date
        if ($nextDueDate->greaterThan($leaveEnd)) {
            return null;
        }

        return $nextDueDate;
    }

    /**
     * Upload progress report (creates new record)
     */
    public function uploadProgressReport(Request $request, $study_leave_id)
    {
        $request->validate([
            'progress_report' => 'required|file|mimes:pdf|max:10240',
            'remark' => 'nullable|string|max:1000',
            'due_date' => 'required|date',
        ]);

        $studyLeave = StudyLeave::findOrFail($study_leave_id);

        // Check authorization
        if ($studyLeave->empno != session('empno')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Handle file upload
        if ($request->hasFile('progress_report')) {
            $file = $request->file('progress_report');
            $empno = $studyLeave->empno;
            $studyLeaveId = $studyLeave->id;
            $timestamp = time();
            
            // Generate filename: empno_studyleaveid_timestamp_progress_report.pdf
            $filename = $empno . '_' . $studyLeaveId . '_' . $timestamp . '_progress_report.pdf';
            
            // Store the file in storage/app/private/study_leave_progress_report
            $path = $file->storeAs('private/study_leave_progress_report', $filename);

            // Create new progress report record
            StudyLeaveProgressReports::create([
                'study_leave_id' => $studyLeaveId,
                'due_date' => $request->input('due_date'),
                'submitted_date' => Carbon::now()->format('Y-m-d'),
                'document_path' => $path,
                'remark' => $request->input('remark'),
                'status_id' => 3, // Status 3 as per requirement
            ]);

            return redirect()->route('StudyLeave.progressReports.show', $studyLeave->id)
                ->with('success', 'Progress report uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to upload progress report.');
    }

    /**
     * Serve progress report file
     */
    public function serveProgressReportFile($filename)
    {
        // Construct the file path - storage/app/private/study_leave_progress_report/filename
        $relativePath = 'private/study_leave_progress_report/' . $filename;
        $filePath = storage_path('app/' . $relativePath);

        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // Extract employee number from filename (format: empno_studyleaveid_reportid_progress_report.pdf)
        $parts = explode('_', $filename);
        $fileEmpNo = $parts[0] ?? null;

        // Authorization check
        $currentEmpNo = (string) session('empno');
        $isOwner = ($currentEmpNo === $fileEmpNo);

        if (!$isOwner) {
            abort(403, 'Unauthorized access to this file');
        }

        // Serve the file
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"'
        ]);
    }
}
