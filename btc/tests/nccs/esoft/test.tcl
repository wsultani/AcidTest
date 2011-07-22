
##########################################################################
#
# GUI2Script Generated Test
#
##########################################################################
#
# COMMANDER INFO
# - Originating Test Name    : esoft
# - Originating Project Name : NCCS_Feature_Tests
#
##########################################################################
# Script Created: 2011/05/10 17:08:14
##########################################################################

#
# Initialization of user boolean(s)
#
set bEnableLogging 0; # Enable (1) to create a log file for enhanced debugging

set bEnableSNMPMonitoring 0; # enables SNMP monitoring, needs ./files/snmp/snmp_config.xml

set env(SPIRENT_TCLAPI_ROOT)        /usr/local/stc/Layer_4_7_Auto_Linux_3.60/Layer_4_7_Application_Linux/TclAPI
set env(SPIRENT_TCLAPI_LICENSEROOT) /usr/local/stc/license

#
# Initialization of status boolean(s)  
#
set bTestActive 0

if {[catch {
    #
    # Load the SPI_AV package
    #
    set auto_path [linsert $auto_path 0 $env(SPIRENT_TCLAPI_ROOT)]
    package forget SPI_AV
    package require SPI_AV
    
    #
    # Initialize the API
    #
    SPI_AV::InitializeAPI
    
    #
    # Add the appropriate license(s)
    #
    SPI_AV::AddLicense [file join $env(SPIRENT_TCLAPI_LICENSEROOT) "R08070008_R08070010.xml"]
    
    #
    # Source the config file
    #
    source "./config.tcl"

    
    # To activate client subnet validation,
    # take the comments out of the following 2 lines.
    #puts "Started client subnet validation"
    #SPI_AV::Validation::ValidateClientSubnets configArray
	
    # To activate server subnet validation,
    # take the comments out of the following 2 lines.
    #puts "Started server subnet validation"
    #SPI_AV::Validation::ValidateServerSubnets configArray


    #
    # Set test directory and test id
    #
    set testDirectory [pwd]
    set testId "esoft"
    
    #
    # Enable logging if required
    #
    if {$bEnableLogging} {
        SPI_AV::DebugLogFile on
    }

    #
    # Create Client Cluster
    #
    set clientUnits [list]
    lappend clientUnits "192.168.100.202;6"
    set clientClusterID [SPI_AV::ClusterController::CreateCluster "client" "MyClientCluster" $clientUnits]
    puts "Created client cluster"
    
    #
    # Create Server Cluster
    #
    set serverUnits [list]
    lappend serverUnits "192.168.100.202;7"
    set serverClusterID [SPI_AV::ClusterController::CreateCluster "server" "MyServerCluster" $serverUnits]
    puts "Created server cluster"
    

    #
    # Attempt to reserve all Appliance units/port groups
    #
    set allUnits [concat $clientUnits $serverUnits]
    puts "Reserving appliance port group(s)"
    set previousReserveStatus [SPI_AV::Appliance::ReserveAppPortGroups $allUnits]
    puts " - done"

    #
    # Define statistics callback procedures
    #

    
    set clientStatsCallbackID [SPI_AV::ClusterController::RegisterStatsCallback $clientClusterID clientStatsCallbackProc [list "time*" "http"]]
    set serverStatsCallbackID [SPI_AV::ClusterController::RegisterStatsCallback $serverClusterID serverStatsCallbackProc [list "tcpConn" "time*" ]]
    #
    # Define a Client callback procedure
    #
    proc clientStatsCallbackProc {clusterID data} {
    array set dataArray $data
    catch {
    puts "Client Attempted    : $dataArray(http,attemptedTxns)"
    puts "Client Successful   : $dataArray(http,successfulTxns)"
    puts "Client Unsuccessful : $dataArray(http,unsuccessfulTxns)"
    puts "Client Aborted      : $dataArray(http,abortedTxns)"
    puts "\tTIME (seconds)  Elapsed   : $dataArray(timeElapsed)"
    puts "\t\t\tRemaining : $dataArray(timeRemaining)"
    }
    puts ""
    }
    
    #
    # Define a Server callback procedure
    #
    proc serverStatsCallbackProc {clusterID data} {
    array set dataArray $data
    
    catch {
    puts "Server Per second        : $dataArray(tcpConn,connsPerSec)"
    puts "Server Open              : $dataArray(tcpConn,openConns)"
    puts "Server Closed with error : $dataArray(tcpConn,closedWithError)"
    puts "Server Closed with reset : $dataArray(tcpConn,closedWithReset)"
    puts "Server Closed no error   : $dataArray(tcpConn,closedWithNoError)"
    puts "\tTIME (seconds)  Elapsed   : $dataArray(timeElapsed)"
    }
    puts ""
    }
    

    #
    # Generate the test
    #
    puts -nonewline "Generating Test"; flush stdout
    SPI_AV::ClusterController::GenerateClusteredTest configArray $testDirectory $testId
    puts " - done"


    #
    # Stop the cluster(s)
    #
    puts -nonewline "Stopping Client Cluster"; flush stdout
    SPI_AV::ClusterController::StopClusteredTest $clientClusterID
    SPI_AV::ClusterController::WaitForClusteredTestCompletion $clientClusterID
    puts " - done"
    
    puts -nonewline "Stopping Server Cluster"; flush stdout
    SPI_AV::ClusterController::StopClusteredTest $serverClusterID
    SPI_AV::ClusterController::WaitForClusteredTestCompletion $serverClusterID
    puts " - done"
    

    #
    # Remove any previous copy of the test from the cluster(s)
    #
    puts -nonewline "Removing Test From Client Cluster"; flush stdout
    SPI_AV::ClusterController::RemoveClusteredTest $clientClusterID $testId
    puts " - done"
    
    puts -nonewline "Removing Test From Server Cluster"; flush stdout
    SPI_AV::ClusterController::RemoveClusteredTest $serverClusterID $testId
    puts " - done"
    

    #
    # Upload the test to the cluster(s)
    #
    puts -nonewline "Uploading Tests To Client and Server Clusters"; flush stdout
    SPI_AV::ClusterController::UploadAllClusteredTests [list $clientClusterID $serverClusterID] $testDirectory $testId
    puts " - done"
    

    # Status variable update
    set bTestActive 1

    #
    # Start the SNMP monitoring
    #
    if {$bEnableSNMPMonitoring} {
        puts -nonewline "Starting SNMP Polling"; flush stdout
        set snmpConfigFile [file join $testDirectory "files" "snmp" "snmp_config.xml"]
        set port [SPI_AV::StartSNMPPolling $testId $testDirectory $snmpConfigFile]
        puts " - done"
    }

    #
    # Start the test
    #
    puts -nonewline "Starting Server Cluster"; flush stdout
    SPI_AV::ClusterController::StartClusteredTest $serverClusterID $testId
    puts " - done"
    
    puts "Starting Client Cluster"; flush stdout
    SPI_AV::ClusterController::StartClusteredTest $clientClusterID $testId
    puts "Client cluster is SUCCESSFULLY started"
    

    #
    # Wait for the client cluster to finish
    #
    puts "Waiting For Client Cluster To Complete"; flush stdout
    SPI_AV::ClusterController::WaitForClusteredTestCompletion $clientClusterID
    puts "Client Cluster Test Completed"
    
    #
    # Stop the server cluster (and wait for it to stop)
    #
    puts -nonewline "Stopping Server Cluster"; flush stdout
    SPI_AV::ClusterController::StopClusteredTest $serverClusterID
    SPI_AV::ClusterController::WaitForClusteredTestCompletion $serverClusterID
    puts " - done"
    
    #
    # Stop the SNMP monitoring
    #
    if {$bEnableSNMPMonitoring} {
        puts -nonewline "Stopping SNMP Polling"; flush stdout
        puts [SPI_AV::StopSNMPPolling $port]
        unset port
    }

    # Status variable update
    set bTestActive 0

    #
    # Download test results
    #
    set resultsDirectory [file join $testDirectory $testId "results"]
    puts -nonewline "Transferring Client Cluster Results"; flush stdout
    SPI_AV::ClusterController::GetClusteredTestResults $clientClusterID $testId [file join $resultsDirectory]
    puts " - done"
    
    puts -nonewline "Transferring Server Cluster Results"; flush stdout
    SPI_AV::ClusterController::GetClusteredTestResults $serverClusterID $testId [file join $resultsDirectory]
    puts " - done"
    
} catchError]} {
    puts "An exception occurred: $catchError"
    puts "ErrorInfo:"
    puts "$::errorInfo"
    
    if {$bTestActive} {
       catch {SPI_AV::ClusterController::AbortClusteredTest $clientClusterID}
       catch {SPI_AV::ClusterController::AbortClusteredTest $serverClusterID}
    }
    
    # Stop the poller if it is alive
    if [info exists port] {
        catch {SPI_AV::StopSNMPPolling $port}
    }

    catch {SPI_AV::Dump}
}

#
# Unregister the previously registered callbacks
#
catch {SPI_AV::ClusterController::UnregisterStatsCallback $clientClusterID $clientStatsCallbackID}
catch {SPI_AV::ClusterController::UnregisterStatsCallback $serverClusterID $serverStatsCallbackID}

# Release the reserved Appliance port group(s)
catch {SPI_AV::Appliance::ReleaseAppPortGroups $previousReserveStatus}

catch {SPI_AV::CleanUp}

puts "Done!"

