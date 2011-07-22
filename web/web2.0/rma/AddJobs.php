<?
  // open connection to mysql database
  include 'dbconnect.php';
?>

<?
function fetchGroup ($get,$tbl,$group,$selected,$intype) {
  $query="SELECT $get FROM $tbl GROUP BY $group";
  $result=mysql_query($query) or die(mysql_error());
  // Print out result
  $tname="-" . strtolower($group). "[]";
  while($row = mysql_fetch_array($result)){
    switch ($intype) {
      case "option":
        if ( $row[$get] == $selected ) {
          echo "<option selected=\"yes\" value=\"" . $row[$get] . "\">" . $row[$get] ."</option>\n";
        } else {
          echo "<option value=\"" . $row[$get] . "\">" . $row[$get] ."</option>\n";
        }
        break;
      case "checkbox":
       if ( $row[$get] == $selected ) {
         echo "<tr><td><input type=checkbox name=\"$tname\" selected=\"yes\" value=\"" . $row[$get] . "\">" . $row[$get] ."</td></tr>\n";
       } else {
         echo "<tr><td><input type=checkbox name=\"$tname\" value=" . $row[$get] . ">" . $row[$get] ."</td></tr>\n";
       }
        break;
     }
  }
}

function make_ul_tree ($get,$tbl,$group) {
  $query="SELECT $get FROM $tbl GROUP BY $group";
  $result=mysql_query($query) or die(mysql_error());

  $tname="-" . strtolower($group). "[]";

  while($row = mysql_fetch_array($result)){
    echo "<li><input type=checkbox name=\"$tname\" value=" . $row[$get] . ">" . $row[$get] . "\n";
    
    $query1="SELECT Name FROM $tbl WHERE Suite =\"" . $row[$get] . "\" ORDER BY Name";
    $result1=mysql_query($query1) or die(mysql_error());
    echo "<ul>\n";
    // Print out result
    while($row1 = mysql_fetch_array($result1)){
      echo "<li><input type=checkbox name=\"-testcase[]\" value=" . $row1['Name'] . ">" . $row1['Name'] . "\n";
    }
    echo "</ul>\n";
  }
}

function write_to_file ($file,$stringData) {
  $fh = fopen($file, 'w') or die("can't open file");
  fwrite($fh, $stringData);
  fclose($fh);
}

?>

<html>
<head>
    <!-- <meta http-equiv="refresh" content="0" /> -->
    <link rel="stylesheet" type="text/css" href="BTC.css"></link>
    <title>Bivio Test Center - Add Jobs</title>

    <script type="text/javascript">
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
    <SCRIPT SRC="mktree.js" LANGUAGE="JavaScript"></SCRIPT>  
</head>
<body>

<form action="" method="post">

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


<?
  if ( $_POST['RunTest'] == "Run" ) {

    if ( $_POST['qt'] == "on" ) {

      $_POST['-testbed'] = TempTB;

      if ( $_POST['-cfgfile'] == "" ) {

        $file = "/tmp/testbed_" . time() . ".cfg";
        $_POST['-cfgfile'] = $file;
        $data = "set testbed(name) TempTB\n";
        $data .= "set testbed(dut0) TempBV\n";
        $data .= "set dut0(Platform) BV7500\n";
        $data .= "set dut0(Name) TempBV\n";
        $data .= "set dut0(Class) bivio\n";
        $data .= "set dut0(Connection) console\n";
        $data .= "set dut0(IPAddress) " . $_POST['qt_ip'] . "\n";
        $data .= "set dut0(Console) " . $_POST['qt_cip'] . "\n";
        $data .= "set dut0(Port) " . $_POST['qt_port'] . "\n";
        $data .= "set dut0(Login) " . $_POST['qt_login'] . "\n";
        $data .= "set dut0(Password) " . $_POST['qt_pass'] . "\n";
        write_to_file($file,$data);
      }
    }

    if ( $_POST['-testbed'] != "Select" ) {

      $cmd = "./BTC";
      foreach ($_POST as $key => $value) {

        // do not process the hidden key RunTest
        if ($key == "RunTest" || $key == "Priority") {
          continue;
        }

        // do not process the autoinstall if zero
        if ($key == "-autoinstall" && $value == "0") {
          continue;
        }

        // do not process the quick test values
        if ($key == "qt" || $key == "qt_ip" || $key == "qt_cip" || $key == "qt_port" || $key == "qt_login" || $key == "qt_pass") {
          continue;
        }


        // if value is an array
        if (is_array($value)) {
          foreach ($value as $v){
            $nvalue = $nvalue . " " . $v;
          }
          $value = $nvalue;
        }

        if ($value != "") {
          $cmd = $cmd . " " . $key . " " . "\"" . trim($value) . "\"";
        }
      }

      //echo "Test Command = " . $cmd . "<br>\n";

      $table="btc_scheduler";

      if ($_POST['Priority'] == "") {
          $priority = 1;
      } else {
          $priority = $_POST['Priority'];
      }

      $keys = join(", ", array('Script', 'Testbed', 'Priority'));
      $vals = join("', '", array($cmd, $_POST['-testbed'], $priority));
  
      $query = "INSERT INTO $table ($keys) VALUES ('$vals')";
      mysql_query ($query);
      $result = mysql_affected_rows();
  
      if ( $result <= 0 ) {
        $stat = fail;
        $msg = "Failed to insert data into table " . $table;
      } else {
        $stat = pass;
        $msg = mysql_affected_rows() . " rows inserted into table " .  $table;
      }
    } else {
      if ( $_POST['-testbed'] == "Select" ) {
        $stat = fail;
        $msg = "Please select a Testbed";
      }
    }
  }

  if ( $stat == "pass" ) {
    //change dir to BTC script dir.
    chdir('../btc');

    //fork off and run the command
    exec('./scheduler.tcl &');
  }

  if ( $msg != "" ) {
    echo "<tr id=" . $stat . " align=center><td><b>" . $msg . "</b></td></tr>";
  }
?>


  <tr><th align=left><h2>Select a Testbed (Required)</h2></th></tr>

  <tr>
  <td>
    <table border=0 cellpadding=5 cellspacing=0 valign=top width=100%>
    <tr align=left>
    <td>
    <select name="-testbed">
      <option value="Select">Select a Testbed</option>
<?
  // generate the select options list
  fetchGroup("Testbed","btc_testbeds","Testbed","","option");
?>
    </select>
    </td>
    </tr>
    </table>
  </td>
  </tr>

    <tr>
    <td>

  <tr><td><br>
    <input type=checkbox name=qt onclick="javascript:winHeight('tbLink', 'tb_window', '0px')">Configure Quick Testbed
      <div class="tt"><a href="#"><image src=info.png align=top border=0>
        <div class="tooltip">Configure a quick temporary testbed<br>
          <br>Which will not be stored in the database<br>
        </div></a>
      </div>

    <div id=tb_window style="overflow:auto; width:100%; height: 0px;">
      <table id=border border=1 farem=box rules=all cellpadding=5 cellspacing=0 valign=top width=100%>
        <tr><th align=left>System IP</th><td width=90%><input name=qt_ip width=100></td></tr>
        <tr><th align=left>Console IP</th><td width=90%><input name=qt_cip width=100></td></tr>
        <tr><th align=left>Console Port</th><td><input name=qt_port width=100></td></tr>
        <tr><th align=left>Login</th><td><input name=qt_login width=100></td></tr>
        <tr><th align=left>Password</th><td><input name=qt_pass width=100></td></tr>
        <tr><td align=left><br></td><td></td></tr>
        <tr><th align=left>Existing CFG File</th><td width=90%><input name=-cfgfile width=100></td></tr>
      </table>
    </div>
  </td></tr>

  <tr><td><br><br></td></tr>

  <tr><td>
    <table border=0 cellpadding=0 width=100% cellspacing=0>
      <tr><th align=left><h2>Select a Testcase (Required)<h2></th>
      <th align=right>
        <a id=tcLink href="javascript:winHeight('tcLink', 'tc_window', '100px')">[-] Expand</a>
      </th>
      </tr>
    </table>
  </td>
  </tr>

  <tr>
  <td>
    <table border=0 cellpadding=0 cellspacing=0 valign=top width=100%>
    <tr align=left>
    <td>
    <div id=tc_window style="overflow:auto; width:100%; height: 100px;">
    <table border=0 cellpadding=0 cellspacing=0 valign=top align=left>
      <ul class="mktree">
<?
  // generate the select options list
  make_ul_tree("Suite","btc_tests","Suite");
?>
      </ul>

    </table>
    </div>
    </td>
    </tr>
    </table>
  </td>
  </tr>

  <tr><td><br><br></td></tr>

  <tr><th align=left><h2>Select Options (Optional)</h2></th></tr>
  <tr>

  <td>
    <table id=border border=1 farem=box rules=all cellpadding=5 cellspacing=0 valign=top width=100%>
      <tr align=center>
       
<!-- Remove the verbose selection option for RMA
      <td width=33% valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Set Verbose Level

          <div class="tt"><a href="#"><image src=info.png align=top border=0>
            <div class="tooltip">Select the amount of information displayed in the realtime log<br>
              <br>This is a subset of the stored log which contains all the info.<br>
              <br>Default is : 3 - High verbose.
            </div></a>
          </div>

        </th></tr>
        <tr><td>
          <select name=-verbose>
          <option value=1>1 - Low verbose</option>
          <option value=2>2 - Medium verbose</option>
          <option value=3 selected >3 - High verbose</option>
          <option value=4>4 - Full verbose</option>
          </select>
       </td>
       </tr>
      </table>
      </div>
      </td>
-->
      <input type=hidden name=-verbose value=3>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Chassis Serial

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Enter a space seperated list of serial numbers<br>
              <br>These serial numbers will be stored in the database as part of the test.<br>
              <br>Example : 7APC-000437 7NPC-000459
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=-serials size=40 / ></td></tr>
      </table>
      </td>

      <td width=33% valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Set Number of Iterations

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Set the number of times you want to loop the test.<br>
              <br>Only insert a number if you wish to loop more then once.<br>
              <br>Default is : 1
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=-loop size=40 / ></td></tr>
      </table>
      </td>

      <td width=33% valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Set Break Point

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Set the break point for a looped testcase. Onlu applies to looped tests<br>
              <br>If the selected state is matched the test will break out of loop and end test.<br>
              <br>Default is : NONE
            </div></a>
          </div>

        </th></tr>
        <tr><td>
          <select name=-break>
          <option value="">NONE</option>
          <option value=FAIL>FAIL</option>
          <option value=ERROR>ERROR</option>
          <option value=WARNING>WARNING</option>
          <option value=PASS>PASS</option>
          </select>

       </td>
      </table>
      </td>

      </tr>
      <tr>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Enable/Disable Logging

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Select logging. If enabled will create and store a complete log of the test<br>
              <br>It is recommended to enable logging if you wish to keep a record of the test.<br>
              <br>Default is : Disabled
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=radio name=-logging checked value=0 / >Logging Disabled</td></tr>
        <tr><td><input type=radio name=-logging value=1 / >Logging Enabled</td></tr>
      </table>
      </td>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Enable/Disable Posting to Database

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Select posting to database. If enabled will post results to database<br>
              <br>It is recommended to enable posting if you wish to keep a record of the test.<br>
              <br>Default is : Disabled
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=radio name=-post checked value=0 / >Posting  Disabled</td></tr>
        <tr><td><input type=radio name=-post value=1 / >Posting Enabled</td></tr>
      </table>
      </td>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Set E-Mail Notification Receipents

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Enter a space seperated list of emails.<br>
              <br>A notification email will go out to the list when the test has completed/terminated.<br>
              <br>If no "@" is entered then "@bivio.net" is appended to to reciepient.
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=-mail size=40 / ></td></tr>
      </table>
      </td>
      
      </tr>
      <tr>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Enter Extended ROM

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">To update the extended rom, insert the full tftp rom location.<br>
              <br>Example : /tftpboot/ROMOdessa/romext_2.64.bin
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=-updaterom size=40 / ></td></tr>
      </table>
      </td>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Enter a Build

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Enter the build string. The build string must contain the distro string.<br>
              <br>Example : fcdist.5.0.5.10.
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=-build size=40 / ></td></tr>
      </table>
      </td>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Enable Autoinstall

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Select if you wish to install the build before commencing any tests.<br>
              <br>This will install the build into the system first and then begin the tests.<br>
              <br>Default is : Disabled
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=radio name=-autoinstall checked value=0 / >Disabled</td></tr>
        <tr><td><input type=radio name=-autoinstall value=1 / >Enabled</td></tr>
      </table>
      </td>

      </tr>
      <tr>

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Enter FPGA File

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Enter the full path to the fpga shell scrip installer.<br>
              <br>Example : /install/jtagfpga/RPMS/jtagfpga-bp11.xpc13-4.1.sh
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=-fpga size=40 / ></td></tr>
      </table>
      </td>

<!-- Remove the job priority option for RMA
      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Enter Job Priority

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Allows you to select the priority of the job.<br>
              <br>The lower the number the higher the priority.<br>
              <br>This field can be used to control the order of the jobs when entering multiple jobs.<br>
              <br>Default is : 1
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=Priority size=40 / ></td></tr>
      </table>
      </td>
-->

<!--
      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Force Console Reset

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">If the console to any devise is blocked on first attemp, It will reset the console and retry to connect to the console.<br>
              <br>Default is : Disabled
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=radio name=-force checked value=0 / >Disabled</td></tr>
        <tr><td><input type=radio name=-force value=1 / >Enabled</td></tr>
      </table>
      </td>
-->

      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Add Comments

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Its a good idea to add some specific comments about your test.<br>
              <br>This will help you easily identify your test results in the results table.
            </div></a>
          </div>

        </th></tr>
        <tr><td><textarea name=-comments cols=40></textarea></td></tr>
      </table>
      </td>

      <td></td>

      </tr>
<!-- Removing empty row for RMA
      <tr>

<!-- Remove the Power down option for RMA
      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Power Down System

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Power down the system when testing is completed.<br>
              <br>This option will only work if the system is configured for rpower.<br>
              <br>Default is : Disabled.
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=radio name=-powerdown checked value=0 / >Disabled</td></tr>
        <tr><td><input type=radio name=-powerdown value=1 / >Enabled</td></tr>
      </table>
      </td>

<!-- Remove the serial option for RMA
      <td valign=top>
      <table border=0 cellpadding=5 cellspacing=0 valign=top align=left width=100%>
        <tr><th align=left>Chassis Serial

          <div class="tt"><a href="#"><image src=info.png align=absmiddle border=0>
            <div class="tooltip">Enter a space seperated list of serial numbers<br>
              <br>These serial numbers will be stored in the database as part of the test.<br>
              <br>Example : 7APC-000437 7NPC-000459
            </div></a>
          </div>

        </th></tr>
        <tr><td><input type=text name=-serials size=40 / ></td></tr>
      </table>
      </td>

      <td></td>

      </tr>
-->
    </table>
  </td>
  </tr>

  <tr><td><br><br></td></tr>

  <tr><th>
    <table border=0 width=100% cellpadding=0>
    <tr>
    <td width=50% align=center><input type="submit" value="Add Job" /></td>
    <input type=hidden name=RunTest value=Run />
    <td align=center><b><? echo '<a href="'.$_SERVER['REQUEST_URI'].'">Reset</a>'; ?></b></td>
    </tr>
    </table>
  </th>
  </tr>

</table>
</form>
</body>
</html>
