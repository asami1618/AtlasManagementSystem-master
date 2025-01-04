<?php
namespace App\Calendars\Admin;

use Carbon\Carbon;
use App\Models\Calendars\ReserveSettings;

class CalendarWeekDay{
  protected $carbon;

  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  function getClassName(){
    return "day-" . strtolower($this->carbon->format("D"));
  }

  function render(){
    return '<p class="day">' . $this->carbon->format("j") . '日</p>';
  }

  function everyDay(){
    return $this->carbon->format("Y-m-d");
  }

  function dayPartCounts($ymd){
    $html = [];
    $one_part = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '1')->first();
    $two_part = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '2')->first();
    $three_part = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '3')->first();

    // 12/15　スクール予約確認画面
    $html[] = '<div class="text-left">';
    if($one_part){
      // 1/4 追記　リレーションからユーザー数を取得
      $reserveSettings = ReserveSettings::where('setting_reserve', $ymd) //「setting_reserve」カラムが　$ymd の値と一致するレコードを検索する
      ->where('setting_part', 1) //「setting_part」カラムが「1」であるレコードを条件に追加・この部分で、2つの条件（setting_reserve が $ymd で、setting_part が 1）に一致するレコードを絞り込み
      ->first();
      $count_one = $reserveSettings->users()->count();
      // dd($count_one);
      $html[] = '<p class="day_part m-0 pt-1"><a href="/calendar/' . urlencode($ymd) .'/1">1部</a><span class="reservation-count"> ' . $count_one . ' </span></p>';
    }
    if($two_part){
      // 1/4 追記　リレーションからユーザー数を取得
      $reserveSettings = ReserveSettings::where('setting_reserve', $ymd) //「setting_reserve」カラムが　$ymd の値と一致するレコードを検索する
      ->where('setting_part', 2) //「setting_part」カラムが「2」であるレコードを条件に追加・この部分で、2つの条件（setting_reserve が $ymd で、setting_part が 2）に一致するレコードを絞り込み
      ->first();
      $count_two = $reserveSettings->users()->count();
      // dd($count_one);
      $html[] = '<p class="day_part m-0 pt-1"><a href="/calendar/' . urlencode($ymd) .'/2">2部</a><span class="reservation-count"> ' . $count_two . ' </span></p>';
    }
    if($three_part){
      $reserveSettings = new ReserveSettings(); // 1/4追記　インスタンス生成
      // 1/4 追記　リレーションからユーザー数を取得
      $reserveSettings = ReserveSettings::where('setting_reserve', $ymd) //「setting_reserve」カラムが　$ymd の値と一致するレコードを検索する
      ->where('setting_part', 3) //「setting_part」カラムが「3」であるレコードを条件に追加・この部分で、2つの条件（setting_reserve が $ymd で、setting_part が 3）に一致するレコードを絞り込み
      ->first();
      $count_three = $reserveSettings->users()->count();
      // dd($count_one);
      $html[] = '<p class="day_part m-0 pt-1"><a href="/calendar/' . urlencode($ymd) .'/3">3部</a><span class="reservation-count"> ' . $count_three . ' </span></p>';
    }
    $html[] = '</div>';

    // 1/4 「予約している人数を表示」処理解説
    // 1.「$reserveSettings」にデータを取得する部分
    // <ReserveSettings::where>
    // ->「ReserveSettings」テーブルから特定の条件に一致するレコードを検索

    // <条件1 ('setting_reserve', $ymd)>
    // -> 「ReserveSettings」テーブルの「setting_reserve」カラムが＄ymd(ユーザーが選択した日付)と一致するレコードを検索する

    // <条件2 ('setting_part', 1)>
    // -> 「ReserveSettings」テーブルの「setting_part」カラムが1(「1部」を意味する値)であるレコードに絞り込む

    // ・->first();
    // 条件に一致する最初のレコードを取得　この結果は「$reserveSettings」に格納される

    // 2.リレーションを通じてユーザー数を取得
    // $reserveSettings->users()
    // ->「ReserveSettings」モデルに定義されたリレーション(users)を使って「ReserveSettings」レコードに関連づけられている「User」レコードを取得
    // 今回は「belongsToMany」と設定されている

    // ->count();
    // 関連づけられた「User」レコードの数をカウント　この数は「$count_one」に格納される


    return implode("", $html);
  }


  function onePartFrame($day){
    $one_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '1')->first();
    if($one_part_frame){
      $one_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '1')->first()->limit_users;
    }else{
      $one_part_frame = "20";
    }
    return $one_part_frame;
  }
  function twoPartFrame($day){
    $two_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '2')->first();
    if($two_part_frame){
      $two_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '2')->first()->limit_users;
    }else{
      $two_part_frame = "20";
    }
    return $two_part_frame;
  }
  function threePartFrame($day){
    $three_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '3')->first();
    if($three_part_frame){
      $three_part_frame = ReserveSettings::where('setting_reserve', $day)->where('setting_part', '3')->first()->limit_users;
    }else{
      $three_part_frame = "20";
    }
    return $three_part_frame;
  }

  //
  function dayNumberAdjustment(){
    $html = [];
    $html[] = '<div class="adjust-area">';
    $html[] = '<p class="d-flex m-0 p-0">1部<input class="w-25" style="height:20px;" name="1" type="text" form="reserveSetting"></p>';
    $html[] = '<p class="d-flex m-0 p-0">2部<input class="w-25" style="height:20px;" name="2" type="text" form="reserveSetting"></p>';
    $html[] = '<p class="d-flex m-0 p-0">3部<input class="w-25" style="height:20px;" name="3" type="text" form="reserveSetting"></p>';
    $html[] = '</div>';
    return implode('', $html);
  }
}