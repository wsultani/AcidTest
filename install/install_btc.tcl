#!/usr/bin/expect --

###############################################################################
##
## PROC
##
###############################################################################
proc connect { {user root} {passwrd root} {pc_ip localhost} } {
    set method ssh

    set gretry 3
    set lretry $gretry
    set attempt 1

    switch $method {
        "ssh" {
            set cmd "$user@$pc_ip"
        }
        "telnet" {
            set cmd "$pc_ip"
        }
        "console" {
            set method "telnet"
            set cmd "$console $port"
        }
        default {
            set method "telnet"
            set cmd "$pc_ip"
        }
    }

    puts "Connecting to $method $cmd .."
    if { [catch "spawn -noecho $method $cmd" reason] } {
        puts "Failed to spawn $method $cmd : $reason"
        exit
    }

    while {$lretry > 0} {
        expect {
            -re "(.*)Escape character is(.*)" {
                set msg $expect_out(buffer)
                exp_send "\r"
                exp_send "\r"
                exp_continue
            }

            -nocase "(.*)connection refused(.*)" {
                set msg $expect_out(buffer)
                puts "Connection refused"
                return ""
            }

            -re "(.*)you want to continue connecting(.*)" {
                set msg $expect_out(buffer)
                exp_send "yes\r"
                exp_continue
            }

            -re "Last(.*)ogin:(.*)" {
                set msg $expect_out(buffer)
                sleep 2
                exp_send "\r"
                exp_continue
            }

            -re "(.*)ogin:(.*)" {
                set msg $expect_out(buffer)
                exp_send "$user\r"
                exp_continue
            }

            -re "(.*)assword:(.*)" {
                set msg $expect_out(buffer)
                exp_send "$passwrd\r"
                exp_continue
            }

            -re {(.*)\#} {
                exp_send "\r"
                set msg $expect_out(buffer)
                return $spawn_id
            }

            -re "ftp>" {
                set msg $expect_out(buffer)
                exp_send "exit\r"
                exp_continue
            }

            -timeout 5 timeout {
                exp_send "\r"
                incr attempt
                incr lretry -1
            }

            eof {
                set msg $expect_out(buffer)
                puts "Connection to $method $cmd failed"
                exit
            }
        }
    }
}

###############################################################################
##
## PROC
##
###############################################################################
proc exec_i { cmd {exp "NOpaTTERN"} } {

    set max_try 2
    set try 1

    exp_send "$cmd\r"

    while { $try <= $max_try } {

        expect {
            -re (.*$exp.*) {
                return [list 1 "$expect_out(buffer)"]
            }


            -re "$cmd.*~]# " {
                return [list 1 "$expect_out(buffer)"]
            }

            -timeout 30 timeout {
                puts "Timedout waiting for reply \($try/$max_try\)"
                incr try 1
                exp_send "\r"
                continue
            }

            eof {
                set ret $expect_out(buffer)
                puts "Connection failed"
                return [list 0 ""]
             }
        }
    }
    return [list 0 ""]
}

###############################################################################
##
## PROC
##
###############################################################################
proc exec_a { cmd {exp ""} } {

    set max_try 2
    set try 1

    while { $try <= $max_try } {
        catch "eval exec $cmd" ret
        if { $exp != "" || ![regexp "$exp" $ret match] } {
            puts "$ret"
            incr try 1
            continue
        }
        return [list 1 "$ret"]
    }
    return [list 0 ""]
}

###############################################################################
##
## PROC
##
###############################################################################
proc do_yum { mode pkg } { 

    set cmd "yum info installed $pkg"
    set ret [exec_$::mode $cmd]

    if { [lindex $ret 0] == 0 } {
        return [list 0 "Unknown"]
    }

    if { [regexp {Name\s+:\s+(\S+).*Version\s+?:\s+(\S+)} [lindex $ret 1] match name ver] } {
        set installed 1
    }

    switch $mode {

        info {
            if { [info exists installed] } {
                return [list 1 $ver]
            } else {
                return [list 0 "Unknown"]
            }
        }

        install {
            if { [info exists installed] } {
                return [list 1 $ver]
            }

            set cmd "yum install -y $pkg"
            set ret [exec_$::mode $cmd {~]#\s}]

            puts "Verifying package $pkg installation"

            set cmd "yum info installed $pkg"
            set ret [exec_$::mode $cmd]

            if { [regexp {Name\s+:\s+(\S+).*Version\s+?:\s+(\S+)} [lindex $ret 1] match name ver] } {
                return [list 1 $ver]
            } else {
                return [list 0 "Unknown"]
            }
        }

        erase  {
        }
    }
    return [list 0 "Unknown"]
}

###############################################################################
##
## PROC
##
###############################################################################
proc cvs_ssh_co { user pass module } {
    set max_try 2
    set try 1

    set cmd "cvs -d :ext:${user}@beavis.bivio.net:/export/beavis/cvsroot co $module"
    exp_send "$cmd\r"

    while { $try <= $max_try } {

        expect {

            "(.*)you want to continue connecting(.*)" {
                set msg $expect_out(buffer)
                exp_send "yes\r"
                exp_continue
            }

            -re "(.*)ogin:(.*)" {
                set msg $expect_out(buffer)
                exp_send "$user\r"
                exp_continue
            }

            -re "(.*)assword:(.*)" {
                set msg $expect_out(buffer)
                exp_send "$pass\r"
                exp_continue
            }

            -re {(.*)~]#} {
                exp_send "\r"
                set msg $expect_out(buffer)
                return [list 1 "$expect_out(buffer)"]
            }

            -timeout 30 timeout {
                puts "Timedout waiting for reply \($try/$max_try\)"
                incr try 1
                exp_send "\r"
                continue
            }

            eof {
                set ret $expect_out(buffer)
                puts "Connection failed"
                return [list 0 ""]
             }
        }
    }
    return [list 0 ""]
}

###############################################################################
##
## PROC
##
###############################################################################
proc cvs_pserver_co { user pass module } {
    set max_try 2
    set try 1

    set cmd "export CVSROOT=:pserver:$user@cvs-server:/export/cvsroot"
    exp_send "$cmd\r"

    set cmd "cvs login"
    exp_send "$cmd\r"

    while { $try <= $max_try } {

        expect {

            -re "(.*)ogin:(.*)" {
                set msg $expect_out(buffer)
                exp_send "$user\r"
                exp_continue
            }

            -re "(.*)assword:(.*)" {
                set msg $expect_out(buffer)
                exp_send "$pass\r"
                expect {
                    $prompt {
                         exp_send "cvs co $module\r"
                         set msg $expect_out(buffer)
                         exp_continue
                    }
                }
            }

            -re {(.*)~]#} {
                exp_send "\r"
                set msg $expect_out(buffer)
                return [list 1 "$expect_out(buffer)"]
            }

            -timeout 30 timeout {
                puts "Timedout waiting for reply \($try/$max_try\)"
                incr try 1
                exp_send "\r"
                continue
            }

            eof {
                set ret $expect_out(buffer)
                puts "Connection failed"
                return [list 0 ""]
             }
        }
    }
    return [list 0 ""]
}

###############################################################################
##
## MAIN
##
###############################################################################

set pkglst [list expect tcl httpd mysql-server php php-mysql phpmyadmin itcl mysqltcl tcllib]
set module "automation"
set cvstype pserver

set okargs [list -u -p -l -m]
set usage "Available arguments are:\n"
append usage "\t-m\ta for auto or i for interactive mode\n"
append usage "\t-u\troot user name\n"
append usage "\t-p\troot user password\n"
append usage "\t-l\tenable expect user logging\n"

foreach arg $argv {
    if { [regexp -- {-} $arg match] } {
        set argarr($arg) "[lindex $argv [expr [lsearch $argv $arg] + 1]]"
        if { [lsearch $okargs $arg] < 0 } {
            puts "Unsupported argument: \"$arg\""
            puts "$usage"
            exit
        }
    }
}

if { ![info exists argarr(-m)] } {
    puts "Please select the mode -m"
    puts "$usage"
    exit
}

switch $argarr(-m) {
    "i" {
        set ::mode i
        if { ![info exists argarr(-u)] && ![info exists argarr(-p)]} {
            puts "need root user and password for interactive mode"
            puts "$usage"
            exit
        }
    }

    "a" {
        set ::mode a
    }
}

if { ![info exists argarr(-l]} {
    log_user 1
} else {
    log_user 0
} 

if { $::mode == "i" } {
    # Connect to the server.
    puts "Connecting to localhost .."
    set spawn_id [connect $argarr(-u) $argarr(-p)]
    if { $spawn_id == "" } {
        puts "Unable to connect to localhost"
        exit
    }
}

set tfmt {%-10s%-20s%-1s%10s%10s}
set tb "-"
set hr [string repeat "$tb" 10]

puts "Checking for required packages"
set nopkg ""
set inspkg ""

foreach pkg $pkglst {
    puts [format $tfmt $hr $hr$hr $tb  $hr $hr]

    set ret [do_yum info $pkg]
    if { [lindex $ret 0] == 0 } {
        lappend nopkg $pkg
    } else {
        lappend inspkg $pkg
    }

    puts [format $tfmt | $pkg | [lindex $ret 1] |]
}

puts [format $tfmt $hr $hr$hr $tb  $hr $hr]

if {[llength $nopkg] > 0} {
    foreach pkg $nopkg {

        if { $::mode == "i" } {
            while (1) {
                puts "\nDo you want to install \"$pkg\" now? (y/n) ..."
                interact { 
                    "n" {
                         send_user "Warning - $pkg is a required package ...\n"
                         break
                    }
    
                    "y" {
                         puts "Installing package : $pkg ... Please wait"
    
                         set ret [do_yum install $pkg]
                         if { [lindex $ret 0] == 0 } {
                             puts "Package $pkg is NOT installed."
                             continue
                         } else {
                             puts "Package $pkg is installed."
                             puts [format $tfmt $hr $hr$hr $tb  $hr $hr]
                             puts [format $tfmt | $pkg | [lindex $ret 1] |]
                             puts [format $tfmt $hr $hr$hr $tb  $hr $hr]
                             lappend inspkg $pkg
                         }
    
                         break
                    }

                    \003   exit

                    -re (.*) {
                         continue
                    }
                }
            }
        } else {
            puts "\nInstalling package : $pkg ... Please wait"
            set ret [do_yum install $pkg]
            if { [lindex $ret 0] == 0 } {
                puts "Package $pkg is NOT installed."
                continue
            } else {
                puts "Package $pkg is installed."
                puts [format $tfmt $hr $hr$hr $tb  $hr $hr]
                puts [format $tfmt | $pkg | [lindex $ret 1] |]
                puts [format $tfmt $hr $hr$hr $tb  $hr $hr]
                lappend inspkg $pkg
            }
        }
    }
}

# start the mysql server
if { [lsearch $inspkg "mysql-server"] >= 0 } {
    puts "\nStarting the mysql server ..."
    set cmd "/etc/init.d/mysqld restart"
    set ret [exec_$::mode $cmd]

    if { [regexp {Starting MySQL:.*OK} [lindex $ret 1] match] } {
        puts "MYSQL server started successfully"
    } else {
        puts "MYSQL server did not start ... Exitng Script!"
        exit
    }

    puts "\nCreating mysql tables."
    set cmd "mysql -u root mysql < mysql.sql"
    set ret [exec_$::mode $cmd]

    set cmd "mysql -u root -e'create database btc_db;'"
    set ret [exec_$::mode $cmd]

    set cmd "mysql -u root btc_db < btc.sql"
    set ret [exec_$::mode $cmd]
}


if { [lsearch -exact $inspkg "php"] >= 0 } {
    # open, read, and close the original file
    set phpf "/etc/php.ini"
    set fd [open $phpf r]
    set data [read $fd]
    close $fd 

    if { [regsub -all {short_open_tag\s+=\s+OFF} $data {short_open_tag = ON} datamod] > 0 } {
        puts "\nEnabling php short tags in $phpf"
        file rename -force $phpf "${phpf}.orig"
        set fd [open $phpf w]
        puts $fd $datamod
        close $fd 
    }
}

if { [lsearch -exact $inspkg "phpMyAdmin"] >= 0 } {
    # open, read, and close the original file
    set phpf "/etc/httpd/conf.d/phpMyAdmin.conf"
    set fd [open $phpf r]
    set data [read $fd]
    close $fd

    if { [regsub -nocase -all {deny\s+from\s+all} $data {deny from None} datamod] > 0 } {
        puts "\nEnabling phpmyadmin acees in $phpf"
        file rename -force $phpf "${phpf}.orig"
        set fd [open $phpf w]
        puts $fd $datamod
        close $fd
    }
}

if { [lsearch -exact $inspkg "httpd"] >= 0 } {
    puts "\nStarting the httpd server ..."
    set cmd "/etc/init.d/httpd restart"
    set ret [exec_$::mode $cmd]

    if { [regexp {Starting httpd:.*OK} [lindex $ret 1] match] } {
        puts "HTTPD server started successfully"
    } else {
        puts "HTTPD server did not start ... Exitng Script!"
        exit
    }
}

# check out the CVS module.
if { [info exists spawn_id] && $spawn_id != "" } {
    while (1) {
        puts "\nDo you want to check out the CVS module now? (y/n) ..."
        interact {
            "n" {
                 send_user "Please make sure to check out the module later ...\n"
                 break
             }

             "y" {
                 puts "Please enter your CVS user name ..."
                 interact {
                    -echo -re "(.*)\r" {
                         set uname $interact_out(1,string)
                         return
                    }
                }

                 puts "Please enter your CVS user password ..."
                 interact {
                    -echo -re "(.*)\r" {
                         set password $interact_out(1,string)
                         return
                    }
                }

                set ret [cvs_${cvstype}_co $uname $password $module]
                if { [lindex $ret 0] == 0 } {
                    puts "Could not check out the CVS module $module"
                } else {
                    puts "CVS module $module checked out successfully"
                }

                break
            }

            \003   exit

            -re (.*) {
                 continue
            }
        }
    }
} else {
    puts "\nTo do a CVS checkout of $module follow these steps:"
    puts "1. Set the CVS root to :pserver:<username>@cvs-server:/export/cvsroot.\n\t(export CVSROOT=:pserver:<username>@cvs-server:/export/cvsroot)"
    puts "2. Do a \"cvs login\" and insert your cvs password.\n\t(cvs login)"
    puts "3. Do a \"cvs checkout\" of the module.\n\t(cvs checkoout $module)."
}

puts "\nEnd install script"
exit
