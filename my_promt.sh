i need to show view , edite, extend,progress button show each leave records under actions coulumn.

Only show edite button when MA return the studyleave.(status_id= 3 mean it return)
Only show Progress,view,Extend button when approved studyleave and studyleave in progress(study_leave_from,study_leave_to)
Only show Prgress,View button when study leave in after 3 month for end to study leave((study_leave_from,study_leave_to)
Only show View button after the study leave end (study_leave_from,study_leave_to)
Only show View button after the study leave end
Use approciate dissable button options to hide unwateded action button.
Analyse the UI in createStudyLeave.blade page and apply approciate style to show that. 

Only show View button after the study leave end


create blade form under StudyLeave/study_leave_progress_reports, that user can uplode prorgress reports 
User could uplode progress report for each 6 month in refferance study leave time period
when click progress buttons in createStudyLave page, should navaiaget to coresponding progress report page
uplode pdf shoulde be store in storage/app/private/study_leave_progress_report
when progress reports uploded use study_leave_progress_reports table to sotre data
Analayse the blade pages in StudyLave folder and apply apporiciate UI for that (coler theme should be same)
Use StudyLeaveProgressReports model,StudyLeaveProgressReports controller to implment this
I have already created the table to save the details of progress report that table stucture in 2025_12_09_052345_create_study_leave_progress_reports_table.php
update exite thing againe


user should be able to uplode progress report at the begining of study leave and befor sheduled due date 

when  showing due date in progress uploded caluclate the duedate using studyleave start date,
don't use study_leave_progress_reports_table to show due date

change the progress_report_form.blade table againe
User could uplode progress report
when uplode report, create new record that study_leave_id,due_date,submitted_date,status_id as 3,and cretd_time,updated_time and document_path as reletive path pdf store,
If it is uplode , then show next uplode link with due date that if current date is pass the previous due date




----------------------------------------------------------------------


analyse the viewStudyLeave blade page and it's included forms
Then create Suitable table form blade to show the StudyLeaveExtension table reocrd that related to viewing studyleave
In this table form should include old_end_date,new_end_date,reason_for_extension and status_id
create this table form table in study_leave_extenstion folder and it include in the viewStudyLeave blade page(top or bootm)
user StudyLeaveExtenstionController to write relateded backend logics



Analyse the exite tables and  UI themes of ma.studyLeave.blade.php
Then create separate table form class study_leave_extensions_table.blade.php in ma/study_leave directory
That should include to below of the exiting page of studyLeave.blade.php
Reference No,Employee No,Name with Initials,Department,Faculty,Applied Date,status,action are should be show in table
Use study_leaves,study_leave_extenstions,employees,departments,faculties tables to implement this
write all backend logics in MAController


Make blade page that name is study_leave_extenstion_view_form in ma/study_leave direcotry
It show Study_leave referance Number ,old_end_date,new_end_date,reason_for_extension and calculateded duration
And Under the more details tab,show study_lave_details Using including StudyLeave/basic_info_form.blade,details_form.blade.php,
working_covering_persons_form.blade.php and when apply this form use eanble read only options in forms
Then apply ma reamrk and return to user and forward to head button.
all the back end logics should be in MAController

  <i class="fas fa-clock me-1"></i>{{ $durationDays }} days ({{ $durationMonths }} months)


--------------------------------------------------------------------------------------------------------------------------------

i need to do addtional updates on createStudyLeave.blade php
I need to add adtional combinations of actions buttons showing in actiona field
it is that when last reocord in study_leave_extension table is stauts is 3 then show only view,Return Extend,progress button
when last record in study_leave_extension table is status is 4,5,6,7,8 then show only view,progress button
when last record in study_leave_extension table is status is 1,2 then show only view,progress button

Additional update on createStudyLeave.blade php
if Study leave time period + study leave extensions time priod s >= 3y year do not allow to extend


study the Pending Study Leave Application table in the StudyLeave.blade file
then create this kind of table card to Pending Study Leave application in hod/study_leave direcotry as the
study_leave_table.blade.php
then include this card in the hod/indexStudyLeave.blade.php
this table card only show study leave that realated to this hod empo and status_id=5
how databse conncect
HOD_EMP_NO -> department_heads -> department_id->employees -> employee_no ->study_leaves 
(conditions department_heads.emp_no=HOD_EMP_NO and study_leaves.status_id=5
write all backend logic on HODController

i need to correct this include hod/study_leave direcotry as the
study_leave_table.blade.php into the hod/index.blade.php not in to the hod/indexStudyLeave.blade.php
apply index.blade's black theme to study_leave_table



study ma/showStudyLeave.blade page and generate hod/study_leave/view_study_leave_form.balde 
That should be include  @include('ma.partials.studyLeave', ['readonly' => true]) 
add get HOD inputs for that
 Whether adequate staff available for the continuation of academic                                                     
 programs during the period of applicant’s leave  (yes or no radio button)

Whether satisfactory agreements can be made to                                   
cover applicant’s teaching activities and other commitments  (yes or no radio button)

Whether the applicant has served at least one(01) year in the Department? (yes or no)

Leave is recommended (yes or no radio btn)

*If not recommended please give reasons 

Any other remarks    

back end logic right HODController 



------------------------------------------------------------------------------------------------------------
study the Pending Study Leave Application table in the hod/study_leave/study_leave_table.blade file
then create this kind of table card to Pending Study Leave application in dean/study_leave direcotry as the
study_leave_table.blade.php
then include this card in the dean/index.blade.php (don't change the exiting componet of index file)
this table card only show study leave that realated to this dean empo and status_id=6
how databse conncect
DEAN_EMP_NO -> faculty_deans -> faculty_id->employees -> employee_no ->study_leaves 
(conditions faculty_deans.emp_no=HOD_EMP_NO and study_leaves.status_id=6
write all backend logic on DeanController



study hod/study_leave/view_study_leave_form.blade page and generate dean/study_leave/view_study_leave_form.balde 
That should be include  @include('ma.partials.studyLeave', ['readonly' => true]) 
add get Dean inputs for that are 
Is leave recommended ? yes /no (radio btn)
If not recommended please give reasons : (text area)
back end logic right DeanController 


-------------------------------------------------------------------------------------------------------------------
study the Pending Study Leave Application table in the hod/study_leave/study_leave_table.blade file
then create this kind of table card to Pending Study Leave application in vc/study_leave direcotry as the
study_leave_table.blade.php
then include this card in the vc/index.blade.php (don't change the exiting componet of index file)
this table card only show study leave that realated to this vc empo and status_id=7
how databse conncect
study_leaves table  ->empno->employees  table 
(conditions employees.main_branch_id=52 and study_leaves.status_id=7
write all backend logic on VCController


study hod/study_leave/view_study_leave_form.blade page and generate vc/study_leave/view_study_leave_form.balde 
That should be include  
 <!-- Study Leave Details (readonly) -->
    @include('StudyLeave.basic_info_form', ['readonly' => true])
    @include('StudyLeave.details_form', ['readonly' => true])
    @include('StudyLeave.working_covering_persons_form', ['readonly' => true])

add get vc inputs for that are 
Recommended to submit to Leave and Awards Committee ? yes /no (radio btn)
Recommended to submit to Leave and Awards Committee ? yes /no (radio btn)
back end logic right VCController 


-------------------------------------------------------------------------------------------------
'vc_recommend_submit_to_committee' txt
'vc_council_covering_approval_status'txt
'vc_not_approve_reason' => $request->vc_not_approve_reason,txt
'vc_remarks' => txt
'dean_empno' => 
'dean_leave_recommendation_status' txt
'dean_not_recommended_reason' txt
'dean_remarks' txt

-----------------------------developing Progress in MA Dash board----------------------

Analyse the exite tables and  UI themes of ma.studyLeave.blade.php
Then create separate table form class study_leave_progress_reports_table.blade.php in ma/study_leave directory
That should include to below of the exiting page of studyLeave.blade.php
Reference No,Employee No,Name with Initials,Department,Faculty,Applied Date,status,action are should be show in table
Use study_leaves,study_leave_progress_reports,employees,departments,faculties tables to implement this
write all backend logics in MAController

------------------------Add porgress report View MA ---------------------------------------------

Study the ma/study_leave/study_leave_extension_view_form.blade.php and then using the same theme colores
generate the ma/study_leave/study_leave_progress_report_view_form.blade.php. that should include study leave progress report pdf
(pdf store path can be extract from doucument path in study_leave_pogress_reports tables) and include
approved progress reports (stauts_id=1)
<!-- Study Leave Details (readonly) -->
    @include('StudyLeave.basic_info_form', ['readonly' => true])
    @include('StudyLeave.details_form', ['readonly' => true])
    @include('StudyLeave.working_covering_persons_form', ['readonly' => true])
write all backend logics in MAController

------------------------------HOD Extenstion table -----------------


Analyse the exite tables and  UI themes of ma/study_leave/study_leave_extensions_table.blade.php
Then create separate table form class study_leave_extensions_table.blade.php in hod/study_leave directory
That should include to below of the exiting page of hod/index.blade.php
write all backend logics in HODController

--------------------------view hod Extenstion ------------------------
study the ma/study_leave/study_leave_extension_view_form.blade.php
Make blade page that name is study_leave_extenstion_view_form.blade.php in hod/study_leave direcotry
And Under the more details tab,show study_lave_details Using including StudyLeave/basic_info_form.blade,details_form.blade.php,
working_covering_persons_form.blade.php and when apply this form use eanble read only options in forms
Then apply ma reamrk and return to user and forward to head button.
all the back end logics should be in HODController

remove Whether adequate staff available for the continuation of academic programs during the extended period? *,
Whether satisfactory agreements can be made to cover applicant's teaching activities and other commitments during the extension? *,
Whether the extension request is justified based on the service period and academic requirements? *
------------------------------Dean Extenstion table -----------------


Analyse the exite tables and  UI themes of ma/study_leave/study_leave_extensions_table.blade.php
Then create separate table form class study_leave_extensions_table.blade.php in dean/study_leave directory
That should include to below of the exiting page of dean/index.blade.php
write all backend logics in HODController

------------------------view dean Extenstion-------------------------------------------------

study the hod/study_leave/study_leave_extension_view_form.blade.php Then create same page like blade page that name is study_leave_extenstion_view_form.blade.php in dean/study_leave direcotry
all the back end logics should be in DeanController

recreate dean/study_leave/study_leave_extension_view_form.blade.php like that
study the hod/study_leave/study_leave_extension_view_form.blade.php
Make blade page that name is study_leave_extenstion_view_form.blade.php in dean/study_leave direcotry
And Under the more details tab,show study_lave_details Using including StudyLeave/basic_info_form.blade,details_form.blade.php,
working_covering_persons_form.blade.php and when apply this form use eanble read only options in forms
Then apply ma reamrk and return to user and forward to VC button.
all the back end logics should be in DeanController


dean/study_leave/study_leave_extenstion_view_form.blade.php


HOD Remarks should come below of the More Details - Orginal Study Leave Application


------------------------------VC Extenstion table -----------------


Analyse the exite tables and  UI themes of ma/study_leave/study_leave_extensions_table.blade.php
Then create separate table form class study_leave_extensions_table.blade.php in vc/study_leave directory
That should include to below of the exiting page of vc/index.blade.php
write all backend logics in VCController



------------------------view vc Extenstion-------------------------------------------------


study the hod/study_leave/study_leave_extension_view_form.blade.php
Make blade page that name is study_leave_extenstion_view_form.blade.php in vc/study_leave direcotry
And Under the more details tab,show study_lave_details Using including StudyLeave/basic_info_form.blade,details_form.blade.php,
working_covering_persons_form.blade.php and when apply this form use eanble read only options in forms
Then apply ma reamrk and return to user and forward to VC button.
all the back end logics should be in VCController
HOD Reviw and dean remok should come below of the More Details - Orginal Study Leave Application


 //'dean_empno' => self::DEAN_EMP_NO,
                //'dean_recommend' => $request->dean_recommend,
//'dean_remarks' => DB::raw("CONCAT(COALESCE(dean_remarks, ''), '" . addslashes($deanRemarks) . "')"),

		'vc_empno',
                'vc_recommend' ,
                'vc_not_recommend_reason', 
                'vc_remarks'

-----------------------------developing Progress in MA Dash board----------------------

Analyse the exite tables and  UI themes of ma/study_leave/study_leave_progress_reports_table.blade.php
Then create separate table form class study_leave_progress_reports_table.blade.php in hod/study_leave directory
That should include to below of the exiting page of hod/index.blade.php
Reference No,Employee No,Name with Initials,Department,Faculty,Applied Date,status,action are should be show in table
Use study_leaves,study_leave_progress_reports,employees,departments,faculties tables to implement this
write all backend logics in HODController


----------------------------Extenstion date restiction-----------------------------
Restict date selection in "To" field in study_leave_extension_form.blade.php
When user select the "From" date, then "To" date should be restict thate "To" date should be greater than "From" date
And also "To" date should not exceed more than 3 years from the original study leave and we can calculate that using till that date $remainingDays+"From" date

----------------------------HOD review section mandatory-----------------------------
make study_leave_hod_review_section.blade.php fields readonly and editeble according to readonly varibale true or false


all field in study_leave_hod_review_section.blade.php is field if available using hod_adequate_staff_available,hod_teaching_covered,hod_service_period,hod_recommend,hod_not_recommend_reason,hod_remarks

----------------------------------------------------------------------------------------------------------------------

 <div class="card mt-4">
            <div class="card-header card-header-dark text-white fw-semibold">
                <i class="fas fa-clipboard-check me-2"></i>HOD Review & Recommendation
            </div>
            <div class="card-body">
                
                <!-- Question 1 -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Whether adequate staff available for the continuation of academic programs during the period of applicant's leave?
                        <span class="text-danger">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_adequate_staff_available" id="adequateStaffYes" value="yes" required>
                        <label class="form-check-label" for="adequateStaffYes">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_adequate_staff_available" id="adequateStaffNo" value="no" required>
                        <label class="form-check-label" for="adequateStaffNo">
                            No
                        </label>
                    </div>
                </div>

                <!-- Question 2 -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Whether satisfactory agreements can be made to cover applicant's teaching activities and other commitments?
                        <span class="text-danger">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_teaching_covered" id="teachingCoveredYes" value="yes" required>
                        <label class="form-check-label" for="teachingCoveredYes">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_teaching_covered" id="teachingCoveredNo" value="no" required>
                        <label class="form-check-label" for="teachingCoveredNo">
                            No
                        </label>
                    </div>
                </div>

                <!-- Question 3 -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Whether the applicant has served at least one (01) year in the Department?
                        <span class="text-danger">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_service_period" id="servicePeriodYes" value="yes" required>
                        <label class="form-check-label" for="servicePeriodYes">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_service_period" id="servicePeriodNo" value="no" required>
                        <label class="form-check-label" for="servicePeriodNo">
                            No
                        </label>
                    </div>
                </div>

                <!-- Question 4 - Recommendation -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Leave is recommended
                        <span class="text-danger">*</span>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_recommend" id="recommendYes" value="yes" required>
                        <label class="form-check-label" for="recommendYes">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="hod_recommend" id="recommendNo" value="no" required>
                        <label class="form-check-label" for="recommendNo">
                            No
                        </label>
                    </div>
                </div>

                <!-- Conditional: If not recommended -->
                <div class="mb-4" id="notRecommendReasonDiv" style="display: none;">
                    <label for="hod_not_recommend_reason" class="form-label fw-semibold">
                        If not recommended, please give reasons
                        <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control" id="hod_not_recommend_reason" name="hod_not_recommend_reason" rows="4" 
                              placeholder="Please provide detailed reasons for not recommending this leave"></textarea>
                    <div class="invalid-feedback">
                        Please provide reasons for not recommending.
                    </div>
                </div>

                <!-- Any other remarks -->
                <div class="mb-4">
                    <label for="hod_remarks" class="form-label fw-semibold">
                        Any other remarks
                    </label>
                    <textarea class="form-control" id="hod_remarks" name="hod_remarks" rows="3" 
                              placeholder="Add any additional comments or remarks (optional)"></textarea>
                </div>

            </div>
        </div>


        ---------------------------------------------------------------------------------------------------
        ----------------------------Dean review section mandatory-----------------------------
make study_leave_dean_review_section.blade.php fields readonly and editeble according to readonly varibale true or false


all field in study_leave_hod_review_section.blade.php is field if available using hod_adequate_staff_available,hod_teaching_covered,hod_service_period,hod_recommend,hod_not_recommend_reason,hod_remarks

----------------------------------------------------------------------------------------------------------------------



