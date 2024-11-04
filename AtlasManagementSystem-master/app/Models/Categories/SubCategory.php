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
        // リレーションの定義 9/29　追記 →10/19　修正
        //「1対多」の「多」から見た「1」を表す → メソッド名は単数形・「belongsTo」を使う
        return $this->belongsTo('App\Models\Categories\MainCategory');
    }

    public function posts(){
        // リレーションの定義 10/19
        // 「postsテーブル」と「subCategories」で「多対多」の関係
        // 　return $this->belongsToMany('①関係するモデルの場所', '②中間テーブルの名前' ,'③中間テーブルにある自分(sub_category)のidが入るカラム' , '④中間テーブルの相手モデル(post)に関係しているカラム');
        return $this->belongsToMany('App\Models\Posts\Post', 'post_sub_categories', 'sub_category_id', 'post_id');
    }
}