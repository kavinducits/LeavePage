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
        $departmentIds = $this->getHodDepartments();

      
        // Get all study leave applications for HOD review from assigned departments
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->where('study_leaves.status_id', 9) // Processing HOD Academic Establishment (status_id = 9)
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
           

        return view('hod_academic_establishment.study_leave_dashboard', compact('studyLeaveApplications'));
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
            

       return view('hod_academic_establishment.study_leave_progress_report_dashboard', compact('progressReportApplications'));
       
    }
}
