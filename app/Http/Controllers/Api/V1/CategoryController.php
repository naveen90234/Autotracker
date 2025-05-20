<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Config;

class CategoryController extends Controller
{
    public function getCategories(){
        try {
            $categories = Category::orderBy('id', 'DESC')->get();
            return response()->json(['status' => true, 'message' => Config::get('message-constants.CATEGORY_SUCCESS'), 'data' => $categories]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object)[]]);
        }
    }
}
