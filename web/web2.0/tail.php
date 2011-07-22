<html>

<?
  $myFile = $_GET['file'];
  $fh = fopen($myFile, 'r');
  $theData = fread($fh, filesize($myFile));
  fclose($fh);
  $refrate = $_GET['refresh'];
 ?>

<head>
    <script type="text/javascript">
      function update(inId, inVal) {
        var url=window.location.href;
        var header = url.split(('&' + inId + '=') ,1);
        window.location = header + '&' + inId + '=' + inVal;
      }
    </script>

    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <meta http-equiv="refresh" content="<? echo $refrate; ?>" />
<title><? echo $myFile; ?></title>
</head>

<body>

  <table width=100% cellpadding=5 cellspacing=0 border=0 style="position:fixed; top:0; left:0;">
    <tr width=50%><th>Auto-Refresh Page : <select name=refresh onchange="update('refresh',this.value)">
            <option value=never <? if ($_GET['refresh'] == never) { echo "selected"; } ?>>Never</option>
            <option value=20 <? if ($_GET['refresh'] == 50) { echo "selected"; } ?>>50 sec</option>
            <option value=20 <? if ($_GET['refresh'] == 20) { echo "selected"; } ?>>20 sec</option>
            <option value=10 <? if ($_GET['refresh'] == 10) { echo "selected"; } ?>>10 sec</option>
            <option value=5 <? if ($_GET['refresh'] == 5) { echo "selected"; } ?>>5 sec</option>
          </select>
      </th>
    </tr>
  </table>

  <pre>
<?
  echo $theData;
?>
  </pre>

</body>
</html>
