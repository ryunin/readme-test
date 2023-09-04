<?php
session_start();

require_once '../classes/UserLogic.php';

// ログインフォームが送信された場合の処理
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // エラーメッセージ
    $err = [];

    // バリデーション
    if (!$email) {
        $err['email'] = 'メールアドレスを記入してください。';
    }
    if (!$password) {
        $err['password'] = 'パスワードを記入してください。';
    }

    if (count($err) === 0) {
        // ログイン成功時の処理
        $result = UserLogic::login($email, $password);

        if ($result) {
            if (isset($_POST['redirect'])) {
                // ボタンがクリックされた場合にリダイレクト
                header("Location: catnet.php"); // リダイレクト先のURLを指定
                exit;
            }
        } else {
            // ログイン失敗時の処理
            $err['login_fail'] = 'ログインに失敗しました。';
        }
    }

    // エラーがあった場合は戻す
    $_SESSION['err'] = $err;
    header('Location: login_form.php');
    return;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>catnet</title>
</head>
<body>
    <h2>ログイン完了</h2>
    <p>ログインしました！</p>
    <a href="./mypage.php">マイページへ</a>
    <a href="./upload_form.php">ホーム画面へ</a>
</body>
</html>
