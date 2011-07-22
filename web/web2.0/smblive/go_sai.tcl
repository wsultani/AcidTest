#!/usr/bin/expect --

##########################################################################################
##											##
##											##
##					Local Procs					##
##											##
##											##
##########################################################################################

proc send_simple_email { {email_list ""} {subject ""} {body ""} } {

    if { [catch "package require smtp" ret] } {
        puts "No snmp package - email will not be sent"
        return [list false "$ret"]
    }

    package require mime

    set email_server "stimpy.bivio.net"
    set orig "BivioTestCenter"
    set summ "
      <html>
      <head>
      </head>
      <body>
      <table>
      <tr><td>
    "
    if { $subject == "" } {
        set subject "BTC Automated Email"
    }

    if { $body == "" } {
        append summ "No email body provided"
    } else {
        append summ "<pre>$body</pre>"
    }

    append summ "
        </td></tr>
        </table>
        </body>
        </html>
    "
    set token [mime::initialize -canonical text/html -string $summ]

    mime::setheader $token Subject $subject

    foreach recipient $email_list {
        if { ![regexp "@" $recipient match] } {
            append recipient "@bivio.net"
        }
        smtp::sendmessage $token -originator $orig -recipients $recipient -servers $email_server
    }
    mime::finalize $token
}

proc problem_with { out {summ ""} } {
    set found_problem 0

    set err_msgs {not found|ERROR|Warning|Fail}

    if { [regexp -nocase {fail|bad|0|false} [lindex $out 0] match] } {
        set found_problem 1
    }

    if { [regexp -nocase {pass|good|1|true} [lindex $out 0] match] } {
        set found_problem 0
    }

    if { [string length $summ] > 0 } {
        if { [regexp -nocase $err_msgs [lindex $out 1] match] } {
            set found_problem 1
        }
    }

    return $found_problem
}

proc mk_dir { dirlst } {
    set rdir ""
    foreach dir $dirlst {
        set rdir [file join $rdir $dir]
        if { [catch {file mkdir $rdir} err] } {
            return [list false "$err"]
        }
        if { [catch {file attributes $rdir -permissions 0777} err] } {
            continue
        }
    }
    return [list true $rdir]
}

##########################################################################################
##											##
##											##
##					Main Script					##
##											##
##											##
##########################################################################################

if {$argc < 8 } {
    puts "\nERROR:\tNeed 8 arg. You entered $argc args:"
    puts "\t\t$argv"
    puts "\tSyntax:\t\t./go_sai.tcl smb_ip flows pkt duration sai ports testtype email notes"
    puts "\tExample:\t./go_sai.tcl \"192.168.100.212 192.168.100.214\" \"96\" \"64\" \"10\" \"test.sai\" \"{4:2:0 10G fiber} {4:3:0 10G fiber}\" \"thruput\" \"wsultani@bivio.net\"\n"
    exit
}
  
set smb_ip "[lindex $argv 0]"
set flows "[lindex $argv 1]"
set pkt "[lindex $argv 2]"
set duration "[lindex $argv 3]"
set sai "[lindex $argv 4]"
set ports "[lindex $argv 5]"
set testtype "[lindex $argv 6]"
set email "[lindex $argv 7]"
set notes "[lindex $argv 8]"

#puts "$smb_ip $flows $pkt $duration $sai $ports $email $notes"

#set smb_ip "192.168.100.212 192.168.100.214"
#set flows "96"
#set pkt "64"
#set duration "10"
#set sai "test.sai"
#set ports "{4:2:0 10G fiber} {4:3:0 10G fiber}"
#set email "wsultani@bivio.net"
#set notes ""

source sai_report_standalone.lib

set dl 40
set fmt "%-30s %-${dl}s %-5s\n"
set fmt2 "%-20s: %-80s\n"

while (1) {
    append param "[format $fmt2 "SMB" "$smb_ip"]"
    append param "[format $fmt2 "Test Type" "$testtype"]"
    append param "[format $fmt2 "Flows" "$flows"]"
    append param "[format $fmt2 "Pkts" "$pkt"]"
    append param "[format $fmt2 "Duration" "$duration"]"
    append param "[format $fmt2 "Ports" "$ports"]"

    set res "PASS"

    # save the location of the current working dir
    set restore [pwd]

    # set the timestamp
    set date [clock format [clock seconds] -format %Y%m%d]
    set tstamp [clock format [clock seconds] -format %H%M%S]

    set testdir [file join "/" "tmp" "$date-$tstamp"]

    # Need to make a tmp dir. The sai file will run from here.
    set ret [mk_dir "$testdir"]
    if { [problem_with $ret] } {
        set res FAIL
        append summ [format $fmt "Create $testdir" "[string repeat "." $dl]" "$res"]
        append resu "\nUnable to make dir $testdir:\n[lindex $ret 1]"
        break
    }

    # change to the test dir
    if { [catch "cd $testdir" err] } {
        set res FAIL
        append summ [format $fmt "Create $testdir" "[string repeat "." $dl]" "$res"]
        append resu "\nCould not cd to $testdir:\n$err"
        break
    }
    append summ [format $fmt "Create $testdir" "[string repeat "." $dl]" "$res"]


    set ret [generate_sai "$smb_ip" "$flows" "$pkt" "$duration" "$sai" "$ports" "$testtype"]
    #set ret [list true ""]
    if { [problem_with $ret] } {
        set res FAIL
        append summ [format $fmt "Generated sai file" "[string repeat "." $dl]" "$res"]
        append resu "\nFailed to generate sai file:\n[lindex $ret 1]"
        break
    }
    append summ [format $fmt "Generated sai file" "[string repeat "." $dl]" "$res"]

    set ret [run_sai_test "$sai" 1]
    #set ret [list true ""]
    if { [problem_with $ret] } {
        set res FAIL
        append summ [format $fmt "Run sai file" "[string repeat "." $dl]" "$res"]
        append resu "\nError while running sai file:\n [lindex $ret 1]"
        break
    }
    append summ [format $fmt "Run sai file" "[string repeat "." $dl]" "$res"]

    set ret [parse_sai_results "$sai" "temp/PERF_GROUP.csv" "" "$testtype"]
    if { [problem_with $ret] } {
        set res FAIL
        append summ [format $fmt "Parse sai results" "[string repeat "." $dl]" "$res"]
        append resu "\nError while parsing results:\n[lindex $ret 1]"
        break
    }
    append summ [format $fmt "Parse sai results" "[string repeat "." $dl]" "$res"]
    append resu "\n[lindex $ret 1]"
    break
}

set subject "$smb_ip - RESULT: $res"

append body "\n[string repeat "-" 100]\nTest Summary:\n[string repeat "-" 100]\n"
append body "$summ\n"
append body "\n[string repeat "-" 100]\nTest Notes:\n[string repeat "-" 100]\n"
if { $notes != "" } {
    append body "$notes\n"
    append body "\n[string repeat "-" 100]\nTest Parameters:\n[string repeat "-" 100]\n"
}
append body "$param\n"
append body "\n[string repeat "-" 100]\nTest Results:\n[string repeat "-" 100]\n"
append body "$resu\n"

send_simple_email "$email" "$subject" "$body"

puts "$subject\n"
puts "$body\n"

exit
