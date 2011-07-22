<?php

  if (!function_exists('str_getcsv')) {
    function str_getcsv($input, $delimiter = ',', $enclosure = '"', $escape = '\\', $eol = '\n') {
      if (is_string($input) && !empty($input)) {
        $output = array();
        $tmp    = preg_split("/".$eol."/",$input);
        if (is_array($tmp) && !empty($tmp)) {
          while (list($line_num, $line) = each($tmp)) {
            if (preg_match("/".$escape.$enclosure."/",$line)) {
              while ($strlen = strlen($line)) {
                $pos_delimiter       = strpos($line,$delimiter);
                  $pos_enclosure_start = strpos($line,$enclosure);
                  if (
                    is_int($pos_delimiter) && is_int($pos_enclosure_start)
                      && ($pos_enclosure_start < $pos_delimiter)
                    ) {
                    $enclosed_str = substr($line,1);
                    $pos_enclosure_end = strpos($enclosed_str,$enclosure);
                    $enclosed_str = substr($enclosed_str,0,$pos_enclosure_end);
                    $output[$line_num][] = $enclosed_str;
                    $offset = $pos_enclosure_end+3;
                  } else {
                    if (empty($pos_delimiter) && empty($pos_enclosure_start)) {
                      $output[$line_num][] = substr($line,0);
                      $offset = strlen($line);
                    } else {
                      $output[$line_num][] = substr($line,0,$pos_delimiter);
                      $offset = (
                        !empty($pos_enclosure_start)
                        && ($pos_enclosure_start < $pos_delimiter)
                      )
                      ?$pos_enclosure_start
                      :$pos_delimiter+1;
                    }
                  }
                  $line = substr($line,$offset);
                }
              } else {
                $line = preg_split("/".$delimiter."/",$line);

                /*
                * Validating against pesky extra line breaks creating false rows.
                */
                if (is_array($line) && !empty($line[0])) {
                  //$output[$line_num] = $line;
                  $output = $line;
                }
              }
            }
            return $output;
          } else {
            return false;
        }
      } else {
        return false;
      }
    }
  }

  function viewtotals($filename) {

    exec("cat $filename | grep Totals", $res);

    $prev = 0;
    $title = str_getcsv("Name,StreamCnt,PacketLength,CRC Size,TxFrames,CurrentLoad%,Throughput,RxFrames,LostFrames,DupFrames,LossPercnt,Tx fps ,Tx L2 Mbps ,Rx fps ,Rx L3 Mbps ,Rx L2 Mbps");

    foreach($res as $line) {

      $data = str_getcsv($line);

      if ($data[2] != $prev) {
        $prev = $data[2];

        echo "<tr><td colspan=20><br><br></td></tr>\n";
        echo "<tr>\n";

        foreach($title as $val) {
          if ($val == "Name" || $val == "") {
            continue;
          }

          echo "<th>" . $val . "</th>\n";
        }

        echo "</tr>\n";
      }

      echo "<tr>\n";

      foreach($data as $value) {

        if ($value == "Totals" || $value == "") {
          continue;
        }

        if (!is_numeric($value)) {
          echo "<th colspan=$cspan>$value</th>";
        } else {
          echo "<td colspan=$cspan align=center>" . round($value,2) . "</td>";
        }
      }

      echo "</tr>\n";
    }

    return(true);
  }

  // bool viewlog(str filename)
  // parses input CSV file into table rows
  // returns FALSE if cannot open file, otherwise TRUE

  function viewlog2($filename) {

    $tmp =  exec("cat $filename | grep Totals");
    echo $tmp;
    return(true);

    $fp = fopen($filename,"r");

    while(($line = fgetcsv($fp, 1000)) !== false) {
      $num = count($line);
      if ($num > 1) {
        $cspan = 1;
      } else {
        $cspan = 100;
      }

      if ($line[0] == "Totals" || ereg ("Name", $line[0]) || ereg ("Iteration", $line[0])) {
          echo "<tr id=\"Totals\">";
      } else {
          echo "<tr>";
      }

      foreach($line as $value) {
        if (!is_numeric($value)) {
          echo "<th colspan=$cspan>$value</th>";
        } else {
          echo "<td colspan=$cspan align=center>" . round($value,2) . "</td>";
        }
      }
      echo "</tr>\n";
    }

    fclose($fp);
    return(true);
  }

  $filename = $_GET[csvfile];
  $title = basename($filename,'.csv');
?>

<html>
<head>
   <link rel="stylesheet" type="text/css" href="BTC.css"></link>

    <script type="text/javascript">

      function checkboxfilter (_id){
        var checked = new Array();
        var elements = document.getElementsByTagName('input');
        for(var i = 0; i < elements.length; i++){
          if(elements[i].type == 'hidden'){
            if(elements[i].checked){
              checked.push(elements[i].value);
            }
          }
        }

        var table = document.getElementById(_id);
        var ele;
        for (var r = 1; r < table.rows.length; r++){
          ele = table.rows[r].innerHTML.replace(/<[^>]+>/g,"");
          var displayStyle = '';
            for (var i = 0; i < checked.length; i++) {
              if (ele.indexOf(checked[i])>=0)
                continue;
              else {
                displayStyle = 'none';
              }
            }
            table.rows[r].style.display = displayStyle;
          }
        }

      </script>

   <title><? echo $title; ?></title>
</head>
<body>

  <table id=border width=90% align=center valign=top border=1 rules=all>
   <tr><td colspan=100>
      <table border=0 width=100% cellpadding=0 valign=top>
        <tr>
        <td align=center><h2><? echo $title; ?></h2></td>
<!--
        <td><input type=hidden value=Totals checked></input></td>
        <td><button onclick="checkboxfilter('border')" value=Filter>Show Totals Only</button></td>
-->
        </tr>
      </table>
      </td>
    </tr>

<?
  //echo viewlog($filename);
  //viewlog2($filename);
  viewtotals($filename);
?>

</table>
</body>
</html>
