<?php

namespace App\Http\Controllers\Authenticated\Calendar\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendars\General\CalendarView;
use App\Models\Calendars\ReserveSettings;
use App\Models\Calendars\Calendar;
use App\Models\USers\User;
use Auth;
use DB;

class CalendarsController extends Controller
{
    public function show(){
        //カレンダーを生成する "CalendarView" 
        // "CalendarView" を　"ビュー (authenticated.calendar.general.calendar) "に渡して表示。
        $calendar = new CalendarView(time());
        return view('authenticated.calendar.general.calendar', compact('calendar'));
    }

    public function reserve(Request $request){
        DB::beginTransaction();
        try{
            $getPart = $request->getPart;
            $getDate = $request->getData;
            // dd($getPart,$getDate);
            // 12/22　array_combineとは
            // ->連想配列(キーと値のペア)を作成するphpの関数
            $reserveDays = array_filter(array_combine($getDate, $getPart));
            foreach($reserveDays as $key => $value){
                $reserve_settings = ReserveSettings::where('setting_reserve', $key)->where('setting_part', $value)->first();
                $reserve_settings->decrement('limit_users');
                $reserve_settings->users()->attach(Auth::id());
            }
            DB::commit();
        }catch(\Exception $e){
            DB::rollback();
        }
        return redirect()->route('calendar.general.show', ['user_id' => Auth::id()]);
    }

    public function delete(Request $request){
    // ・Request $request　はユーザーから送信されたデータを受け取るための変数
    // ・「予約キャンセル」のボタンを押した時に送られるデータ(予約ID)を取得できる

        // ① ユーザーから送信されたデータ（予約ID）を受け取る
        $reservation_id = $request->input('reservation_id');
        // ・input('reservation_id')は、ユーザーが送信したreservation_id（予約ID）を取得するための関数
        // 。input('reservation_id') は、フォームのhidden入力フィールドなどから送信された値も取得する

        // ② ログイン中のユーザーのIDを取得
        $user_id = Auth::id();
        // ・Auth::id()を使って、現在ログインしているユーザーのIDを取得

        // ③ 予約IDとユーザーIDがあるかチェック
        if ($reservation_id && $user_id) {
        // ・この条件分岐は予約ID($reservation_id)と予約ID($user_id)が両方とも存在しているか確認している
        // ・&&(AND演算子)を使って、両方が存在する場合にのみ次の処理を実行する
        // ※ &&(AND演算子)は両方の条件が存在する(true)の場合にのみ処理を実行する

        // チェックしている項目
        // 1.$reservation_id -> 予約IDがあるか？(フォームから送信されたか)
        // 2.$user_id -> ログインしているユーザーのIDがあるか(ログインしているか)
        // この2つがどちらもtrue(存在する)なら、データベースを検索する処理に進む

        // ④ 予約があるかどうかをデータベースで確認
        $hasReservation = \DB::table('reserve_setting_users')
        // \DB::table('reserve_setting_users')
        // ・reserve_setting_usersテーブルを操作する準備をしている
        // ・\DB::tableは Laravelのデータベースを使うための書き方
            ->where('reserve_setting_id', $reservation_id)
            // ・reserve_setting_idがフォームから送られた$reservation_idと一致するデータを探す
            ->where('user_id', $user_id)
            // ・user_id が現在ログインしている$user_id と一致するデータを探す
            // ・つまり「このユーザーがこの予約を持っているか」をチェックしている
            ->exists();
            // ・検索したデータが存在すればtrue、なければfalseを返す
        }

        // ⑤ もし予約があるなら削除
        if ($hasReservation) {
        // ◇$hasReservation がtrueなら、削除処理を実行する
        // ・exists()で調べた結果、ユーザーがこの予約を持っている場合にtrueになる
        // ・false の場合、削除処理をスキップ

            // ⑥ 予約データを削除
            \DB::table('reserve_setting_users')
            // ・reserve_setting_users テーブルを操作する
            // ・Laravelのクエリビルダを使って、データベースのreserve_setting_usersテーブルを操作する準備をする
                ->where('reserve_setting_id', $reservation_id)
                // ・reserve_setting_id(予約ID)が$reservation_idに一致するデータを検索する
                ->where('user_id', $user_id)
                // user_id(ユーザーID)が$user_idに一致するデータを検索する
                ->delete();
                // ・条件に一致するデータを削除する

                // ⑦ 元の画面に戻る
                return redirect()->back();
                // ・削除が完了したら、元のページにリダイレクト(戻る)
            }

        // <処理の流れ　まとめ>
        // ① フォームから予約ID(reservation_id)を受け取る
        // ② ログイン中のユーザーのID(user_id)を取得
        // ③ 予約IDとユーザーIDがあるかチェック
        // ④ ユーザーがその予約を持っているかデータベースで確認
        // ⑤ 予約があるなら削除
        // ⑥ 削除が完了したら、元のページに戻る

        // 下記不要↓↓
        // // 予約を検索して削除(データベースから該当する予約を探す)
        // $reservation = ReserveSettings::find($reservationId);
        // // ReserveSettings は予約データを管理するテーブル(モデル)
        // // find($reservationId) は指定したreservation_idのデータを検索する
        
        // if ($reservation) {
            // // もし、$reservation にデータが入っていたら　予約が存在しキャンセル処理を行う
            //     auth()->user()->reserveSettings()->detach($reservationId); //中間テーブルの削除
            //     // ログインしているユーザーの予約との関連を削除(中間テーブルのデータを削除)
            //     // ・auth()->user():現在ログインしているユーザー
            //     // ・reserveSettings():ユーザーが持つ予約のリレーション
            //     // ・detach($reservationId):reservation_idのデータを中間テーブルから削除
            //     $reservation->delete(); 
            //     //予約データ自体を削除
            //     return response()->json(['message' => '予約がキャンセルされました'], 200);
            //     // ・response()->json:JSON形式でレスポンスを作る
            //     // ・['message' => '予約がキャンセルされました']:返すデータ
            //     // ・200:HTTPステータスコード(成功表す)
            
            // }
            
    }
}