<?php

namespace App\Models\Categories;

use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;
    protected $fillable = [
        'main_category'
    ];

    public function subCategories(){
        // リレーションの定義 9/29　追記　→10/19 修正
        //「１対多」の　「1」から見た「多」を表す → メソッド名は複数形・「hasMany」を使う
        return $this->hasMany('App\Models\Categories\SubCategory');
        
    }

}