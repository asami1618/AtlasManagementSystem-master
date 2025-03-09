@extends('layouts.sidebar')
@section('content')
<!-- スクール枠登録(calendar.admin.setting)が動く -->
<div class="vh-100 pt-5">
  <div class="w-100">
    <div>
      <div class="calendar_setting_area w-75 m-auto pt-5 pb-5">
        <p class="calendar_title">{{ $calendar->getTitle() }}</p> 
        {!! $calendar->render() !!}
          <div class="adjust-table-btn m-auto text-right">
            <input type="submit" class="btn btn-primary" value="登録" form="reserveSetting" onclick="return confirm('登録してよろしいですか？')">
          </div>
      </div>
    </div>
  </div>
</div>
@endsection