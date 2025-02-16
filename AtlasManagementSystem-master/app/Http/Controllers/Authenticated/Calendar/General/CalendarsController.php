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
    //Request $requestは　ユーザーから送られてきたデータ(予約IDなど)を取得する
    // 予約IDを取得
    $reservation_id = $request->input('reservation_id');
    // リクエストから「予約ID」を取得する


    if ($reservation_id) {
        // データベースから予約を削除
        $deleted = ReserveSettings::where('id', $reservation_id)->delete();
    }
        
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