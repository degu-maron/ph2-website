<?php
$dbn = 'mysql:dbname=posse; host=db';
$user = 'root';
$pass = 'root';

try {
  $pdo = new PDO($dbn, $user, $pass);
} catch (PDOException $e) {
  die("接続エラー：{$e->getMessage()}");
}

if ($_POST) {
  $day = $_POST['day'];
  // 〇年〇月〇日の表記を〇-〇-〇にしたい
  // str_replace( $検索文字列 , $置換後文字列 , $検索対象文字列 [, int &$count ] )
  $target = ['年', '月'];
  $replaceYearMonth = str_replace($target, '-', $day);
  $replaceDate = str_replace('日', '', $replaceYearMonth);
  // strtotime関数で現在日時や指定した日時のUnixタイムスタンプを取得する
  $newDate = date('Y-m-d', strtotime($day));
  // $content = filter_input(INPUT_POST, 'content', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
  // $language = filter_input(INPUT_POST, 'language', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
  // $hour = filter_input(INPUT_POST, 'hour', FILTER_DEFAULT, FILTER_VALIDATE_INT);
  $content = $_POST['content'];
  $language = $_POST['language'];
  $hour = $_POST['hour'];

  // POSTされたものをstudiesテーブルに追加
  $sql = 'INSERT INTO studies(day, hour) VALUES (:day, :hour)';
  $stmt = $pdo->prepare($sql);
  if ($newDate === "") {
    $stmt->bindValue("day", null, PDO::PARAM_NULL);
  } else {
    $stmt->bindValue(':studied_date', $newDate);
  }
  if ($hour === "") {
    $stmt->bindValue(":hour", null, PDO::PARAM_NULL);
  } else {
    $stmt->bindValue(':hour', $hour);
  }
  $studies = $stmt->execute();

  // 言語、コンテンツは別で管理したい
  $studies_id = $pdo->lastInsertId();

  // 言語複数あったらその数で割ったのを学習時間として登録
  $time_lang = $hour / count($language);
  $sql = 'INSERT INTO languages(studies_id, language, hour) VALUES (:studies_id, :language, :hour)';
  $stmt = $pdo->prepare($sql);
  foreach ($language as $lang) {
    $stmt->bindValue(':studies_id', $studies_id);
    $stmt->bindValue(':language', $lang);
    $stmt->bindValue(':hour', $time_lang);
    $languages = $stmt->execute();
  }

  // コンテンツも同様
  $time_con = $hour / count($content);
  $sql = 'INSERT INTO content(studies_id, content, hour) VALUES (:studies_id, :content, :hour)';
  $stmt = $pdo->prepare($sql);
  foreach ($content as $con) {
    $stmt->bindValue(':studies_id', $studies_id);
    $stmt->bindValue(':content', $con);
    $stmt->bindValue(':hour', $time_con);
    $contents = $stmt->execute();
  }

  // POSTおわったらリダイレクトする
  header("Location:./index.php");
} else {
  echo "error";
}

// 日の合計
$date = $pdo->query("SELECT DATE_FORMAT(day, '%Y-%m-%d') as day, case when sum(hour) is not null then sum(hour) else 0 end as hour from studies group by day having day = DATE_FORMAT(CURDATE(), '%Y-%m-%d')")->fetchAll(PDO::FETCH_ASSOC);
// 月の合計
$stmt = $pdo->prepare("SELECT DATE_FORMAT(day, '%Y-%m') as month, sum(hour) as hour from studies group by month having month=:month");
$stmt->bindValue(':month', date('Y-m'));
$stmt->execute();
$month = $stmt->fetchAll(PDO::FETCH_ASSOC);
// 総計
$total = $pdo->query("SELECT sum(hour) as hour from studies")->fetchAll(PDO::FETCH_ASSOC);

// 日毎の学習時間
$sql = "SELECT calendar.ymd as ymd, COALESCE(studies.hour, 0) as 学習時間 FROM (
  SELECT DATE_FORMAT(date_add(date_add(last_day(now()), interval - day(last_day(now())) DAY) , INTERVAL td.add_day DAY), '%y-%m-%d') AS ymd FROM (
      SELECT 0 as add_day FROM dual WHERE ( @num:= 1 - 1 ) * 0 union all SELECT @num:= @num + 1 as add_day FROM `information_schema`.columns limit 31
  ) AS td
) AS calendar LEFT JOIN studies ON calendar.ymd = studies.day WHERE month(calendar.ymd) = month(now()) ORDER BY calendar.ymd";
$hours = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// グラフデータ用意
$formatted_days_data = array_map(function ($data) {
  return $data['ymd'];
}, $hours);
$j_days_data = json_encode($formatted_days_data);
file_put_contents("days.json", $j_days_data);

$formatted_hours_data = array_map(function ($data) {
  return $data['学習時間'];
}, $hours);
$j_hours_data = json_encode($formatted_hours_data);
file_put_contents("hours.json", $j_hours_data);

?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>POSSE | Webapp</title>
  <link rel="stylesheet" href="./assets/styles/normalize.css">
  <link rel="stylesheet" href="./assets/styles/style.css">
  <!-- chartjs読み込み -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" defer></script>
  <!-- js読み込み -->
  <script src="./assets/scripts/modal.js" defer></script>
  <script src="./assets/scripts/calendar.js" defer></script>
  <script src="./assets/scripts/barGraph.js" defer></script>
  <script src="./assets/scripts/languageChart.js" defer></script>
  <script src="./assets/scripts/studyChart.js" defer></script>
</head>

<body>
  <header>
    <div class="header_logo">
      <img src="./assets/img/logo.svg" alt="posse">
    </div>
    <div class="header_title">4th week</div>
    <button id="js-recordButton" class="header_record-button">記録・投稿</button>
  </header>
  <div class="modal" id="js-modal">
    <div class="modal_container" id="js-modalContainer">
      <div class="modal_container_inner">
        <div class="modal_inner_left">
          <div class="modal_day">
            <p class="label">学習日</p>
            <input type="text" id="day" name="day">
          </div>
          <div class="modal_content">
            <p class="label">学習コンテンツ（複数選択可）</p>
            <div class="checkbox_container">
              <div class="checkbox"><input type="checkbox" id="content1" name="content"><label for="content1"
                  class="checkbox_label">N予備校</label></div>
              <div class="checkbox"><input type="checkbox" id="content2" name="content"><label for="content2"
                  class="checkbox_label">ドットインストール</label></div>
              <div class="checkbox"><input type="checkbox" id="content3" name="content"><label for="content3"
                  class="checkbox_label">POSSE課題</label></div>
            </div>
          </div>
          <div class="modal_language">
            <p class="label">学習言語（複数選択可）</p>
            <div class="checkbox_container">
              <div class="checkbox"><input type="checkbox" id="language1" name="language"><label for="language1"
                  class="checkbox_label">HTNL</label></div>
              <div class="checkbox"><input type="checkbox" id="language2" name="language"><label for="language2"
                  class="checkbox_label">CSS</label></div>
              <div class="checkbox"><input type="checkbox" id="language3" name="language"><label for="language3"
                  class="checkbox_label">JavaScript</label></div>
              <div class="checkbox"><input type="checkbox" id="language4" name="language"><label for="language4"
                  class="checkbox_label">PHP</label></div>
              <div class="checkbox"><input type="checkbox" id="language5" name="language"><label for="language5"
                  class="checkbox_label">Laravel</label></div>
              <div class="checkbox"><input type="checkbox" id="language6" name="language"><label for="language6"
                  class="checkbox_label">SQL</label></div>
              <div class="checkbox"><input type="checkbox" id="language7" name="language"><label for="language7"
                  class="checkbox_label">SHELL</label></div>
              <div class="checkbox"><input type="checkbox" id="language8" name="language"><label for="language8"
                  class="checkbox_label">情報システム基礎(その他)</label></div>
            </div>

          </div>
        </div>
        <div class="modal_inner_right">
          <div class="modal_hour">
            <p class="label">学習時間</p>
            <input type="text" id="hour" name="hour">
          </div>
          <div class="modal_twitter">
            <p class="label">Twitter用コメント</p>
            <input type="text" id="twitter" name="twitter">
          </div>
          <input type="checkbox" id="share_button"><label for="share_button"
            class="share_button_label">Twiterにシェアする</label>
        </div>
      </div>
      <button id="modalRecordButton" class="modal_record-button">記録・投稿</button>
      <button id="js-closeButton" class="modal_close-button">×</button>
    </div>
    <div id="js-calendar" class="calendar">
      <button id="js-backButton" class="calendar_close-button">←</button>
      <table>
        <thead>
          <tr>
            <th id="prev">&lt;</th>
            <th id="title" colspan="5">2022年10月</th>
            <th id="next">&gt;</th>
          </tr>
          <tr>
            <th class="week">Sun</th>
            <th class="week">Mon</th>
            <th class="week">Tue</th>
            <th class="week">Wed</th>
            <th class="week">Thu</th>
            <th class="week">Fri</th>
            <th class="week">Sat</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
          <th id="calendarButton" class="calendar_button">決定</th>
        </tfoot>
      </table>
    </div>
    <div id="js-loading" class="loading">
      <div class="loader">
        <div class="one"></div>
        <div class="two"></div>
        <div class="three"></div>
      </div>
      <button id="js-closeButton2" class="modal_close-button">×</button>
    </div>
    <div id="js-done" class="done">
      <div class="done_inner">
        <p class="done_message">AWESOME!</p>
        <div class="done_mark"></div>
        <p class="done_comment">記録・投稿<br>完了しました</p>
      </div>
      <button id="js-closeButton3" class="modal_close-button">×</button>
    </div>
  </div>
  <main>
    <div class="main_content">
      <div class="main_content_left">
        <div class="main_content_hours">
          <div class="hour_data">
            <p class="hour_data_title">Today</p>
            <p class="hour_data_figure">
              <?php
              if (isset($date[0]['hour'])) {
                echo $date[0]['hour'];
              } else {
                echo '0';
              }
              ?>
            </p>
            <p class="hour_data_unit">hour</p>
          </div>
          <div class="hour_data">
            <p class="hour_data_title">Month</p>
            <p class="hour_data_figure">
              <?php
              if (isset($month[0]['hour'])) {
                echo $month[0]['hour'];
              } else {
                echo '0';
              }
              ?>
            </p>
            <p class="hour_data_unit">hour</p>
          </div>
          <div class="hour_data">
            <p class="hour_data_title">Total</p>
            <p class="hour_data_figure">
              <?php
              if (isset($total[0]['hour'])) {
                echo $total[0]['hour'];
              } else {
                echo '0';
              }
              ?>
            </p>
            <p class="hour_data_unit">hour</p>
          </div>
        </div>
        <div class="main_content_bar-graph">
          <canvas id="barGraph" class="bar-graph">
            Canvas not supported...
          </canvas>
        </div>
      </div>
      <div class="main_content_right">
        <div class="main_content_language">
          <div class="chart_area">
            <canvas id="languageChart" class="language_chart" height="303px" width="183px">
              Canvas not supported...
            </canvas>
          </div>
          <div class="legend_area">
            <p class="legend_label label_html">HTML</p>
            <p class="legend_label label_css">CSS</p>
            <p class="legend_label label_js">JavaScript</p>
            <p class="legend_label label_php">PHP</p>
            <p class="legend_label label_laravel">Larabel</p>
            <p class="legend_label label_sql">SQL</p>
            <p class="legend_label label_shell">SHELL</p>
            <p class="legend_label label_info">情報システム基礎知識(その他)</p>
          </div>
        </div>
        <div class="main_content_study-content">
          <div class="chart_area">
            <canvas id="studyChart" class="study_chart">
              Canvas not supported...
            </canvas>
          </div>
          <div class="legend_area">
            <p class="legend_label label_Nyobi">N予備校</p>
            <p class="legend_label label_dotinstall">ドットインストール</p>
            <p class="legend_label label_task">課題</p>
          </div>

        </div>
      </div>
    </div>
    <div class="main_bottom">
      <div class="main_bottom_arrow arrow_left"></div>
      <p class="main_bottom_month">
        <?= date('Y年m月') ?>
      </p>
      <div class="main_bottom_arrow arrow_right"></div>
    </div>
  </main>
  <!-- <div class="test">
    <?PHP
    echo '<pre>';
    var_dump($formatted_hours_data);
    var_dump($formatted_days_data);
    ?>
  </div> -->











  <!-- 棒グラフ -->
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>

  <script>
    let type = 'bar';

    let days = JSON.parse('<?php // echo $j_days_data; ?>');
    let hours = JSON.parse('<?php // echo $j_hours_data; ?>');

    const ctx = document.getElementById('barGraph').getContext('2d');
    const gradientStroke = ctx.createLinearGradient(0, 0, 0, 100);
    gradientStroke.addColorStop(0, 'rgb(20, 191, 250)');
    gradientStroke.addColorStop(0.5, 'rgb(20, 180, 240)');
    gradientStroke.addColorStop(1, 'rgb(19, 131, 201)');

    const data = {
      labels: days,
      datasets: [{
        label: '',
        data: hours,
        backgroundColor: gradientStroke,
        borderRadius: 50,
        barThickness: 4,
      }]
    };

    const options = {
      scales: {
        x: {
          grid: {
            display: false,
            drawBorder: false,
          },
          ticks: {
            autoSkip: false,
            maxRotation: 0,  // 数字が傾かないように
            minRotation: 0,
            color: 'rgb(50, 101, 255)',
            callback: (value) => {
              if (value % 2 == 0) {
                return '';
              } else {
                return value + 1;
              };
            },
          },
        },
        y: {
          ticks: {
            color: 'rgb(50, 101, 255)',
            suggestedMin: 0,
            suggestedMax: 8,
            stepSize: 2,
            callback: (value) => value + 'h'
          },
          grid: {
            display: false,
            drawBorder: false,
          }
        },
      },
      animation: false,
      plugins: {
        legend: {
          display: false
        },
      },
    };

    const hoursGraph = new Chart(ctx, {
      type: type,
      data: data,
      options: options,
    });
    hoursGraph

  </script> -->
</body>

</html>