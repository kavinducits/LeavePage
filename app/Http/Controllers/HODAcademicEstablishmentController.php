<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HODAcademicEstablishmentController extends Controller
{
     private const HOD_EMP_NO = 12453;
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
           // ->whereRaw('(end_date IS NULL OR end_date >= CURDATE())') // Current or future end date
            ->pluck('department_id')
            ->toArray();

        if (empty($departments)) {
            // HOD not found in department_heads table - show error
            abort(403, 'Access denied. Employee ' . $hodEmpNo . ' is not authorized as a Head of Department.');
        }

        return $departments;
    }
    //
    public function study_leave()
    {
       // dd('here');
        // Get department IDs for this HOD
       // $departmentIds = $this->getHodDepartments();

      
        // Get all study leave applications for HOD review from assigned departments
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_approvals.status_id', 9) // Processing HOD Academic Establishment (status_id = 9)
            //->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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

          
           
           

        return view('hod_academic_establishment.study_leave_dashboard', compact('studyLeaveApplications'));
    }
    public function showStudyLeaveApplication($id)
    {
   
        
        // Get department IDs for this HOD
        //$departmentIds = $this->getHodDepartments();
       
       
        // Fetch the study leave application with all necessary details
        $draft_study_leave = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->leftJoin('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->leftJoin('employees as teaching_nominee_t', 'teaching_nominee_t.employee_no', '=', 'study_leaves.nominee_teaching_empno')
            ->leftJoin('employees as admin_nominee_t', 'admin_nominee_t.employee_no', '=', 'study_leaves.nominee_admin_empno')
            ->leftJoin('employees as other_nominee_t', 'other_nominee_t.employee_no', '=', 'study_leaves.nominee_other_empno')
            ->where('study_leave_approvals.study_leave_id', $id)
            ->where('study_leave_approvals.status_id', 9) // Processing HOD
            //->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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
        /*
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
                */
        $departmentHead = null;
// dd($academic_establishmnet_department_id);
                    if ($draft_study_leave && isset($draft_study_leave->department_id)) {
                        $departmentHead = DB::table('department_heads')
                ->Join('employees', 'department_heads.emp_no', '=', 'employees.employee_no')
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
                ->leftJoin('categories as head_positions','department_heads.head_position', '=', 'head_positions.id')
                ->where('department_heads.department_id', $draft_study_leave->department_id)
                ->where('department_heads.active_status', 1)
                ->select(
                    'department_heads.emp_no as head_emp_no',
                    DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as head_name"),
                    'categories.category_name as head_title',
                    'categories.id as head_title_id',
                    'head_positions.category_name as head_position',
                    'head_positions.id as head_position_id'
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
      

        return view('hod_academic_establishment.study_leave.view_study_leave_form', compact('draft_study_leave', 'user', 'readonly', 'departmentHead'));
    }

     public function approveStudyLeave(Request $request, $id)
    {
        // Validate the Academic Establishment HOD review inputs
        //dd($request->all());
        $request->validate([
            'registrar_recommendation' => 'nullable|string|in:yes,no',
            'registrar_not_recommend_reason' => 'required_if:registrar_recommendation,no|string|nullable',
            'registrar_remarks' => 'nullable|string|max:1000',
        ]);

        $hodEmpNo = self::HOD_EMP_NO;

        // Verify the application exists and is in the correct status
        $application = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->where('study_leaves.id', $id)
            ->where('study_leave_approvals.status_id', 9) // Processing Academic Establishment HOD
            ->select('study_leaves.*', 'study_leave_approvals.id as approval_id')
            ->first();

        if (!$application) {
            return redirect()->route('hodacademicestablishment.studyLeave')->with('error', 'Application not found or not accessible.');
        }

        // Update the study_leave_approvals table with Academic Establishment HOD details
        DB::table('study_leave_approvals')
            ->where('study_leave_id', $id)
            ->update([
                'status_id' => 5, // Processing Department HOD (forward to applicant's department HOD)
                'registrar_empno' => $hodEmpNo,
                'registrar_recommendation' => $request->registrar_recommendation,
                'registrar_not_recommend_reason' => $request->registrar_not_recommend_reason,
                'registrar_remarks' => $request->registrar_remarks,
                'updated_at' => now()
            ]);
          
        return redirect()->route('hodacademicestablishment.studyLeave')->with('success', 'Study Leave Application reviewed and forwarded to Department HOD successfully.');
    }

     public function study_leave_extenstions()
    {
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();

        // Get study leave extension applications for HOD review from assigned departments
        $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_extensions.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions.status_id', 6) // Processing HOD Academic Establishment (status_id = 6)
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


        return view('hod_academic_establishment.study_leave_extensions_dashboard', compact( 'extensionApplications'));
    }
     public function study_leave_progress_reports()
    {
       
        // Get department IDs for this HOD
        $departmentIds = $this->getHodDepartments();


        // Get study leave progress report applications for HOD review from assigned departments
        $progressReportApplications = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_progress_reports_approval.approval_status_id', 9) // Forwarded to HOD Academic Establishment
            //->whereIn('employees.department_id', $departmentIds) // Filter by HOD's departments
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
            

       return view('hod_academic_establishment.study_leave_progress_report_dashboard', compact('progressReportApplications'));
       
    }

    /**
     * Show progress report details for HOD review
     */
    public function showProgressReport($progress_report_id)
    {
        
        $hodEmpNo = self::HOD_EMP_NO;
        $departmentIds = $this->getHodDepartments();

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
            ->where('study_leave_progress_reports_approval.approval_status_id', 9) // Ensure HOD has access
            //->whereIn('employees.department_id', $departmentIds)
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
                'designations.designation_name as designation',
                'statuses.status'
            )
            ->first();

        if (!$progressReport) {
            return redirect()->route('hodacademicestablishment.studyLeaveProgress')->with('error', 'Progress report not found.');
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

        // Get department head information using study_leave_id
        $departmentHead = null;
        if ($progressReport->department_id) {
            $departmentHead = DB::table('department_heads')
                ->join('employees', 'department_heads.emp_no', '=', 'employees.employee_no')
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
                ->leftJoin('categories as head_positions', 'department_heads.head_position', '=', 'head_positions.id')
                ->where('department_heads.department_id', $progressReport->department_id)
                ->where('department_heads.active_status', 1)
                ->select(
                    'department_heads.emp_no as head_emp_no',
                    DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as head_name"),
                    'categories.category_name as head_title',
                    'categories.id as head_title_id',
                    'head_positions.category_name as head_position',
                    'head_positions.id as head_position_id'
                )
                ->first();
        }

        $readonly = false;

        return view('hod_academic_establishment.study_leave.study_leave_progress_report_view_form', compact('progressReport', 'user', 'readonly', 'approvedReports', 'draft_study_leave', 'departmentHead'));
    }

    /**
     * Submit progress report review (approve or return) by HOD Academic Establishment (acting as Registrar)
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

        $hodEmpNo = self::HOD_EMP_NO;
        $departmentIds = $this->getHodDepartments();

        // Verify the progress report
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('study_leave_progress_reports_approval.approval_status_id', 9)
            //->whereIn('employees.department_id', $departmentIds)
            ->select('study_leave_progress_reports.*')
            ->first();

        if (!$progressReport) {
            return redirect()->route('hodacademicestablishment.studyLeaveProgress')->with('error', 'Progress report not found.');
        }

            // Approve the progress report
            DB::table('study_leave_progress_reports_approval')
                ->where('study_leave_progress_report_id', $progress_report_id)
                ->update([
                    'registrar_empno' => $hodEmpNo,
                    'registrar_approval_status' => 1, // Approved
                    'registrar_remarks' => $request->remark,
                    'approval_status_id' => 5, // Approved
                    'updated_at' => now()
                ]);


            return redirect()->route('hodacademicestablishment.studyLeaveProgress')->with('success', 'Progress report approved successfully.');
      
    }

    /**
     * Approve progress report by HOD Academic Establishment (acting as Registrar)
     */
    public function approveProgressReport(Request $request, $progress_report_id)
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $hodEmpNo = self::HOD_EMP_NO;
        $departmentIds = $this->getHodDepartments();

        // Verify the progress report
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('study_leave_progress_reports_approval.approval_status_id', 9)
            //->whereIn('employees.department_id', $departmentIds)
            ->select('study_leave_progress_reports.*')
            ->first();

        if (!$progressReport) {
            return redirect()->route('hodacademicestablishment.studyLeaveProgress')->with('error', 'Progress report not found.');
        }

        // Update approval record - approve as registrar
        DB::table('study_leave_progress_reports_approval')
            ->where('study_leave_progress_report_id', $progress_report_id)
            ->update([
                'registrar_empno' => $hodEmpNo,
                'registrar_approval_status' => 1, // Approved
                'registrar_remarks' => $request->remark,
                'approval_status_id' => 1, // Approved
                'updated_at' => now()
            ]);

        // Update progress report status to Approved
        DB::table('study_leave_progress_reports')
            ->where('id', $progress_report_id)
            ->update([
                'status_id' => 1, // Approved
                'updated_at' => now()
            ]);

        return redirect()->route('hodacademicestablishment.studyLeaveProgress')->with('success', 'Progress report approved successfully.');
    }

    /**
     * Return progress report to MA
     */
    public function returnProgressReport(Request $request, $progress_report_id)
    {
        $request->validate([
            'remark' => 'required|string|max:1000',
        ], [
            'remark.required' => 'Remarks are required when returning a progress report.'
        ]);

        $hodEmpNo = self::HOD_EMP_NO;
        $departmentIds = $this->getHodDepartments();

        // Verify the progress report
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports.id', '=', 'study_leave_progress_reports_approval.study_leave_progress_report_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('study_leave_progress_reports_approval.approval_status_id', 9)
            //->whereIn('employees.department_id', $departmentIds)
            ->select('study_leave_progress_reports.*')
            ->first();

        if (!$progressReport) {
            return redirect()->route('hodacademicestablishment.studyLeaveProgress')->with('error', 'Progress report not found.');
        }

        // Update approval record - return to MA
        DB::table('study_leave_progress_reports_approval')
            ->where('study_leave_progress_report_id', $progress_report_id)
            ->update([
                'registrar_empno' => $hodEmpNo,
                'registrar_approval_status' => 2, // Not Approved / Returned
                'registrar_not_approve_reason' => $request->remark,
                'registrar_remarks' => $request->remark,
                'approval_status_id' => 4, // Return to MA (Processing MA)
                'updated_at' => now()
            ]);

        // Update progress report status to Processing MA
        DB::table('study_leave_progress_reports')
            ->where('id', $progress_report_id)
            ->update([
                'status_id' => 4, // Processing MA
                'updated_at' => now()
            ]);

        return redirect()->route('hodacademicestablishment.studyLeaveProgress')->with('success', 'Progress report returned to MA successfully.');
    }
}
