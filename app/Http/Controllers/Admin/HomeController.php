<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Category;
use App\Models\DeleteRequest;
use App\Models\User;
use App\Models\StockImage;
use App\Models\Notifications;
use App\Lib\Uploader;
use Auth;
use Hash;
use Validator;
use DataTables;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Lib\Email;


class HomeController extends Controller
{

    //To Load admin panel dashboard page
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::active()->count();
        $title = "Dashboard";
        $breadcrumbs = [
            ['name' => 'Dashboard', 'relation' => 'Current', 'url' => '', 'icon' => 'fa fa-dashboard']
        ];

        return view('admin.home.dashboard', compact('title', 'breadcrumbs', 'totalUsers', 'activeUsers'));
    }

    public function profile(Request $request)
    {
        $title = "Profile";
        $breadcrumbs = [
            ['name' => 'Profile', 'relation' => 'Current', 'url' => '']
        ];
        $date = date('Y-m-d h:i:s', time());
        $data = Admin::find(Auth::guard('admin')->user()->id);
        if ($request->ajax() && $request->isMethod('post')) {
            try {
                $validator = Validator::make($request->all(), [
                    'name'              => 'required|max:45',
                    'profile_picture'     => 'nullable|mimes:jpeg,png,jpg,gif,svg'
                ]);
                if ($validator->fails()) {
                    foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                        if (!isset($firstError))
                            $firstError = $messages[0];
                        $error[$field_name] = $messages[0];
                    }

                    return response()->json(array('errors' => $validator->messages()), 422);
                } else {
                    $formData = ['name' => $request->get('name')];
                    if ($request->hasFile('profile_picture')) {
                        if ($request->file('profile_picture')->isValid()) {
                            $path = "/uploads/admin/";

                            if (!file_exists(public_path($path))) {
                                mkdir(public_path($path), 0777, true);
                            }

                            //unlink already exist image
                            if (file_exists($data->profile_picture)) {
                                unlink($data->profile_picture);
                            }

                            $responseData =  Uploader::doUpload($request->file('profile_picture'), $path, false);
                            $formData['profile_picture'] = $responseData['file'];
                        }
                    }
                    $data->update($formData);
                    return ['status' => 'true', 'message' => __("Profile updated successfully.")];
                }
            } catch (\Exception $e) {
                return ['status' => 'false', 'message' => $e->getMessage()];
            }
        }
        return view('admin.home.profile', compact('title', 'breadcrumbs', 'data'));
    }


    public function  changePassword(Request $request)
    {
        $title = "Change Password";
        $breadcrumbs = [
            ['name' => 'Change Password', 'relation' => 'Current', 'url' => '']
        ];
        if ($request->ajax() && $request->isMethod('post')) {

            try {
                $validator = Validator::make($request->all(), [
                    'current_password'      => 'required|max:45',
                    'new_password'          => 'required|max:45|min:8|same:confirm_password',
                    'confirm_password'      => 'required|max:45|min:8'
                ]);
                if ($validator->fails()) {
                    foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                        if (!isset($firstError))
                            $firstError = $messages[0];
                        $error[$field_name] = $messages[0];
                    }

                    return response()->json(array('errors' => $validator->messages()), 422);
                } else {
                    $data = Admin::find(Auth::guard('admin')->user()->id);
                    if (Hash::check($request->get('current_password'), $data->password)) {
                        $data->update(['password' => Hash::make($request->get('new_password'))]);

                        return ['status' => 'true', 'message' => __("Password updated successfully.")];
                    } else {

                        return ['status' => 'false', 'message' => __("Current password does't match.")];
                    }
                }
            } catch (\Exception $e) {
                return ['status' => 'false', 'message' => $e->getMessage()];
            }
        }
        return view('admin.home.change_password', compact('title', 'breadcrumbs'));
    }

    public function broadcasting_user(Request $request)
    {
        if ($request->ajax() && $request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'send_to' => 'required',
                'user_ids' => 'required_if:send_to,==,selected_users',
                'title' => 'required',
                'body' => 'required',
                'notification_send_time' => 'required|date',
                'notification_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ], [
                'user_ids.required_if' => 'Please select users.',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['status' => false, 'error' => $validator->messages()], 200);
            }
    
            $notification_image = null;
            if ($request->hasFile('notification_image')) {
                $notification_image = $request->file('notification_image')->store('notifications', 'public');
            }
    
            $notification_arr = [
                'sender_id' => 0,
                'title' => $request->title,
                'type' => ($request->send_to == 'all_users') ? 'BROADCAST_MESSAGE_ALL' : 'BROADCAST_MESSAGE_SPECIFIC_USER',
                'message' => $request->body,
                'notification_send_time' => $request->notification_send_time,
                'notification_image' => $notification_image,
                'is_seen' => 1,
            ];
    
            if ($request->send_to == 'all_users') {
                $notification_arr['user_id'] = 0;
                Notifications::create($notification_arr);
                $users_id = User::active()->latest()->pluck('id')->toArray();
                $notification_arr['user_id'] = $users_id;
            } else {
                foreach ($request->user_ids as $usersId) {
                    $notification_arr['user_id'] = $usersId;
                    Notifications::create($notification_arr);
                }
            }
    
            sendNotification($notification_arr);
            return response()->json(['status' => true, 'success' => 'Broadcast Message scheduled successfully.']);
        }
    
        $users = User::active()->latest()->get();
        return view('admin/broadcasting/broadcastmsg', compact('users'));
    }
    



    public function accountDeletionRequests(Request $request)
    {

        $title = "Account Deletion Requests";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];

        if ($request->ajax()) {
            $data = DeleteRequest::latest()->get();
            $table = DataTables::of($data)

                ->addIndexColumn()
                ->editColumn('action', function ($row) {

                    $btn = '<div class="action-width"><button class="btn btn-danger btn-sm ml-2 delete_account" data-id="' . $row->id . '"><i class="icon-trash" aria-hidden="true"></i></button></div>';

                    return $btn;
                })
                ->editColumn('description', function ($row) {
                    return '<td><input type="hidden" class="description" value="' . $row->reason . '" /><button type="button" class="btn btn-primary viewDescriptionBtn" data-toggle="modal" data-target="#viewDescriptionBtn">View Reason</button></td>';
                })
                ->editColumn('name', function ($row) {
                    return  "$row->name";
                })
                ->editColumn('created_at', function ($row) {
                    return '<td>' . $row->created_at->format('m/d/Y') . '</td>';
                })

                ->rawColumns(['action', 'description', 'created_at'])
                ->make(true);

            return $table;
        }
        return view('admin/home/account-delete-request', compact('title', 'breadcrumbs'));
    }


    public function deleteUserAccount($id){
        $deleteRequest = DeleteRequest::find($id);
        $user = User::where(['email' => $deleteRequest->email])->first();
        if ($user) {
            
            $isDeleted = delete_user_account($user->id);

            if($isDeleted){

                //delete request from DB.
                $deleteRequest->delete();

                // return redirect('/admin/account-deletion-requests')->with('success_msg',  $user->user_name . ' account has been deleted!');
                return redirect()->back()->with('success_msg',  $user->user_name . ' account has been deleted.');
            }else{
                return redirect()->back()->with('error_msg', 'Something went wrong while deleting account, Please try again.');
            }

        } else {
            return redirect()->back()->with('error_msg', 'User account does not exist.');
        }
    }

    public function stockimageslist()
{
    $images = StockImage::all();
    return view('admin.stock_images.index', compact('images'));
}

    // Show the form for adding stock images
    public function stockimagesadd()
    {
        return view('admin.stock_images.add');
    }

    // Store stock image
    public function stockimagesstore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'status' => 'required|in:Active,Inactive',
        ]);
    
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Store the image in the 'public/stock_images' directory
            $imagePath = $request->file('image')->store('stock_images', 'public');
    
            // Remove "stock_images/" from the path to prevent duplication
            $imageName = str_replace('stock_images/', '', $imagePath);
        }
    
        // Save only the relevant attributes in the database
        StockImage::create([
            'title' => $request->title,
            'image' => $imageName, // Only store filename instead of full path
            'status' => $request->status
        ]);
    
        return redirect()->route('admin.stock_images')->with('success', 'Stock image uploaded successfully!');
    }
    
    

public function stockimagesdelete($id)
{
    $image = StockImage::findOrFail($id);

    if ($image->image) {
        Storage::disk('public')->delete($image->image);
    }

    $image->delete();

    return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
}

public function stockimagestatus(Request $request, $id)
{
    $image = StockImage::findOrFail($id);
    $image->status = $image->status === 'Active' ? 'Inactive' : 'Active';
    $image->save();

    return response()->json(['success' => true, 'status' => $image->status]);
}


}
