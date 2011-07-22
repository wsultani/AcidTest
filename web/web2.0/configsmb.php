<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?
  $table="btc_smb_inventory";
  $empty=0;

  $fields = array(Name, IPAddress, SMBSlot, BVSlot);
  $req = array(Name, IPAddress, SMBSlot, BVSlot);

  foreach ($_POST as $key => $value) {
    if ( !in_array($key, $req) ) {
      continue;
    }

    if ( $value == "" ) {
      $stat = fail;
      $msg .= $key . " - cannot be empty.<br>";
      $empty ++;
    }
  }

  if ( $_POST["IPAddress"] != "" && $_POST["SMBSlot"] != "" && $_POST["BVSlot"] != "") {
    $ip = $_POST["IPAddress"];
    $smbs = $_POST["SMBSlot"];
    $bvs = $_POST["BVSlot"];
    $query = "SELECT * FROM $table WHERE IPAddress = \"$ip\" AND SMBSlot = \"$smbs\" AND BVSlot = \"$bvs\"";
    mysql_query ($query);
    $result = mysql_affected_rows();
    if ( $result > 0 ) {
      $stat = fail;
      $msg .= "smb system " . $ip . " with smbslot " . $smbs . " and bvslot " . $bvs . " is already Registered.<br>";
      $empty ++;
    }
  }

  if ( $empty == "0" && $_POST['IPAddress'] != "" ) {
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
    <title>Bivio Test Center - Config SmartBits Device</title>
  </head>
  <body>
    <form method="post">
    <table border=0 cellpadding=0 cellspacing=0 valign=top align=center width=100%>
    <tr align=left><th><h2>Configure SmartBits Device</h2></th></tr>
<?
    if ( $msg != "" ) {
      echo "<tr id=" . $stat . " align=center><td><b>" . $msg . "</b></td></tr>";
    }
?>

    <input type=hidden name="Class" value="smb">

    <tr><td>
    <table border=0 cellpadding=5 cellspacing=0 valign=top>

<?
    foreach ($fields as $key => $name) {
      if ( in_array($name, $req) ) {
        echo "<tr><td><b>" . $name . "</b></td><td><input name=" . $name . " width=100 value=" . $_POST[$name] . "><font size=0.6em> Required</td></tr>";
      } else {
        echo "<tr><td><b>" . $name . "</b></td><td><input name=" . $name . " width=100 value=" . $_POST[$name] . "></td></tr>";
      }
    }
?>

    </table>
    </td>
    </tr>

    <tr><th>
      <table border=0 width=100% cellpadding=0>
      <tr>
      <td width=50% align=center><input type="submit" value="Register SMB"></td>
      <td align=center><b><? echo '<a href="'.$_SERVER['REQUEST_URI'].'">Reset</a>'; ?></b></td>
      </tr>
      </table>
    </th>
    </tr>
    </table>
  </form>
  </body>
</html>
