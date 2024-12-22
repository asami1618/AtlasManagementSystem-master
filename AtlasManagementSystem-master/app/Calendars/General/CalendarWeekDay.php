<?php
namespace App\Calendars\General;

use App\Models\Calendars\ReserveSettings;
use Carbon\Carbon;
use Auth;

class CalendarWeekDay{
  protected $carbon;

  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  function getClassName(){
    return "day-" . strtolower($this->carbon->format("D"));
  }

  function pastClassName(){
    return;
  }

  function getPart()
  {
    return $this->getPart;
  }

  function getData()
  {
    return $this->getData;
  }

  /**
   * @return
   */

  function render(){
    return '<p class="day">' . $this->carbon->format("j"). '日</p>';
  }
  
  function selectPart($ymd){
    // 現在の日付
    $today = date('Y-m-d');

    // 予約枠の取得
    // 特定の日付（$ymd）について、各時間帯（1部、2部、3部）の予約可能枠（limit_users）を取得。
    // 時間帯ごとの予約設定が存在しない場合は、枠数を 0 として返す。
    $one_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '1')->first();
    $two_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '2')->first();
    $three_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '3')->first();
    
    // 予約枠が存在しない場合の初期化
    // 各部の予約枠が存在する場合はその limit_users（残り枠数）を取得し、存在しない場合は 0 を設定。
    // この処理を1部、2部、3部に対して個別に実行。
    // 1部
    if($one_part_frame){
      $one_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '1')->first()->limit_users;
    }else{
      $one_part_frame = '0';
    }

    // 2部
    if($two_part_frame){
      $two_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '2')->first()->limit_users;
    }else{
      $two_part_frame = '0';
    }

    // 3部
    if($three_part_frame){
      $three_part_frame = ReserveSettings::with('users')->where('setting_reserve', $ymd)->where('setting_part', '3')->first()->limit_users;
    }else{
      $three_part_frame = '0';
    }

    // <ユーザーの予約確認> 12/15
    // ①現在ログインしているユーザーの"id"を取得
    $user_id = Auth::id();
    $html = []; // 変数の初期化    

    // ②ReserveSettingsテーブルからsetting_reserveが$ymdと一致するレコードを取得
    $user_reservations = ReserveSettings::whereDate('setting_reserve', $ymd)
    // ③whereHas('users', function($query))は、
    // ReserveSettingsモデルが関連するusersテーブルのリレーション先に対して条件を追加する
    // ReserveSettingsレコードに関連するusersテーブルの情報も調べる
    ->whereHas('users', function ($query) use ($user_id) {
      $query->where('user_id', $user_id) //ログインしているユーザーの予約だけを探す
      // ④limit_users = 'confirmed'「予約が確定しているユーザーのみ」対象とする
      ->where('limit_users', 'confirmed');
    })
    ->get();

    // 過去日の場合
    if (strtotime($ymd) <= strtotime($today)) {
      if ($user_reservations->isEmpty()) {
        // 予約していない場合
        $html[] = '<p>受付終了</p>';
        // 12/22　下記追記・CalendarView80行目の「' . $day->everyDay() . '」を削除
        $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
      } else {
        // 予約がある場合のCalendarViewではselectPartは使用されていないため、表示がされていない
        // CalendarViewの54行目に記述
        foreach ($user_reservations as $reservation) {
          if (!empty($reservation->setting_part)) {
            $html[] = '<p>リモ' . $reservation->setting_part . '部</p>';
          }
        }
      }
    } else {    
    // 現在または未来日のセレクトボックス生成
    $html[] = '<select name="getPart[]" class="border-primary" style="width:70px; border-radius:5px;" form="reserveParts">';
    $html[] = '<option value="" selected></option>';   
    
    // 現在または未来日のオプションを追加
    // 各部の選択肢生成　残り枠数が 0 の場合は選択肢を disabled（無効化）し、選択不可とする。
    // 残り枠数が 1 以上の場合は有効な選択肢として表示
      // 1部
      if($one_part_frame == "0"){
        $html[] = '<option value="1" disabled>リモ1部(残り0枠)</option>';
      }else{
        $html[] = '<option value="1">リモ1部(残り'.$one_part_frame.'枠)</option>';
      }

      // 2部
      if($two_part_frame == "0"){
        $html[] = '<option value="2" disabled>リモ2部(残り0枠)</option>';
      }else{
        $html[] = '<option value="2">リモ2部(残り'.$two_part_frame.'枠)</option>';
      }

      // 3部
      if($three_part_frame == "0"){
        $html[] = '<option value="3" disabled>リモ3部(残り0枠)</option>';
      }else{
        $html[] = '<option value="3">リモ3部(残り'.$three_part_frame.'枠)</option>';
      }
      // セレクトボックスの閉じタグ
      $html[] = '</select>';
    }
      // 配列 $html を文字列に結合し、最終的なHTMLコードとして返す。
      return implode('', $html);
    }

  function getDate(){
    return '<input type="hidden" value="'. $this->carbon->format('Y-m-d') .'" name="getData[]" form="reserveParts">';
  }

  // function __construct() {
  //   $this->carbon = Carbon::now(); // 現在の日付と時刻を設定
  // }

  function everyDay(){
    return $this->carbon->format('Y-m-d');
  }

  function authReserveDay(){
    return Auth::user()->reserveSettings->pluck('setting_reserve')->toArray();
  }

  function authReserveDate($reserveDate){
    return Auth::user()->reserveSettings->where('setting_reserve', $reserveDate);
  }
}
