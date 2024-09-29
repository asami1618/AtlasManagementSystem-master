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
        // リレーションの定義 9/29　追記
        //「１対多」の「1」側 → メソッド名は単数形でbelongsToを使う
        return $this->belongsTo('App\Models\Categories\MainCategory');
    }

}