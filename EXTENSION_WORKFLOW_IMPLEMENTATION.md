# Study Leave Extension Workflow Implementation

## Overview
This document describes the complete study leave extension approval workflow implementation.

## Status Codes

| Status ID | Status Name | Description |
|-----------|-------------|-------------|
| 1 | Approved | Extension has been approved by VC |
| 2 | Rejected | Extension has been rejected |
| 3 | Editing | Extension is being edited |
| 4 | Processing MA | Extension returned to Management Assistant for corrections |
| 5 | Processing HOD | Extension is being reviewed by Department HOD |
| 6 | Processing Dean | Extension is being reviewed by Dean |
| 7 | Processing VC | Extension is being reviewed by Vice Chancellor |
| 8 | VC Checked | Extension has been checked by VC |
| 9 | Processing HOD Academic Establishment | Used for study leave approvals |
| 10 | Processing Registrar | Extension is being reviewed by HOD Academic Establishment/Registrar |

## Extension Approval Workflow

```
User → MA → HOD Academic Establishment → Department HOD → Dean → VC
       (10)         (5)                       (6)         (7)    (1)
```

### Workflow Steps

1. **Management Assistant (MA) Review**
   - Lists extensions submitted by users under their jurisdiction
   - Can forward to HOD Academic Establishment (Status → 10)
   - Can return to user for corrections (Status → 4)
   - Controller: `MAController.php`
   - Methods: `forwardExtension()`, `returnExtension()`

2. **HOD Academic Establishment / Registrar Review**
   - Lists extensions with `status_id = 10`
   - Reviews extension request and MA remarks
   - Can forward to Department HOD (Status → 5)
   - Can return to MA for corrections (Status → 4)
   - Controller: `HODAcademicEstablishmentController.php`
   - Methods: `study_leave_extenstions()`, `showExtension()`, `forwardExtension()`, `returnExtension()`
   - Route Prefix: `/HODAcademicEstablishment/extension/`
   - View: `resources/views/hod_academic_establishment/study_leave/study_leave_extension_view_form.blade.php`
   - Fields: `registrar_empno`, `registrar_remarks`

3. **Department HOD Review**
   - Lists extensions with `status_id = 5`
   - Reviews and provides recommendation
   - Can forward to Dean (Status → 6)
   - Can return to user (Status → 3)
   - Controller: `HODController.php`
   - Methods: `approveExtension()`, `returnExtension()`

4. **Dean Review**
   - Lists extensions with `status_id = 6`
   - Reviews and provides recommendation
   - Can forward to VC (Status → 7)
   - Can return
   - Controller: `DeanController.php`

5. **Vice Chancellor (VC) Approval**
   - Lists extensions with `status_id = 7`
   - Final approval decision
   - Can approve (Status → 1)
   - Can reject (Status → 2)
   - Controller: `VCController.php`

## Database Schema

### study_leave_extensions Table
Key fields related to workflow:
- `status_id`: Current status in approval workflow
- `ma_empno`: Management Assistant employee number
- `ma_remarks`: MA's remarks
- `registrar_empno`: HOD Academic Establishment employee number
- `registrar_remarks`: Registrar's remarks
- `hod_empno`: Department HOD employee number
- `hod_recommend`: HOD recommendation (yes/no)
- `hod_remarks`: HOD remarks
- `dean_leave_recommendation_status`: Dean recommendation
- `dean_empno`: Dean employee number
- `dean_remark`: Dean remarks
- `vc_empno`: VC employee number
- `vc_recommend`: VC recommendation
- `vc_remarks`: VC remarks

## Routes

### HOD Academic Establishment Extension Routes
```php
Route::get('/HODAcademicEstablishment/extension/{extension_id}', 
    [HODAcademicEstablishmentController::class, 'showExtension'])
    ->name('hodacademicestablishment.show.extension');

Route::post('/HODAcademicEstablishment/extension/{extension_id}/forward', 
    [HODAcademicEstablishmentController::class, 'forwardExtension'])
    ->name('hodacademicestablishment.extension.forward');

Route::post('/HODAcademicEstablishment/extension/{extension_id}/return', 
    [HODAcademicEstablishmentController::class, 'returnExtension'])
    ->name('hodacademicestablishment.extension.return');
```

## Key Implementation Details

### HOD Academic Establishment Role
- Acts as "Registrar" in the extension workflow
- Positioned between MA and Department HOD
- Employee number: 12453 (defined as `HOD_EMP_NO` constant)
- Reviews extensions before they reach department level
- Provides administrative oversight

### Status Transitions
- Forward flow increases status (4→10→5→6→7→1)
- Return flow sends back to previous stage (→4 for MA)
- Each approval stage saves empno and remarks

### Validation
- Forward action: `registrar_remarks` is optional
- Return action: `registrar_remarks` is required
- JavaScript validation on client side
- Laravel validation on server side

## Files Modified

1. **database/seeders/StatusesSeeder.php**
   - Added status 9 and 10

2. **app/Http/Controllers/MAController.php**
   - Updated `forwardExtension()` to use status 10

3. **app/Http/Controllers/HODAcademicEstablishmentController.php**
   - Updated `study_leave_extenstions()` to query status 10
   - Added `showExtension()` method
   - Added `forwardExtension()` method (forwards to status 5)
   - Added `returnExtension()` method (returns to status 4)

4. **routes/web.php**
   - Added 3 routes for HOD Academic Establishment extension handling

5. **resources/views/hod_academic_establishment/study_leave/study_leave_extension_view_form.blade.php**
   - Complete refactoring to use HOD Academic Establishment routes
   - Changed labels from HOD to Registrar
   - Simplified form (removed recommendation radio buttons)
   - Updated forward target to Department HOD
   - Updated return target to MA

## Testing Checklist

- [ ] MA can forward extension to HOD Academic Establishment (status → 10)
- [ ] HOD Academic Establishment sees extensions with status 10
- [ ] HOD Academic Establishment can view extension details
- [ ] HOD Academic Establishment can forward to Department HOD (status → 5)
- [ ] HOD Academic Establishment can return to MA (status → 4)
- [ ] Department HOD sees extensions with status 5
- [ ] Department HOD can forward to Dean (status → 6)
- [ ] Dean can forward to VC (status → 7)
- [ ] VC can approve (status → 1)
- [ ] All remarks are saved correctly
- [ ] Department head information displays correctly
- [ ] Validation works (remarks required for return)
