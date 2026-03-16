<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeanController extends Controller
{
    // Hardcoded Dean employee number - change this to switch to a different Dean
    private const DEAN_EMP_NO = 5045; // Dean for faculty 1 (has leave applications)
    
    // Hardcoded VC employee number - same as VCController
    private const VC_EMP_NO = 1001; // Example VC emp_no

    /**
     * Get faculty IDs for the current Dean
     * Returns array of faculty IDs or shows error page if Dean not found
     */
    private function getDeanFaculties()
    {
        $deanEmpNo = self::DEAN_EMP_NO;

        // Check if this employee is a valid Dean in faculty_deans table
        $faculties = DB::table('faculty_deans')
            ->where('emp_no', $deanEmpNo)
            ->where('active_status', 1) // Only active appointments
            ->whereRaw('(end_date IS NULL OR end_date >= CURDATE())') // Current or future end date
            ->pluck('faculty_id')
            ->toArray();

        if (empty($faculties)) {
            // Dean not found in faculty_deans table - show error
            abort(403, 'Access denied. Employee ' . $deanEmpNo . ' is not authorized as a Dean.');
        }

        return $faculties;
    }

    public function index()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        // Get all applications for Dean review (status_id = 6) from assigned faculties
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 6) // Processing Dean
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status'
            )
            ->get();

        // Get all study leave applications for Dean review (status_id = 6) from assigned faculties
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_approvals.status_id', 6) // Processing Dean
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('study_leaves.created_at')
            ->select(
                'study_leaves.id',
                'study_leaves.reference_no',
                'study_leaves.empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leaves.created_at as applied_date',
                'statuses.status'
            )
            ->get();

        // Get study leave extension applications for Dean review from assigned faculties
        $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions_approvals.status_id', 6) // Processing Dean (status_id = 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('study_leave_extensions.created_at')
            ->select(
                'study_leave_extensions.id as extension_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_extensions.old_end_date',
                'study_leave_extensions.new_end_date',
                'study_leave_extensions.reason_for_extension',
                'study_leave_extensions.created_at as extension_applied_date',
                'statuses.status as status'
            )
            ->get();

        return view('dean.index', compact('applications', 'studyLeaveApplications', 'extensionApplications'));
    }
     public function leave_index()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        // Get all applications for Dean review (status_id = 6) from assigned faculties
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 6) // Processing Dean
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status'
            )
            ->get();

       

        return view('dean.leave_dashboard', compact('applications'));
    }
     public function study_leave_index()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();


        // Get all study leave applications for Dean review (status_id = 6) from assigned faculties
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_approvals.status_id', 6) // Processing Dean
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('study_leaves.created_at')
            ->select(
                'study_leaves.id',
                'study_leaves.reference_no',
                'study_leaves.empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leaves.created_at as applied_date',
                'statuses.status'
            )
            ->get();

        // Get study leave extension applications for Dean review from assigned faculties
        $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions_approvals.status_id', 6) // Processing Dean (status_id = 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('study_leave_extensions.created_at')
            ->select(
                'study_leave_extensions.id as extension_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_extensions.old_end_date',
                'study_leave_extensions.new_end_date',
                'study_leave_extensions.reason_for_extension',
                'study_leave_extensions.created_at as extension_applied_date',
                'statuses.status as status'
            )
            ->get();

        return view('dean.study_leave_dashboard', compact('studyLeaveApplications', 'extensionApplications'));
    }
    public function study_leave_index_accepted()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        // Get all study leave applications already forwarded by Dean (dean_empno set, past status 6)
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
            ->whereNotNull('study_leave_approvals.dean_empno') // Dean has already acted
            ->where('study_leave_approvals.status_id', '!=', 6) // No longer at Dean stage
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('study_leave_approvals.dean_reviewed_date')
            ->select(
                'study_leaves.id',
                'study_leaves.reference_no',
                'study_leaves.empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leaves.created_at as applied_date',
                'statuses.status'
            )
            ->get();

        return view('dean.study_leave_dashboard_accepted', compact('studyLeaveApplications'));
    }

    public function study_leave_extensions_accepted()
    {
        $facultyIds = $this->getDeanFaculties();

        $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->whereNotNull('study_leave_extensions_approvals.dean_empno')
            ->where('study_leave_extensions_approvals.status_id', '!=', 6)
            ->whereIn('employees.faculty_id', $facultyIds)
            ->orderByDesc('study_leave_extensions_approvals.dean_reviewed_date')
            ->select(
                'study_leave_extensions.id as extension_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_extensions.old_end_date',
                'study_leave_extensions.new_end_date',
                'study_leave_extensions.reason_for_extension',
                'study_leave_extensions.created_at as extension_applied_date',
                'statuses.status as status'
            )
            ->get();

        return view('dean.study_leave_extenstions_dashboard_accepted', compact('extensionApplications'));
    }

     public function study_leave_extensions()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();



        // Get study leave extension applications for Dean review from assigned faculties
        $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions_approvals.status_id', 6) // Processing Dean (status_id = 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('study_leave_extensions.created_at')
            ->select(
                'study_leave_extensions.id as extension_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_extensions.old_end_date',
                'study_leave_extensions.new_end_date',
                'study_leave_extensions.reason_for_extension',
                'study_leave_extensions.created_at as extension_applied_date',
                'statuses.status as status'
            )
            ->get();

        return view('dean.study_leave_extenstions_dashboard', compact('extensionApplications'));
    }

    /**
     * Get study leave progress reports for Dean review
     */
    public function study_leave_progress_reports()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        // Get study leave progress report applications for Dean review from assigned faculties
        $progressReportApplications = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_progress_reports_approval.approval_status_id', 6) // Processing Dean (status_id = 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->orderByDesc('study_leave_progress_reports.submitted_date')
            ->select(
                'study_leave_progress_reports.id as progress_report_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_progress_reports.submitted_date',
                'statuses.status as status'
            )
            ->get();

        return view('dean.study_leave_progress_report_dashboard', compact('progressReportApplications'));
    }

    public function study_leave_progress_reports_accepted()
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        $progressReportApplications = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->whereNotNull('study_leave_progress_reports_approval.dean_empno')
            ->where('study_leave_progress_reports_approval.approval_status_id', '!=', 6)
            ->whereIn('employees.faculty_id', $facultyIds)
            ->orderByDesc('study_leave_progress_reports_approval.dean_reviewed_date')
            ->select(
                'study_leave_progress_reports.id as progress_report_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_progress_reports.submitted_date',
                'statuses.status as status'
            )
            ->get();

        return view('dean.study_leave_progress_report_dashboard_accepted', compact('progressReportApplications'));
    }

    public function show($id)
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->select(
                'leave_details.*',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile',
                'employees.nic',
                'leave_types.name as leave_type_name',
                'statuses.status'
            )
            ->first();

        if (!$application) {
            return redirect()->route('dean.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // Merge other leave details into application object
        if ($otherLeaveDetails) {
            $application->leave_type_id = $otherLeaveDetails->leave_type_id;
            $application->from_date = $otherLeaveDetails->from_date;
            $application->end_date = $otherLeaveDetails->end_date;
            $application->duration = $otherLeaveDetails->duration;
            $application->leave_document = $otherLeaveDetails->leave_document;
            $application->consent_letter = $otherLeaveDetails->consent_letter;
        }

        // Decode JSON fields to arrays for multiple files
        $application->leave_documents = $application->leave_document
            ? json_decode($application->leave_document, true)
            : [];
        $application->consent_letters = $application->consent_letter
            ? json_decode($application->consent_letter, true)
            : [];

        // Get travel details for this application
        $travelDetails = DB::table('leave_request_details')
            ->where('reference_no', $application->reference_no)
            ->get()
            ->map(function ($detail) {
                // Decode documents JSON
                $detail->documents = $detail->documents ? json_decode($detail->documents, true) : [];

                // Ensure documents is always an array
                if (!is_array($detail->documents)) {
                    $detail->documents = [];
                }

                return $detail;
            });

        return view('dean.show', compact('application', 'travelDetails'));
    }

    public function recommend(Request $request, $id)
    {
        $request->validate([
            'dean_recommend' => 'required|boolean',
            'dean_remarks' => 'required_if:dean_recommend,0',
        ]);

        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 6)
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('dean.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table for validation/processing
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // Always forward to VC (status_id = 7)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'dean_recommend' => $request->dean_recommend,
                'dean_remarks' => $request->dean_remarks,
                'dean_reviewed_by' => 'Dean', // Or get from auth if available
                'dean_reviewed_at' => now(),
                'dean_name' => 'Dean',
                'dean_designation' => 'Dean',
                'dean_empno' => self::DEAN_EMP_NO, // Record which Dean processed this
                'status_id' => 7, // Processing VC
                'updated_at' => now(),
            ]);

        $msg = $request->dean_recommend ? 'Application forwarded to VC with recommendation.' : 'Application forwarded to VC without recommendation.';
        return redirect()->route('dean.index')->with('success', $msg);
    }

    public function showStudyLeaveApplication(Request $request, $id)
    {
        $from = $request->get('from');
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();
       
        // Fetch study leave application with faculty filtering
        $draft_study_leave = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->leftJoin('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
            ->leftJoin('employees as teaching_nominee_t', 'teaching_nominee_t.employee_no', '=', 'study_leaves.nominee_teaching_empno')
            ->leftJoin('employees as admin_nominee_t', 'admin_nominee_t.employee_no', '=', 'study_leaves.nominee_admin_empno')
            ->leftJoin('employees as other_nominee_t', 'other_nominee_t.employee_no', '=', 'study_leaves.nominee_other_empno')
            ->where('study_leaves.id', $id)
            ->where(function($q) use ($from, $facultyIds) {
                if ($from === 'accepted') {
                    $q->whereNotNull('study_leave_approvals.dean_empno')
                      ->whereIn('employees.faculty_id', $facultyIds);
                } else {
                    $q->where('study_leave_approvals.status_id', 6) // Processing Dean
                      ->whereIn('employees.faculty_id', $facultyIds);
                }
            })
            ->select(
                'study_leaves.*',
                'employees.employee_no as employee_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.email',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'faculties.id as faculty_id',
                'designations.designation_name as designation',
                'statuses.status',
                'study_leaves.nominee_teaching_empno as teaching_nominee_emp_no',
                DB::raw("CONCAT(teaching_nominee_t.initials, ' ', teaching_nominee_t.last_name) as teaching_nominee_name"),
                'study_leaves.nominee_admin_empno as admin_nominee_emp_no',
                DB::raw("CONCAT(admin_nominee_t.initials, ' ', admin_nominee_t.last_name) as admin_nominee_name"),
                'study_leaves.nominee_other_empno as other_nominee_emp_no',
                DB::raw("CONCAT(other_nominee_t.initials, ' ', other_nominee_t.last_name) as other_nominee_name"),
                // Deputy Registrar Review data from study_leave_approvals
                'study_leave_approvals.registrar_recommendation',
                'study_leave_approvals.registrar_not_recommend_reason',
                'study_leave_approvals.registrar_remarks',
                // HOD Review data from study_leave_approvals
                'study_leave_approvals.hod_adequate_staff_available',
                'study_leave_approvals.hod_teaching_covered',
                'study_leave_approvals.hod_service_period',
                'study_leave_approvals.hod_recommend',
                'study_leave_approvals.hod_not_recommend_reason',
                'study_leave_approvals.hod_remarks'
            )
            ->first();

        if (!$draft_study_leave) {
            return redirect()->route('dean.index')->with('error', 'Study leave application not found or not accessible.');
        }

        // Prepare user object for the partial view
        $user = (object) [
            'empno' => $draft_study_leave->employee_no,
            'name_with_initials' => $draft_study_leave->name_with_initials,
            'email' => $draft_study_leave->email,
            'department' => $draft_study_leave->department,
            'faculty' => $draft_study_leave->faculty,
            'designation' => $draft_study_leave->designation
        ];
        
        $readonly = true;

        return view('dean.study_leave.view_study_leave_form', compact('draft_study_leave', 'user', 'readonly', 'from'));
    }

    public function approveStudyLeave(Request $request, $id)
    {
        // Validate the Dean review inputs
        $request->validate([
            'dean_recommend' => 'required|integer|in:0,1',
            'dean_not_recommend_reason' => 'required_if:dean_recommend,0|string|nullable',
            'dean_remarks' => 'nullable|string',
        ]);

        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        // Verify the application belongs to this Dean's faculties
        $application = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leaves.id', $id)
            ->where('study_leave_approvals.status_id', 6) // Processing Dean
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->select('study_leaves.*', 'study_leave_approvals.id as approval_id')
            ->first();

        if (!$application) {
            return redirect()->route('dean.index')->with('error', 'Application not found or not accessible.');
        }

        // Update the study_leave_approvals table with Dean review
        DB::table('study_leave_approvals')
            ->where('study_leave_id', $id)
            ->update([
                'status_id' => 7, // Processing VC (forward to VC)
                'dean_empno' => self::DEAN_EMP_NO,
                'dean_leave_recommendation_status' => $request->dean_recommend,
                'dean_not_recommended_reason' => $request->dean_not_recommend_reason,
                'dean_remarks' => $request->dean_remarks,
                'dean_reviewed_date' => now()->toDateString(),
                'updated_at' => now()
            ]);
          
        return redirect()->route('dean.study.leave.index')->with('success', 'Study leave application forwarded to VC successfully.');
    }

    /**
     * Show study leave extension for Dean review
     */
    public function showExtension(Request $request, $extension_id)
    {
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();
        $from = $request->get('from');

        // Get the complete study leave extension data
        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions.id', $extension_id)
            ->where(function ($q) use ($from) {
                if ($from === 'accepted') {
                    $q->whereNotNull('study_leave_extensions_approvals.dean_empno');
                } else {
                    $q->where('study_leave_extensions_approvals.status_id', 6); // Processing Dean
                }
            })
            ->whereIn('employees.faculty_id', $facultyIds) // Filter by Dean's faculties
            ->select(
                'study_leave_extensions.id as extension_id',
                'study_leave_extensions.study_leave_id',
                'study_leave_extensions.old_end_date',
                'study_leave_extensions.new_end_date',
                'study_leave_extensions.extension_payment_type',
                'study_leave_extensions.reason_for_extension',
                'study_leave_extensions_approvals.status_id as extension_status_id',
                'study_leave_extensions_approvals.ma_empno',
                'study_leave_extensions_approvals.ma_recommend',
                'study_leave_extensions_approvals.ma_not_recommend_reason',
                'study_leave_extensions_approvals.ma_remarks',
                'study_leave_extensions_approvals.acad_est_head_empno',
                'study_leave_extensions_approvals.acad_est_head_recommend',
                'study_leave_extensions_approvals.acad_est_head_not_recommend_reason',
                'study_leave_extensions_approvals.acad_est_head_remarks',
                'study_leave_extensions_approvals.hod_remarks as extension_hod_remarks',
                'study_leave_extensions_approvals.hod_recommend as extension_hod_recommend',
                'study_leave_extensions_approvals.hod_not_recommend_reason as extension_hod_not_recommend_reason',
                'study_leave_extensions_approvals.dean_recommend as extension_dean_recommend',
                'study_leave_extensions_approvals.dean_not_recommended_reason as extension_dean_not_recommend_reason',
                'study_leave_extensions_approvals.dean_remark as extension_dean_remarks',
                'study_leave_extensions_approvals.dean_remark',
                'study_leaves.*', // Get all study leave fields
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials',
                'employees.nic',
                'employees.email',
                'employees.mobile_no as mobile',
                'employees.department_id',
                'employees.faculty_id',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'statuses.status'
            )
            ->first();

            

        if (!$extension) {
            $redirectRoute = $from === 'accepted'
                ? 'dean.study.leave.extensions.accepted'
                : 'dean.index';
            return redirect()->route($redirectRoute)->with('error', 'Extension application not found or not accessible.');
        }

        // Create user object for forms
        $user = (object)[
            'empno' => $extension->empno,
            'name_with_initials' => $extension->name_with_initials,
            'names_denoted_by_initials' => $extension->name_denoted_by_initials,
            'nic' => $extension->nic,
            'email' => $extension->email,
            'mobile' => $extension->mobile,
            'department' => $extension->department,
            'faculty' => $extension->faculty,
            'designation' => $extension->designation,
        ];

        // Create draft_study_leave object for forms (using the original study leave data)
        $draft_study_leave = $extension;

        // Calculate duration for display
        $oldDate = \Carbon\Carbon::parse($extension->old_end_date);
        $newDate = \Carbon\Carbon::parse($extension->new_end_date);
        $durationDays = $oldDate->diffInDays($newDate);
        $durationMonths = round($durationDays / 30, 1);

        $readonly = true;
       // dd( $extension);

        return view('dean.study_leave.study_leave_extension_view_form', compact('extension', 'user', 'readonly', 'draft_study_leave', 'durationDays', 'durationMonths', 'from'));
    }

    /**
     * Approve/Forward extension to VC
     */
    public function approveExtension(Request $request, $extension_id)
    {
        $request->validate([
            'dean_recommend' => 'required|integer|in:0,1',
            'dean_not_recommend_reason' => 'required_if:dean_recommend,0|string|nullable',
            'dean_remarks' => 'nullable|string',
        ]);

        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('study_leave_extensions_approvals.status_id', 6)
            ->whereIn('employees.faculty_id', $facultyIds)
            ->select('study_leave_extensions.*')
            ->first();

        if (!$extension) {
            return redirect()->route('dean.index')->with('error', 'Extension application not found.');
        }

        // Prepare Dean remarks
        $deanRemarks = "Dean Review:\n";
        $deanRemarks .= "- Recommendation: " . ($request->dean_recommend == 1 ? 'Yes' : 'No') . "\n";
        
        if ($request->dean_recommend == 0 && $request->dean_not_recommend_reason) {
            $deanRemarks .= "- Reason for Not Recommending: " . $request->dean_not_recommend_reason . "\n";
        }
        
        if ($request->dean_remarks) {
            $deanRemarks .= "- Additional Remarks: " . $request->dean_remarks . "\n";
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        $deanRemarks .= "\n[Dean Reviewed - " . $timestamp . "]";

        // Update extension status to Processing VC (status_id = 7)
        DB::table('study_leave_extensions_approvals')
            ->where('study_leave_extension_id', $extension_id)
            ->update([
                'status_id' => 7, // Processing VC
                'dean_empno' => self::DEAN_EMP_NO,
                'dean_recommend' => $request->dean_recommend,
                'dean_not_recommended_reason' => $request->dean_not_recommend_reason,
                'dean_remark' => DB::raw("CONCAT(COALESCE(dean_remark, ''), '" . addslashes($deanRemarks) . "')"),
                'dean_reviewed_date' => now()->toDateString(),
                'updated_at' => now()
            ]);

        return redirect()->route('dean.study.leave.extensions')->with('success', 'Extension has been successfully forwarded from Faculty Dean to Vice Chancellor.');
    }

    /**
     * Return extension to employee
     */
    public function returnExtension(Request $request, $extension_id)
    {
        $request->validate([
            'dean_remarks' => 'required|string|max:1000',
        ]);

        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('study_leave_extensions_approvals.status_id', 6)
            ->whereIn('employees.faculty_id', $facultyIds)
            ->select('study_leave_extensions.*')
            ->first();

        if (!$extension) {
            return redirect()->route('dean.index')->with('error', 'Extension application not found.');
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        $returnRemark = "\n\n[Dean Returned - " . $timestamp . "]\n" . $request->dean_remarks;

        // Update extension status to Returned (status_id = 3)
        DB::table('study_leave_extensions_approvals')
            ->where('study_leave_extension_id', $extension_id)
            ->update([
                'status_id' => 3, // Returned
                'dean_empno' => self::DEAN_EMP_NO,
                'dean_remark' => DB::raw("CONCAT(COALESCE(dean_remark, ''), '" . addslashes($returnRemark) . "')"),
                'updated_at' => now()
            ]);

        return redirect()->route('dean.index')->with('success', 'Extension request returned to employee.');
    }

    /**
     * Show progress report for Dean review
     */
    public function showProgressReport(Request $request, $progress_report_id)
    {
        $from = $request->get('from');
        // Get faculty IDs for this Dean
        $facultyIds = $this->getDeanFaculties();

        // Fetch the progress report with related study leave and employee details
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->when($from === 'accepted', function ($query) use ($facultyIds) {
                $query->whereNotNull('study_leave_progress_reports_approval.dean_empno')
                      ->whereIn('employees.faculty_id', $facultyIds);
            }, function ($query) use ($facultyIds) {
                $query->where('study_leave_progress_reports_approval.approval_status_id', 6)
                      ->whereIn('employees.faculty_id', $facultyIds);
            })
            ->select(
                'study_leave_progress_reports.*',
                'study_leave_progress_reports.id as progress_report_id',
                'study_leaves.*',
                'study_leaves.scholarship_source as scholarship_source',
                'study_leaves.scholarship_amount as scholarship_amount',
                'study_leaves.project_name as project_name',
                'study_leaves.nominee_teaching_empno as nominee_teaching_empno',
                'study_leaves.nominee_admin_empno as nominee_admin_empno',
                'study_leaves.nominee_other_empno as nominee_other_empno',
                'study_leaves.self_funding_declaration as self_funding_declaration',
                'study_leaves.placement_letter as placement_letter',
                'employees.employee_no as employee_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.email',
                'departments.department_name as department',
                'departments.id as department_id',
                'faculties.faculty_name as faculty',
                'faculties.id as faculty_id',
                'designations.designation_name as designation',
                'statuses.status',
                // Registrar review data from study_leave_progress_reports_approval
                'study_leave_progress_reports_approval.registrar_empno',
                'study_leave_progress_reports_approval.registrar_approval_status',
                'study_leave_progress_reports_approval.registrar_not_approve_reason',
                'study_leave_progress_reports_approval.registrar_remarks',
                // HOD review data from study_leave_progress_reports_approval
                'study_leave_progress_reports_approval.hod_empno',
                'study_leave_progress_reports_approval.hod_approval_status',
                'study_leave_progress_reports_approval.hod_not_approve_reason',
                'study_leave_progress_reports_approval.hod_remarks',
                // Dean review data from study_leave_progress_reports_approval
                'study_leave_progress_reports_approval.dean_approval_status',
                'study_leave_progress_reports_approval.dean_not_approve_reason',
                'study_leave_progress_reports_approval.dean_remarks'
            )
            ->first();

        if (!$progressReport) {
            return redirect()->route('dean.index')->with('error', 'Progress report not found.');
        }

        $draft_study_leave = $progressReport;

        // Fetch all approved progress reports for this study leave (status_id = 1)
        $approvedReports = DB::table('study_leave_progress_reports')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_progress_reports.study_leave_id', $progressReport->study_leave_id)
            ->where('study_leave_progress_reports.status_id', 1) // Approved status
            ->where('study_leave_progress_reports.id', '!=', $progress_report_id) // Exclude current report
            ->select(
                'study_leave_progress_reports.id',
                'study_leave_progress_reports.due_date',
                'study_leave_progress_reports.submitted_date',
                'study_leave_progress_reports.document_path',
                'statuses.status'
            )
            ->orderBy('study_leave_progress_reports.due_date', 'desc')
            ->get();

        // Prepare user object for the view
        $user = (object) [
            'empno' => $progressReport->employee_no,
            'name_with_initials' => $progressReport->name_with_initials,
            'email' => $progressReport->email,
            'department' => $progressReport->department,
            'faculty' => $progressReport->faculty,
            'designation' => $progressReport->designation
        ];

        // Get VC information for forwarding
        $vcEmpNo = self::VC_EMP_NO;
        $vcInfo = DB::table('employees')
            ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
            ->where('employees.employee_no', $vcEmpNo)
            ->select(
                'employees.employee_no as vc_emp_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as vc_name"),
                'categories.category_name as vc_title'
            )
            ->first();

        $readonly = false;

        return view('dean.study_leave.study_leave_progress_report_view_form', compact('progressReport', 'user', 'readonly', 'approvedReports', 'vcInfo', 'draft_study_leave', 'from'));
    }

    /**
     * Submit progress report review (approve or return) by Dean
     */
    public function submitProgressReportReview(Request $request, $progress_report_id)
    {
        $request->validate([
            'approval_decision' => 'required|in:approved,not_approved',
            'remark' => 'nullable|string|max:1000',
        ]);

        // If not approved, remarks are required
        if ($request->approval_decision === 'not_approved' && empty(trim($request->remark))) {
            return redirect()->back()->with('error', 'Remarks are required when returning a progress report.');
        }

        $deanEmpNo = self::DEAN_EMP_NO;
        $facultyIds = $this->getDeanFaculties();

        // Verify the progress report
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('study_leave_progress_reports_approval.approval_status_id', 6) // Processing Dean
            ->whereIn('employees.faculty_id', $facultyIds)
            ->select('study_leave_progress_reports.*')
            ->first();

        if (!$progressReport) {
            return redirect()->route('dean.index')->with('error', 'Progress report not found.');
        }

        if ($request->approval_decision === 'approved') {
            // Approve and forward to VC
            DB::table('study_leave_progress_reports_approval')
                ->where('study_leave_progress_report_id', $progress_report_id)
                ->update([
                    'dean_empno' => $deanEmpNo,
                    'dean_approval_status' => 1, // Approved
                    'dean_remarks' => $request->remark,
                    'approval_status_id' => 7, // Processing VC
                    'dean_reviewed_date' => now()->toDateString(),
                    'updated_at' => now()
                ]);

            // Update progress report status to Processing VC
            DB::table('study_leave_progress_reports')
                ->where('id', $progress_report_id)
                ->update([
                    'status_id' => 7, // Processing VC
                    'updated_at' => now()
                ]);

            return redirect()->route('dean.study.leave.progress')->with('success', 'Progress report approved and forwarded to VC successfully.');
        } else {
            // Return to HOD (not approved)
            DB::table('study_leave_progress_reports_approval')
                ->where('study_leave_progress_report_id', $progress_report_id)
                ->update([
                    'dean_empno' => $deanEmpNo,
                    'dean_approval_status' => 2, // Not Approved / Returned
                    'dean_not_approve_reason' => $request->remark,
                    'dean_remarks' => $request->remark,
                    'approval_status_id' => 5, // Return to HOD
                    'updated_at' => now()
                ]);

            // Update progress report status to Processing HOD
            DB::table('study_leave_progress_reports')
                ->where('id', $progress_report_id)
                ->update([
                    'status_id' => 5, // Processing HOD
                    'updated_at' => now()
                ]);

            return redirect()->route('dean.index')->with('success', 'Progress report returned to HOD successfully.');
        }
    }
} 
