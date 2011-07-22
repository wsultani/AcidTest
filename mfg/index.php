<html>
<head>
   <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center</title>

    <script type="text/javascript">
      function navto(inID) {
        var url=window.location.href;
        var header = url.split('?nav=',1);
        window.location = header + '?nav=' + inID;
      }
    </script>
</head>
<body>
<table id=border width=90% align=center valign=top border=0 rules=all>
  <tr>
  <td width=10%></td>
  <td align=center valign=center><h1><br>Bivio Test Center (BTC)<br></h1></td>
  </tr>
  <tr>
  <td valign=top>
    <table width=100% border=0 valign=top cellpadding=5>
      <tr><td align=right><button value=monitor onclick="navto(this.value)">Search</button></td></tr>
      <tr><td align=right><button value=exec onclick="navto(this.value)">ExecuteTest</button></td></tr>
    </table>
  </td>
  <td valign=top>
<?php

  switch ( $_GET['nav'] ) {
    case "config": {
      include 'config.php';
      break;
    }
    case "monitor": {
      include 'mansearch.php';
      break;
    }
    case "exec": {
      include 'manexec.php';
      break;
    }
  }  
?>
  </td>
  </tr>
</table>
</body>
</html>
