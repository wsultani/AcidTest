<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?
  function fetchWhere ($get,$tbl,$where,$match,$class) {
    //$query="SELECT $get FROM $tbl WHERE $where =\"$match\"";
    $query="SELECT * FROM $tbl WHERE $where =\"$match\" ORDER BY $get";
    $result=mysql_query($query) or die(mysql_error());

    if ( mysql_num_rows($result) == 0 ) {
        echo "<tr colsapn=5><td align=center>No registered " . $class . " device available.<br>\n";
        echo "Register a device first to make it avaible here.</td></tr>\n";
        return;
    }

    $num=0;

    // Print out result
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
      $out = "";
      while (list ($key, $val) = each($row)) {
        // Do not show the following fields
        if ( $key == "id" || $key == $where ) {
            continue;
        }

        $out .= "<tr><td width=50%><b>" . $key . "</b></td><td width=50%>" . $val ."</td></tr>\n";
        $out .= "<input type=hidden name=\"" . $key . "\" value=\"" . $val ."\"></input>\n";
      }

      $b = "$class$num" . "name";
      if ($_POST[$b] == $row[$get]) {
        $cb = "checked";
      } else {
        $cb = "";
      }

      echo "<tr><td><input type=checkbox name=\"$class$num" . "name\" value=$row[$get] $cb>";
      echo "<a href=\"javascript:showhide('$class$num');\">$row[$get]</a></td>\n";
      //echo "<td>Dev# <input type=text name=\"$class$num" . "num\" value=$num size=1></input></tr>\n";
      echo "<input type=hidden name=\"$class$num" . "num\" value=$num></input>\n";

      if ($_POST[DUT] == $row[$get]) {
        $rb = "checked";
      } else {
        $rb = "";
      }

      echo "<td><b>Set as DUT</b><input type=radio name=\"DUT\" value=$row[$get] $rb></input><td></tr>\n";

      echo "<tr><td colspan=5>\n";
      echo "<form action=\"" . $_SERVER['REQUEST_URI'] . "&show=editdevice\" method=\"post\">\n";
      echo "<span id=\"$class$num\" style=\"display:none\">\n";
      echo "<table cellpadding=0 cellspacing=0 border=0 width=90% align=right>\n";
      echo $out;
      echo "<tr><td align=right colspan=4><input type='submit' value='Edit'></input></td></tr>\n";
      echo "</table>\n";
      echo "</span>\n";
      echo "</form>\n";
      echo "</td></tr>\n";
      echo "<tr><td colspan=2><hr>\n";
      echo "</td></tr>\n";
      $num++;
    }
  }
?>

<?
  $empty=0;
  foreach ($_POST as $key => $value) {

    // get the list of devices in the testbed.
    if (ereg ("(([a-zA-z]+)[0-9]+)name", $key, $match)) {
          $dev_array[$key] = $value;
    }

    // checking for empty values
    if ($key == "Testbed") {
      if (empty($value)) {
        $stat = fail;
        $msg = $msg . $key . " - cannot be empty.<br>";
        $empty ++;
      }
    }
  }

  if ($_POST['Testbed'] != "" && !isset($_POST[DUT])) {
    $stat = fail;
    $msg = $msg . "DUT - cannot be empty.<br>";
    $empty ++;
  }

  if ( $empty == "0" && $_POST['Testbed'] != "" ) {
    foreach ($dev_array as $key => $value) {
      if (ereg ("(([a-zA-z]+)[0-9]+)name", $key, $match)) {
          $num = $match[1] . "num";
          $num = $_POST[$num];
          $name = $value;
          $class = $match[2];
          if ($_POST[DUT] == $name) {
            $dut = 1;
          } else {
            $dut = 0;
          }
      } else {
          continue;
      }

      $table="btc_testbeds";
      $arr1 = array('Testbed', 'DevName', 'DevClass', 'DevNum', 'DUT');
      $keys = join(", ",($arr1));
      $arr2 = array($_POST[Testbed], $name, $class, $num, $dut);
      $vals = join("\", \"",($arr2));

      $query = "REPLACE INTO $table ($keys) VALUES (\"$vals\")";

      mysql_query ($query);
      $result = mysql_affected_rows();
      if ( $result <= 0 ) {
        $stat = fail;
        $msg = "Failed to insert data into table " . $table;
      } else { 
        $stat = pass;
        $msg = mysql_affected_rows() . " rows inserted into table " .  $table;

        // associate the device in the inventory table with the testbed
        $table = "btc_" . $class . "_inventory";
        $query = "UPDATE $table SET Testbed=\"$_POST[Testbed]\" WHERE Name=\"$name\"";

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
    }
  }
?>

<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Create Testbed</title>

    <script type="text/javascript">
        function showhide(targetID) {
          //change target element mode
          var elementmode = document.getElementById(targetID).style;
          elementmode.display = (!elementmode.display) ? 'none' : '';
	}
    </script>

  </head>
  <body>
    <form method="post">
    <table border=0 cellpadding=5 cellspacing=0 valign=top align=center width=100%>
    <tr align=left><th><h2>Create New Testbed</h2></th></tr>

<?
    if ( $msg != "" ) {
      echo "<tr id=" . $stat . " align=center><td><b>" . $msg . "</b></td></tr>";
    }
?>

    <tr><td>
    <table border=0 cellpadding=0 cellspacing=0 valign=top width=100%>
    <tr>
    <td><b>Testbed Name</b></td><td><input name=Testbed value="<? echo $_POST[Testbed]; ?>" width=100 /><font size=0.6em /> Required</td>
    <td width=33% align=center><input type="submit" value="Create Testbed" /></td>
    <td width=33% align=center><b><? echo '<a href="'.$_SERVER['REQUEST_URI'].'">Reset</a>'; ?></b></td>
    </tr>
    <tr><td colspan=5><br></td></tr>
    </table>
    </tr><tr>

    <tr><td>
    <table id=border width=100% valign=top border=1 frame=box rules=all cellpadding=5 cellspacing=0>
    <tr><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Available Bivio Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_bivio_inventory","Testbed","","bivio");
?>
    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Available PC Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_pc_inventory","Testbed","","pc");
?>

    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Available ADB Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_adb_inventory","Testbed","","adb");
?>

    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Available Switch Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_sw_inventory","Testbed","","sw");
?>

    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Available SmartBits Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_smb_inventory","Testbed","","smb");
?>

    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Available Avalanche Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_aval_inventory","Testbed","","aval");
?>

    </td></tr></table>
    </table>

    </td></tr>
    </table>
    </td></tr>

    <tr><th>
      <table border=0 width=100% cellpadding=0>
      <tr>
      <td width=50% align=center><input type="submit" value="Create Testbed" /></td>
      <td align=center><b><? echo '<a href="'.$_SERVER['REQUEST_URI'].'">Reset</a>'; ?></b></td>
      </tr>
      </table>
    </th>
    </tr>
    </table>
  </form>
  </body>
</html>
