<!-- 社員情報登録 -->
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>社員情報登録</title>
  <style>div{padding: 10px;font-size:16px;}</style>
</head>
<body>

<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
    <div class="navbar-header"><a class="navbar-brand" href="select.php">社員情報一覧</a></div>
    </div>
  </nav>
</header>
<!-- Head[End] -->

<!-- Main[Start] -->
<form method="post" action="insert.php">
  <div class="jumbotron">
   <fieldset>
    <legend>社員情報登録</legend>
     <label>社員番号：<input type="number" name="shainno" id="shainno" minlength="6" maxlength="6" pattern="\d{6}"></label><br>
     <label>名前：<input type="text" name="name"><br>
     <label>性別：<select name="gender" id="gender"></label><br>
        <option value="">選択してください</option>
        <option value="男性">男性</option>
        <option value="女性">女性</option>
        <option value="その他">その他</option>
       </select><br>
     <label>年齢： <input type="number" name="age" id="age" min="18" max="100"></label><br>
     <label>部署：<select name="busho" id="busho"></label>
        <option value="">選択してください</option>
        <option value="営業部">営業部</option>
        <option value="人事部">人事部</option>
        <option value="IT部">IT部</option>
        </select><br>
     <label>役職： <select name="position" id="position"></label>
       <option value="">選択してください</option>
       <option value="マネージャー">マネージャー</option>
       <option value="一般職">一般職</option>
       <option value="役員">役員</option>
        </select><br>
     <label>入社年：<input type="number" name="year" minlength="4" maxlength="4" pattern="\d{4}"></label><br>
     <input type="submit" value="送信">
    </fieldset>
  </div>
</form>
<!-- Main[End] -->


</body>
</html>
