#!/usr/bin/expect --

###########################################################################################
##
## This script is run as a cronjob. It checks the queue to see if there are any 
## pending jobs. If jobs found, it checks the testbed to see if available. If available
## it starts the script, else it moves on to the next job.
##
## [wsultani@scooby btc]$ crontab -e
## */1 * * * * cd /var/www/html/automation/btc/ && ./scheduler.tcl
##
###########################################################################################

proc check_tb_availability {} {
    upvar tb ltb

    if { [catch "spawn -noecho telnet $ltb(Console) $ltb(Port)" ret] } {
        puts "Failed to spawn : $ret"
        return 0
    }

    expect {
            -re "(.*)Escape character is(.*)" {
                puts "Console to $ltb(Name) is avaialble"
                close -i $spawn_id
                sleep 2
                return 1
            }

            -timeout 5 timeout {
                return 0
            }

            eof {
                return 0
            }
    }
    close -i $spawn_id
    return 1
}

package require mysqltcl

log_user 0

#---------------------------------------------------------------------------------
# mysql info required to connect to database and set up the test harness
set mysql_server localhost
set mysql_user btc_user
set mysql_password btc_user
set mysql_port 23
set mysql_database btc_db
set mysql_table btc_scheduler

::mysqlclose

# connect to mysql server to get harnesss info
if { [catch {::mysql::connect -h $mysql_server -user $mysql_user \
                -password $mysql_password -db $mysql_database} ret] } {
    puts "Unable to connect to $mysql_server - $ret"
    exit
}

# set the local mysql object name. All calls to the mysql server will
# have to use this object name.
set ::mysql $ret
puts "connected to $mysql_server \($ret\)"
#---------------------------------------------------------------------------------

# set the local mysql handle to the global handle
set mysql $::mysql

set sfields {Job Script Testbed Status Priority Log Start}

set sql_cmd "SELECT [join $sfields ,] FROM $mysql_table \
	WHERE Status = \"\" OR Status = \"In Use\" \
	ORDER BY Priority ASC"

# check to see if there are any jobs queued
if { [catch {::mysql::sel $mysql $sql_cmd -list} ret] } {
    puts "could not exec sql cmd : $sql_cmd - $ret"
    exit
}

# if no jobs were found then exit test.
if { [llength $ret] <= 0 } {
    puts "No jobs scheduled"
}

foreach job $ret {
    array set scheduler ""
    foreach field $sfields value $job {
        set scheduler($field) $value
    }

    puts "check testbed $scheduler(Testbed) availability"
    set fields {Platform Class Name IPAddress Login Password Console Connection Port RemotePower}

    if { [regexp -- {-cfgfile\s+(\S+)\s+} $scheduler(Script) match cfgfile] } {
        source [string trim $cfgfile "\""]

        set ret ""
        foreach sf $fields {
            if { [info exists dut0($sf)] } {
                lappend ret "$dut0($sf)"
            } else {
                lappend ret ""
            }
        }
        set ret [list $ret]

    } else {
        # get the list of devics and connection info for the testbed
        set sql_cmd "select [join $fields ,] from btc_bivio_inventory where Testbed=\"$scheduler(Testbed)\""

        if { [catch {::mysql::sel $mysql $sql_cmd -list} ret] } {
            puts "could not exec sql cmd : $sql_cmd - $ret"
            continue
        }
    }

    # if no devices were found then exit test.
    if { [llength $ret] <= 0 } {
        puts "No testbed $scheduler(Testbed) found in database"
        continue
    }

    foreach bv $ret {

        set err 0

        foreach dev [list $bv] {
            foreach field $fields value $dev {

                set tb($field) $value
            }
        }

        if { ![check_tb_availability] } {

            set sql_cmd "UPDATE $mysql_table SET Status = \"In Use\" WHERE Job = \"$scheduler(Job)\""
            set ret [::mysql::exec $mysql "$sql_cmd"]
    
            puts "Testbed $scheduler(Testbed) is busy"
            incr err
            break
        }
    }

    if { $err > 0 } {
        continue
    }

    puts "Running $scheduler(Script)"
    set timenow [clock format [clock seconds] -format {%Y%m%d%H%M%S}]
    set log "/tmp/${scheduler(Job)}_$timenow.log"

    if [catch "exec $scheduler(Script) > $log &" ret] {
        puts "error - $ret"
    } else {
        set sql_cmd "UPDATE $mysql_table \
		SET Status = \"Running\", Start = $timenow , Log = \"$log\" , PID = \"$ret\" \
		WHERE Job = \"$scheduler(Job)\""
        set ret [::mysql::exec $mysql "$sql_cmd"]
    }

    sleep 2

    array unset scheduler
}

::mysqlclose
exit
