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

        // Store basic info in session for later steps

         session(['study_leave' => ['employee_no' => session('empno'),'passport_no' => $validatedData['passport_no'], 'passport_validity' => $validatedData['passport_validity']]]); 

      
        // redirect Details of the Study Leave

        return redirect()->route('StudyLeave.Details.create')->with('success', 'Basic information saved successfully!');
    }

    public function createDetails()
    {
      
        
       

       return view('StudyLeave.createDetails');
       
       
      
    
    }
    public function storeDetails(Request $request)
    {
        
        // Validate the incoming request data
       
        // Validation rules for Study Leave details - adjust fields to match your createDetails.blade.php
        $rules = [
            'leave_type' => 'required|string|max:100',
            'leave_payment_type' => 'required|string|max:100',
           'study_leave_from' => 'required|date',
           'study_leave_to' => 'required|date|after_or_equal:study_leave_from',
            'degree_title' => 'required|string|max:255',
            'university_institute' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'field_of_study' => 'required|string|max:255',
            'study_program_details' => 'required|string|max:1000',
            'funding_type' => 'required|string|max:255',
            'scholarship_source' => 'nullable|string|max:1000',
            'scholarship_amount' => 'nullable|numeric|min:0',
           'project_name' => 'nullable|string|max:255',
            'any_other_details' => 'nullable|string|max:1000',
            'air_passage_request' => 'required|in:yes,no',
            'warm_cloth_allowance_request' => 'required|in:yes,no',
        ];

        $validatedData = $request->validate($rules);
        
        session(['study_leave' => array_merge(session('study_leave', []), $validatedData)]);

        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message
      

        return redirect()->route('StudyLeave.PreviousStudyLeaves.create')->with('success', 'Study leave details saved successfully!');
    }

     /**
     * Show the form for creating a previous study leave .
     */

    public function createPreviousStudyLeaves(){

       
    
         return view('StudyLeave.createPreviousStudyLeaves');

    }

    public function storePreviousStudyLeaves(Request $request)
    {
        
        // Validate the incoming request data
        
        // Normalize fields that may arrive as arrays (use the first non-empty element)
        $fields = [
            'prev_leave_type',
            'prev_university',
            'prev_duration_from',
            'prev_duration_to',
            'prev_with_pay',
            'prev_completed',
        ];

        $input = $request->all();

        foreach ($fields as $field) {
            if (array_key_exists($field, $input) && is_array($input[$field])) {
                $first = null;
                foreach ($input[$field] as $val) {
                    if ($val !== null && $val !== '') {
                        $first = $val;
                        break;
                    }
                }
                if ($first === null) {
                    $first = $input[$field][0] ?? null;
                }
                $input[$field] = $first;
            }
        }

        // Put normalized values back into the request so the validator gets scalars
        $request->merge($input);
        $validatedData = $request->validate([
            'prev_leave_type' => 'nullable|string|max:100',
            'prev_university' => 'nullable|string|max:255',
            'prev_duration_from' => 'nullable|date',
            'prev_duration_to' => 'nullable|date|after_or_equal:prev_duration_from',
            'prev_with_pay' => 'nullable|string|max:50',
            'prev_completed' => 'nullable|string|in:Completed,Not Completed'
            
        ]);
       
        session(['study_leave' => array_merge(session('study_leave', []), $validatedData)]);
        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message

        return redirect()->route('StudyLeave.WorkCoveringPersons.create')->with('success', 'Previous study leave details saved successfully!');
    }
    public function createWorkCoveringPersons()
    {
      
       return view('StudyLeave.createWorkCoveringPersons');
       
       
      
    
    }

    public function storeWorkCoveringPersons(Request $request)
    {
       
        
        // Validate the incoming request data
        
        $validatedData = $request->validate([
            'nominee_teaching_empno' => 'required|string|max:255',
            'nominee_admin_empno' => 'required|string|max:255',
            'nominee_other_empno' => 'required|string|max:255',
            
        ]);
        
        session(['study_leave' => array_merge(session('study_leave', []), $validatedData)]);
        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message

        return redirect()->route('StudyLeave.Handeling.create')->with('success', 'Work covering persons details saved successfully!');
    }
    public function createHandeling()
    {
        
      

       return view('StudyLeave.createHandeling');
       
       
      
    
    }
    public function storeHandeling(Request $request)
    { 
        
        
        // Validate the incoming request data
        
        $validatedData = $request->validate([
            'library_and_property_handling' => 'required|string|max:100',
            'loan_handling' => 'required|string|max:100',
            
            
        ]);
        session(['study_leave' => array_merge(session('study_leave', []), $validatedData)]);

        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message

        return redirect()->route('StudyLeave.Summary.show')->with('success', 'Handling of details saved successfully!');
    }
    public function showSummary()
    {
        
      
       // Retrieve all relevant data for the summary view

       return view('StudyLeave.showSummary');
       
       
      
    
    }
    public function submitApplication(Request $request)
    {
        // Here you can handle the submission logic, e.g., save all data to the database
        $data = session('study_leave', []);
        dd($data);

        if (empty($data)) {
            return redirect()->back()->with('error', 'No study leave data found in session.');
        }

        // Ensure employee no is present
        $data['employee_no'] = $data['employee_no'] ?? session('empno');

        // Add timestamps (assuming table uses them)
        $data['created_at'] = now();
        $data['updated_at'] = now();

        try {
            // Insert into study_leave_record and get inserted id
            $insertId = DB::table('study_leave_record')->insertGetId($data);
        } catch (\Exception $e) {
            // Log or return error
            return redirect()->back()->with('error', 'Failed to save study leave: ' . $e->getMessage());
        }

        // Clear session data for study leave after successful save
        session()->forget('study_leave');

        // Optionally flash the inserted id to the session
        session()->flash('study_leave_record_id', $insertId);


        // For demonstration, we'll just redirect back with a success message
        return redirect()->route('firstPage')->with('success', 'Study leave application submitted successfully!');
    }

    public function getEmployeeInfo($emp_no)
    {                               
       //dd($emp_no);
        // Fetch employee info from the database
        $employee = DB::table('employees')
            ->where('employee_no', $emp_no)
            ->select('employee_no', DB::raw("CONCAT(initials, ' ', last_name) as name"))
            ->first();

        if ($employee) {
            return response()->json([
                'success' => true,
                'data' => $employee
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
    }                                                                                                     

}
