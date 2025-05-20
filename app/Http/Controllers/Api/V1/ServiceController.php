<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Service;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\DrivingStyle;
use JWTAuth;
use DB;

class ServiceController extends Controller
{
   
    public function addService(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|integer|exists:vehicles,id',
            'driving_style' => 'required|integer|exists:driving_styles,id',
            'service_name' => 'required|string|unique:services,service_name',
            'service_date' => 'required|date',
            'service_mileage' => 'required|integer|min:0',
            'parts_list_id_cost' => 'required|array|min:1',
            'parts_list_id_cost.*.cost' => 'required|numeric|min:0',
            'service_cost' => 'required|numeric|min:0',
            'note' => 'nullable|string',
            'document' => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $user = JWTAuth::toUser(JWTAuth::getToken());

        $filePath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document');
            $documentName = time() . '.' . $documentPath->getClientOriginalExtension();
            $documentPath->move(public_path('uploads/services/'), $documentName);
            $filePath = '/public/uploads/services/' . $documentName;
        }

        

        $service = Service::create([
            'user_id' => $user->id,
            'car_id' => $request->car_id,
            'driving_style_id' => $request->driving_style,
            'service_name' => $request->service_name,
            'service_date' => $request->service_date,
            'service_mileage' => $request->service_mileage,
            'service_cost' => $request->service_cost,
            'note' => $request->note,
            'document' => $filePath
        ]);

        $partsListIdCost = [];
        foreach ($request->parts_list_id_cost as $part) {
            $partsListIdCost[] = [
                'id' => $part['id'], 
                'cost' => $part['cost']
            ];
            
            // Save each part expense in car_parts_expenses table
            DB::table('car_parts_expenses')->insert([
                'user_id' => $user->id,
                'car_part_id' => $part['id'],
                'cost' => $part['cost'],
                'service_id' => $service->id
            ]);
        }

        // Fetch parts expenses for this service
        $partsExpenses = DB::table('car_parts_expenses')
            ->where('service_id', $service->id)
            ->select('car_part_id as id', 'cost')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Service added successfully.',
            'data' => [
                'service' => $service,
                'parts_expenses' => $partsExpenses
            ]
        ]);


    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
        ]);
    }
}

public function showServiceList(Request $request)
{
    try {
        $user = JWTAuth::toUser(JWTAuth::getToken());

        $services = Service::where('user_id', $user->id)->latest()->get();

        $servicesWithParts = [];
        foreach($services as $service) {
            $partsExpenses = DB::table('car_parts_expenses')
                ->where('service_id', $service->id)
                ->select('car_part_id as id', 'cost')
                ->get();

            $serviceData = $service->toArray();
            $serviceData['parts_expenses'] = $partsExpenses;
            $servicesWithParts[] = $serviceData;
        }

        return response()->json([
            'status' => true,
            'message' => 'Service list fetched successfully.',
            'data' => $servicesWithParts
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
        ]);
    }
}
public function serviceDetails(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ]);
        }

        $user = JWTAuth::toUser(JWTAuth::getToken());
        
        $service = Service::where('id', $request->service_id)
                         ->where('user_id', $user->id)
                         ->first();

        if (!$service) {
            return response()->json([
                'status' => false,
                'message' => 'Service not found',
                'data' => (object)[]
            ]);
        }

        $partsExpenses = DB::table('car_parts_expenses')
            ->where('service_id', $service->id)
            ->select('car_part_id as id', 'cost')
            ->get();

        $serviceData = $service->toArray();
        $serviceData['parts_expenses'] = $partsExpenses;

        return response()->json([
            'status' => true,
            'message' => 'Service details fetched successfully.',
            'data' => $serviceData
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'data' => (object)[]
        ]);
    }
}

public function getServiceHistory(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|integer|exists:vehicles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        $user = JWTAuth::toUser(JWTAuth::getToken());

        $services = Service::where('user_id', $user->id)
            ->where('car_id', $request->car_id)
            ->orderBy('service_date', 'desc')
            ->get();

        $servicesWithParts = [];
        foreach($services as $service) {
            $partsExpenses = DB::table('car_parts_expenses')
                ->where('service_id', $service->id)
                ->select('car_part_id as id', 'cost')
                ->get();

            $serviceData = $service->toArray();
            $serviceData['parts_expenses'] = $partsExpenses;
            $servicesWithParts[] = $serviceData;
        }

        return response()->json([
            'status' => true,
            'message' => 'Service history fetched successfully.',
            'data' => $servicesWithParts
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ]);
    }
}

public function getCarLastService(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'car_id' => 'required|integer|exists:vehicles,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => (object)[]
            ]);
        }

        $user = JWTAuth::toUser(JWTAuth::getToken());
        
        // Get the latest service for the specified car
        $latestService = Service::where('user_id', $user->id)
                               ->where('car_id', $request->car_id)
                               ->orderBy('service_date', 'desc')
                               ->first();

        if (!$latestService) {
            return response()->json([
                'status' => false,
                'message' => 'No service records found for this car.',
                'data' => (object)[]
            ]);
        }

        // Return only the required fields
        $serviceData = $latestService;

        return response()->json([
            'status' => true,
            'message' => 'Latest service details fetched successfully.',
            'data' => $serviceData
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage(),
            'data' => (object)[]
        ]);
    }
}

}

