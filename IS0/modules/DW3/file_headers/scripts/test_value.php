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



#$msg = "input value is " . $input_value;
#$cmd = "echo $msg >> /home/itadmins/amckenzie/public_html/IS0/modules/hosts/scripts/temp.txt";
#exec($cmd);


if($input_name == 'name')
 { # Begin building name tests
	# For hostnames, we want to check the following:
	# - min 2 character.
	# - max 15 characters.
	# - Allowed characters:  A-Z a-z 0-9 -
	# - must contain at least one letter.
	# - must end with a letter or number
   # - not already used in DB

   if(preg_match("/^[0-9a-z\-]{1,14}[0-9a-z]$/i", $input_value))
	 { # If the name is valid, check to see if it exists
		$dbh = new db_iface('database');
		$query = "SELECT name,subnet FROM hosts WHERE name=?";
		$query_term[] = $input_value;

		$result = $dbh->query($query, $query_term);
		if(isset($result))
		 { # Begin dealing with results
			if (count($result) > '1')
			 { # Begin multiple results
				echo "Multiple hosts match that name.";
			 } # End multiple results
			else
			 { # Begin single result
				echo '<span style="color:red;">Host "' . $result[0]['name'] . '" exists in subnet ' . $result[0]['subnet'] . '</span>';
			 } # End single result
		 } # End dealing with results
		else { echo "Hostname is valid and unused."; }
	 } # End valid name
	else { echo "Invalid host name!"; }

 } # End building name tests

if($input_name == 'ip')
 { # Begin building IP tests
	# The IP must be valid, and must not already exist in the DB
	if(preg_match("/\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b/", $input_value))
	 { # Begin checking good IP
		$dbh = new db_iface('database');      
 		$query = "SELECT name,ip FROM hosts WHERE ip=?";
  	 	$query_term[] = $input_value;

   	$result = $dbh->query($query, $query_term);
		if(isset($result))
		 { # Begin dealing with results
			echo '<span style="color:red">IP ' . $result[0]['ip'] . ' is already assigned to ' . $result[0]['name'] . '</span>';
		 } # End dealing with results
		else { echo "IP is valid and not in use"; }
	 } # End checking good IP
	else { echo "IP is not valid"; }
 } # End building IP tests

if($input_name == "mac")
 { # Begin building MAC address tests

	if($input_value == "")
	 { echo ""; }

	if(preg_match("/^([0-9a-f][0-9a-f][:]){5}[0-9a-f][0-9a-f]$/i", $input_value))
	 { # Begin dealing with valid MAC
		$dbh = new db_iface('database');
      $query = "SELECT name,mac FROM hosts WHERE mac=?";
      $query_term[] = $input_value;

      $result = $dbh->query($query, $query_term);
      if(isset($result))
       { # Begin dealing with results
			if(count($result) > '1')
			 { # Begin multiple results
				$i = 1;
				foreach($result as $row)
				 {
					if($i != '1') { $hostlist .= ', '; }
					$hostlist .= $row['name'];
					$i++;
				 }
			 } # End multiple results
			else { $hostlist = $result[0]['name']; }
			echo 'MAC is valid, and in use by: ' . $hostlist;
		 } # End dealing with results
		else { echo "Valid MAC"; }
	} # End dealing with valid MAC

	elseif(preg_match("/^([0-9a-f][0-9a-f][-]){5}[0-9a-f][0-9a-f]$/i", $input_value))
	 { # Begin hyphen case
		$temp = str_replace("-", ":", $input_value);
		echo "Should be $temp";
	 } # End hyphen case
	
	elseif(preg_match(("/^[0-9a-f]{12}$/i"),$input_value))
	 { # Begin no punctuation case
		$temp = "";
		for($i=0;$i<strlen($input_value);$i++)
	    {  
   	   if(preg_match("/[2|4|6|8]|10/i",$i))
      	#if($i == (2|4|6|8|'a'|'A'))
       	 {  
         	$temp .=  ":";
       	 }  
    	  $temp .= $input_value[$i];
    	 }  
  	 	 echo "MAC should be $temp";
	  } # End no punctuation case

	elseif(preg_match("/^([0-9a-f][0-9a-f][\s]){5}[0-9a-f][0-9a-f]$/i", $input_value))
	 {
		$temp = str_replace(" ", ":", $input_value);
		echo "MAC should be $temp";
	 }

	else { echo "<b>Invalid MAC!</b>"; } 
 } # End building MAC address tests



?>
