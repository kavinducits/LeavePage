<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\OtherLeavesDetail;
use App\Models\LeaveRequestDetail;
use App\Models\StudyLeave;
use App\Models\StudyLeaveExtension;
use PHPUnit\Framework\Constraint\Count;

class StudyLeaveController extends Controller
{


    /**
     * Show the form for creating a basic information .
     * Calling Route: StudyLeave.create
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

        if (($allStudyLeavesCount->total_count-$rejectLeaves->count() - $approvedLeavesCount->approved_count) == 0  && $approvedLeavesInProgress == 0 && $this->calculateTotalStudyLeaveDays(session('empno')) <= 1095) {
            $isEnableStudyLeaveRequiste = true;
        }

        $academicYears = $this->academicYears();

        return view('StudyLeave.createStudyLeave', compact('user', 'drafts', 'previousLeaves', 'hasActiveDraft', 'isEnableStudyLeaveRequiste', 'currentDate', 'academicYears'));
    }

    /**
     * Calculate total study leave days taken by an employee.
     */
    public function calculateTotalStudyLeaveDays($emp_no)
    {
        $totalDays = 0;
        $previousLeaves=StudyLeave::where('empno', $emp_no)
        ->where('is_draft', false)
        ->where('status_id', 1)
        ->select('study_leave_from','study_leave_to');

        if($previousLeaves->count() > 0){
            foreach($previousLeaves->get() as $leave){
                $leave_id = $leave->id;
                $extensions = StudyLeaveExtension::where('study_leave_id', $leave_id)
                ->where('status_id', 1)
                ->select('new_end_date')
                ->OrderBy('id', 'desc')
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
     * Store a basic information in storage.
     * Calling Route: StudyLeave.store
     */
    public function storeStudyLeave(Request $request)
    {
        /*

        $academicYear = $request->input('academic_year');
        // Store the academic year in session or pass it to the next step as needed
        session(['study_leave' => ['academic_year' => $academicYear]]);
        */

        return redirect()->route('StudyLeave.BasicInfo.create');
    }

    /**
     * Show the form for creating a basic information .
     * Calling Route: StudyLeave.BasicInfo.create
     */
    public function createBasicInfo()
    {
        $readonly = false;

       $user = $this->getUserBasicInfo(session('study_leave.employee_no') ?? session('empno'));

        if (!$user)
            abort(404, 'User not found');

      
        return view('StudyLeave.createBasicInfo', compact('user',  'readonly'));
    }

    /**
     * Fetch user basic information from the database.
     */

    private function getUserBasicInfo($emp_no)
    {

        // Fetch employee info from the database
        $employee = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', $emp_no)
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

        return $employee;
    }

    /**
     * Store a basic information in storage.
     * Calling Route: StudyLeave.BasicInfo.store
     */
    public function storeBasicInfo(Request $request)
    {

        $academicYear = session('study_leave.academic_year');
        $this->updateBasicInfo($request, $academicYear);
        // redirect Details of the Study Leave
        return redirect()->route('StudyLeave.Details.create')->with('success', 'Basic information saved successfully!');
    }
    /**
     * Save draft and  exit basic information in storage.
     * Calling Route: StudyLeave.BasicInfo.exit
     */
    public function exiteBasicInfo(Request $request)
    {
        $academicYear = session('study_leave.academic_year');

        $this->updateBasicInfo($request, $academicYear);

        return redirect()->route('StudyLeave.create')->with('success', 'Basic information saved successfully!');
    }
    /**
     * Update or create basic information in storage.
     */
    private function updateBasicInfo($request, $academicYear = null)
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
                'current_step' => 1,
                'academic_year' => $academicYear,
            ]);
        }
        return;
    }

    /**
     * Show the form for creating study leave details.
     * Calling Route: StudyLeave.Details.create
     */

    public function createDetails()
    {
        $readonly = false;

        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave =$this->getStudyLeaveDraft($empno);

        $totalDaysStydyLeave = $this->calculateTotalStudyLeaveDays($empno);

        return view('StudyLeave.createDetails', compact('draft_study_leave', 'readonly', 'totalDaysStydyLeave'));
    }
   
    /**
     * Store study leave details in storage.
     * Calling Route: StudyLeave.Details.store
     */  
    public function storeDetails(Request $request)
    {


        $this->updateDetails($request);

        return redirect()->route('StudyLeave.WorkCoveringPersons.create')->with('success', 'Study leave details saved successfully!');
    }
    /**
     * Save draft and  exit study leave details in storage.
     * Calling Route: StudyLeave.Details.exit
     */
    public function exiteDetails(Request $request)
    {

        $this->updateDetails($request);

        return redirect()->route('StudyLeave.create')->with('success', 'Study leave details saved successfully!');
    }


    /**
     * Update study leave details in storage.
     */

    private function updateDetails($request)
    {
        // Validation rules for Study Leave details 
        $rules = array(
            'study_location' => 'required|string|max:100',
            'passport_no' => 'required_if:study_location,Abroad|nullable|string|max:50',
            'passport_validity' => 'required_if:study_location,Abroad|nullable|date',
            'leave_payment_type' => 'required|string|max:100',
            'study_leave_from' => 'required|date',
            'study_leave_to' => 'required|date|after_or_equal:study_leave_from',
            'degree_title' => 'required|string|max:255',
            'university_institute' => 'required|string|max:255',
            'country' => 'required_if:study_location,Abroad|nullable|string|max:100',
            'field_of_study' => 'required|string|max:255',
            'study_program_details' => 'nullable|string|max:1000',
            'funding_type' => 'required|string|max:100',
            'scholarship_source' => 'required_if:funding_type,scholarship|string|max:1000',
            'scholarship_amount' => 'required_if:scholarship_source,agency|nullable|numeric|min:10',
            'project_name' => 'required_if:scholarship_source,project|nullable|string|max:255',
            'any_other_details' => 'nullable|string|max:1000',
            'air_passage_request' => 'required_if:funding_type,self|string|in:yes,no',
            'warm_cloth_allowance_request' => 'required_if:funding_type,self|string|in:yes,no',
            'loan_handling' => 'required_if:leave_payment_type,Without Pay|string|max:100',
        );
        $empno = session('study_leave.employee_no') ?? session('empno');

        // Get or create draft to get the study leave ID
        $draft = $this->getStudyLeaveDraft($empno);
            
        if($draft->placement_letter == null){
               
            $rules['placement_letter'] = 'required|file|mimes:pdf|max:10240';
       }
        if($draft->self_funding_declaration == null){
            $rules['self_funding_declaration'] = 'required_if:funding_type,self|file|mimes:pdf|max:10240';
         }


       
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
           // Store the path directly for database storage
            $validatedData['self_funding_declaration'] = $this->saveUplodedPdfAttachment($file,$empno, $draft, 'self_funding_declaration');
         

            // Update the existing draft with the file path
            if ($draft) {
                $draft->update([
                    'self_funding_declaration' => $validatedData['self_funding_declaration'],
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

            // Store the path directly for database storage
            $validatedData['placement_letter'] = $this->saveUplodedPdfAttachment($file,$empno, $draft, 'placement_letter');

            if ($draft) {
                $draft->update([
                    'placement_letter' => $validatedData['placement_letter'],
                    'is_draft' => true,
                    'current_step' => 2,
                ]);
            }
        } else {
            // Remove file field from validated data if no file uploaded
            unset($validatedData['placement_letter']);
        }


        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message
      

        if($validatedData['study_location'] == 'Sri Lanka'){
            $validatedData['country'] = 'Sri Lanka';
        }

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
                'passport_no' => $validatedData['passport_no'] ?? null,
                'passport_validity' => $validatedData['passport_validity'] ?? null,
            ];

            // Update existing draft
            $draft->update($updateData);
        }
    }

    /**
     * Save uploaded PDF attachment to private storage and return the path.
     */
    private function saveUplodedPdfAttachment($file,$empno, $draft, $type)
    {
         $studyLeaveId = $draft->id;
         
        // Delete existing file if it exists
        if (!empty($draft->$type)) {
            if (Storage::exists($draft->$type)) {
                Storage::delete($draft->$type);
            }
        }
         
        $filename = $this->generateFilename($empno, $studyLeaveId, $type);
        $directory = 'study_leave_documents/'.$type; 
        $path = $this->savePdfToStorage($file, $directory, $filename);
        return $path;
    }
    /**
     * Generate a unique filename for the uploaded PDF.
     */
    private function generateFilename($empno, $studyLeaveId, $type)
    {
        return $empno . '_' . $studyLeaveId . '_' . $type . '.pdf';
        
    }
    /**
     * Save uploaded PDF to private storage.
     */

    private function savePdfToStorage($file, $directory, $filename)
    {
        // Store the file in the specified private directory
        return $file->storeAs($directory, $filename);
    }
    /**
     * Fetch draft study leave details from the database.
     */
    private function getStudyLeaveDraft($empno)
    {
        return StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();
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

        // Validate that each employee number exists
        $employeeNumbers = [
            'nominee_teaching_empno' => $validatedData['nominee_teaching_empno'],
            'nominee_admin_empno' => $validatedData['nominee_admin_empno'],
            'nominee_other_empno' => $validatedData['nominee_other_empno']
        ];

        foreach ($employeeNumbers as $field => $empNo) {
            $employee = $this->getEmployee($empNo);
            if (!$employee) {
                return back()->withErrors([
                    $field => 'Employee number ' . $empNo . ' does not exist.'
                ])->withInput();
            }
        }

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
        $displayEditeBtn = true;
        $empno = session('study_leave.employee_no') ?? session('empno');
        $user = DB::table('employees')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employees.employee_no', $empno)
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
            

        // Retriev, compact('draft_study_leave')e all relefor the summary view
 

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
                "placement_letter",
                "nominee_teaching_empno",
                "nominee_admin_empno",
                "nominee_other_empno"
            )
            ->first();

            


        return view('StudyLeave.showSummary', compact('draft_study_leave', 'user', 'readonly','displayEditeBtn'));
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
                'current_step' => 4,
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
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
            ->where('employee_no', $emp_no)
            ->select(
                'employee_no', 
                DB::raw("CONCAT(initials, ' ', last_name) as name"), 
                'assign_ma_user_id', 
                'designation_name as designation',
                'department_name as department',
                'faculty_name as faculty',
                'categories.category_name as title'
            )
            ->first();

        return $employee;
    }
    public function getStudyLeaves($emp_no)
    {

        // Fetch employee info from the database with latest extension status and total duration
        $previousLeaves = DB::table('study_leaves')
            ->where('empno', $emp_no)
            ->select(
                'study_leaves.id', 
                'degree_title', 
                'university_institute', 
                'study_leave_from', 
                'study_leave_to', 
                'leave_payment_type', 
                'study_leaves.created_at', 
                'study_leaves.status_id', 
                'statuses.status', 
                'reference_no',
                'latest_extensions.extension_status_id',
                DB::raw('COALESCE(extension_durations.total_extension_days, 0) as total_extension_days')
            )
            ->join('statuses', 'study_leaves.status_id', '=', 'statuses.stat_id')
            ->leftJoin(DB::raw('(SELECT study_leave_id, status_id as extension_status_id FROM study_leave_extensions WHERE id IN (SELECT MAX(id) FROM study_leave_extensions GROUP BY study_leave_id)) as latest_extensions'), 'study_leaves.id', '=', 'latest_extensions.study_leave_id')
            ->leftJoin(DB::raw('(SELECT study_leave_id, SUM(DATEDIFF(new_end_date, old_end_date)) as total_extension_days FROM study_leave_extensions WHERE status_id = 1 GROUP BY study_leave_id) as extension_durations'), 'study_leaves.id', '=', 'extension_durations.study_leave_id')
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
        $filePath = storage_path('app/private/study_leave_documents/' . $type . '/' . $filename);

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
       //
       
       

        $rules = array(
             'study_location' => 'required|string|max:100',
            'passport_no' => 'required_if:study_location,Abroad|nullable|string|max:50',
            'passport_validity' => 'required_if:study_location,Abroad|nullable|date',
            'leave_payment_type' => 'required|string|max:100',
            'study_leave_from' => 'required|date',
            'study_leave_to' => 'required|date|after_or_equal:study_leave_from',
            'degree_title' => 'required|string|max:255',
            'university_institute' => 'required|string|max:255',
            'country' => 'required_if:study_location,Abroad|nullable|string|max:100',
            'field_of_study' => 'required|string|max:255',
            'study_program_details' => 'nullable|string|max:1000',
            'funding_type' => 'required|string|max:100',
            'scholarship_source' => 'required_if:funding_type,scholarship|string|max:1000',
            'scholarship_amount' => 'required_if:scholarship_source,agency|nullable|numeric|min:10',
            'project_name' => 'required_if:scholarship_source,project|nullable|string|max:255',
            'any_other_details' => 'nullable|string|max:1000',
            'air_passage_request' => 'required_if:funding_type,self|string|in:yes,no',
            'warm_cloth_allowance_request' => 'required_if:funding_type,self|string|in:yes,no',
            'loan_handling' => 'required_if:leave_payment_type,Without Pay|string|max:100',
            'nominee_teaching_empno' => 'required|string|max:255',
            'nominee_admin_empno' => 'required|string|max:255',
            'nominee_other_empno' => 'required|string|max:255',
        );

        

        $studyLeave = StudyLeave::find($id);
        
        if (!$studyLeave) {
            return redirect()->route('StudyLeave.create')->with('error', 'Study leave application not found.');
        }

        // Check if files already exist
        if($studyLeave->placement_letter == null){
            $rules['placement_letter'] = 'required|file|mimes:pdf|max:10240';
        } else {
            $rules['placement_letter'] = 'nullable|file|mimes:pdf|max:10240';
        }
        
        if($studyLeave->self_funding_declaration == null){
            $rules['self_funding_declaration'] = 'required_if:funding_type,self|file|mimes:pdf|max:10240';
        } else {
            $rules['self_funding_declaration'] = 'nullable|file|mimes:pdf|max:10240';
        }

        $validatedData = $request->validate($rules);
        if($validatedData['study_location'] == 'Sri Lanka'){
            $validatedData['country'] = 'Sri Lanka';
        }
        
        // Handle placement_letter file upload
        if ($request->hasFile('placement_letter')) {
            $file = $request->file('placement_letter');
            $empno = $studyLeave->empno;
            $studyLeaveId = $studyLeave->id;

            // Delete old file if exists
            if ($studyLeave->placement_letter && Storage::exists($studyLeave->placement_letter)) {
                Storage::delete($studyLeave->placement_letter);
            }

            // Generate filename: empno_studyleaveid_placement_letter.pdf
          
            $validatedData['placement_letter'] = $this->saveUplodedPdfAttachment($file,$empno, $studyLeave, 'placement_letter');
        } else {
            // Keep existing file path if no new file uploaded
            unset($validatedData['placement_letter']);
        }

        // Handle self_funding_declaration file upload
        if ($request->hasFile('self_funding_declaration')) {
            $file = $request->file('self_funding_declaration');
            $empno = $studyLeave->empno;
            $studyLeaveId = $studyLeave->id;

            // Delete old file if exists
            if ($studyLeave->self_funding_declaration && Storage::exists($studyLeave->self_funding_declaration)) {
                Storage::delete($studyLeave->self_funding_declaration);
            }

            // Generate filename: empno_studyleaveid_self_funding_declaration.pdf
        
            $validatedData['self_funding_declaration'] = $this->saveUplodedPdfAttachment($file,$empno, $studyLeave, 'self_funding_declaration');
        } else {
            // Keep existing file path if no new file uploaded
            unset($validatedData['self_funding_declaration']);
        }

        // Update study leave with validated data
       
        $studyLeave->update($validatedData + ['status_id' => 4, 'is_draft' => false]);

        return redirect()->route('StudyLeave.create')->with('success', 'Study leave application updated successfully.');
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

        // Get extensions for this study leave
        $extensions = \App\Models\StudyLeaveExtension::where('study_leave_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Decide which blade to use and readonly status
        $readonly = true;
       
        return view('StudyLeave.viewStudyLeave', compact('user', 'draft_study_leave', 'departmentHead', 'readonly', 'extensions'));
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
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('categories', 'employees.title_id', '=', 'categories.id')
            ->where('employees.main_branch_id', 52) // Filter for academic staff first
            ->where(function($query) use ($searchTerm) {
                $query->where('employees.employee_no', 'like', '%' . $searchTerm . '%')
                    ->orWhere(DB::raw("CONCAT(employees.initials, ' ', employees.last_name)"), 'like', '%' . $searchTerm . '%')
                    ->orWhere(DB::raw("CONCAT(employees.name_denoted_by_initials, ' ', employees.last_name)"), 'like', '%' . $searchTerm . '%');
            })
            ->select(
                'employees.employee_no',
                DB::raw("CONCAT(employees.initials, ' ', employees.last_name) as name"),
                'faculties.faculty_name',
                'departments.department_name',
                'categories.category_name as title'
            )
            ->limit(10)
            ->get();

        return response()->json($employees);
    }

    public function deleteFile(Request $request)
    {
        $fileType = $request->input('type'); // 'placement_letter' or 'self_funding_declaration'
        $empno = session('study_leave.employee_no') ?? session('empno');

        // Validate file type
        if (!in_array($fileType, ['placement_letter', 'self_funding_declaration'])) {
            return response()->json(['success' => false, 'message' => 'Invalid file type'], 400);
        }

        // Find the draft study leave
        $draft = StudyLeave::where('empno', $empno)
            ->where('id', $request->input('study_leave_id'))
            ->first();

        if (!$draft) {
            return response()->json(['success' => false, 'message' => 'Draft not found'], 404);
        }

        // Get the file path
        $filePath = $draft->$fileType;

        if (empty($filePath)) {
            return response()->json(['success' => false, 'message' => 'No file to delete'], 404);
        }

        // Delete the file from storage
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Update the database to remove the file path
        $draft->update([$fileType => null]);

        // Update the session to remove the file path
        $sessionData = session('study_leave', []);
        if (isset($sessionData[$fileType])) {
            $sessionData[$fileType] = null;
            session(['study_leave' => $sessionData]);
        }

        return response()->json(['success' => true, 'message' => 'File deleted successfully']);
    }

    public function academicYears()
    {
        $currentYear = date('Y');
        $years = [];

        for ($i = -1; $i < 3; $i++) {
            $startYear = $currentYear + $i;
            $endYear = $startYear + 1;
            $years[] = "{$startYear}/{$endYear}";
        }

        return $years;
    }

    
}
