<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?
  function fetchGroup ($get,$tbl,$group,$selected ) {
    $query="SELECT $get FROM $tbl GROUP BY $group";
    $result=mysql_query($query) or die(mysql_error());
    // Print out result
    while($row = mysql_fetch_array($result)){
      if ( $row[$get] == $selected ) {
        echo "<option selected=\"yes\" value=\"" . $row[$get] . "\">" . $row[$get] ."</option>\n";
      } else {
        echo "<option value=\"" . $row[$get] . "\">" . $row[$get] ."</option>\n";
      }
    }
  }
?>

<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Test Results</title>
  </head>

    <script type="text/javascript">
      function selectShow(inID) {
        var url=window.location.href;
        var header = url.split('&show=',1);
        window.location = header + '&show=' + inID;
      }

      function update(inId, inVal) {
        var url=window.location.href;
        var header = url.split(('&' + inId + '=') ,1);
        if ( url.indexOf("?") != -1 ) {
          window.location = header + '&' + inId + '=' + inVal;
        } else {
          window.location = header + '?' + inId + '=' + inVal;
        }
      }

      function ChangeColor(tableRow, highLight) {
        if (highLight) {
          tableRow.style.backgroundColor = '#dcfac9';
          tableRow.style.cursor = 'pointer';
        } else {
          tableRow.style.backgroundColor = 'transparent';
        }
      }

      function DoNav(theUrl) {
        document.location.href = theUrl;
      }

    </script>

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

  <tr><td align=center>

<?
  $tbl = btc_builds;
  $count = "20";
  $sel = "Last $count Test Results";
  $query="SELECT * FROM $tbl ORDER BY id DESC LIMIT 0,$count";

?>
<!-------------------------------- Start of build case --------------------------------------------> 
<form name="build" action="" method=post>
    <table border=0 cellpadding=5 cellspacing=0 valign=top width=100%>
      <tr align=left><th><h2>Serial Number Search</h2></th><th></th></tr>
      <tr>
        <td>
          <input type=text name=serial size=50 /><input type=submit name=submit value=Search />
        </td>

        <td>
<!--
    <select name="build" onChange="update('build',this.value)">
    <tr align=left><th><h2>Select a Build</h2></th><th align=right><h2>Serial Number Search</h2></tr>
    <tr><td>
    <select name="build" onChange="update('build',this.value)">
    <option value="">Select a build</option>

<?
  //if ( $_GET['build'] != "" ) {
  //  $build = $_GET['build'];
  //}
  // generate the select options list
  //fetchGroup("Version","btc_builds","Version",$build);
?>

    </select>
-->
        </td>

      </tr>
    </table>
</form>
<!-------------------------------- End of build case --------------------------------------------> 

</td>
</tr>

  <div id="info_block" style="display: block">

<?
  $ignore = array("id", "DefectCount", "Log", "Version");
  $width = 40;

  if ( $_GET['build'] != "" ) {
    $sel = $build;
    $tbl = btc_builds;
    $where = Version;
    $ignore = array("id", "DefectCount", "Version");
    $width = 30;

    $query="SELECT * FROM $tbl WHERE $where=\"$sel\" ORDER BY id DESC";
  }

  if ( $_POST['serial'] != "" ) {
    $sel = $_POST['serial'];
    $tbl = btc_builds;
    $where = Serials;
    $ignore = array("id", "DefectCount", "Version");
    $width = 30;

    $query="SELECT * FROM $tbl WHERE $where like \"%$sel%\" ORDER BY id DESC";
  }


    echo "<tr align=left>\n";
    echo "<th><h2>$sel</h2></th>\n";
    echo "</tr><tr><td>\n";
    $result=mysql_query($query) or die(mysql_error());

    // Print out result
    $doonce = 0;
    $align = center;
    echo "<table id=border rules=all cellpadding=8 cellspacing=0 valign=top align=center width=100%>\n";
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      reset ($row);
      while (list ($key, $val) = each($row)) {
        $val = str_replace(". ", ".<br>", $val);

        // Do not show the following fields
        if (in_array($key, $ignore)) {
            continue;
        }

        $tmpval ="<td valign=top align =" . $align . ">" . $val . "</td>";

        if ( $key == "Comments" ) {
            if ( strlen($val) > 100 ) {
                $tmpval ="<td valign=top align =left width=" . $width . "%>" . $val . "</td>";
            } else {
                $tmpval ="<td valign=top align =left>" . $val . "</td>";
            }
        }

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

        $url = str_replace($_SERVER['DOCUMENT_ROOT'], "http://" . $_SERVER['SERVER_NAME'], $row["Log"]);

        // show build info and testbed info in rows, everything else in columns.
        $values = $values . $tmpval . "\n";
        $fields = $fields . "<th>" . $key . "</th>\n";
      }

      // write the fields as the header. do it only once.
      if ( $doonce == "0" ) {
          echo $fields . "\n";
          $doonce ++;
      }
      echo "<tr onmouseover=\"ChangeColor(this, true);\" onmouseout=\"ChangeColor(this, false);\" onclick=\"DoNav('" . $url . "');\">" . $values . "</tr>\n";
      $fields = "";
      $values = "";
    }
    echo "</table>\n";
?>

  </div>
    </td>
    </tr>
    </table>
  </body>
</html>
