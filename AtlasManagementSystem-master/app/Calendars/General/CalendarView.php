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
    $html[] = '<table class="table">';
    $html[] = '<thead>';
    $html[] = '<tr>';
    $html[] = '<th>月</th>';
    $html[] = '<th>火</th>';
    $html[] = '<th>水</th>';
    $html[] = '<th>木</th>';
    $html[] = '<th>金</th>';
    $html[] = '<th>土</th>';
    $html[] = '<th>日</th>';
    $html[] = '</tr>';
    $html[] = '</thead>';
    $html[] = '<tbody>';
    $weeks = $this->getWeeks();
    foreach($weeks as $week){
      $html[] = '<tr class="'.$week->getClassName().'">';
      
      $days = $week->getDays();
      foreach($days as $day){
        $startDay = $this->carbon->copy()->format("Y-m-01");
        $toDay = $this->carbon->copy()->format("Y-m-d");
        
        if($startDay <= $day->everyDay() && $toDay >= $day->everyDay()){
          $html[] = '<td class="past-day border">';
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
            $html[] = '<p class="m-auto p-0 w-75" style="font-size:16px">リモ' . $reserve->setting_part  . '部</p>';
            $html[] = '<input type="hidden" name="getPart[]" value="" form="reserveParts">';
          }else{
            // 予約データを取得する例 (配列を仮定)
            $date = $day->everyDay(); // 予約日
            $reserve = $day->authReserveDate($date)->first();

            if($reserve) {
              $part = $reserve->setting_part;
              // キャンセルボタン
              $html[] = '<button type="button" class="btn btn-danger p-0 w-75" data-bs-toggle="modal" data-bs-target= #cancelModal data-date="'  . $day->authReserveDate($day->everyDay())->first()->setting_reserve .'" data-part="' . htmlspecialchars($reserve->setting_part, ENT_QUOTES, 'UTF-8') . '" style="font-size:12px">'. $reservePart .'</button>';
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
            $html[] = '      <div class="modal-footer justify-content-between">';
            $html[] = '        <button type="button" class="btn btn-secondary bg-primary" data-bs-dismiss="modal">閉じる</button>';
            $html[] = '        <button type="button" class="btn btn-danger" id="confirmCancel" data-reservation-id="' . $day->authReserveDate($day->everyDay())->first()->id . '">キャンセルする</button>';
            $html[] = '      </div>';
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
    $html[] = '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>'; // ① jQuery を先に読み込む
    $html[] = '<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>';
    $html[] = '<script>
    $(document).ready(function () {
      // ①ページ読み込みが完了した時に実行される処理
      const cancelModal = $("#cancelModal");
      // 取得した要素を"cancelModal"という変数に保存

      // ②モーダルが表示される直前のイベントリスナーを設定
      cancelModal.on("show.bs.modal", function (event) {
        // ③トリガーボタンの情報を取得
        const button = $(event.relatedTarget); // トリガーとなったボタン
        const date = button.data("date"); // data-date 属性の値を取得
        const part = button.data("part"); // data-part 属性の値を取得
        const reservationId = button.data("reservation-id"); // data-reservation-id 属性の値を取得

        // ④モーダルの内容を更新
        cancelModal.find(".modal-body p:nth-child(1)").text("予約日: " + date);
        cancelModal.find(".modal-body p:nth-child(2)").text("時間: " + part + "部");

        // ⑤キャンセルボタンに予約IDを設定
        $("#confirmCancel").data("reservation-id", reservationId); // data-reservation-id を設定
      });

      // ⑥キャンセルボタンがクリックされたときの処理
      $("#confirmCancel").on("click" , function() {
        const reservationId = $(this).data("reservation-id");

        // ⑦AJAXでキャンセルリクエストを送信
        // AJAXという仕組みを用いてページを再読み込みせずにサーバーと通信
        $.ajax({
          url: "/delete/calendar", //キャンセル処理を実行するサーバー
          // ボタンをクリックするとこのファイルにデータが送信される
          type: "POST",
          data: { 
            reservation_id: reservationId, //送信するデータ(予約ID)を指定
          },
          success: function(response) { //キャンセルが成功した時の処理
            console.log("サーバーの応答:", response);

            // 成功したらモーダルを閉じてリロード
            $("#cancelModal").modal("hide");
            alert("予約がキャンセルされました");
            location.reload(); // ページをリロードして反映
          }
        });
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