<?php

namespace App\Http\Controllers;

use App\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Helpers\CMail;
use App\Models\User;

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

    public function sendPasswordResetLink(Request $request){
        // Validate form forget apssword
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ],[
            'email.required' => 'The :attribute is required',
            'email.email'=> 'Invalid email address',
            'email.exists' => 'we can not find a user with this email address'
        ]);

        // Get User Details
        $user = User::where('email', $request->email)->first();

        // Generate Token
        $token = base64_encode(Str::random(64));

        // Check if there is an existing token
        $oldToken = DB::table('password_reset_tokens')
                        ->where('email',$user->email)
                        ->first();

        if ($oldToken) {
            // update existing token
            DB::table('password_reset_tokens')
                ->where('email',$user->email)
                ->update([
                    'token' =>$token,
                    'created_at' => Carbon::now()
                ]);
        } else {
            // Add new reset password token
            DB::table('password_reset_tokens')->insert([
                'email'=> $user->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
        }

        // create clicable action link

        $actionLink = route('admin.reset_password_form', ['token' => $token]);

        $data = array(
            'actionLink'=> $actionLink,
            'user' => $user
        );

        $mail_body = view('email-templates.forget-template', $data)->render();

        $mailConfig = array(
            'recipient_address' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Reset Password',
            'body'=> $mail_body
        );
        
        if (CMail::send($mailConfig)) {
            return redirect()->route('admin.forget')->with('success', 'We have e-mailed your password rest link');
        } else {
            return redirect()->route('admin.forget')->with('fail','Something went wrong. Resetting password link not sent. Try again later.');
        }
        
    }

    public function resetForm(Request $request, $token = null){
        // Check if this token is exists
        $isTokenExists = DB::table('password_reset_tokens')
                            ->where('token', $token)
                            ->first();

        if (!$isTokenExists) {
            return redirect()->route('admin.forget')->with('fail', 'Invalid token. Request another reset password link');
        } else {
            // Check if Token is not expired
            $diffMins = Carbon::createFromFormat('Y-m-d H:i:s', $isTokenExists->created_at)->diffInMinutes(Carbon::now());
            
            if($diffMins > 15){
                // When token is older than 15 minutes
                return redirect()->route('admin.forget')->with('fail','The Password reset link you clicked has expired. Please request a new link');
            }

            $data = [
                'pageTitle' => 'Reset Password',
                'token' => $token
            ];

            return view('back.pages.auth.reset', $data);
        }
    }

    public function resetPasswordHandler(Request $request){
        // Validate the form
        $request->validate([
            'new_password' =>'required|min:6|required_with:new_password_confirmation|same:new_password_confirmation',
            'new_password_confirmation' => 'required'
        ]);

        $dbToken = DB::table('password_reset_tokens')
                        ->where('token', $request->token)
                        ->first();

        // Get User Details
        $user = User::where('email', $dbToken->email)->first();

        // Update Password
        User::where('email', $user->email)->update([
            'password'=>Hash::make($request->new_password)
        ]);

        // Send notification email
        $data = array(
            'user'=>$user,
            'new_password'=>$request->new_password
        );

        $mail_body = view('email-templates.password-changes-template', $data)->render();

        $mailConfig = array(
            'recipient_address' => $user->email,
            'recipient_name' => $user->name,
            'subject' => 'Password Changed',
            'body'=>$mail_body
        );

        if (CMail::send($mailConfig)) {
            // Delete Token from DB
            DB::table('password_reset_tokens')->where([
                'email'=> $dbToken->email,
                'token'=> $dbToken->token,
            ])->delete();
            return redirect()->route('admin.login')->with('success', 'Done!, Your Password has been change successfully. Use your new password for login into system');
        } else {
            return redirect()->route('admin.reset_password_form', ['token' => $dbToken->token])->with('fail', 'Something wnet wrong. Try again later');
        }
        
    }
}
