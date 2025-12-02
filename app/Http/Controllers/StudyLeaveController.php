<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\OtherLeavesDetail;
use App\Models\LeaveRequestDetail;
use App\Models\StudyLeave;
use PHPUnit\Framework\Constraint\Count;

class StudyLeaveController extends Controller
{


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return redirect()->route('StudyLeave.BasicInfo.create');
    }


    /**
     * Show the form for creating a basic information .
     */
    public function createStudyLeave()
    {

        $currentDate = date('Y-m-d');

        $user = null;
        $isEnableStudyLeaveRequiste = false;

        $previousLeaves = null;
        $hasActiveDraft = false;
        $approvedLeaves = $this->getStudyLeaves(session('empno'))->where('status_id', 1);
        $returnLeaves = $this->getStudyLeaves(session('empno'))->where('status_id', 3);
        $rejectLeaves = $this->getStudyLeaves(session('empno'))->where('status_id', 2);

        $previousLeaves = $approvedLeaves->merge($returnLeaves)->merge($rejectLeaves);
        $drafts = StudyLeave::where('empno', session('empno'))
            ->where('is_draft', true)
            ->first();

        if ($drafts) {
            $hasActiveDraft = true;
        }

        $approvedLeavesCount = StudyLeave::where('empno', session('empno'))
            ->where('status_id', 1)
            ->where('is_draft', false)
            ->select(
                DB::raw('COUNT("id") as approved_count')

            )
            ->first();
        $approvedLeavesInProgress = StudyLeave::where('empno', session('empno'))
            ->where('status_id', 1)
            ->where('is_draft', false)

            ->whereDate('study_leave_to', '>=', $currentDate)
            ->count();
        $allStudyLeavesCount = StudyLeave::where('empno', session('empno'))
            ->select(
                DB::raw('COUNT("id") as total_count')
            )
            ->first();

        if (($allStudyLeavesCount->total_count - $approvedLeavesCount->approved_count) == 0  && $approvedLeavesInProgress == 0) {
            $isEnableStudyLeaveRequiste = true;
        }

        return view('StudyLeave.createStudyLeave', compact('user', 'drafts', 'previousLeaves', 'hasActiveDraft', 'isEnableStudyLeaveRequiste', 'currentDate'));
    }
    public function storeStudyLeave(Request $request)
    {

        $academicYear = $request->input('academic_year');
        // Store the academic year in session or pass it to the next step as needed
        session(['study_leave' => ['academic_year' => $academicYear]]);

        return redirect()->route('StudyLeave.BasicInfo.create');
    }
    public function createBasicInfo()
    {
        $readonly = false;

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

        $draft_study_leave = StudyLeave::where('empno', $user->empno)
            ->where('is_draft', true)
            ->select(
                "passport_no",
                "passport_validity"
            )
            ->first();

        return view('StudyLeave.createBasicInfo', compact('user', 'draft_study_leave', 'readonly'));
    }
    /**
     * Store a basic information in storage.
     */
    public function storeBasicInfo(Request $request)
    {

        $academicYear = session('study_leave.academic_year');

        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Update existing draft
            $draft->update([
                'academic_year' => $academicYear,
                'is_draft' => true
            ]);
        } else {
            // Create new draft record
            StudyLeave::create([
                'empno' => session('empno'),
                'academic_year' => $academicYear,
                'is_draft' => true,

            ]);
        }
        $this->updateBasicInfo($request);

        // redirect Details of the Study Leave

        return redirect()->route('StudyLeave.Details.create')->with('success', 'Basic information saved successfully!');
    }
    public function exiteBasicInfo(Request $request)
    {

        $this->updateBasicInfo($request);

        return redirect()->route('StudyLeave.create')->with('success', 'Basic information saved successfully!');
    }
    public function updateBasicInfo($request)
    {
       
        // Check if there's an existing draft for this employee
        $draft = StudyLeave::where('empno', session('empno'))
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Update existing draft
            $draft->update([
               'is_draft' => true,
                'current_step' => 1
            ]);
        } else {
            // Create new draft record
            StudyLeave::create([
                'empno' => session('empno'),
                'is_draft' => true,
                'current_step' => 1
            ]);
        }
        return;
    }

    public function createDetails()
    {
        $readonly = false;

        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->select(
                "leave_type",
                "leave_payment_type",
                "study_leave_from",
                "study_leave_to",
                "degree_title",
                "university_institute",
                "country",
                "field_of_study",
                "study_program_details",
                "funding_type",
                "scholarship_source",
                "scholarship_amount",
                "project_name",
                "any_other_details",
                "air_passage_request",
                "warm_cloth_allowance_request",
                "self_funding_declaration",
                "placement_letter"
            )
            ->first();

        return view('StudyLeave.createDetails', compact('draft_study_leave', 'readonly'));
    }
    public function storeDetails(Request $request)
    {

        $this->updateDetails($request);

        return redirect()->route('StudyLeave.WorkCoveringPersons.create')->with('success', 'Study leave details saved successfully!');
    }

    public function updateDetails($request)
    {
        // This function can be used to update details if needed
        // Validate the incoming request data

        // Validation rules for Study Leave details - adjust fields to match your createDetails.blade.php

        $rules = array(
            // 'leave_type' => 'required|string|max:100',
            'study_location' => 'required|string|max:100',
            'passport_no' => 'required_if:study_location,Abroad|nullable|string|max:50',
            'passport_validity' => 'required_if:study_location,Abroad|nullable|date',
            'leave_payment_type' => 'required|string|max:100',
            'study_leave_from' => 'required|date',
            'study_leave_to' => 'required|date|after_or_equal:study_leave_from',
            'degree_title' => 'required|string|max:255',
            'university_institute' => 'required|string|max:255',
            'country' => 'required_if:study_location,Abroad|string|max:100',
            'field_of_study' => 'required|string|max:255',
            'study_program_details' => 'nullable|string|max:1000',
            'funding_type' => 'required|string|max:100',
            'scholarship_source' => 'required_if:funding_type,scholarship|string|max:1000',
            'scholarship_amount' => 'required_if:scholarship_source,agency|nullable|numeric|min:10',
            'project_name' => 'required_if:scholarship_source,project|nullable|string|max:255',
            'any_other_details' => 'nullable|string|max:1000',
            'air_passage_request' => 'required_if:funding_type,self|string|in:yes,no',
            'warm_cloth_allowance_request' => 'required_if:funding_type,self|string|in:yes,no',
            'self_funding_declaration' => 'required_if:funding_type,self|file|mimes:pdf|max:10240',
            'placement_letter' => 'required|file|mimes:pdf|max:10240',
            'loan_handling' => 'required_if:leave_payment_type,Without Pay|string|max:100',
        );

       

        try {
            $validatedData = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            
            // Optionally dump to see immediately during development
            dd([
                'validation_errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            
            
        }
      

        // Handle file upload BEFORE storing in session
        if ($request->hasFile('self_funding_declaration')) {

            $file = $request->file('self_funding_declaration');
            $empno = session('study_leave.employee_no') ?? session('empno');

            // Get or create draft to get the study leave ID
            $draft = StudyLeave::where('empno', $empno)
                ->where('is_draft', true)
                ->first();

            if (!$draft) {
                // Create draft if it doesn't exist yet
                $draft = StudyLeave::create([
                    'empno' => $empno,
                    'is_draft' => true
                ]);
            }

            $studyLeaveId = $draft->id;

            // Generate filename: empno_studyleaveid_self_funding_declaration.pdf
            $filename = $empno . '_' . $studyLeaveId . '_self_funding_declaration.pdf';

            // Store the file in storage/app/private/self_funding_declaration (private folder)

            $path = $file->storeAs('self_funding_declaration', $filename);

            // Store the path directly for database storage
            $validatedData['self_funding_declaration'] = $path;

            // Update the existing draft with the file path
            if ($draft) {
                $draft->update([
                    'self_funding_declaration' => $path,
                    'is_draft' => true
                ]);
            }
        } else {
            // Remove file field from validated data if no file uploaded
            unset($validatedData['self_funding_declaration']);
        }
        if ($request->hasFile('placement_letter')) {

            $file = $request->file('placement_letter');
            $empno = session('study_leave.employee_no') ?? session('empno');

            // Get or create draft to get the study leave ID
            $draft = StudyLeave::where('empno', $empno)
                ->where('is_draft', true)
                ->first();

            if (!$draft) {
                // Create draft if it doesn't exist yet
                $draft = StudyLeave::create([
                    'empno' => $empno,
                    'is_draft' => true
                ]);
            }

            $studyLeaveId = $draft->id;

            // Generate filename: empno_studyleaveid_placement_letter.pdf
            $filename = $empno . '_' . $studyLeaveId . '_placement_letter.pdf';

            // Store the file in storage/app/private/placement_letter (private folder)
            $path = $file->storeAs('placement_letter', $filename);

            // Store the path directly for database storage
            $validatedData['placement_letter'] = $path;

            if ($draft) {
                $draft->update([
                    'placement_letter' => $path,
                    'is_draft' => true,
                    'current_step' => 2,
                ]);
            }
        } else {
            // Remove file field from validated data if no file uploaded
            unset($validatedData['placement_letter']);
        }

        // Now store in session (file is already processed and stored as path)
        session(['study_leave' => array_merge(session('study_leave', []), $validatedData)]);

        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message
        $empno = session('study_leave.employee_no') ?? session('empno');
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Prepare update data
            $updateData = [
                // 'leave_type' => $validatedData['leave_type'],
                'leave_payment_type' => $validatedData['leave_payment_type'],
                'study_leave_from' => $validatedData['study_leave_from'],
                'study_leave_to' => $validatedData['study_leave_to'],
                'degree_title' => $validatedData['degree_title'],
                'university_institute' => $validatedData['university_institute'],
                'country' => $validatedData['country'],
                'field_of_study' => $validatedData['field_of_study'],
                'study_program_details' => $validatedData['study_program_details'] ?? null,
                'funding_type' => $validatedData['funding_type'],
                'scholarship_source' => $validatedData['scholarship_source'] ?? null,
                'scholarship_amount' => $validatedData['scholarship_amount'] ?? null,
                'project_name' => $validatedData['project_name'] ?? null,
                'any_other_details' => $validatedData['any_other_details'] ?? null,
                'air_passage_request' => $validatedData['air_passage_request'] ?? null,
                'warm_cloth_allowance_request' => $validatedData['warm_cloth_allowance_request'] ?? null,
                'loan_handling' => $validatedData['loan_handling'] ?? null,
            ];

            // Update existing draft
            $draft->update($updateData);
        }
    }
    public function exiteDetails(Request $request)
    {

        $this->updateDetails($request);

        return redirect()->route('StudyLeave.create')->with('success', 'Study leave details saved successfully!');
    }

  
    public function createWorkCoveringPersons()
    {
        $readonly = false;
        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->select(
                "nominee_teaching_empno",
                "nominee_admin_empno",
                "nominee_other_empno"
            )
            ->first();

        return view('StudyLeave.createWorkCoveringPersons', compact('draft_study_leave', 'readonly'));
    }

    public function storeWorkCoveringPersons(Request $request)
    {
        $this->updateWorkCoveringPersons($request);
        return redirect()->route('StudyLeave.Summary.show')->with('success', 'Work covering persons details saved successfully!');
    }

    public function exiteWorkCoveringPersons(Request $request)
    {

        $this->updateWorkCoveringPersons($request);

        return redirect()->route('StudyLeave.create')->with('success', 'Work covering persons details saved successfully!');
    }
    public function updateWorkCoveringPersons($request)
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
        $empno = session('study_leave.employee_no') ?? session('empno');
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Update existing draft
            $draft->update([
                'nominee_teaching_empno' => $validatedData['nominee_teaching_empno'],
                'nominee_admin_empno' => $validatedData['nominee_admin_empno'],
                'nominee_other_empno' => $validatedData['nominee_other_empno'],
                'current_step' => 3,
                'is_draft' => true,
            ]);
        }
    }
   
   
    public function showSummary()
    {
        $readonly = true;

        // Retriev, compact('draft_study_leave')e all relefor the summary view

        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->select(
                "library_and_property_handling",
                "loan_handling"
            )
            ->first();

        return view('StudyLeave.showSummary', compact('draft_study_leave', 'readonly'));
    }
    public function submitApplication(Request $request)
    {

        // Validate the incoming request data

        $this->updateSummary($request);
        $empno = session('study_leave.employee_no') ?? session('empno');
        // Find and update the draft to mark it as submitted
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            $draft->update([
                'is_draft' => false,
                'status_id' => 4, // Assuming '4' is the status ID for 'Submitted'
                'reference_no' => $this->generateReferenceNumber(),

            ]);
        }

        // Clear the session data after successful submission
        $request->session()->forget('study_leave');

        return redirect()->route('StudyLeave.create')->with('success', 'Study leave application submitted successfully! Your application is now under review.');
    }


    public function exitSummary(Request $request)
    {


        $this->updateSummary($request);

        return redirect()->route('StudyLeave.create')->with('success', 'Handling of details saved successfully!');
    }
    public function updateSummary($request)
    {
    
       
        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message
        $empno = session('study_leave.employee_no') ?? session('empno');

        // Find and update the draft to mark it as submitted
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            $draft->update([
                'is_draft' => true,
               // 'library_and_property_handling' => $validatedData['library_and_property_handling'],
               // 'loan_handling' => $validatedData['loan_handling'],
                'current_step' => 4,
                //'status_id' => 4, // Assuming '4' is the status ID for 'Submitted'

            ]);
        }
    }

    public function getEmployeeInfo($emp_no)
    {

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

        // Fetch employee info from the database
        $employee = DB::table('employees')
            ->join('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employee_no', $emp_no)
            ->select('employee_no', DB::raw("CONCAT(initials, ' ', last_name) as name"), 'assign_ma_user_id', 'designation_name as designation')
            ->first();

        return $employee;
    }
    public function getStudyLeaves($emp_no)
    {

        // Fetch employee info from the database
        $previousLeaves = DB::table('study_leaves')
            ->where('empno', $emp_no)
            ->select('id', 'degree_title', 'university_institute', 'study_leave_from', 'study_leave_to', 'leave_payment_type', 'study_leaves.created_at', 'status_id', 'status', 'reference_no')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->get();

        return $previousLeaves;
    }

    /**
     * Securely serve private study leave files
     * Only allows access if user is authorized (employee who owns it, or approvers)
     */
    public function serveFile($type, $filename)
    {

        // Validate file type
        $allowedTypes = ['self_funding_declaration', 'placement_letter'];
        if (!in_array($type, $allowedTypes)) {
            abort(404, 'Invalid file type');
        }

        // Construct the file path
        $filePath = storage_path('app/private/' . $type . '/' . $filename);

        // Check if file exists
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }

        // Extract employee number from filename (format: empno_studyleaveid_type.pdf)
        $parts = explode('_', $filename);
        $fileEmpNo = $parts[0] ?? null;

        // Authorization check: Allow if:
        // 1. User is the employee who owns the file
        // 2. User is an approver (MA, HOD, Dean, VC) - you can add more checks here
        $currentEmpNo = (string) session('empno');

        $isOwner = ($currentEmpNo === $fileEmpNo);

        // Check if user is an approver by checking if they have ma_user_id, hod role, etc.
        // For now, we'll allow access if they're the owner or if they have a session
        // You can add more sophisticated role checks here
        $isApprover = !empty(session('ma_user_id')) || !empty(session('hod_id')) || !empty(session('dean_id'));

        if (!$isOwner) {
            abort(403, 'Unauthorized access to this file');
        }

        // Serve the file
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"'
        ]);
    }
    public function deleteStudyLeaveDraft($id, Request $request)
    {

        $empno = session('empno');

        // Find the draft study leave for the current employee
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Delete the draft
            $draft->delete();

            return redirect()->route('StudyLeave.create')->with('success', 'Draft study leave application deleted successfully.');
        } else {
            return redirect()->route('StudyLeave.create')->with('error', 'No draft study leave application found to delete.');
        }
    }
    public function showEditeStudyLeaveForm($id)
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

        // Get the specific study leave application with all details
        // Only show if the employee is assigned to this specific MA
        $draft_study_leave = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->where('study_leaves.id', $id)
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
                'employees.email as email',
                'study_leaves.scholarship_source as scholarship_source',
                'study_leaves.scholarship_amount as scholarship_amount',
                'study_leaves.project_name as project_name',
                'study_leaves.nominee_teaching_empno as nominee_teaching_empno',
                'study_leaves.nominee_admin_empno as nominee_admin_empno',
                'study_leaves.nominee_other_empno as nominee_other_empno',
                'study_leaves.ma_remarks as ma_remarks',

            )
            ->first();

        if (!$draft_study_leave) {
            return redirect()->route('ma.studyleave')->with('error', 'Application not found.');
        }

        // Fetch Department Head details for the application's department
        $departmentHead = null;
        if ($draft_study_leave && isset($draft_study_leave->department_id)) {
            $departmentHead = DB::table('department_heads')
                ->join('employees', 'department_heads.emp_no', '=', 'employees.employee_no')
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
                ->leftJoin('categories as head_positions', 'department_heads.head_position', '=', 'head_positions.id')
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

        // Decide which blade to use and readonly status
        $readonly = false;
        $view = 'ma.showStudyLeave';


        return view('StudyLeave.returnStudyLeaveForm', compact('user', 'draft_study_leave', 'departmentHead', 'readonly'));
    }

    public function updateEditeStudyLeave(Request $request, $id)
    {
        // Validate the incoming request data

        $rules = array(
            'empno' => 'required|string|max:20',
            'name_with_initials' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'department' => 'required|string|max:100',
            'faculty' => 'required|string|max:100',
            'designation' => 'required|string|max:100',
            'passport_no' => 'nullable|string|max:50',
            'passport_validity' => 'nullable|date',
            'leave_payment_type' => 'required|string|max:100',
            'study_leave_from' => 'required|date',
            'study_leave_to' => 'required|date|after_or_equal:study_leave_from',
            'degree_title' => 'required|string|max:255',
            'university_institute' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'field_of_study' => 'required|string|max:255',
            'study_program_details' => 'nullable|string|max:1000',
            'funding_type' => 'required|string|max:100',
            'scholarship_source' => 'nullable|string|max:1000',
            'scholarship_amount' => 'nullable|numeric|min:0',
            'project_name' => 'nullable|string|max:255',
            'any_other_details' => 'nullable|string|max:1000',
            'air_passage_request' => 'nullable|in:yes,no',
            'warm_cloth_allowance_request' => 'nullable|in:yes,no',
            'self_funding_declaration' => 'nullable|file|mimes:pdf|max:10240',
            'placement_letter' => 'nullable|file|mimes:pdf|max:10240',
            'nominee_teaching_empno' => 'required|string|max:255',
            'nominee_admin_empno' => 'required|string|max:255',
            'nominee_other_empno' => 'required|string|max:255',
            'library_and_property_handling' => 'required|string|max:100',
            'loan_handling' => 'required|string|max:100',
        );

        $validatedData = $request->validate($rules);
        $studyLeave = StudyLeave::find($id);
        if ($studyLeave) {
            $studyLeave->update($validatedData + ['status_id' => 4, 'is_draft' => false]);

            return redirect()->route('StudyLeave.create')->with('success', 'Study leave application updated successfully.');
        } else {
            return redirect()->route('StudyLeave.create')->with('error', 'Study leave application not found.');
        }
    }

    /**
     * Generate a new reference number following the pattern: <empNo><year><04><No>
     */
    private function generateReferenceNumber()
    {
        $currentYear = date('Y');

        // Get the highest ID from leave_details table and add 1
        $lastId = DB::table('study_leaves')->max('id') ?? 0;
        $newNo = $lastId + 1;

        // Format: <empNo><year><04><No>
        return "{$currentYear}04{$newNo}";
    }

    public function showStudyLeave($id)
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

        // Get the specific study leave application with all details
        // Only show if the employee is assigned to this specific MA
        $draft_study_leave = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->where('study_leaves.id', $id)
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
                'employees.email as email',
                'study_leaves.scholarship_source as scholarship_source',
                'study_leaves.scholarship_amount as scholarship_amount',
                'study_leaves.project_name as project_name',
                'study_leaves.nominee_teaching_empno as nominee_teaching_empno',
                'study_leaves.nominee_admin_empno as nominee_admin_empno',
                'study_leaves.nominee_other_empno as nominee_other_empno',
                'study_leaves.ma_remarks as ma_remarks',

            )
            ->first();

        if (!$draft_study_leave) {
            return redirect()->route('ma.studyleave')->with('error', 'Application not found.');
        }

        // Fetch Department Head details for the application's department
        $departmentHead = null;
        if ($draft_study_leave && isset($draft_study_leave->department_id)) {
            $departmentHead = DB::table('department_heads')
                ->join('employees', 'department_heads.emp_no', '=', 'employees.employee_no')
                ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
                ->leftJoin('categories as head_positions', 'department_heads.head_position', '=', 'head_positions.id')
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

        // Decide which blade to use and readonly status
        $readonly = true;
       
        return view('StudyLeave.viewStudyLeave', compact('user', 'draft_study_leave', 'departmentHead', 'readonly'));
    }

    public function continueDraft($id)
    {
        $empno = session('empno');
        // Find the draft study leave for the current employee
        $current_step = StudyLeave::where('id', $id)

            ->first();
        switch ($current_step->current_step) {
            case 1:
                $route = 'StudyLeave.BasicInfo.create';
                break;
            case 2:
                $route = 'StudyLeave.Details.create';
                break;
            case 3:
                $route = 'StudyLeave.WorkCoveringPersons.create';
                break;
            case 4:
                $route = 'StudyLeave.Summary.show';
                break;
            default:
                $route = 'StudyLeave.create';
                break;
        }

        if ($current_step->current_step !== null) {
            return redirect()->route($route)->with('success', 'Continuing your draft study leave application.');
        } else {
            return redirect()->route('StudyLeave.create')->with('error', 'No draft study leave application found to continue.');
        }
    }

    public function searchAcademicEmployees(Request $request)
    {
        $searchTerm = $request->input('query');

        $employees = DB::table('employees')
            ->where('main_branch_id', 52) // Filter for academic staff first
            ->where(function($query) use ($searchTerm) {
            $query->where('employee_no', 'like', '%' . $searchTerm . '%')
                ->orWhere(DB::raw("CONCAT(initials, ' ', last_name)"), 'like', '%' . $searchTerm . '%')
                ->orWhere(DB::raw("CONCAT(name_denoted_by_initials, ' ', last_name)"), 'like', '%' . $searchTerm . '%');
            })
            ->select('employee_no', DB::raw("CONCAT(initials, ' ', last_name) as name"))
            ->limit(10)
            ->get();

        return response()->json($employees);
    }
}
