<!-- 社員情報一覧 -->
<?php
//1.  DB接続します
try {
  //Password:MAMP='root',XAMPP=''
  $pdo = new PDO('mysql:dbname=member_information;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
  exit('DB_CONNECT'.$e->getMessage());
}

//２．データ登録SQL作成
$sql = "SELECT * FROM allmembers;";

$stmt = $pdo->prepare($sql);
$status = $stmt->execute(); //True or false

//３．データ表示
// $view=""; //無視
if($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);
}

//全データ取得
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
//JSONに値を渡す場合に使う
$json = json_encode($values,JSON_UNESCAPED_UNICODE);

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>社員情報一覧</title>
<link rel="stylesheet" href="css/range.css">
<style>div{padding: 10px;font-size:16px;}</style>
</head>
<body id="main">
<!-- Head[Start] -->
<header>
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
      <a class="navbar-brand" href="index2.php">社員情報登録</a>
      </div>
    </div>
  </nav>
</header>
<!-- Head[End] -->


<!-- Main[Start] -->
<div>
    <div class="container jumbotron">
<table class="styled-table">
<thead>
        <tr>
            <th>社員番号</th>
            <th>名前</th>
            <th>性別</th>
            <th>年齢</th>
            <th>部署</th>
            <th>役職</th>
            <th>入社年</th>
            <th>登録日</th>
        </tr>
    </thead>
    <tbody>



<?php foreach($values as $value){ ?>
  <tr>
  <td><?=$value["shainno"]?></td>
  <td><?=$value["name"]?></td>
  <td><?=$value["gender"]?></td>
  <td><?=$value["age"]?></td>
  <td><?=$value["busho"]?></td>
  <td><?=$value["position"]?></td>
  <td><?=$value["year"]?></td>
  <td><?= date('Y年m月d日 H:i', strtotime($value["indate"])) ?></td>
  </tr>
<?php } ?>
</tbody>
</table> 

</div>
</div>
<!-- Main[End] -->


<script>
  //JSON受け取り
  const a = '<?php echo $json; ?>';
  console.log(JSON.parse(a));
  
</script>
</body>
</html>
