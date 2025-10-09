<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OtherLeavesDetail;
use App\Models\LeaveRequestDetail;


class StudyLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       
        $user = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', session('empno'))
            ->select(
                'employees.employee_no as empno',
                'employees.nic',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'employees.email as email',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile'
            )
            ->first();

        if (!$user)
            abort(404, 'User not found');
       

        $leaveTypes = DB::table('leave_types')->get();
        $statuses = DB::table('statuses')->pluck('status', 'stat_id');

        // Check if we're editing an existing record
        $leave = null;
        $otherLeave = null;
        $remark = null;
        $academicYear = null;

        
            // Get the otherleavesdetails record for this reference
            //$otherLeave = OtherLeavesDetail::where('reference_no', $leave->reference_no)->first();

            // If it's a returned form, get the remark
            
        // Note: For new applications, $leave and $otherLeave will be null and the form will work without a database record

        // Only fetch previous leaves with status_id = 1 (approved) for the current academic year
        $previousLeaves = DB::table('leave_details')
            ->join('otherleavesdetails', 'leave_details.reference_no', '=', 'otherleavesdetails.reference_no')
            ->join('leave_types', 'otherleavesdetails.leave_type_id', '=', 'leave_types.id')
            ->join('statuses', 'leave_details.status_id', '=', 'statuses.stat_id')
            ->where('leave_details.nic', $user->nic)
            ->where('leave_details.status_id', 1)
            ->whereYear('leave_details.applied_date', now()->year)
            ->orderByDesc('leave_details.applied_date')
            ->select(
                'leave_types.name as leave_type',
                'otherleavesdetails.from_date',
                'otherleavesdetails.end_date as to_date',
                'otherleavesdetails.duration',
                'statuses.status',
                'leave_details.applied_date'
            )
            ->get();

        // Load existing travel details if editing
        $travelDetails = [];
        if ($leave) {
            $travelDetails = LeaveRequestDetail::where('reference_no', $leave->reference_no)->get();
        }

       //return view('StudyLeave.create', compact('user', 'leaveTypes', 'previousLeaves', 'leave', 'otherLeave', 'remark', 'travelDetails', 'academicYear'));
       
       
       //return view('create', compact('user', 'leaveTypes', 'previousLeaves', 'leave', 'otherLeave', 'remark', 'travelDetails', 'academicYear'));
       return redirect()->route('StudyLeave.BasicInfo.create');
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Show the form for creating a basic information .
     */
    public function createBasicInfo()
    {
       
        $user = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', session('empno'))
            ->select(
                'employees.employee_no as empno',
                'employees.nic',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'employees.email as email',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile'
            )
            ->first();

        if (!$user)
            abort(404, 'User not found');
       

       return view('StudyLeave.createBasicInfo', compact('user'));
       
       
      
    
    }
    /**
     * Store a basic information in storage.
     */
    public function storeBasicInfo(Request $request)
    {
        
        // Validate the incoming request data
        $validatedData = $request->validate([
            'empno' => 'required|string|max:20',
            'name_with_initials' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'department' => 'required|string|max:100',
            'faculty' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'passport_no' => 'string|max:50',
            'passport_validity' => 'string|max:50'
            
        ]);

        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message

        return redirect()->route('StudyLeave.Details.create')->with('success', 'Basic information saved successfully!');
    }

    public function createDetails()
    {
       
        $user = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', session('empno'))
            ->select(
                'employees.employee_no as empno',
                'employees.nic',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name_with_initials"),
                'employees.name_denoted_by_initials as names_denoted_by_initials',
                'employees.email as email',
                'departments.department_name as department',
                'faculties.faculty_name as faculty',
                'designations.designation_name as designation',
                'employees.mobile_no as mobile'
            )
            ->first();

        if (!$user)
            abort(404, 'User not found');
       

       return view('StudyLeave.createDetails', compact('user'));
       
       
      
    
    }

}
