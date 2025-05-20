<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\Setting;
use App\Lib\Uploader;
use App\Models\VersionControlSetting;
use Config;
use Thumbnail;
use JWTAuth;
use App\Models\Vehicle;
use App\Models\Garage;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class HomeController extends Controller
{
    public function getAuthToken(Request $request)
    {
        try {
            $data = $request->all();
            $validator = Validator::make($data, [
                'access_token' => 'required',
            ]);

            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $error]);
            } else {
                $access_token = Config::get('message-constants.AUTH_TOKEN_ACCESS_KEY');
                if ($access_token == $request->access_token) {

                    $auth = Setting::select('value')->where('field_name', 'auth_token')->first();
                    return response()->json(['status' => true, 'message' => Config::get('message-constants.AUTH_TOKEN_SUCCESS'), 'data' => $auth]);

                } else {

                    return response()->json(['status' => false, 'message' => Config::get('message-constants.AUTH_TOKEN_INVALID'), 'data' => []]);

                }
            }
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    public function versionControl()
    {
        try {

            $getSetting = VersionControlSetting::select('field_name', 'value')->get();

            $versionControlSetting = [];

            foreach ($getSetting as $setting) {
                $versionControlSetting[$setting['field_name']] = $setting['value'];
            }
            return response()->json(['status' => true, 'message' => Config::get('message-constants.VERSION_CONTROL_SUCCESS'), 'data' => $versionControlSetting]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }


    public function chatUpload(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'file' => 'required',
                'file_type' => 'required|in:AUDIO,IMAGE,VIDEO'
            ]);
            if ($validator->fails()) {
                $response['status'] = false;
                $response['message'] = $validator->errors()->first();
                $response['data'] = (object) [];
                return response()->json($response);
            } else {

                if ($request->hasFile('file')) {

                    $destinationPath = '/uploads/chat/';
                    if (!file_exists(public_path($destinationPath))) {
                        mkdir(public_path($destinationPath), 0777, true);
                    }

                    $responseData = Uploader::documentUpload($request->file('file'), $destinationPath);
                    if ($responseData['status'] == "true") {

                        $data['file_path'] = $responseData['file'];


                        if ($request->file_type == 'VIDEO') {
                            //generating thumbnail of video
                            $thumbnail_image = time() . rand(0, 9999) . '.jpg';

                            $thumbnail_path = 'uploads/chat/thumb/';

                            if (!file_exists(public_path($thumbnail_path))) {
                                mkdir(public_path($thumbnail_path), 0777, true);
                            }


                            $thumbnail_status = Thumbnail::getThumbnail($data['file_path'], public_path($thumbnail_path), $thumbnail_image, 1);
                            $data['thumbnail_path'] = $thumbnail_path . $thumbnail_image;

                        } else {

                            $data['thumbnail_path'] = "";

                        }


                        return response()->json(['status' => true, 'message' => Config::get('message-constants.CHAT_UPLOAD_SUCCESS'), 'data' => $data]);
                    } else {
                        return response()->json(['status' => false, 'message' => Config::get('message-constants.CHAT_UPLOAD_TRY_AGAIN'), 'data' => (object) []]);
                    }
                } else {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.CHAT_UPLOAD_SELECT_FILE'), 'data' => (object) []]);
                }

            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => []]);
        }
    }

    public function createGarage(Request $request)
{
    try {
        // Validate request
        $validator = Validator::make($request->all(), [
            'garage_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        // Get authenticated user
        $user = Auth::user();

        // Check if user already has a garage
        $existingGarage = Garage::where('user_id', $user->id)->first();
        if ($existingGarage) {
            return response()->json([
                'status' => false,
                'message' => 'You can only create one garage.',
                'data' => $existingGarage // Optional: Return existing garage details
            ]);
        }

        // Create new garage
        $garage = Garage::create([
            'user_id' => $user->id,
            'garage_name' => $request->garage_name,
        ]);

        // Update user's garage count to 1
        $user->garagecount = 1;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Garage created successfully.',
            'data' => [
                'garage_id' => $garage->id,
                'garage_name' => $garage->garage_name,
                'garagecount' => $user->garagecount,
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json(['status' => false, 'message' => $e->getMessage()]);
    }
}


public function homePage(Request $request)
{
    try {
        $userId = JWTAuth::toUser(JWTAuth::getToken())->id;

            $user = User::where('id',$userId)->first();

        // Get user's garage
        $garage = Garage::where('user_id', $user->id)->first();

        // Get user's cars (assuming Vehicle model has a user_id or garage_id relation)
        $cars = Vehicle::where('user_id', $user->id)->get();

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
                'vehicle_images' => $car->vehicle_images ?? [], // Assuming images stored as comma-separated string
            ];
        }

        // Count total rides of the user
        $ridesCount = Vehicle::where('user_id', $user->id)->count();

        return response()->json([
            'status' => true,
            'message' => 'Home page data fetched successfully.',
            'data' => [
                'garage_name' => $garage ? $garage->garage_name : '',
                'cars' => $carData,
                'rides_count' => $ridesCount,
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

public function nearbyMechanics(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'min_distance' => 'nullable|numeric|min:0|max:49',
                'max_distance' => 'nullable|numeric|min:1|max:50',
                'rating' => 'nullable|numeric|min:1|max:5',
                'service_type' => 'nullable|string|max:255',
                'engine_type' => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ], 422);
            }

            $latitude = $request->latitude;
            $longitude = $request->longitude;

            $minMiles = $request->min_distance ?? 0;
            $maxMiles = $request->max_distance ?? 50;

            $minMeters = $minMiles * 1609.34;
            $maxMeters = $maxMiles * 1609.34;

            $keyword = $request->service_type ? $request->service_type . " mechanic" : "car mechanic";
            $apiKey = config('message-constants.api_key');
            $client = new \GuzzleHttp\Client();

            $url = "https://maps.googleapis.com/maps/api/place/nearbysearch/json";
            $params = [
                'location' => "$latitude,$longitude",
                'radius' => $maxMeters,
                'keyword' => $keyword,
                'type' => 'car_repair',
                'key' => $apiKey
            ];

            $response = $client->get($url, ['query' => $params]);
            $data = json_decode($response->getBody(), true);

            $results = collect($data['results'] ?? [])->filter(function($item) use ($request) {
                // Filter by exact rating match
                if ($request->filled('rating')) {
                    $requestedRating = floatval($request->rating);
                    $itemRating = floatval($item['rating'] ?? 0);
                    return round($itemRating) == round($requestedRating); // Round to match exact rating
                }
                return true;
            })->map(function ($item) use ($request, $client, $apiKey, $latitude, $longitude) {
                $placeId = $item['place_id'] ?? null;

                $lat = $item['geometry']['location']['lat'] ?? null;
                $lng = $item['geometry']['location']['lng'] ?? null;

                $distance = null;
                if ($lat && $lng) {
                    $earthRadius = 6371000; // meters
                    $dLat = deg2rad($lat - $latitude);
                    $dLon = deg2rad($lng - $longitude);
                    $a = sin($dLat / 2) * sin($dLat / 2) +
                        cos(deg2rad($latitude)) * cos(deg2rad($lat)) *
                        sin($dLon / 2) * sin($dLon / 2);
                    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                    $distance = $earthRadius * $c;
                }

                // Get place details
                $phone = null;
                $website = null;
                if ($placeId) {
                    try {
                        $detailsUrl = "https://maps.googleapis.com/maps/api/place/details/json";
                        $detailsResponse = $client->get($detailsUrl, [
                            'query' => [
                                'place_id' => $placeId,
                                'fields' => 'formatted_phone_number,website',
                                'key' => $apiKey
                            ]
                        ]);
                        $detailsData = json_decode($detailsResponse->getBody(), true);
                        $phone = $detailsData['result']['formatted_phone_number'] ?? null;
                        $website = $detailsData['result']['website'] ?? null;
                    } catch (\Exception $e) {
                        \Log::warning('Place Details API failed for place_id: ' . $placeId);
                    }
                }

                // Photos
                $photos = [];
                if (!empty($item['photos'])) {
                    foreach ($item['photos'] as $photo) {
                        $photos[] = "https://maps.googleapis.com/maps/api/place/photo?maxwidth=400&photoreference={$photo['photo_reference']}&key={$apiKey}";
                    }
                }

                return [
                    'id' => $placeId,
                    'name' => $item['name'] ?? '',
                    'address' => $item['vicinity'] ?? '',
                    'rating' => $item['rating'] ?? null,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'is_open_now' => $item['opening_hours']['open_now'] ?? null,
                    'total_ratings' => $item['user_ratings_total'] ?? 0,
                    'phone' => $phone,
                    'website' => $website,
                    'images' => $photos,
                    'engine_type_supported' => $request->engine_type ?? 'all',
                    'distance' => $distance, // in meters
                ];
            })->filter(function ($item) use ($minMeters, $maxMeters) {
                if ($item['distance'] === null) return false;
                // Distance filter only
                return $item['distance'] >= $minMeters && $item['distance'] <= $maxMeters;
            })->sortByDesc('rating')->values();

            return response()->json([
                'status' => true,
                'message' => 'Nearby mechanic shops fetched successfully',
                'data' => $results
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Nearby Mechanics Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong: ' . $e->getMessage(),
                'data' => []
            ]);
        }
    }



}
