<?php

namespace App\Http\Controllers;

use App\Models\StudyLeaveProgressReports;
use App\Models\StudyLeaveProgressReportsApproval;
use App\Models\StudyLeave;
use App\Models\StudyLeaveExtension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
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
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->orderBy('study_leave_progress_reports.due_date', 'asc')
            ->select('study_leave_progress_reports.*', 'statuses.status as status_name')
            ->get();

        // Calculate if user can upload next report
        $canUploadNext = $this->canUploadNextReport($study_leave, $progress_reports);
        $nextDueDate = $this->calculateNextDueDate($study_leave, $progress_reports);

        return view('StudyLeave.study_leave_progress_reports.progress_report_form', compact('study_leave', 'progress_reports', 'canUploadNext', 'nextDueDate'));
    }

    /**
     * Check if user can upload next progress report
     * No time limitations - always allowed.
     */
    private function canUploadNextReport($study_leave, $progress_reports)
    {
        return true;
    }

    /**
     * Check if the study leave has any approved extensions
     */
    private function isExtended($study_leave_id)
    {   
        $extensions = StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $study_leave_id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions_approvals.status_id', 1) // Only approved extensions
            ->count();

        return $extensions > 0;
    }
    /**
     * Get the last extended end date for a study leave
     */

    private function getLastExtendedEndDate($study_leave_id)
    {
        $extensions = StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $study_leave_id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions_approvals.status_id', 1) // Only approved extensions
            ->orderBy('study_leave_extensions.new_end_date', 'desc')
            ->first();

        if ($extensions) {
            return Carbon::parse($extensions->new_end_date);
        }
        else {
            return null;
        }

        
    }

    /**
     * Calculate next due date for progress report
     * Returns today's date as default; user can override in the form.
     */
    private function calculateNextDueDate($study_leave, $progress_reports)
    {
        return Carbon::now();
    }

    /**
     * Upload progress report (creates new record)
     */
    public function uploadProgressReport(Request $request, $study_leave_id)
    {
        $request->validate([
            'progress_report' => 'required|file|mimetypes:application/pdf,application/x-pdf,application/acrobat,text/pdf,text/x-pdf|max:10240',
            'remark' => 'nullable|string|max:1000',
            'due_date' => 'required|date',
        ]);

        $studyLeave = StudyLeave::findOrFail($study_leave_id);

        // Check authorization
        if ($studyLeave->empno != session('empno')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Handle file upload
        if ($request->hasFile('progress_report') && $request->file('progress_report')->isValid()) {
            $file = $request->file('progress_report');
            $empno = $studyLeave->empno;
            $studyLeaveId = $studyLeave->id;
            $timestamp = time();
            
            // Generate filename: empno_studyleaveid_timestamp_progress_report.pdf
            $filename = $empno . '_' . $studyLeaveId . '_' . $timestamp . '_progress_report.pdf';
            
            // Store the file in storage/app/private/study_leave_documents/study_leave_progress_report
            $path = $file->storeAs('study_leave_documents/study_leave_progress_report', $filename, 'local');

            // Verify the file was stored
            if (!$path) {
                return redirect()->back()->with('error', 'Failed to save the progress report file.');
            }

            // Create new progress report record
            $progressReport = StudyLeaveProgressReports::create([
                'study_leave_id' => $studyLeaveId,
                'due_date' => $request->input('due_date'),
                'submitted_date' => Carbon::now()->format('Y-m-d'),
                'document_path' => $path,
                'remark' => $request->input('remark'),
                'status_id' => 4, // Status 4 as per requirement
            ]);

            // Create approval record and forward to MA (status_id = 4: Processing MA)
            StudyLeaveProgressReportsApproval::create([
                'study_leave_progress_report_id' => $progressReport->id,
                'approval_status_id' => 4, // Processing MA
            ]);

            return redirect()->route('StudyLeave.progressReports.show', $studyLeave->id)
                ->with('upload_success', true);
        }

        return redirect()->back()->with('error', 'Failed to upload progress report. Please ensure you selected a valid PDF file.');
    }

    /**
     * Delete a returned progress report
     */
    public function deleteProgressReport($id)
    {
        $progressReport = StudyLeaveProgressReports::findOrFail($id);
        $studyLeave = StudyLeave::findOrFail($progressReport->study_leave_id);

        // Check authorization
        if ($studyLeave->empno != session('empno')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Only allow deletion of returned reports (status_id = 3)
        if ($progressReport->status_id != 3) {
            return redirect()->back()->with('error', 'Only returned progress reports can be deleted.');
        }

        // Delete the file from storage
        if ($progressReport->document_path && Storage::disk('local')->exists($progressReport->document_path)) {
            Storage::disk('local')->delete($progressReport->document_path);
        }

        // Delete the database record
        $progressReport->delete();

        return redirect()->route('StudyLeave.progressReports.show', $studyLeave->id)
            ->with('success', 'Progress report deleted successfully. You can now upload a new report.');
    }

    /**
     * Serve progress report file
     */
    public function serveProgressReportFile($filename)
    {
       
        // Construct the file path - storage/app/study_leave_documents/study_leave_progress_report/filename
        $relativePath = 'study_leave_documents/study_leave_progress_report/' . $filename;
        $filePath = storage_path('app/private/' . $relativePath);

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

    /**
     * Remove progress report document (for returned reports)
     */
    public function removeProgressReportDocument($report_id)
    {
        // Get the progress report
        $progressReport = StudyLeaveProgressReports::findOrFail($report_id);
        
        // Get associated study leave
        $studyLeave = StudyLeave::find($progressReport->study_leave_id);
        
        // Authorization check
        if ($studyLeave->empno != session('empno')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access.'
            ], 403);
        }

        // Only allow removal of returned reports (status_id = 3)
        if ($progressReport->status_id != 3) {
            return response()->json([
                'success' => false,
                'message' => 'Only returned progress reports can have their documents removed.'
            ], 400);
        }

        // Delete the file from storage
        if ($progressReport->document_path && Storage::disk('local')->exists($progressReport->document_path)) {
            Storage::disk('local')->delete($progressReport->document_path);
        }

        // Clear the document path
        $progressReport->document_path = null;
        $progressReport->save();

        return response()->json([
            'success' => true,
            'message' => 'Document removed successfully. You can now upload a new document.'
        ]);
    }

    /**
     * Re-upload progress report document (for returned reports)
     */
    public function reuploadProgressReport(Request $request, $report_id)
    {
        // Validate request
        $request->validate([
            'document' => 'required|file|mimetypes:application/pdf,application/x-pdf,application/acrobat,text/pdf,text/x-pdf|max:10240',
            'notes' => 'nullable|string|max:1000'
        ]);

        // Get the progress report
        $progressReport = StudyLeaveProgressReports::findOrFail($report_id);
        
        // Get associated study leave
        $studyLeave = StudyLeave::find($progressReport->study_leave_id);
        
        // Authorization check
        if ($studyLeave->empno != session('empno')) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        // Only allow reupload of returned reports (status_id = 3)
        if ($progressReport->status_id != 3) {
            return redirect()->back()->with('error', 'Only returned progress reports can be re-uploaded.');
        }

        try {
            // Delete old document if exists
            if ($progressReport->document_path && Storage::disk('local')->exists($progressReport->document_path)) {
                Storage::disk('local')->delete($progressReport->document_path);
            }

            // Upload new document with validation
            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                $file = $request->file('document');
                $empno = $studyLeave->empno;
                $timestamp = time();
                
                // Generate filename: empno_studyleaveid_timestamp_progress_report.pdf
                $filename = $empno . '_' . $studyLeave->id . '_' . $timestamp . '_progress_report.pdf';
                
                // Store the file in storage/app/study_leave_documents/study_leave_progress_report
                $path = $file->storeAs('study_leave_documents/study_leave_progress_report', $filename, 'local');

                // Verify the file was stored
                if (!$path) {
                    return redirect()->back()->with('error', 'Failed to save the progress report file.');
                }

                // Prepare the new remark
                $timestamp_remark = now()->format('Y-m-d');
                $newRemark = "\n\n[Resubmitted - " . $timestamp_remark . "]\n";
                if ($request->notes) {
                    $newRemark .= $request->notes;
                } else {
                    $newRemark .= "Document re-uploaded after corrections.";
                }

                // Update progress report
                $progressReport->document_path = $path;
                $progressReport->status_id = 4; // Status 4 = Processing MA
                $progressReport->remark = DB::raw("CONCAT(COALESCE(remark, ''), '" . addslashes($newRemark) . "')");
                $progressReport->submitted_date = Carbon::now()->format('Y-m-d');
                $progressReport->updated_at = now();
                $progressReport->save();

                // Update approval record to forward back to MA
                StudyLeaveProgressReportsApproval::where('study_leave_progress_report_id', $progressReport->id)
                    ->update([
                        'approval_status_id' => 4, // Processing MA
                        'updated_at' => now()
                    ]);

                return redirect()->route('StudyLeave.show.studyLeave', $studyLeave->id)
                    ->with('success', 'Progress report re-uploaded successfully and forwarded to MA for review!');
            }

            return redirect()->back()->with('error', 'Failed to upload progress report. Please ensure you selected a valid PDF file.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload document: ' . $e->getMessage());
        }
    }
}
