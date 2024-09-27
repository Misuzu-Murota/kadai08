<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $d = date("Y年m月d日 H時i分");
    $shainno = $_POST["shainno"] ?? '';
    $gender = $_POST["gender"] ?? '';
    $age = $_POST["age"] ?? '';
    $busho = $_POST["busho"] ?? '';
    $yakushoku = $_POST["yakushoku"] ?? '';
    $year = $_POST["year"] ?? '';

    $impression = $_POST["impression"] ?? '';
    $q1 = $_POST["q1"] ?? '';
    $options = [];
    for ($i = 1; $i <= 11; $i++) {
        if (isset($_POST["option$i"])) {
            $options[] = $_POST["option$i"];
        }
    }
    $q22 = $_POST["q22"] ?? '';
    $q23 = $_POST["q23"] ?? '';
    $careerimpact = $_POST["careerimpact"] ?? '';
    $solve = $_POST["solve"] ?? '';

    $c = ",";
    $str = $d. $c .$shainno . $c . $gender . $c . $age . $c . $busho . $c . $yakushoku . $c . $year . $c . $impression . $c . $q1 . $c . implode($c, $options) . $c . $q22 . $c . $q23 . $c . $careerimpact . $c . $solve;

    $file = fopen("data.csv", "a");
    if ($file) {
        fwrite($file, $str . "\n");
        fclose($file);
        header("Location: index.php");
        exit;
    } else {
        echo "ファイルのオープンに失敗しました。";
    }
}

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="importmap">
      {"imports": {
          "@google/generative-ai": "https://esm.run/@google/generative-ai"
        }
      }
    </script>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/sample.css">
    <title>社内課題抽出ツール</title>
</head>

<body>

<h1>書き込みしました。</h1>
<h2>./data.csv を確認しましょう！</h2>

<ul>
<li><a href="index.php">戻る</a></li>
</ul>
</body>
</html>