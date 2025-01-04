@extends('layouts.sidebar')

<!-- /calendar/{date}/{part} -->

@section('content')
<div class="vh-100 d-flex" style="align-items:center; justify-content:center;">
  <div class="w-50 m-auto h-75">
    <p>
      <span>{{ $date }}日</span>
      <span class="ml-3">{{ $part }}部</span>
    </p>
    <div class="h-75 border">
      <table class="">
        <thead>
          <tr class="text-center">
            <th class="w-25">ID</th>
            <th class="w-25">名前</th>
            <th class="w-25"></th>
            <th class="w-25">予約場所</th>
          </tr>
        </thead>
        <tbody>
          <!-- 予約情報 $reservePersons をループで処理し
          その中に含まれる関連ユーザー情報をさらにループしてテーブルの行 (<tr>) として出力する仕組み -->
          @foreach($reservePersons as $reserve)
          <!-- 「users」のリレーションループ -->
          <!-- 「ReserveSettings」モデルのリレーション「users」を使って、予約に関連するユーザー情報を取得 -->
            @foreach($reserve->users as $user)
              <tr class="text-center">
                <td class="w-25">{{ $user->id }}</td>
                <td class="w-25">{{ $user->over_name }}</td>
                <td class="w-25">{{ $user->under_name}}</td>
                <!-- 予約場所が設定されていない場合は、デフォルト値として「リモート」を表示 -->
                <td class="w-25">{{ $reserve->location ?? 'リモート'}}</td>
              </tr>
            @endforeach
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection