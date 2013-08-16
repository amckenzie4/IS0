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
#               ddns-hostname [hostname]
#			 }
#		 } # End group [name]
#
#  
#
#########################################################################

# text generation functions have now been outsourced to
# dhcpd_gen_functions.php, so that needs to be included.
$include1 = $mod_to_include . '/dhcpd_gen_functions.php';
include($include1);

# We're going to have output, sooner or later, and we want it centered.
echo '<center>' . "\n";

# Establish DB object.

$dbh = new db_iface('database');

# Establish the output file

$f = fopen($config['system']['dhcpd_conf_loc'], "w");

$log_msg = "dhcpd.conf will be written to " . $config['system']['dhcpd_conf_loc'];
logger('info', $log_msg);

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

# First we need to deal with all of the actual shared networks.
foreach($shared_networks as $sn)
{ # Begin foreach($shared_networks)
   if($sn['network_name'] == 'none')
    { # Deal with unshared subnets
       echo "<p>Bypassing shared network 'none'</p>";
   	   $sn_none = $sn;
    } # End dealing with unshared subnets
   else
   { # Begin exclude sn = none
      $text = "# " . $sn['comments'] . "\n";
      $text .= "shared-network $sn[network_name] { \n";
      $text .= "# Begin shared network " . $sn['network_name'] . "\n\n";
      
      # We now need to include all the appropriate subnets.
      # That is done with the network_subnet() function.
      $text .= network_subnets($sn['network_name']);
      
      $text .= " } # End shared network $sn[network_name]\n\n\n";
      fwrite($f, $text);
   } # End exclude sn = none
   # We really, really, REALLY don't want to keep the old $text.
   # That would be Bad.	
   unset($text);   
} # End foreach($shared_networks)

# Shared networks dealt with, we need to cope with subnets that
# aren't in any shared networks.  DW3 differentiates these by 
# claiming they're in a shared network called "none".

if(isset($sn_none))
{ # Start unshared subnets
   $text = "# The following subnets are not in any shared network.\n\n";
   $text .= network_subnets($sn['network_name']);
   $text .= "\n\n";
   
   fwrite($f, $text);
   # We still don't want to keep $text.  It's Bad, I tell you!
   unset($text)  ;
} # End unshared subnets

# So shared networks and subnets are done.  Great.
# Now comes the hard part:  hosts.

# First get a list of all groups

clear_query();
$query = "SELECT * FROM org_grp";
$query_terms[] = '';
$groups = $dbh->query($query, $query_terms);

# We'll want a count of processed groups, so let's start counting at 0
$groupcount = 0;

# Start stepping through the subnets, adding to $text as necessary.
foreach($groups as $group)
{ # Begin foreach($groups)
   # First, figure out if the group has any hosts in it.  We don't want any host
   # that is either a CNAME record or has status set to 'off'.
   clear_query();
   $query = "SELECT hosts.*,subnet.zone FROM hosts,subnet WHERE hosts.grp=? AND hosts.status != 'off' AND hosts.type != 'cname' AND hosts.subnet=subnet.subnet";
   $query_terms[] = $group['grp'];
   $grp_hosts = $dbh->query($query,$query_terms);
   
   if($grp_hosts)
   {
      # Call the external function to generate the host listing.
      # This function returns an array, where the element 0 is the
      # requested text listing, and element 1 is the number of host entries
      # generated.
      $text = "group " . $group['grp'] . "{\n";
      $text .= "\n";
      
      $hosts = group_hosts($grp_hosts);
      $text .= $hosts[0];
      $hostcount = $hosts[1];
   }
   $text .= "} # End group " . $group['grp'] . "\n\n"; # End group	
   fwrite($f, $text);
   echo "<p>Total hosts processed:  " . $hostcount . " in group " . $group['grp'] . "</p>\n";

} # End foreach($groups)

echo "<br><br><p>Total groups processed: " . $groupcount . "</p><br>\n";
?>