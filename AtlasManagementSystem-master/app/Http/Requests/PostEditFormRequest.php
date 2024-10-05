<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostEditFormRequest extends FormRequest
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
        return [
            'post_title' => 'required|string|max:100',
            'post_body' => 'required|string|max:5000',
        ];
    }

    public function messages(){
        return [
            'post_title.required' => '入力必須です。',
            'post_title.max' => '最大文字数は100文字です。',
            'post_body.required' => '入力必須です。',
            'post_body.max' => '最大文字数は5000文字です。',
        ];
    }
}
