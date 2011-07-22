<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<html>
<head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Job Status</title>
</head>
<body>

  <table id=contents cellpadding=0 cellspacing=0 valign=top align=center width=100%>
    <tr><td>
      <div id="main">
      <ul id="secondary">

<?
  $L2 = array();

  foreach ($L2 as $key=>$val) {

    if ($_GET['show'] == $val) {
      echo "<li><span>" . $key . "</span></li>";
    } else {
      echo "<li><a href=\"#\" onclick=\"selectShow('" . $val . "')\">" . $key . "</a></li>";
    }
  }
?>
      </ul>
      </div>
    </td></tr>

    <tr><td><br><br></td></tr>
<?
  if ($_POST['Action'] != "") {
    $tbl = btc_scheduler;
    $query="DELETE FROM $tbl WHERE Job = " . $_POST['Action'];
    $result=mysql_query($query) or die(mysql_error());

    if ( $result <= 0 ) {
      $stat = fail;
      $msg = "Failed to delete Job " . $_POST['Action'] . " from " . $tbl;
    } else {
      // reset the mysql table auto_increment
      $query="ALTER TABLE $tbl AUTO_INCREMENT=0";
      $result=mysql_query($query) or die(mysql_error());
      $stat = pass;
      $msg = "Deleted Job " . $_POST['Action'] . " from " . $tbl;
    }
  }

  if ($_POST['PID'] != "") {
    // kill the process and all its childeren.
    $cmd = 'pstree -p '. $_POST[PID] . ' | grep -o "[0-9]\{2,50\}" | xargs kill';
    $ret = exec($cmd);
    //exec('kill '.$_POST[PID]);
    $stat = pass;
    echo "<tr id=" . $stat . " align=center><td><b>Kill signal sent to PID: " . $_POST['PID'] . "</b></td></tr>";
  }

  if ( $msg != "" ) {
    echo "<tr id=" . $stat . " align=center><td><b>" . $msg . "</b></td></tr>";
  }
?>

    <tr><th align=left><h2>Queued Jobs</h2></th></tr>

    <tr>
    <td>
<?
  $tbl = btc_scheduler;
  $sel = "Queued Jobs";
  $query="SELECT * FROM $tbl WHERE Status = \"\" OR Status = \"In Use\" ORDER BY Priority ASC, Job DESC";
  $result=mysql_query($query) or die(mysql_error());

  $dq_msg="<tr><td align=center><b>Queue is empty</b></td></tr>";

    // Print out result
    $doonce = 0;
    $align = center;
    echo "<form action=\"\" method=post>";
    echo "<table id=border rules=all cellpadding=8 cellspacing=0 valign=top align=left width=100%>\n";
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      $dq_msg="";
      reset ($row);
      while (list ($key, $val) = each($row)) {

        // do not process the hidden key RunTest
        if ($key == "PID" || $key == "Log") {
          continue;
        }

        if ($key == "Script") {
          $align = left;
        } else {
          $align = center;
        }

        if ($key == "Job") {
          $jid = $val;
        }

        //$val = str_replace(". ", ".<br>", $val);

        $tmpval ="<td valign=top align =" . $align . ">" . $val . "</td>";

        $values = $values . $tmpval . "\n";
        $fields = $fields . "<th>" . $key . "</th>\n";
      }

      // add the action coloumn for each job.
      $fields = $fields . "<th>Action</th>\n";
      $values = $values . "<td align=center>Remove Job<br><input type=submit name=Action value=" . $jid . "></input></td>";

      // write the fields as the header. do it only once.
      if ( $doonce == "0" ) {
          echo $fields . "\n";
          $doonce ++;
      }
      echo "<tr>" . $values . "</tr>\n";
      $fields = "";
      $values = "";
   }
   echo $dq_msg;
   echo "</table>\n";
   echo "</form>\n";
?>

    </td>
    </tr>

    <tr><td align=left><br><br></td></tr>

    <tr><th align=left><h2>Running Jobs</h2></th></tr>

    <tr>
    <td>
<?
  $tbl = btc_scheduler;
  $count = "10";
  $query="SELECT * FROM $tbl WHERE Status = \"Running\" ORDER BY Start DESC, Priority ASC";
  $result=mysql_query($query) or die(mysql_error());

    // Print out result
    $doonce = 0;
    $align = center;
    echo "<form action=\"\" method=post>";
    echo "<table id=border rules=all cellpadding=8 cellspacing=0 valign=top width=100%>\n";
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      reset ($row);

      if ( $row["PID"] != "" ) {
        $val = $row["PID"];

        // see if the process is still running.
        $running = exec("ps -eo pid | grep -w $val");

        if ( $running != "" ) {
            $query="Update $tbl SET Status = \"Running\" WHERE PID = \"$val\"";
        } else {
            $query="Update $tbl SET Status = \"Ran\" WHERE PID = \"$val\"";
        }
      }

      $res=mysql_query($query) or die(mysql_error());

      while (list ($key, $val) = each($row)) {

        if ( $key == "PID" ) {
          if ($running != "") {
            $val = "Kill PID<br><input type=submit name=PID value=" . $val . "></input>";
          }
        }

        if ($key == "Script") {
          $align = left;
        } else {
          $align = center;
        }

        if ( $key == "Status" ) {
          if ($running != "") {
            $val = "Running";
          }
        }

        $tmpval ="<td valign=top align =" . $align . ">" . $val . "</td>";

        // if log then make it a link to the log.
        if ( $key == "Log" ) {
          $url = "tail.php?file=" . $val;
          $tmpval = "<td valign=top align=" . $align . "><a target=_blank href=" . $url . ">" . $val . "</a></td>";
        }

        $values = $values . $tmpval . "\n";
        $fields = $fields . "<th>" . $key . "</th>\n";
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
   echo "</form>\n";
?>

    </td>
    </tr>

    <tr><td align=left><br><br></td></tr>

    <tr><th align=left><h2>Completed Jobs</h2></th></tr>

    <tr>
    <td>
<?
  $tbl = btc_scheduler;
  $count = "10";
  $query="SELECT * FROM $tbl WHERE Status = \"Ran\" ORDER BY Start DESC, Priority ASC LIMIT 0,$count";
  $result=mysql_query($query) or die(mysql_error());

    // Print out result
    $doonce = 0;
    $align = center;
    echo "<form action=\"\" method=post>";
    echo "<table id=border rules=all cellpadding=8 cellspacing=0 valign=top width=100%>\n";
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      reset ($row);

      if ( $row["PID"] != "" ) {
        $val = $row["PID"];
        // see if the process is still running.
        $running = exec("ps -eo pid | grep -w $val");
      }

      while (list ($key, $val) = each($row)) {

        if ( $key == "PID" ) {
          if ($running != "") {
            $val = "Kill PID<br><input type=submit name=PID value=" . $val . "></input>";
          }
        }

        if ($key == "Script") {
          $align = left;
        } else {
          $align = center;
        }

        if ( $key == "Status" ) {
          if ($running != "") {
            $val = "Running";
          }
        }

        $tmpval ="<td valign=top align =" . $align . ">" . $val . "</td>";

        // if log then make it a link to the log.
        if ( $key == "Log" ) {
          $url = "tail.php?file=" . $val;
          $tmpval = "<td valign=top align=" . $align . "><a target=_blank href=" . $url . ">" . $val . "</a></td>";
        }

        $values = $values . $tmpval . "\n";
        $fields = $fields . "<th>" . $key . "</th>\n";
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
   echo "</form>\n";
?>

</td>
</tr>
</table>
</body>
</html>
