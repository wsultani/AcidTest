<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?
  function fetchWhere ($get,$tbl,$where,$match,$class) {
    //$query="SELECT $get FROM $tbl WHERE $where =\"$match\"";
    $query="SELECT * FROM $tbl WHERE $where =\"$match\"";
    $result=mysql_query($query) or die(mysql_error());

    $query="SELECT DevName FROM btc_testbeds WHERE Testbed =\"$match\" AND DUT = \"1\"";
    $result2=mysql_query($query) or die(mysql_error());
    $row2 = mysql_fetch_array($result2,MYSQL_ASSOC);

    $num=0;

    // Print out result
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)){

      if ($row2[DevName] == $row[$get]) {
        $dut = "DUT";
        $style = "style='text-align:center; border:red thin solid; color:red'";
      } else {
        $dut = "";
        $style = "";
      }

      $out = "";
      while (list ($key, $val) = each($row)) {
        // Do not show the following fields
        if ( $key == "id" || $key == $where ) {
            continue;
        }
        $out .= "<tr><td width=50%><b>" . $key . "</b></td><td width=50%>" . $val ."</td></tr>\n";
        $out .= "<input type=hidden name=\"" . $key . "\" value=\"" . $val ."\"></input>\n";
      }

      echo "<tr><td width=70%>$row[$get]</td>\n";
      echo "<td " . $style . " width=30%><b>" . $dut . "</b></td></tr>\n";
      //echo "<td " . $style . " width=30%>Dev# $num </td></tr>\n";

      echo "<tr><td colspan=5>\n";
      echo "<form action=\"" . $_SERVER['REQUEST_URI'] . "&show=editdevice\" method=\"post\">\n";
      echo "<table cellpadding=0 cellspacing=3 border=0 width=90% align=right>\n";
      echo $out;
      echo "<tr><td align=right colspan=4><input type='submit' value='Edit'></input></td></tr>\n";
      echo "</table>\n";
      echo "</form>\n";
      echo "</td></tr>\n";
      $num++;
    }
  }
?>

<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Testbeds</title>

    <script type="text/javascript">
        function showhide(targetID) {
          //change target element mode
          var elementmode = document.getElementById(targetID).style;
          elementmode.display = (!elementmode.display) ? 'none' : '';
	}

	function deleteAlert(name){
	  var conBox = confirm("Are you sure you want to delete: " + name);
	  if(conBox){
            var url=window.location.href;
	    window.location = url + '&delete=' + name;
	  }else{
	    return;
  	  }
	}
    </script>

  </head>
  <body>


    <table border=0 cellpadding=5 cellspacing=0 valign=top align=center width=100%>
<?
  if ($_GET["delete"] != "") {
    $tbl = "btc_testbeds";
    $query="SELECT * FROM $tbl WHERE Testbed = \"$_GET[delete]\"";
    $result=mysql_query($query) or die(mysql_error());
    while($row = mysql_fetch_array($result)){
      $dev = $row["DevClass"];
      $table = "btc_" . $dev . "_inventory";
      $query2="UPDATE $table SET Testbed = \"\" WHERE Testbed = \"$_GET[delete]\"";
      $result2=mysql_query($query2) or die(mysql_error());
      $stat = "pass";
      echo "<tr id=" . $stat . " align=center><td>Deleted " . $_GET[delete] . " from " . $table . "</td></tr>";
    }

    $query="DELETE FROM $tbl WHERE Testbed = \"$_GET[delete]\"";
    $result=mysql_query($query) or die(mysql_error());
    $stat = "pass";
    echo "<tr id=" . $stat . " align=center><td>" . $_GET[delete] . " Deleted from " . $tbl . "</td></tr>";
  }
?>

    <tr align=left><th><h2>BTC Testbeds</h2></td></tr>

<?
    $get = "Testbed";
    $tbl = "btc_testbeds";
    $group = "Testbed";

    $query="SELECT $get FROM $tbl GROUP BY $group";
    $result=mysql_query($query) or die(mysql_error());
    // Print out result
    $tname="-" . strtolower($group). "[]";
    while($row = mysql_fetch_array($result)){

      $dev = $row[$get];
?>

    <tr><th>
      <table cellpadding=0 cellspacing=0 border=0 width=100%>
        <tr><th align=left><h4><a href="javascript:showhide('<? echo $dev; ?>');"><? echo $dev; ?></a></h4></th>
        <td align=right><a href="javascript:deleteAlert('<? echo $dev; ?>');">Delete Testbed</a></td></tr>
      </table>
    </th></tr>

    <tr><td>
    <span id="<? echo $dev ?>" style="display:none;">
    <table id=border width=100% valign=top border=1 frame=box rules=all cellpadding=5 cellspacing=0>
    <tr><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Bivio Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_bivio_inventory","Testbed",$dev,"bivio");
?>
    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>PC Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_pc_inventory","Testbed",$dev,"pc");
?>
    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>ADB Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_adb_inventory","Testbed",$dev,"adb");
?>
    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Switch Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_sw_inventory","Testbed",$dev,"sw");
?>
    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>SmartBits Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_smb_inventory","Testbed",$dev,"smb");
?>
    </td></tr></table>
    </table>

    </td><td valign=top width=20%>

    <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
    <tr><th align=left>Avalanche Devices</th></tr>
    <tr><td><table width=100% cellpadding=5 cellspacing=0>
<?
  // generate the select options list
  fetchWhere("Name","btc_aval_inventory","Testbed",$dev,"aval");
?>
    </td></tr></table>
    </table>


    </td></tr>
    </table>
    </span>

    </td></tr>

<?
    }
?>
    </table>
  </body>
</html>
