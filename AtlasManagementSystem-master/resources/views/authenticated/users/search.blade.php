@extends('layouts.sidebar')

@section('content')
<div class="search_content w-100 border d-flex">
  <div class="reserve_users_area">
    @foreach($users as $user)
    <div class="border one_person">
      <div>
        <span class="users_title">ID : </span><span>{{ $user->id }}</span>
      </div>
      <div><span class="users_title">名前 : </span>
        <a href="{{ route('user.profile', ['id' => $user->id]) }}">
          <span>{{ $user->over_name }}</span>
          <span>{{ $user->under_name }}</span>
        </a>
      </div>
      <div>
        <span class="users_title">カナ : </span>
        <span>({{ $user->over_name_kana }}</span>
        <span>{{ $user->under_name_kana }})</span>
      </div>
      <div>
        @if($user->sex == 1)
        <span class="users_title">性別 : </span><span>男</span>
        @elseif($user->sex == 2)
        <span class="users_title">性別 : </span><span>女</span>
        @else
        <span class="users_title">性別 : </span><span>その他</span>
        @endif
      </div>
      <div>
        <span class="users_title">生年月日 : </span><span>{{ $user->birth_day }}</span>
      </div>
      <div>
        @if($user->role == 1)
        <span class="users_title">役職 : </span><span>教師(国語)</span>
        @elseif($user->role == 2)
        <span class="users_title">役職 : </span><span>教師(数学)</span>
        @elseif($user->role == 3)
        <span class="users_title">役職 : </span><span>講師(英語)</span>
        @else
        <span class="users_title">役職 : </span><span>生徒</span>
        @endif
      </div>
      
      <!-- 選択科目の表示 -->
      <!-- 50行目　UserモデルとSubjectモデルのリレーションを指す。
      Userモデルに　"subjects"という"belongsToMany"や"hasMany"のリレーションが定義されていることが前提。　-->
      <div>
        @if($user->role == 4)
        @foreach($user->subjects as $subject)
        <span class="users_title">選択科目 :</span><span>{{ $subject->subject }}</span>
        @endforeach
        @endif
      </div>
    </div>
    @endforeach
  </div>
  <div class="search_area w-25 border">
    <!-- ワード検索 -->
    <div>
      <div class="search_free_word_area">
        <p class="search_title">検索</p>
        <input type="text" class="free_word" name="keyword" placeholder="キーワードを検索" form="userSearchRequest">
      </div>

      <!-- カテゴリ選択 -->
      <div>
        <p class="caregory_title">カテゴリ</p>
        <select form="userSearchRequest" class="category_select" name="category">
          <option value="name">名前</option>
          <option value="id">社員ID</option>
        </select>
      </div>

      <!-- 並び替え選択 -->
      <div>
        <p class="updown_title">並び替え</p>
        <select name="updown" class="updown_select" form="userSearchRequest">
          <option value="ASC">昇順</option>
          <option value="DESC">降順</option>
        </select>
      </div>

      <!-- 検索条件の追加 -->
      <div class="add_search">
        <p class="m-0 search_conditions">
          <span class="add_search_conditions">検索条件の追加</span>
        </p>
        <div class="search_conditions_inner">
          <div>
            <p class="sex_title">性別</p>
            <span>男</span><input type="radio" name="sex" value="1" form="userSearchRequest">
            <span>女</span><input type="radio" name="sex" value="2" form="userSearchRequest">
            <span>その他</span><input type="radio" name="sex" value="3" form="userSearchRequest">
          </div>
          <div>
            <p class="role_title">権限</p>
            <select name="role" form="userSearchRequest" class="engineer">
              <option selected disabled>----</option>
              <option value="1">教師(国語)</option>
              <option value="2">教師(数学)</option>
              <option value="3">教師(英語)</option>
              <option value="4" class="">生徒</option>
            </select>
          </div>
          <div class="selected_engineer">
            <p class="subject_title">選択科目</p>
            @foreach($allSubjects as $subject)
                <input type="checkbox" id="subject_{{ $subject->id }}" name="subject[]" value="{{ $subject->id }}" form="userSearchRequest">
                <label for="subject_{{ $subject->id }}">{{ $subject->subject }}</label>
            @endforeach
            <!-- <span>国語</span><input type="checkbox" name="subject[]"  form="userSearchRequest">
            <span>数学</span><input type="checkbox" name="subject[]" form="userSearchRequest">
            <span>英語</span><input type="checkbox" name="subject[]" form="userSearchRequest"> -->
          </div>
        </div>
      </div>

      <!-- 検索ボタン -->
      <form action="{{ route('user.show') }}" method="get" id="userSearchRequest">
        <div class="users_search_area">
          <input type="submit" name="search_btn" class="users_search_btn btn btn-info" value="検索" form="userSearchRequest">
        </div>
        
        <!-- リセットボタン -->
        <div class="users_reset_area">
          <a href="#" onclick="resetForm(event)">リセット</a>
        </div>
      </form>
      <script>
        function resetForm(event) {
          event.preventDefault(); // デフォルトのリンク動作を無効化
          const form = document.getElementById('userSearchRequest');
          form.reset(); // フォームのリセット
        }
      </script>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </div>
  </div>
</div>
@endsection
