$(function () {
    $(document).on('click', '.other_role', function () {
      $('.select_teacher').removeClass('d-none');
    });
    $(document).on('click', '.admin_role', function () {
      $('.select_teacher').addClass('d-none');
    });
  });

  $(document).on('click keyup change', function () {
    var over_name = $('.over_name').val().length;
    if (over_name >= 1) {
      $('.over_name').addClass('success_name');
    } else {
      $('.over_name').removeClass('success_name');
    }

    var over_name_kana = $('.over_name_kana').val().length;
    if (over_name_kana >= 1) {
      $('.over_name_kana').addClass('success_name_kana');
    } else {
      $('.over_name_kana').removeClass('success_name_kana');
    }

    var under_name = $('.under_name').val().length;
    if (under_name >= 1) {
      $('.under_name').addClass('success_under_name');
    } else {
      $('.under_name').removeClass('success_under_name');
    }

    var under_name_kana = $('.under_name_kana').val().length;
    if (under_name_kana >= 1) {
      $('.under_name_kana').addClass('success_under_name_kana');
    } else {
      $('.under_name_kana').removeClass('success_under_name_kana');
    }

    var mail_address = $('.mail_address').val().length;
    if (mail_address >= 1) {
      $('.mail_address').addClass('success_mail_address');
    } else {
      $('.mail_address').removeClass('success_mail_address');
    }

    var password = $('.password').val().length;
    if (password >= 1) {
      $('.password').addClass('success_password');
    } else {
      $('.password').removeClass('success_password');
    }

    var password_confirm = $('.password_confirmation').val().length;
    if (password_confirm >= 1) {
      $('.password_confirmation').addClass('success_password_confirm');
    } else {
      $('.password_confirmation').removeClass('success_password_confirm');
    }

    var sex = $('input:radio[name="sex"]:checked').val();
    if (sex > 0) {
      $('.sex').addClass('success_sex');
    } else {
      $('.sex').removeClass('success_sex');
    }

    var old_year = $('.old_year').val();
    if (old_year !== 'none') {
      $('.old_year').addClass('success_year');
    } else {
      $('.old_year').removeClass('success_year');
    }

    var old_month = $('.old_month').val();
    if (old_month !== 'none') {
      $('.old_month').addClass('success_month');
    } else {
      $('.old_month').removeClass('success_month');
    }

    var old_day = $('.old_day').val();
    if (old_day !== 'none') {
      $('.old_day').addClass('success_day');
    } else {
      $('.old_day').removeClass('success_day');
    }

    var role = $('input:radio[name="role"]:checked').val();
    if (role > 0) {
      $('.role').addClass('success_role');
    } else {
      $('.role').removeClass('success_role');
    }

    if ($('.over_name').hasClass('success_name') && $('.over_name_kana').hasClass('success_name_kana') && $('.under_name').hasClass('success_under_name') && $('.under_name_kana').hasClass('success_under_name_kana') && $('.mail_address').hasClass('success_mail_address') && $('.password').hasClass('success_password') && $('.password_confirmation').hasClass('success_password_confirm') && $('.sex').hasClass('success_sex') && $('.old_year').hasClass('success_year') && $('.old_month').hasClass('success_month') && $('.old_day').hasClass('success_day') && $('.role').hasClass('success_role')) {
      $('.register_btn').prop('disabled', false);
    } else {
      $('.register_btn').prop('disabled', true);
    }
  });

  // // 新規登録
  // document.addEventListener("DOMContentLoaded", function () {
  //   const form = document.querySelector("form");
  //   const submitButton = document.querySelector(".register_btn");

  //   form.addEventListener("input", function () {
  //     // すべての必須項目が入力されているかチェック
  //     const isFormValid = form.checkValidity();
  //     submitButton.disabled = !isFormValid; // フォームが有効ならボタンを有効化
  //   });
  // });

  // 新規登録 非活性設定
    document.addEventListener("DOMContentLoaded", function () {
      const form = document.querySelector("form");
      // form:ページ内の<form>要素を取得
      const submitButton = document.querySelector(".register_btn");
      // submitButton:クラス名「.register_btn」を持つボタンを取得

      // 初期状態でボタンを無効化
      submitButton.disabled = true;
      // trueに設定しておくとボタンが押せなくなる

      form.addEventListener("input", function () {
          // すべての必須項目が入力されているかチェック
          submitButton.disabled = !form.checkValidity();
          // form.checkValidity():フォーム検証メソッド
          // フォーム内のすべての要素(<input>など)をチェック
      });
  });


  


  
  //  掲示板　カテゴリー検索
  $(function () {
    // タイトルをクリックすると
  $('.category-menu-item-btn').on("click", function () {
    // クリックした次の要素(コンテンツ)を開閉
      $(this).next().slideToggle(300);
    // タイトルにopenクラスを付け外しして矢印の向きを変更
    $(this).toggleClass("open", 300);
    });
  });

  // ユーザー一覧　検索条件の追加
    document.addEventListener("DOMContentLoaded", function() {
      // ・HTMLの読み込みが完了したタイミングでFubctionないの処理を実行
      // ・「DOMのコンテンツが読み込まれた時」に発火するイベント
      // ・基本的な使い方「要素.addEventListener('イベントの種類',関数)」
      const span = document.querySelector('.add_search_conditions');
      // ・document.querySelectorとは「指定したCSSセレクタに一致する最初の要素を取得」
      // ・取得した要素を「span」という変数に格納
      if (span) {
        // ・spanに要素が正常に取得できたかチェック
          span.addEventListener('click', function() {
            // ・spanにclickイベントリスナーを追加
            // ・spanがクリックされたら、function()内の処理を実行
            this.classList.toggle('open');
            // ・クリックされた要素(span)にopenクラスを追加/削除(トグル)
            // ・thisは、イベント発火元のspanを指す
            // ・classList.toggle() とは？
            // ①指定したクラスがない場合 → 追加
            // ②指定したクラスがある場合 → 削除
        });
      }
  });
  
    document.addEventListener('DOMContentLoaded', function() {
      const span = document.querySelector('.subject_edit_btn');

      if (span) {
        span.addEventListener('click', function() {
            this.classList.toggle('open'); // クリック時にopenクラスをトグル
        });
      }
  });
