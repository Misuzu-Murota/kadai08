<?php
// CSVファイルのパス
$csvFile = 'data.csv';

// 部署を取得
$busho = isset($_GET['busho']) ? $_GET['busho'] : '';
// var_dump($busho); Console.logと同じで画面に表示できる

if (!$busho) {
    die('部署が指定されていません。');
}

// CSVデータを配列に読み込む
$data = array();
$employeeData = array();

if (($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $dateString = $row[0];  // CSVから取得した日付の文字列
        $employeeId = $row[1];  // 社員番号
        $currentBusho = $row[4]; // 部署
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

        // 各項目ごとの変換関数
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
                    return 0;  // nullを返すことで後で処理する際にエラーを防ぐ
            }
        };

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

        if ($currentBusho === $busho) {
            if (!isset($employeeData[$employeeId])) {
                $employeeData[$employeeId] = array();
            }
            $employeeData[$employeeId][] = array(
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
$jsonData = json_encode($employeeData);
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
     <title><?php echo htmlspecialchars($busho); ?> 回答結果</title> <!-- タイトルに部署名を挿入 -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .chart-ichi {
    display: flex;
    justify-content: space-around; /* 各グラフを均等に配置 */
    align-items: flex-start;
    flex-wrap: wrap;
    gap: 20px; /* セクション間の隙間 */
}

.chart-container {
    flex: 1;
    min-width: 300px; /* 各グラフの最小幅 */
    max-width: 400px; /* 各グラフの最大幅 */
    height: 300px; /* 高さを指定 */
}
h2{
    font-size: 20px
}
    </style>
</head>
<body>
<h1><?php echo htmlspecialchars($busho); ?> 回答結果</h1> <!-- タイトル部分にも部署名を挿入 -->

    <div class= "chart-ichi">
    <div class="chart-container">
        <h2>今の組織に対する印象</h2>
        <canvas id="impression-chart"></canvas>
    </div>
    <div class="chart-container">
        <h2>キャリアへの影響度</h2>
        <canvas id="careerimpact-chart"></canvas>
    </div>
    <div class="chart-container">
        <h2>課題解決意欲</h2>
        <canvas id="solve-chart"></canvas>
    </div>
    </div>

<script>
$(document).ready(function() {
    var employeeData = <?php echo $jsonData; ?>;
    var allDates = new Set();
    var impressionData = {};
    var careerImpactData = {};
    var solveData = {};

    // 社員番号ごとの色を保存するオブジェクト
    var employeeColors = {};    

    // ランダムな色を生成
    function randomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }

    // 社員番号ごとの色を取得、未設定なら新たに生成して保存
    function getColorForEmployee(employeeId) {
        if (!employeeColors[employeeId]) {
            employeeColors[employeeId] = randomColor();
        }
        return employeeColors[employeeId];
    }    

    // 日付を収集
    $.each(employeeData, function(employeeId, data) {
        if (!data || data.length === 0) return; // データが空の場合はスキップ
        $.each(data, function(index, entry) {
            if (entry.date) {
                allDates.add(entry.date);
            }
        });
    });

    allDates = Array.from(allDates).sort(); // ソート済みの全日付

    $.each(allDates, function(_, date) {
        impressionData[date] = {};
        careerImpactData[date] = {};
        solveData[date] = {};
    });

    // 各社員のデータを整理
    $.each(employeeData, function(employeeId, data) {
        if (data.length === 0) return; // データが空の場合はスキップ

        // 各社員のデータを日付ごとに整理
        $.each(data, function(_, entry) {
            var date = entry.date;
            impressionData[date][employeeId] = entry.impression || 0;
            careerImpactData[date][employeeId] = entry.careerImpact || 0;
            solveData[date][employeeId] = entry.solve || 0;
        });
    });

    // 各データセットを構築
    var impressionDatasets = [];
    var careerImpactDatasets = [];
    var solveDatasets = [];

    $.each(employeeData, function(employeeId, data) {
        var employeeColor = getColorForEmployee(employeeId); // 社員番号ごとの色を取得
        console.log("color:",employeeColor)
        var impressionDataset = {
            label:  employeeId,
            data: allDates.map(date => impressionData[date][employeeId] || 0),
            borderColor: employeeColor, // 固定色を使用
            backgroundColor: 'rgba(0,0,0,0)',
            fill: false
        };

        var careerImpactDataset = {
            label:  employeeId,
            data: allDates.map(date => careerImpactData[date][employeeId] || 0),
            borderColor: employeeColor, // 固定色を使用
            backgroundColor: 'rgba(0,0,0,0)',
            fill: false
        };

        var solveDataset = {
            label: employeeId,
            data: allDates.map(date => solveData[date][employeeId] || 0),
            borderColor: employeeColor, // 固定色を使用
            backgroundColor: 'rgba(0,0,0,0)',
            fill: false
        };

        impressionDatasets.push(impressionDataset);
        careerImpactDatasets.push(careerImpactDataset);
        solveDatasets.push(solveDataset);
    });

    // グラフ描画
    var ctxImpression = document.getElementById('impression-chart').getContext('2d');
    new Chart(ctxImpression, {
        type: 'line',
        data: {
            labels: allDates, // 日付の配列
            datasets: impressionDatasets
        },
        options: {
            maintainAspectRatio: false, // アスペクト比を固定しない
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day'
                    },
                    title: {
                        display: true,
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                    }
                }
            }
        }
    });

    var ctxCareerImpact = document.getElementById('careerimpact-chart').getContext('2d');
    new Chart(ctxCareerImpact, {
        type: 'line',
        data: {
            labels: allDates, // 日付の配列
            datasets: careerImpactDatasets
        },
        options: {
            maintainAspectRatio: false, // アスペクト比を固定しない
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day'
                    },
                    title: {
                        display: true,
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                    }
                }
            }
        }
    });

    var ctxSolve = document.getElementById('solve-chart').getContext('2d');
    new Chart(ctxSolve, {
        type: 'line',
        data: {
            labels: allDates, // 日付の配列
            datasets: solveDatasets
        },
        options: {
            maintainAspectRatio: false, // アスペクト比を固定しない
                       scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'day'
                    },
                    title: {
                        display: true,
                    }
                },
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1
                    },
                    title: {
                        display: true,
                    }
                }
            }
        }
    });

    // ランダムな色を生成
    function randomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
    }
});
</script>
</body>
</html>
