<!-- start of html page -->
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center</title>

    <script type="text/javascript">
      function selectShow(inID) {
        var url=window.location.href;
        var header = url.split('&show=',1);
        window.location = header + '&show=' + inID;
      }
      function update(inId, inVal) {
        var url=window.location.href;
        var header = url.split(('&' + inId + '=') ,1);
        window.location = header + '&' + inId + '=' + inVal;
      }

    </script>

  </head>
  <body>

    <table border=0 cellpadding=10 cellspacing=0 valign=top align=center width=100%>
    <tr align=center><td><h2>Testbed Configuration</h2></td></tr>
    <tr align=center><td>
    <table  border=0 cellpadding=5 cellspacing=0 valign=top>
    <tr>
    <td><button value=bv onclick="selectShow(this.value)">Config Bivio Device</button></td>
    <td><button value=pc onclick="selectShow(this.value)">Config PC Device</button></td>
    <td><button value=sw onclick="selectShow(this.value)">Config Switch Device</button></td>
    <td><button value=tb onclick="selectShow(this.value)">Config Testbeds</button></td>
    </tr>
    </table></td>
    <tr><td align=center>

<?
  if ( $_GET['show'] != "" ) {
    switch ( $_GET['show'] ) {
    case "bv":
      include 'configbv.php';
      break;

    case "pc":
      include 'configpc.php';
      break;

    case "sw":
      include 'configsw.php';
      break;

    case "tb":
      include 'configtb.php';
      break;
    }
  }
?>
    </td>
    </tr>
    </table>
  </body>
</html>
