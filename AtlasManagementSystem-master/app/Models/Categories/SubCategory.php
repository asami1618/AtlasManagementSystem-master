<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'main_category_id',
        'sub_category',
    ];
    public function mainCategory(){
        // リレーションの定義 9/29　追記
        //「１対多」の「多」側 → メソッド名は複数形でhasManyを使う
        return $this->hasMany('App\Models\Categories\SubCategory');
    }

    public function posts(){
        // リレーションの定義
    }
}