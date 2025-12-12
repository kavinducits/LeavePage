<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VCController extends Controller
{
    // Hardcoded VC employee number - change this to switch to a different VC
    private const VC_EMP_NO = 1001; // Example VC emp_no

    public function index()
    {
        // Get all applications for VC review (status_id = 7)
        $applications = DB::table('leave_details')
            ->join('employees', 'leave_details.nic', '=', 'employees.nic')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('otherLeavesDetails', 'leave_details.reference_no', '=', 'otherLeavesDetails.reference_no')
            ->join('leave_types', 'otherLeavesDetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.form_status', 2) // Complete/Submitted
            ->where('leave_details.status_id', 7) // Processing VC
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

        // Get all study leave applications for VC review (status_id = 7)
        // Filter by employees.main_branch_id = 52
        $studyLeaveApplications = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->where('study_leaves.status_id', 7) // Processing VC
            ->where('employees.main_branch_id', 52) // Filter by main_branch_id
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

        return view('vc.index', compact('applications', 'studyLeaveApplications'));
    }

    public function show($id)
    {
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
            ->where('leave_details.status_id', 7)
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
            return redirect()->route('vc.index')->with('error', 'Application not found.');
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

        return view('vc.show', compact('application', 'travelDetails'));
    }

    public function recommend(Request $request, $id)
    {
        $request->validate([
            'vc_recommend_committee' => 'nullable|boolean',
            'vc_approved_council' => 'nullable|boolean',
            'vc_remarks' => 'nullable|string',
        ]);

        // At least one of the two must be set (yes/no)
        if (!isset($request->vc_recommend_committee) && !isset($request->vc_approved_council)) {
            return back()->with('error', 'Please select Yes or No for at least one of the options.');
        }

        $application = DB::table('leave_details')
            ->where('id', $id)
            ->where('form_status', 2)
            ->where('status_id', 7)
            ->first();

        if (!$application) {
            return redirect()->route('vc.index')->with('error', 'Application not found.');
        }

        // Load specific fields from otherLeavesDetails table for validation/processing
        $otherLeaveDetails = DB::table('otherLeavesDetails')
            ->where('reference_no', $application->reference_no)
            ->select('leave_type_id', 'from_date', 'end_date', 'duration', 'leave_document', 'consent_letter')
            ->first();

        // Forward to MA dashboard (status_id = 8, for example)
        DB::table('leave_details')
            ->where('id', $id)
            ->update([
                'vc_recommend_committee' => $request->vc_recommend_committee,
                'vc_approved_council' => $request->vc_approved_council,
                'vc_remarks' => $request->vc_remarks,
                'vc_signature' => 'Dr. A. Silva', // Example signature
                'vc_name' => 'Dr. A. Silva',
                'vc_reviewed_at' => Carbon::now(),
                'vc_checked' => true,
                'vc_empno' => self::VC_EMP_NO, // Record which VC processed this
                'status_id' => 8, // Forwarded to MA dashboard (implement as needed)
                'updated_at' => Carbon::now(),
            ]);

        return redirect()->route('vc.index')->with('success', 'Application forwarded.');
    }

    public function showStudyLeaveApplication($id)
    {
        // Fetch study leave application with main_branch_id filtering
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
            ->where('study_leaves.status_id', 7) // Processing VC
            ->where('employees.main_branch_id', 52) // Filter by main_branch_id
            ->select(
                'study_leaves.*',
                'employees.employee_no as employee_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.email',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'statuses.status',
                'study_leaves.nominee_teaching_empno as teaching_nominee_emp_no',
                DB::raw("CONCAT(teaching_nominee_t.initials, ' ', teaching_nominee_t.last_name) as teaching_nominee_name"),
                'study_leaves.nominee_admin_empno as admin_nominee_emp_no',
                DB::raw("CONCAT(admin_nominee_t.initials, ' ', admin_nominee_t.last_name) as admin_nominee_name"),
                'study_leaves.nominee_other_empno as other_nominee_emp_no',
                DB::raw("CONCAT(other_nominee_t.initials, ' ', other_nominee_t.last_name) as other_nominee_name")
            )
            ->first();

        if (!$draft_study_leave) {
            return redirect()->route('vc.index')->with('error', 'Study leave application not found or not accessible.');
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

        return view('vc.study_leave.view_study_leave_form', compact('draft_study_leave', 'user', 'readonly'));
    }

    public function approveStudyLeave(Request $request, $id)
    {
        // Validate the VC review inputs
        $request->validate([
            'vc_recommend_committee' => 'required|string',
            'vc_approved_council' => 'required|string',
            'vc_not_approve_reason' => 'required_if:vc_approved_council,no|string|nullable',
            'vc_remarks' => 'nullable|string',
        ]);

        // Verify the application belongs to employees with main_branch_id = 52
        $application = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->where('study_leaves.id', $id)
            ->where('study_leaves.status_id', 7) // Processing VC
            ->where('employees.main_branch_id', 52) // Filter by main_branch_id
            ->select('study_leaves.*')
            ->first();

        if (!$application) {
            return redirect()->route('vc.index')->with('error', 'Application not found or not accessible.');
        }

        // Update the study leave application with VC review
        DB::table('study_leaves')
            ->where('id', $id)
            ->update([
                'status_id' => 1, // Approved (final approval by VC)
                'vc_empno' => self::VC_EMP_NO,
                'vc_recommend_submit_to_committee' => $request->vc_recommend_committee,
                'vc_council_covering_approval_status' => $request->vc_approved_council,
                'vc_not_approve_reason' => $request->vc_not_approve_reason,
                'vc_remarks' => $request->vc_remarks,
               // 'vc_reviewed_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
          
        return redirect()->route('vc.index')->with('success', 'Study Leave Application reviewed and approved successfully.');
    }
} 