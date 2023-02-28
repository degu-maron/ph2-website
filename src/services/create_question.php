<?php
// imgファイルの名前を乱数発生させて全部違うやつになるようにしつつ、formで送られてきたファイルを取得してる
$image_name = uniqid(mt_rand(), true) . '.' . substr(strrchr($_FILES['image']['name'], '.'), 1);
$image_path = dirname(__FILE__) . '/../assets/img/quiz/' . $image_name;
move_uploaded_file(
  $_FILES['image']['tmp_name'], 
  $image_path
);


$pdo = new PDO('mysql:host=db;dbname=posse', 'root', 'root');
$questions = $pdo->query("SELECT * FROM questions")->fetchAll(PDO::FETCH_ASSOC);
$choices = $pdo->query("SELECT * FROM choices")->fetchAll(PDO::FETCH_ASSOC);

// データ整形
foreach ($choices as $key =>$choice) {
  // choice変数のquestion_idをキーに、対応するquestion変数を検索。$indexにインデックス番号を代入する
  $index = array_search($choice["question_id"], array_column($questions, 'id'));
  // 検索されたquestion変数にchoice変数の内容を追加する。["choices"]の意味は？？？
  $questions[$index]["choices"][] = $choice;
}

// 問題挿入処理
$stmt = $pdo->prepare("INSERT INTO questions(content, image, supplement) VALUES(:content, :image, :supplement)");
$stmt->execute([
  "content" => $_POST["content"],
  "image" => $image_name,
  "supplement" => $_POST["supplement"]
]);
$lastInsertId = $pdo->lastInsertId();   // last InsertId : Returns the ID of the last inserted row or sequence value

// 選択肢挿入処理
$stmt = $pdo->prepare("INSERT INTO choices(name, valid, question_id) VALUES(:name, :valid, :question_id)");
for ($i = 0; $i < count($_POST["choices"]); $i++) {
  $stmt->execute([
    "name" => $_POST["choices"][$i],
    "valid" => (int)$_POST['correctChoice'] === $i + 1 ? 1 : 0,
    "question_id" => $lastInsertId
  ]);
}

// 全部終わったら問題一覧の画面に遷移
header("Location: ". "http://localhost:8080/admin/index.php");

?>