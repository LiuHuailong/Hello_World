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

    $name = $mysqli->real_escape_string($_POST['name']);
    $id = $mysqli->real_escape_string($_POST['id']);
    $pass = $mysqli->real_escape_string($_POST['pass']);
    $password = password_hash($pass, PASSWORD_DEFAULT);

    $query = "insert into User_Table(UserID,UserName,UserPassword) values('$name','$id','$password')";

    if($mysqli->query($query)) {  ?>
      <h2>This is Sharecal www.sharecal.club</h2>
      <div class="alert alert-success" role="alert">登録しました</div>
      <?php
      header("Location: login.html");
      ?>
      <?php } else { ?>
      <h2>This is Sharecal www.sharecal.club</h2>
      <div class="alert alert-danger" role="alert">すでに登録されています</div>
      <?php
    }
?>

</body>
</html>