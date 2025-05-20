<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrivingStyle;
use Illuminate\Http\Request;
use DB;
use DataTables;
use Illuminate\Support\Facades\Validator;

class DrivingStyleController extends Controller
{
    public function addDrivingStyle(Request $request)
    {
        $title = "Driving Style";
        $breadcrumbs = [
            ['name' => 'Driving Style', 'relation' => 'Current', 'url' => '']
        ];

        if ($request->ajax() && $request->isMethod('post')) {
            try {
                $validator = Validator::make($request->all(), [
                    'name'              => 'required|max:45|unique:driving_styles,name'
                ], [
                    'name.unique' => 'The Driving Style name has already been taken.',
                    'name.required' => 'The Driving Style name field is required.',
                    'name.max' => 'The Driving Style name must not be greater than 45 characters.'
                ]);
                if ($validator->fails()) {
                    foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                        if (!isset($firstError))
                            $firstError = $messages[0];
                        $error[$field_name] = $messages[0];
                    }

                    return response()->json(array('errors' => $validator->messages()), 422);
                } else {
                    $driving = new DrivingStyle();
                    $driving->name = $request->get('name');
                    $driving->save();


                    return ['status' => 'true', 'message' => __("Driving Style added successfully.")];
                }
            } catch (\Exception $e) {
                return ['status' => 'false', 'message' => $e->getMessage()];
            }
        }
        return view('admin.driving_styles.adddrivingstyle', compact('title', 'breadcrumbs'));
    }


    public function drivingstylelist(Request $request)
{
    $title = "Driving Style List";
    $breadcrumbs = [
        ['name' => $title, 'relation' => 'Current', 'url' => '']
    ];

    if ($request->ajax()) {
        // Apply filtering BEFORE get()
        $query = DrivingStyle::orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== null) {
            $query->where('status', (int)$request->status);
        }

        $drivinglist = $query->get();

        return Datatables::of($drivinglist)
            ->addIndexColumn()
            ->editColumn('status', function ($row) {
                $status = ($row->status == 1) ? 'checked' : '';

                return '<label class="switch">
                            <input type="checkbox" ' . $status . ' class="togbtn" data-id="' . $row->id . '">
                            <div class="slider round">
                                <span class="on">Active</span>  
                                <span class="off">Inactive</span>
                            </div>
                        </label>';
            })
            ->editColumn('action', function ($row) {
                return '<div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row->id . '">
                                <a class="dropdown-item" href="' . url('admin/driving_styles/edit/' . $row->id) . '">Edit</a>
                                <a class="dropdown-item delete_selling" href="javascript:void(0)" data-id="' . $row->id . '">Delete</a>
                            </div>
                        </div>';
            })
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('m/d/Y');
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    return view('admin/driving_styles/drivinglisting', compact('title', 'breadcrumbs'));
}


    public function editDrivingStyle($id)
    {

        $title = "Driving Style List";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];

        $driving = DrivingStyle::where('id', $id)->first();
        return view('admin/driving_styles/edit-driving', compact('title', 'breadcrumbs', 'driving'));
    }

    public function updateDrivingStyle(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:45|unique:driving_styles,name,' . $id . ',id'
            ], [
                'name.unique' => 'The driving style name has already been taken.',
                'name.required' => 'The driving style name field is required.',
                'name.max' => 'The driving style name must not be greater than 45 characters.'
            ]);
            if ($validator->fails()) {
                foreach ($validator->messages()->getMessages() as $field_name => $messages) {
                    if (!isset($firstError))
                        $firstError = $messages[0];
                    $error[$field_name] = $messages[0];
                }

                return response()->json(array('errors' => $validator->messages()), 422);
            } else {
                $driving = DrivingStyle::find($id);
                $driving->name = $request->get('name');
                $driving->save();


                return ['status' => 'true', 'message' => __("Driving Style updated successfully.")];
            }
        } catch (\Exception $e) {
            return ['status' => 'false', 'message' => $e->getMessage()];
        }
    }

    public function deleteStyle($id) {
        $driving = DrivingStyle::find($id);
    
        if ($driving) {
            $driving->delete();
            return redirect()->route('admin.driving_styles')->with('success_msg', $driving->name . ' Style has been deleted.');
        } else {
            return redirect()->route('admin.driving_styles')->with('error_msg', 'Driving Style does not exist.');
        }
    }
    
    

    public function status(Request $request)
    {
        $id = $request->id;
        $row = DrivingStyle::whereId($id)->first();
        $row->status = $row->status == 1 ? 0 : 1;
        $row->save();

        if($row->status){
            $driving_data['link'] = '<h2>Hello, ' . $row->name . '</h2>';
            $driving_data['link'] .= '<h3>Driving Style has been activated.</h3>';
        }else{
            $driving_data['link'] = '<h2>Hello, ' . $row->name . '</h2>';
            $driving_data['link'] .= '<h3>Driving Style has been deactivated, please contact to administrator.</h3>';

        }

        return response()->json(['success' => 'Driving Style status changed successfully.', 'val' => $row->status]);
    }
}
