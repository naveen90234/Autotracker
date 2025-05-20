<?php

use App\Models\Setting;
use App\Models\UserDevice;
use App\Models\User;
use App\Models\Notifications;
use App\Models\BlockUser;
use App\Models\ReportUser;
use App\Models\SubscriptionPayment;
use App\Models\DeleteRequest;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

function base_url($url = "")
{

    $url = (string)$url;
    return  str_replace("public", "", url($url));
}

function setting($key = null)
{
    if ($key) {
        $setting = Setting::where('field_name', $key)->first();
        if ($setting) {
            return $setting->value;
        } else {
            return '';
        }
    } else {
        return '';
    }
}

function chkAuthToken($token)
{

    $chkToken = Setting::whereRaw("field_name='auth_token' AND value='" . $token . "' ")->count();
    if ($chkToken > 0) {
        return $chkToken;
    } else {
        return false;
    }
}

function is_valid_auth_token()
{
    $headers = apache_request_headers();
    $return_value = false;
    if (array_key_exists('auth_token', $headers)) {

        $auth_token = $headers['auth_token'];

        $check = chkAuthToken($auth_token);

        if (!empty($check) && $check == 1) {

            $return_value = true;
        }
    }
    return $return_value;
}

function generateEmailOTP()
{

    $setting = Setting::where('field_name', 'smtp_bypass')->first();

    if ($setting->value == '1') {
        $otp = 123456;
    } else {
        $otp = mt_rand(100000, 999999);
    }
    return $otp;
}


function generateGroupCode($user1, $user2)
{
    if ($user1 > $user2) {
        $group_code = $user2 . '-' . $user1;
    } else {
        $group_code = $user1 . '-' . $user2;
    }

    return $group_code;
}

function delete_user_account($userId){

    $user = User::find($userId);

    //delete user data from all the related table
    UserDevice::where('user_id', $userId)->delete();
    Notifications::where('user_id', $userId)->orWhere('sender_id', $userId)->delete();
    BlockUser::where('blocked_by', $userId)->orWhere('blocked_to', $userId)->delete();
    DB::table('chats')->where('sender', $userId)->orWhere('receiver', $userId)->delete();
    ReportUser::where('user_id', $userId)->orWhere('reporter_id', $userId)->delete();

    SubscriptionPayment::where('user_id', $userId)->delete();
    DeleteRequest::where(['email' => $user->email])->delete();

    //unlink profile image
    if (file_exists($user->profile_picture)) {
        unlink($user->profile_picture);
    }

    if($user->delete()){
        return 1;
    }else{
        return 0;
    }

}

function unreadNotificationCount($user_id)
{

    $user = User::find($user_id);

    $notification_count_1 = Notifications::where(['user_id' => $user_id, 'is_seen' => 0])->where('type', '<>', 'BROADCAST_MESSAGE_ALL')->count();

    $notification_count_2 = Notifications::where('type', 'BROADCAST_MESSAGE_ALL')->where('created_at', '>=', $user->created_at)->whereNotIn('id', function ($query) use ($user_id) {
        $query->select('notification_id')->from('seen_broadcast_notifications')->where(['user_id' => $user_id]);
    })->count();

    $totalUnreadNotifications = $notification_count_1 + $notification_count_2;

    return $totalUnreadNotifications;
}



// Send notification using firebase.
function sendNotification($data)
{
    if ($data['user_id'] != "") {

        $query = UserDevice::whereHas('user', function ($q) {
            $q->where('notification_status', 1);
        });
        if (is_array($data['user_id'])) {
            $FcmUserToken = $query->whereIn('user_id', $data['user_id'])->whereNotNull('device_token')->where('device_token', '<>', " ")->pluck('device_token')->all();
        } else {
            $FcmUserToken = $query->where('user_id', $data['user_id'])->whereNotNull('device_token')->pluck('device_token')->all();
        }

        if (!empty($FcmUserToken)) {

            $extra = $data;

            $extra = [];
            foreach ($data as $key => $value) {
                if (is_string($value)) {
                    $extra[$key] = $value;
                } else {
                    $extra[$key] = json_encode($value);
                }
            }


            $firebase = (new Factory)
                ->withServiceAccount(public_path() . '/app_android.json');

            $messaging = $firebase->createMessaging();

            $message = CloudMessage::fromArray([
                'notification' => [
                    "title" => $data['title'],
                    "body" => $data['message'],
                ],
                'data' => $extra
            ]);

            $res = $messaging->sendMulticast($message, $FcmUserToken);

            // // Process the result
            // $successful = $res->successes()->count();
            // $failed = $res->failures()->count();

            // $failures = $res->failures()->map(function ($failure) {
            //     return [
            //         'token' => $failure->target(),
            //         'error' => $failure->error()->getMessage(),
            //     ];
            // });

            // $result = json_encode([
            //     'successful' => $successful,
            //     'failed' => $failed,
            //     'failures' => $failures,
            // ]);

            // \Log::info($result);
        }
    }
}

