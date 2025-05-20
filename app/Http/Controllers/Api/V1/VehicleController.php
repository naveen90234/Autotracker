<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Vehicle;
use App\Models\StockImage;
use App\Models\Car;
use Illuminate\Support\Facades\Auth;
use App\Lib\Uploader;
use App\Http\Controllers\Controller;
use Config;
use JWTAuth;

class VehicleController extends Controller
{
    public function addEditVehicle(Request $request)
    {
        try {
            $user = Auth::user();

            // Validate request
            $validator = Validator::make($request->all(), [
                'year' => 'required|string|exists:cars,year',
                'model' => 'required|string|exists:cars,model',
                'make' => 'required|string|exists:cars,make',
                'vehicle_images' => 'nullable|array',
                'vehicle_images.*' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image_ids' => 'nullable|array',
                'track_by' => 'required|in:0,1',
                'vehicle_nickname' => 'nullable|string|max:255',
                'identification_number' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            // Ensure year, model, and make belong to the same car entry
            $carExists = Car::where('year', $request->year)
                ->where('model', $request->model)
                ->where('make', $request->make)
                ->exists();

            if (!$carExists) {
                return response()->json([
                    'status' => false,
                    'message' => Config::get('message-constants.INVALID_CAR')
                ]);
            }

            $data = $request->only(['year', 'model', 'make', 'track_by', 'vehicle_nickname', 'identification_number']);
            $data['user_id'] = $user->id;
            $data['track_by'] = $request->track_by == 1 ? 'km' : 'miles';

            $vehicleImages = [];

            $baseUrl = env('APP_URL') . '/public/uploads/vehicles/'; // Define base URL for images

            // Handle new image uploads
            if ($request->hasFile('vehicle_images')) {
                foreach ($request->file('vehicle_images') as $image) {
                    $responseData = Uploader::doUpload($image, 'uploads/vehicles/');
                    if ($responseData['status'] === true) {
                        $vehicleImages[] = $baseUrl . basename($responseData['file']); // Ensure full URL
                    }
                }
            }

            // Handle images selected from stock_images table
            if ($request->has('image_ids')) {
                $stockImages = StockImage::whereIn('id', $request->image_ids)
                    ->pluck('image')
                    ->map(function ($image) use ($baseUrl) {
                        return $baseUrl . ltrim($image, '/'); // Ensure full URL
                    })
                    ->toArray();
                
                $vehicleImages = array_merge($vehicleImages, $stockImages);
            }



            // Ensure vehicle_images is stored as a JSON string
            $data['vehicle_images'] = $vehicleImages;

            // Check if editing or adding a vehicle
            $vehicle = Vehicle::updateOrCreate(
                ['user_id' => $user->id, 'identification_number' => $request->identification_number],
                $data
            );

            return response()->json([
                'status' => true,
                'message' => $request->has('identification_number') ? 'Vehicle updated successfully.' : 'Vehicle added successfully.',
                'data' => $vehicle
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Get stock images for user to choose from
    public function stockVehicleImages()
    {
        try {
            $baseUrl = env('APP_URL') . '/public/uploads/vehicles/'; // Define base URL for images
    
            // Fetch all images from stock_images table and generate full URL
            $images = StockImage::select('id', 'image')->get()->map(function ($image) use ($baseUrl) {
                return [
                    'id' => $image->id,
                    'image' => $baseUrl . ltrim($image->image, '/')
                ];
            });
    
            return response()->json([
                'status' => true,
                'message' => Config::get('message-constants.stock_vehicle_images'),
                'data' => $images
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function carsList(Request $request)
{
    try {
        $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

        $cars = Vehicle::where('user_id', $userId)->get();

        if ($cars->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No cars found.',
                'data' => [],
            ]);
        }

        $carData = [];
        foreach ($cars as $car) {
            $carData[] = [
                'id' => $car->id,
                'vehicle_nickname' => $car->vehicle_nickname,
                'make' => $car->make,
                'model' => $car->model,
                'year' => $car->year,
                'track_by' => $car->track_by,
                'identification_number' => $car->identification_number,
                'current_miles' => $car->current_miles,
                'vehicle_images' => $car->vehicle_images ?? [], // array casting se already JSON banega
            ];
        }

        return response()->json([
            'status' => true,
            'message' => 'Cars list fetched successfully.',
            'data' => $carData,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'data' => [],
        ]);
    }
}

public function carDetails(Request $request)
{
    try {
        $request->validate([
            'car_id' => 'required|exists:vehicles,id',
        ]);

        $user = JWTAuth::toUser(JWTAuth::getToken());

        // Fetch the vehicle belonging to the logged-in user
        $car = Vehicle::where('id', $request->car_id)
                      ->where('user_id', $user->id)
                      ->first();

        if (!$car) {
            return response()->json([
                'status' => false,
                'message' => 'Car not found or does not belong to user.',
                'data' => [],
            ]);
        }

        $carData = [
            'id' => $car->id,
            'vehicle_nickname' => $car->vehicle_nickname,
            'make' => $car->make,
            'model' => $car->model,
            'year' => $car->year,
            'track_by' => $car->track_by,
            'identification_number' => $car->identification_number,
            'current_miles' => $car->current_miles,
            'vehicle_images' => $car->vehicle_images ?? [],
        ];

        return response()->json([
            'status' => true,
            'message' => 'Car details fetched successfully.',
            'data' => $carData,
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'data' => [],
        ]);
    }
}

public function getCarCurrentMiles(Request $request)
{
    try {
        $request->validate([
            'car_id' => 'required|exists:vehicles,id',
        ]);

        $user = JWTAuth::toUser(JWTAuth::getToken());

        // Find car of this user
        $car = Vehicle::where('id', $request->car_id)
                      ->where('user_id', $user->id)
                      ->first();

        if (!$car) {
            return response()->json([
                'status' => false,
                'message' => 'Car not found or unauthorized access.',
                'data' => [],
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Current miles fetched successfully.',
            'data' => [
                'id' => $car->id,
                'vehicle_nickname' => $car->vehicle_nickname,
                'current_miles' => $car->current_miles,
            ],
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'data' => [],
        ]);
    }
}

public function addOrUpdateCurrentMiles(Request $request)
{
    try {
        $request->validate([
            'car_id' => 'required|exists:vehicles,id',
            'miles' => 'required|numeric|min:1',
        ]);

        $user = JWTAuth::toUser(JWTAuth::getToken());

        // Get vehicle
        $vehicle = Vehicle::where('id', $request->car_id)
                          ->where('user_id', $user->id)
                          ->first();

        if (!$vehicle) {
            return response()->json([
                'status' => false,
                'message' => 'Car not found or unauthorized access.',
            ]);
        }

        // Check if new miles >= current miles
        if ($request->miles <= $vehicle->current_miles) {
            return response()->json([
                'status' => false,
                'message' => 'Miles cannot be less than existing current miles.',
            ]);
        }

        // Update miles
        $vehicle->current_miles = $request->miles;
        $vehicle->save();

        return response()->json([
            'status' => true,
            'message' => 'Current miles updated successfully.',
            'data' => [
                'car_id' => $vehicle->id,
                'current_miles' => $vehicle->current_miles,
            ],
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
        ]);
    }
}
    
}
