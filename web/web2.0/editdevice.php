<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?

if ($_POST['delete']) {
    
    $table = "btc_" . $_POST['Class'] . "_inventory";
    $query = "DELETE FROM " . $table . " WHERE Name=\"" . $_POST['Name'] . "\"";
    echo $query;

    mysql_query ($query);
    $result = mysql_affected_rows();
    if ( $result <= 0 ) {
      $stat = fail;
      $msg = "Failed to insert data into table " . $table;
    } else {
      $stat = pass;
      $msg = mysql_affected_rows() . " rows inserted into table " .  $table;
      header ("location:" . $_POST['ref']);
    }
}

if ($_POST['update']) {

    foreach ($_POST as $key => $val) {
        if ($key == "update" || $key == "delete" || $key == "ref") {
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
      header ("location:" . $_POST['ref']);
    }
}

?>

<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Edit Device</title>
  </head>
  <body>
    <form method="post">
    <table border=0 cellpadding=0 cellspacing=0 valign=top align=center width=100%>
    <tr align=left><th><h2>Edit Device Configuration</h2></th></tr>
<?
    if ( $msg != "" ) {
      echo "<tr id=" . $stat . " align=center><td><b>" . $msg . "</b></td></tr>";
    }
?>

    <tr><td>
    <table border=0 cellpadding=5 cellspacing=0 valign=top>

<?
    foreach ($_POST as $key => $val) {

      if ($key == "update" || $key == "delete" || $key == "ref") {
          continue;
      }

      $out = "<tr><td><b>$key</b></td><td><input name=$key width=100 value=$val>";

      if ( $key == "Connection" ) {
        $out = "<tr><td><b>$key</b></td>";
        $out .= "<td><select name=$key width=100 style=\"width:145px\">";
        $out .= "<option value=\"\">Select ...</option>";

        $out .= "<option value=console";
        if ($val == "console") { $out .= " selected"; }
        $out .= ">Console</option>";

        $out .= "<option value=telnet";
        if ($val == "telnet") { $out .= " selected"; }
        $out .= ">Telnet</option>";

        $out .= "<option value=ssh";
        if ($val == "ssh") { $out .= " selected"; }
        $out .= ">SSH</option>";

        $out .= "</select>";
      }

      if ( $key == "Platform" ) {
        $out = "<tr><td><b>$key</b></td>";
        $out .= "<td><select name=$key width=100 style=\"width:145px\">";
        $out .= "<option value=\"\">Select ...</option>";

        $out .= "<option value=BV7500";
        if ($val == "BV7500") { $out .= " selected"; }
        $out .= ">BV7500</option>";

        $out .= "<option value=BV2000";
        if ($val == "BV2000") { $out .= " selected"; }
        $out .= ">BV2000</option>";

        $out .= "</select>";
      }

      echo $out;
      $out = "";
    }
?>

    </table>
    </td>
    </tr>

    <input type="hidden" name="ref" value="<?php echo $_SERVER['HTTP_REFERER'] ?>">

    <tr><th>
      <table border=0 width=100% cellpadding=0>
      <tr>
      <td width=50% align=center><input type="submit" name="update" value="Update"></td>
      <td width=50% align=center><input type="submit" name="delete" value="Delete"></td>
      </tr>
      </table>
    </th>
    </tr>
    </table>

  </form>
  </body>
</html>
