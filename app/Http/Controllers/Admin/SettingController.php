<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\VersionControlSetting;
use Validator;
use App\Lib\Uploader;
use DB;

class SettingController extends Controller
{

    //Manage app version settings
    public function editSetting(Request $request)
    {
        $title = "Edit Version Settings";
        $breadcrumbs = [
            ['name' => 'Edit Version Setting', 'relation' => 'Current', 'url' => '']
        ];

        if ($request->ajax() && $request->isMethod('post')) {
            // Validation rules
            $rules = [
                'ios_maintenance' => 'required',
                'ios_force_update' => 'required',
                'ios_message' => 'required',
                'ios_version' => 'required',
                'ios_app_link' => 'required',
                'android_maintenance' => 'required',
                'android_force_update' => 'required',
                'android_message' => 'required',
                'android_version' => 'required',
                'android_app_link' => 'required',
            ];

            $request->validate($rules);

            // Update settings
            $fields = [
                'ios' => [
                    'maintenance' => $request->ios_maintenance,
                    'force_update' => $request->ios_force_update,
                    'message' => $request->ios_message,
                    'version' => $request->ios_version,
                    'app_link' => $request->ios_app_link,
                ],
                'android' => [
                    'maintenance' => $request->android_maintenance,
                    'force_update' => $request->android_force_update,
                    'message' => $request->android_message,
                    'version' => $request->android_version,
                    'app_link' => $request->android_app_link,
                ],
            ];

            foreach ($fields as $platform => $settings) {
                foreach ($settings as $key => $value) {
                    VersionControlSetting::updateOrCreate(
                        ['field_name' => "{$platform}_{$key}"],
                        ['value' => $value]
                    );
                }
            }

            return response()->json(['status' => 'true', 'message' => 'App setting updated successfully.']);
        }

        $getSetting = VersionControlSetting::select('field_name', 'value')->get();

        $data = [];

        foreach ($getSetting as $setting) {
            $data[$setting['field_name']] = $setting['value'];
        }

        return view('admin/settings/app-version-setting', compact('title', 'breadcrumbs', 'data'));

    }

    //Manage admin panel settings
    // public function manageSetting(Request $request){
    //     $poll_distance = Setting::where('field_name', 'poll_distance')->first();
    //     $map_icon = Setting::where('field_name', 'map_icon')->first();

    //     $title = "Setting";

    //     $breadcrumbs = [
    //         ['name' => 'Setting', 'relation' => 'Current', 'url' => '']
    //     ];

    //     if ($request->ajax() && $request->isMethod('post')) {
    //         $rules = array(
    //             'poll_distance' => 'required|numeric',
    //             'map_icon' => 'nullable|image',
    //         );

    //         $validator = Validator::make($request->all(), $rules);
    //         if ($validator->fails()) {
    //             return response()->json(array('errors' => $validator->messages()), 422);
    //         } else {

    //             if ($request->hasfile('map_icon')) {

    //                 //unlink existing file
    //                 if (file_exists($map_icon['value'])) {
    //                     unlink($map_icon['value']);
    //                 }

    //                 $destinationPath = '/uploads/map_icon/';

    //                 if (!file_exists(public_path($destinationPath))) {
    //                     mkdir(public_path($destinationPath), 0777, true);
    //                 }

    //                 $responseData = Uploader::doUpload($request->file('map_icon'), $destinationPath);
    //                 if ($responseData['status'] == "true") {
    //                     $map_icon->update(['value' => $responseData['file']]);
    //                 }

    //             }

    //             $poll_distance->update(['value' => $request->poll_distance]);

    //             return ['status' => 'true', 'message' => 'Setting updated successfully.'];
    //         }
    //     }
    //     return view('admin/settings/setting', compact('title', 'breadcrumbs', 'poll_distance', 'map_icon'));

    // }
}
