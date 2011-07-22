<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="BTC.css"></link>
<title>Smartbits Quick Test</title>
<body onLoad="document.getElementById('submit_btn').disabled = false;">

<form action="" method="post">

<?
  if (isset($_POST["go"])) {
    $empty = 0;
    $req = array(smb_ip, duration, flows, pkt, email, ports);

    foreach ($_POST["ports"] as $key => $value) {
     $req[] = $value . "_speed";
     $req[] = $value . "_mode";
    }

    foreach ($_POST as $key => $value) {
      if ( !in_array($key, $req) ) {
        continue;
      }

      if (empty($value)) {
        $stat = fail;
        $msg .= $key . " - cannot be empty.<br>";
        $empty ++;
      }
    }

    if ($empty == 0) {
      foreach ($_POST["ports"] as $key => $value) {
        $nports .= "{" . $value . " " . $_POST[$value . "_speed"] . " " . $_POST[$value . "_mode"] . "} ";
      }
      $nports = trim($nports);

      $cmd = "/usr/bin/sudo ./go_sai.tcl \"" . $_POST["smb_ip"] . "\" \"" . $_POST["flows"] . "\" \"" 
	. $_POST["pkt"] . "\" \"" . $_POST["duration"] . "\" \"test.sai\" \"" . $nports . "\" \""
	. $_POST["testtype"] . "\" \"" . $_POST["email"] . "\" \"" . $_POST["notes"] . "\"";
    }
  }
?>

<table id=border bgcolor=#FFFDF3 width=90% border=0 cellpadding=5 cellspacing=0 align=center>

<tr><td colspan=2>
  <table cellspacing=0 cellpadding=10 id=border width=100%>
    <tr><td><h2 style="padding:0px;" align=center valign=center>.. Spirent SMBAPI Administrator ..</h2></td></tr>
  </table>
</td></tr>

<tr><td valign=top>

<table id=border bgcolor=#FFFDF3 width=100% height=100% border=0 cellpadding=0 cellspacing=0>
  <tr>
  <th align=left>Configuration</th>
  </tr>

  <tr>
<?
    if ( $msg != "" ) {
      echo "<tr id=" . $stat . " align=center><td><b>" . $msg . "</b></td></tr>";
    }
?>

    <td valign=top>

      <table width=100% border=0 cellpadding=0 cellspacing=3 height=100%>
        <tr>
          <th align=left nowrap>SMB Device</th>
          <td>
            <select name="smb_ip" onchange="this.form.submit();">
              <option value="">----- Select A Smartbits Chassis -----</option>
              <option value="" disabled> ----- QA Eng -----</option>
              <option value="192.168.100.212 192.168.100.214" 
		<? if($_POST["smb_ip"] =='192.168.100.212 192.168.100.214') echo ' selected="selected"'; ?>
		>192.168.100.212/214</option>
              <option value="" disabled> ----- SW Eng -----</option>
              <option value="192.168.100.216"
		<? if($_POST["smb_ip"] =='192.168.100.216') echo ' selected="selected"'; ?>
		>192.168.100.216</option>
              <option value="" disabled> ----- HW Eng -----</option>
              <option value="192.168.100.220"
		<? if($_POST["smb_ip"] =='192.168.100.220') echo ' selected="selected"'; ?>
		>192.168.100.220</option>
              <option value="" disabled> ----- RMA -----</option>
              <option value="192.168.7.215"
		<? if($_POST["smb_ip"] =='192.168.7.215') echo ' selected="selected"'; ?>
		>192.168.7.215</option>
            </select>
          </td>
        </tr>

        <tr>
          <th align=left nowrap>Test Type</th>
          <td><table align=center border=0 cellpadding=5 cellspacing=0 width=100%>
            <tr><td><input type=radio name=testtype value=thruput 
			<? if($_POST['testtype'] == thruput) echo ' checked'; ?> >THRUPUT</td>

            <td><input type=radio name=testtype value=latency 
                        <? if($_POST['testtype'] == latency) echo ' checked'; ?> >LATENCY</td>

            </tr></table>
          </td>
        </tr>

        <tr>
          <th align=left nowrap>Duration (sec)</th>
          <td><input name=duration size="80" <? echo " value=" . $_POST['duration']; ?>></td>
        </tr>

        <tr>
          <th align=left nowrap>Flows</th>
          <td><input name=flows size="80" <? echo " value=" . $_POST['flows']; ?>></td>
        </tr>

        <tr>
          <th align=left nowrap>Pkt Size</th>
          <td><input name=pkt size="80" <? echo " value=" . $_POST['pkt']; ?>></td>
        </tr>

        <tr>
          <th align=left nowrap>Email</th>
          <td><input name=email size="80" <?  echo " value=" . $_POST['email']; ?>></td>
        </tr>

        <tr>
          <th align=left nowrap valign=top>Notes</th>
          <td><textarea name="notes" cols="60" rows="3" <?  echo " value=" . $_POST['notes']; ?>></textarea></td>
        </tr>

        <tr>
          <td></td>
          <td><table align=center border=0 cellpadding=5 cellspacing=0 width=100%>
            <tr><td><input id="submit_btn" type=submit name=go value="Start test" disabled /></td>
            <td><input type="reset" name="reset_form" value="Reset" onClick="history.go(0)" /></td>
            </tr></table>
          </td>
        </tr>


      </table>
    </td>
  </tr>
</table>
</td>

<td width=100% valign=top>

<?
  $dir = preg_replace("/[\s+|\.]/", "", $_POST["smb_ip"]);

  if (isset($_POST["smb_ip"]) && !isset($_POST["go"])) {

     $cmd = "/usr/bin/sudo ./get_available.tcl \"" . $_POST["smb_ip"] . "\" " . $dir;

      if (isset($cmd)) {

        echo "Checking Available ports for " . $_POST["smb_ip"] . " ...";
        $a = popen($cmd, 'r');

        while($b = fgets($a)) {
          echo ".";
          ob_flush();flush();
        }
        pclose($a);
        echo " Done! ";
      }

     unset($cmd);
  }
   
  $file = "$dir/Available.txt";
  if (file_exists($file)) {
    $data = fopen($file,'r');

    while(!feof($data)) { 
      $line = fgets($data);

      if (preg_match("/(\d+):(\d+):(\d+)\s+\d+\s+(\S+)\s+(.*Available)/", $line, $regs)) {
        $sys = $regs[1];
        $slot = $regs[2];
        $port = $regs[3];
        $type = preg_replace('/-/', '_', $regs[4]);

        $ctype["$sys:$slot"] = $type;

        if (preg_match("/Available/", $regs[5], $match)) {
          $arr["$sys:$slot"] .= $port;
        } else {
          $arr["$sys:$slot"] .= "";
        }
      }

    }

    fclose($data);

    $count = 1;
    $phost = 00;

    include 'card_matrix.php';

    echo "<table id=border width=100% bgcolor=#FFF3B3 rules=all border=0 cellpadding=3 cellspacing=0><tr>\n";
    echo "<th colspan=2>Available Ports <br> " . $_POST["smb_ip"] . "</th></tr><tr>\n";
    foreach ($arr as $key => $value) {

     $h = str_split($key, 2);

      if ($count == 3 || $h[0] !=  $phost) {
        $count = 1;
        echo "</tr><tr>\n";
      }

      echo "<td><table border=0 cellpadding=0 cellspacing=0 width=100%><tr><td>" . $key . 
		"</td><td align=right>" . $ctype[$key] . "</td></tr></table>
		<table align=center id=border bgcolor=#FFFFFF rules=all border=0 
		cellpadding=3 cellspacing=0><tr>\n";

      $a = str_split($value, 2);
      foreach ($a as $k => $v) {
        if (empty($v)) {
          echo "<td>Unavailable</td>";
          continue;
        }

        $id = $key . ":" . $v;

        echo "<td id=" . $id;

		 if(in_array($id, $_POST[ports])) echo ' "bgcolor=#00CC33"';

	echo ">
	      <table border=0 cellpading=0 cellspacing=0>
	        <tr><td>" . $v . "</td><td>
		<input type=checkbox name=ports[] value=" . $id . "
onclick=\"document.getElementById('" . $id . "').style.backgroundColor=this.checked?'#00CC33':'#FFFFFF';\"";

		 if(in_array($id, $_POST[ports])) echo ' "checked"';

        $tp = $ctype["$key"];

        $speed = ${$tp}[speed];
        $media = ${$tp}[media];


        echo "></input></td>
		</tr><tr>
		<td id=small>Speed</td><td><select name=" . $id . "_speed>
		  <option value=\"\">-</option>";


        if (is_array($speed)) {
          foreach ($speed as $sk => $sv) {
            echo "<option value=" . $sv;

            if($_POST[$id . "_speed"] == $sv) echo ' selected="selected"';

            echo ">" . $sv . "</option>\n";
          }
        } else {
            echo "<option value=" . $speed;

            if($_POST[$id . "_speed"] == $speed) echo ' selected="selected"';

            echo ">" . $speed . "</option>\n";
        }

        echo "</select></td>
		</tr><tr>
		<td id=small>Mode</td>
		<td id=small nowrap>";

        if (is_array($media)) {
          foreach ($media as $mk => $mv) {
            echo "<input type=radio name=" . $id . "_mode value=" . $mv;

            if($_POST[$id . "_mode"] == $mv) echo ' checked';

            echo ">" . $mv . "</input><br>\n";
          }
        } else {
            echo "<input type=radio name=" . $id . "_mode value=" . $media;

            if($_POST[$id . "_mode"] == $media) echo ' checked';

            echo ">" . $media . "</input><br>\n";
        }

	echo "</td>
		</tr></table></td>\n";
      }
      echo "</tr></table></td>\n";
      $phost = $h[0];
      $count ++;
    }
    echo "</tr></table>\n";
  }

?>

</td>
</tr>


<tr>
<td valign=top colspan=2>

  <table id=border bgcolor=#FFFFFF width=100% border=0 cellpadding=5 cellspacing=0>
    <tr>
    <th align=left colspan=2>Progress</th>
    </tr>

    <tr>
    <td>
      <?

      if (isset($cmd)) {

?>

      <div id=progwin style="height:300px; width:1450px; overflow: scroll; overflow-x: hidden;">
      <pre style="font-size:12px;">

<?
        $a = popen($cmd, 'r');
    
        while($b = fgets($a)) {
          echo $b ."\n";
          ob_flush();flush();

          echo "<script type=text/javascript>";
          echo "var objDiv = document.getElementById('progwin');";
          echo "objDiv.scrollTop = objDiv.scrollHeight;";
          echo "</script>";

        }
        pclose($a);
?>
      </pre>
      </div>
<?
      }
  
      ?>
    </td>
    </tr>
  </table>

    </td>
  </tr>
</table>

</td>

<td></td>

</tr>
</table>

</form>

</body>

</html>
