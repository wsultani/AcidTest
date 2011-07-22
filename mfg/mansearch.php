<?
  // open connection to mysql database
  $user="btc_user";
  $password="btc_user";
  $database="man_db";
  mysql_connect(localhost,$user,$password);
  @mysql_select_db($database) or die( "Unable to select database");

?>

<html>
<head>
    <!-- <meta http-equiv="refresh" content="0" /> -->
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Manufaturing Tests</title>
</head>
<body>
<table id=border align=center valign=top border=1 frame=below rules=none cellpadding=5 cellspacing=10>
  <tr>
  <td align=center valign=center><h2>Search Test Results</h2></td>
  </tr>

  <tr><th align=left>Enter or Scan Serial Number</th></tr>

  <tr>
  <td valign=top>
    <form action="" method=post>
    <table border=0 valign=top cellpadding=10>
      <tr><td><input name=Serial size=100 /></td>
      <td><button type=submit value=Submit>Search</button></td></tr>
    </table>
    </form>
  </td>
  </tr>
</table>

<br>

<?php

  $tbl="btc_perf";
  $where="Serial";
  $sel=$_POST['Serial'];
  $format="row";
  $count="10";

  if ( $_POST['Serial'] != "") {
    $query="SELECT * FROM $tbl WHERE $where=\"$sel\"";
    $result=mysql_query($query) or die(mysql_error());
    $header="<tr><td><h3>Serial Number:</h3></td><td><h2>$sel</h2></td></tr>";
  } else {
    $query="SELECT * FROM $tbl ORDER BY id DESC LIMIT 0,$count";
    $result=mysql_query($query) or die(mysql_error());
    $header="<tr><td><h2>Last $count Test Results</h2></td></tr>";
  }
    // Print out result
    $doonce = 0;
    $align = center;

    echo "<table align=center border=0 cellpadding=5>" . $header . "</table>\n";

    echo "<table id=border rules=all border=1 cellpadding=8 cellspacing=0 valign=top align=center>\n";
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      reset ($row);
      while (list ($key, $val) = each($row)) {
        $val = str_replace(". ", ".<br>", $val);

        // Do not show the following fields
        if ( $key == "id" || $key == $where ) {
            continue;
        }

        $tmpval ="<td valign=top align =" . $align . ">" . $val . "</td>";

        if ( $key == "PassRate") {
            switch (TRUE) {
                case ($val == 100):
                    $bgc = pass;
                    break;
                case ($val < 100 && $val > 90):
                    $bgc = warning;
                    break;
                case ($val < 90):
                    $bgc = fail;
                    break;
            }
            $val = $val . "%";
            $tmpval ="<td id=$bgc valign=top align =" . $align . ">" . $val . "</td>";
        }

        // if log then make it a link to the log.
        if ( $key == "Log" ) {
          $localhost = "http://" . $_SERVER['SERVER_ADDR'] . "/";
          $url = str_replace("/var/www/html/", $localhost, $val);
          $clean = ereg_replace(".*/mfg/","", $val);
          $tmpval = "<td valign=top align=" . $align . "><a href=" . $url . ">" . $clean . "</a></td>";
        }

        if ($format == "row") {
          $values = $values . $tmpval . "\n";
          $fields = $fields . "<th>" . $key . "</th>\n";
        } else {
          $values = "";
          $fields = $fields . "<tr>" . "<th>" . $key . "</th>" . $tmpval . "</tr>\n";
        }
      }

      // write the fields as the header. do it only once.
      if ( $doonce == "0" ) {
          echo $fields . "\n";
          $doonce ++;
      }
      echo "<tr>" . $values . "</tr>\n";
      $fields = "";
      $values = "";
    }
    echo "</table>\n";
  //}

  //close the mysql connection
  mysql_close();
?>

</body>
</html>
