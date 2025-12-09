<?php

namespace App\Http\Controllers;

use App\Models\StudyLeave;
use App\Models\StudyLeaveExtension;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudyLeaveExtensionController extends Controller
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
        //
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
    public function show(StudyLeaveExtension $studyLeaveExtension)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(StudyLeaveExtension $studyLeaveExtension)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, StudyLeaveExtension $studyLeaveExtension)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(StudyLeaveExtension $studyLeaveExtension)
    {
        //
    }
     public function showStudyLeaveExtensionForm($id){

        $readonly = false;
        $study_leave = StudyLeave::where('id', $id)
  
            ->select(
                "id",
                "leave_type",
                "leave_payment_type",
                "study_leave_from",
                "study_leave_to",
                "funding_type",
                "scholarship_source",
                "scholarship_amount",
                "project_name",
                "nominee_teaching_empno",
                "nominee_admin_empno",
                "nominee_other_empno",
                "reference_no"
            )
            ->first();

        return view('StudyLeave.study_leave_extension.study_leave_extension_form', compact('study_leave', 'readonly'));


    }

    public function storeStudyLeaveExtension(Request $request, $id)
    {
        // Validate the incoming request data
        $rules = array(
            
            //'leave_payment_type' => 'required|string|max:100',
            'old_end_date' => 'required|date',
            'new_end_date' => 'required|date|after_or_equal:old_end_date',
            //'funding_type' => 'required|string|max:100',
           // 'scholarship_source' => 'required_if:funding_type,scholarship|string|max:1000',
           // 'scholarship_amount' => 'required_if:scholarship_source,agency|nullable|numeric|min:10',
           // 'project_name' => 'required_if:scholarship_source,project|nullable|string|max:255',
            'reason_for_extension' => 'required|string|max:2000',
        );
        $validator = Validator::make($request->all(), $rules);
        //$validator = \Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            dd([
                'errors' => $validator->errors()->all(),
                'failed_rules' => $validator->failed(),
                'request_data' => $request->all()
            ]);
        }

        $validatedData = $validator->validated();
        dd($validatedData);

        // Create a new StudyLeaveExtension record
        $creationSuccess = $this->createStudyLeaveExtension($id, $validatedData);
        if (!$creationSuccess) {
            return redirect()->back()->withErrors(['error' => 'Failed to submit study leave extension. Please try again.'])->withInput();
        }
        return redirect()->route('StudyLeave.create')->with('success', 'Study leave extension submitted successfully!');
    }


    public function createStudyLeaveExtension($study_leave_id, $validatedData)
    {
        try {
            // Create a new StudyLeaveExtension record
            $studyLeaveExtension = new StudyLeaveExtension();
            $studyLeaveExtension->study_leave_id = $study_leave_id;
          //  $studyLeaveExtension->leave_payment_type = $validatedData['leave_payment_type'];
            $studyLeaveExtension->extension_from = $validatedData['old_end_date'];
            $studyLeaveExtension->extension_to = $validatedData['new_end_date'];
      //      $studyLeaveExtension->funding_type = $validatedData['funding_type'];
      //      $studyLeaveExtension->scholarship_source = $validatedData['scholarship_source'] ?? null;
       //     $studyLeaveExtension->scholarship_amount = $validatedData['scholarship_amount'] ?? null;
       //     $studyLeaveExtension->project_name = $validatedData['project_name'] ?? null;
            $studyLeaveExtension->reason_for_extension = $validatedData['reason_for_extension'];
            $studyLeaveExtension->save();
            
            return true;
        } catch (\Exception $e) {
            //\Log::error('Study Leave Extension Creation Error: ' . $e->getMessage());
            return false;
        }
    }



}
   
