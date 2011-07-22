#!/usr/bin/expect --

proc ADB_SETTINGS_CHECK_VERSION {} {
#--------------------------------------------------------------------------------------------#
variable p [namespace tail [lindex [info level 0] 0]]
set ${p}(Name)		$p
set ${p}(type)		{testcase}
set ${p}(Description)	{run the TestSettings_CheckVersion robotium testcase}
set ${p}(Suite)		{adb}
set ${p}(Comments)	{}
set ${p}(Author)	{wsultani}
set ${p}(Required)	{::adb}
set ${p}(Created)	{11-07-20}
set ${p}(EOL)		{}
update_mysql_db ${p}
#--------------------------------------------------------------------------------------------#

    set pkg "PeelHandsetTest.apk"
    set path "/var/www/html/automation/btc/tests/adb/"
    set testlst "com.peel.android.SettingsTests.TestSettings_CheckVersion"

    write_step "Installing test pkg - $pkg .."
    set ret [$::adb adb_clean_install [file join $path $pkg]]
    if { [problem_with $ret data] } {
        return $ret
    }

    foreach tc $testlst {
       
        # start a subtest 
        start_subtest $tc

        set ret [$::adb adb_runtest [file join $path $pkg] $tc]
        if { [problem_with $ret data] } {
            write_error [data_from $ret]
            end_subtest [list false [data_from $ret]]
        }

        write_info "Test passed - [data_from $ret]"

        end_subtest [list true [data_from $ret]]
    }

    return [analyze_subtest]
}
# this is required for auto loading
package provide ADB_SETTINGS_CHECK_VERSION 1.0
