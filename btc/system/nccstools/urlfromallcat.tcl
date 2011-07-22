#! /usr/bin/tclsh

source categories.data
set dns "192.168.1.1"
set sequence ""

set fo1 [open avalanche.txt w]
set fo2 [open dns_list.txt w]
foreach cat [lsort -integer [array names category]] {

    if {$cat == "00"} {
        continue
    }

    if {$sequence != "" && $cat != $sequence} {
        puts $fo1 "[string repeat "#" 100]"
        for {set x $sequence} {$x < $cat} {incr x} {
            puts $fo1 "# Catagories - $x Missing catagory sequence"
        }
        puts $fo1 "[string repeat "#" 100]"
    }

    puts "$cat - [lindex $category($cat) 0]"
    puts $fo1 "# $comments($cat)"
    puts $fo1 "1 get [lindex $category($cat) 0]"

    set ret [regexp {http://(\S+)/} [lindex $category($cat) 0] match url]
    puts $fo2 "$dns [lindex [split $url "/"] 0]"

    set sequence [expr $cat + 1]
}
close $fo1
close $fo2
