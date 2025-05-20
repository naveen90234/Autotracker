<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Car;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\CarImport;
use DataTables;
use Illuminate\Support\Facades\Validator;

class CarController extends Controller {
    // Show all cars
    public function index(Request $request)
    {
        $title = "Car Library";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
    
        if ($request->ajax()) {
            $query = Car::latest();
    
            if ($request->has('status') && $request->status !== null) {
                $query->where('status', (int)$request->status);
            }

            $cars = $query->get();
    
            return Datatables::of($cars)
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
                ->editColumn('year', function ($row) {
                    return $row->year;
                })
                ->editColumn('make', function ($row) {
                    return $row->make;
                })
                ->editColumn('model', function ($row) {
                    return $row->model;
                })
                ->editColumn('action', function ($row) {
                    return '<div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton' . $row->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $row->id . '">
                                    <a class="dropdown-item" href="' . route('admin.cars.edit', $row->id) . '">Edit</a>
                                    <a class="dropdown-item delete_car" href="javascript:void(0)" data-id="' . $row->id . '">Delete</a>
                                </div>
                            </div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
    
        return view('admin.cars.index', compact('title', 'breadcrumbs'));
    }
    

    // Show form to add car manually
    public function create()
    {
        return view('admin.cars.create');
    }
    
    public function store(Request $request)
    {
        if ($request->ajax() && $request->isMethod('post')) {
            try {
                // Validate input
                $validator = Validator::make($request->all(), [
                    'year' => 'required|integer|min:1900|max:' . date('Y'),
                    'make' => 'required|string|max:255',
                    'model' => 'required|string|max:255',
                ], [
                    'year.required' => 'The year field is required.',
                    'year.integer' => 'The year must be a valid number.',
                    'year.min' => 'The year must be greater than 1900.',
                    'year.max' => 'The year cannot be in the future.',
                    'make.required' => 'The make field is required.',
                    'model.required' => 'The model field is required.',
                ]);
    
                if ($validator->fails()) {
                    return response()->json(['errors' => $validator->messages()], 422);
                }
    
                // Save the car data
                $car = new Car();
                $car->year = $request->year;
                $car->make = $request->make;
                $car->model = $request->model;
                $car->status = 1; // Assuming cars have a status field
                $car->save();
    
                return response()->json(['status' => 'true', 'message' => 'Car added successfully.']);
    
            } catch (\Exception $e) {
                return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
            }
        }
    
        return redirect()->route('admin.cars.index')->with('error', 'Invalid request.');
    }

    public function edit($id)
    {
        $title = "Edit Car Model";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
    
        $car = car::findOrFail($id);
        return view('admin.cars.edit', compact('title', 'breadcrumbs', 'car'));
    }


    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'year' => 'required|integer|min:1900|max:' . date('Y'),
                    'make' => 'required|string|max:255',
                    'model' => 'required|string|max:255',
                ], [
                    'year.required' => 'The year field is required.',
                    'year.integer' => 'The year must be a valid number.',
                    'year.min' => 'The year must be greater than 1900.',
                    'year.max' => 'The year cannot be in the future.',
                    'make.required' => 'The make field is required.',
                    'model.required' => 'The model field is required.',
                ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()], 422);
            }
    
            $car = Car::findOrFail($id);
    
            $car->update([
                'year'       => $request->year,
                'make' => $request->make,
                'model'       => $request->model,
            ]);
    
            return response()->json(['status' => 'true', 'message' => "Car Model updated successfully."]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'false', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a car
        public function deleteCar($id) {
            $car = Car::find($id);

            if ($car) {
                $car->delete();
                return redirect()->route('admin.cars.index')->with('success_msg', $car->model . 'has been deleted.');
            } else {
                return redirect()->route('admin.cars.index')->with('error_msg', 'Car Model does not exist.');
            }
        }

    // Show CSV Upload Form
    public function showUploadForm() {
        $title = "Car Library";
        $breadcrumbs = [
            ['name' => $title, 'relation' => 'Current', 'url' => '']
        ];
        return view('admin.cars.upload', compact('title', 'breadcrumbs'));
    }

    // Handle CSV Upload
    public function uploadCSV(Request $request) {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);
    
        try {
            Excel::import(new CarImport, $request->file('csv_file'));
            return response()->json(['message' => 'Cars imported successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to import cars. ' . $e->getMessage()], 500);
        }
    }

    // Toggle car status
    public function status(Request $request)
    {
        $id = $request->id;
        $row = Car::whereId($id)->first();
        $row->status = $row->status == 1 ? 0 : 1;
        $row->save();

        if($row->status){
            $driving_data['link'] = '<h2>Hello, ' . $row->model . '</h2>';
            $driving_data['link'] .= '<h3>Car Model has been activated.</h3>';
        }else{
            $driving_data['link'] = '<h2>Hello, ' . $row->name . '</h2>';
            $driving_data['link'] .= '<h3>Car Model has been deactivated.</h3>';

        }

        return response()->json(['success' => 'Car Model status changed successfully.', 'val' => $row->status]);
    }
}
