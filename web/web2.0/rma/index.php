<?
  // open connection to mysql database
  include 'dbconnect.php';

  $refrate = $_GET['refresh'];
?>

<html>
<head>
   <meta http-equiv="refresh" content="<? echo $refrate; ?>" />
   <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center</title>

    <script type="text/javascript">
      function navto(inID) {
        var url=window.location.href;
        var header = url.split('?nav=',1);
        window.location = header + '?nav=' + inID;
      }

      function update(inId, inVal) {
        var url=window.location.href;
        var header = url.split(('&' + inId + '=') ,1);
        window.location = header + '&' + inId + '=' + inVal;
      }
    </script>
</head>
<body>

  <table width=100% cellpadding=5 cellspacing=0 border=0 style="position:fixed; top:0; left:0;">
    <tr width=50%><th>Auto-Refresh Page : <select name=refresh onchange="update('refresh',this.value)">
            <option value=never <? if ($_GET['refresh'] == never) { echo "selected"; } ?>>Never</option>
            <option value=5 <? if ($_GET['refresh'] == 5) { echo "selected"; } ?>>5 sec</option>
            <option value=10 <? if ($_GET['refresh'] == 10) { echo "selected"; } ?>>10 sec</option>
            <option value=20 <? if ($_GET['refresh'] == 20) { echo "selected"; } ?>>20 sec</option>
            <option value=50 <? if ($_GET['refresh'] == 50) { echo "selected"; } ?>>50 sec</option>
          </select>
      </th>
    </tr>
  </table>

<table width=90% align=center valign=top border=0>
  <tr>
  <td align=left valign=center><h1>Bivio Test Center (BTC)</h1></td>
  </tr>

  <tr><td>
    <ul id="primary">

<?
  $L1 = array();
  $L1["Test Results"] = "TestResults";

  //Removing perdformance tab for RMA
  //$L1["Performance"] = "Performance";

  $L1["Add Jobs"] = "AddJobs";
  $L1["Job Status"] = "JobStatus";
  $L1["Testcase Info"] = "TestcaseInfo";
  $L1["Testbed"] = "TestbedInfo";

  $default = "TestResults";

  foreach ($L1 as $key=>$val) {

    if ($_GET['nav'] == "") {
      $_GET['nav'] = $default;
    }
  
    if ($_GET['nav'] == $val) {
      $cur = " class=\"current\"";
    } else {
      $cur = "";
    }

    echo "<li><a href=\"#\" onclick=\"navto('" . $val . "')\"" . $cur . ">" . $key . "</a></li>";

  }
?>
    </ul>
  </td></tr>

  <tr>
  <td valign=top>

<?php

  $file = $_GET['nav'] . ".php";
  include $file;

?>
  </td>
  </tr>
</table>
</body>
</html>
