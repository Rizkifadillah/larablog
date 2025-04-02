<?php

namespace App\Http\Controllers;

use App\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm(Request $request){
        $data = [
            'pageTitle'=>'Login'
        ];
        return view('back.pages.auth.login', $data);
    }

    public function forgetForm(Request $request){
        $data = [
            'pageTitle'=>'Forget Password'
        ];
        return view('back.pages.auth.forget', $data);
    }

    public function loginHandler(Request $request){
        // variable kondisi bisa menggunakan 'email' atau 'username'
        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if ($fieldType=='email') {
            $request->validate([
                'login_id' => 'required|email|exists:users,email',
                'password' => 'required|min:5'
            ],[
                'login_id.required' => 'Enter your email or username',
                'login_id.email' => 'Invalid email address',
                'login_id.exists' => 'No account found for this email'
            ]);
        }else{
            $request->validate([
                'login_id' => 'required|exists:users,username',
                'password' => 'required|min:5'
            ],[
                'login_id.required' => 'Enter your username or username',
                'login_id.username' => 'Invalid username address',
                'login_id.exists' => 'No account found for this username'
            ]);
        }

        // function auth
        $creds = array(
            $fieldType => $request->login_id,
            'password' => $request->password
        );

        if (Auth::attempt($creds)) {
            // Check if account is inactive mode
            if ( Auth::user()->status == UserStatus::Inactive) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('fail','Your account is currently inactive, Please, contact support at (support@jayatama.id) for further assistance');
            }

            // Check if account is pending mode
            if ( Auth::user()->status == UserStatus::Pending) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->with('fail','Your account is currently pending approval, Please, check your email for further instructions 
                or contact support at (support@jayatama.id) assistance');
            }

            // Redirect use to dashboard
            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->withInput()->with('fail','Incorrect password');
        }
        
    }
}
