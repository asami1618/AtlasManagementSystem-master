<?php

namespace App\Http\Requests\BulletinBoard;

use Illuminate\Foundation\Http\FormRequest;

class PostFormRequest extends FormRequest
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
            'post_category_id' => 'required',
            'post_title' => 'required|string|max:100',
            'post_body' => 'required|string|max:5000',
            'comment' => 'required|string|max:250',
        ];
    }

    public function messages(){
        return [
            'post_category_id.required' => '入力必須です。',
            'post_title.required' => '入力必須です。',
            'post_title.max' => '最大文字数は100文字です。',
            'post_body.required' => '入力必須です。',
            'post_body.max' => '最大文字数は5000文字です。',
            'comment.required' => '入力必須です。',
            'comment.max' => '最大文字数は250文字です。',
        ];
    }
}