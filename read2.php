<!-- 個人別詳細結果 -->
<?php
// CSVファイルのパス
$csvFile = 'data.csv';

// 社員番号を取得
$shainno = isset($_GET['shainno']) ? $_GET['shainno'] : '';

if (!$shainno) {
    die('社員番号が指定されていません。');
}

// CSVデータを配列に読み込む
$data = array();
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $dateString = $row[0];  // CSVから取得した日付の文字列
        $employeeId = $row[1];  // 社員番号
        $impression = $row[7];  // 今の組織運営に対する印象

        // キャリアへの影響と解決状況の列位置を取得
        $careerImpact = $row[count($row) - 2];  // 右から2つ目の列（課題影響）
        $solve = $row[count($row) - 1];  // 右から1つ目の列（課題解決意欲）

        // 日付の変換
        $date = DateTime::createFromFormat('Y年m月d日 H時i分', $dateString);
        if ($date === false) {
            echo "日付変換エラー: $dateString\n";
            continue; // 変換に失敗した場合はスキップ
        }
        $formattedDate = $date->format('Y-m-d');

        // 日付の変換
        $date = DateTime::createFromFormat('Y年m月d日 H時i分', $dateString);
        if ($date === false) {
            echo "日付変換エラー: $dateString\n";
            continue; // 変換に失敗した場合はスキップ
        }
        $formattedDate = $date->format('Y-m-d');

        // 各項目ごとの変換関数
        // 今の組織運営に対する印象の数値化
        $convertImpression = function($value) {
            switch ($value) {
                case 'うまく回っている':
                    return 4;
                case 'まあまあうまく回っている':
                    return 3;
                case 'あまりうまく回っていない':
                    return 2;
                case '全くうまくいっていない':
                    return 1;
                default:
                    return 0;
            }
        };
          // 課題影響の数値化
        $convertCareerImpact = function($value) {
            switch ($value) {
                case '大いに影響する':
                    return 4;
                case '多少影響する':
                    return 3;
                case 'あまり影響しない':
                    return 2;
                case '全く影響しない':
                    return 1;
                default:
                    return 0;
            }
        };
         // 課題解決意欲の数値化
        $convertSolve = function($value) {
            switch ($value) {
                case 'どんな形であっても改善に向けた活動をしたい':
                    return 4;
                case 'できる範囲で改善に向けた活動をしたい':
                    return 3;
                case '業務指示であれば、対応する':
                    return 2;
                case 'あまり気乗りしない':
                    return 1;
                default:
                    return 0;
            }
        };

        if ($employeeId === $shainno) {
            $data[] = array(
                "date" => $formattedDate,
                "impression" => $convertImpression($impression),
                "careerImpact" => $convertCareerImpact($careerImpact),
                "solve" => $convertSolve($solve)
            );
        }
    }
    fclose($handle);
}

// JSON形式にエンコードしてJavaScriptに渡す
$jsonData = json_encode($data);
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.0.0"></script>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/sample.css">
    <title>個人別詳細結果</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .charts-container {
            display: flex; /* 横並びにする */
            flex-wrap: wrap; /* 画面サイズによっては折り返す */
            gap: 20px; /* グラフ間の隙間 */
            width: 90%;
            height: 300px;
        }
    </style>
</head>
<body>
    <h1>個人別詳細結果</h1>
    <div class="charts-container">
     <div class="chart-container">
        <canvas id="impression-chart" width="300" height="200"></canvas>
     </div>
     <div class="chart-container">
        <canvas id="careerimpact-chart" width="300" height="200"></canvas>
     </div>
     <div class="chart-container">
        <canvas id="solve-chart" width="300" height="200"></canvas>
     </div>
    </div>
    <script>
    $(document).ready(function() {
        var data = <?php echo $jsonData; ?>;

        var labels = data.map(d => d.date);
        var impressions = data.map(d => d.impression);
        var careerImpacts = data.map(d => d.careerImpact);
        var solves = data.map(d => d.solve);

        // 今の組織に対する印象
        var ctx1 = document.getElementById('impression-chart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Impression',
                    data: impressions,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    fill: false
                }]
            },
            options: {
                maintainAspectRatio: false, // アスペクト比の維持を無効にする
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value; // 小数点を表示しない
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: '今の組織に対する印象'
                    }
                }
            }
        });

        // キャリアへの影響度
        var ctx2 = document.getElementById('careerimpact-chart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Career Impact',
                    data: careerImpacts,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    fill: false
                }]
            },
            options: {
                maintainAspectRatio: false, // アスペクト比の維持を無効にする
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value; // 小数点を表示しない
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'キャリアへの影響度'
                    }
                }
            }
        });

        // 解決意欲
        var ctx3 = document.getElementById('solve-chart').getContext('2d');
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Solve',
                    data: solves,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    fill: false
                }]
            },
            options: {
                maintainAspectRatio: false, // アスペクト比の維持を無効にする
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value; // 小数点を表示しない
                            }
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: '解決意欲'
                    }
                }
            }
        });
    });
    </script>
</body>
</html>