<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Notifications;
use Illuminate\Http\Request;
use App\Models\UserDevice;
use App\Models\BlockUser;
use App\Models\UserOtp;
use App\Models\Vehicle;
use App\Models\UserInterest;
use App\Models\Setting;
use App\Lib\Uploader;
use App\Models\User;
use Carbon\Carbon;
use App\Lib\Email;
use Validator;
use JWTAuth;
use Hash;
use DB;
use Config;
use App\Models\ClearedBroadcastNotification;
use App\Models\SeenBroadcastNotification;
use App\Models\ReportUser;

class UserController extends Controller
{

    public $otpExpiryTime = '+5 minute';



    public function checkUser(Request $req)
    {
        try {

            $validator = Validator::make($req->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                $formData = $req->all();

                $checkEmail = User::where('email', '=', $req->email)->first();
                if ($checkEmail) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.Email_exist'), 'data' => (object) []]);
                } else {

                    $otp_expiry = date('Y-m-d H:i:s', strtotime($this->otpExpiryTime));
                    $otp = generateEmailOTP();

                    $userOtp = UserOtp::where('email', '=', $req->email)->first();
                    if ($userOtp) {

                        $current_time = date('Y-m-d H:i:s');
                        $otp_expiry_time = date('Y-m-d H:i:s', strtotime($userOtp->opt_expiry));


                        /*  if (strtotime($current_time) <= strtotime($otp_expiry_time)) {
                              $message = "You can resend OTP after 5 minute.";
                              return response()->json(['status' => false, 'message' => $message, 'data' => (object)[]]);
                          } */


                        $userOtp->update(['otp' => $otp, 'opt_expiry' => $otp_expiry]);

                        $email_data['otp'] = $otp;
                        Email::send('email-verification', $email_data, $req->email, "Verify Email OTP");

                        $message = Config::get('message-constants.OTP_Resent');

                        $formData['otp'] = $otp;

                        return response()->json(['status' => true, 'message' => $message, 'data' => $formData]);
                    } else {
                        UserOtp::create(['email' => $req->email, 'otp' => $otp, 'opt_expiry' => $otp_expiry]);

                        $email_data['otp'] = $otp;
                        Email::send('email-verification', $email_data, $req->email, "Verify Email OTP");

                        $message = Config::get('message-constants.OTP_Verification');
                        $formData['otp'] = $otp;

                        return response()->json(['status' => true, 'message' => $message, 'data' => $formData]);
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }




    // This method use for signup along with signup otp verification.
    protected function signup(Request $request)
    {
        try {
            $data = $request->all();
            $otp_expiry = date('Y-m-d H:i:s', strtotime($this->otpExpiryTime));
    
            $validator = Validator::make($data, [
                'email' => 'required|string|email|unique:users,email',
                'password' => 'required',
                'user_name' => 'required',
                'mobile_number' => 'required',
                'profile_picture' => 'nullable',
                'country_code' => 'required',
                'device_type' => 'required|in:IPHONE,ANDROID,IOS',
                'device_token' => 'required',
                'device_data' => 'required',
                'device_uniqueid' => 'required',
                'otp' => 'required|min:6|max:6',
                'isdont_askon' => 'required',
                'zip_code' => 'required'
            ], [
                'email.unique' => 'Email address already exists',
            ]);
    
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $error]);
            } 
    
            $date = now();
    
            // Verify OTP before registering user
            $userOtp = UserOtp::where(['email' => $data['email'], 'otp' => $data['otp']])->first();
            if (!$userOtp) {
                $message = Config::get('message-constants.Valid_OTP');
                return response()->json(['status' => false, 'message' => $message, 'data' => (object) []]);
            }
    
            // Check OTP expiry
            if (now()->gt($userOtp->opt_expiry)) {
                $message = Config::get('message-constants.OTP_expired');
                return response()->json(['status' => false, 'message' => $message, 'data' => (object) []]);
            }
    
            // Delete OTP
            UserOtp::where('email', $data['email'])->delete();
    
            // Prepare user data
            $formData = [
                'email' => $data['email'],
                'user_name' => $data['user_name'],
                'mobile_number' => $data['mobile_number'],
                'country_code' => $data['country_code'],
                'zip_code' => $data['zip_code'],
                'password' => Hash::make($data['password']),
                'verified_at' => $date,
                'otp_verify' => 1,
                'status' => 1,
                'garagecount' => 0, // Default value set to 0
            ];
    
            if ($request->hasFile('profile_picture')) {
                $imagePath = $request->file('profile_picture');
                $imageName = time() . '.' . $imagePath->getClientOriginalExtension();
                $imagePath->move(public_path('uploads/profile/'), $imageName);
                $formData['profile_picture'] = '/public/uploads/profile/' . $imageName;
            }
    
            // Create user
            // Create user
                $user = User::create($formData);

                // Insert data in user device table
                UserDevice::deviceHandle([
                    "id" => $user->id,
                    "device_type" => $data['device_type'],
                    "device_token" => $data['device_token'],
                    "device_data" => $data['device_data'],
                    "device_uniqueid" => $data['device_uniqueid'],
                    'isdont_askon' => $data['isdont_askon'],
                ]);

                // Generate JWT token
                $jwtToken = JWTAuth::fromUser($user);

                $user->isdont_askon = (int) $data['isdont_askon'];
                $user->is_two_factor = 0;

                // 🔥 Count user rides (from vehicles table)
                $user->rides_count = Vehicle::where('user_id', $user->id)->count();

                $message = Config::get('message-constants.Signup_successful');

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'data' => $user,
                    'token' => $jwtToken,
                ]);

    
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }
    


    // This method use for signin
    protected function signin(Request $request)
    {
        try {

            $data = $request->all();
            $date = date('Y-m-d h:i:s', time());
            $validator = Validator::make($data, [
                'email' => 'required|email|string',
                'password' => 'required',
                'device_type' => 'required|in:IPHONE,ANDROID,IOS',
                'device_token' => 'required',
                'device_uniqueid' => 'required',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $error]);
            } else {

                $user = User::where(['email' => $data['email']])->first();

                if (!$user) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                }
                if ($user->verified_at == '' && $user->status == 0 && $user->otp_verify == 0) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.Account_not_verified'), 'data' => (object) []], 201);
                }
                if ($user->status == 0) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.Account_deactivated'), 'data' => (object) []], 201);
                }

                if (Hash::check($data['password'], $user->password)) {
                    $input = $request->only('email', 'password');
                    $jwtToken = JWTAuth::attempt($input);
                    //$jwtToken = JWTAuth::fromUser($user);
                    if (!$jwtToken) {

                        // if the credentials are wrong we send an unauthorized error in json format
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.Wrong_credentials'), 'data' => (object) []]);
                    } else {
                        UserDevice::deviceHandle([
                            "id" => $user->id,
                            "device_type" => $data['device_type'],
                            "device_token" => $data['device_token'],
                            "device_uniqueid" => $data['device_uniqueid'],
                            "isdont_askon" => '',
                        ]);

                        //get device detail
                        $device = UserDevice::where(['user_id' => $user->id, 'device_type' => $data['device_type'], 'device_uniqueid' => $data['device_uniqueid']])->first();

                        $user->isdont_askon = $device->isdont_askon;
                        $user->rides_count = Vehicle::where('user_id', $user->id)->count();

                        return response()->json([
                            'status' => true,
                            //'expires' => auth('api')->factory()->getTTL() * 6000, // time to expiration
                            'message' => Config::get('message-constants.Login_success'),
                            'data' => $user,
                            'type' => 'bearer',
                            'token' => $jwtToken,
                        ]);
                    }
                } else {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.Wrong_credentials'), 'data' => (object) []]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    //Forget Password
    public function forgotPasswordOtp(Request $request)
    {
        try {

            $data = $request->all();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);
            if ($validator->fails()) {

                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {
                $user = User::where('email', $data['email'])->first();

                $data = [];
                $data['email'] = $request->email;
                if (!$user) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist')]);
                } else {
                    if ($user->otp_verify == 0 && $user->status == 0) {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.Account_not_verified'), 'data' => (object) []]);
                    } else if ($user->status == 0) {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.Account_deactivated'), 'data' => (object) []]);
                    } else {

                        $otp_expiry = date('Y-m-d H:i:s', strtotime($this->otpExpiryTime));
                        $otp = generateEmailOTP();

                        $data['otp'] = $otp;

                        $userOtp = UserOtp::where('email', '=', $request->email)->first();
                        if ($userOtp) {

                            $current_time = date('Y-m-d H:i:s');
                            $otp_expiry_time = date('Y-m-d H:i:s', strtotime($userOtp->opt_expiry));


                            /* if (strtotime($current_time) <= strtotime($otp_expiry_time)) {
                                 $message = "You can resend OTP after 5 minute.";
                                 return response()->json(['status' => false, 'message' => $message, 'data' => (object)[]]);
                             } */


                            $userOtp->update(['otp' => $otp, 'opt_expiry' => $otp_expiry]);

                            // Send Email
                            $email_data['otp'] = $otp;
                            $email_data['url'] = '';
                            Email::send('reset-password', $email_data, $request->email, "Reset Password Verification Code");

                            return response()->json(['status' => true, 'message' => Config::get('message-constants.Reset_Password_OTP'), 'data' => $data]);
                        } else {
                            UserOtp::create(['email' => $request->email, 'otp' => $otp, 'opt_expiry' => $otp_expiry]);

                            // Send Email
                            $email_data['otp'] = $otp;
                            $email_data['url'] = '';
                            Email::send('reset-password', $email_data, $request->email, "Reset Password Verification Code");

                            return response()->json(['status' => true, 'message' => Config::get('message-constants.Reset_Password_OTP'), 'data' => $data]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }


    // Verify OTP
    public function verifyOtp(Request $request)
    {

        try {
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'otp' => 'required|min:6|max:6'
            ]);
            if ($validator->fails()) {

                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                $user = User::where('email', $data['email'])->first();
                if (!$user) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                }
                $userOtp = UserOtp::where(['email' => $user->email, 'otp' => $request->otp])->first();

                if ($userOtp) {

                    $current_time = date('Y-m-d H:i:s');
                    $otp_expiry = date('Y-m-d H:i:s', strtotime($userOtp->opt_expiry));

                    // if (strtotime($current_time) > strtotime($otp_expiry)) {
                    //     $message = "OTP expired please resend OTP.";
                    //     return response()->json(['status' => false, 'message' => $message, 'data' => (object) []]);
                    // }

                    if ($user->status == 0) {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.Account_deactivated'), 'data' => (object) []]);
                    } else {

                        //delete OTP.
                        UserOtp::where('email', $user->email)->delete();
                        return response()->json(['status' => true, 'message' => Config::get('message-constants.OTP_verified'), 'data' => $user]);
                    }
                } else {
                    $message = Config::get('message-constants.Valid_OTP');
                    return response()->json(['status' => false, 'message' => $message, 'data' => (object) []]);
                }
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }


    // This method use for forgot password
    public function resetPassword(Request $request)
    {
        try {
            $response['status'] = false;
            $response['data'] = (object) [];
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {
                $user = User::where('email', $data['email'])->first();
                if (!$user) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                } else {

                    if ($user->status == 0) {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.Account_deactivated'), 'data' => (object) []]);
                    } else {

                        $user->password = Hash::make($data['password']);
                        $user->save();

                        return response()->json(['status' => true, 'message' => Config::get('message-constants.Password_Reset'), 'data' => $user]);
                    }

                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    // This method is use to resend
    protected function sendOtp(Request $request)
    {
        try {

            $data = $request->all();
            $validator = Validator::make($data, [
                'email' => 'required|string|email',
                'is_resend' => 'required|in:0,1',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $error]);
            } else {

                if ($request->is_resend) {
                    $message = Config::get('message-constants.OTP_Verification_resent');
                } else {
                    $message = Config::get('message-constants.OTP_Verification');
                }

                $checkEmail = User::where('email', $request->email)->first();
                if (!$checkEmail) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.Email_not_exist'), 'data' => (object) []]);
                } else {

                    $otp_expiry_time = date('Y-m-d H:i:s', strtotime($this->otpExpiryTime));
                    $otp = generateEmailOTP();

                    $data = [];
                    $data['email'] = $request->email;
                    $data['otp'] = $otp;

                    $userOtp = UserOtp::where('email', $request->email)->first();
                    if ($userOtp) {

                        $current_time = date('Y-m-d H:i:s');
                        $otp_expiry = date('Y-m-d H:i:s', strtotime($userOtp->opt_expiry));

                        /* if (strtotime($current_time) <= strtotime($otp_expiry)) {
                             $message = "You can resend OTP after 5 minute.";
                             return response()->json(['status' => false, 'message' => $message, 'data' => (object)[]]);
                         } */

                        $userOtp->update(['otp' => $otp, 'opt_expiry' => $otp_expiry_time]);

                        $email_data['otp'] = $otp;
                        Email::send('otp-verification', $email_data, $request->email, "Verify Email OTP");

                        return response()->json(['status' => true, 'message' => $message, 'data' => $data]);
                    } else {
                        UserOtp::create(['email' => $request->email, 'otp' => $otp, 'opt_expiry' => $otp_expiry_time]);

                        $email_data['otp'] = $otp;
                        Email::send('otp-verification', $email_data, $request->email, "Verify Email OTP");

                        return response()->json(['status' => true, 'message' => $message, 'data' => $data]);
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }


    // This method use to Verify signin Otp
    protected function verifyTwoFactorAuth(Request $request)
    {
        try {

            $data = $request->all();
            $date = date('Y-m-d h:i:s', time());
            $validator = Validator::make($data, [
                'device_uniqueid' => 'required|string|max:255',
                'isdont_askon' => 'required|in:0,1',
                'otp' => 'required|min:6|max:6',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $error]);
            } else {

                $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

                $user = User::active()->find($userId);
                if (!$user) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                }

                $userOtp = UserOtp::where(['email' => $user->email, 'otp' => $request->otp])->first();

                if ($userOtp) {

                    $current_time = date('Y-m-d H:i:s');
                    $otp_expiry = date('Y-m-d H:i:s', strtotime($userOtp->opt_expiry));

                    if (strtotime($current_time) > strtotime($otp_expiry)) {
                        $message = Config::get('message-constants.OTP_expired');
                        return response()->json(['status' => false, 'message' => $message, 'data' => (object) []]);
                    }


                    $chkDevice = UserDevice::where(['user_id' => $userId, 'device_uniqueid' => $request->device_uniqueid])->first();
                    if ($chkDevice) {
                        $chkDevice->isdont_askon = $request->isdont_askon;
                        $chkDevice->save();

                        //delete OTP.
                        UserOtp::where('email', $user->email)->delete();

                        return response()->json(['status' => true, 'message' => Config::get('message-constants.Success'), 'data' => $chkDevice]);
                    } else {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.Data_not_found'), 'data' => (object) []]);
                    }
                } else {
                    $message = Config::get('message-constants.Valid_OTP');
                    return response()->json(['status' => false, 'message' => $message, 'data' => (object) []]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    // user logout
    public function userLogout(Request $request)
    {
        try {

            $data = $request->all();
            $response['status'] = false;
            $response['message'] = '';
            $response['data'] = (object) [];

            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            if (!$userId) {
                throw new \Exception("Error Processing Request", 1);
            }

            $validator = Validator::make($request->all(), [
                'device_type' => 'required|in:IPHONE,ANDROID,IOS',
                'device_token' => 'required',
            ]);

            if ($validator->fails()) {
                $response['message'] = $validator->errors()->first();
            } else {
                $userLogged = JWTAuth::toUser(JWTAuth::getToken());
                $userDevice = UserDevice::where(['user_id' => $userLogged->id, 'device_type' => $data['device_type'], 'device_token' => $data['device_token']])->first();
                if ($userDevice) {
                    JWTAuth::invalidate(JWTAuth::getToken());

                    $userDevice->device_type = NULL;
                    $userDevice->device_token = NULL;
                    $userDevice->save();
                    $response['status'] = true;
                    $response['message'] = Config::get('message-constants.Logout_success');
                } else {
                    $response['message'] = Config::get('message-constants.Logout_error');
                }
            }
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    //use to create and edit user profile
    public function createEditProfile(Request $request)
    {
        try {

            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                'user_name' => 'nullable',
                'country_code' => 'nullable',
                'mobile_number' => 'nullable',
                'bio' => 'nullable',
                'latitude'        => 'nullable',
                'longitude'       => 'nullable',
                'profile_picture' => 'nullable|mimes:jpeg,png,jpg,gif,svg',
                'key' => 'required|in:1,0'
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {


                $user = User::find($userId);

                $data = $request->all();
                //dd($data);

                if ($request->hasFile('profile_picture')) {
                    $imagePath = $request->file('profile_picture');
                    $imageName = time() . '.' . $imagePath->getClientOriginalExtension();
                    $imagePath->move(public_path('uploads/profile/'), $imageName);
                    $data['profile_picture'] = '/public/uploads/profile/' . $imageName;
                    }

                
                $updateStatus = $user->update($data);


                if ($request->key) {
                    $message = Config::get('message-constants.Profile_updated');
                } else {
                    $message = Config::get('message-constants.Profile_created');
                }

                if ($updateStatus) {
                    return response()->json(['status' => true, 'message' => $message, 'data' => $user]);
                } else {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.Something_wrong'), 'data' => (object) []]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $response['status'] = false;
            $response['data'] = (object) [];
            $data = $request->all();
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required',
            ]);
            if ($validator->fails()) {

                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {
                $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

                $user = User::find($userId);
                if (!$user) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                } else {
                    if ($data['current_password'] == $data['new_password']) {
                        $response['status'] = false;
                        $response['message'] = Config::get('message-constants.Password_not_same');
                        $response['data'] = (object) [];
                        return response()->json($response);
                    } else if (Hash::check($data['current_password'], $user->password)) {
                        $user->password = Hash::make($data['new_password']);

                        $user->save();

                        $response['status'] = true;
                        $response['message'] = Config::get('message-constants.Password_changed');
                        $response['data'] = $user;
                        return response()->json($response);
                    } else {
                        $response['status'] = false;
                        $response['message'] = Config::get('message-constants.Current_password_wrong');
                        $response['data'] = (object) [];
                        return response()->json($response);
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }


    public function getProfile()
    {
        try {

            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $user = User::where('id',$userId)->with('userinterest')->first();
            $user->notification_count = unreadNotificationCount($userId);
            $user->rides_count = Vehicle::where('user_id', $userId)->count();


            if (!$user) {
                return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
            } else {
                return response()->json(['status' => true, 'message' => Config::get('message-constants.Success'), 'data' => $user]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }


    public function getOtherProfile(Request $request)
    {
        try {

            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                $user = User::active()->find($request->user_id);

                if (!$user) {


                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                } else {

                    return response()->json(['status' => true, 'message' => Config::get('message-constants.Success'), 'data' => $user]);
                }
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    //enable/disable 2FA
    public function updateTwoFactorStatus(Request $request)
    {
        try {
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                'status_key' => 'required|in:0,1',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {
                $user_data = User::find($userId);
                $user_data->update(["is_two_factor" => (integer) $request->status_key]);

                return response()->json(['status' => true, 'message' => Config::get('message-constants.Success'), 'data' => $user_data]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }


    public function delete_account(Request $request)
    {
        try {
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                // 'email' => 'required|email|string',
                'password' => 'required',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                $user = User::where(['id' => $userId])->first();
                if (!$user) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                } else {
                    if (Hash::check($request->password, $user->password)) {

                        $isDeleted = delete_user_account($userId);

                        if ($isDeleted) {
                            return response()->json(['status' => true, 'message' => Config::get('message-constants.Ac_delete_success'), 'data' => (object) []]);
                        } else {
                            return response()->json(['status' => false, 'message' => Config::get('message-constants.AC_delete_error'), 'data' => (object) []]);
                        }

                    } else {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.Wrong_credentials'), 'data' => (object) []]);
                    }
                }
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

        //sync contacts and return registered users list
    /*public function syncContacts(Request $request)
    {
        try {
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;
            $limit = 10;
            $validator = Validator::make($request->all(), [
                'contacts' => 'required',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {
                $contacts = json_decode($request->contacts);
                $mobile_numbers = [];
                foreach ($contacts as $contact) {
                    array_push($mobile_numbers, $contact->mobile);
                }

                $user = User::where(function($query) use($mobile_numbers){
                        $query->whereIn('mobile_number', $mobile_numbers)
                        ->orWhereIn('mobile', $mobile_numbers);
                    })
                    ->where('id', '<>', $userId)
                    ->active()
                    ->paginate();

                return response()->json(['status' => true, 'message' => 'Successfully', 'data' => $user]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }*/


    public function syncContacts(Request $request)
    {
        try {
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                'contacts' => 'required',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {
                $contacts = json_decode($request->contacts);

                $register = [];
                $not_register = [];


                foreach ($contacts as $key => $value) {

                    $checkUser = User::whereNotIn('id', DB::table('friends')
                        ->selectRaw("(CASE WHEN friend_id = " . $userId . " THEN user_id ELSE friend_id END ) as ids")
                        ->whereRaw('user_id = ' . $userId . ' OR friend_id = ' . $userId)
                        ->pluck('ids'))

                        ->whereNotIn('users.id', function ($query) use ($userId) {
                            $query->select('user_id')->from('report_users')->where('reporter_id', $userId)->get();
                        })
                        ->whereNotIn('users.id', function ($query) use ($userId) {
                            $query->select('blocked_to')->from('block_users')->where('blocked_by', $userId)->get();
                        })
                        ->whereNotIn('users.id', function ($query) use ($userId) {
                            $query->select('blocked_by')->from('block_users')->where('blocked_to', $userId)->get();
                        })

                        ->where(function ($q) use ($value) {
                            $q->where('mobile', $value->number)
                                ->orwhere('mobile_number', $value->number);
                        })
                        ->where('status', 1)
                        ->where('id', '<>', $userId)->first();


                    if ($checkUser) {
                        //only push number to register array if duplicate found.
                        if (array_search($value->number, array_column($register, 'mobile_number')) == "" && array_search($value->number, array_column($register, 'mobile')) == "") {
                            array_push($register, $checkUser);
                        }
                        ;
                    } else {
                        $non = array(
                            'name' => $value->name,
                            'mobile_number' => $value->number,
                        );
                        array_push($not_register, $non);
                    }
                }

                $data['registered_users'] = $register;

                $data['non_registered_users'] = $not_register;

                return response()->json(['status' => true, 'message' => Config::get('message-constants.Success'), 'data' => $data]);
            }


        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }


    public function clearNotification(Request $request)
    {
        try {

            $user = JWTAuth::toUser(JWTAuth::getToken());
            $userId = $user->id;

            $validator = Validator::make($request->all(), [
                'notification_id' => 'nullable|numeric',
            ]);

            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                $whereArr = [
                    'user_id' => $userId,
                ];

                if ($request->has('notification_id') && $request->notification_id != "") {
                    $whereArr['id'] = $request->notification_id;

                    $notification = Notifications::where($whereArr)->first();

                    if (!$notification) {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.No_Notification'), 'data' => (object) []]);
                    }
                }

                Notifications::where($whereArr)->where('type', '<>', 'BROADCAST_MESSAGE_ALL')->delete();

                //To clear broadcast notification
                $query = Notifications::where('type', 'BROADCAST_MESSAGE_ALL')
                    ->where('created_at', '>=', $user->created_at);


                if ($request->has('notification_id') && $request->notification_id != "") {
                    $query->where('id', $request->notification_id);
                }

                $broadcastNotification = $query->whereNotIn('notifications.id', function ($query) use ($user) {
                    $query->select('notification_id')->from('cleared_broadcast_notifications')->where('user_id', $user->id)->get();
                })
                    ->get()
                    ->pluck('id');

                foreach ($broadcastNotification as $broadcast_notification) {
                    ClearedBroadcastNotification::create([
                        'user_id' => $userId,
                        'notification_id' => $broadcast_notification,
                    ]);
                }

                return response()->json(['status' => true, 'message' => Config::get('message-constants.Notification_clear'), 'data' => (object) []]);

            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }


    public function getNotificationCount()
    {
        try {
           
            $user = JWTAuth::toUser(JWTAuth::getToken());
            $userId = $user->id;
    
            $notificationCount = Notifications::where('user_id', $userId)
                ->where('is_seen', 0)
                ->count();
    
            return response()->json([
                'status' => true,
                'message' => 'Notification count fetched successfully.',
                'data' => [
                    'has_notifications' => $notificationCount > 0 ? 1 : 0,
                    'unread_count' => $notificationCount
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage(),
                'data' => (object)[]
            ], 500);
        }
    }

    public function notificationList()
{
    try {
        $limit = 10;
        $userLogged = JWTAuth::toUser(JWTAuth::getToken());

        $result = Notifications::where(function ($query) use ($userLogged) {
                $query->where('user_id', $userLogged->id)
                      ->orWhere('type', 'BROADCAST_MESSAGE_ALL');
            })
            ->whereNotIn('notifications.id', function ($query) use ($userLogged) {
                $query->select('notification_id')
                      ->from('cleared_broadcast_notifications')
                      ->where('user_id', $userLogged->id);
            })
            ->where('created_at', '>=', $userLogged->created_at)
            ->with('user')
            ->orderBy('id', 'DESC')
            ->paginate($limit);

        // Mark all fetched notifications as seen
        foreach ($result->items() as $notification) {
            $notification->is_seen = 1;
            $notification->save();
        }

        return response()->json([
            'status' => true,
            'message' => Config::get('message-constants.Success'),
            'data' => $result
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'data' => []
        ]);
    }
}


    public function updateNotificationStatus(Request $request)
    {
        try {
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                'status_key' => 'required|in:0,1',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                $user_data = User::find($userId);
                $user_data->update(["notification_status" => (integer) $request->status_key]);
                $user_data->rides_count = Vehicle::where('user_id', $userId)->count();

                return response()->json(['status' => true, 'message' => Config::get('message-constants.Notification_status'), 'data' => $user_data]);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }



    public function blockUnblockUser(Request $request)
    {
        try {
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                'other_user_id' => 'required|numeric',
                'status_key' => 'required|in:0,1',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                $otherUser = User::active()->find($request->other_user_id);

                if (!$otherUser) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                }

                if ($request->status_key) {

                    $group_code = generateGroupCode($userId, $request->other_user_id);

                    BlockUser::updateOrCreate([
                        'blocked_by' => $userId,
                        'blocked_to' => $request->other_user_id
                    ],[
                        'group_code' => $group_code
                    ]);

                    return response()->json(['status' => true, 'message' => Config::get('message-constants.Blocked_success'), 'data' => (object) []]);
                } else {

                    BlockUser::where([
                        'blocked_by' => $userId,
                        'blocked_to' => $request->other_user_id
                    ])->delete();

                    return response()->json(['status' => true, 'message' => Config::get('message-constants.Unblocked_success'), 'data' => (object) []]);
                }

            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    public function blockedUsersList(Request $request)
    {
        try {
            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;
            $limit = 10;

            $blockedUsers = BlockUser::where('blocked_by', $userId)->with('blockedToUser')->orderBy('id', 'DESC')->paginate($limit);

            return response()->json(['status' => true, 'message' => Config::get('message-constants.Success'), 'data' => $blockedUsers]);


        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

    public function reportUser(Request $request)
    {
        try {

            $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|numeric',
                'reason' => 'required',
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {
                $loggedInUser = User::active()->find($request->user_id);

                $otherUser = User::active()->find($request->user_id);

                if (!$otherUser) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.User_not_exist'), 'data' => (object) []]);
                }

                if ($request->user_id == $userId) {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.Cannot_report_own'), 'data' => (object) []]);
                }

                $group_code = generateGroupCode($userId, $request->user_id);

                ReportUser::updateOrCreate(
                    ['reporter_id' => $userId, 'user_id' => $request->user_id],
                    [
                        'description' => $request->reason,
                        'group_code' => $group_code
                    ]
                );
                return response()->json(['status' => true, 'message' => Config::get('message-constants.Report_success'), 'data' => (object) []]);

            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object) []]);
        }
    }

}
