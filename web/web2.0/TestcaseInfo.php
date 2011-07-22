<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Testcase Info</title>

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

    </script>

  </head>
  <body>

    <table id=contents border=0 cellpadding=0 cellspacing=0 valign=top align=center width=100%>
      <tr><td>
        <div id="main">
        <ul id="secondary">

<?
  $L2 = array();
  $L2["Testcases"] = "testcases";
  $L2["Procedures"] = "procs";

  $default = "testcases";

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

      <tr><td align=center>
<?php

  $file = $_GET['show'] . ".php";
  include $file;

?>
    </td>
    </tr>
    </table>

  </body>
</html>
