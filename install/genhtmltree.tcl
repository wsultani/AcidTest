#!/usr/bin/expect          

if {[llength $argv] > 0 && [lindex $argv 0] != ""} {
    set src [lindex $argv 0]
} else {
    set src [pwd]
}

set url [regsub "/export/scooby" $src "http://192.168.2.30"]

exec -- /usr/bin/tree $src -H "$url" --charset=US -T "Bivio Test Center (BTC) directory tree" -C -I "CVS|pkgIndex.tcl" -o [file join $src index.html]
