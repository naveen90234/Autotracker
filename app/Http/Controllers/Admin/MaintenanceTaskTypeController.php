<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MaintenanceTaskType;
use App\Models\CarPart;
use DataTables;
use Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MaintenanceImport;
use Illuminate\Support\Facades\Log;


class MaintenanceTaskTypeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Maintenance Task Types";
        $breadcrumbs = [['name' => $title, 'relation' => 'Current', 'url' => '']];

        if ($request->ajax()) {
            $query = MaintenanceTaskType::with('carParts');

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('status', function ($row) {
                    $status = $row->status ? 'checked' : '';
                    return '<label class="switch">
                                <input type="checkbox" class="togbtn" data-id="' . $row->id . '" ' . $status . '>
                                <div class="slider round">
                                    <span class="on">Active</span>  
                                    <span class="off">Inactive</span>
                                </div>
                            </label>';
                })
                ->editColumn('car_parts', function ($row) {
                    return $row->carParts->pluck('name')->implode(', '); // Display associated car parts
                })
                ->editColumn('action', function ($row) {
                    return '<a href="' . route('admin.maintenance_task_types.edit', $row->id) . '" class="btn btn-primary">Edit</a>
                            <a href="javascript:void(0)" class="btn btn-danger delete_task_type" data-id="' . $row->id . '">Delete</a>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.maintenance_task_types.index', compact('title', 'breadcrumbs'));
    }

    public function create()
    {
        $title = "Add Maintenance Task Type";
        $breadcrumbs = [['name' => $title, 'relation' => 'Current', 'url' => '']];
        $carParts = CarPart::all();
        return view('admin.maintenance_task_types.create', compact('title', 'breadcrumbs', 'carParts'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'car_parts' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $taskType = MaintenanceTaskType::create(['title' => $request->title]);

        $taskType->carParts()->sync($request->car_parts);

        return response()->json(['status' => true, 'message' => "Maintenance Task Type added successfully."]);
    }

    public function edit($id)
    {
        $title = "Edit Maintenance Task Type";
        $breadcrumbs = [['name' => $title, 'relation' => 'Current', 'url' => '']];
        $taskType = MaintenanceTaskType::findOrFail($id);
        $carParts = CarPart::all();
        return view('admin.maintenance_task_types.edit', compact('title', 'breadcrumbs','taskType', 'carParts'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'car_parts' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $taskType = MaintenanceTaskType::findOrFail($id);
        $taskType->update(['title' => $request->title]);
        $taskType->carParts()->sync($request->car_parts);

        return response()->json(['status' => true, 'message' => "Maintenance Task Type updated successfully."]);
    }

    public function deleteMaintenanceTasktype($id)
    {
        $taskType = MaintenanceTaskType::find($id);

        if (!$taskType) {
            return response()->json(['status' => false, 'message' => 'Task does not exist.'], 404);
        }

        $taskType->delete();

        return response()->json(['status' => true, 'message' => $taskType->title . ' has been deleted successfully.']);
    }

    public function status(Request $request)
    {
        $id = $request->id;
        $row = MaintenanceTaskType::whereId($id)->first();
        $row->status = $row->status == 1 ? 0 : 1;
        $row->save();

        if($row->status){
            $driving_data['link'] = '<h2>Hello, ' . $row->title . '</h2>';
            $driving_data['link'] .= '<h3>Task Type has been activated.</h3>';
        }else{
            $driving_data['link'] = '<h2>Hello, ' . $row->title . '</h2>';
            $driving_data['link'] .= '<h3>Task Type has been deactivated.</h3>';

        }

        return response()->json(['success' => 'Task Type status changed successfully.', 'val' => $row->status]);
    }

    public function showUploadForm() {
        $title = "Maintenance Task Type";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
        return view('admin.maintenance_task_types.upload', compact('title', 'breadcrumbs'));
    }

    // Handle CSV Upload
    public function uploadCSV(Request $request) {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
    
        try {
            Excel::import(new MaintenanceImport, $request->file('csv_file'));
            return response()->json(['message' => 'Maintenance tasks imported successfully.'], 200);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            Log::error('CSV Import Validation Errors:', $failures);
            
            return response()->json(['message' => 'Validation failed.', 'errors' => $failures], 422);
        } catch (\Exception $e) {
            Log::error('CSV Import Error: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to import maintenance tasks. ' . $e->getMessage()], 500);
        }
    }
}
