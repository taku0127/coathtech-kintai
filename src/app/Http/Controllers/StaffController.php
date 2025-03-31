<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    //
    public function staffListIndex(){
        $users = User::all();
        return view('pages.admin.staff_list',compact('users'));
    }
}
