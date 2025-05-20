<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Admin;
use DataTables;
use App\Models\Support;
use App\Models\User;
use App\Models\DeleteRequest;
use Illuminate\Support\Facades\Validator;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Lib\Email;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    public function __construct()
    {
        $this->title = 'Support List';
    }

    public function index(Request $request)
    {
        $title = "Support Information List";

        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
        if ($request->ajax()) {
            $data = Support::orderBy('id', 'DESC')->get();
            $table = DataTables::of($data)

            ->addIndexColumn()
            ->addColumn('reply_status', function($row){  
                $status = ($row->response != NULL && $row->response != "")? 'Yes' : 'No';
                return $status;
            })
            ->addColumn('action', function($row){  
                $response = ($row->response != null) ? $row->response : "notFound";

                $btn = '<td><button data-email='.$row->email.' data-id='.$row->id.' data-description="'.$response.'" class="btn btn-primary btn-sm sendMail" id="sendMail" title="Send response"><i class="fa fa-envelope" aria-hidden="true"></i></button>&nbsp;<button data-id='.$row->id.' class="btn btn-danger btn-sm delete-confirm" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button></td>';
                return $btn;
            })

            ->rawColumns(['action', 'reply_status'])
            ->make(true);

            return $table;
        }
        return view('admin/support/index',compact('title', 'breadcrumbs'));
    }

    public function add_response(Request $request)
    {
        try 
        {
            $data = $request->all();
            $validator = Validator::make($data, [
                'email' => 'required',
                'message' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => false , 'error' => $validator->messages()], 200);
            } else {

                $name = User::where('email',$request->email)->pluck('user_name')->first();
                $data = array("email" => $request->email, "name" => $name, "msg" => $request->message);
                $email = $request->email;

                $setting = Setting::pluck('value', 'field_name');
                $site_email = env('ADMIN_EMAIL')??$setting['site_email'];
                $support = Support::where('id',$request->support_id)->first();


                $email_data['user_name'] = $name;
                $email_data['message'] = "Thank you for reaching out to us regarding $support->message. " . $request->message;
                Email::send('support-response', $email_data, $email, "Support Response");

                $support->update(['response' => $request->message]);

                if( count(Mail::failures()) > 0 ) {
                    return $this->error(__('message.failedMail'), 422);
                }

                return response()->json(['status' => True, 'message' => 'Message sent successfully.', 'data' => '']);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => '']);
        }
    }

    public function destroy_message($id)
    {
        $data = Support::find($id);
        $data->delete();
        return response()->json(['success'=>'Message deleted successfully']);
    }
}
