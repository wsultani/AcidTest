# Tcl package index file, version 1.1
# This file is generated by the "pkg_mkIndex" command
# and sourced either when an application starts up or
# by a "package unknown" script.  It invokes the
# "package ifneeded" command to set up package-related
# information so that packages will be loaded automatically
# in response to "package require" commands.  When this
# script is sourced, the variable $dir must contain the
# full path name of this file's directory.

package ifneeded adb 1.0 [list source [file join $dir adb.lib]]
package ifneeded bivio 1.0 [list source [file join $dir bivio.lib]]
package ifneeded btc_log 1.0 [list source [file join $dir log.lib]]
package ifneeded btc_utils 1.0 [list source [file join $dir utils.lib]]
package ifneeded btc_write 1.0 [list source [file join $dir write.lib]]
package ifneeded config 1.0 [list source [file join $dir config.lib]]
package ifneeded install_build 1.0 [list source [file join $dir install.lib]]
package ifneeded mailer 1.0 [list source [file join $dir mailer.lib]]
package ifneeded nccs 1.0 [list source [file join $dir nccs.lib]]
package ifneeded pc 1.0 [list source [file join $dir pc.lib]]
package ifneeded performance 1.0 [list source [file join $dir performance.lib]]
package ifneeded rpower 1.0 [list source [file join $dir rpower.lib]]
package ifneeded sai_report 1.0 [list source [file join $dir sai_report.lib]]
package ifneeded stc_avalanche 1.0 [list source [file join $dir stc_avalanche.lib]]
package ifneeded sw 1.0 [list source [file join $dir switch.lib]]
