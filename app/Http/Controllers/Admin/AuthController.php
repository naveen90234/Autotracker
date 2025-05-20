<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Auth;
use Hash;
use Session;
use Validator;
use DB;
use Str;
use App\Lib\Email;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {
            try {

                $validator = Validator::make($request->all(), [
                    'email' => 'required|email|exists:admins,email',
                    'password' => 'required|max:60'
                ]);
                if ($validator->fails()) {
                    foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                        if (!isset($firstError))
                            $firstError = $messages[0];
                        $error[$field_name] = $messages[0];
                    }

                    return redirect("admin/login")->with('error_msg', $firstError)->send();
                } else {
                    $admin = Admin::where('email', $request->get('email'))->first();
                    if ($admin) {
                        if (Hash::check($request->get('password'), $admin->password)) {
                            if (Auth::guard('admin')->attempt(['email' => $request->get('email'), 'password' => $request->get('password')], $request->checkbox)) {

                                return redirect("admin/home");
                            } else {
                                return redirect("admin/login")->with('error_msg', "Something went wrong, please try again.")->send();
                            }
                        } else {
                            return redirect("admin/login")->with('error_msg', "Incorrect Password.")->send();
                        }
                    } else {
                        return redirect("admin/login")->with('error_msg', "The email does not exist.")->send();
                    }
                }
            } catch (\Exception $e) {
                return ['status' => 'false', 'message' => $e->getMessage()];
            }
        }
        $title = __('Admin Login');
        return view('admin.auth.login', compact('title'));
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        // Session::flush();
        return redirect(route('admin.login'));
    }


    public function forgotPasswordView()
    {
        $title = __('Admin Login');
        return view('admin.auth.forgot-password', compact('title'));
    }


    public function forgotPassword(Request $request)
    {
        if ($request->isMethod('post')) {
            try {
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email|exists:admins,email',
                ]);
                if ($validator->fails()) {
                    foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                        if (!isset($firstError))
                            $firstError = $messages[0];
                        $error[$field_name] = $messages[0];
                    }

                    return redirect()->back()->with('error_msg', $firstError)->send();

                } else {
                    $admin = Admin::where('email', $request->get('email'))->first();
                    if ($admin) {
                        $token = Str::random(60);
                        $check = DB::table('password_resets')->where('email', $request->get('email'))->first();
                        if ($check) {
                            DB::table('password_resets')->where('email', $request->get('email'))->update(['token' => $token, 'created_at' => date("Y-m-d H:i:s")]);
                        } else {
                            DB::table('password_resets')->insert(['email' => $request->get('email'), 'token' => $token, 'created_at' => date("Y-m-d H:i:s")]);
                        }
                        $mail_data['url'] = '<a class="btn-mail" href="' . route('admin.resetpassword', $token) . '">Reset Password</a>';

                        Email::send('forgot-password', $mail_data, $request->get('email'), 'Reset Password');
                        return redirect()->back()->with('success_msg', "We have emailed you password reset link!")->send();
                    } else {

                        return redirect()->back()->with('error_msg', 'The email does not exist.')->send();
                    }
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error_msg', $e->getMessage())->send();
            }
        }
    }

    public function resetPassword(Request $request, $token)
    {
        if ($request->ajax() && $request->isMethod('post')) {
            try {
                $validator = Validator::make($request->all(), [
                    'password' => 'required|min:8|max:45|required_with:password_confirmation|confirmed',
                    'password_confirmation' => 'required'
                ]);
                if ($validator->fails()) {
                    return response()->json(array('errors' => $validator->messages()), 422);
                } else {
                    $token_data = DB::table('password_resets')->where('token', $token)->first();
                    if ($token_data) {
                        $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $token_data->created_at);
                        $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
                        $diff_in_minutes = $to->diffInMinutes($from);
                        if ($diff_in_minutes <= 60) {
                            $new_password = Hash::make(trim($request->get('password')));
                            Admin::where('email', $token_data->email)->update(['password' => $new_password]);
                            DB::table('password_resets')->where('token', $token)->delete();
                            $message = 'Your password has been changed successfully.';
                            Session::flash('success', $message);
                            return ['status' => 'true', 'message' => $message, 'url' => route('admin.login')];
                        } else {
                            return ['status' => 'false', 'message' => "You password reset token has expired."];
                        }
                    } else {
                        return ['status' => 'false', 'message' => "You password reset token has expired."];
                    }
                }
            } catch (\Exception $e) {
                return ['status' => 'false', 'message' => $e->getMessage()];
            }
        }
        $token_data = DB::table('password_resets')->where('token', $token)->first();
        if ($token_data) {
            $to = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $token_data->created_at);
            $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date("Y-m-d H:i:s"));
            $diff_in_minutes = $to->diffInMinutes($from);
            if ($token_data && $diff_in_minutes <= 60) {
                $title = "Reset Password";
                return view('admin.auth.reset_password', compact('title', 'token'));
            } else {
                $message = "Your reset password link has expired or already been used.";
                Session::flash('danger', $message);
                return redirect(route('admin.login'));
            }
        } else {
            $message = "Your reset password link has expired or already been used.";
            Session::flash('danger', $message);
            return redirect(route('admin.login'));
        }
    }
}
