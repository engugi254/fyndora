<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     * Admin will go to /admin
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * Only guests can access login, except logout.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
