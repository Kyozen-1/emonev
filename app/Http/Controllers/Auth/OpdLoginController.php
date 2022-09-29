<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OpdLoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest:opd')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.opd.login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);
        // Attempt to log the user in
        if (Auth::guard('opd')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return redirect()->intended(route('opd.dashboard.index'));
        }
        // if unsuccessful, then redirect back to the login with the form data
        return redirect()->back()->withInput($request->only('email', 'remember'));
    }

    public function logout()
    {
        Auth::guard('opd')->logout();
        return redirect('opd/login');
    }
}
