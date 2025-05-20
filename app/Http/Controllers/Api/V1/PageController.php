<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pages as PageModel;
use App\Models\Setting;
use Validator;
use JWTAuth;
use Config;

class PageController extends Controller
{
    public function getPage(Request $request){

        try{

            $validator = Validator::make($request->all(), [
                'page_slug' => 'required',
            ]);
            if ($validator->fails()) {
                $error = $validator->errors()->first();
                return response()->json(['status' => false, 'message' => $error]);
            } else {
                $page_content = PageModel::where(['slug'=> $request->page_slug])->first();
                $page_content->description = strip_tags($page_content->description);

                if($page_content){
                    return response()->json(['status' => true, 'message' => Config::get('message-constants.CMS_PAGE_SUCCESS'), 'data' => $page_content]);
                } else {
                    return response()->json(['status' => false, 'message' => Config::get('message-constants.CMS_PAGE_TRY_AGAIN'), 'data' => (object)[]]);
                }
            }

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage(), 'data' => (object)[]]);
        }
    }


    public function welcomeText(Request $request)
    {
        try {
            $data = Setting::select('field_title','value')->whereIn('field_name', ['welcome_text1','welcome_text2','welcome_text3'])->get();

            return response()->json(['status' => true, 'message' => Config::get('message-constants.WELCOME_TEXT_SUCCESS'), 'data' => $data]);

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }

    }
}
