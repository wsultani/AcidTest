<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?
  function fetchWhere ($get,$tbl,$where,$match,$class) {
    $query="SELECT $get FROM $tbl WHERE $where =\"$match\"";
    $result=mysql_query($query) or die(mysql_error());

    $num=0;

    // Print out result
    while($row = mysql_fetch_array($result,MYSQL_ASSOC)){
      $out = "";
      while (list ($key, $val) = each($row)) {
        // Do not show the following fields
        if ( $key == "id" || $key == $where ) {
            continue;
        }
        $out .= "<tr><td width=10%><b>" . $key . "</b></td><td width=100%>" . $val ."</td></tr>\n";
      }

      echo $out;
      $num++;
    }
  }
?>

<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Testcases</title>

    <script type="text/javascript">
        function showhide(targetID) {
          //change target element mode
          var elementmode = document.getElementById(targetID).style;
          elementmode.display = (!elementmode.display) ? 'none' : '';
	}
    </script>

  </head>
  <body>
    <table border=0 cellpadding=5 cellspacing=0 valign=top align=center width=100%>
    <tr align=left><th><h2>BTC Tests</h2></th></tr>

<?
    $get = "Name";
    $tbl = "btc_tests";
    $group = "Name";

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
        <td align=right><a href="javascript:showhide('<? echo $dev; ?>');">Show/Hide</a></td></tr>
      </table>
    </th></tr>

    <tr><td>
    <span id="<? echo $dev ?>" style="display:none;">
    <table width=100% valign=top border=0 rules=none cellpadding=0 cellspacing=0>
    <tr><td valign=top width=50%>

    <table id="border" rules=all cellpadding=5 cellspacing=0 valign=top align=right width=100%>
<?
  // generate the select options list
  fetchWhere("*","btc_tests","Name",$dev,"bivio");
?>
    </td></tr></table>

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
