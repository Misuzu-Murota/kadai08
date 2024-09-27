<!-- 全社結果表示 -->
<?php
// CSVファイルのパス
$csvFile = 'data.csv';

// CSVデータを配列に読み込む
$data = array();
$employees = array();  // 社員番号を保存する配列
if (($handle = fopen($csvFile, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
        // データの構造に基づいて整形
        $dateString = $row[0];  // CSVから取得した日付の文字列
        $employeeId = $row[1];  // 社員番号
        $impression = $row[7];  // 今の組織運営に対する印象
       
        // キャリアへの影響と解決状況の列位置を取得（右から数えてデータを取得する）
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

        $data[] = array(
            "date" => $formattedDate,
            "employeeId" => $employeeId,
            "impression" => $convertImpression($impression),
            "careerImpact" => $convertCareerImpact($careerImpact),
            "solve" => $convertSolve($solve)
        );

        // 社員番号をユニークに保存
        if (!in_array($employeeId, $employees)) {
            $employees[] = $employeeId;
        }
    }
    fclose($handle);
}

// JSON形式にエンコードしてJavaScriptに渡す
$jsonData = json_encode($data);
$jsonEmployees = json_encode($employees);
?>


<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/luxon@3.0.1/build/global/luxon.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-luxon@1.0.0"></script>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/sample.css">
    <title>全社回答結果まとめ</title>
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
<h1>全社結果</h1>
<div class="charts-container">
    <div class="chart-container">
        <canvas id="impressionChart" width="300" height="200" ></canvas>
    </div>
    <div class="chart-container">
        <canvas id="careerImpactChart" width="300" height="200" ></canvas>
    </div>
    <div class="chart-container">
        <canvas id="solveChart"  width="300" height="200"></canvas>
    </div>
</div>

<script>
// PHPからのJSONデータをJavaScriptに渡す
const csvData = <?php echo $jsonData; ?>;
const employeeIds = <?php echo $jsonEmployees; ?>;

// 日付の整理（全社員共通のラベルとして使用）
const uniqueDates = [...new Set(csvData.map(item => item.date))];

// 社員番号ごとの色を固定で割り当てる
const colorMap = {
    '111111': 'rgba(75, 192, 192, 1)',  // 社員111111の色
    '222222': 'rgba(192, 75, 75, 1)',   // 社員222222の色
    // 他の社員番号もここに追加可能
};

// データセット作成の関数
function createDataset(type) {
    return employeeIds.map(employeeId => {
        const employeeData = csvData.filter(item => item.employeeId === employeeId);
        const scores = uniqueDates.map(date => {
            const entry = employeeData.find(item => item.date === date);
            return entry ? entry[type] : null; // データがない場合はnullを設定
        });

        return {
            label: employeeId, // 凡例ラベルに社員番号のみ
            data: scores,
            fill: false,
            borderColor: colorMap[employeeId] || getRandomColor(), // 固定された色、存在しない場合はランダム
            tension: 0.1
        };
    });
}

// ランダムな色を生成する関数（未定義の社員番号のために）
function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

// グラフの描画関数
function drawChart(chartId, type, title) {
    const ctx = document.getElementById(chartId).getContext('2d');
    new Chart(ctx, {
        type: 'line', // 線グラフのタイプ
        data: {
            labels: uniqueDates, // X軸に日付を表示
            datasets: createDataset(type) // 各データセット
        },
        options: {
            maintainAspectRatio: false, // アスペクト比の維持を無効にする
            plugins: {
                title: {
                    display: true,
                    text: title, // グラフのタイトル
                    font: {
                        size: 14 // タイトルのフォントサイズ
                    },
                    padding: {
                        bottom: 10 // タイトルとグラフの間隔
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5, // Y軸の最大値を5に設定
                    ticks: {
                        stepSize: 1, // 1単位で表示
                        callback: function(value) {
                            return Number(value).toFixed(0); // 小数点を表示しない
                        }
                    },
                },
                x: {
                    type: 'time', // 時間スケールを使用
                    time: {
                        unit: 'day', // X軸を日単位で表示
                        tooltipFormat: 'yyyy-MM-dd', // ツールチップでの日付フォーマット
                        displayFormats: {
                            day: 'yyyy-MM-dd' // X軸のフォーマット
                        }
                    },
                }
            }
        }
    });
}

// 各グラフを描画
drawChart('impressionChart', 'impression', '今の組織運営に対する印象');
drawChart('careerImpactChart', 'careerImpact', 'キャリア影響');
drawChart('solveChart', 'solve', '課題解決意欲');
</script>

</body>
</html>
