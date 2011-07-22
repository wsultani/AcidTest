#! /usr/bin/tclsh

if { $argv == "" } {
    puts "\n ERROR: Need required arg : <URL>"
    puts "\tSyntax: dbcatparser.tcl <URL>"
    puts "\t<URL> can be a file or a list\n"
    puts "\tusage: dbcatparser.tcl www.cnn.com www.ebay.com"
    puts "\tusage: dbcatparser.tcl urllist\n"
    exit
}

if {[file isfile $argv]} {
    set inputfile [lindex $argv 0]
    set fh [open $inputfile r]
    set urllst [read $fh]
} else {
    set urllst $argv
}

set dbarr(esoft) 2
set db esoft

set dns "192.168.1.1"
set sequence ""

array set category {}
array set comments {}
set comments(00) "Catagories: - 00 Unknown"

foreach url $urllst {
    if { [catch { exec /usr/bin/cdbutils -i $dbarr($db) -u $url } msg] } {
        puts "Error - unable to query $url : $msg"
        continue
    }

    if { [regexp {Categories:.*} $msg full] } {
        foreach cat [split $full "-"] {
            if { [regexp {(\d+)\s+(.*)} $cat match catid catname] } {
               lappend category($catid) $url
               set comments($catid) [string trim $full]
            }
        }
    } else {
        puts "Error - unable to get category info for $url : $msg"
        lappend category(00) $url
        continue
    }
}

set fo [open categories.data w]
foreach catg [lsort [array names category]] {
    puts $fo "set category($catg) \{$category($catg)\}"
    puts $fo "set comments($catg) \{$comments($catg)\}\n"
}
close $fo

puts "Completed parsing categories"
puts "Generating avalanche configuration files"

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

    #puts "$cat - [lindex $category($cat) 0]"
    puts $fo1 "# $comments($cat)"
    puts $fo1 "1 get [lindex $category($cat) 0]"

    if { [regexp {http://(\S+)/?} [lindex $category($cat) 0] match url] } {
        puts $fo2 "$dns [lindex [split $url "/"] 0]"
    } else {
        puts "Cannot form dns entry for - [lindex $category($cat) 0]"
    }

    set sequence [expr $cat + 1]
}
close $fo1
close $fo2

unset category
unset comments

puts "Completed generating avalanche files"
exit

