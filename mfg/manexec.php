<html>
<head>
    <!-- <meta http-equiv="refresh" content="0" /> -->
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Manufaturing Tests</title>

    <script type="text/javascript">
      function selectShow(inID) {
        var url=window.location.href;
        var header = url.split('?Product=',1);
        window.location = header + '?Product=' + inID;
      }

      function update(inId, inVal) {
        var url=window.location.href;
        var header = url.split(('&' + inId + '=') ,1);
        window.location = header + '&' + inId + '=' + inVal;
      }

      function verify(msg, tag, val) {
        answer = confirm('Are you sure you want to run the following?\n\n' + msg + '\n');
        if (answer !=0) {
          update(tag, val);
        }
        return answer;
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

<table id=border width=90% align=center valign=top border=1 frame=below rules=none cellpadding=5 cellspacing=10>
  <tr>
  <td align=center valign=center><h2>Execute Manufacturing Test</h2></td>
  </tr>

  <tr><th align=left>Step 1: Select Product</th></tr>

  <tr>
  <td valign=top>
    <table border=0 valign=top cellpadding=10 width=100%>
      <tr><td><button disabled value=b2000 onclick="update('Product',this.value)">Bivio 2000</button></td>
      <td><button value=b7000 onclick="update('Product',this.value)">Bivio 7000</button></td>
      <td id=info>Select the appropriate product family. Specific tests will be available depending on the selected product.</td></tr>
    </table>
  </td>
  </tr>

<?php
  if ( $_GET['Product'] != "") {
    $sum = "Product : " . $_GET['Product'];
    switch ( $_GET['Product'] ) {
      case "b2000":
        $options = "<option value=\"\">Select Test</option>";
        $options = $options . "<option disabled value=\"4PortCopper\">4 Port Copper 10/100/1000</option>\n";
        $options = $options . "<option disabled value=\"4PortFiber\">4 Port Fiber</option>\n";
        $options = $options . "<option disabled value=\"4PortFiberWBypass\">4 Port Fiber W/Bypass</option>\n";
        $options = $options . "<option disabled value=\"8PortFiberCopper\">8 Port Copper</option>\n";
        break;
     case "b7000":
        $options = "<option value=\"\">Select Test</option>";
        $options = $options . "<option value=\"10G_2Port\">2 Port 10G</option>";
        $options = $options . "<option value=\"10G_4Port\">4 Port 10G</option>";
        $options = $options . "<option disabled value=\"12PortCopperWBypass\">12 Port Copper W/Bypass</option>";
        break;
    }
?>

  <tr><th align=left>Step 2: Select Type</th></tr>

  <tr>
  <td valign=top>
    <table border=0 valign=top cellpadding=10 width=100%>
      <tr><td><input type=radio name=Type value=Chassis <? if ( $_GET['Type'] == Chassis ) {echo checked;} ?> onChange="update('Type',this.value)">Chassis</input></td>
      <td><input type=radio name=Type value=IOCard <? if ( $_GET['Type'] == IOCard ) {echo checked;} ?> onChange="update('Type',this.value)">I/O Card</input></td>
      <td id=info>Select the specific deviec you wish to test.</td></tr>
    </table>
  </td>
  </tr>
<?
  }
?>

<?php
  if ( $_GET['Type'] != "") {
    $sum = $sum . ", Type : " . $_GET['Type'];
?>

  <tr><th align=left>Step 3: Select Testbed</th></tr>

  <tr>
  <td valign=top>
    <table border=0 valign=top cellpadding=10 cellspacing=0 width=100%>
      <tr><td>
        <select name=Testbed onChange="update('Testbed',this.value)" style="width:200px;">
          <option value=TB1 <? if ( $_GET['Testbed'] == TB1 ) {echo selected;} ?>>Testbed 01</option>
          <option value=TB2 <? if ( $_GET['Testbed'] == TB2 ) {echo selected;} ?>>Testbed 02</option>
          <option value=TB3 <? if ( $_GET['Testbed'] == TB3 ) {echo selected;} ?>>Testbed 03</option>
          <option value=TB4 <? if ( $_GET['Testbed'] == TB4 ) {echo selected;} ?>>Testbed 04</option>
        </select>
      </td>
      <td>
        <a class="popup"href="#thumb"><span><img src="<? echo $_GET['Testbed'] . ".jpg";?>" /></span>(Help?)</a>
      </td>
      <td id=info>Select the testbed that the device is connected to. This referes to the SmartBits Ports and chassis connected to the device under test.</input></td></tr>
    </table>
  </td>
  </tr>
<?
  }
?>

<?php
  if ( $_GET['Testbed'] != "") {
    $sum = $sum . ", Testbed : " . $_GET['Testbed'];
?>

  <tr><th align=left>Step 4: Enter or Scan Serial Number</th></tr> 

  <tr>
  <td valign=top>
    <table border=0 valign=top cellpadding=0 width=100%>
      <tr><td><input type=text name=serial size=50 value="<? if ( $_GET['Serial'] != "") {echo $_GET['Serial'];} ?>" width=100 onChange="update('Serial',this.value)"></input></td>
      <td><input type=button name=serial value=Next></input></td>
      <td id=info>Enter or scan the unique serial number of the device under test.</td></tr>
    </table>
  </td>
  </tr>
<?
  } 
?>

<?php
  if ( $_GET['Testbed'] != "") {
    switch ( $_GET['Testbed'] ) {
      case "TB1":
        $options = "<option value=\"\">Select Test</option>";
        $options = $options . "<option value=\"10G_2Port\">2 Port 10G</option>";
        $options = $options . "<option value=\"10G_4Port\">4 Port 10G</option>";
        break;
     case "TB2":
        $options = "<option value=\"\">Select Test</option>";
        $options = $options . "<option value=\"10G_2Port\">2 Port 10G</option>";
        $options = $options . "<option value=\"10G_4Port\">4 Port 10G</option>";
        break;
     case "TB3":
        $options = "<option value=\"\">Select Test</option>";
        $options = $options . "<option value=\"12Port\">12 Port Copper</option>";
        break;
     case "TB4":
        $options = "<option value=\"\">Select Test</option>";
        $options = $options . "<option value=\"6Port\">6 Port 1G</option>";
        break;
    }
  }
?>

<?php
  if ( $_GET['Serial'] != "") {
    $sum = $sum . ", Serial : " . $_GET['Serial'];
?>

  <tr><th align=left>Step 5: Select Test Type</th></tr>

  <tr>
  <td valign=top>
    <table border=0 valign=top cellpadding=0 width=100%>
      <tr><td>
        <select input type=text name=testType onChange="update('TestType',this.value)" style="width:200px;">
          <? echo $options; ?>
        </select>
      </td>
      <td id=info> Select the Smartbits test available for this testbed.
      </td></tr>
    </table>
  </td>
  </tr>
<?
  }
?>
<?php
  if ( $_GET['TestType'] != "") {
    $sum = $sum . ", Test Type : " . $_GET['TestType'];
?>

  <tr><th align=left>Step 6: Run Test</th></tr>

  <form method=post action="" onsubmit="return verify('<? echo $sum ?>','Start',this.value)">
  <tr>
  <td valign=top>
    <table border=0 valign=top cellpadding=0 width=100%>
      <tr>
      <td>
        <select name=Verbose>
          <option value=0>Default verbosity</option>
          <option value=1>1 - Low verbose</option>
          <option value=2>2 - Medium verbose</option>
          <option value=3>3 - High verbose</option>
          <option value=4>4 - Full verbose</option>
        </select>
       </td>
      <td><button type=submit name=Start value="Run">Run Test</button></td>
      <td id=info>Set the verbosity and execute test. For normal operation the default verbosity is ok.<br>If you encounter any failures then rerun test with higher verbosity.</td>
      </tr>
    </table>
  </td>
  </tr>
  </form>
<?
  }
?>
</table>

<br>

<table id=border width=90% align=center valign=top border=1 frame=box rules=none cellpadding=5 cellspacing=10>
<tr>
<th>
<table border=0 cellpadding=0 width=100%>
<tr>
<th align=left>Log Window</th>
<th style="color:red;"><? echo $sum; ?></th>

<?php
  if ( $_POST['Start'] != "") {
?>

<td align=right>
<a id=logLink href="javascript:winHeight('logLink', 'log_window', '200px')">[-] Expand</a></td>
</tr>
</table>
</th>
</tr>
<tr>
<td>

<?
    $cmd = "./BTC -testcase \"{MAN_TEST_001 " . $_GET['Testbed'] . " " . $_GET['Type'] . " " . $_GET['Serial'] . " " . $_GET['Testbed'] . "_" . $_GET['TestType'] . "}\" -testbed " . $_GET['Testbed'] . " -post 1 -logging 0 -verbose " . $_POST['Verbose'];
    //echo "Command = " . $cmd . "<br>\n";


    //change dir to BTC script dir.
    chdir('/var/www/html/automation/btc');

    //fork off and run the cmd
    $ret = popen("$cmd 2>&1", 'r');
?>


<br><div id=log_window style="overflow:auto; width:100%; height:200px;">
<?

    $resdir = "/var/www/html/automation/mfg/BTC_mfg.log";
    $fh = fopen($resdir, 'w') or die("can't open file");
    $result = 'Unknown';

    while(!feof($ret)) {
      $buffer = fgets($ret);
      echo "<pre>$buffer</pre>\n";

      if (preg_match("/(PASS|FAIL)/", $buffer, $matches)) {
        $result = $matches[0];
      }

      fwrite($fh, $buffer);
      ob_flush();
      flush();
    }
    fclose($fh);
    pclose($ret);
?>
</div>
</td>
</tr>

<tr id=<? echo strtolower($result); ?>>
<td>
<table border=0 cellpading=0 width=100%>
<tr><td align=left><b>Result : <? echo $result; ?></b></td><td align=right><b>Status : Test Completed </b></td></tr>
</table>
</td>
</tr>
<?
  }
?>

</table>
</body>
</html>
