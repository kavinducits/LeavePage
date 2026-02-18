<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\StudyLeave;
use App\Models\StudyLeaveExtension;
use App\Models\StudyLeaveProgressReportsApproval;

class MAController extends Controller
{
    // Hardcoded MA user ID - change this to switch to a different MA
    private const MA_USER_ID = 10390; //15097 for testing 
    PRIVATE const ACADEMIC_ESTABLISHMENT_DEPARTMENT_ID = 5003;

    public function index()
    {
        
        $maUserId = self::MA_USER_ID;

        // Get all submitted applications (form_status = 2) that are being processed by MA (status_id = 4)
        // and are assigned to this specific MA
       
        try {
        
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status'
            )
            ->get();
            } catch (\Exception $e) {
           return view('errors.500');
        }

          

        return view('ma.index', compact('applications'));
    }

    public function show($id)
    {
        $maUserId = self::MA_USER_ID;

        // Get the specific application with all details
        // Only show if the employee is assigned to this MA
        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.id', $id)
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select(
                'leave_details.*',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.department_id as department_id',
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
            return redirect()->route('ma.index')->with('error', 'Application not found.');
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

        // Decide which blade to use and readonly status
        $readonly = false;
        $view = 'ma.show';
        switch ($application->status_id) {
            case 4: // Processing MA
                $view = 'ma.show';
                $readonly = false;
                break;
            case 5: // Processing HOD
                $view = 'ma.hod';
                $readonly = true;
                break;
            case 6: // Processing Dean
                $view = 'ma.dean';
                $readonly = true;
                break;
            case 7: // Processing VC
            case 8: // VC Approved
                $view = 'ma.vc';
                $readonly = true;
                break;
            default:
                $view = 'ma.show';
                $readonly = true;
        }

        // Fetch Department Head details for the application's department
        $departmentHead = null;
        if ($application && isset($application->department_id)) {
            $departmentHead = DB::table('department_heads')
                ->join('employees', 'department_heads.emp_no', '=', 'employees.employee_no')
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
                ->leftJoin('categories as head_positions','department_heads.head_position', '=', 'head_positions.id')
                ->where('department_heads.department_id', $application->department_id)
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

        return view($view, compact('application', 'readonly', 'travelDetails', 'departmentHead'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $maUserId = self::MA_USER_ID;

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('ma.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table for validation/processing
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // Prepare new remark by appending to existing remarks
        $newRemark = '';
        if ($request->remark) {
            $timestamp = now()->format('Y-m-d');
            $newRemark = "\n\n[MA Review - " . $timestamp . "]\n" . $request->remark;
        }

        // Update status to Processing HOD (status_id = 5)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'status_id' => 9, // Processing HOD
                'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                'remark' => DB::raw("CONCAT(COALESCE(remark, ''), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);

        return redirect()->route('ma.index');
    }

    public function return(Request $request, $id)
    {
        
        $request->validate([
            'remark' => 'required|string|max:1000',
        ], [
            'remark.required' => 'Remarks are required when returning an application.'
        ]);

        $maUserId = self::MA_USER_ID;

        $application = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->where('leave_details.id', $id)
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select('leave_details.*')
            ->first();

        if (!$application) {
            return redirect()->route('ma.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table for validation/processing
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // Prepare new remark by appending to existing remarks
        $timestamp = now()->format('Y-m-d');
        $newRemark = "\n\n[MA Return - " . $timestamp . "]\n" . $request->remark;

        // Update status to Returned (form_status = 3, status_id = 2 for Rejected)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'form_status' => 3, // Returned
                'status_id' => 2, // Rejected
                'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                'remark' => DB::raw("CONCAT(COALESCE(remark, ' '), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);

        return redirect()->route('ma.index')->with('success', 'Application returned to user successfully.');
    }

    public function dashboard(Request $request)
    {
        $maUserId = self::MA_USER_ID;

        // Get search and sort parameters
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'applied_date'); // default sort by applied_date
        $sortOrder = $request->get('sort_order', 'desc'); // default descending

        // Build the query
        $query = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->whereIn('leave_details.form_status', [2, 3]) // Complete/Submitted
            ->whereIn('leave_details.status_id', [1, 2, 4, 5, 6, 7,8])
            ->where('employees.assign_ma_user_id', $maUserId); // Filter by assigned MA

        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('leave_details.reference_no', 'LIKE', "%{$search}%")
                  ->orWhere('employees.employee_no', 'LIKE', "%{$search}%")
                  ->orWhere('employees.initials', 'LIKE', "%{$search}%")
                  ->orWhere('employees.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('departments.department_name', 'LIKE', "%{$search}%")
                  ->orWhere('faculties.faculty_name', 'LIKE', "%{$search}%")
                  ->orWhere('leave_types.name', 'LIKE', "%{$search}%")
                  ->orWhere('statuses.status', 'LIKE', "%{$search}%");
            });
        }

        // Apply sorting
        $validSortColumns = [
            'applied_date' => 'leave_details.applied_date',
            'reference_no' => 'leave_details.reference_no',
            'empno' => 'employees.employee_no',
            'name' => 'employees.last_name',
            'department' => 'departments.department_name',
            'faculty' => 'faculties.faculty_name',
            'leave_type' => 'leave_types.name',
            'status' => 'statuses.status'
        ];

        if (array_key_exists($sortBy, $validSortColumns)) {
            $query->orderBy($validSortColumns[$sortBy], $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('leave_details.applied_date'); // default fallback
        }

        $applications = $query->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'categories.category_name as title',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status',
                'leave_details.status_id',
                'leave_details.remark'
            )
            ->get();

        // Load specific fields from otherLeavesDetails table for each application
        foreach ($applications as $application) {
            $otherLeaveDetails = DB::table('otherLeavesDetails')
                ->where('reference_no', $application->reference_no)
                ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
                ->first();

            if ($otherLeaveDetails) {
                $application->leave_type_id = $otherLeaveDetails->leave_type_id;
                $application->from_date = $otherLeaveDetails->from_date;
                $application->end_date = $otherLeaveDetails->end_date;
                $application->duration = $otherLeaveDetails->duration;
                $application->leave_document = $otherLeaveDetails->leave_document;
                $application->consent_letter = $otherLeaveDetails->consent_letter;
            }
        }

        // Applications for status sidebar (status_id 4-8) assigned to this MA
        $statusApplications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->whereBetween('leave_details.status_id', [4, 8])
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'leave_types.name as leave_type',
                'leave_details.status_id'
            )
            ->get();

        return view('ma.dashboardDemo', compact('applications', 'statusApplications', 'search', 'sortBy', 'sortOrder'));
    }

    public function dashboardVcApproved()
    {
        $maUserId = self::MA_USER_ID;

        // Get all applications with status_id = 8 (VC Approved) assigned to this MA
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 8) // VC Approved
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'leave_details.applied_date',
                'leave_types.name as leave_type',
                'statuses.status',
                'leave_details.remark'
            )
            ->get();

        // Load specific fields from otherLeavesDetails table for each application
        foreach ($applications as $application) {
            $otherLeaveDetails = DB::table('otherLeavesDetails')
                ->where('reference_no', $application->reference_no)
                ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
                ->first();

            if ($otherLeaveDetails) {
                $application->leave_type_id = $otherLeaveDetails->leave_type_id;
                $application->from_date = $otherLeaveDetails->from_date;
                $application->end_date = $otherLeaveDetails->end_date;
                $application->duration = $otherLeaveDetails->duration;
                $application->leave_document = $otherLeaveDetails->leave_document;
                $application->consent_letter = $otherLeaveDetails->consent_letter;
            }
        }

        return view('ma.vcapproved', compact('applications'));
    }

    public function statusPage()
    {
        $maUserId = self::MA_USER_ID;

        $statusApplications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->whereBetween('leave_details.status_id', [4, 8])
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_details.id',
                'leave_details.reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'leave_types.name as leave_type',
                'leave_details.status_id'
            )
            ->get();

        // Load specific fields from otherLeavesDetails table for each application
        foreach ($statusApplications as $application) {
            $otherLeaveDetails = DB::table('otherLeavesDetails')
                ->where('reference_no', $application->reference_no)
                ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
                ->first();

            if ($otherLeaveDetails) {
                $application->leave_type_id = $otherLeaveDetails->leave_type_id;
                $application->from_date = $otherLeaveDetails->from_date;
                $application->end_date = $otherLeaveDetails->end_date;
                $application->duration = $otherLeaveDetails->duration;
                $application->leave_document = $otherLeaveDetails->leave_document;
                $application->consent_letter = $otherLeaveDetails->consent_letter;
            }
        }

        return view('ma.status', compact('statusApplications'));
    }

    public function showHod($id)
    {
        return $this->show($id);
    }

    public function showDean($id)
    {
        return $this->show($id);
    }

    public function showVc($id)
    {
        return $this->show($id);
    }

    public function studyLeavePage(Request $request)
    {
        $maUserId = self::MA_USER_ID;
        
        // Get search and sort parameters
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'applied_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Build base query
        $query = DB::table('study_leaves')
            ->leftJoin('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'employees.employee_no', '=', 'study_leaves.empno')
            ->leftJoin('statuses', 'statuses.stat_id', '=', 'study_leave_approvals.status_id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->where('study_leaves.is_draft', false) // Only non-draft applications
            ->where(function($q) {
            $q->where('study_leave_approvals.status_id', 4) // Processing MA (status_id = 4)
              ->orWhere('study_leave_approvals.status_id', 8) // VC Checked - awaiting council approval
              ->orWhereNotNull('study_leave_approvals.ma_empno') // Or MA has processed it
              ->orWhereNull('study_leave_approvals.id'); // Or no approval record yet (newly submitted)
            })
            ->whereNotIn('study_leave_approvals.status_id', [1]); // Exclude approved applications
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('study_leaves.reference_no', 'LIKE', "%{$search}%")
                  ->orWhere('employees.employee_no', 'LIKE', "%{$search}%")
                  ->orWhere('employees.initials', 'LIKE', "%{$search}%")
                  ->orWhere('employees.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('departments.department_name', 'LIKE', "%{$search}%")
                  ->orWhere('faculties.faculty_name', 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw("COALESCE(statuses.status, 'Pending')"), 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        $validSortColumns = [
            'applied_date' => 'study_leaves.created_at',
            'reference_no' => 'study_leaves.reference_no',
            'empno' => 'employees.employee_no',
            'name' => 'employees.last_name',
            'department' => 'departments.department_name',
            'faculty' => 'faculties.faculty_name',
            'status' => DB::raw("COALESCE(statuses.status, 'Pending')")
        ];

        if (array_key_exists($sortBy, $validSortColumns)) {
            $query->orderBy($validSortColumns[$sortBy], $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('study_leaves.created_at');
        }
        
        $studyLeaveApplications = $query->select(
                'study_leaves.id as id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leaves.created_at as applied_date',
                'study_leave_approvals.status_id as approval_status_id',
                DB::raw("COALESCE(statuses.status, 'Pending') as status")
            )
            ->get();

        // Calculate statistics
        $allApplications = DB::table('study_leaves')
            ->leftJoin('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'employees.employee_no', '=', 'study_leaves.empno')
            ->where('employees.assign_ma_user_id', $maUserId)
            ->where('study_leaves.is_draft', false)
            ->select('study_leave_approvals.status_id', 'study_leaves.created_at')
            ->get();

        $statistics = [
            'total' => $allApplications->count(),
            'pending' => $allApplications->where('status_id', 4)->count() + $allApplications->whereNull('status_id')->count(),
            'reviewed' => $allApplications->whereNotNull('status_id')->where('status_id', '>', 4)->count(),
            'this_month' => $allApplications->where('created_at', '>=', now()->startOfMonth())->count()
        ];

        return view('ma.studyLeave', compact('studyLeaveApplications', 'search', 'sortBy', 'sortOrder', 'statistics'));
    }
    
    public function showStudyLeaveExtensionsPage()
    {
        
        $maUserId = self::MA_USER_ID;
        $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions_approvals.study_leave_extension_id', '=', 'study_leave_extensions.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->where(function($query) {
                $query->where('statuses.status', 'Processing MA')
                      ->orWhereNotNull('study_leave_extensions_approvals.ma_empno');
            })
            ->where('statuses.stat_id', '!=', 1) // Exclude status_id = 1
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
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

        return view('ma.showStudyLeaveExtensions', compact('extensionApplications'));
    }
    
    public function studyLeaveProgressReportsPage()
    {
     
        $maUserId = self::MA_USER_ID;

        $progressReportApplications = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports_approval.study_leave_progress_report_id', '=', 'study_leave_progress_reports.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->where('statuses.stat_id', '!=', 1) // Exclude status_id = 1
            ->where(function($query) {
                $query->where('statuses.status', 'Processing MA')
                      ->orWhere('statuses.status', 'Editing') // Include reports returned to user
                      ->orWhereNotNull('study_leave_progress_reports_approval.ma_empno');
            })
            ->select(
                'study_leave_progress_reports.id as progress_report_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_progress_reports.submitted_date',
                'study_leave_progress_reports.due_date',
                'statuses.status as status'
            )
            ->get();

        return view('ma.showStudyLeaveProgressReport', compact('progressReportApplications'));
    }
    public function studyLeaveStatusPage()
    {
         $maUserId = self::MA_USER_ID;

         $statusApplications = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'employees.employee_no', '=', 'study_leaves.empno')
            ->join('statuses', 'statuses.stat_id', '=', 'study_leave_approvals.status_id') // adjusted to status_id
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->whereBetween('study_leave_approvals.status_id', [4, 8])
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->orderByDesc('study_leaves.created_at')
            ->select(
                'study_leaves.id as reference_no',
                'employees.id as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'employees.created_at as applied_date',
                'study_leave_approvals.status_id as status_id'
            )
            ->get();

         

        

        return view('ma.studyleavestatus', compact('statusApplications'));
    }
    public function showStudyLeave($id)
    {
        $maUserId = self::MA_USER_ID;

        // Get the specific study leave application with all details
        // Only show if the employee is assigned to this specific MA
       $draft_study_leave = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leaves.id', $id)
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select(
                'study_leaves.*',
                'employees.employee_no as employee_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.department_id as department_id',
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile',
                'employees.nic',
                'statuses.status',
                'study_leave_approvals.status_id as approval_status_id',
                'employees.email as email',
                'study_leaves.scholarship_source as scholarship_source',
                'study_leaves.scholarship_amount as scholarship_amount',
                'study_leaves.project_name as project_name',
                'study_leaves.nominee_teaching_empno as nominee_teaching_empno',
                'study_leaves.nominee_admin_empno as nominee_admin_empno',
                'study_leaves.nominee_other_empno as nominee_other_empno',
                'study_leaves.self_funding_declaration as self_funding_declaration',
                'study_leaves.placement_letter as placement_letter'
            )
            ->first();
           

        $user = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', $draft_study_leave->employee_no)
            ->select(
                'employees.employee_no as empno',
                'employees.nic',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'employees.email as email',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile',
                'employees.assign_ma_user_id'
            )
            ->first();

        if (!$draft_study_leave) {
            return redirect()->route('ma.studyleave')->with('error', 'Application not found.');
        }

        // Fetch Department Head details for the application's department
        /*
        $departmentHead = null;
                    if ($draft_study_leave && isset($draft_study_leave->department_id)) {
                        $departmentHead = DB::table('department_heads')
                ->join('employees', 'department_heads.emp_no', '=', 'employees.employee_no')
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
*/
        // Fetch Department Head details for the Academic Establishment Department
        $academic_establishmnet_department_id = self::ACADEMIC_ESTABLISHMENT_DEPARTMENT_ID;
        $departmentHead = null;
        
        if ($draft_study_leave && $academic_establishmnet_department_id) {
            $departmentHead = DB::table('department_heads')
                ->leftjoin('employees', 'department_heads.emp_no', '=', 'employees.employee_no') //covert it to join
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
                ->leftJoin('categories as head_positions','department_heads.head_position', '=', 'head_positions.id')
                ->where('department_heads.department_id', $academic_establishmnet_department_id)
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
       

        // Decide which blade to use and readonly status
        $readonly = false;
        $view = 'ma.showStudyLeave';

      

        $totalStudyLeaveDays = $this->calculateTotalStudyLeaveDays($draft_study_leave->employee_no);
        $totalStudyLeaveDuration = $this->calculateTotalStudyLeaveMonths($draft_study_leave->employee_no);

        $from = new \DateTime($draft_study_leave->study_leave_from);
        $to = new \DateTime($draft_study_leave->study_leave_to);
        $interval = $from->diff($to);
        $requistedStudyLeaveDays = $interval->days + 1; // +1 to include both start and end dates

        return view("ma.showStudyLeave", compact('draft_study_leave', 'readonly','departmentHead','user', 'totalStudyLeaveDays', 'requistedStudyLeaveDays', 'totalStudyLeaveDuration'));
    }
     /**
     * Calculate total study leave days taken by an employee.
     */
    public function calculateTotalStudyLeaveDays($emp_no)
    {
        $totalDays = 0;
        $previousLeaves=StudyLeave::where('empno', $emp_no)
        ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
        ->where('study_leave_approvals.is_draft', false)
        ->where('study_leave_approvals.status_id', 1)
        ->select('study_leaves.study_leave_from as study_leave_from','study_leaves.study_leave_to as study_leave_to');

        if($previousLeaves->count() > 0){
            foreach($previousLeaves->get() as $leave){
                $leave_id = $leave->id;
                $extensions = StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $leave_id)
                ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
                ->where('study_leave_extensions_approvals.status_id', 1)
                ->select('study_leave_extensions.new_end_date')
                ->OrderBy('study_leave_extensions.id', 'desc')
                ->first();
                if($extensions){
                    $extended_end_date = $extensions->new_end_date;
                } else {
                    $extended_end_date = $leave->study_leave_to;
                }
                $from = new \DateTime($leave->study_leave_from);
                $to = new \DateTime($extended_end_date);
                $interval = $from->diff($to);
                $totalDays += $interval->days + 1; // +1 to include both start and end dates
                
            }
            return $totalDays;
        }
        else {
            return $totalDays;
        }

    }

    /**
     * Calculate total study leave months and days taken by an employee.
     * Returns ['months' => int, 'days' => int]
     */
    public function calculateTotalStudyLeaveMonths($emp_no)
    {
        $totalMonths = 0;
        $totalDays = 0;
        $previousLeaves = StudyLeave::where('empno', $emp_no)
            ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
            ->where('study_leave_approvals.is_draft', false)
            ->where('study_leave_approvals.status_id', 1)
            ->select('study_leaves.id', 'study_leaves.study_leave_from as study_leave_from', 'study_leaves.study_leave_to as study_leave_to')
            ->get();

        foreach ($previousLeaves as $leave) {
            $extensions = StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $leave->id)
                ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
                ->where('study_leave_extensions_approvals.status_id', 1)
                ->select('study_leave_extensions.new_end_date')
                ->orderBy('study_leave_extensions.id', 'desc')
                ->first();

            $endDate = $extensions ? $extensions->new_end_date : $leave->study_leave_to;
            $from = new \DateTime($leave->study_leave_from);
            $to = new \DateTime($endDate);
            $interval = $from->diff($to);
            $totalMonths += ($interval->y * 12) + $interval->m;
            $totalDays += $interval->d;
        }

        // Carry over excess days into months
        $totalMonths += intdiv($totalDays, 30);
        $totalDays = $totalDays % 30;

        return ['months' => $totalMonths, 'days' => $totalDays];
    }

    public function approveStudyLeave(Request $request, $id)
    {
       
        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $maUserId = self::MA_USER_ID;
/*
        $application = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leaves.id', $id)
            ->where('study_leaves.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select('study_leaves.*')
            ->first();

        if (!$application) {
            return redirect()->route('ma.studyleave')->with('error', 'Application not found.');
        }

        // Prepare new remark by appending to existing remarks
        
        $newRemark = '';
        if ($request->remark) {
            $timestamp = now()->format('Y-m-d');
            $newRemark = "\n\n[MA Review - " . $timestamp . "]\n" . $request->remark;
        }
*/
        // Update status to Processing HOD (status_id = 5)
        /*
        DB::table('study_leaves')
            ->where('id', $id)
            ->update([
                'status_id' => 9, // Processing HOD
                'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                //'remark' => DB::raw("CONCAT(COALESCE(remark, ''), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);
            */
            DB::table('study_leave_approvals')
            ->where('study_leave_id', $id)
            ->update([
                'status_id' => 9, // Processing HOD
                'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                //'remark' => DB::raw("CONCAT(COALESCE(remark, '', '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);
    

        return redirect()->route('ma.studyleave');
    }

    public function returnStudyLeave(Request $request, $id)
    {
       // dd('returnStudyLeave function called');
        
        $request->validate([
            'remark' => 'required|string|max:1000',
        ], [
            'remark.required' => 'Remarks are required when returning an application.'
        ]);

        $maUserId = self::MA_USER_ID;

        $application = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id' )
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leaves.id', $id)
            ->where('study_leave_approvals.status_id', 4) // Processing MA
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select('study_leaves.*')
            ->first();

        if (!$application) {
            return redirect()->route('ma.studyleave')->with('error', 'Application not found.');
        }

        // Prepare new remark by appending to existing remarks
        $timestamp = now()->format('Y-m-d');
        $newRemark = "\n\n[MA Return - " . $timestamp . "]\n" . $request->remark;

        // Update status to Returned (status_id = 2 for Rejected)
        /*
        DB::table('study_leaves')
            ->where('id', $id)
            ->update([
                'status_id' => 3, // Edited to Returned
               'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                'ma_remarks' => DB::raw("CONCAT(COALESCE(ma_remarks, ''), '" . addslashes($newRemark) . "')"),
                //'remark' => DB::raw("CONCAT(COALESCE(remark, ' '), '" . addslashes($newRemark) . "')"),
                // Mark as draft for resubmission
                'updated_at' => now()
            ]);
            */

             DB::table('study_leave_approvals')
            ->where('study_leave_id', $id)
            ->update([
                'status_id' => 3, // Edited to Returned
               'ma_empno' => self::MA_USER_ID, // Record which MA processed this
                'ma_remarks' => DB::raw("CONCAT(COALESCE(ma_remarks, ''), '" . addslashes($newRemark) . "')"),
                //'remark' => DB::raw("CONCAT(COALESCE(remark, ' '), '" . addslashes($newRemark) . "')"),
                // Mark as draft for resubmission
                'updated_at' => now()
            ]);

        return redirect()->route('ma.studyleave')->with('success', 'Application returned to the user successfully.');
    }

    /**
     * Show study leave extension details
     */
    public function showExtension($extension_id)
    {
        $maUserId = self::MA_USER_ID;

        // Get the complete study leave data for the extension
        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->select(
                'study_leave_extensions.id as extension_id',
                'study_leave_extensions.study_leave_id',
                'study_leave_extensions.old_end_date',
                'study_leave_extensions.new_end_date',
                'study_leave_extensions.reason_for_extension',
                'study_leave_extensions_approvals.status_id as extension_status_id',
                'study_leave_extensions_approvals.ma_remarks',
                'study_leaves.*', // Get all study leave fields
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials',
                'employees.nic',
                'employees.email',
                'employees.mobile_no as mobile',
                'employees.department_id',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'statuses.status'
            )
            ->first();

        if (!$extension) {
            return redirect()->route('ma.studyleave')->with('error', 'Extension application not found.');
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

        // Get Department Head details
        $departmentHead = null;
        if ($extension->department_id) {
            $departmentHead = DB::table('department_heads')
                ->join('employees', 'department_heads.emp_no', '=', 'employees.employee_no')
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
                ->leftJoin('categories as head_positions', 'department_heads.head_position', '=', 'head_positions.id')
                ->where('department_heads.department_id', $extension->department_id)
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

        // Calculate duration for display
        $oldDate = \Carbon\Carbon::parse($extension->old_end_date);
        $newDate = \Carbon\Carbon::parse($extension->new_end_date);
        $durationDays = $oldDate->diffInDays($newDate);
        $durationMonths = round($durationDays / 30, 1);

        $readonly = false;

        return view('ma.study_leave.study_leave_extension_view_form', compact('extension', 'user', 'departmentHead', 'readonly', 'draft_study_leave', 'durationDays', 'durationMonths'));
    }

    /**
     * Forward extension to HOD
     */
    public function forwardExtension(Request $request, $extension_id)
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000',
            'ma_recommend' => 'required|in:0,1',
            'ma_not_recommend_reason' => 'nullable|required_if:ma_recommend,0|string|max:1000',
        ]);

        $maUserId = self::MA_USER_ID;

        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('employees.assign_ma_user_id', $maUserId)
            ->select('study_leave_extensions.*')
            ->first();

        if (!$extension) {
            return redirect()->route('ma.studyleave')->with('error', 'Extension application not found.');
        }

        // Prepare remark
        $newRemark = '';
        if ($request->remark) {
            $timestamp = now()->format('Y-m-d');
            $newRemark = "\n\n[MA Review - " . $timestamp . "]\n" . $request->remark;
        }

        // Update status to Processing HOD Academic Establishment/Registrar (status_id = 9)
        DB::table('study_leave_extensions_approvals')
            ->where('study_leave_extension_id', $extension_id)
            ->update([
                'status_id' => 9, // Processing HOD Academic Establishment/Registrar
                'ma_empno' => self::MA_USER_ID,
                'ma_recommend' => $request->ma_recommend,
                'ma_not_recommend_reason' => $request->ma_recommend == 0 ? $request->ma_not_recommend_reason : null,
                'ma_remarks' => DB::raw("CONCAT(COALESCE(ma_remarks, ''), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);

        return redirect()->route('ma.studyleave')->with('success', 'Extension request forwarded to HOD Academic Establishment successfully.');
    }

    /**
     * Return extension to user
     */
    public function returnExtension(Request $request, $extension_id)
    {
        $request->validate([
            'remark' => 'required|string|max:1000',
        ], [
            'remark.required' => 'Remarks are required when returning an extension request.'
        ]);

        $maUserId = self::MA_USER_ID;

        $extension = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_extensions.id', $extension_id)
            ->where('employees.assign_ma_user_id', $maUserId)
            ->select('study_leave_extensions.*')
            ->first();
           

        if (!$extension) {
            return redirect()->route('ma.studyleave')->with('error', 'Extension application not found.');
        }

        // Prepare remark
        $timestamp = now()->format('Y-m-d');
        $newRemark = "\n\n[MA Return - " . $timestamp . "]\n" . $request->remark;

        // Update status to Returned (status_id = 3)
        DB::table('study_leave_extensions_approvals')
            ->where('study_leave_extension_id', $extension_id)
            ->update([
                'status_id' => 3, // Returned
                'ma_empno' => self::MA_USER_ID,
                'ma_remarks' => DB::raw("CONCAT(COALESCE(ma_remarks, ''), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);

        return redirect()->route('ma.studyleave')->with('success', 'Extension request returned to user successfully.');
    }

    /**
     * Show progress report for review
     */
    public function showProgressReport($progress_report_id)
    {
        $maUserId = self::MA_USER_ID;

        // Fetch the progress report with related study leave and employee details
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('study_leave_progress_reports_approval', 'study_leave_progress_reports_approval.study_leave_progress_report_id', '=', 'study_leave_progress_reports.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('employees.assign_ma_user_id', $maUserId) // Ensure MA has access
            ->select(
                'study_leave_progress_reports.*',
                'study_leave_progress_reports.id as progress_report_id',
                'study_leave_progress_reports_approval.approval_status_id',
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
            return redirect()->route('ma.studyleave')->with('error', 'Progress report not found.');
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

        // Get department head information for forwarding
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

        return view('ma.study_leave.study_leave_progress_report_view_form', compact('progressReport', 'user', 'readonly', 'approvedReports', 'departmentHead', 'draft_study_leave'));
    }

    /**
     * Forward progress report to HOD
     */
    public function approveProgressReport(Request $request, $progress_report_id)
    {
        $request->validate([
            'remark' => 'nullable|string|max:1000',
        ]);

        $maUserId = self::MA_USER_ID;

        // Verify the progress report belongs to this MA
       
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('employees.assign_ma_user_id', $maUserId)
            ->select('study_leave_progress_reports.*')
            ->first();
          
        if (!$progressReport) {
            return redirect()->route('ma.studyleave')->with('error', 'Progress report not found.');
        }

        // Prepare remarkh
        $newRemark = '';
        if ($request->remark) {
            $timestamp = now()->format('Y-m-d');
            $newRemark = "\n\n[MA Review - " . $timestamp . "]\n" . $request->remark;
        }

        // Update status to Processing HOD (status_id = 5)
        DB::table('study_leave_progress_reports')
            ->where('id', $progress_report_id)
            ->update([
                'status_id' => 5, // Processing HOD
                'remark' => DB::raw("CONCAT(COALESCE(remark, ''), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);

        // Update approval record - forward to HOD Academic Establishment
        StudyLeaveProgressReportsApproval::where('study_leave_progress_report_id', $progress_report_id)
            ->update([
                'ma_empno' => self::MA_USER_ID,
                'approval_status_id' => 9, // Processing HOD Academic Establishment
                'updated_at' => now()
            ]);

        return redirect()->route('ma.studyleave')->with('success', 'Progress report forwarded to HOD Academic Establishment successfully.');
    }

    /**
     * Return progress report to user
     */
    public function returnProgressReport(Request $request, $progress_report_id)
    {
        $request->validate([
            'remark' => 'required|string|max:1000',
        ], [
            'remark.required' => 'Remarks are required when returning a progress report.'
        ]);

        $maUserId = self::MA_USER_ID;

        // Verify the progress report belongs to this MA
        $progressReport = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_progress_reports.id', $progress_report_id)
            ->where('employees.assign_ma_user_id', $maUserId)
            ->select('study_leave_progress_reports.*')
            ->first();

        if (!$progressReport) {
            return redirect()->route('ma.studyleave')->with('error', 'Progress report not found.');
        }

        // Prepare remark
        $timestamp = now()->format('Y-m-d');
        $newRemark = "\n\n[MA Returned - " . $timestamp . "]\n" . $request->remark;

        // Update status to Returned (status_id = 3)
        DB::table('study_leave_progress_reports')
            ->where('id', $progress_report_id)
            ->update([
                'status_id' => 3, // Returned
                'remark' => DB::raw("CONCAT(COALESCE(remark, ''), '" . addslashes($newRemark) . "')"),
                'updated_at' => now()
            ]);

        // Update ma_empno in approval record
        StudyLeaveProgressReportsApproval::where('study_leave_progress_report_id', $progress_report_id)
            ->update([
                'ma_empno' => self::MA_USER_ID,
                'updated_at' => now()
            ]);

        return redirect()->route('ma.studyleave')->with('success', 'Progress report returned to user successfully. User can now remove and re-upload the progress report.');
    }

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
        //$currentEmpNo = (string) session('empno');
        //$isOwner = ($currentEmpNo === $fileEmpNo);
        $maUserId = self::MA_USER_ID;
        $studyLeaveRecord = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leave_progress_reports.document_path', $relativePath)
            ->select('employees.assign_ma_user_id as ma_user_id')
            ->first();
           
            if($studyLeaveRecord->ma_user_id == $maUserId){
                $isOwner = true;
            } else {
                $isOwner = false;
            }
        
        

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
     * Finalize study leave with committee and council approval
     */
    public function approveWithCouncil(Request $request, $id)
    {
        $request->validate([
            'study_leave_decision' => 'required|in:approved,not_approved',
            'ma_approve_leave_committee' => 'required_if:study_leave_decision,approved|nullable|in:0,1',
            'ma_leave_committee_number' => 'required_if:study_leave_decision,approved|nullable|string|max:255',
            'ma_leave_committee_date' => 'required_if:study_leave_decision,approved|nullable|date',
            'ma_approve_council' => 'required_if:study_leave_decision,approved|nullable|in:0,1',
            'ma_council_number' => 'required_if:study_leave_decision,approved|nullable|string|max:255',
            'ma_council_date' => 'required_if:study_leave_decision,approved|nullable|date',
        ]);

        $maUserId = self::MA_USER_ID;

        // Verify the application is in VC checked status (status_id = 8)
        $application = DB::table('study_leaves')
            ->join('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leaves.id', $id)
            ->where('study_leave_approvals.status_id', 8) // VC Checked
            ->where('employees.assign_ma_user_id', $maUserId)
            ->select('study_leaves.*', 'study_leave_approvals.id as approval_id')
            ->first();

        if (!$application) {
            return redirect()->route('ma.studyleave')->with('error', 'Application not found or not in the correct status.');
        }

        $isApproved = $request->study_leave_decision === 'approved';
        $statusId = $isApproved ? 1 : 2; // 1 = Approved, 2 = Rejected

        $updateData = [
            'status_id' => $statusId,
            'updated_at' => now(),
        ];

        if ($isApproved) {
            $updateData['ma_approve_leave_committee'] = $request->ma_approve_leave_committee;
            $updateData['ma_leave_committee_number'] = $request->ma_leave_committee_number;
            $updateData['ma_leave_committee_date'] = $request->ma_leave_committee_date;
            $updateData['ma_approve_council'] = $request->ma_approve_council;
            $updateData['ma_council_number'] = $request->ma_council_number;
            $updateData['ma_council_date'] = $request->ma_council_date;
        }

        DB::table('study_leave_approvals')
            ->where('id', $application->approval_id)
            ->update($updateData);

        $message = $isApproved
            ? 'Study leave application has been approved and finalized successfully.'
            : 'Study leave application has been rejected.';

        return redirect()->route('ma.studyleave')->with('success', $message);
    }

    public function studyLeaveAccepted(Request $request)
    {
          $maUserId = self::MA_USER_ID;
        
        // Get search and sort parameters
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'applied_date');
        $sortOrder = $request->get('sort_order', 'desc');
        
        // Build base query
        $query = DB::table('study_leaves')
            ->leftJoin('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'employees.employee_no', '=', 'study_leaves.empno')
            ->leftJoin('statuses', 'statuses.stat_id', '=', 'study_leave_approvals.status_id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            ->where('study_leaves.is_draft', false) // Only non-draft applications
            ->where(function($q) {
                $q->where('study_leave_approvals.status_id', 1); // Processing MA (status_id = 1)
                 
                 
            });
        
        // Apply search filter if provided
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('study_leaves.reference_no', 'LIKE', "%{$search}%")
                  ->orWhere('employees.employee_no', 'LIKE', "%{$search}%")
                  ->orWhere('employees.initials', 'LIKE', "%{$search}%")
                  ->orWhere('employees.last_name', 'LIKE', "%{$search}%")
                  ->orWhere('departments.department_name', 'LIKE', "%{$search}%")
                  ->orWhere('faculties.faculty_name', 'LIKE', "%{$search}%")
                  ->orWhere(DB::raw("COALESCE(statuses.status, 'Pending')"), 'LIKE', "%{$search}%");
            });
        }
        
        // Apply sorting
        $validSortColumns = [
            'applied_date' => 'study_leaves.created_at',
            'reference_no' => 'study_leaves.reference_no',
            'empno' => 'employees.employee_no',
            'name' => 'employees.last_name',
            'department' => 'departments.department_name',
            'faculty' => 'faculties.faculty_name',
            'status' => DB::raw("COALESCE(statuses.status, 'Pending')")
        ];

        if (array_key_exists($sortBy, $validSortColumns)) {
            $query->orderBy($validSortColumns[$sortBy], $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('study_leaves.created_at');
        }
        
        $acceptedApplications = $query->select(
                'study_leaves.id as id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leaves.created_at as applied_date',
                'study_leave_approvals.status_id as approval_status_id',
                DB::raw("COALESCE(statuses.status, 'Pending') as status")
            )
            ->get();

        // Calculate statistics
        $allApplications = DB::table('study_leaves')
            ->leftJoin('study_leave_approvals', 'study_leave_approvals.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'employees.employee_no', '=', 'study_leaves.empno')
            ->where('employees.assign_ma_user_id', $maUserId)
            ->where('study_leaves.is_draft', false)
            ->select('study_leave_approvals.status_id', 'study_leaves.created_at')
            ->get();

        $statistics = [
            'total' => $allApplications->count(),
            'pending' => $allApplications->where('status_id', 4)->count() + $allApplications->whereNull('status_id')->count(),
            'reviewed' => $allApplications->whereNotNull('status_id')->where('status_id', '>', 4)->count(),
            'this_month' => $allApplications->where('created_at', '>=', now()->startOfMonth())->count()
        ];


        return view('ma.studyLeaveAccept', compact('acceptedApplications', 'search', 'sortBy', 'sortOrder', 'statistics'));
    }
    public function showStudyLeaveExtensionsAcceptedPage()
    {
       $maUserId = self::MA_USER_ID;

         $extensionApplications = DB::table('study_leave_extensions')
            ->join('study_leaves', 'study_leave_extensions.study_leave_id', '=', 'study_leaves.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->join('study_leave_extensions_approvals', 'study_leave_extensions_approvals.study_leave_extension_id', '=', 'study_leave_extensions.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->where(function($query) {
                $query->where('statuses.stat_id', 1)
                      ->orWhereNotNull('study_leave_extensions_approvals.ma_empno');
            })
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
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

       

        return view('ma.showStudyLeaveExtensionsAccept',  compact('extensionApplications'));
    }
    public function studyLeaveProgressReportsAcceptedPage()
    {
        
        $maUserId = self::MA_USER_ID;

         $progressReportApplications = DB::table('study_leave_progress_reports')
            ->join('study_leaves', 'study_leave_progress_reports.study_leave_id', '=', 'study_leaves.id')
            ->join('study_leave_progress_reports_approval', 'study_leave_progress_reports_approval.study_leave_progress_report_id', '=', 'study_leave_progress_reports.id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->where('employees.assign_ma_user_id', $maUserId) // Filter by assigned MA
            //->whereNotNull('study_leave_progress_reports.submitted_date') // Only submitted reports
            //->where('statuses.status', 'Processing MA') // Filter for MA Processing status
            ->where(function($query) {
                $query->where('statuses.stat_id', 1)
                     
                      ->orWhereNotNull('study_leave_progress_reports_approval.ma_empno');
            })
            ->select(
                'study_leave_progress_reports.id as progress_report_id',
                'study_leaves.id as study_leave_id',
                'study_leaves.reference_no as reference_no',
                'employees.employee_no as empno',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'study_leave_progress_reports.submitted_date',
                'study_leave_progress_reports.due_date',
                'statuses.status as status'
            )
            ->get();

        return view('ma.showStudyLeaveProgressReportAccept', compact('progressReportApplications'));

       
   
    
}
}
