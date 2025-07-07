<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminIndex(){

        return view('backend.pages.dashboard.index');
    }
    public function superAdminIndex(){
        return view('backend.pages.dashboard.index');
    }

    public function employeeIndex(){
        return view('frontend.pages.employee-dashboard.index');
    }
}
