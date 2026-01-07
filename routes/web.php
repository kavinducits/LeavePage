<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\MAController;
use App\Http\Controllers\HODController;
use App\Http\Controllers\DeanController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\VCController;
use App\Http\Controllers\StudyLeaveController;
use App\Http\Controllers\StudyLeaveExtensionController;
use App\Http\Controllers\StudyLeaveProgressReportsController;


// Login routes
// Set the default login page to MA page
Route::get('/', [MAController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// URL-based login by employee ID: /leaves/{id}
Route::get('/leaves/{id}', [LoginController::class, 'loginById'])->name('login.byid');

// MA routes (no authentication required)
Route::get('/MApage', [MAController::class, 'index'])->name('ma.index');
Route::get('/MAPage', [MAController::class, 'index'])->name('ma.index.alt'); // Alternative route
Route::get('/MApage/{id}', [MAController::class, 'show'])->name('ma.show');
Route::post('/MApage/{id}/approve', [MAController::class, 'approve'])->name('ma.approve');
Route::post('/MApage/{id}/return', [MAController::class, 'return'])->name('ma.return');
Route::POST('/MApage/studyLeave/view/{id}/return', [MAController::class, 'returnStudyLeave'])->name('ma.studyleave.return');

Route::get('/MApage/progressReport/file/{filename}', [MAController::class, 'serveProgressReportFile'])->name('ma.serveProgressReport');

// HOD routes (no authentication required)
Route::get('/HODpage', [HODController::class, 'index'])->name('hod.index');//remove
Route::get('/HODpage/leave', [HODController::class, 'leave_index'])->name('hod.leave.index');
Route::get('/HODpage/studyLeave', [HODController::class, 'study_leave_index'])->name('hod.study.leave.index');
Route::get('/HODpage/{id}', [HODController::class, 'show'])->name('hod.show');
Route::post('/HODpage/{id}/approve', [HODController::class, 'approve'])->name('hod.approve');
Route::post('/HODpage/{id}/return', [HODController::class, 'return'])->name('hod.return');

// Dean/Registrar routes (no authentication required)
Route::get('/Deanpage', [DeanController::class, 'index'])->name('dean.index');
Route::get('/Deanpage/{id}', [DeanController::class, 'show'])->name('dean.show');
Route::post('/Deanpage/{id}/recommend', [DeanController::class, 'recommend'])->name('dean.recommend');

// Dean Study Leave routes
Route::get('/Deanpage/studyleave/view/{id}', [DeanController::class, 'showStudyLeaveApplication'])->name('dean.view.studyLeave');
Route::post('/Deanpage/studyleave/view/{id}/approve', [DeanController::class, 'approveStudyLeave'])->name('dean.view.studyLeave.approve');
Route::get('/Deanpage/extension/{extension_id}', [DeanController::class, 'showExtension'])->name('dean.show.extension');
Route::post('/Deanpage/extension/{extension_id}/approve', [DeanController::class, 'approveExtension'])->name('dean.extension.approve');
Route::post('/Deanpage/extension/{extension_id}/return', [DeanController::class, 'returnExtension'])->name('dean.extension.return');

// VC routes (no authentication required)
Route::get('/VCpage', [VCController::class, 'index'])->name('vc.index');
Route::get('/VCpage/{id}', [VCController::class, 'show'])->name('vc.show');
Route::post('/VCpage/{id}/recommend', [VCController::class, 'recommend'])->name('vc.recommend');

// VC Study Leave routes
Route::get('/VCpage/studyleave/view/{id}', [VCController::class, 'showStudyLeaveApplication'])->name('vc.view.studyLeave');
Route::post('/VCpage/studyleave/view/{id}/approve', [VCController::class, 'approveStudyLeave'])->name('vc.view.studyLeave.approve');

// VC Extension routes
Route::get('/VCpage/extension/{extension_id}', [VCController::class, 'showExtension'])->name('vc.show.extension');
Route::post('/VCpage/extension/{extension_id}/approve', [VCController::class, 'approveExtension'])->name('vc.extension.approve');
Route::post('/VCpage/extension/{extension_id}/return', [VCController::class, 'returnExtension'])->name('vc.extension.return');

//Study Leave routes (no authentication required)
//Route::get('/StudyLeave', [StudyLeaveController::class, 'create'])->name('StudyLeave.create');
Route::get('/StudyLeave', [StudyLeaveController::class, 'createStudyLeave'])->name('StudyLeave.create');
Route::post('/StudyLeave', [StudyLeaveController::class, 'storeStudyLeave'])->name('StudyLeave.store');
Route::delete('/StudyLeave/Delete/{id}', [StudyLeaveController::class, 'deleteStudyLeaveDraft'])->name('StudyLeave.DeleteDraft');
//Basic Info
Route::get('/StudyLeave/BasicInfo', [StudyLeaveController::class, 'createBasicInfo'])->name('StudyLeave.BasicInfo.create');
Route::post('/StudyLeave/BasicInfo', [StudyLeaveController::class, 'storeBasicInfo'])->name('StudyLeave.BasicInfo.store');
Route::post('/StudyLeave/BasicInfo/exit', [StudyLeaveController::class, 'exiteBasicInfo'])->name('StudyLeave.BasicInfo.exit');

//Details of the Study Leave
Route::get('/StudyLeave/Details', [StudyLeaveController::class, 'createDetails'])->name('StudyLeave.Details.create');
Route::post('/StudyLeave/Details', [StudyLeaveController::class, 'storeDetails'])->name('StudyLeave.Details.store');
Route::post('/StudyLeave/Details/exit', [StudyLeaveController::class, 'exiteDetails'])->name('StudyLeave.Details.exit');

//Details of the Previous Study Leave
Route::get('/StudyLeave/PreviousStudyLeaves', [StudyLeaveController::class, 'createPreviousStudyLeaves'])->name('StudyLeave.PreviousStudyLeaves.create');
Route::post('/StudyLeave/PreviousStudyLeaves', [StudyLeaveController::class, 'storePreviousStudyLeaves'])->name('StudyLeave.PreviousStudyLeaves.store');

//Nominate Work covering Persons
Route::get('/StudyLeave/WorkCoveringPersons', [StudyLeaveController::class, 'createWorkCoveringPersons'])->name('StudyLeave.WorkCoveringPersons.create');
Route::post('/StudyLeave/WorkCoveringPersons', [StudyLeaveController::class, 'storeWorkCoveringPersons'])->name('StudyLeave.WorkCoveringPersons.store');
Route::post('/StudyLeave/WorkCoveringPersons/exit', [StudyLeaveController::class, 'exiteWorkCoveringPersons'])->name('StudyLeave.WorkCoveringPersons.exit');

//Handling of
Route::get('/StudyLeave/Handeling', [StudyLeaveController::class, 'createHandeling'])->name('StudyLeave.Handeling.create');
Route::post('/StudyLeave/Handeling', [StudyLeaveController::class, 'storeHandeling'])->name('StudyLeave.Handeling.store');

// Summary and Submit
Route::get('/StudyLeave/Summary', [StudyLeaveController::class, 'showSummary'])->name('StudyLeave.Summary.show');
Route::post('/StudyLeave/Submit', [StudyLeaveController::class, 'submitApplication'])->name('StudyLeave.Submit');
Route::post('/StudyLeave/Summary/exit', [StudyLeaveController::class, 'exitSummary'])->name('StudyLeave.Summary.exit');


//Get the emp no and name by ajax
Route::get('/StudyLeave/get-employee-info/{emp_no}', [StudyLeaveController::class, 'getEmployeeInfo'])->name('StudyLeave.getEmployeeInfo');
//Search Academic Employees for work covering
Route::get('/StudyLeave/workingcovering/academic/search', [StudyLeaveController::class, 'searchAcademicEmployees'])->name('StudyLeave.searchAcademicEmployees');

// Secure file serving route
Route::get('/StudyLeave/files/{type}/{filename}', [StudyLeaveController::class, 'serveFile'])->name('StudyLeave.serveFile');
Route::post('/StudyLeave/files/delete', [StudyLeaveController::class, 'deleteFile'])->name('StudyLeave.deleteFile');

//Route::get('/StudyLeave/view/{id}', [MAController::class, 'showStudyLeaveApplication'])->name('StudyLeave.show.studyLeaveApplication');
//Route::get('/StudyLeave/view/{id}', [MAController::class, 'showStudyLeave'])->name('StudyLeave.show.studyLeaveApplication');
Route::get('/dashboard/StudyLeave/{id}', [MAController::class, 'showStudyLeave'])->name('ma.show.studyleave');
Route::get('/dashboard/StudyLeaveExtension/{extension_id}', [MAController::class, 'showExtension'])->name('ma.show.extension');
Route::POST('/dashboard/StudyLeaveExtension/{extension_id}/forward', [MAController::class, 'forwardExtension'])->name('ma.extension.forward');
Route::POST('/dashboard/StudyLeaveExtension/{extension_id}/return', [MAController::class, 'returnExtension'])->name('ma.extension.return');
Route::get('/dashboard/StudyLeaveProgressReport/{progress_report_id}', [MAController::class, 'showProgressReport'])->name('ma.show.studyleave.progressreport');
Route::POST('/dashboard/StudyLeaveProgressReport/{progress_report_id}/approve', [MAController::class, 'approveProgressReport'])->name('ma.progressreport.approve');
Route::POST('/dashboard/StudyLeaveProgressReport/{progress_report_id}/return', [MAController::class, 'returnProgressReport'])->name('ma.progressreport.return');

Route::POST('/StudyLeave/view/{id}/approve', [MAController::class, 'approveStudyLeave'])->name('StudyLeave.approve');

Route::get('/StudyLeave/view/{id}', [StudyLeaveController::class, 'showStudyLeave'])->name('StudyLeave.show.studyLeave');
Route::get('/StudyLeave/view/{id}/edite', [StudyLeaveController::class, 'showEditeStudyLeaveForm'])->name('StudyLeave.show.editeForm');
Route::post('/StudyLeave/view/{id}/edite', [StudyLeaveController::class, 'editeStudyLeaveApplication'])->name('StudyLeave.edite.application');
Route::post('/StudyLeave/view/{id}/edite/update', [StudyLeaveController::class, 'updateEditeStudyLeave'])->name('StudyLeave.update.edite.application');
Route::get('/StudyLeave/view/{id}/extend', [StudyLeaveExtensionController::class, 'showStudyLeaveExtensionForm'])->name('StudyLeave.show.extensionForm');
Route::POST('/StudyLeave/view/{id}/extend', [StudyLeaveExtensionController::class, 'storeStudyLeaveExtension'])->name('StudyLeave.store.extension');

// Progress Reports Routes
Route::get('/StudyLeave/view/{id}/progress-reports', [StudyLeaveProgressReportsController::class, 'showProgressReports'])->name('StudyLeave.progressReports.show');
Route::POST('/StudyLeave/{study_leave_id}/progress-report/upload', [StudyLeaveProgressReportsController::class, 'uploadProgressReport'])->name('StudyLeave.progressReport.upload');
Route::get('/StudyLeave/progress-report/files/{filename}', [StudyLeaveProgressReportsController::class, 'serveProgressReportFile'])->name('StudyLeave.serveProgressReport');

Route::get('/StudyLeave/draft/{id}/continue', [StudyLeaveController::class, 'continueDraft'])->name('StudyLeave.continue.draft');


Route::get('/HODDashboard', [HODController::class, 'showStudyLeaves'])->name('hod.show.studyleaves');
Route::get('/HODDashboard/view/{id}', [HODController::class, 'showStudyLeaveApplication'])->name('hod.view.studyLeave');
Route::POST('/HODDashboard/view/{id}/approve', [HODController::class, 'approveStudyLeave'])->name('hod.view.studyLeave.approve');
Route::get('/HODDashboard/extension/{extension_id}', [HODController::class, 'showExtension'])->name('hod.show.extension');
Route::POST('/HODDashboard/extension/{extension_id}/approve', [HODController::class, 'approveExtension'])->name('hod.extension.approve');
Route::POST('/HODDashboard/extension/{extension_id}/return', [HODController::class, 'returnExtension'])->name('hod.extension.return');
Route::get('/HODDashboard/progressreport/{progress_report_id}', [HODController::class, 'showProgressReport'])->name('hod.show.studyleave.progressreport');






// Protected routes (user must be logged in)
Route::middleware('checklogin')->group(function () {
    
    Route::get('/firstPage', [LeaveController::class, 'index'])->name('leaves.index'); // Landing page
    Route::get('/leave/create', [LeaveController::class, 'create'])->name('leaves.create'); // Create new leave or open draft/returned
    Route::get('/leave/show/{id}', [LeaveController::class, 'show'])->name('leaves.show'); // View submitted leave application
    Route::post('/leave/store', [LeaveController::class, 'store'])->name('leaves.store'); // Save (submit or draft)
    Route::delete('/leave/delete/{id}', [LeaveController::class, 'destroy'])->name('leaves.destroy'); // Delete draft

    // AJAX endpoints for file upload/delete
    Route::post('/leave/upload-file', [LeaveController::class, 'uploadFile'])->name('leaves.uploadFile');
    Route::post('/leave/delete-file', [LeaveController::class, 'deleteFile'])->name('leaves.deleteFile');

    // AJAX endpoints for travel document upload/delete
    Route::post('/leave/upload-travel-document', [LeaveController::class, 'uploadTravelDocument'])->name('leaves.uploadTravelDocument');
    Route::post('/leave/remove-travel-document', [LeaveController::class, 'removeTravelDocument'])->name('leaves.removeTravelDocument');

    // AJAX endpoint for temporary file upload
    Route::post('/leave/upload-temp-file', [LeaveController::class, 'uploadTempFile'])->name('leaves.uploadTempFile');
    Route::post('/leave/delete-temp-file', [LeaveController::class, 'deleteTempFile'])->name('leaves.deleteTempFile');

    // AJAX endpoint for saving travel details
    Route::post('/leave/save-travel-detail', [LeaveController::class, 'saveTravelDetail'])->name('leaves.saveTravelDetail');
    Route::delete('/leave/delete-travel-detail/{id}', [LeaveController::class, 'deleteTravelDetail'])->name('leaves.deleteTravelDetail');

    // Test endpoint for debugging
    Route::post('/leave/test-upload', [LeaveController::class, 'testUpload'])->name('leaves.testUpload');

    Route::get('/leave/draft/create', [LeaveController::class, 'createDraft'])->name('leaves.draft.create');
    Route::get('/leave/new', [LeaveController::class, 'create'])->name('leaves.new'); // New application without database record

    // Other Leaves Details routes
    Route::post('/other-leaves/store', [LeaveController::class, 'storeOtherLeave'])->name('other-leaves.store');
    Route::get('/other-leaves', [LeaveController::class, 'getOtherLeaves'])->name('other-leaves.index');
    Route::get('/other-leaves/{id}', [LeaveController::class, 'getOtherLeave'])->name('other-leaves.show');
    Route::put('/other-leaves/{id}', [LeaveController::class, 'updateOtherLeave'])->name('other-leaves.update');
    Route::delete('/other-leaves/{id}', [LeaveController::class, 'deleteOtherLeave'])->name('other-leaves.delete');
    
    // Test route to view other leaves details
    Route::get('/test-other-leaves', function() {
        $otherLeaves = \App\Models\OtherLeavesDetail::with('leaveType')->get();
        return response()->json($otherLeaves);
    })->name('test.other-leaves');
    
    // Test route to manually trigger saveToOtherLeavesDetails
    Route::get('/test-save-other-leaves/{referenceNo}', [LeaveController::class, 'testSaveToOtherLeaves'])->name('test.save-other-leaves');
});

Route::get('/dashboard', [MAController::class, 'dashboard'])->name('ma.dashboard');
Route::get('/dashboard/vc-approved', [MAController::class, 'dashboardVcApproved'])->name('ma.dashboard.vcapproved');
Route::get('/dashboard/status', [MAController::class, 'statusPage'])->name('ma.status');
Route::get('/dashboard/study-leave', [MAController::class, 'studyLeavePage'])->name('ma.studyleave');
Route::get('/dashboard/study-leave-status', [MAController::class, 'studyLeaveStatusPage'])->name('ma.studyleavestatus');
//Route::get('/dashboard/study-leave/{id}', [MAController::class, 'showStudyLeave'])->name('ma.show.studyleave');

Route::get('/MApage/{id}/hod', [MAController::class, 'showHod'])->name('ma.show.hod');
Route::get('/MApage/{id}/dean', [MAController::class, 'showDean'])->name('ma.show.dean');
Route::get('/MApage/{id}/vc', [MAController::class, 'showVc'])->name('ma.show.vc');
