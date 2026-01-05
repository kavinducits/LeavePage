<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SebastianBergmann\Environment\Console;

class HODController extends Controller
{
    // Hardcoded HOD employee number - change this to switch to a different HOD
    private const HOD_EMP_NO = 5178; // HOD for department 114 (has leave applications)

    /**
     * Get department IDs for the current HOD
     * Returns array of department IDs or shows error page if HOD not found
     */
    private function getHodDepartments()
    {
        $hodEmpNo = self::HOD_EMP_NO;

        // Check if this employee is a valid HOD in department_heads table
        $departments = DB::table('department_heads')
            ->where('emp_no', $hodEmpNo)
            ->where('active_status', 1) // Only active appointments
            ->whereRaw('(end_date IS NULL OR end_date >= CURDATE())') // Current or future end date
            ->pluck('department_id')
            ->toArray();

        if (empty($departments)) {
            // HOD not found in department_heads table - show error
            abort(403, 'Access denied. Employee ' . $hodEmpNo . ' is not authorized as a Head of Department.');
        }

        return $departments;
    }

    public function index()
    {
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        // Get all applications for HOD review (status_id = 5) from assigned departments
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 5) // Processing HOD
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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

        // Get all study leave applications for HOD review from assigned departments
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->where('study_leaves.status_id', 5) // Processing HOD (status_id = 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
            ->orderByDesc('study_leaves.created_at')
            ->select(
                'study_leaves.id',
                'study_leaves.reference_no',
                'study_leaves.empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leaves.created_at as applied_date',
                'statuses.status',
                'employees.department_id'
            )
            ->get();

        // Get study leave extension applications for HOD review from assigned departments
        $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_extensions.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions.status_id', 5) // Processing HOD (status_id = 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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

        // Get study leave progress report applications for HOD review from assigned departments
        $progressReportApplications = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_progress_reports.status_id', 5) // Processing HOD (status_id = 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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

        return view('hod.index', compact('applications', 'studyLeaveApplications', 'extensionApplications', 'progressReportApplications'));
    }

    public function show($id)
    {
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

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
            ->where('leave_details.status_id', 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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
            return redirect()->route('hod.index')->with('error', 'Application not found.');
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

        // Get Dean information for this faculty
        $deanInfo = null;
        if ($application->faculty_id) {
            $deanInfo = DB::table('faculty_deans')
                ->join('employees as dean_emp', 'faculty_deans.emp_no', '=', 'dean_emp.employee_no')
                ->leftJoin('categories', 'dean_emp.title_id', '=', 'categories.id')
                ->leftJoin('faculties', 'faculty_deans.faculty_id', '=', 'faculties.id')
                ->where('faculty_deans.faculty_id', $application->faculty_id)
                ->where('faculty_deans.active_status', 1)
                ->whereRaw('(faculty_deans.end_date IS NULL OR faculty_deans.end_date >= CURDATE())')
                ->select(
                    'categories.category_name as title',
                    'dean_emp.initials',
                    'dean_emp.last_name',
                    'faculties.faculty_name'
                )
                ->first();
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

        return view('hod.show', compact('application', 'travelDetails', 'deanInfo'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'hod_adequate_staff' => 'required|boolean',
            'hod_teaching_covered' => 'required|boolean',
            'hod_exam_work_completed' => 'required|boolean',
            'hod_recommend' => 'required|boolean',
            'hod_not_recommend_reason' => 'required_if:hod_recommend,0',
        ]);

        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2)
            ->where('leave_details.status_id', 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('hod.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table for validation/processing
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // If recommended or not, always forward to Dean (status_id = 6)
        $status_id = 6;

        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'hod_adequate_staff' => $request->hod_adequate_staff,
                'hod_teaching_covered' => $request->hod_teaching_covered,
                'hod_exam_work_completed' => $request->hod_exam_work_completed,
                'hod_recommend' => $request->hod_recommend,
                'hod_not_recommend_reason' => $request->hod_not_recommend_reason,
                'hod_other_remarks' => $request->hod_other_remarks,
                'hod_reviewed_by' => 'O. Wickramasinghe',
                'hod_reviewed_at' => now(),
                // New HOD fields
                'hod_name' => 'O. Wickramasinghe',
                'hod_recommendation' => $request->hod_recommend,
                'hod_forwarded_date' => now(),
                'hod_designation' => 'Head of Department', // Can be made configurable in future
                'hod_empno' => self::HOD_EMP_NO, // Record which HOD processed this
                'status_id' => $status_id,
                'updated_at' => now(),
            ]);

        $msg = $request->hod_recommend ? 'Application forwarded to Dean.' : 'Application not recommended.';
        return redirect()->route('hod.index')->with('success', $msg);
    }

    public function showStudyLeaves()
    {
        
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        // Get all study leave applications for HOD review from assigned departments
        // Connection: HOD_EMP_NO -> department_heads -> department_id -> employees -> employee_no -> study_leaves
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->where('study_leaves.status_id', 5) // Processing HOD (status_id = 5)
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
            ->orderByDesc('study_leaves.created_at')
            ->select(
                'study_leaves.id',
                'study_leaves.reference_no',
                'study_leaves.empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leaves.created_at as applied_date',
                'statuses.status',
                'employees.department_id'
            )
            ->get();

        return view('hod.indexStudyLeave', compact('studyLeaveApplications'));
    }
   public function showStudyLeaveApplication($id)
    {
   
        
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();
       
       
        // Fetch the study leave application with all necessary details
        $draft_study_leave = DB::table('study_leaves')
            ->leftJoin('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->leftJoin('employees as teaching_nominee_t', 'teaching_nominee_t.employee_no', '=', 'study_leaves.nominee_teaching_empno')
            ->leftJoin('employees as admin_nominee_t', 'admin_nominee_t.employee_no', '=', 'study_leaves.nominee_admin_empno')
            ->leftJoin('employees as other_nominee_t', 'other_nominee_t.employee_no', '=', 'study_leaves.nominee_other_empno')
            ->where('study_leaves.id', $id)
            ->where('study_leaves.status_id', 5) // Processing HOD
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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
                'departments.id as department_id'
                
            )
            ->first();

       
        if (!$draft_study_leave) {
            return redirect()->route('hod.show.studyleaves')->with('error', 'Study leave application not found or not accessible.');
        }

        // Get Dean information for this faculty
        $deanInfo = null;
        if ($draft_study_leave->faculty_id) {
            $deanInfo = DB::table('faculty_deans')
                ->join('employees as dean_emp', 'faculty_deans.emp_no', '=', 'dean_emp.employee_no')
                ->leftJoin('categories', 'dean_emp.title_id', '=', 'categories.id')
                ->leftJoin('faculties', 'faculty_deans.faculty_id', '=', 'faculties.id')
                ->where('faculty_deans.faculty_id', $draft_study_leave->faculty_id)
                ->where('faculty_deans.active_status', 1)
                ->whereRaw('(faculty_deans.end_date IS NULL OR faculty_deans.end_date >= CURDATE())')
                ->select(
                    'categories.category_name as title',
                    'dean_emp.initials',
                    'dean_emp.last_name',
                    'faculties.faculty_name'
                )
                ->first();
        }

        // Set user and readonly flag for the partial view
        $user = (object) [
            'empno' => $draft_study_leave->employee_no,
            'name_with_initials' => $draft_study_leave->name_with_initials,
            'email' => $draft_study_leave->email,
            'department' => $draft_study_leave->department,
            'faculty' => $draft_study_leave->faculty,
            'designation' => $draft_study_leave->designation
        ];
        
        $readonly = true;
      

        return view('hod.study_leave.view_study_leave_form', compact('draft_study_leave', 'user', 'readonly', 'deanInfo'));
    }

    /**
     * Show study leave extension for HOD review
     */
    public function showExtension($extension_id)
    {
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        // Get the complete study leave extension data
        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leave_extensions.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('study_leave_extensions.status_id', 5) // Processing HOD
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
            ->select(
                'study_leave_extensions.id as extension_id',
                'study_leave_extensions.study_leave_id',
                'study_leave_extensions.old_end_date',
                'study_leave_extensions.new_end_date',
                'study_leave_extensions.reason_for_extension',
                'study_leave_extensions.status_id as extension_status_id',
                'study_leave_extensions.ma_remarks',
                'study_leave_extensions.hod_remarks',
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
            return redirect()->route('hod.index')->with('error', 'Extension application not found or not accessible.');
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

        // Get Dean details for forwarding
        $deanInfo = null;
        if ($extension->faculty_id) {
            $deanInfo = DB::table('faculty_deans')
                ->join('employees as dean_emp', 'faculty_deans.emp_no', '=', 'dean_emp.employee_no')
                ->leftJoin('categories', 'dean_emp.title_id', '=', 'categories.id')
                ->leftJoin('faculties', 'faculty_deans.faculty_id', '=', 'faculties.id')
                ->where('faculty_deans.faculty_id', $extension->faculty_id)
                ->where('faculty_deans.active_status', 1)
                ->whereRaw('(faculty_deans.end_date IS NULL OR faculty_deans.end_date >= CURDATE())')
                ->select(
                    'categories.category_name as title',
                    'dean_emp.initials',
                    'dean_emp.last_name',
                    'faculties.faculty_name'
                )
                ->first();
        }

        // Calculate duration for display
        $oldDate = \Carbon\Carbon::parse($extension->old_end_date);
        $newDate = \Carbon\Carbon::parse($extension->new_end_date);
        $durationDays = $oldDate->diffInDays($newDate);
        $durationMonths = round($durationDays / 30, 1);

        $readonly = true;
       
        return view('hod.study_leave.study_leave_extension_view_form', compact('extension', 'user', 'deanInfo', 'readonly', 'draft_study_leave', 'durationDays', 'durationMonths'));
    }

    /**
     * Approve/Forward extension to Dean
     */
    public function approveExtension(Request $request, $extension_id)
    {
        $request->validate([
            'hod_recommend' => 'required|string',
            'hod_not_recommend_reason' => 'required_if:hod_recommend,no|string|nullable',
            'hod_remarks' => 'nullable|string',
        ]);

        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('study_leave_extensions.status_id', 5)
            ->whereIn('employees.department_id', $departmentIds)
            ->select('study_leave_extensions.*')
            ->first();

        if (!$extension) {
            return redirect()->route('hod.index')->with('error', 'Extension application not found.');
        }

     //  dd($request->hod_not_recommend_reason);

        // Update extension status to Processing Dean (status_id = 6)
        DB::table('study_leave_extensions')
            ->where('id', $extension_id)
            ->update([
                'status_id' => 6, // Processing Dean
                'hod_empno' => self::HOD_EMP_NO,
                'hod_recommend' => $request->hod_recommend,
                'hod_not_recommend_reason' => $request->hod_not_recommend_reason,
                'hod_remarks' => $request->hod_remarks,
                'updated_at' => now()
            ]);
           

        return redirect()->route('hod.index')->with('success', 'Extension request forwarded to Dean successfully.');
    }

    /**
     * Return extension to employee
     */
    public function returnExtension(Request $request, $extension_id)
    {
        $request->validate([
            'hod_remarks' => 'required|string|max:1000',
        ]);

        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('study_leave_extensions.status_id', 5)
            ->whereIn('employees.department_id', $departmentIds)
            ->select('study_leave_extensions.*')
            ->first();

        if (!$extension) {
            return redirect()->route('hod.index')->with('error', 'Extension application not found.');
        }

        $timestamp = now()->format('Y-m-d H:i:s');
        $returnRemark = "\n\n[HOD Returned - " . $timestamp . "]\n" . $request->hod_remarks;

        // Update extension status to Returned (status_id = 3)
        DB::table('study_leave_extensions')
            ->where('id', $extension_id)
            ->update([
                'status_id' => 3, // Returned
                'hod_empno' => self::HOD_EMP_NO,
                'hod_remarks' => DB::raw("CONCAT(COALESCE(hod_remarks, ''), '" . addslashes($returnRemark) . "')"),
                'updated_at' => now()
            ]);

        return redirect()->route('hod.index')->with('success', 'Extension request returned to employee.');
    }

    public function approveStudyLeave(Request $request, $id)
    {
        // Validate the HOD review inputs
        $request->validate([
            'hod_adequate_staff_available' => 'required|string',
            'hod_teaching_covered' => 'required|string',
            'hod_service_period' => 'required|string',
            'hod_recommend' => 'required|string',
            'hod_not_recommend_reason' => 'required_if:hod_recommend,no|string|nullable',
            'hod_remarks' => 'nullable|string',
        ]);

        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        // Verify the application belongs to this HOD's departments
        $application = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leaves.id', $id)
            ->where('study_leaves.status_id', 5) // Processing HOD
            ->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
            ->select('study_leaves.*')
            ->first();

        if (!$application) {
            return redirect()->route('hod.show.studyleaves')->with('error', 'Application not found or not accessible.');
        }

        // Update the study leave application with HOD review
        DB::table('study_leaves')
            ->where('id', $id)
            ->update([
                'status_id' => 6, // Processing Dean (forward to Dean)
                'hod_empno' => self::HOD_EMP_NO,
                'hod_adequate_staff_available' => $request->hod_adequate_staff_available,
                'hod_teaching_covered' => $request->hod_teaching_covered,
                'hod_service_period' => $request->hod_service_period,
                'hod_recommend' => $request->hod_recommend,
                'hod_not_recommend_reason' => $request->hod_not_recommend_reason,
                'hod_remarks' => $request->hod_remarks,
                'updated_at' => now()
            ]);
          
        return redirect()->route('hod.index')->with('success', 'Study Leave Application reviewed and forwarded to Dean successfully.');
    }

    public function showProgressReport($progress_report_id)
    {
       
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

       

        // Fetch the progress report with related study leave and employee details
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('study_leave_progress_reports.status_id', 5) // Processing HOD
            ->whereIn('employees.department_id', $departmentIds) // Ensure HOD has access
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
                'statuses.status'
            )
            ->first();
      
        if (!$progressReport) {
            return redirect()->route('hod.index')->with('error', 'Progress report not found.');
        }
        $draft_study_leave=$progressReport;

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
            ->orderBy('study_leave_progress_reports.due_date', 'asc')
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

        // Get Dean information for forwarding
        $deanInfo = null;
        if ($progressReport->faculty_id) {
            $deanInfo = DB::table('faculty_deans')
                ->join('employees', 'faculty_deans.emp_no', '=', 'employees.employee_no')
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
              // ->leftJoin('categories as dean_positions', 'faculty_deans.dean_position', '=', 'dean_positions.id')
                ->where('faculty_deans.faculty_id', $progressReport->faculty_id)
                ->where('faculty_deans.active_status', 1)
                ->select(
                    'faculty_deans.emp_no as dean_emp_no',
                    DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as dean_name"),
                    'categories.category_name as dean_title',
                    //'categories.id as dean_title_id',
                    //'dean_positions.category_name as dean_position',
                   // 'dean_positions.id as dean_position_id'
                )
                ->first();
        }
    
   
        $readonly = false;

        return view('hod.study_leave.study_leave_progress_report_view_form', compact('progressReport', 'user', 'readonly', 'approvedReports', 'deanInfo', 'draft_study_leave'));
    }
} 