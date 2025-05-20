<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SellingTip;
use Illuminate\Http\Request;

class SellingTipController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10); // default 10 per page
    
            $tips = SellingTip::orderBy('created_at', 'desc')->paginate($perPage);
    
            $tips->getCollection()->transform(function ($tip) {
                $tip->image = $tip->image ? asset('public/storage/article_images/' . $tip->image) : null;
                return $tip;
            });
    
            return response()->json([
                'status' => true,
                'message' => 'Tips retrieved successfully',
                'data' => [
                    'tips' => $tips->items(),
                    'pagination' => [
                        'total' => $tips->total(),
                        'current_page' => $tips->currentPage(),
                        'per_page' => $tips->perPage(),
                        'last_page' => $tips->lastPage(),
                        'from' => $tips->firstItem(),
                        'to' => $tips->lastItem()
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

}
