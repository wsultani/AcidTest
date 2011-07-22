<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?

function plotGraph ($query,$ignore,$id,$title) {

  //echo "<br<br>Query = " . $query;
  //echo "<br><br>Id = " . $id;

  $tbl = "btc_perf";
  $where = $_POST['Build'];
  $result=mysql_query($query) or die(mysql_error());

  $num_rows = mysql_num_rows($result);

  if($num_rows == NULL) {
    return NULL;
  }

  $p = new GNUPlot();
  $img="temp/test" .$id. ".png";
  $p->setTitle("Troughput Trending for $where");

  $doonce = 0;

  $rid = 1;

  $tics = array();

  $arrayB = array();
  $arrayG = array();

  while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
    $dq_msg="";
    reset ($row);

    $data = new PGData('test Data');

    while (list ($key, $val) = each($row)) {

      // ignore keys
      if (in_array($key, $ignore)) {
        continue;
      }

      if ($key == "id") {
        $val = $rid;
        $rid ++;
      }

      if ($key == "Build" && $_POST['Name'] != "") {
        $tcname="$val";
        //continue;
      }

      if ($key == "Name" && $_POST['Build'] != "") {
        $tcname="$val";
        //continue;
      }

      if ($val == "") {
        continue;
      }

      if ($key == "CPU" && $val == "0") {
        $val = "All";
      }

      // ignore FPGA for now since some builds do not have
      // the FPGA info and the cells will not line up
      // re enable this feature in the future
      if ($key == "FPGA" && $_POST['GBuild'] != "") {
        continue;
      }

      if ( $doonce == "0" ) {
        $header .= "<th>" . $key . "</th>\n";
        $tics[] = $key;
      }

      //$vals .= "<td align=center " . $css_id . ">" . $val . "</td>\n";

      $data->addDataEntry( array($key, $val) );

      $varray[$key] = $val;

    }

    $doonce ++;
    $data->changeLegend($varray['id']);
    $p->plotData( $data, 'linespoints', '1:($2)' );

    if ( $_GET['show'] == "compare") {
      $tcname = $row['id'];
    }

    if ( $row['Build'] == $_POST['GBuild'] && $_POST['GBuild'] != $_POST['Build']) {
      $arrayG[$tcname] = $varray;
    } else {
      $arrayB[$tcname] = $varray;
    }

  }

  //echo "<br> arrayB <br>" . print_r($arrayB) . "<br>";
  //echo "<br> arrayG <br>" . print_r($arrayG) . "<br>";

  $delta = 1;

  foreach ($arrayB as $_tcname => $_val) {
    foreach ($_val as $_key => $val) {
      if (isset($arrayG[$_tcname][$_key])) {
        $gval = $arrayG[$_tcname][$_key];

        $css_id = "bgcolor=white";
        $gcss_id = "";

        if ($_key != id) {
          if ($val < $gval && $val > ($gval - $delta)) {
            $css_id = "id=warning";
            $gcss_id = "";
          }
        
          if ($val >= ($gval + $delta)) {
            $css_id = "id=pass";
            $gcss_id = "";
          }

          if ($val < ($gval - $delta)) {
            $css_id = "id=fail";
            $gcss_id = "";
          }
        }

        $gvals .= "<td align=center " . $gcss_id . ">" . $gval . "</td>\n";
      }

      $tvals .= "<td align=center " . $css_id . ">" . $val . "</td>\n";
    }

    $gcss_id = "id=ref";
    $tvals = "</td>\n" . $tvals . "\n</tr>";

    if ($_POST['GBuild'] != "" && $gvals != "") {
      $vals .= "<tr " . $gcss_id . ">\n</td>\n" . $gvals . "\n</tr><tr>\n";
    } else {
      $vals .= "<tr>";
    }

    $vals .= $tvals;

    $fvals .= $vals . "<tr><td colspan=50><br></td></tr>";
    $tvals = "";
    $vals = "";
    $gvals = "";

  }

  $tmphead = $header;

  $jtics = "rotate (". join(", ", $tics) . ")";

  $p->setDimLabel(x, "Packet Size (with CRC)");
  $p->setDimLabel(y, "% Throughput");
  $p->set("key outside");
  $p->setSize(1, 1);
  $p->set("term png size 1400, 500");
  $p->setRange(y, 0, 100);
  //$p->setTics(x, "$jtics");
  $p->export($img);
  $p->close();

  echo "\n<table id=border width=100% align=left valign=top border=1 rules=all cellpadding=0>";
  echo "\n<tr><th align=left><h2>" . $_POST['Port'] . " Port Card - " . $title . "</h2></th></tr>";
  echo "\n<tr><td><img src=" . $img . " border=0></img></td></tr>";
  echo "\n<tr><td>";
  echo "\n<table id=border-rowonly width=100% align=left valign=top border=0 rules=all cellpadding=2><tr>";
  echo $tmphead;
  echo "\n</tr><tr>\n";
  echo $fvals;
  echo "</tr></table>\n";
  echo "</td></tr>\n";

  if ($_POST['GBuild'] != "") {
    echo "<tr><td>\n";
    echo "<table border=0 cellpadding=5 cellspacing=0>\n";
    echo "<tr><td><b>Legend</b></td><td></td></tr>\n";
    echo "<tr><td " . $gcss_id . "></td><td>Reference build</td></tr>\n";
    echo "<tr><td id=pass></td><td>Test value greater then tolerance (" . $delta . "%)</td></tr>\n";
    echo "<tr><td id=warning></td><td>Test value within tolerance (" . $delta . "%)</td></tr>\n";
    echo "<tr><td id=fail></td><td>Test value less then tolerance</td></tr>\n";
    echo "<tr><td></td><td></td></tr></table>\n";
  }

  echo "</td></tr></table>\n";
}


function fetchGroup ($get,$tbl,$group,$intype,$name) {

  if ($intype == "option") {
    echo "<select name=" . $name . " onchange=\"this.form.submit();\">\n";
    echo "<option value=\"\">Select a " . $get . "</option>\n";
  }

  $query="SELECT $get FROM $tbl GROUP BY $group";
  $result=mysql_query($query) or die(mysql_error());

  $selected = $_POST[$name];

  // Print out result
  $tname= $name . "[]";
  while($row = mysql_fetch_array($result)){

    $style = "";
    $val = $row[$get];

    if($get == "Build" || $get == "GBuild") {
      $altbuild['5.1.1.8'] = '5.1.2';
      $tmp = split(" ", $row[$get]);
      $val = $tmp[0];

      if (array_key_exists($val, $altbuild)) {
        $val = $val . " - (" . $altbuild[$val] . ")";
      }

      if(preg_match('/^\d+\.\d+\.\d+[\s|-]/', $row[$get], $match)) {
        $style = "font-weight: bold;";
      }
    }

    switch ($intype) {
      case "option":
        if ( $row[$get] == $selected ) {
          echo "<option selected=\"yes\" style=\"" . $style . "\" value=\"" . $row[$get] . "\">" . $val ."</option>\n";
        } else {
          echo "<option style=\"" . $style . "\" value=\"" . $row[$get] . "\">" . $val ."</option>\n";
        }
        break;
      case "checkbox":
       if ( $row[$get] == $selected ) {
         echo "<tr><td><input type=checkbox name=\"$tname\" selected=\"yes\" value=\"" . $row[$get] . "\">" . $val ."</td></tr>\n";
       } else {
         echo "<tr><td><input type=checkbox name=\"$tname\" value=\"" . $row[$get] . "\">" . $val ."</td></tr>\n";
       }
        break;
     }
  }
  if ($intype == "option") {
    echo "</select>\n";
  }
}

function fetchWhere ($get,$tbl,$where,$intype,$grp,$name) {

  if ($intype == "option") {
    echo "<select name=" . $name . " onchange=\"this.form.submit();\">\n";
    echo "<option value=\"\">Select a " . $get . "</option>\n";
  } else {
    echo "<table width=100% align=center valign=top border=0 rules=none>";
  }

  //$where must be an array.
  foreach ($where as $key => $val) {
    $str[] = $key . " = \"" . $val . "\"";
  }

  $wstr = join(" AND ", $str);

  if ($grp != "") {
    $query="SELECT $get FROM $tbl WHERE $wstr GROUP BY $grp";
  } else {
    $query="SELECT $get FROM $tbl WHERE $wstr";
  }

  $result=mysql_query($query) or die(mysql_error());

  $selected = $_POST[$name];

  // Print out result
  $tname= $name . "[]";
  while($row = mysql_fetch_array($result)){

    $style = "";
    $val = $row[$get];

    if($get == "Build" || $get == "GBuild") {
      $altbuild['5.1.1.8'] = '5.1.2'; 
      $tmp = split(" ", $row[$get]);
      $val = $tmp[0];

      if (array_key_exists($val, $altbuild)) {
        if(preg_match('/^\d+\.\d+\.\d+[\s|-]?/', $altbuild[$val], $match)) {
          $style = "font-weight: bold;";
        }
        $val = $val . " - (" . $altbuild[$val] . ")";
      } else {
        $val = $val . " - (" . $val . ")";
      }

      if(preg_match('/^\d+\.\d+\.\d+[\s|-]/', $row[$get], $match)) {
        $style = "font-weight: bold;";
      }
    }

    switch ($intype) {
      case "option":
        if ( $row[$get] == $selected ) {
          echo "<option selected=\"yes\" style=\"" . $style . "\" value=\"" . $row[$get] . "\">" . $val . "</option>\n";
        } else {
          echo "<option style=\"" . $style . "\" value=\"" . $row[$get] . "\">" . $val . "</option>\n";
        }
        break;
      case "checkbox":
       if ( $row[$get] == $selected ) {
         echo "<tr><td><input type=checkbox name=\"$tname\" selected=\"yes\" value=\"" . $val . "\">" . $row[$get] ."</td></tr>\n";
       } else {
         echo "<tr><td><input type=checkbox name=\"$tname\" value=\"" . $row[$get] . "\">" . $val ."</td></tr>\n";
       }
        break;
     }
  }
  if ($intype == "option") {
    echo "</select>\n";
  } else {
    echo "</table>";
  }
}
?>

<?
include('PHP_GnuPlot.php');
?>

<html>
<head>
   <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Performance</title>

    <script type="text/javascript">
      function selectShow(inID) {
        var url=window.location.href;
        if ( url.indexOf("?") == -1 ) {
          url += '?';
        }
        var header = url.split('&show=',1);
        window.location = header + '&show=' + inID;
      }

      function update(inId, inVal) {
        var url=window.location.href;
        var header = url.split(('&' + inId + '=') ,1);
        window.location = header + '&' + inId + '=' + inVal;
      }

      function formPost(fname, build, port, platform) {     

        var fields = ["Build","Port","Platform","action"];

        for ( var i in fields ) {

          key = fields[i];

          if ( key == "Build" ) {
              val = build;
          }

          if ( key == "Port" ) {
              val = port;
          }

          if ( key == "Platform" ) {
              val = platform;
          }

          if ( key == "action" ) {
              val = "Plot";
          }

          var input = document.createElement("input");
          input.setAttribute("type", "hidden");
          input.setAttribute("name", key);
          input.setAttribute("value", val);
          fname.appendChild(input);
        }
        fname.submit();
      }

      function winHeight(linkId, winId, winHeight) {
        var win = document.getElementById(winId);
        var ln = document.getElementById(linkId);
        if(win.style.height == winHeight){
            win.style.height = '100%';
            ln.innerHTML = '[+] Collapse';
        } else {
            win.style.height = winHeight;
            ln.innerHTML = '[-] Expand';
        }
      }

    </script>

</head>
<body>

    <table id=contents border=0 cellpadding=0 cellspacing=0 valign=top align=center width=100%>
      <tr><td>
        <div id="main">
        <ul id="secondary">

<?
  $L2 = array();
  $L2["Build Summary"] = "summary";
  $L2["Testcase Summary"] = "compare";
  $L2["NIM coverage Summary"] = "nim";
  $L2["Upload Manual Test Results"] = "stc";

  $default = "summary";

  foreach ($L2 as $key=>$val) {

    if ($_GET['show'] == "") {
      $_GET['show'] = $default;
    }

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
  if ($_GET['show'] == "summary") {
    $colw = "20%";
?>
  <tr>
  <td>
    <form action="" method="post">
    <input type=hidden name=type value=summary>
      <table id=border width=100% align=center valign=top border=1 rules=all>
        <tr>
        <th width=<? echo $colw; ?>>Build</th>
        <th width=<? echo $colw; ?>>Platform</th>
        <th width=<? echo $colw; ?>>Port</th>
        <th width=<? echo $colw; ?>>Reference Build (Optional)</th>
        <th width=<? echo $colw; ?>>Plot Graph</th>
        </tr>

        <td align=center>
<?
  // generate the select options list
  fetchGroup("Build","btc_perf","Build","option","Build");
?>
        </td>

        <td align=center>

          <table width=100% align=center valign=top border=0 rules=none>
            <td align=center>
<?
  $where["Build"] = $_POST['Build'];
  // generate the select options list
  fetchWhere("Platform","btc_perf",$where,"option","Platform","Platform");
  //fetchGroup("Port","btc_perf","Port","option");
?>
            </td>
          </table>

        </td>

        <td align=center>

          <table width=100% align=center valign=top border=0 rules=none>
            <td align=center>
<?
  $where["Build"] = $_POST['Build'];
  $where["Platform"] = $_POST['Platform'];
  // generate the select options list
  fetchWhere("Port","btc_perf",$where,"option","Port","Port");
  //fetchGroup("Port","btc_perf","Port","option");
?>
              </select>
            </td>
          </table>

        </td>

        <td align=center>
<?
  $bwhere["Platform"] = $_POST['Platform'];
  $bwhere["Port"] = $_POST['Port'];
  // generate the select options list
  fetchWhere("Build","btc_perf",$bwhere,"option","Build","GBuild");
  //fetchGroup("Build","btc_perf","Build","option","GBuild");
?>
        </td>

        <td align=center>
          <input type="submit" name=action value="Plot" />
        </td>

        </tr>
      </table>
    </form>
  </td>
  </tr>
<?
  }
?>

<?
  if ($_GET['show'] == "compare") {
    $colw = "25%";
?>
  <tr>
  <td>
    <form action="" method="post">
    <input type=hidden name=type value=compare>
      <table id=border width=100% align=center valign=top border=1 rules=all>
        <tr>
        <th width=<? echo $colw; ?>>Testcase</th>
        <th width=<? echo $colw; ?>%>Port</th>
        <th width=<? echo $colw; ?>%>Builds</th>
        <th width=<? echo $colw; ?>%>Plot Graph</th>
        </tr>

        <tr>
        <td align=center>
<?
  // generate the select options list
  fetchGroup("Name","btc_perf","Name","option","Name");
?>
        </td>

        <td align=center>
<?
  $where["Name"] = $_POST['Name'];
  // generate the select options list
  fetchWhere("Port","btc_perf",$where,"option","Port","Port");
  //fetchGroup("Build","btc_perf","Build","checkbox");
?>
        </td>

        <td align=center>
<?
  $where["Name"] = $_POST['Name'];
  $where["Port"] = $_POST['Port'];
  // generate the select options list
  fetchWhere("Build","btc_perf",$where,"checkbox","Build","Build");
  //fetchGroup("Build","btc_perf","Build","checkbox");
?>
        </td>

        <td align=center>
          <input type="submit" name=action value="Plot" />
        </td>

        </tr>
      </table>
    </form>
  </td>
  </tr>
<?
  }
?>

<?
  if ($_GET['show'] == "nim") {
    $tbl = "btc_perf";
    $q = "SELECT Port FROM $tbl GROUP BY Port ORDER BY Port ASC";
    $result=mysql_query($q) or die(mysql_error());

    $num_rows = mysql_num_rows($result);

    if($num_rows == NULL) {
      return NULL;
    }

    $doonce = 0;
    $parr = array();

    while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
      reset ($row);

      while (list ($key, $val) = each($row)) {

        // since 12 port combination is no longer supported, ignore values.
        if ($val == "12x4" || $val == "12x2") {
          continue;
        }

        $ports .= "<th>" . $val . "</th>\n";
        $parr[] = $val; 
      }
    }
?>

  <tr>
  <td>
    <table width=100% align=center valign=top border=0 cellpadding=3>
      <tr><th><h2 align=left>Select a Build</h2></th></tr>
      <tr><td>
      <form action="" method="post">
<?
  // generate the select options list
  fetchGroup("Build","btc_perf","Build","option","Build");
?>
      </form>
      </td>
      </tr>
    </table>
  </td>
  </tr>

  <tr>
  <td>
    <form action="http://<? echo $_SERVER['SERVER_NAME']; ?>/automation/web/web2.0/?nav=Performance" method="post" name="nim">
    <input type=hidden name=type value=summary>

      <table id=border width=100% align=center valign=top border=1 rules=all cellpadding=3>
        <tr>
        <th>Build</th>
        <th>Platform</th>
        <? echo $ports; ?>
        </tr>

<?

    if($_POST["Build"] != "") {
      $q = "SELECT Build, Platform FROM $tbl WHERE Build = \"" .  $_POST["Build"] . "\" GROUP BY Build, Platform ORDER BY Build DESC";
    } else {
      $q = "SELECT Build, Platform FROM $tbl GROUP BY Build, Platform ORDER BY Build DESC";
    }

    $bresult=mysql_query($q) or die(mysql_error());

    $num_rows = mysql_num_rows($bresult);

    if($num_rows == NULL) {
      return NULL;
    }

    while($brow = mysql_fetch_array($bresult,MYSQL_ASSOC)) {
      reset ($brow);
      $mbuild = $brow["Build"];
      $platform = $brow["Platform"];
      $nav = "summary";

      echo "<tr><td>" . $mbuild . "</a></td><td>" . $platform . "</a></td>";

      foreach ($parr as $p) {
        $q = "SELECT * FROM $tbl WHERE Build = \"$mbuild\" AND Platform = \"$platform\" AND Port = \"$p\" AND `440` > 0";
        $presult=mysql_query($q) or die(mysql_error());

        $num_rows = mysql_num_rows($presult);
        if($num_rows <= 0) {
          echo "<td id=fail align=center>No</td>\n";
        } else {
          echo "<td id=pass align=center><a href=\"javascript:formPost(nim, '" . $mbuild . "', '" . $p . "', '" . $platform . "');\">Yes</a></td>\n";
        }
      }
    }
  }
?>
  </form>
  </td>
  </tr>

<?
  if ($_GET['show'] == "stc") {
  $colw = "33%";

  if ($_POST['autopop'] != "") {
    $data = $_POST['autopop'];
    
    if(preg_match('/V: Version ([\d|.]+) \(Build (\d+)\).*L: (.*System)/s', $data, $bmatch)) {
        $_POST['Build'] = $bmatch[1] . " " . $bmatch[2] . " " . $bmatch[3]; 
    }

    if(preg_match('/(Back Plane)\s+FPGA\s+version:\s+(\d+)/s', $data, $match)) {
      $fpga[$match[1]] = $match[2];
    }

    if(preg_match('/product:(.*)/', $data, $match)) {
      $_POST['Platform'] = $match[1];
    }

    if(preg_match_all('/HW Slot No.*?(?=HW Slot No.)/s', $data, $match)) {
      foreach($match[0] as $mk => $mv) {
        if(preg_match('/HW\s+Slot\s+No.\s+(\d+).*ROM\s+version:\s+(\S+)\s+-\s+ENABLE.*(APC|NPC)\s+XPC\s+FPGA\s+version:\s+(\d+)/s', $mv, $match)) {
          $rom["$match[3]$match[1]"] = $match[2];
          $fpga["$match[3]$match[1]"] = $match[4];
        }
      }
    }
    $_POST['FPGA'] = join("/", $fpga);
    $_POST['ROM'] = join("/", $rom);
  }
?>

  <tr>
  <td>
    <form enctype="multipart/form-data" action="" method="post">
      <table id=border width=100% align=center valign=top border=1 rules=all cellpadding=5>
        <tr>
        <th align=left width=<? echo $colw; ?>>Testcase Name</th>
        <th align=left width=<? echo $colw; ?>>Build</th>
        <th align=left width=<? echo $colw; ?>>FPGA</th>
        </tr>

        <tr>
        <td valign=top><? fetchGroup("Name","btc_perf","Name","option","Name"); ?><br><br>
<?
  if ($_POST['Name'] != "") {
    $where["Name"] = $_POST['Name'];
    $tbl = btc_perf;
    $get = "Sbypass, Jumbo, Qos, CFG, Speed";
    $char = preg_split("/,/", $get);

    //$where must be an array.
    foreach ($where as $key => $val) {
      $str[] = $key . " = \"" . $val . "\"";
    }

    $wstr = join(" AND ", $str);

    $query="SELECT $get FROM $tbl WHERE $wstr GROUP BY 'Name'";

    $result=mysql_query($query) or die(mysql_error());

    echo "<table id=border width=100% align=center valign=top border=1 rules=rows cellpadding=5>";

    // Print out result
    while($row = mysql_fetch_array($result)){
      foreach ($char as $k => $v) {
        echo "<tr><th>" . $v . "</th><td width=100%>" . $row[$k] . "</td></tr>\n";
        echo "<input type=hidden name=" . $v . " value=\"" . $row[$k] . "\" />\n";
      }
    }
    echo "</table>";
  }
?>
        </td>
        <td valign=top><input size=40 type=text name=Build <?  echo " value=\"" . $_POST['Build'] . "\""; ?>>
        <input type=button name=auto value="Auto Populate" onclick="javascript:winHeight('', 'tb_window', '0px')"><br><br></td>
        <td valign=top><input size=40 type=text name=FPGA <?  echo " value=" . $_POST['FPGA']; ?>>
        <input type=button name=auto value="Auto Populate" onclick="javascript:winHeight('', 'tb_window', '0px')"><br><br></td>
        </tr>

        <tr>
        <th align=left width=<? echo $colw; ?>>Testbed</th>
        <th align=left width=<? echo $colw; ?>>ROM</th>
        <th align=left width=<? echo $colw; ?>>Platform</th>
        </tr>

        <tr>
        <td valign=top><input size=40 type=text name=Testbed <?  echo " value=" . $_POST['Testbed']; ?>><br><br></td>
        <td><input size=40 type=text name=ROM <?  echo " value=" . $_POST['ROM']; ?>>
        <input type=button name=auto value="Auto Populate" onclick="javascript:winHeight('', 'tb_window', '0px')"><br><br></td>
        <td><input size=40 type=text name=Platform <? echo " value=\"" . $_POST['Platform']. "\""; ?>>
        <input type=button name=auto value="Auto Populate" onclick="javascript:winHeight('', 'tb_window', '0px')"><br><br></td>
        </tr>

        <tr>
        <th align=left width=<? echo $colw; ?>>Port</th>
        <th align=left width=<? echo $colw; ?>>Results File</th>
        <th align=left width=<? echo $colw; ?>></th>
        </tr>

        <tr>
        <td valign=top><? fetchGroup("Port","btc_perf","Port","option","Port"); ?><br><br></td>
        <td valign=top>
          <input size=40 name="file" type="file" /><br />
        </td>
        <td valign=center align=middle>
          <input type="submit" name=upload value="GO!!" />
        </td>
        </tr>

      </table>
    </form>
  </td>
  </tr>

  <tr>
  <td>

<?
    if ($_POST['upload'] != "") {
      $table = btc_perf;

      unset($_POST['upload']);

      if($_FILES["file"]["name"] == "") {
        $msg .= "Results file - cannot be empty!<br>\n";
        $id = fail;
      } else {
        $allowedExtensions = array("txt", "csv");
        if (!in_array(end(explode(".", strtolower($_FILES["file"]["name"]))), $allowedExtensions)) { 
          $msg .= $_FILES["file"]["name"] . " is an invalid file type!<br>\n";
          $id = fail;
        }
      }

      foreach ($_POST as $pk => $pv) {
        if($pv == "") {
          $msg .= $pk . " - can not be empty!<br>\n";
          $id = fail;
        }
      }

      if($id != fail) {
        if ($_FILES["file"]["error"] > 0) {
          $msg .= "Error: " . $_FILES["file"]["error"] . "<br>\n";
          $id = fail;
        } else {
          $msg .= "File Name: " . $_FILES["file"]["name"] . "<br>\n";
          $msg .= "Type: " . $_FILES["file"]["type"] . "<br>\n";
          $msg .= "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br>\n";
          $msg .= "Stored in: " . $_FILES["file"]["tmp_name"] . "<br><br>\n";
    
          $myFile = $_FILES["file"]["tmp_name"];
          $fh = fopen($myFile, 'r');
          $theData = fread($fh, filesize($myFile));
          fclose($fh);
  
          if(preg_match('/Test Summary Table(.*)Graph: LatencyBar/s', $theData, $match)) {
            //echo "<pre>" . $match[1] . "</pre>";
            $msg .= "<table with=100% cellpading=0 border=0><tr>\n";
            $lines = split("\n", trim($match[1]));
            foreach($lines as $k => $v) {
              //echo "line - " . $v . "<br>\n";
              if(preg_match('/^(\d+),[\d|.]+,[\d|.]+,([\d|.]+)/', $v, $match)) {
                //echo $match[1] . " - " . $match[2] . "<br>\n";
                $res[$match[1]] = round($match[2], 2);
                }
            }

            $loc = "upload/" . time() . "_" . $_FILES["file"]["name"];
            if(move_uploaded_file($_FILES["file"]["tmp_name"], $loc)) {
              $msg .= "Saved file to : " . $loc . "<br><br>\n";
              $id = pass;
            } else {
              $msg .= "There was an error uploading the file to " . $loc . "<br>\n";
              $id = fail;
            }

            $res = $_POST + $res;

            $keys = implode("`, `", array_keys($res));
            $vals = implode("', '", $res);
            $sql_cmd = "INSERT INTO $table (`" . $keys . "`) VALUES ('" . $vals . "')";
            //$msg .= $sql_cmd . "<br><br>\n";
            
            mysql_query ($sql_cmd);
            $result = mysql_affected_rows();

            if ( $result <= 0 ) {
              $id = fail;
              $msg .= "Failed to insert data into table " . $table . "<br>\n";
            } else {
              $id = pass;
              $msg .= mysql_affected_rows() . " rows inserted into table " .  $table . "<br>\n";
            }

            $msg .= "<table id=border width=100% cellpadding=3 border=1 rules=all>";
            $msg .= "<tr><th>" . implode("</th><th>", array_keys($res)) . "</th></tr>\n";
            $msg .= "<tr bgcolor=white><td>" . implode("</td><td>", array_values($res)) . "</td></tr>\n";
            $msg .= "</table><br><br>\n";

          } else {
            $msg .= "No Match found :<br> <pre>" . $theData . "</pre>";
            $id = fail;
          }
        }
      }
      echo "<tr><td align=center id=" . $id . "><b>" . $msg . "</b></td></tr>";
    }
?>

  <tr><td>
    <div id=tb_window style="overflow:auto; width:100%; height: 0px;">
      <form action="" method=post>
        <input type=hidden name=Name value="<?php echo $_POST['Name']; ?>">
        <input type=hidden name=Testbed value="<?php echo $_POST['Testbed']; ?>">
        <input type=hidden name=Port value="<?php echo $_POST['Port']; ?>">
        <table id=border border=1 farem=box rules=all cellpadding=5 cellspacing=0 valign=top width=100%>
          <tr><th><h2>Copy and paste the output of "fwinfo", "cat /proc/branding_info" and "cat /etc/NRDIST" here!</h2></th></tr>
          <tr><td><textarea style="width:100%" rows=20 name=autopop></textarea><br>
          <input type=submit name=submit value=Submit /></td></tr>
        </table>
      </form>
    </div>
  </td></tr>

<?
  }

  if ($_POST['action'] == "Plot") {

    $tbl = "btc_perf";

    if ($_POST['type'] == "summary") {

      $querylist = array();

      $required = array("Build", "Platform", "Port");
      foreach ($required as $req) {
        if ($_POST[$req] == "") {
          $msg = "<tr><td id=fail align=center><b>Please select a " . $req . "</b></td></tr>";
          echo $msg;
          exit;
        }
      }

      $ignore = array("Build", "Testbed", "Port", "Platform");
      $hname = "Testcase";
      $where = $_POST['Build'];

      //code to used if checkbox is used.
      //$app = array();
      //foreach ($_POST['Port'] as $value) {
      //    $app[] = "Port = \"" . $value . "\"";
      //}
      //$app_build = join(" OR ", $app);
      //$app_joined = "(" . $app_build . ")";

      //code to used if select is used.
      $app_joined = "Port = \"" . $_POST['Port'] . "\"";

      $app_joined .= " AND Platform = \"" . $_POST['Platform'] . "\"";

      if ($_POST['GBuild'] != "") {
        $app_joined .= " AND (Build = \"" . $_POST['Build'] . "\"";
        $app_joined .= " OR Build = \"" . $_POST['GBuild'] . "\")";
      } else {
        $app_joined .= " AND Build = \"" . $_POST['Build'] . "\"";
      }

      // old grouping scheme based on old testcase naming.
      //$filterlist["Standard Test"] = " AND Jumbo = \"off\" AND Sbypass = \"off\" AND CPU = \"All\"";
      //$filterlist["Jumbo Enabled for Small Packet"] = " AND Jumbo = \"on\" AND Name NOT LIKE '%JUMBO'";
      //$filterlist["Softbypass Enabled"] = " AND Jumbo = \"off\" AND Sbypass = \"on\"";
      //$filterlist["Jumbo Enabled"] = " AND Jumbo = \"on\" AND Name LIKE '%JUMBO'";
      //$filterlist["CPU Test with QOS Disabled"] = " AND CPU != \"All\" AND Qos = \"off\"";
      //$filterlist["CPU Test with QOS Enabled"] = " AND CPU != \"All\" AND Qos = \"on\"";

      // new grouping based on new testcase scheme (01/05/2010)
      $filterlist["Softbypass Disabled"] = " AND Sbypass = \"off\" AND Name NOT LIKE '%JUMBOPKT'";
      $filterlist["Softbypass Enabled"] = " AND Sbypass = \"on\" AND Name NOT LIKE '%JUMBOPKT'";
      $filterlist["Jumbo Enabled"] = " AND Jumbo = \"on\" AND Name LIKE '%JUMBOPKT'";

      foreach ($filterlist as $title => $filter) {

        // mysql query for all results
        //$q = "SELECT * FROM $tbl WHERE " . $app_joined . $filter . " HAVING Build = \"$where\" ORDER BY Name, CPU";
        $q = "SELECT * FROM $tbl WHERE " . $app_joined . $filter . " ORDER BY Name, CPU";
        $querylist[$title] = $q;
      }

    }

    if ($_POST['type'] == "compare") {

      $required = array("Name", "Port", "Build");
      foreach ($required as $req) {
        if ($_POST[$req] == "") {
          $msg = "<tr><td id=fail align=center><b>Please select a " . $req . "</b></td></tr>";
          echo $msg;
          exit;
        }
      }

      $ignore = array("Name");
      $hname = "Build";
      $where = $_POST['Name'];
      $app = array();
      foreach ($_POST['Build'] as $value) {
          $app[] = "Build = \"" . $value . "\"";
      }
      $app_build = join(" OR ", $app);
      $app_joined = "(" . $app_build . ")";

      if ($_POST['Port'] != "") {

        //code to used if checkbox is used.
        //$app2 = array();
        //foreach ($_POST['Testbed'] as $value) {
        //  $app2[] = "Testbed = \"" . $value . "\"";
        //}
        //$app_testbed = join(" OR ", $app2);

        //code to used if select is used.
        $app_testbed = "Port = \"" . $_POST['Port'] . "\"";
        $app_joined .= " AND " . "(" . $app_testbed . ")";
        
      }

      //$query="SELECT * FROM $tbl WHERE Name = \"$where\" AND " . $app_joined;
      $query="SELECT * FROM $tbl WHERE " . $app_joined . " HAVING Name = \"$where\"";
      $querylist[$_POST['Name']] = $query;
    }
?>

  <tr>
  <td>
  <table width=100% align=center valign=top border=0 rules=none cellpadding=0 cellspacing=0>

<?
  $i = 1;
  foreach ($querylist as $t => $q) {
    echo "<tr><td valign=top style=\"padding-bottom: 20px\">";
    plotGraph($q,$ignore,$i,$t);
    echo "</td></tr>";
    $i ++;
  }
?>

  </table>

  </td>
  </tr>
<?
  }
?>

  </table>
</body>
</html>
