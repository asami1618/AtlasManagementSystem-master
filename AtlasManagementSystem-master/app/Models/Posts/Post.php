<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'user_id',
        'post_title',
        'post',
    ];

    public function user(){
        return $this->belongsTo('App\Models\Users\User');
    }

    public function postComments(){
        return $this->hasMany('App\Models\Posts\PostComment');
    }

    public function subCategories(){
        // リレーションの定義 9/21　追記 →10/19　修正
        // 「postsテーブル」と「subCategories」で「多対多」の関係
        // 　return $this->belongsToMany('①関係するモデルの場所', '②中間テーブルの名前' ,'③中間テーブルにある自分(post)のidが入るカラム' , '④中間テーブルの相手モデル(sub_category)に関係しているカラム');
        return $this->belongsToMany('App\Models\Categories\SubCategory' , 'post_sub_categories' ,'post_id' ,'sub_category_id');
    }

    // コメント数
    public function commentCounts($post_id){
        return Post::with('postComments')->find($post_id)->postComments()->get()->count();
    }
}