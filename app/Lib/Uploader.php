<?php
namespace App\Lib;
use Illuminate\Database\Eloquent\Model;
use Image;
use Illuminate\Support\Facades\Storage;
/**
 *
 *
 * This Library use for image upload and resizing.
 *
 *
 **/

class Uploader
{

    public static function doUpload($file,$path,$thumb=false,$pre=null,$id=null){
        $response = [];
        $image = $file;
		$rand = mt_rand(1000,9999);
        if($id!=null){
            $file = $id.$rand.'.'.$image->getClientOriginalExtension();
        }else{
            $file = $pre.time().$rand.'.'.$image->getClientOriginalExtension();
        }
        $destinationPath = public_path().'/'.$path;
        Image::make($image)->save($destinationPath.$file,70);
        $thumbPath = '';
        if($thumb==true){
            $thumbPath = public_path($path).'thumb/'.$file;
            if(!file_exists(public_path($path).'thumb/')) {
              mkdir(public_path($path).'thumb/', 0777, true);
            }
            $cropInfo = Image::make($image)->heighten(200)->save($thumbPath);
        }

        if($path == '/uploads/profile/'){
            $thumbPath = public_path($path).'thumb/'.$file;
            if(!file_exists(public_path($path).'thumb/')) {
              mkdir(public_path($path).'thumb/', 0777, true);
            }
            $cropInfo = Image::make($image)->resize(150, 150)->save($thumbPath);
        }

        if($path == '/uploads/map_icon/'){
            $thumbPath = public_path($path).'thumb/'.$file;
            if(!file_exists(public_path($path).'thumb/')) {
              mkdir(public_path($path).'thumb/', 0777, true);
            }
            $cropInfo = Image::make($image)->resize(150, 150)->save($thumbPath);
        }


        $response['status']     = true;
        $response['file']       = "public".$path.$file;
        $response['thumb']       = "public".$path."thumb/".$file;
        $response['file_name']  = $file;
        $response['path']       = $path;
        return $response;

    }


      public static function documentUpload($file,$path,$thumb=false,$id=null,$pre=null){
         $response = [];
        $image = $file;
        if($id!=null){
            $file = $id.'.'.$image->getClientOriginalExtension();
        }else{
            $file = $pre.time().rand(0,9999).'.'.$image->getClientOriginalExtension();
        }
        $destinationPath = public_path().'/'.$path;
        if($uploaded = $image->move($destinationPath, $file)){

            // $cropInfo = Image::make($uploaded->getRealPath())->resize(568, 231)->save($destinationPath.$file);
            $response['status']     = true;
            $response['file']       = "public".$path.$file;
            $response['file_name']  = $file;
            $response['path']       = $path;
        }else{
            $response['status']     = false;
        }
        return $response;

    }
}
