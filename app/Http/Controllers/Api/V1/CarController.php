<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Config;
use App\Models\CarPart;

class CarController extends Controller
{
    // Get unique years from cars table
        public function getCarYears(){
            try {
                $years = Car::select('id', 'year')->distinct()->orderBy('year', 'desc')->get();

                return response()->json([
                    'status' => true,
                    'message' => Config::get('message-constants.VEHICLE_YEARS_FETCHED'),
                    'data' => $years
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }


        public function getCarModels(Request $request)
        {
            try {
                $validator = Validator::make($request->all(), [
                    'car_make' => 'required|exists:cars,make',
                    'car_year' => 'required|exists:cars,year',
                ]);
        
                if ($validator->fails()) {
                    return response()->json([
                        'status' => false,
                        'message' => $validator->errors()->first()
                    ]);
                }
        
                $models = Car::where('make', $request->car_make)
                    ->where('year', $request->car_year)
                    ->select('id', 'model')
                    ->distinct()
                    ->get();
        
                return response()->json([
                    'status' => true,
                    'message' => Config::get('message-constants.VEHICLE_MODELS_FETCHED'),
                    'data' => $models
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        

        // Get makes based on selected year and model
        public function getCarMakes(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'car_year' => 'required|exists:cars,year',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first()
                ]);
            }

            $makes = Car::where('year', $request->car_year)
                ->select('id', 'make')
                ->distinct()
                ->get();

            return response()->json([
                'status' => true,
                'message' => Config::get('message-constants.VEHICLE_MAKES_FETCHED'),
                'data' => $makes
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getCarPartsList()
    {
        try {
            $carParts = CarPart::where('status', 1)
                ->select('id', 'name', 'status')
                ->get();

            return response()->json([
                'status' => true,
                'message' => 'Car parts list fetched successfully',
                'data' => $carParts
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

}
