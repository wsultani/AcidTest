#!/usr/bin/expect --

if {[lindex $argv 1] != ""} {
  set resdir [lindex $argv 1]
} else {
  set resdir ""
}

foreach srcfile [lindex $argv 0] {
  puts "Generating csv file for $srcfile"

  if [catch "source $srcfile" ret] {
    puts "Error sourcing $srcfile - $ret"
    puts "Exiting script"
    exit
  }

  if ![array exists CLI] {
    puts "CLI array not found in $srcfile"
    puts "please make sure the cli data is in the format \"set CLI(<cmd>) {<output>}\""
    puts "Exiting Script"
    exit
  }

  set dstfile "[file rootname $srcfile].csv"
  set fout [open [file join "$resdir" "$dstfile"] w]
  puts "writing results to [file join $resdir $dstfile]"
  
  foreach cli [array names CLI] {
    set out ""
    foreach line [split $CLI($cli) "\n"] {
      if {[llength $line] <= 0 || [regexp {[\[\?]} $line match] } {
        continue
      }
      append out "$line\n"
    }
  
    puts "\"$cli\",\"[string trim $out]\""
    puts $fout "\"$cli\",\"[string trim $out]\""
  
  }
}

puts "Script Done"
exit
