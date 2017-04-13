<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<meta http-equiv="content-script-type" content="text/javascript">
<link href="css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css">
<script type="text/javascript" src="jquery-3.1.1.min.js"></script>
<script src="bootjs/bootstrap.min.js"></script>
</head>
<body>

<?php

    $mysqli = new mysqli( 'localhost', 'root', '@dev5-Sherecal()', 'sharecal');
    if( $mysqli->connect_errno ) {
      echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
    }
    $mysqli->set_charset('utf8');

    $id = $mysqli->real_escape_string($_POST['id']);
    $password = $mysqli->real_escape_string($_POST['pass']);

    // クエリの実行
    $query = "select * from User_Table where UserID='$id'";
    $result = $mysqli->query($query);
    if (!$result) {
      print('クエリーが失敗しました。' . $mysqli->error);
      $mysqli->close();
      exit();
    }

    // パスワード(暗号化済み）とユーザーIDの取り出し
    while ($row = $result->fetch_assoc()) {
      $db_hashed_pwd = $row['UserPassword'];
      $user_id = $row['UserID'];
    }

    // データベースの切断
    $result->close();

    // ハッシュ化されたパスワードがマッチするかどうかを確認
    if (password_verify($password, $db_hashed_pwd)) {
      $_SESSION['user'] = $user_id;
      header("Location: calen_ver3.php");
      exit;
    } else { ?>
      <div class="alert alert-danger" role="alert">IDとパスワードが一致しません。</div>
    <?php } ?>

</body>
</html>