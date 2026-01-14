<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HODAcademicEstablishmentController extends Controller
{
    //
    public function study_lave()
    {
        return view('hod.academic_establishment.study_leave_index');
    }
}
