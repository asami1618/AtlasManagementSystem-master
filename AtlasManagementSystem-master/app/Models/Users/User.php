<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Posts\Like;
use Auth;

class User extends Authenticatable
{
    use Notifiable;
    use softDeletes;

    const CREATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';

    protected $fillable = [
        'over_name',
        'under_name',
        'over_name_kana',
        'under_name_kana',
        'mail_address',
        'sex',
        'birth_day',
        'role',
        'password'
    ];

    protected $dates = ['deleted_at'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function posts(){
        return $this->hasMany('App\Models\Posts\Post');
    }

    public function calendars(){
        return $this->belongsToMany('App\Models\Calendars\Calendar', 'calendar_users', 'user_id', 'calendar_id')->withPivot('user_id', 'id');
    }

    public function reserveSettings(){
        return $this->belongsToMany('App\Models\Calendars\ReserveSettings', 'reserve_setting_users', 'user_id', 'reserve_setting_id')->withPivot('id');
    }

    public function subjects(){
        // 　return $this->belongsToMany('①関係するモデルの場所', '②中間テーブルの名前' ,'③中間テーブルにある自分(post)のidが入るカラム' , '④中間テーブルの相手モデル(sub_category)に関係しているカラム');
        // ::classを使用しても'App'の書き方と場所は同じ。'::class'の書き方をする場合はUse宣言するかしないかを判断する。
        // 　判断基準としてはディレクトリが同じであればUse宣言不要、別の場所にあれば必要。
        return $this->belongsToMany(Subjects::class,'subject_users', 'user_id', 'subject_id');// リレーションの定義 6/16追記 7/15　修正 11/4修正
    }

    // いいねしているかどうか
    public function is_Like($post_id){
        return Like::where('like_user_id', Auth::id())->where('like_post_id', $post_id)->first(['likes.id']);
    }

    public function likePostId(){
        return Like::where('like_user_id', Auth::id());
    }
    
}
