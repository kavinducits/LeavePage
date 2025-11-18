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

        $user = null;

        $previousLeaves = null;
        $hasActiveDraft = false;
        $previousLeaves = $this->getStudyLeaves(session('empno'))->where('status_id', 1);
        $drafts = StudyLeave::where('empno', session('empno'))
            ->where('is_draft', true)
            ->first();

        if ($drafts) {
            $hasActiveDraft = true;
        }

        return view('StudyLeave.createStudyLeave', compact('user', 'drafts', 'previousLeaves', 'hasActiveDraft'));
    }
    public function storeStudyLeave(Request $request)
    {
        //dd('here');
        //
        $academicYear = $request->input('academic_year');
        // Store the academic year in session or pass it to the next step as needed
        session(['study_leave' => ['academic_year' => $academicYear]]);
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
                'is_draft' => true
            ]);
        }

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

        $draft_study_leave = StudyLeave::where('empno', $user->empno)
            ->where('is_draft', true)
            ->select(
                "passport_no",
                "passport_validity"
            )
            ->first();

        return view('StudyLeave.createBasicInfo', compact('user', 'draft_study_leave'));
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
            'passport_no' => 'nullable|string|max:50',
            'passport_validity' => 'nullable|date'
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
        // Check if there's an existing draft for this employee
        $draft = StudyLeave::where('empno', session('empno'))
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Update existing draft
            $draft->update([
                'passport_no' => $validatedData['passport_no'] ?? null,
                'passport_validity' => $validatedData['passport_validity'] ?? null,
            ]);
        } else {
            // Create new draft record
            StudyLeave::create([
                'empno' => session('empno'),
                'passport_no' => $validatedData['passport_no'] ?? null,
                'passport_validity' => $validatedData['passport_validity'] ?? null,
                'is_draft' => true
            ]);
        }


        // redirect Details of the Study Leave

        return redirect()->route('StudyLeave.Details.create')->with('success', 'Basic information saved successfully!');
    }

    public function createDetails()
    {

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


        return view('StudyLeave.createDetails', compact('draft_study_leave'));
    }
    public function storeDetails(Request $request)
    {

        // Validate the incoming request data

        // Validation rules for Study Leave details - adjust fields to match your createDetails.blade.php

        $rules = array(
            // 'leave_type' => 'required|string|max:100',
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
            'placement_letter' => 'nullable|file|mimes:pdf|max:10240'
        );

        $validatedData = $request->validate($rules);

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
                    'is_draft' => true
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
            ];

            // Update existing draft
            $draft->update($updateData);
        }



        return redirect()->route('StudyLeave.WorkCoveringPersons.create')->with('success', 'Study leave details saved successfully!');
    }

    /**
     * Show the form for creating a previous study leave .
     */

    public function createPreviousStudyLeaves()
    {

        $previousLeaves = $this->getStudyLeaves(session('empno'));




        return view('StudyLeave.createPreviousStudyLeaves', compact('previousLeaves'));
    }

    public function storePreviousStudyLeaves(Request $request)
    {
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
            'prev_duration_to' => 'nullable|date',
            'prev_with_pay' => 'nullable|in:yes,no',
            'prev_completed' => 'nullable|string|in:Completed,Not Completed'
        ]);

        // Merge validated previous-leave data into the study_leave session
        session(['study_leave' => array_merge(session('study_leave', []), $validatedData)]);

        return redirect()->route('StudyLeave.WorkCoveringPersons.create')->with('success', 'Previous study leave details saved successfully!');
    }
    public function createWorkCoveringPersons()
    {
        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->select(
                "nominee_teaching_empno",
                "nominee_admin_empno",
                "nominee_other_empno"
            )
            ->first();

        return view('StudyLeave.createWorkCoveringPersons', compact('draft_study_leave'));
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
            ]);
        }

        return redirect()->route('StudyLeave.Summary.show')->with('success', 'Work covering persons details saved successfully!');
    }
    public function createHandeling()
    {

        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->select(
                "library_and_property_handling",
                "loan_handling"
            )
            ->first();

        return view('StudyLeave.createHandeling', compact('draft_study_leave'));
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
        $empno = session('study_leave.employee_no') ?? session('empno');
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Update existing draft
            $draft->update([
                'library_and_property_handling' => $validatedData['library_and_property_handling'],
                'loan_handling' => $validatedData['loan_handling'],
            ]);
        }

        return redirect()->route('StudyLeave.Summary.show')->with('success', 'Handling of details saved successfully!');
    }
    public function showSummary()
    {

        // Retriev, compact('draft_study_leave')e all relefor the summary view

        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->select(
                "library_and_property_handling",
                "loan_handling"
            )
            ->first();

        return view('StudyLeave.showSummary', compact('draft_study_leave'));
    }
    public function submitApplication(Request $request)
    {

        // Validate the incoming request data

        $validatedData = $request->validate([
            'library_and_property_handling' => 'required|string|max:100',
            'loan_handling' => 'required|string|max:100',


        ]);
        session(['study_leave' => array_merge(session('study_leave', []), $validatedData)]);
        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message
        $empno = session('study_leave.employee_no') ?? session('empno');

        // Find and update the draft to mark it as submitted
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            $draft->update([
                'is_draft' => false,
                'status_id' => 4, // Assuming '4' is the status ID for 'Submitted'

            ]);
        }

        // Clear the session data after successful submission
        $request->session()->forget('study_leave');

        return redirect()->route('StudyLeave.create')->with('success', 'Study leave application submitted successfully! Your application is now under review.');
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
            ->join('designations', 'employees.designation_id', '=', 'designations.id')
            ->where('employee_no', $emp_no)
            ->select('employee_no', DB::raw("CONCAT(initials, ' ', last_name) as name"), 'assign_ma_user_id', 'designation_name as designation')
            ->first();

        return $employee;
    }
    public function getStudyLeaves($emp_no)
    {
        //dd($emp_no);
        // Fetch employee info from the database
        $previousLeaves = DB::table('study_leaves')
            ->where('empno', $emp_no)
            ->select('id', 'degree_title', 'university_institute', 'study_leave_from', 'study_leave_to', 'leave_payment_type', 'study_leaves.created_at', 'status_id', 'status')
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
        //dd('here');
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
        // dd($fileEmpNo);

        // Authorization check: Allow if:
        // 1. User is the employee who owns the file
        // 2. User is an approver (MA, HOD, Dean, VC) - you can add more checks here
        $currentEmpNo = (string) session('empno');
        //  dd($currentEmpNo);
        $isOwner = ($currentEmpNo === $fileEmpNo);
        //  dd($isOwner);
        // Check if user is an approver by checking if they have ma_user_id, hod role, etc.
        // For now, we'll allow access if they're the owner or if they have a session
        // You can add more sophisticated role checks here
        $isApprover = !empty(session('ma_user_id')) || !empty(session('hod_id')) || !empty(session('dean_id'));
        //dd($isApprover);
        /*
        if (!$isOwner && !$isApprover) {
            abort(403, 'Unauthorized access to this file');
        }
*/
        if (!$isOwner) {
            abort(403, 'Unauthorized access to this file');
        }

        // Serve the file
        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($filename) . '"'
        ]);
    }
    public function deleteStudyLeaveDraft(Request $request)
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
}
