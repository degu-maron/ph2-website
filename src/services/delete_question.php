<?= $_GET['id']; ?>

<?php

// データ整形
foreach ($choices as $key =>$choice) {
    // choice変数のquestion_idをキーに、対応するquestion変数を検索。$indexにインデックス番号を代入する
    $index = array_search($choice["question_id"], array_column($questions, 'id'));
    // 検索されたquestion変数にchoice変数の内容を追加する。["choices"]の意味は？？？
    $questions[$index]["choices"][] = $choice;
}



// 削除処理
$pdo->beginTransaction();
Try{
    // choicesとquestions両方けす
    $delete_choices = $pdo->prepare("DELETE FROM choices WHERE question_id = :id");
    $delete_choices->bindValue(':id', $_REQUEST["id"]);
    $delete_choices->execute();

    $delete_question = $pdo->prepare("DELETE FROM questions WHERE id = :id");
    $delete_question->bindValue(':id', $_REQUEST["id"]);
    $delete_question->execute();
    $pdo->commit();
    echo "削除しました";
} catch (PDOException $e) {
    // エラー発生
    echo $e->getMessage();
     
} finally {
    // DB接続を閉じる
    $pdo = null;
}
?>