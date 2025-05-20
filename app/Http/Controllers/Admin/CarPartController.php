<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\CarPart;
use App\Models\MaintenanceTaskType;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CarPartImport;

class CarPartController extends Controller
{
    public function index(Request $request)
    {
        $title = "Car Parts Library";
        $breadcrumbs = [['name' => $title, 'relation' => 'Current', 'url' => '']];
    
        if ($request->ajax()) {
            $query = CarPart::with(['cars', 'maintenanceTaskTypes']); // Include maintenance task types
        
            if ($request->has('status') && $request->status !== null) {
                $query->where('status', (int)$request->status);
            }
        
            return Datatables::of($query)
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
                ->editColumn('cars', function ($row) {
                    return $row->cars->pluck('model')->implode(', '); // Show selected car models
                })
                ->editColumn('maintenanceTaskTypes', function ($row) {
                    return $row->maintenanceTaskTypes->pluck('title')->implode(', '); // Show selected task types
                })
                ->editColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row->id . '">
                                    <a class="dropdown-item" href="' . route('admin.car_parts.edit', $row->id) . '">Edit</a>
                                    <a class="dropdown-item delete_carpart" href="javascript:void(0)" data-id="' . $row->id . '">Delete</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    
        return view('admin.car_parts.index', compact('title', 'breadcrumbs'));
    }
    
    

    public function create()
    {
        $title = "Add Car Part";
        $breadcrumbs = [['name' => $title, 'relation' => 'Current', 'url' => '']];
        
        $cars = Car::all();
        $taskTypes = MaintenanceTaskType::all(); // Fetch all maintenance task types
    
        return view('admin.car_parts.create', compact('title', 'breadcrumbs', 'cars', 'taskTypes'));
    }
    

    public function store(Request $request)
{
    if ($request->ajax() && $request->isMethod('post')) {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'cars' => 'required|array|min:1',
                'cars.*' => 'exists:cars,id',
                'task_types' => 'required|array|min:1',
                'task_types.*' => 'exists:maintenance_task_types,id',
            ], [
                'name.required' => 'The Part Name field is required.',
                'cars.required' => 'Please select at least one car.',
                'task_types.required' => 'Please select at least one maintenance task type.',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 422);
            }

            // Save the car part
            $carPart = new CarPart();
            $carPart->name = $request->name;
            $carPart->status = 1;
            $carPart->save();

            // Attach selected cars and task types
            $carPart->cars()->attach($request->cars);
            $carPart->maintenanceTaskTypes()->attach($request->task_types);

            return response()->json(['status' => 'true', 'message' => 'Car Part added successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
        }
    }

    return redirect()->route('admin.car_parts.index')->with('error', 'Invalid request.');
}


public function edit($id)
{
    $title = "Edit Car Part";
    $breadcrumbs = [['name' => $title, 'relation' => 'Current', 'url' => '']];

    $cars = Car::all();
    $taskTypes = MaintenanceTaskType::all();
    
    $carPart = CarPart::with(['cars', 'maintenanceTaskTypes'])->findOrFail($id);
    $selectedCars = $carPart->cars->pluck('id')->toArray();
    $selectedTaskTypes = $carPart->maintenanceTaskTypes->pluck('id')->toArray();

    return view('admin.car_parts.edit', compact('title', 'breadcrumbs', 'carPart', 'cars', 'selectedCars', 'taskTypes', 'selectedTaskTypes'));
}



public function update(Request $request, $id)
{
    try {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'cars' => 'required|array|min:1',
            'cars.*' => 'exists:cars,id',
            'task_types' => 'required|array|min:1',
            'task_types.*' => 'exists:maintenance_task_types,id',
        ], [
            'name.required' => 'The Car Part name is required.',
            'cars.required' => 'At least one car must be selected.',
            'task_types.required' => 'At least one maintenance task type must be selected.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()], 422);
        }

        $carPart = CarPart::findOrFail($id);
        $carPart->update(['name' => $request->name]);

        // Sync selected cars and task types
        $carPart->cars()->sync($request->cars);
        $carPart->maintenanceTaskTypes()->sync($request->task_types);

        return response()->json(['status' => 'true', 'message' => "Car Part updated successfully."]);
    } catch (\Exception $e) {
        return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
    }
}


public function deleteCarPart($id)
{
    $carPart = CarPart::find($id);

    if (!$carPart) {
        return response()->json(['status' => false, 'message' => 'Car Part does not exist.'], 404);
    }

    $carPart->delete();

    return response()->json(['status' => true, 'message' => $carPart->name . ' has been deleted successfully.']);
}


public function status(Request $request)
    {
        $id = $request->id;
        $row = CarPart::whereId($id)->first();
        $row->status = $row->status == 1 ? 0 : 1;
        $row->save();

        if($row->status){
            $driving_data['link'] = '<h2>Hello, ' . $row->name . '</h2>';
            $driving_data['link'] .= '<h3>Car Part has been activated.</h3>';
        }else{
            $driving_data['link'] = '<h2>Hello, ' . $row->name . '</h2>';
            $driving_data['link'] .= '<h3>Car Part has been deactivated.</h3>';

        }

        return response()->json(['success' => 'Car Part status changed successfully.', 'val' => $row->status]);
    }

     // Show CSV Upload Form
     public function showUploadForm() {
        $title = "Car Part Library";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
        return view('admin.car_parts.upload', compact('title', 'breadcrumbs'));
    }

    // Handle CSV Upload
    public function uploadCSV(Request $request) {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
    
        try {
            Excel::import(new CarPartImport, $request->file('csv_file'));
            return response()->json(['message' => 'Car Parts imported successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to import cars. ' . $e->getMessage()], 500);
        }
    }
}
