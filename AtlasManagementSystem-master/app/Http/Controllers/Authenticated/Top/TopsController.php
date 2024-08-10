<?php

namespace App\Http\Controllers\Authenticated\Top;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\User;
use Auth;

class TopsController extends Controller
{
    public function show(){

        $query = User::with('user')->whereIn('role', [1,2,3]);
        return view('authenticated.top.top');
    }

    public function logout(){
        Auth::logout();
        return redirect('/login');
    }
}