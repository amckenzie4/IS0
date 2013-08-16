<?php

#########################################################################
#
#  This generates the bind9 zone files.  The sequence is:
#
#	1) Remove all files from the working directory
#	2) Retrive zone information from the DB
#	3) For each zone:
#		a) create the lookup file and write the headers to it
#		b) retrieve subnet information for all appropriate subnets
#		c) for each subnet:
#			i) open the subnet reverse lookup file and write the headers to it
#			ii) retrieve host data for the subnet from the DB
#			iii) write each host to the lookup and reverse lookup files
#			iv) close the reverse lookup file
#		d) write the file location to the apropriate /16 reverse lookup
#			file.
#	4) Close any open files.
#
#########################################################################


# Anything written to the screen should be centered.

echo '<center>' . "\n";

# Establish the DB object

$dbh = new db_iface('database');

# Find the current date
$cdate = date('Ymd');

# Step 1 - Delete all the old files
$dir = $config['system']['named_conf_loc'];

$command = "rm -rf " . $dir . "*";

system($command);


# Step 2 - Retrieve zone info from DB
clear_query();

$query = "SELECT * FROM zone WHERE active IS NULL OR active='yes'";
$query_terms[] = "";
$zones = $dbh->query($query,$query_terms);

if(isset($zones))
 { # Begin zones
 	foreach($zones as $zone)
	 { # Begin processing zones
		# Update the screen.
		echo "<p>Processing zone {$zone['zone']}...</p>";
		# Find out when the zone was last updated, and figure out
		# what the serial number should be.
		if($zone['change_date'] != $cdate)
		 { # Begin generating serial number
			$sernum = '1';
			$serial = $cdate . $sernum;
		 }
		else 
		 { 
			$sernum = $zone['serial'] + 1;
			$serial = $cdate . $sernum;
		 } # End generating serial number
		# Now put the serial and change_date in the DB
		clear_query();
		$query = "UPDATE zone SET serial=?,change_date=? WHERE id=?";
		$query_terms = array($sernum,$cdate,$zone['id']);
		$result = $dbh->query($query,$query_terms);

		$filename = $dir . $zone['zone'] . ".zone";
		# 3a - Create the file handle, and write headers
		$zonefile = fopen($filename, 'w');
		
		$headers = '; Created by DW.  Do not edit by hand!' . "\n";
		$headers .= "; {$zone['comments']}\n";
		$headers .= "\$TTL {$zone['ttl']}\n\n";
		$headers .= "@	IN	SOA	{$zone[zone]}.	{$zone['admin_email']}. (\n";
		$headers .= "			{$serial}		; serial\n";
		$headers .= "			{$zone['refresh']}		; refresh\n";
		$headers .= "			{$zone['retry']}		; retry\n";
		$headers .= "			{$zone['expiration']}		; expiration\n";
		$headers .= "			{$zone['minimum']} )		; minimum\n\n";

		if($zone['ns1'])
		 {	$headers .= "		NS	{$zone['ns1']}.\n"; }
		if($zone['ns2'])
		 {	$headers .= "		NS	{$zone['ns2']}.\n"; }
		
		$headers .= "\n";

		$zone_headers = $headers;

		fwrite($zonefile, $headers);
		
		# 3b - retrieve subnet information
		clear_query();
		$query = "SELECT subnet,dns1,dns2 FROM subnet WHERE zone=? AND (bind_active IS NULL OR bind_active='yes')";
		$query_terms[] = $zone['zone'];
		$subnets = $dbh->query($query, $query_terms);

		foreach($subnets as $subnet)
		 { # Begin processing subnets
			echo "<p>&nbsp;&nbsp;&nbsp...writing " . $srlf . "</p>\n";
			# 3ci - define the reverse lookup file and open it for writing.
			#		- figure out what the appropriate larger reverse file  is, 
			#			if any, and create a handler for it.
			
			#### For now, EVERYTHING is part of a larger 16 bit subnet.
			#### The next bunch of commented lines reflect that, but will
			#### eventually be useful when I build some intelligence in.
			#if(!preg_match('/\/24$/', $subnet['subnet']))
			# { # Figure out the filename for the subnet
				$sn_arr = explode('.', $subnet['subnet']);
				$rev_subnet = "{$sn_arr[3]}.{$sn_arr[2]}.{$sn_arr[1]}.{$sn_arr[0]}";
				$srlf = preg_replace('/\//', '_', $rev_subnet) . '.in-addr.arpa.zone'; 
				$rvlookup_file = $dir . $srlf;
				$non_24 = 'y';


			# } # End figuring out the filename
			#else
			# { # Begin figuring out filename for /24 subnets
			#	$sn_arr = explode('.', $subnet['subnet']);
			#	$rev_subnet = "{$sn_arr[2]}.{$sn_arr[1]}.{$sn_arr[0]}";
			#	$srlf = $rev_subnet . '.in-addr.arpa.zone';
			#	$non_24 = 'n';
			# } # End figuring out filename for /24 subnets
		
			#### Insert stuff for intelligent sizing of reverse lookup files
			#### here.  Until then, everything that's not a /24 is a /16

			if($non_24 = 'y') { $subnet_size = '16'; }
			if($subnet_size == '16')
			 {	# Generate the name for the /16 file.
				$slash16 = "{$dir}{$sn_arr[1]}.{$sn_arr[0]}.in-addr.arpa.zone";
			 } # End /16 file name

			$rvlookup = fopen($rvlookup_file, 'a');

			# Now figure out the headers.
			clear_query();
			$query = "SELECT * FROM file_header WHERE file=?";
			$query_data[] = $srlf;
			$subnet_headers = $dbh->query($query, $query_data);

			if($subnet_headers)
			 { # Begin subnet headers
				$sheaders = preg_replace('/\r\n/', "\n\t\t", $subnet_headers[0]['header_text']);
			 } 
			else
			 {
				$sheaders = $zone_headers;
			 } # End subnet headers

			#echo "<p>Writing sheaders to {$rvlookup_file}: ";
			#echo $sheaders . "\n\n";

			fwrite($rvlookup, $sheaders);


			#3cii - retrieve host data for the subnet
			clear_query();
			$query = "SELECT name,ip,type,wireless_ip,wireless_subnet FROM hosts WHERE (subnet=? and (status IS NULL or status='on')) OR (wireless_subnet=? and (status IS NULL or status='on'))";
			$query_terms[] = $subnet['subnet'];
			$hosts = $dbh->query($query,$query_terms);

			#3ciii - write the hosts to both the lookup and reverse lookup files
			foreach($hosts as $host)
			 { # Begin writing hosts

				# Create the lookup entry:
				$lookup = "{$host[name]}	IN	{$host[type]}	{$host[ip]}\n";
				# Create the reverse lookup entry, assuming /16
				$ip_arr = explode('.', $host['ip']);
				$reverse_lookup = "{$ip_arr[2]}.{$ip_arr[3]}			PTR	{$host[name]}.{$zone[zone]}.\n";
			
				fwrite($rvlookup, $reverse_lookup);
				fwrite($zonefile, $lookup);
				unset($lookup);
				unset($reverse_lookup);
			 } # End writing hosts
			

			#3civ - close the reverse lookup file

			fclose($rvlookup_file);

			#3cv - write to the larger reverse file if necessary and close it.
			
			if(isset($slash16))
			 { # Write the big file
				$bigfile = fopen($slash16, 'a');
				$includeline = '$INCLUDE "' . $srlf . "\";\n";
				fwrite($bigfile, $includeline);
				fclose($bigfile);
  			 } # End the big file
			# Close out variables we'll need again.
			unset($non_24);
			unset($hosts);
		} # End processing subnets
	 } # End processing zones
 } # End zones
else
 { # Begin no zones
	echo '<p>No zones found to process -- check your database!</p>';
 } # End no zones

 









?>
