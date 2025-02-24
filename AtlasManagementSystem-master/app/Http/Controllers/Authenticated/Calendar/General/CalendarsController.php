<?php

namespace App\Http\Controllers\Authenticated\Calendar\General;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Calendars\General\CalendarView;
use App\Models\Calendars\ReserveSettings;
use App\Models\Calendars\Calendar;
use App\Models\USers\User;
use Illuminate\Support\Facades\Log;
use Auth;
use DB;

class CalendarsController extends Controller
{
    public function show(Request $request){
        //カレンダーを生成する "CalendarView" 
        // "CalendarView" を　"ビュー (authenticated.calendar.general.calendar) "に渡して表示。
        $user_id = Auth::id(); // ログインユーザーのIDを取得
        $calendar = new CalendarView(time());

        // リクエストから予約情報を取得
        $reservation_id = $request->input('reservation_id', null);
        $reservation_user_id = $request->input('reservation_user_id', null);

        // 予約情報をViewに渡す
        return view('authenticated.calendar.general.calendar', compact('calendar', 'user_id', 'reservation_id', 'reservation_user_id'));
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
    
    public function delete(Request $request)
    {
        // Log::info('キャンセルリクエストを受信', ['reservation_id' => $request->reservation_id]);

        // dd($request->all());

        // dd($request->all()); // 送信されたデータをすべて確認
        // ・Request $request　はユーザーから送信されたデータを受け取るための変数
        // ・「予約キャンセル」のボタンを押した時に送られるデータ(予約ID)を取得できる
        
        // ① ユーザーから送信されたデータ（予約ID）を受け取る
        $reservation_user_id = intval($request->input('reservation_user_id')); 
        $reservation_id = intval($request->input('reservation_id'));

        $user_id = auth()->id();// ここでログインユーザーのIDを取得
        // dd(auth()->id());
        // dd($reservation_id);
        
        
        // dd($reservation_user_id, $reservation_id, $user_id);
        // ・input('reservation_id')は、ユーザーが送信したreservation_id（予約ID）を取得するための関数
        // ・input('reservation_id') は、フォームのhidden入力フィールドなどから送信された値も取得する
        
        // ② 予約IDとユーザーIDがあるかチェック
        $hasReservation = null;
        if ($reservation_id && $user_id) {
            // ・この条件分岐は予約ID($reservation_id)と予約ID($user_id)が両方とも存在しているか確認している
            
            // ④ 予約があるかどうかをデータベースで確認
            $hasReservation = \DB::table('reserve_setting_users')
            // ・reserve_setting_usersテーブルを操作する準備をしている
            // ・\DB::tableは Laravelのデータベースを使うための書き方
            ->where('id', intval($reservation_user_id)) 
            // ・予約のユニークIDで検索する
            ->where('user_id', intval($user_id)) // ログイン中のユーザーの予約を探す
            // ・reserve_setting_idがフォームから送られた$reservation_idと一致するデータを探す
            ->where('reserve_setting_id', intval($reservation_id)) // 予約のIDを指定            // ・user_id が現在ログインしている$user_id と一致するデータを探す
            // ・つまり「このユーザーがこの予約を持っているか」をチェックしている
            ->first();
            // ・検索したデータが存在すればtrue、なければfalseを返す
            // dd($hasReservation); // 結果を確認
            // dd(auth()->id());
            // dd($reservation_id);
            // dd($reservation_user_id, $reservation_id, auth()->id());
            
            // ⑤ もし予約があるなら削除
            if ($hasReservation) {
                // ◇$hasReservation がtrueなら、削除処理を実行する
                // ・exists()で調べた結果、ユーザーがこの予約を持っている場合にtrueになる
                // ・false の場合、削除処理をスキップ
                
                // 予約データを削除する前に、予約枠を元に戻す処理を追加
                // ⑥予約枠を元に戻す(該当する予約枠のの空き数を1つ増やす)
                \DB::table('reserve_settings')
                // 予約枠のテーブルを指定
                ->where('id', $reservation_id)
                // 'id'が$reservation_idに一致する1つの予約枠を探す
                ->increment('limit_users', 1);
                // 'limit_users'(空き枠数)の値を1つ増やす
                
                // input hidden に予約IDとユーザーIDを埋め込んでいるため、リクエストで取得できる
                $reserve_setting_id = $request->input('reserve_setting_id');
                $user_id = $request->input('user_id');
                
                \DB::table('reserve_setting_users')
                // ・reserve_setting_users テーブルを操作する
                // ・Laravelのクエリビルダを使って、データベースのreserve_setting_usersテーブルを操作する準備をする
                ->where('reserve_setting_id',  $reserve_setting_id)
                //・一つ目の枠を絞り込む
                ->where('user_id', $user_id)//ユーザーを絞り込む
                // ・user_id(ユーザーID)が$user_idに一致するデータを検索する
                ->delete();
                // ・条件に一致するデータを削除する
                
                // ⑧ 元の画面に戻る
                // 削除後にカレンダー画面へリダイレクト
                return redirect()->route('calendar.general.show', [
                    'reservation_id' => $reservation_id,
                    'reservation_user_id' => $reservation_user_id
                ]);
            }
        }
        return redirect()->back();
        // <処理の流れ　まとめ>
        // ① フォームから予約ID(reservation_id)を受け取る
        // ② ログイン中のユーザーのID(user_id)を取得
        // ③ 予約IDとユーザーIDがあるかチェック
        // ④ ユーザーがその予約を持っているかデータベースで確認
        // ⑤ 予約枠を元に戻す
        // ⑥ 予約があるなら削除
        // ⑦ 削除が完了したら、元のページに戻る
        
    }
}