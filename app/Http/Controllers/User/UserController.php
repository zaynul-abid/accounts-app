<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(){

        $users = User::paginate(10);
        return view('backend.pages.users.index',compact('users'));
    }
    public function create(){

    }
    public function store(Request $request){

    }
    public function show($id){

    }
    public function edit($id){

    }
    public function update(Request $request, $id){

    }
    public function destroy($id){

    }
}
