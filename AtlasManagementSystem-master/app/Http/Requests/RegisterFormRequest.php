<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // falseからtrueに変更　権限を持っているかどうかを判断する
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
            // 項目名　=>　ルール
            'over_name' => 'required|string|max:10',
            'under_name' => 'required|string|max:10',
            'over_name_kana' => 'required|string|max:30|regex:/\A[ァ-ヶー]+\z/u',
            'under_name_kana' => 'required|string|max:30|regex:/\A[ァ-ヶー]+\z/u',
            'mail_address' => 'required|email:strict,dns,spoof|unique:users,mail_address|max:100',
            'sex' => 'required|' . Rule::in(["1", "2", "3"]),
            'old_year' => 'required',
            'old_month' => 'required',
            'old_day' => 'required',
            'birth' => 'required|after:1999-12-31|date',
            'role' => 'required|in:1,2,3,4',
            'password' => 'required|min:8|max:30|confirmed',        
        ];
    }
    public function messages()
    {
        return[
            'over_name.required' => '※入力必須です',
            'over_name.max' => '※10文字以下で入力してください',
            'under_name.required' => '※入力必須です',
            'under_name.max' => '※10文字以下で入力してください',
            'over_name_kana.required' => '※入力必須です' ,
            'over_name_kana.max' => '※30文字以下で入力してください' ,
            'over_name_kana.regex' => '※入力はカタカナのみです' ,
            'under_name_kana.required' => '※入力必須です' ,
            'under_name_kana.max' => '※30文字以下で入力してください' ,
            'under_name_kana.regex' => '※入力はカタカナのみです' ,
            'mail_address.required' => '※入力必須です' ,
            'mail_address.email' => '※形式が正しくありません' ,
            'mail_address.unique' => '※すでに登録されています' ,
            'mail_address.max' => '※100文字以下で入力してください' ,
            'sex.required' => '※入力必須です' ,
            'sex.in' => '※男性、女性、その他以外は無効です。' ,
            'birth.required' => '※入力必須です',
            'birth.after' => '日付は「2000年から本日まで」です。',
            'birth.date' => '正しい日付ではありません。',
            'role.required' => '※入力必須です' ,
            'role.in' => '※講師(国語)、講師(数学)、教師(英語)、生徒以外は無効です。' ,
            'password.required' => '※入力必須です' ,
            'password.min' => '※8文字以上で入力してください' ,
            'password.max' => '※30文字以内で入力してください' ,
            'password.confirmed' => '※確認用パスワードと一致しません' ,
        ];
    }

    protected function prepareForValidation()
    {
        $birth = ($this->filled(['old_year', 'old_month','old_day'])) ? $this->old_year .'-'. $this->old_month .'-'. $this->old_day : '';
        $this->merge([
            'birth' => $birth
        ]);
    }
}
