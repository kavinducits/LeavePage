<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\OtherLeavesDetail;
use App\Models\LeaveRequestDetail;
use App\Models\StudyLeave;

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
    public function createStudyLeave()
    {
        
        $user=null;
        $drafts=null;
        $previousLeaves=null;
        $hasActiveDraft=false;
        $previousLeaves = $this->getStudyLeaves(session('empno'));
        
        
       
       
       return view('StudyLeave.createStudyLeave',compact('user', 'drafts', 'previousLeaves', 'hasActiveDraft'));
    
    }
    public function storeStudyLeave(Request $request)
    {
        //dd('here');
        //
        $academicYear = $request->input('academic_year');
        // Store the academic year in session or pass it to the next step as needed
        session(['study_leave' => ['academic_year' => $academicYear]]);
        return redirect()->route('StudyLeave.BasicInfo.create')->with('success', 'Academic year saved successfully!');
    }
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
                'employees.mobile_no as mobile',
                'employees.assign_ma_user_id'
            )
            ->first();

        if (!$user)
            abort(404, 'User not found');

        
        session(['ma_user_id' => $user->assign_ma_user_id]);

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

        // Merge basic info into existing study_leave session data
        $studyLeave = session('study_leave', []);
        $studyLeave = array_merge($studyLeave, [
            'employee_no' => session('empno'),
            'passport_no' => $validatedData['passport_no'] ?? null,
            'passport_validity' => $validatedData['passport_validity'] ?? null,
        ]);
        session(['study_leave' => $studyLeave]);

      
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
            'air_passage_request' => 'nullable|in:yes,no',
            'warm_cloth_allowance_request' => 'nullable|in:yes,no',
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

         $previousLeaves = $this->getStudyLeaves(session('empno'));
         

         return view('StudyLeave.createPreviousStudyLeaves', compact('previousLeaves'));

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
        // Here you would typically save the complete study leave application to the database
        $studyLeaveData = session('study_leave', []);
        $employee=$this->getEmployee(session('empno'));
        $ma_id=$employee->assign_ma_user_id;
       

        // Save $studyLeaveData to the database as needed
        StudyLeave::create([
            'empno' => $studyLeaveData['employee_no'] ?? null,
            'passport_no' => $studyLeaveData['passport_no'] ?? null,
            'passport_validity' => $studyLeaveData['passport_validity'] ?? null,
            'leave_type' => $studyLeaveData['leave_type'] ?? null,
            'leave_payment_type' => $studyLeaveData['leave_payment_type'] ?? null,
            'study_leave_from' => $studyLeaveData['study_leave_from'] ?? null,
            'study_leave_to' => $studyLeaveData['study_leave_to'] ?? null,
            'degree_title' => $studyLeaveData['degree_title'] ?? null,
            'university_institute' => $studyLeaveData['university_institute'] ?? null,
            'country' => $studyLeaveData['country'] ?? null,
            'field_of_study' => $studyLeaveData['field_of_study'] ?? null,
            'study_program_details' => $studyLeaveData['study_program_details'] ?? null,
            'funding_type' => $studyLeaveData['funding_type'] ?? null,
            'any_other_details' => $studyLeaveData['any_other_details'] ?? null,
            'air_passage_request' => $studyLeaveData['air_passage_request'] ?? null,
            'warm_cloth_allowance_request' => $studyLeaveData['warm_cloth_allowance_request'] ?? null,
            'scholarship_source' => $studyLeaveData['scholarship_source'] ?? null,
            'scholarship_amount' => $studyLeaveData['scholarship_amount'] ?? null,
            'project_name' => $studyLeaveData['project_name'] ?? null,
            'nominee_teaching_empno' => $studyLeaveData['nominee_teaching_empno'] ?? null,
            'nominee_admin_empno' => $studyLeaveData['nominee_admin_empno'] ?? null,
            'nominee_other_empno' => $studyLeaveData['nominee_other_empno'] ?? null,
            'library_and_property_handling' => $studyLeaveData['library_and_property_handling'] ?? null,
            'loan_handling' => $studyLeaveData['loan_handling'] ?? null,
            'ma_empno' => $ma_id ?? null,
            'hod_empno' => $studyLeaveData['hod_empno'] ?? null,
            'hod_staff_adequacy_recommendation' => $studyLeaveData['hod_staff_adequacy_recommendation'] ?? null,
            'hod_teaching_coverage_recommendation' => $studyLeaveData['hod_teaching_coverage_recommendation'] ?? null,
            'hod_one_year_service_verification' => $studyLeaveData['hod_one_year_service_verification'] ?? null,
            'hod_leave_recommendation_status' => $studyLeaveData['hod_leave_recommendation_status'] ?? null,
            'hod_not_recommended_reason' => $studyLeaveData['hod_not_recommended_reason'] ?? null,
            'dean_leave_recommendation_status' => $studyLeaveData['dean_leave_recommendation_status'] ?? null,
            'dean_not_recommended_reason' => $studyLeaveData['dean_not_recommended_reason'] ?? null,
            'vc_empno' => $studyLeaveData['vc_empno'] ?? null,
            'vc_recommend_submit_to_committee' => $studyLeaveData['vc_recommend_submit_to_committee'] ?? null,
            'vc_council_covering_approval_status' => $studyLeaveData['vc_council_covering_approval_status'] ?? null,
            'status_id' => 4 ?? null, // Assuming '4' indicates a newly submitted application to Management Assistant(MA)
           
        ]);
        // Clear the session data after submission
        $request->session()->forget('study_leave');
        // For demonstration, we'll just redirect back with a success message
        return redirect()->route('StudyLeave.create')->with('success', 'Study leave application submitted successfully!');
    }
    
    /**
     * Display a specific study leave application for viewing/approval
     * 
     * @param string $id - Can be either reference_no or database id
     * @return \Illuminate\View\View
     */
    public function showStudyLeaveApplication($id)
    {
       
       
        // Try to find by reference_no first, then by id
        $application = DB::table('study_leaves')
            ->leftJoin('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->leftJoin('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->leftJoin('employees as teaching_nominee_t', 'teaching_nominee_t.employee_no', '=', 'study_leaves.nominee_teaching_empno')
            ->leftJoin('employees as admin_nominee_t', 'admin_nominee_t.employee_no', '=', 'study_leaves.nominee_admin_empno')
            ->leftJoin('employees as other_nominee_t', 'other_nominee_t.employee_no', '=', 'study_leaves.nominee_other_empno')
            
            ->where('study_leaves.id', $id)
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
                DB::raw("CONCAT(other_nominee_t.initials, ' ', other_nominee_t.last_name) as other_nominee_name"),
                'departments.id as department_id'
                
            )
            ->first();

       
        if (!$application) {
            abort(404, 'Study leave application not found');
        }

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

//dd($departmentHead);

        return view('ma.showStudyLeave', compact('application', 'departmentHead'));
    }

    public function getEmployeeInfo($emp_no)
    {                               
       //dd($emp_no);
        // Fetch employee info from the database
        $employee = $this->getEmployee($emp_no);

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
    public function getEmployee($emp_no)
    {                               
       //dd($emp_no);
        // Fetch employee info from the database
        $employee = DB::table('employees')
            ->where('employee_no', $emp_no)
            ->select('employee_no', DB::raw("CONCAT(initials, ' ', last_name) as name"), 'assign_ma_user_id')
            ->first();

        return $employee;
    }  
    public function getStudyLeaves($emp_no)
    {                               
       //dd($emp_no);
        // Fetch employee info from the database
        $previousLeaves = DB::table('study_leaves')
            ->where('empno', $emp_no)
            ->select('id','degree_title','university_institute','study_leave_from','study_leave_to','leave_payment_type','study_leaves.created_at','status_id','status')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id' )
            ->get();

        return $previousLeaves;
    }                                                                                                           

}
