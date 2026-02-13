<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\OtherLeavesDetail;
use App\Models\LeaveRequestDetail;
use App\Models\StudyLeave;
use App\Models\StudyLeaveApproval;
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
        // Check if user is logged in
        if (!session('empno')) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $currentDate = date('Y-m-d');

        $user = null;
        $isEnableStudyLeaveRequiste = false;

        $previousLeaves = null;
        $hasActiveDraft = false;
        $approvedLeaves = $this->getStudyLeaves(session('empno'))->where('status_id', 1);
        $returnLeaves = $this->getStudyLeaves(session('empno'))->where('status_id', 3);
        $rejectLeaves = $this->getStudyLeaves(session('empno'))->where('status_id', 2);

        //$previousLeaves = $approvedLeaves->merge($returnLeaves)->merge($rejectLeaves);
        $previousLeaves = $this->getStudyLeaves(session('empno'));
        $drafts = StudyLeave::where('empno', session('empno'))
            ->where('is_draft', true)
            ->first();

        if ($drafts) {
            $hasActiveDraft = true;
        }

        $approvedLeavesCount = StudyLeave::where('empno', session('empno'))
            ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
            ->where('study_leave_approvals.status_id', 1)
            ->where('study_leaves.is_draft', false)
            ->select(
                DB::raw('COUNT("study_leaves.id") as approved_count')

            )
            ->first();
        //dd($approvedLeavesCount);
        $approvedLeavesInProgress = StudyLeave::where('empno', session('empno'))
            ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
            ->where('study_leave_approvals.status_id', 1)
            ->where('study_leaves.is_draft', false)

            ->whereDate('study_leaves.study_leave_to', '>=', $currentDate)
            ->count();
       
        $allStudyLeavesCount = StudyLeave::where('empno', session('empno'))
            ->select(
                DB::raw('COUNT("id") as total_count')
            )
            ->first();

        if (($allStudyLeavesCount->total_count - $rejectLeaves->count() - $approvedLeavesCount->approved_count) == 0  && $approvedLeavesInProgress == 0 && $this->calculateTotalStudyLeaveDays(session('empno')) <= 1095) {
            $isEnableStudyLeaveRequiste = true;
        }

        $academicYears = $this->academicYears();

        // Calculate progress report data for each leave
        $leaveProgressData = [];
        if ($previousLeaves) {
            foreach ($previousLeaves as $leave) {
                $progressReports = \App\Models\StudyLeaveProgressReports::where('study_leave_id', $leave->id)
                    ->orderBy('due_date', 'asc')
                    ->get();

                $hasPendingProgressReport = \App\Models\StudyLeaveProgressReports::where('study_leave_id', $leave->id)
                    ->whereNotIn('status_id', [1, 2])
                    ->where('submitted_date', '!=', null)
                    ->exists();

                $canUpload = $this->canUploadProgressReport($leave, $progressReports);
                $nextDueDate = $this->calculateNextProgressReportDueDate($leave, $progressReports);

                $leaveProgressData[$leave->id] = [
                    'canUpload' => $canUpload,
                    'hasPending' => $hasPendingProgressReport,
                    'nextDueDate' => $nextDueDate
                ];
            }
        }

        return view('StudyLeave.createStudyLeave', compact('user', 'drafts', 'previousLeaves', 'hasActiveDraft', 'isEnableStudyLeaveRequiste', 'currentDate', 'academicYears', 'leaveProgressData'));
    }

    /**
     * Calculate total study leave days taken by an employee.
     */
    public function calculateTotalStudyLeaveDays($emp_no)
    {
        $totalDays = 0;
        $previousLeaves = StudyLeave::where('empno', $emp_no)
             ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
            ->where('study_leave_approvals.status_id', 1)
            ->where('study_leaves.is_draft', false)
            ->select('study_leaves.study_leave_from as study_leave_from', 'study_leaves.study_leave_to as study_leave_to');

        if ($previousLeaves->count() > 0) {
            foreach ($previousLeaves->get() as $leave) {
                $leave_id = $leave->id;
                $extensions = StudyLeaveExtension::where('study_leave_extensions.study_leave_id', $leave_id)
                    ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
                    ->where('study_leave_extensions_approvals.status_id', 1)
                    ->select('study_leave_extensions.new_end_date')
                    ->OrderBy('study_leave_extensions.id', 'desc')
                    ->first();
                if ($extensions) {
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
        } else {
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

        // Check if user is logged in
        $empno = session('study_leave.employee_no') ?? session('empno');

        if (!$empno) {
            return redirect()->route('login')->with('error', 'Please login to continue.');
        }

        $user = $this->getUserBasicInfo($empno);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Employee record not found. Please contact administrator.');
        }


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
                //'academic_year' => $academicYear,
            ]);
        }
        return;
    }
    public function getAllStudyLeaves($empno)
    {
        //$empno = session('empno');
        return StudyLeave::where('empno', $empno)
            ->first();
    }

    /**
     * Show the form for creating study leave details.
     * Calling Route: StudyLeave.Details.create
     */

    public function createDetails()
    {

        $readonly = false;

        $empno = session('study_leave.employee_no') ?? session('empno');

        $draft_study_leave = $this->getStudyLeaveDraft($empno);

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
            'leave_payment_type' => 'required|integer|in:1,2',
            'study_leave_from' => 'required|date',
            'study_leave_to' => 'required|date|after_or_equal:study_leave_from',
            'degree_title' => 'required|string|max:255',
            'university_institute' => 'required|string|max:255',
            'country' => 'required_if:study_location,Abroad|nullable|string|max:100',
            'field_of_study' => 'required|string|max:255',
            'study_program_details' => 'nullable|string|max:1000',
            'funding_type' => 'required|integer|in:1,2',
            'scholarship_source' => 'required_if:funding_type,2|integer|in:1,2',
            'scholarship_amount' => 'required_if:scholarship_source,1|nullable|numeric|min:10',
            'project_name' => 'required_if:scholarship_source,2|nullable|string|max:255',
            'any_other_details' => 'nullable|string|max:1000',
            'air_passage_request' => 'required_if:funding_type,1|integer|in:0,1',
            'warm_cloth_allowance_request' => 'required_if:funding_type,1|integer|in:0,1',
            'loan_handling' => 'required_if:leave_payment_type,2|string|max:100',
        );
        $empno = session('study_leave.employee_no') ?? session('empno');

        // Get or create draft to get the study leave ID
        $draft = $this->getStudyLeaveDraft($empno);

        if ($draft->placement_letter == null) {

            $rules['placement_letter'] = 'required|file|mimes:pdf|max:10240';
        }
        if ($draft->self_funding_declaration == null) {
            $rules['self_funding_declaration'] = 'required_if:funding_type,1|file|mimes:pdf|max:10240';
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
            $validatedData['self_funding_declaration'] = $this->saveUplodedPdfAttachment($file, $empno, $draft, 'self_funding_declaration');


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
            $validatedData['placement_letter'] = $this->saveUplodedPdfAttachment($file, $empno, $draft, 'placement_letter');

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


        if ($validatedData['study_location'] == 'Sri Lanka') {
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
    private function saveUplodedPdfAttachment($file, $empno, $draft, $type)
    {
        $studyLeaveId = $draft->id;

        // Delete existing file if it exists
        if (!empty($draft->$type)) {
            if (Storage::exists($draft->$type)) {
                Storage::delete($draft->$type);
            }
        }

        $filename = $this->generateFilename($empno, $studyLeaveId, $type);
        $directory = 'study_leave_documents/' . $type;
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
                "id",
                "empno",
                "nominee_teaching_empno",
                "nominee_admin_empno",
                "nominee_other_empno",
                "consent_letter_teaching_path",
                "consent_letter_admin_path",
                "consent_letter_other_path"
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
            'consent_letter_teaching' => 'nullable|file|mimes:pdf|max:5120',
            'consent_letter_admin' => 'nullable|file|mimes:pdf|max:5120',
            'consent_letter_other' => 'nullable|file|mimes:pdf|max:5120',
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

        // Store only non-file data in session (exclude uploaded files)
        $sessionData = [
            'nominee_teaching_empno' => $validatedData['nominee_teaching_empno'],
            'nominee_admin_empno' => $validatedData['nominee_admin_empno'],
            'nominee_other_empno' => $validatedData['nominee_other_empno'],
        ];
        session(['study_leave' => array_merge(session('study_leave', []), $sessionData)]);

        // Here you can handle the validated data, e.g., save it to the database or session
        // For demonstration, we'll just redirect back with a success message
        $empno = session('study_leave.employee_no') ?? session('empno');
        $draft = StudyLeave::where('empno', $empno)
            ->where('is_draft', true)
            ->first();

        if ($draft) {
            // Prepare update data
            $updateData = [
                'nominee_teaching_empno' => $validatedData['nominee_teaching_empno'],
                'nominee_admin_empno' => $validatedData['nominee_admin_empno'],
                'nominee_other_empno' => $validatedData['nominee_other_empno'],
                'current_step' => 3,
                'is_draft' => true,
            ];

            // Handle consent letter uploads
            $consentLetters = [
                'teaching' => ['file' => 'consent_letter_teaching', 'path_field' => 'consent_letter_teaching_path'],
                'administrative' => ['file' => 'consent_letter_admin', 'path_field' => 'consent_letter_admin_path'],
                'other' => ['file' => 'consent_letter_other', 'path_field' => 'consent_letter_other_path']
            ];

            foreach ($consentLetters as $type => $config) {
                if ($request->hasFile($config['file'])) {
                    $file = $request->file($config['file']);

                    // Delete old file if exists
                    if ($draft->{$config['path_field']}) {
                        Storage::delete($draft->{$config['path_field']});
                    }

                    // Generate filename: empno_id_referenceNo_type.pdf
                    $filename = $empno . '_' . $draft->id . '_' . $draft->reference_no . '_' . $type . '.pdf';

                    // Store in private directory
                    $path = $file->storeAs(
                        'study_leave_documents/consent_letters_nominators/' . $type,
                        $filename,
                        'private'
                    );

                    $updateData[$config['path_field']] = $path;
                }
            }

            // Update existing draft
            $draft->update($updateData);
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




        return view('StudyLeave.showSummary', compact('draft_study_leave', 'user', 'readonly', 'displayEditeBtn'));
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

        // Create approval record with MA as first approver
        StudyLeaveApproval::create([
            'study_leave_id' => $draft->id,
            'status_id' => 4, // Pending

        ]);



        if ($draft) {
            $draft->update([
                'is_draft' => false,
              // 'status_id' => 4, // Assuming '4' is the status ID for 'Submitted'
                'reference_no' => $this->generateReferenceNumber(),

            ]);


            // Store reference number for display in success message
            $referenceNo = $draft->reference_no;
        } else {
            return redirect()->route('StudyLeave.create')->with('error', 'No draft application found to submit.');
        }

        // Clear the session data after successful submission
        $request->session()->forget('study_leave');

        // Set session flags for success popup
        return redirect()->route('StudyLeave.create')
            ->with('success', 'Study leave application submitted successfully! Your application is now under review.')
            ->with('show_success_modal', true)
            ->with('reference_number', $referenceNo);
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
            ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
            ->where('empno', $emp_no)
            ->select(
                'study_leaves.id',
                'degree_title',
                'university_institute',
                'study_leave_from',
                'study_leave_to',
                'leave_payment_type',
                'study_leaves.created_at',
                'study_leave_approvals.status_id as status_id',
                'statuses.status',
                'reference_no',
                'latest_extensions.extension_status_id',
                DB::raw('COALESCE(extension_durations.total_extension_days, 0) as total_extension_days')
            )
            ->join('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
            ->leftJoin(DB::raw('(SELECT study_leave_id, study_leave_extensions_approvals.status_id as extension_status_id FROM study_leave_extensions Join study_leave_extensions_approvals ON study_leave_extensions.id = study_leave_extensions_approvals.study_leave_extension_id WHERE study_leave_extensions.id IN (SELECT MAX(id) FROM study_leave_extensions GROUP BY study_leave_id)) as latest_extensions'), 'study_leaves.id', '=', 'latest_extensions.study_leave_id')
            ->leftJoin(DB::raw('(SELECT study_leave_id, SUM(DATEDIFF(new_end_date, old_end_date)) as total_extension_days FROM study_leave_extensions Join study_leave_extensions_approvals ON study_leave_extensions.id = study_leave_extensions_approvals.study_leave_extension_id WHERE study_leave_extensions_approvals.status_id = 1 GROUP BY study_leave_id) as extension_durations'), 'study_leaves.id', '=', 'extension_durations.study_leave_id')
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
            ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
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
                'study_leave_approvals.ma_remarks as ma_remarks',

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
            'leave_payment_type' => 'required|integer|in:1,2',
            'study_leave_from' => 'required|date',
            'study_leave_to' => 'required|date|after_or_equal:study_leave_from',
            'degree_title' => 'required|string|max:255',
            'university_institute' => 'required|string|max:255',
            'country' => 'required_if:study_location,Abroad|nullable|string|max:100',
            'field_of_study' => 'required|string|max:255',
            'study_program_details' => 'nullable|string|max:1000',
            'funding_type' => 'required|integer|in:1,2',
            'scholarship_source' => 'required_if:funding_type,2|integer|in:1,2',
            'scholarship_amount' => 'required_if:scholarship_source,1|nullable|numeric|min:10',
            'project_name' => 'required_if:scholarship_source,2|nullable|string|max:255',
            'any_other_details' => 'nullable|string|max:1000',
            'air_passage_request' => 'required_if:funding_type,1|integer|in:0,1',
            'warm_cloth_allowance_request' => 'required_if:funding_type,1|integer|in:0,1',
            'loan_handling' => 'required_if:leave_payment_type,2|string|max:100',
            'nominee_teaching_empno' => 'required|string|max:255',
            'nominee_admin_empno' => 'required|string|max:255',
            'nominee_other_empno' => 'required|string|max:255',
        );



        $studyLeave = StudyLeave::find($id);

        if (!$studyLeave) {
            return redirect()->route('StudyLeave.create')->with('error', 'Study leave application not found.');
        }

        // Check if files already exist
        if ($studyLeave->placement_letter == null) {
            $rules['placement_letter'] = 'required|file|mimes:pdf|max:10240';
        } else {
            $rules['placement_letter'] = 'nullable|file|mimes:pdf|max:10240';
        }

        if ($studyLeave->self_funding_declaration == null) {
            $rules['self_funding_declaration'] = 'required_if:funding_type,1|file|mimes:pdf|max:10240';
        } else {
            $rules['self_funding_declaration'] = 'nullable|file|mimes:pdf|max:10240';
        }

        $validatedData = $request->validate($rules);
        if ($validatedData['study_location'] == 'Sri Lanka') {
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

            $validatedData['placement_letter'] = $this->saveUplodedPdfAttachment($file, $empno, $studyLeave, 'placement_letter');
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

            $validatedData['self_funding_declaration'] = $this->saveUplodedPdfAttachment($file, $empno, $studyLeave, 'self_funding_declaration');
        } else {
            // Keep existing file path if no new file uploaded
            unset($validatedData['self_funding_declaration']);
        }

        // Update study leave with validated data

        // $studyLeave->update($validatedData + ['status_id' => 4, 'is_draft' => false]);
        //$studyLeave->update($validatedData + ['status_id' => 4, 'is_draft' => false]);

        // Update the study_leave_approvals table
        StudyLeaveApproval::where('study_leave_id', $id)
            ->update(['status_id' => 4, 'is_draft' => false]);

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
       // dd($id);
        $draft_study_leave = DB::table('study_leaves')
            ->join('employees', 'study_leaves.empno', '=', 'employees.employee_no')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('faculties', 'employees.faculty_id', '=', 'faculties.id')
            ->leftJoin('designations', 'employees.designation_id', '=', 'designations.id')
            ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
            ->join('statuses', 'study_leave_approvals.status_id', '=', 'statuses.stat_id')
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
                'study_leave_approvals.ma_remarks as ma_remarks',

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

        // Get extensions for this study leave with status information
        $extensions = \App\Models\StudyLeaveExtension::where('study_leave_id', $id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->join('statuses', 'study_leave_extensions_approvals.status_id', '=', 'statuses.stat_id')
            ->select('study_leave_extensions.*', 'statuses.status')
            ->orderBy('study_leave_extensions.created_at', 'desc')
            ->get();

        // Calculate if extension is allowed
        $study_leave = StudyLeave::findOrFail($id);

        // Get progress reports for this study leave with status information
        $progressReports = \App\Models\StudyLeaveProgressReports::where('study_leave_id', $id)
            ->leftJoin('statuses', 'study_leave_progress_reports.status_id', '=', 'statuses.stat_id')
            ->select('study_leave_progress_reports.*', 'statuses.status')
            ->orderBy('study_leave_progress_reports.due_date', 'asc')
            ->get();

        // Check for pending progress report (status not approved or rejected)
        $hasPendingProgressReport = \App\Models\StudyLeaveProgressReports::where('study_leave_id', $id)
            ->whereNotIn('status_id', [1, 2]) // Not approved (1) or rejected (2)
            ->where('submitted_date', '!=', null)
            ->exists();

        // Calculate if user can upload next progress report
        $canUploadProgressReport = $this->canUploadProgressReport($study_leave, $progressReports);
        $nextProgressReportDueDate = $this->calculateNextProgressReportDueDate($study_leave, $progressReports);

        // Check for pending extension requests (status not approved or rejected)
        $hasPendingExtension = \App\Models\StudyLeaveExtension::where('study_leave_id', $id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->whereNotIn('study_leave_extensions_approvals.status_id', [1, 2]) // Not approved (1) or rejected (2)
            ->exists();

        $extensionController = new StudyLeaveExtensionController();
        $totalDurationDays = $extensionController->calculateTotalStudyLeaveDays($study_leave->empno);
        $threeYearsInDays = 3 * 365;
        $canExtend = $totalDurationDays < $threeYearsInDays && !$hasPendingExtension;
        $remainingDays = $threeYearsInDays - $totalDurationDays;

        // Get the last approved extension to determine the new start date
        $lastApprovedExtension = StudyLeaveExtension::where('study_leave_id', $id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions_approvals.status_id', 1) // Only approved extensions
            ->orderBy('study_leave_extensions.created_at', 'desc')
            ->first();

        $extensionStartDate = $lastApprovedExtension
            ? $lastApprovedExtension->new_end_date
            : $study_leave->study_leave_to;

        // Prepare process status information for stages display with approval tracking
        // Fetch the approval details from study_leave_approvals table
        $approvalDetails = StudyLeaveApproval::where('study_leave_id', $id)->first();

        // Use status_id from study_leave_approvals table for accurate tracking
        $currentStatusId = $approvalDetails->status_id ?? $draft_study_leave->status_id ?? 4;

        $processStatus = [
            'current_status_id' => $currentStatusId,
            'status_name' => $draft_study_leave->status ?? 'Processing MA',
            'ma_empno' => $approvalDetails->ma_empno ?? null,
            'registrar_empno' => $approvalDetails->registrar_empno ?? null,
            'hod_empno' => $approvalDetails->hod_empno ?? null,
            'dean_empno' => $approvalDetails->dean_empno ?? null,
            'vc_empno' => $approvalDetails->vc_empno ?? null,
            'registrar_recommendation' => $approvalDetails->registrar_recommendation ?? null,
            'hod_recommend' => $approvalDetails->hod_recommend ?? null,
            'dean_leave_recommendation_status' => $approvalDetails->dean_leave_recommendation_status ?? null,
            'vc_recommend_submit_to_committee' => $approvalDetails->vc_recommend_submit_to_committee ?? null,
        ];

        // Decide which blade to use and readonly status
        $readonly = true;

        return view('StudyLeave.viewStudyLeave', compact('user', 'draft_study_leave', 'departmentHead', 'readonly', 'extensions', 'progressReports', 'processStatus', 'study_leave', 'canExtend', 'totalDurationDays', 'remainingDays', 'extensionStartDate', 'hasPendingExtension', 'canUploadProgressReport', 'hasPendingProgressReport', 'nextProgressReportDueDate'));
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
            ->where(function ($query) use ($searchTerm) {
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

    /**
     * Check if user can upload next progress report
     */
    private function canUploadProgressReport($study_leave, $progressReports)
    {
        $today = \Carbon\Carbon::now();
        $leaveStart = \Carbon\Carbon::parse($study_leave->study_leave_from);

        // Get the actual end date (considering extensions)
        $lastApprovedExtension = StudyLeaveExtension::where('study_leave_id', $study_leave->id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions_approvals.status_id', 1)
            ->orderBy('study_leave_extensions.created_at', 'desc')
            ->first();

        $leaveEnd = $lastApprovedExtension
            ? \Carbon\Carbon::parse($lastApprovedExtension->new_end_date)
            : \Carbon\Carbon::parse($study_leave->study_leave_to);

        // Can't upload if study leave hasn't started
        if ($today->lessThan($leaveStart)) {
            return false;
        }

        // Allow uploads during the leave period and within 3 months after it ends
        $threeMonthsAfterEnd = $leaveEnd->copy()->addMonths(3);
        if ($today->greaterThan($threeMonthsAfterEnd)) {
            return false;
        }

        // Check for pending reports (submitted but not approved/rejected)
        $hasPendingReport = $progressReports->filter(function ($report) {
            return !in_array($report->status_id, [1, 2]) && $report->submitted_date != null;
        })->count() > 0;

        if ($hasPendingReport) {
            return false;
        }

        // Calculate which 6-month period we're currently in
        $monthsSinceStart = $leaveStart->diffInMonths($today);
        $currentPeriod = floor($monthsSinceStart / 6) + 1; // Period 1, 2, 3, etc.

        // Calculate the due date for the current period
        $currentPeriodDueDate = $leaveStart->copy()->addMonths($currentPeriod * 6);

        // If current period's due date is beyond leave end, use leave end date
        if ($currentPeriodDueDate->greaterThan($leaveEnd)) {
            $currentPeriodDueDate = $leaveEnd;
        }

        // Check if a report already exists for the current period
        $currentPeriodReport = $progressReports->filter(function ($report) use ($currentPeriodDueDate) {
            $reportDueDate = \Carbon\Carbon::parse($report->due_date);
            return $reportDueDate->isSameDay($currentPeriodDueDate);
        })->first();

        // Can upload if no report exists for current period
        return $currentPeriodReport === null;
    }

    /**
     * Calculate the next progress report due date
     */
    private function calculateNextProgressReportDueDate($study_leave, $progressReports)
    {
        $today = \Carbon\Carbon::now();
        $leaveStart = \Carbon\Carbon::parse($study_leave->study_leave_from);

        // Get the actual end date (considering extensions)
        $lastApprovedExtension = StudyLeaveExtension::where('study_leave_id', $study_leave->id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions_approvals.status_id', 1)
            ->orderBy('study_leave_extensions.created_at', 'desc')
            ->first();

        $leaveEnd = $lastApprovedExtension
            ? \Carbon\Carbon::parse($lastApprovedExtension->new_end_date)
            : \Carbon\Carbon::parse($study_leave->study_leave_to);

        // Calculate which 6-month period we're currently in
        $monthsSinceStart = $leaveStart->diffInMonths($today);
        $currentPeriod = floor($monthsSinceStart / 6) + 1; // Period 1, 2, 3, etc.

        // Calculate the due date for the current period
        $currentPeriodDueDate = $leaveStart->copy()->addMonths($currentPeriod * 6);

        // If current period's due date is beyond leave end, use leave end date
        if ($currentPeriodDueDate->greaterThan($leaveEnd)) {
            $currentPeriodDueDate = $leaveEnd;
        }

        return $currentPeriodDueDate->format('Y-m-d');
    }

    /**
     * Download consent letter template
     */
    public function downloadConsentLetterTemplate()
    {
        $filePath = storage_path('app/public/consent_letter_nomination/Consent_Letter_for_Nomination.pdf');

        if (!file_exists($filePath)) {
            return back()->with('error', 'Consent letter template not found.');
        }

        return response()->download($filePath, 'Consent_Letter_for_Nomination.pdf');
    }

    /**
     * View uploaded consent letter
     */
    public function viewConsentLetter($type, $id)
    {
        // Get authenticated user's employee number from session
        $userEmpNo = session('empno');


        if (!$userEmpNo) {
            abort(403, 'Unauthorized access. Please login.');
        }

        // Find the study leave record
        $studyLeave = StudyLeave::findOrFail($id);


        // Verify the user owns this study leave (check both empno fields)
        $sessionStudyLeaveEmpNo = session('study_leave.employee_no');
        /*
        if ($studyLeave->empno !== $userEmpNo && $studyLeave->empno !== $sessionStudyLeaveEmpNo) {
            abort(403, 'Unauthorized access to this document.');
        }
        */
        //dd($studyLeave->empno, strval($userEmpNo), $sessionStudyLeaveEmpNo);
        if ($studyLeave->empno !== strval($userEmpNo)) {
            abort(403, 'Unauthorized access to this document.');
        }
        // Determine which path to use based on type
        $pathField = '';
        switch ($type) {
            case 'teaching':
                $pathField = 'consent_letter_teaching_path';
                break;
            case 'administrative':
                $pathField = 'consent_letter_admin_path';
                break;
            case 'other':
                $pathField = 'consent_letter_other_path';
                break;
            default:
                abort(404, 'Invalid consent letter type.');
        }

        // Get the file path
        $filePath = $studyLeave->$pathField;

        if (!$filePath) {
            abort(404, 'Consent letter not found.');
        }

        // Get the full path to the file
        $fullPath = Storage::disk('private')->path($filePath);

        if (!file_exists($fullPath)) {
            abort(404, 'Consent letter file does not exist.');
        }

        // Return the file for viewing in browser
        return response()->file($fullPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="consent_letter_' . $type . '.pdf"'
        ]);
    }

    /**
     * Remove uploaded consent letter
     */
    public function removeConsentLetter($type, $id)
    {
        // Get authenticated user's employee number from session
        $userEmpNo = session('empno');

        if (!$userEmpNo) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access. Please login.'], 403);
        }

        // Find the study leave record
        $studyLeave = StudyLeave::findOrFail($id);

        // Verify the user owns this study leave - check multiple session possibilities
        $sessionStudyLeaveEmpNo = session('study_leave.employee_no');
        $isOwner = ($studyLeave->empno === $userEmpNo) ||
            ($studyLeave->empno === $sessionStudyLeaveEmpNo) ||
            ($sessionStudyLeaveEmpNo === $userEmpNo);

        if (!$isOwner) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this document.',
                'debug' => [
                    'studyLeave_empno' => $studyLeave->empno,
                    'session_empno' => $userEmpNo,
                    'session_study_leave_empno' => $sessionStudyLeaveEmpNo
                ]
            ], 403);
        }

        // Determine which path field to use based on type
        $pathField = '';
        switch ($type) {
            case 'teaching':
                $pathField = 'consent_letter_teaching_path';
                break;
            case 'administrative':
                $pathField = 'consent_letter_admin_path';
                break;
            case 'other':
                $pathField = 'consent_letter_other_path';
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Invalid consent letter type.'], 400);
        }

        // Get the file path
        $filePath = $studyLeave->$pathField;

        if ($filePath) {
            // Delete the file from storage
            if (Storage::disk('private')->exists($filePath)) {
                Storage::disk('private')->delete($filePath);
            }

            // Update database to remove the path
            $studyLeave->update([
                $pathField => null
            ]);

            return response()->json(['success' => true, 'message' => 'Consent letter removed successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Consent letter not found.'], 404);
    }
}
