<?php

if ($_POST['update']) {

    foreach ($_POST as $key => $val) {
        if ($key == "update") {
            continue;
        }
        $postarr[] = $key . "=\"" . $val . "\"";
    }
    $out = implode(",", $postarr);

    $table = "btc_" . $_POST['Class'] . "_inventory";
    $query = "UPDATE " . $table . " SET " . $out . " WHERE Name=\"" . $_POST['Name'] . "\"";
    echo $query;

    mysql_query ($query);
    $result = mysql_affected_rows();
    if ( $result <= 0 ) {
      $stat = fail;
      $msg = "Failed to insert data into table " . $table;
    } else {
      $stat = pass;
      $msg = mysql_affected_rows() . " rows inserted into table " .  $table;
    }
}

?>

<html>
<head>
</head>
<body>

<form method="post">
<table>

<?php

foreach ($_GET as $key => $val) {
    echo "<tr><td>" . $key . " </td><td><input type=text name=\"" . $key . "\" value=\"" . $val . "\"></input></td></tr>\n";
} 
?>

<tr><td><input type="submit" value="Submit" name="update" /></td></tr>

</table>
</form>
</body>
</html>
