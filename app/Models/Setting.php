<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Validator;
class Setting extends Model
{
    protected $fillable = [
        'field_title','field_name','field_type','value'
    ];

    public $timestamps = false;

    public static function validate($input,$id){
        if(!$id){
            $rules = [
                'field_title'     =>      'required|max:100',
                'field_name'      =>      'required|max:100|unique:settings,field_name,'.$id,
                'field_type'      =>      'required',
                'value'           =>      'required|max:100'
            ];
        }else{
           $rules = [
                'field_title'     =>      'required|max:100',
                'field_name'      =>      'required|max:100|unique:settings,field_name,'.$id,
                'field_type'      =>      'required'
            ];
        }

        $messages = [
            'field_title.required'     =>      'Field title is required',
            'field_name.required'      =>      'Field name is required',
            'field_name.unique'        =>      'Field name has already been taken',
            'field_type.required'      =>      'Field type is required',
            'value.required'           =>      'Value is required'
        ];

        return validator::make($input,$rules,$messages);
    }



    public function getValueAttribute($value)
    {
        if($this->field_type == 'image'){
            $img = $value;
            if ($img != NULL) {
                $img = base_url() . '/' . $img;
            }
            return $img;
        }
        return $value;
    }

}
