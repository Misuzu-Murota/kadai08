<!-- AIさんの相談/本画面 -->
<?php
session_start(); // セッションを開始

// 初期値を設定
$shainno = '';
$name = '';
$gender = '';
$age = '';
$busho = '';
$position = '';
$year = '';

// 入力がある場合、セッションからデータを取得
if (isset($_SESSION['employee'])) {
    $employee = $_SESSION['employee'];
    $shainno = $employee['shainno'];
    $name = $employee['name'];
    $gender = $employee['gender'];
    $age = $employee['age'];
    $busho = $employee['busho'];
    $position = $employee['position'];
    $year = $employee['year'];
}

?>

<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="importmap">
      {"imports": {
          "@Google Drive/generative-ai": "https://esm.run/@google/generative-ai"
        }
      }
      
    </script>
    <script>
      let isReset = false;
       $(document).ready(function () {
            $('#resetButton').on('click', function () {
                // 全ての入力項目をリセット
                $('input[type="text"], input[type="number"], textarea, select').val(''); // フォーム内外のすべての入力項目をクリア
                $('.clearable').empty(); // classがclearableの要素をクリア
                 // リセット状態を更新
                isReset = true;
            });
        });
    </script>
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/sample.css">
    <title>社内課題抽出ツール</title>
</head>
<body>
<div> <!--回答全体-->
  <div>
    <h1>Alさんによる組織のお悩み相談</h1>
    <p>これから以下質問に回答いただき、ご自身のこと及び組織に関する設問にご回答をお願いします</p>
  </div>
<div class="zentai" id="zentai">  <!--所属情報-->
  <h2>所属情報</h2>
  <div class="shozoku">
  
  <form action="input.php" method="post">
  <div>
    <label for="shainno">・社員番号(6桁)</label>
    <input type="number" name="shainno" id="shainno" minlength="6" maxlength="6" pattern="\d{6}" value="<?php echo htmlspecialchars($shainno); ?>">
    <span id="shainno-error" class="error-message" style="display:none;">社員番号は6桁で入力してください。</span>
    <button id="button0" type="submit">社員情報を取得</button>
    <button type="button" id="resetButton">リセット</button>
  </div>
  </form>

  <?php if (isset($_SESSION['employee'])): ?>
  <div>
    <label for="name">・名前</label>
    <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($employee['name']); ?>" >
    <span id="name-error" class="error-message" style="display:none;">名前を記入してください</span>
  </div>
  <div>
    <label for="gender">・性別</label>
    <input type="text" name="gender" id="gender" value="<?php echo htmlspecialchars($employee['gender']); ?>" >
    <span id="gender-error" class="error-message" style="display:none;">性別を選択してください</span>
  </div>
<div>
  <label for="age">・年齢</label>
  <input type="number" name="age" id="age" min="18" max="100" value="<?php echo htmlspecialchars($employee['age']); ?>" >
</div>
<div>
  <label for="busho">・部署</label>
  <input type="text" name="busho" id="busho" value="<?php echo htmlspecialchars($employee['busho']); ?>" >
  <span id="busho-error" class="error-message" style="display:none;">部署を選択してください</span>
</div>
<div>
  <label for="position">・役職</label>
  <input type="text" name="position" id="position" value="<?php echo htmlspecialchars($employee['position']); ?>" >
  <span id="yakushoku-error" class="error-message" style="display:none;">役職を選択してください</span>
</div>
<div>
  <label for="year">・入社年</label>
  <input type="number" name="year" id="year" min="1990" max="2024"  value="<?php echo htmlspecialchars($employee['year']); ?>" >
  <span id="year-error" class="error-message" style="display:none;">入社年を選択してください</span>
</div>
<?php else: ?>
    <p>社員情報が見つかりませんでした。</p>
<?php endif; ?>

</div>
<div> <!--所属組織に関する質問-->
    <h2>所属組織について</h2>
<div>  <!--Q1-1,Q1-2:今の組織運営に対する印象/なぜそう答えたか-->
<div> <!--今の組織運営に対する印象-->
  <label for="q1-1">Q1-1:今の組織運営に対する印象を教えてください</label>
    <select name="impression" id="impression">
     <option value="">選択してください</option>
     <option value="うまく回っている">うまく回っていると感じる</option>
     <option value="まあまあうまく回っている">まあまあうまく回っている</option>
     <option value="あまりうまく回っていない">あまりうまく回っていない</option>
     <option value="全くうまくいっていない">全くうまくいっていない</option>
    </select>
    <span id="impression-error" class="error-message" style="display:none;">選択してください</span>
</div>
<div> <!--今の組織運営に対する印象/なぜそう答えたか-->
  <label for="q1-2">Q1-2:Q1-1でなぜそう答えたか教えてください</label>
  <input type="text" name="q1" id="q1">
  <span id="q1-2-error" class="error-message" style="display:none;">記入してください</span>
</div>
</div>
<div> <!--Q2~3-->
  <div>  <!--Q2-1:課題だと感じていることか-->
    <label for="q2-1">Q2-1:今所属する組織他、会社全体における課題だと感じていることを教えてください（当てはまるものすべて）</label>
    <div class="checkbox-group">
      <div>
        <label for="option1">
        <input type="checkbox" id="option1" name="option1" value="業績"> 業績
        </label>
      </div>
      <div>
        <label for="option2">
        <input type="checkbox" id="option2" name="option2" value="新規雇用が難航していることによる人手不足"> 人手不足（新規採用難航）
        </label>
      </div>
      <div>
        <label for="option3">
        <input type="checkbox" id="option3" name="option3" value="退職しても補充がこないことによる人手不足"> 人手不足（離職率増加）
        </label>
      </div>
      <div>
        <label for="option4">
        <input type="checkbox" id="option4" name="option4" value="報酬制度"> 人事制度（報酬）
        </label>
      </div>
      <div>
        <label for="option5">
        <input type="checkbox" id="option5" name="option5" value="人事評価制度"> 人事制度（評価）
        </label>
      </div>
      <div>
        <label for="option6">
        <input type="checkbox" id="option6" name="option6" value="組織内の人間関係"> 組織内の人間関係
        </label>
      </div>
      <div>
        <label for="option7">
        <input type="checkbox" id="option7" name="option7" value="女性活躍"> 女性活躍
        </label>
      </div>
      <div>
        <label for="option8">
        <input type="checkbox" id="option8" name="option8" value="業務の役割分担や効率化"> 業務分担/効率化（DX化など）
        </label>
      </div>
      <div>
        <label for="option9">
        <input type="checkbox" id="option9" name="option9" value="組織の雰囲気"> 組織風土
        </label>
      </div>
      <div>
        <label for="option10">
        <input type="checkbox" id="option10" name="option10" value="働き方"> 働き方改革（残業/有給・育休取得他）
        </label>
      </div>
      <div>
        <label for="option11">
        <input type="checkbox" id="option11" name="option11" value="ない"> 特に課題と感じていることはない
        </label>
      </div>
        <span id="q2-1-error" class="error-message" style="display:none;">選択してください</span>
    </div>
  </div>
  <div>  <!--Q2-2:課題内容-->
    <label for="q2">Q2-2:なぜその回答を選んだのか具体的にどの点を課題と感じているか簡単に教えてください</label><br>
    <input type="text" name="q22" id="q22">
    <span id="q2-2-error" class="error-message" style="display:none;">記入してください</span>
  </div>
  <div>  <!--Q2-3:課題原因-->
    <label for="q2">Q2-3:課題の原因は何だと考えているか簡単に教えてください</label><br>
    <input type="text" name="q23" id="q23">
    <span id="q2-3-error" class="error-message" style="display:none;">記入してください</span>
  </div>
  <div>  <!--Q3-1:キャリア影響-->
    <label for="q3">Q3-1:今の課題はあなたの今後のキャリアに影響すると感じていますか</label><br>
    <select name="careerimpact" id="careerimpact">
     <option value="">選択してください</option>
     <option value="大いに影響する">転職も含めてとても影響する</option>
     <option value="多少影響する">多少は影響する</option>
     <option value="あまり影響しない">あまり影響しない</option>
     <option value="全く影響しない">全く影響しない</option>
    </select>
    <span id="careerimpact-error" class="error-message" style="display:none;">選択してください</span>
  </div>
  <div>  <!--Q3-2:課題解決意欲-->
    <label for="q3">Q3-2:今の課題をあなた自身が解決したいと思いますか</label><br>
    <select name="solve" id="solve">
     <option value="">選択してください</option>
     <option value="どんな形であっても改善に向けた活動をしたい">とてもそう思う（どんな形であっても積極的に対応したい）</option>
     <option value="できる範囲で改善に向けた活動をしたい">そう思う（業務指示であれば積極的に、有志であればできる範囲で関わりたい）</option>
     <option value="業務指示であれば、対応する">あまり思わない（業務指示であれば最低限対応する）</option>
     <option value="あまり気乗りしない">全く思わない（業務指示であっても、あまりやりたくない）</option>
    </select>
    <span id="solve-error" class="error-message" style="display:none;">選択してください</span>
  </div>
</div>   <!--Q2~3-->
<div class="button">
  <div>
   <button id="button1" type="submit">結果を表示する</button>
  </div>
  <div>
    <button id="button2">詳細結果</button>
  </div>
  <div>
    <button id="button3">所属組織結果</button>
  </div>
</div>

<div id="view" style="display: none;"></div>
<div id="preresults" style="display: none;">
  <h3>過去のアドバイス</h3>
  <div id="past-comments-container"></div>
</div>
</div>
<script>
$(document).ready(function() {
  console.log("jQuery is working!");
  
    $('#button2').click(function(event) {
        event.preventDefault(); // フォームの送信を防ぐ
        var shainno = $('#shainno').val(); // 社員番号を取得
        console.log('社員番号:', shainno); // デバッグ用
        if (shainno) {
            // `read2.php` に社員番号を送信してリダイレクトする
            window.open('read2.php?shainno=' + encodeURIComponent(shainno), '_blank');
        } else {
            alert('社員番号を入力してください。');
        }
    });
    $('#button3').click(function(event) {
    event.preventDefault(); // フォームの送信を防ぐ
    var busho = $('#busho').val(); // 部署を取得
    console.log('所属:', busho); // デバッグ用
    if (busho) {
        // `read3.php` に部署を送信してリダイレクトする
        window.open('read3.php?busho=' + encodeURIComponent(busho), '_blank');
    } else {
        alert('部署を入力してください。');
    }
});

$(window).on('load', function () {
                // 全ての入力項目をリセット
                $('input[type="text"], input[type="number"], textarea, select').val(''); // フォーム内のすべての入力項目をクリア

                // フォーム外のすべての入力項目もクリア
                $('input[type="text"], input[type="number"], textarea, select').val(''); // フォーム外の入力項目をクリア
                $('#zentai').empty(); // displayAreaの内容をクリア
                $('.clearable').empty(); // classがclearableの要素をクリア
            });
});
</script>