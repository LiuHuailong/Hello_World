<?php
  $mysqli = new mysqli('localhost', 'root', '@dev5-Sherecal()', 'sharecal');
  if ($mysqli->connect_errno) {
    echo $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
  }
  $mysqli->set_charset('utf8');

  $userid = $mysqli->real_escape_string($_POST['userid']);
  $yy = $mysqli->real_escape_string($_POST['yy']);
  $mm = $mysqli->real_escape_string($_POST['mm']);
  $day = $mysqli->real_escape_string($_POST['day']);

  $_Date = '$yy' . '-' . '$mm' . '-' . '$day';

  $sql0 = "SELECT COUNT(*) FROM Private_Table AS P WHERE P.UserID = '$userid' AND P.CalendarDate = '$_Date' ORDER BY P.TitleNum";

  $result = $mysqli->query($sql0);
  if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
  }

  $count = 0;
  // 何行のTitleがあるかをcountに代入
  while ($row = $result->fetch_assoc()) {
    $count = $row[0];
  }
  // データベースの切断
  $result->close();

  $sql1 = "SELECT P.Title FROM Private_Table AS P WHERE P.UserID = '$userid' AND P.CalendarDate = '$_Date'";

  $result = $mysqli->query($sql1);
  if (!$result) {
    print('クエリーが失敗しました。' . $mysqli->error);
    $mysqli->close();
    exit();
  }

  $array = array();
  while ($row = $result->fetch_assoc()) {
    $array[] = $row[0];
  }

?>

<script type="text/javascript">
  var title_array = <?php $array; ?>;
  var title_array_count = <?php count($array) ?>
</script>