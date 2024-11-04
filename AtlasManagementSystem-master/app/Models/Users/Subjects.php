<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

use App\Models\Users\User;

class Subjects extends Model
{
    const UPDATED_AT = null;


    protected $fillable = [
        'subject'
    ];

    public function users(){
        return $this->belongsToMany(User::class,'subject_users', 'subject_id', 'user_id' );// リレーションの定義 6/16 追記 7/6　修正　11/4修正
    }
}