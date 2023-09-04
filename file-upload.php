<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catnet</title>
  </head>
<body>



<?php
require_once "./dbc.php";

// ファイル関連の取得
$file = $_FILES['img'];
$filename = basename($file['name']);
$tmp_path = $file['tmp_name'];
$file_err = $file['error'];
$filesize = $file['size'];
$upload_dir = 'images/';
$save_filename = date('YmdHis') . $filename;
$err_msgs = array();
$save_path = $upload_dir . $save_filename;

// キャプションを取得
$caption = filter_input(INPUT_POST, 'caption', FILTER_SANITIZE_SPECIAL_CHARS);

// キャプションのバリデーション
// 未入力
if (empty($caption)) {
  array_push($err_msgs, 'キャプションを入力してください。');
}
// 140文字か
if (strlen($caption) > 140) {
  array_push($err_msgs, 'キャプションは140文字以内で入力してください。');
}

// ファイルのバリデーション
// ファイルサイズが1MB未満か
if ($filesize > 1048576 || $file_err == 2) {
  array_push($err_msgs, 'ファイルサイズは1MB未満にしてください。');
}

// 拡張は画像形式か
$allow_ext = array('jpg', 'jpeg', 'png');
$file_ext = pathinfo($filename, PATHINFO_EXTENSION);

if (!in_array(strtolower($file_ext), $allow_ext)) {
  array_push($err_msgs, '画像ファイルを添付してください。');
}

if (count($err_msgs) === 0) {
  // ファイルはあるかどうか？
  if (is_uploaded_file($tmp_path)) {
    if (move_uploaded_file($tmp_path, $save_path)) {
      echo $filename . 'を' . $upload_dir . 'アップしました。';
      // DBに保存(ファイル名、ファイルパス、キャプション)
      $result = fileSave($filename, $save_path, $caption);

      if ($result) {
        echo 'データベースに保存しました！';
      } else {
        echo 'データベースへの保存が失敗しました！';
      }
    } else {
      echo 'ファイルが保存できませんでした。';
    }
  } else {
    echo 'ファイルが選択されていません。';
    echo '<br>';
  }
} else {
  foreach ($err_msgs as $msg) {
    echo $msg;
    echo '<br>';
  }
}

?>
<a href="./upload_form.php">戻る</a>
