<?php

#########################################################################
#
#	This generates the dhcpd.conf file.  The general form for the file is:
#
#		[headers]
#
#		shared-network [shared network name]
#		 { # Begin shared network [name]
#			subnet [subnet name]
#			 {
#				[subnet directives]
#			 }
#			subnet [subnet name]
#			 {
#				[subnet directives]
#			 }
#		 } # End shared network [name]
#
#		shared-network [shared network name]
#		 { # Begin shared network [name]
#			[subnets]
#		 } # End shared network [name]
#
#		group [group name]
#		 { # Begin group [name]
#			host [FQDN]
#			 {
#				hardware ethernet [MAC address];
#				fixed-address [IP];
#			 } 
#			host [FQDN] // pooled host
#			 {
#				hardware ethernet [MAC address];
#			 }
#		 } # End group [name]
#
#  
#
#########################################################################

echo '<center>' . "\n";

# Establish DB object.

$dbh = new db_iface('database');

# Establish the output file

$f = fopen($config['system']['dhcpd_conf_loc'], "w");

$log_msg = "dhcpd.conf will be written to " . $config['system']['dhcpd_conf_loc'];
logger(info, $log_msg);

# Pull the headers from the file_header table and write them to the 
# output file.
clear_query();

$query = "SELECT * FROM file_header WHERE file=?";
$query_terms[] = "dhcpd.conf";

$headers = $dbh->query($query, $query_terms);

if(isset($headers))
 { # Write headers
	$text = preg_replace('/\r\n/', "\n", $headers[0]['header_text']);
	$text .= "\n\n";
	fwrite($f, $text);
	echo 'Wrote headers to dhcpd.conf' . "<br/>\n";
 } # End writing headers
else { echo "Failed to find headers!<br>\n"; }


# Headers written, pull a list of all shared networks. 

clear_query();
$query = "SELECT * FROM shared_network ORDER BY network_name";
$query_terms[] = "";

$shared_networks = $dbh->query($query, $query_terms);

# Now we get to the fun part.  While entering shared networks and their
# contents, we need to loop.  Each shared network contains one or more
# subnets, and each one needs to be set up individually. So...

#echo 'shared_networks is '; print_r($shared_networks); echo "<br>\n";


foreach($shared_networks as $sn)
 { # Begin writing shared networks
	# Begin the shared-network.
	if($sn['network_name'] == 'none')
	 { # Deal with unshared subnets
		$sn_none = $sn;
	 } # End dealing with unshared subnets
	else
	{ # Begin exclude sn = none
		$text = <<<EOL

# $sn[comments]
shared-network $sn[network_name] { 
# Begin shared network $sn[network_name]

EOL;
	
		# Get the list of all subnets in the shared network
		clear_query();
		$query = "SELECT * FROM subnet WHERE shared_network=? AND dhcpd_active IS NULL ORDER BY subnet";
		$query_terms[] = $sn['network_name'];
		$subnets = $dbh->query($query, $query_terms);

		# Start stepping through the subnets, adding to $text as necessary.
		foreach($subnets as $subnet)
		 { # Begin listing subnet
			##if ($subnet['subnet'] != 'pool') 
			## { # Begin exclude subnet = pool
				$text .= "\n";
				$text .= "   subnet " . $subnet['subnet'] . " netmask " . $subnet['mask'] ." {\n";
				if(isset($subnet['dns1']))
				 { # Begin dns option
					$text .= "		option domain-name-servers " . $subnet['dns1'];	
					if (isset($subnet['dns2']))
						{ $text .= ', ' . $subnet['dns2']; }
					$text .= ";\n";
				 } # End dns option

				if(isset($subnet['broadcast']))
				 { # Begin broadcast option
					$text .= '		option broadcast-address ' . $subnet['broadcast'] . ";\n";
				 } # End broadcast option
				
				if(isset($subnet['mask']))
				 { # Begin mask option
					$text .= '		option subnet-mask ' . $subnet['mask'] . ";\n";
				 } # End mask option

				if(isset($subnet['gateway']))
				 { # Begin routers option
					$text .= '		option routers ' . $subnet['gateway'] . ";\n";
				 } # End routers option

				if(isset($subnet['zone']))
				 { # Begin domain-name option
					$text .= '		option domain-name "' . $subnet['zone'] . '"' . ";\n";
				 } # End domain-name option

				if(isset($subnet['pool1']) && $subnet['pool1'] != "")
				 { # Begin dealing with pools
					$text .= "	pool {\n";
					if(isset($subnet['pool1_dirs']))
					 { # Begin pool1 directives
						$text .= "		" . preg_replace('/\r\n/', "\n\t\t", $subnet['pool1_dirs']);
						$text .= "\n";
					 } # End pool1 directives
					$text .= "		range " . $subnet['pool1'] . "\n";
					$text .= "	}\n";
					
					if(isset($subnet['pool2']))
					 { # Begin dealing with pool 2
						$text .= "	pool {\n";
						if(isset($subnet['pool2_dirs']))
						 { # Begin pool2 directives
							$text .= "		" . preg_replace('/\r\n/', "\n\t\t", $subnet['pool2_dirs']);
							$text .= "\n";
						 } # End pool2 directives
						$text .= "		range " . $subnet['pool2'] . "\n";
						$text .= "	}\n";
					 } # End dealing with pool 2
				 } # End dealing with pools
				$text .= "   } # End subnet " . $subnet['subnet'] . "\n";
			#} # End exclude subnet = pool
		 } # End listing subnet
	
	
		$text .= " } # End shared network $sn[network_name]\n";
		fwrite($f, $text);
	} # End exclude sn = none

 	unset($text);
 } # End writing shared networks

if(isset($sn_none))
 { # Begin dealing with shared network "none"

	$text = "# The following subnets are not in any shared network.\n\n";
	clear_query();
	$query = "SELECT * FROM subnet WHERE shared_network=? ORDER BY subnet";
	$query_terms[] = 'none';
	$subnets = $dbh->query($query, $query_terms);

	# Start stepping through the subnets, adding to $text as necessary.
	foreach($subnets as $subnet)
	 { # Begin listing subnet
		##if ($subnet['subnet'] != 'pool') 
	 	## { # Begin exclude subnet = pool
			$text .= "\n";
			$text .= "subnet " . $subnet['subnet'] . " netmask " . $subnet['mask'] ." {\n";
			if(isset($subnet['dns1']))
			 { # Begin dns option
				$text .= "	option domain-name-servers " . $subnet['dns1'];	
				if (isset($subnet['dns2']))
					{ $text .= ', ' . $subnet['dns2']; }
				$text .= ";\n";
			 } # End dns option

			if(isset($subnet['broadcast']))
			 { # Begin broadcast option
				$text .= '	option broadcast-address ' . $subnet['broadcast'] . ";\n";
			 } # End broadcast option
			
			if(isset($subnet['mask']))
			 { # Begin mask option
				$text .= '	option subnet-mask ' . $subnet['mask'] . ";\n";
			 } # End mask option

			if(isset($subnet['gateway']))
			 { # Begin routers option
				$text .= '	option routers ' . $subnet['gateway'] . ";\n";
			 } # End routers option

			if(isset($subnet['zone']))
			 { # Begin domain-name option
				$text .= '	option domain-name "' . $subnet['zone'] . '"' .";\n";
			 } # End domain-name option

			if(isset($subnet['directives']))
			 { # Begin directives option
				$text .= "	" . preg_replace('/\r\n/', "\n\t", $subnet['directives']);
				$text .= "\n";
			 } # End directives option

			if(isset($subnet['pool1']) && $subnet['pool1'] != "")
			 { # Begin dealing with pools
#print_r($subnet); echo "<br>\n";
				$text .= "	pool {\n";
				if(isset($subnet['pool1_dirs']))
				 { # Begin pool1 directives
					$text .= "		" . preg_replace('/\r\n/', "\n\t\t", $subnet['pool1_dirs']);
					$text .= "\n";
				 } # End pool1 directives
				$text .= "		range " . $subnet['pool1'] . "\n";
				$text .= "	}\n";
				
				if(isset($subnet['pool2']))
				 { # Begin dealing with pool 2
					$text .= "	pool {\n";
					if(isset($subnet['pool2_dirs']))
					 { # Begin pool2 directives
						$text .= "		" . preg_replace('/\r\n/', "\n\t\t", $subnet['pool2_dirs']);
						$text .= "\n";
					 } # End pool2 directives
					$text .= "		range " . $subnet['pool2'] . "\n";
					$text .= "	}\n";
				 } # End dealing with pool 2
			 } # End dealing with pools

			$text .= "} # End subnet " . $subnet['subnet'] . "\n";
		 ##} # End exclude subnet = pool
	 } # End listing subnet


	fwrite($f, $text);
 } # End dealing with shared-network 'none'

# We're done adding subnet definitions, so it's time to look at hosts.

unset($text);
clear_query();
$query = "SELECT * FROM org_grp";
$query_terms[] = '';
$groups = $dbh->query($query, $query_terms);

$groupcount = 0;

# Start stepping through the subnets, adding to $text as necessary.
foreach($groups as $group)
 { # Begin processing groups
	clear_query();
	$query = "SELECT hosts.*,subnet.zone FROM hosts,subnet WHERE hosts.grp=? and hosts.subnet=subnet.subnet";
	$query_terms[] = $group['grp'];
	$grp_hosts = $dbh->query($query,$query_terms);
$groupcount++;
	if($grp_hosts)
	 { # Begin printing group
		$hostcount = 0;
		$text = "group " . $group['grp'] . "{\n";
		$text .= "\n";
		foreach($grp_hosts as $host)
		 { # Begin printing hosts
			if($host['status'] != 'off' || $host['type'] == 'CNAME')
			 { # Begin Status is not Off 
				# Begin the primary entry.
				$text .= '  host ' . $host['name'] . '.' . $host['zone'] . " {\n";
				$text .= '	hardware ethernet ' . $host['mac'] . ";\n";
				if($host['ip'] != 'pool' && $host['ip'] != NULL)
				 { # Begin printing IP
					$text .= '	fixed-address ' . $host['ip'] . ";\n";
				 } # End printing IP	
				$text .= "  }\n\n";

				# Begin the pool entry for pool+static hosts
				if($host['ip'] != 'pool' && $host['pool'] == 'yes')
				 { # Begin pool+static entry
					$text .= '  host ' . $host['name'] . '.pool' . " {\n";
					$text .= '  hardware ethernet ' . $host['mac'] . ";\n";
					$text .= "  }\n\n";
				 } # End pool+static entry

				# Begin the entry for wireless
				if($host['wireless_subnet'])
				 { # Begin wireless entry
					clear_query();
					$query = "SELECT hosts.id,hosts.wireless_subnet,subnet.zone as wireless_zone FROM hosts,subnet WHERE hosts.id=? AND hosts.wireless_subnet=subnet.subnet";
					$query_terms[] = $host['id'];
					$wireless_stuff = $dbh->query($query, $query_terms);
					$host['wireless_zone'] = $wireless_stuff[0]['wireless_zone'];
					$text .= '  host ' . $host['name'] . '.' . $host['wireless_zone'] . " {\n";
					$text .= '  hardware ethernet ' . $host['wireless_mac'] . ";\n";
					if($host['wireless_ip'] != 'pool')
					 { # Begin wireless assigned IP
						$text .= '  fixed-address ' . $host['ip'] . ";\n";
					 } # End wireless assigned IP
					$text .= "  }\n\n";
				 } # End wireless entry
				
			 } # End Status is not Off
			$hostcount++;
		 } # End printing hosts
		$text .= "} # End group " . $group['grp'] . "\n\n"; # End group	
		fwrite($f, $text);
	 } # Finshing printing group
	echo "<p>Total hosts processed:  " . $hostcount . " in group " . $group['grp'] . "</p>\n";
 } # End processing groups


echo "<br><br><p>Total groups processed: " . $groupcount . "</p><br>\n";



?>
