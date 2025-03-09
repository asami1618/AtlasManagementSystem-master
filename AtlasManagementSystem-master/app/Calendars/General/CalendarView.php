<?php
namespace App\Calendars\General;

use Carbon\Carbon;
use Auth;

class CalendarView{

  private $carbon;
  function __construct($date){
    $this->carbon = new Carbon($date);
  }

  public function getTitle(){
    return $this->carbon->format('Y年n月');
  }

  function render(){
    $html = [];
    $html[] = '<div class="calendar text-center">';
    $html[] =   '<table class="table">';
    $html[] =     '<thead>';
    $html[] =       '<tr>';
    $html[] =         '<th class="border">月</th>';
    $html[] =         '<th class="border">火</th>';
    $html[] =         '<th class="border">水</th>';
    $html[] =         '<th class="border">木</th>';
    $html[] =         '<th class="border">金</th>';
    $html[] =         '<th class="border day-sat">土</th>';
    $html[] =         '<th class="border day-sun">日</th>';
    $html[] =       '</tr>';
    $html[] =     '</thead>';
    $html[] = '<tbody>';
    $weeks = $this->getWeeks();
    foreach($weeks as $week){
      $html[] = '<tr class="'.$week->getClassName().'">';
      
      $days = $week->getDays();
      foreach($days as $day){
        $startDay = $this->carbon->copy()->format("Y-m-01");
        $toDay = $this->carbon->copy()->format("Y-m-d");
        
        if($startDay <= $day->everyDay() && $toDay >= $day->everyDay()){
          $html[] = '<td class="past-day border '.$day->getClassName().'">';
        }else{
          $html[] = '<td class="border '.$day->getClassName().'">';
        }
        $html[] = $day->render();
        
        // 予約状態のチェック
        // $day->everyDay() は現在のカレンダーの日付
        // $day->authReserveDay() は、現在ログインしているユーザーが予約している日付のリスト。
        // 予約がある日
        if(in_array($day->everyDay(), $day->authReserveDay())){
          $reservePart = $day->authReserveDate($day->everyDay())->first()->setting_part;
          if($reservePart == 1){
            $reservePart = "リモ1部";
          }else if($reservePart == 2){
            $reservePart = "リモ2部";
          }else if($reservePart == 3){
            $reservePart = "リモ3部";
          }
          // 12/15　
          // 7３行目の処理の目的:この条件により$day->everyDay()が
          // $startDayから$toDayの範囲内の日付であるかどうかを判定している
          
          // $startDay: 日付範囲の開始日を表している変数
          // $toDay: 日付範囲の終了日を表している変数
          // $day->everyDay(): 現在の日付（または特定の繰り返し日）を取得するメソッド
          
          // $startDay <= $day->everyDay():
          // ->現在の日付（$day->everyDay()）が日付範囲の開始日よりも後か、または等しい
          // $toDay >= $day->everyDay():
          // ->現在の日付（$day->everyDay()）が日付範囲の終了日よりも前か、または等しい
          
          // 12/15　条件が成立した場合の処理
          if($startDay <= $day->everyDay() && $toDay >= $day->everyDay()){
            $reserve = $day->authReserveDate($day->everyDay())->first();
            $html[] = '<p class="m-auto p-0 w-75 closed-part" style="font-size:16px">リモ' . $reserve->setting_part  . '部</p>';
            $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
          }else{
            // 予約データを取得する例 (配列を仮定)
            $date = $day->everyDay(); // 予約日
            $reserve = $day->authReserveDate($date)->first();

            if($reserve) {
              $part = $reserve->setting_part;
              $reservations = $day->authReserveDate($day->everyDay()); // 予約データを取得

              // キャンセルボタン
              foreach ($reservations as $reservation) {
                $html[] = '<button type="button" class="btn btn-danger p-0 w-75" data-bs-toggle="modal" data-bs-target= #cancelModal 
                          data-reservation-id="' . htmlspecialchars($reservation->id, ENT_QUOTES, 'UTF-8') . '"
                          data-date="'  . htmlspecialchars($reservation->setting_reserve, ENT_QUOTES, 'UTF-8') .'"
                          data-part="' . htmlspecialchars($reserve->setting_part, ENT_QUOTES, 'UTF-8') . '"
                          style="font-size:12px">'. htmlspecialchars($reservePart, ENT_QUOTES, 'UTF-8') .'</button>';
              }
              $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
            }

            // モーダルを追加
            $html[] = '<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">';
            $html[] = '  <div class="modal-dialog">';
            $html[] = '    <div class="modal-content">';
            $html[] = '      <div class="modal-body">';
            $html[] = '        <p>予約日:</p>';
            $html[] = '        <p>時間:リモ 部</p>';
            $html[] = '        <p>上記の予約をキャンセルしてもよろしいですか？</p>';
            $html[] = '      </div>';

            $reservations = $day->authReserveDate($day->everyDay()); // 予約データを取得
            // $reservations = Auth::user()->reserveSettings()->get();
            // dd($reservations);
            
            foreach ($reservations as $reservation) {
              $reservation_id = $reservation->id;
              // 予約情報を取得
              $reserved_user = \DB::table('reserve_setting_users')
                  ->where('reserve_setting_id', $reservation_id)
                  ->get();
              
              $reservation_user_id = optional($reserved_user)->user_id ?? $reservation->user_id;

              $html[] = '      <div class="modal-footer justify-content-between">';
              $html[] = '        <button type="button" class="btn btn-secondary bg-primary" data-bs-dismiss="modal">閉じる</button>';


              // dd($reservation_user_id);
              // dd($reservation);
              // フォームを追加
              $html[] = '         <form action="/delete/calendar" method="POST" onsubmit="return confirm(\'本当にキャンセルしますか？\');">';
              $html[] = '           <input type="hidden" name="_token" value="' . csrf_token() . '">'; // CSRF対策
              $html[] = '           <input type="hidden" id="reservationIdInput" name="reservation_id" >';
              $html[] = '           <input type="hidden" id="reservationUserIdInput" name="reservation_user_id">';
              $html[] = '           <button type="submit" class="btn btn-danger confirmCancel" data-reservation-id="' . $reservation_id . '" data-reservation-user-id="' . strval($reservation_user_id) . '">キャンセルする</button>'; //フォームタグで設定するか　aタグで設定するか
              $html[] = '         </form>';
              $html[] = '      </div>';
            }

            $html[] = '    </div>';
            $html[] = '  </div>';
            $html[] = '</div>';
            $html[] = '<script>
          </script>';            
          }
        }else{ //予約がない日->CalendarWeekDayクラスのselectPart()メソッド
          $html[] = $day->selectPart($day->everyDay());
        }
        $html[] = $day->getDate();
        $html[] = '</td>';
      }
      $html[] = '</tr>';
    }
    $html[] = '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>';
    $html[] = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';
    $html[] = '<script>
    // 1. ページが読み込まれたら実行する
    $(document).ready(function () {
      // 2. モーダル（ポップアップ画面）を取得
      const cancelModal = $("#cancelModal");
      // ・$("#cancelModal") は <div id="cancelModal"> というモーダルを取得
      // ・cancelModal という変数に保存して、後で使いやすくする->「キャンセル確認のポップアップ」 のこと
      
      // 3. モーダルが開かれる前に予約情報をセット
      cancelModal.on("show.bs.modal", function (event) {
        
      // 4. モーダルを開いたボタンの情報を取得
      const button = $(event.relatedTarget); // トリガーとなったボタン
      // event.relatedTarget は、モーダルを開いたボタンのこと
      
      const date = button.data("date"); // data-date 属性の値を取得(予約日)
      const part = button.data("part"); // data-part 属性の値を取得(予約時間)
      
      const reservationId = button.data("reservation-id");
      // キャンセルボタン(button)の data-reservation-id 属性の値を取得する(予約ID)
      const reservationUserId = button.attr("data-reservation-user-id");
      console.log("取得した予約ユーザーID:", reservationUserId);
        //キャンセルボタン(button)の data-reservation-user-id の値を取得する(ユーザーID)
        // ->なぜこの情報を取得するのか？ 「どの予約をキャンセルするのか」「どのユーザーがキャンセルするのか」サーバーに伝えるため

        console.log("event.relatedTarget:", event.relatedTarget);
        console.log("ボタン情報:", button);

        console.log("モーダルを開いた予約ID:", reservationId);
        console.log("取得した予約ユーザーID (attr使用):", reservationUserId);
        console.log("取得した予約ユーザーID:", reservationUserId);
        console.log("data-date:", date);
        console.log("data-part:", part);

        // 5. モーダルの内容を書き換える
        cancelModal.find(".modal-body p:nth-child(1)").text("予約日: " + date);
        cancelModal.find(".modal-body p:nth-child(2)").text("時間: " + part + "部");
        
        // 6. フォームの 「hidden input」に予約情報をセット
        // 「フォーム内の隠し入力欄（hidden input）に値をセットする処理」
        $("#reservationIdInput").val(reservationId);
        // ・$("#reservationIdInput")-> id="reservationIdInput"の要素(inputタグ)を取得する
        // ・.val(reservationId)-><input>の現在の入力値(value)を「reservationId」にセットする
        $("#reservationUserIdInput").val(reservationUserId);
        // ・$("#reservationUserIdInput")-> id="reservationUserIdInput"の要素(inputタグ)を取得
        // ・.val(reservationUserId)-><input>の現在の入力値(value)を「reservationUserId」にセットする

        // 7. キャンセルボタンに予約IDをセット
        cancelModal.find(".confirmCancel").attr("data-reservation-id", reservationId);
        });
        // ①cancelModal の中にあるconfirmCancelクラスを持つ要素を探す
        // ②対象要素の data-reservation-id 属性の値をreservationId に設定
        // data-reservation-id を設定

        // 8. キャンセルボタンがクリックされたらフォーム送信
        $(document).on("click", ".confirmCancel", function () {        
          let reservationId = $(this).data("reservation-id"); // ボタンから予約IDを取得

          console.log("選択した予約ID:", reservationId);
          console.log("フォームにセットした予約ID:", $("#reservationIdInput").val());

          // フォーム送信（ページリロードあり）
          console.log("フォーム送信前の予約ID:", $("#reservationIdInput").val());
          $("#cancelForm").submit();
      });
    });
    </script>';
        
    $html[] = '</tbody>';
    $html[] = '</table>';
    $html[] = '</div>';
    $html[] = '    
    ';
    $html[] = '<form action="/reserve/calendar" method="post" id="reserveParts">'.csrf_field().'</form>';
    $html[] = '<form action="/delete/calendar" method="post" id="deleteParts">'.csrf_field().'</form>';

    return implode('', $html);
  }

  protected function getWeeks(){
    $weeks = [];
    $firstDay = $this->carbon->copy()->firstOfMonth();
    $lastDay = $this->carbon->copy()->lastOfMonth();
    $week = new CalendarWeek($firstDay->copy());
    $weeks[] = $week;
    $tmpDay = $firstDay->copy()->addDay(7)->startOfWeek();
    while($tmpDay->lte($lastDay)){
      $week = new CalendarWeek($tmpDay, count($weeks));
      $weeks[] = $week;
      $tmpDay->addDay(7);
    }
    return $weeks;
  }
}