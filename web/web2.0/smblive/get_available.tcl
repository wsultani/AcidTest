#!/usr/bin/expect --

global env

# set up the environment required by smartbits.
set env(LD_LIBRARY_PATH) "/usr/local/smartbits/SmartBitsAPI/bin"
set env(PATH) "$env(PATH):/usr/local/smartbits/SmartBitsAPI/bin"

set sys_ip [lindex $argv 0]
set dir [lindex $argv 1]

if { ![file exists $dir] } {
  if { [catch [file mkdir $dir] ret] }  {
    puts "Could not create $dir .. $ret .. exiting!"
    exit 
  }
}

cd $dir
eval exec smbapi -a $sys_ip >@stdout
  
exit
