<?php
namespace App\Lib;

use Mail;
use App\Models\EmailTemplate;
use App\Models\Setting;

/**
 *
 *
 * This Library use for image upload and resizing.
 *
 *
 **/

class Email
{


    public static function send($template, $data, $email = null, $title = null)
    {
        $setting = Setting::pluck('value', 'field_name');
        if ($setting['smtp_bypass'] != '1') {
            if (!$email || $email == '') {
                $email = $data['email'];
            }
            $maildata = EmailTemplate::where('slug', '=', $template)->first();
            // if ($maildata) {
            //     $site_email = env('MAIL_FROM_ADDRESS') ?? $setting['site_email'];
            //     $site_title = $setting['site_title'];
            //     $message = str_replace(explode(",", $maildata->keywords), $data, $maildata->content);
            //     $subject = ($title == null) ? $maildata->title : $title;

            //     Mail::send('email.email', array('data' => $message), function ($message) use ($site_email, $email, $subject, $site_title) {
            //         $message->from($site_email, $site_title);
            //         $message->to($email, $email)->subject($subject);
            //     });
            // }
            if ($maildata) {
                $site_email = env('MAIL_FROM_ADDRESS') ?? $setting['site_email'];
                $site_title = $setting['site_title'];
                // $message         = str_replace(explode(",",$maildata->keywords), $data,$maildata->content);
                $message = $maildata->content;
                if (array_key_exists('otp', $data)) {
                    $message = str_replace('{{OTP}}', $data['otp'], $message);
                }
                $message = str_replace('{{APP_NAME}}', $site_title, $message);
                $message = str_replace('{{APP_EMAIL}}', $site_email, $message);
                if (array_key_exists('user_email', $data)) {
                    $message = str_replace('{{USER_EMAIL}}', $data['user_email'], $message);
                }
                if (array_key_exists('user_name', $data)) {
                    $message = str_replace('{{USER_NAME}}', $data['user_name'], $message);
                }
                if (array_key_exists('message', $data)) {
                    $message = str_replace('{{MESSAGE}}', $data['message'], $message);
                }
                if (array_key_exists('url', $data)) {
                    $message = str_replace('{{URL}}', $data['url'], $message);
                }

                $subject = ($title == null) ? $maildata->subject : $title;

                Mail::send('email.email', array('data' => $message), function ($message) use ($site_email, $email, $subject, $site_title) {
                    $message->from($site_email, $site_title);
                    $message->to($email, $email)->subject($subject);
                });
            }
        }
    }

}
