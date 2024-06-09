<?php

namespace App\Models\Posts;

use Illuminate\Database\Eloquent\Model;

use App\Models\Users\User;

class PostComment extends Model
{
    const UPDATED_AT = null;
    const CREATED_AT = null;

    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
    ];

    public function post(){
        return $this->belongsTo('App\Models\Posts\Post');
    }

    public function commentUser($user_id){
        return User::where('id', $user_id)->first();
    }
}