<?php
/* ドライバ呼び出しを使用して MySQL データベースに接続する compose.yamlを参照*/
$dsn = 'mysql:dbname=posse;host=db';
$user = 'root';
$password = 'root';

$dbh = new PDO($dsn, $user, $password);
echo 'DB接続成功</br>';

/* qustionsテーブル検索 */
$sql_questions = "SELECT * FROM questions";
$questions = $dbh->query($sql_questions)->fetchAll(PDO::FETCH_ASSOC);
echo('<pre>');
var_dump($questions);
print('</br>');

/* choicesテーブル検索 */
$sql_choices = "SELECT id, question_id, name, valid FROM choices";
$choices = $dbh->query($sql_choices)->fetchAll(PDO::FETCH_ASSOC);
echo('<pre>');
var_dump($choices);
print('</br>');

print('</br>');

/* データ整形 */
//choicesをレコード数分ループ回す。レコード：配列の一行
foreach ($choices as $key =>$choice) {
  // choice変数のquestion_idをキーに、対応するquestion変数を検索。$indexにインデックス番号を代入する
  $index = array_search($choice["question_id"], array_column($questions, 'id'));
  // 検索されたquestion変数にchoice変数の内容を追加する。["choices"]の意味は？？？
  $questions[$index]["choices"][] = $choice;
}
echo('<pre>');
var_dump($questions);

?>