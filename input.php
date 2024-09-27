<!-- 社員番号から情報を取得する -->
<?php
session_start(); // セッションを開始

//1.  DB接続します
try {
  //Password:MAMP='root',XAMPP=''
  $pdo = new PDO('mysql:dbname=member_information;charset=utf8;host=localhost','root','');
} catch (PDOException $e) {
  exit('DB_CONNECT'.$e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shainno'])) {
  $shainno = $_POST['shainno'];
  var_dump($shainno);

 // SQL文で該当する社員番号のデータを取得
$stmt = $pdo->prepare("SELECT * FROM allmembers WHERE shainno = :shainno");
$stmt->bindValue(':shainno', $shainno, PDO::PARAM_INT); // 型を指定してバインド

$stmt->execute();

 // 結果を取得
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if ($employee) {
    // データが存在する場合、フォームに反映させる
    $_SESSION['employee'] = $employee;
    $_SESSION['employee']['shainno'] = $shainno; // 社員番号もセッションに保存
    // 自動リダイレクトを追加
    header("Location: index.php?success=1"); // リダイレクト先のURLを指定
    exit(); // スクリプトの実行を終了
} else {
    echo "該当する社員が見つかりませんでした。";
    exit();
}
}
?>





<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    </script>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/sample.css">
    <title>社内課題抽出ツール</title>
</head>

<body>

<h1>書き込みしました。</h1>
<h2>./index.php を確認しましょう！</h2>

<ul>
<li><a href="index.php">戻る</a></li>
</ul>
</body>
</html>