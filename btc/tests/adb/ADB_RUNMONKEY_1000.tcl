#!/usr/bin/expect --

proc ADB_RUNMONKEY_1000 {} {
#--------------------------------------------------------------------------------------------#
variable p [namespace tail [lindex [info level 0] 0]]
set ${p}(Name)		$p
set ${p}(type)		{testcase}
set ${p}(Description)	{Run the nrsensors cmd and look for a zero value. If found fail test, else pass}
set ${p}(Suite)		{hardware}
set ${p}(Comments)	{}
set ${p}(Author)	{wsultani}
set ${p}(Required)	{::adb}
set ${p}(Created)	{08-06-27}
set ${p}(EOL)		{}
update_mysql_db ${p}
#--------------------------------------------------------------------------------------------#

    upvar #1 marray marr
    set pkt $marr(-build)

    set evcount 1000
    set margs(--throttle) 100
    set margs(--pct-majornav) 0
    set margs(--pct-syskeys) 0
    set margs(--pct-touch) 50
    set margs(--pct-motion) 50
    set margs(--seed) 50

    set ret [$::adb adb_runmonkey $pkt "$evcount" "margs"]
    if { [problem_with $ret] } {
        write_error "[data_from $ret]"
        return [list false "[data_from $ret]"]
    }

    write_info [data_from $ret]

    return [list true [data_from $ret]]
}
# this is required for auto loading
package provide ADB_RUNMONKEY_1000 1.0
