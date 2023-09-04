<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Catnet</title>
  </head>
</body>



<?php
require_once "./dbc.php";

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

// ファイル関連の取得
$files = $_FILES['img'];

// 複数ファイルが取得できるため、繰り返し処理で1ファイルずつ処理をします
for ($i = 0; $i < count($files['name']); $i++) {
    // ファイルのバリデーション
    $file_err = $files['error'][$i];
    $filesize = $files['size'][$i];
    $filename = basename($files['name'][$i]);
    $err_msgs = array();
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
    // ファイルパスの生成
    $upload_dir = 'images/';
    $tmp_path = $files['tmp_name'][$i];
    $save_filename = date('YmdHis') . $filename;
    $save_path = $upload_dir . $save_filename;

    // ファイルの保存処理
    if (count($err_msgs) === 0) {
        // ファイルはあるかどうか？
        if (is_uploaded_file($tmp_path)) {
            if (move_uploaded_file($tmp_path, $save_path)) {
                echo $filename . 'を' . $upload_dir . 'アップしました。<br>';
                // DBに保存(ファイル名、ファイルパス、キャプション)
                // 注意：ファイルの数だけSQLが発行されてしまうため、性能的に良くありません。
                // できれば1つのSQLで一括で保存できるようにした方がいいです。
                $result = fileSave($filename, $save_path, $caption);
    
                if (!$result) {
                    echo 'データベースへの保存が失敗しました！';
                    // エラーの場合は処理を終了したいのでexitで終了させています。
                    // ただこの場合戻るボタンが出なくなるので、本来は微妙です。
                    exit;
                }
            } else {
                echo 'ファイルが保存できませんでした。';
                exit;
            }
        } else {
            echo 'ファイルが選択されていません。';
            exit;
        }
    } else {
        foreach ($err_msgs as $msg) {
            echo $msg;
            echo '<br>';
        }
        break;
    }
}

?>
<a href="./upload_form.php">戻る</a>
