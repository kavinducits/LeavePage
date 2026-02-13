<?php

namespace App\Http\Controllers;

use App\Models\StudyLeave;
use App\Models\StudyLeaveExtension;
use App\Models\StudyLeaveExtensionsApprovals;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;

class StudyLeaveExtensionController extends Controller
{
    

    /**
     * Get all extensions for a specific study leave
     */
    public function getExtensionsByStudyLeaveId($study_leave_id)
    {
        return StudyLeaveExtension::where('study_leave_id', $study_leave_id)
            ->orderBy('study_leave_extensions.created_at', 'desc')
            ->get();
    }

    
     public function showStudyLeaveExtensionForm($id){

        $readonly = false;
        $study_leave = StudyLeave::where('id', $id)
  
            ->select(
                "id",
                "empno",
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

        // Calculate total duration including all extensions
        $originalDuration = \Carbon\Carbon::parse($study_leave->study_leave_from)
            ->diffInDays(\Carbon\Carbon::parse($study_leave->study_leave_to));
        
        // Get all approved extensions for this study leave
        $extensions = StudyLeaveExtension::where('study_leave_id', $id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->whereIn('study_leave_extensions_approvals.status_id', [1]) // Only approved extensions
            ->get();
        
        $totalExtensionDays = 0;
        foreach ($extensions as $extension) {
            $extensionDays = \Carbon\Carbon::parse($extension->old_end_date)
                ->diffInDays(\Carbon\Carbon::parse($extension->new_end_date));
            $totalExtensionDays += $extensionDays;
        }
        
        $totalDurationDays = $this->calculateTotalStudyLeaveDays($study_leave->empno);
        $threeYearsInDays = 3 * 365; // 1095 days
        
        // Check for pending extension requests (status not approved or rejected)
        $hasPendingExtension = StudyLeaveExtension::where('study_leave_id', $id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->whereNotIn('study_leave_extensions_approvals.status_id', [1, 2]) // Not approved (1) or rejected (2)
            ->exists();
        
        $canExtend = $totalDurationDays < $threeYearsInDays && !$hasPendingExtension;
        $remainingDays = $threeYearsInDays - $totalDurationDays;

        // Get the last approved extension to determine the new start date
        $lastApprovedExtension = StudyLeaveExtension::where('study_leave_id', $id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->where('study_leave_extensions_approvals.status_id', 1) // Only approved extensions
            ->orderBy('study_leave_extensions.created_at', 'desc')
            ->first();
        
        // If there's an approved extension, use its new_end_date as the start date
        // Otherwise, use the original study leave end date
        $extensionStartDate = $lastApprovedExtension 
            ? $lastApprovedExtension->new_end_date 
            : $study_leave->study_leave_to;
        
        return view('StudyLeave.study_leave_extension.study_leave_extension_form', compact('study_leave', 'readonly', 'canExtend', 'totalDurationDays', 'remainingDays', 'extensionStartDate', 'hasPendingExtension'));


    }

     /**
     * Calculate total study leave days taken by an employee.
     */
    public function calculateTotalStudyLeaveDays($emp_no)
    {
        $totalDays = 0;
        $previousLeaves=StudyLeave::where('empno', $emp_no)
        ->join('study_leave_approvals', 'study_leaves.id', '=', 'study_leave_approvals.study_leave_id')
        ->where('study_leaves.is_draft', false)
        ->where('study_leave_approvals.status_id', 1)
        ->select('study_leaves.study_leave_from','study_leaves.study_leave_to');

        if($previousLeaves->count() > 0){
            foreach($previousLeaves->get() as $leave){
                $leave_id = $leave->id;
                $extensions = StudyLeaveExtension::where('study_leave_id', $leave_id)
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

    public function storeStudyLeaveExtension(Request $request, $id)
    {
        // First, check if total duration exceeds 3 years
        $study_leave = StudyLeave::findOrFail($id);
        
        $originalDuration = \Carbon\Carbon::parse($study_leave->study_leave_from)
            ->diffInDays(\Carbon\Carbon::parse($study_leave->study_leave_to));
        
        // Get all approved extensions
        $extensions = StudyLeaveExtension::where('study_leave_id', $id)
            ->join('study_leave_extensions_approvals', 'study_leave_extensions.id', '=', 'study_leave_extensions_approvals.study_leave_extension_id')
            ->whereIn('study_leave_extensions_approvals.status_id', [1])
            ->get();
        
        $totalExtensionDays = 0;
        foreach ($extensions as $extension) {
            $extensionDays = \Carbon\Carbon::parse($extension->old_end_date)
                ->diffInDays(\Carbon\Carbon::parse($extension->new_end_date));
            $totalExtensionDays += $extensionDays;
        }
        
        $totalDurationDays = $originalDuration + $totalExtensionDays;
        $threeYearsInDays = 3 * 365;
        
        if ($totalDurationDays >= $threeYearsInDays) {
            return redirect()->back()
                ->withErrors(['error' => 'Cannot extend: Total study leave duration (including approved extensions) has reached or exceeded 3 years limit.'])
                ->withInput();
        }
        
        // Validate the incoming request data
        $rules = array(
            
           
            'old_end_date' => 'required|date',
            'new_end_date' => 'required|date|after_or_equal:old_end_date',
            'reason_for_extension' => 'required|string|max:2000',
        );
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            dd([
                'errors' => $validator->errors()->all(),
                'failed_rules' => $validator->failed(),
                'request_data' => $request->all()
            ]);
        }

        $validatedData = $validator->validated();
    
        // Create a new StudyLeaveExtension record
        $creationSuccess = $this->createStudyLeaveExtension($id, $validatedData);
        if (!$creationSuccess) {
            return redirect()->back()->withErrors(['error' => 'Failed to submit study leave extension. Please try again.'])->withInput();
        }
        return redirect()->route('StudyLeave.show.studyLeave', $id)->with('success', 'Study leave extension submitted successfully!');
    }


    public function createStudyLeaveExtension($study_leave_id, $validatedData)
    {
        
        try {
            
            // Create a new StudyLeaveExtension record
            $studyLeaveExtension = new StudyLeaveExtension();
            $studyLeaveExtensionApprovals = new StudyLeaveExtensionsApprovals();
           
            $studyLeaveExtension->study_leave_id = $study_leave_id;
           
        
            $studyLeaveExtension->old_end_date = $validatedData['old_end_date'];
            $studyLeaveExtension->new_end_date = $validatedData['new_end_date'];
            $studyLeaveExtension->reason_for_extension = $validatedData['reason_for_extension'];
           // $studyLeaveExtension->status_id = 4; // Pending status
           
            $studyLeaveExtension->save();
          
            $studyLeaveExtensionApprovals->study_leave_extension_id = $studyLeaveExtension->id;
            $studyLeaveExtensionApprovals->status_id = 4; // Pending status

            $studyLeaveExtensionApprovals->save();
            
            return true;
        } catch (\Exception $e) {
            dd($e->getMessage());
            return false;
        }
    }

    /**
     * Update an existing study leave extension (resubmit)
     */
    public function updateStudyLeaveExtension(Request $request, $id)
    {
        // Find the extension
        $extension = StudyLeaveExtension::findOrFail($id);
        $extensionApprover = StudyLeaveExtensionsApprovals::where('study_leave_extension_id', $id)->first();
        
        // Validate the incoming request data
        $rules = [
            'old_end_date' => 'required|date',
            'new_end_date' => 'required|date|after_or_equal:old_end_date',
            'reason_for_extension' => 'required|string|max:2000',
        ];
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();
        
        try {
            // Update the extension
            $extension->old_end_date = $validatedData['old_end_date'];
            $extension->new_end_date = $validatedData['new_end_date'];
            $extension->reason_for_extension = $validatedData['reason_for_extension'];
           // $extension->status_id = 4; // Reset to pending status
            $extension->save();
            
            // Update the approval record to pending status
            $extensionApprover->status_id = 4;
            $extensionApprover->save();
            
            return redirect()->route('StudyLeave.show.studyLeave', ['id' => $extension->study_leave_id])
                ->with('success', 'Extension resubmitted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to resubmit extension. Please try again.'])
                ->withInput();
        }
    }


}
   
