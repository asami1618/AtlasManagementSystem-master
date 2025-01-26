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

    public function delete(Request $request)
    {
        // リクエストから予約IDを取得
        $reservationId = $request->input('reservation_id');

        // 予約IDに関連する予約を取得
        $reservation = ReserveSettings::find($reservationId);
    
        if ($reservation) {
            // 中間テーブルから関連を削除
            $user = auth()->user();
            $user->reserveSettings()->detach($reservationId);
            // 予約データを削除
            $reservation->delete(); // ここで予約レコード自体を削除
        }
        // キャンセル成功後、元のページにリダイレクト
        return redirect()->back();
    }
}