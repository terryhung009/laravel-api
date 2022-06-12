<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class ResizeImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'image' =>['required'],
            'w' =>['required','regex:/^\d+(\.\d+)?%?$/'],//50,50%,50.123,50.123%
            'h' =>'regex:/^\d+(\.\d+)?%?$/',
            'album_id'=>'exist:\App\models\Album,id'

        ];

        // $all = $this->all();
        // if (isset($all['image']) && $all['image'] instanceof UploadedFile) {
        //     $rules['image'][] = 'image';
        // } else {
        //     $rules['image'][] = 'url';
        // }

        // echo '<pre>';
        // var_dump($rules);
        // echo '</pre>';
        // exit;

           
        $image = $this->all()['image'] ?? false;
        // $image = $this->all();  
        // $image = $this->file('image'); 
        // echo '<pre>';
        // var_dump($image);
        // echo '</pre>';
        // exit;
        if($image && $image instanceof UploadedFile){
            $rules['image'][]='image';

        }else{
            $rules['image'][]='url';
        }

        // echo '<pre>';
        // var_dump($rules);
        // echo '</pre>';
        // exit;
        
        return $rules;


    }
}
