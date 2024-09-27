// geminiを設定
import { genAI , model } from "./js/firebase.js";

$(document).ready(function() {
    $(".error-message").hide();

    $('form').on('submit', async function(event) {
      event.preventDefault();  // デフォルトのフォーム送信を防ぐ

        // エラーメッセージを初期化
        $(".error-message").hide();
        
    // フォームデータの取得
    const formData = {
            shainno: $('#shainno').val(),
            gender: $('#gender').val(),
            age: $('#age').val(),
            busho: $('#busho').val(),
            yakushoku: $('#yakushoku').val(),
            year: $('#year').val(),
            impression: $('#impression').val(),
            q1: $('#q1').val(),
            q2_2: $('#q22').val(),
            q2_3: $('#q23').val(),
            careerimpact: $('#careerimpact').val(),
            solve: $('#solve').val(),
            options: $('input[name="option"]:checked').map(function() {
              return this.value;
            }).get().join(', ')
          };
      // バリデーションチェック
      let valid = true;
          $('.error-message').hide();
          if (!formData.shainno.match(/^\d{6}$/)) {
            $('#shainno-error').show();
            valid = false;
          }
          if (!formData.gender) {
            $('#gender-error').show();
            valid = false;
          }
          if (!formData.age) {
            $('#age-error').show();
            valid = false;
          }
          if (!formData.busho) {
            $('#busho-error').show();
            valid = false;
          }
          if (!formData.yakushoku) {
            $('#yakushoku-error').show();
            valid = false;
          }
          if (!formData.year) {
            $('#year-error').show();
            valid = false;
          }
          if (!formData.impression) {
            $('#impression-error').show();
            valid = false;
          }
          if (!formData.q1) {
            $('#q1-2-error').show();
            valid = false;
          }
          if (!formData.q2_2) {
            $('#q2-2-error').show();
            valid = false;
          }
          if (!formData.q2_3) {
            $('#q2-3-error').show();
            valid = false;
          }
          if (!formData.careerimpact) {
            $('#careerimpact-error').show();
            valid = false;
          }
          if (!formData.solve) {
            $('#solve-error').show();
            valid = false;
          }
          if (!$('input[name="option"]:checked').length) {
            $('#q2-1-error').show();
            valid = false;
          }

          if (!valid) {
            return;
          }


          try {
            // AIリクエストの作成
            const response = await model.generateContent({
              prompt: `以下の内容に基づいてアドバイスを生成してください。\n
              - 印象: ${formData.impression}\n
              - Q1: ${formData.q1}\n
              - Q2-2: ${formData.q2_2}\n
              - Q2-3: ${formData.q2_3}\n
              - Q3-1: ${formData.careerimpact}\n
              - Q3-2: ${formData.solve}\n
              - 選択肢: ${formData.options}`,
              temperature: 0.5
            });

            // AIからのアドバイスを表示
            $('#advice-content').text(response.choices[0].text);
          } catch (error) {
            console.error('AIリクエストエラー:', error);
            $('#advice-content').text('AIからのアドバイスの取得中にエラーが発生しました。');
          }
        });
      });
