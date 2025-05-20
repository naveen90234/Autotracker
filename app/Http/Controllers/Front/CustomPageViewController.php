<?php
namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\DeleteRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;
use Mail;
use Validator;
use App\Models\Pages;
use App\Models\Admin;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Lib\Email;
use App\Models\Support;

class CustomPageViewController extends Controller
{

    public function showPages($slug)
    {
        $row = Pages::where(['slug' => $slug])->first();
        if ($row) {
            $slugTitle = ucwords(str_replace("-", " ", $slug));
            return view('front/pages/index', compact('row', 'slugTitle'));
        }
    }


    public function support()
    {
        $adminData = Admin::select('email')->find(1);
        return view('support', compact('adminData'));
    }


    public function submitSupportForm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $data = array("email" => $request->email, "name" => $request->name, "message" => $request->message);

        /** Inserting data Data **/
        Support::create($data);



        $email_data['user_name'] = $request->name;
        $email_data['user_email'] = $request->email;
        $email_data['message'] = $request->message;

        $setting = Setting::pluck('value', 'field_name');
        $site_email = env('ADMIN_EMAIL')??$setting['site_email'];

        Email::send('contact-us', $email_data, $site_email, "Contact Us");

        return redirect()->back()->with('message', 'Your Query has been submitted, Thank You!');
    }

    public function accountDeletion()
    {
        return view('delete-request');
    }



    public function submitAccountDeletionRequest(Request $request)
    {
        try {
            $data = $request->all();
            $validator = Validator::make($data, [
                'name' => 'required',
                'email' => 'required|string|email',
                'reason' => 'required',
            ]);
            if ($validator->fails()) {
                //return back()->with(['status' => false, 'error' => $validator->errors()->first(), 'data' =>(object)[]]);
                return back()->withErrors($validator)->withInput();

            } else {

                $user = User::where(['email' => $request->email])->first();
                if ($user) {

                    /** Inserting data Data **/
                    DeleteRequest::updateOrCreate([
                        'email' => $request->email,
                    ],
                    [
                        'name' => $request->name,
                        'reason' => $request->reason,
                    ]);


                   //Sending email to admin.
                   $email_data['user_name'] = $request->name;
                   $email_data['user_email'] = $request->email;
                   $email_data['message'] = $request->reason;

                    $setting = Setting::pluck('value', 'field_name');
                    $site_email = env('ADMIN_EMAIL')??$setting['site_email'];

                    Email::send('account-deletion', $email_data, $site_email, "Account Deletion Request");

                    return redirect()->back()->with('message', 'Your request has been submitted, Thank You!');
                } else {
                    return redirect()->back()->with(['status' => false, 'message' => "Account does not exist.", 'data' => (object) []], 200);
                }


            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => '']);
        }
    }


}
