<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?
  $table="btc_sw_inventory";
  $empty=0;

  $fields = array(Type, Name, IPAddress, Console, Port, Login, Password, Connection, RemotePower, RPLogin, RPPassword);
  $req = array(Type, Name, IPAddress, Console, Port, Login, Password, Connection);

  foreach ($_POST as $key => $value) {
    if ( !in_array($key, $req) ) {
      continue;
    }

    if (empty($value)) {
      $stat = fail;
      $msg = $msg . $key . " - cannot be empty.<br>";
      $empty ++;
    }

    if ($key == "Console" || $key == "IPAddress") {
      $query = "SELECT * FROM $table WHERE $key = \"$value\"";
      mysql_query ($query);
      $result = mysql_affected_rows();
        if ( $result > 0 ) {
        $stat = fail;
        $msg .= $key . " = " . $value . " is already Registered.<br>";
        $empty ++;
      }
    }
  }

  if ( $empty == "0" && $_POST['Class'] != "" ) {
    $keys = join(", ",(array_keys($_POST)));
    $vals = join("\", \"",(array_values($_POST)));

    $query = "INSERT INTO $table ($keys) VALUES (\"$vals\")";
    mysql_query ($query);
    $result = mysql_affected_rows();
    if ( $result <= 0 ) {
      $stat = fail;
      $msg = "Failed to insert data into table " . $table;
    } else { 
      $stat = pass;
      $msg = mysql_affected_rows() . " rows inserted into table " .  $table;
      $tb=$_POST['Testbed'];
    }
  }
?>

<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Config Switch Device</title>
  </head>
  <body>
    <form method="post">
    <table border=0 cellpadding=0 cellspacing=0 valign=top align=center width=100%>
    <tr align=left><th><h2>Configure Switch Device</h2></th></tr>
<?
    if ( $msg != "" ) {
      echo "<tr id=" . $stat . " align=center><td><b>" . $msg . "</b></td></tr>";
    }
?>

    <input type=hidden name="Class" value="switch">

    <tr><td>
    <table border=0 cellpadding=5 cellspacing=0 valign=top>

<?
    foreach ($fields as $key => $name) {

      $out = "<tr><td><b>$name</b></td><td><input name=$name width=100 value=$_POST[$name]>";

      if ( $name == "Connection" ) {
        $out = "<tr><td><b>$name</b></td>";
        $out .= "<td><select name=$name width=100 style=\"width:145px\">";
        $out .= "<option value=\"\">Select ...</option>";
        $out .= "<option value=console>Console</option>";
        $out .= "<option value=telnet>Telnet</option>";
        $out .= "<option value=ssh>SSH</option>";
        $out .= "</select>";
      }

      if ( in_array($name, $req) ) {
        $out .= " <font size=0.6em>Required</td></tr>";
      } else {
        $out .= "</td></tr>";
      }
      echo $out;
      $out = "";
    }
?>

    </table>
    </td>
    </tr>

    <tr><th>
      <table border=0 width=100% cellpadding=0>
      <tr>
      <td width=50% align=center><input type="submit" value="Register Switch" /></td>
      <td align=center><b><? echo '<a href="'.$_SERVER['REQUEST_URI'].'">Reset</a>'; ?></b></td>
      </tr>
      </table>
    </th>
    </tr>
    </table>
  </form>
  </body>
</html>
