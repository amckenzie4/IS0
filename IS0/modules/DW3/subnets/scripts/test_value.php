<?php

$config = parse_ini_file("../../../config/config.ini",true);
$db_config = parse_ini_file("../../../config/config_db.ini",true);

include_once("../../../includes/common_logger.inc");
include_once("../../../includes/common_db.inc");
include_once("../../../includes/common_subnet.inc");

$input_name = $_GET["input"];
$input_value = $_GET["value"];
#$input_name = 'mac';
# Add your test here.

######################################################################
#																							#
#  As you can see above, there are two values passed into this 		#
# script:  input_name, and input_value.  input_name is the name of   #
# the column you're testing, and input_value is whatever has been    #
# entered by the user.  The standard form for a test is as folows:	#
#																							#
#	if ($input_name == 'foo')														#
#	 {																						#
#		# Run tests																		#
#     if(some_test_result) { echo "Good value!"; }							#
#		else { echo "Bad value!"; }												#
#	 }																						#
#																							#
#  Essentially, if the input_name is whatever you're testing, run		#
# whatever tests you want, and return a string.  The testValue java-	#
# script code will take care of inserting that code into the 			#
# appropriate div.																	#
#																							#
#  I've left a few tests in this skel script as demonstrations.  The #
# first checks hostnames, the second IP addresses, and the third MAC #
# addresses.  All three first check for sane values, then check to 	#
# see if the value already exists in a database.							#
#																							#
######################################################################

/*
 # This outputs to a local temp.txt file with the name and input entered.
$msg = "input name is " . $input_name . " and  value is " . $input_value;
$cmd = "echo $msg >> ./temp.txt";
exec($cmd);
*/

if($input_name == 'wins' || $input_name == 'dns1' || $input_name == 'dns2' || $input_name == 'gateway')
 { # Begin building IP tests
	# The IP must be valid
	if(preg_match("/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/", $input_value))
	 { # Begin good IP
		echo "IP is valid"; 
	 } # End good IP
	else { echo "IP is not valid"; }
 } # End building IP tests


if($input_name == 'subnet')
 { # Begin building subnet tests
	# The subnet must be valid, and not already exist in the DB
	if(preg_match("/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\/(([0-9])|([12][0-9])|(3[0-2]))\b/", $input_value))
	 { # Begin checking good IP
		$dbh = new db_iface('database');      
 		$query = "SELECT subnet FROM subnet WHERE subnet=?";
  	 	$query_term[] = $input_value;

   	$result = $dbh->query($query, $query_term);
		if(isset($result))
		 { # Begin dealing with results
			echo '<span style="color:red">IP ' . $result[0]['subnet'] . ' is already assigned</span>';
		 } # End dealing with results
		else { echo "Subnet is valid and not in use"; }
	 } # End checking good IP
	else { echo "Subnet is not valid"; }
 } # End building IP tests
		
















?>
