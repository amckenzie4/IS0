<?php
# These are functions to generate parts of the dhcpd.conf file.
# They are all called from dhcpd_gen.php.

function network_subnets($shared_network)
{ # Begin function subnet_include

   # Establish DB object. (Re-establish due to scope issues)
   $nsdbh = new db_iface('database');
   $text = NULL;
   
   echo "Processing shared network '" . $shared_network . "'<br>\n";
   # Get the list of all subnets in the shared network
   clear_query();
   $query = "SELECT * FROM subnet WHERE shared_network=? AND dhcpd_active IS NULL ORDER BY subnet";
   $query_terms[] = $shared_network;
   $subnets = $nsdbh->query($query, $query_terms);
   
   # Start stepping through the subnets, adding to $text as necessary.
   foreach($subnets as $subnet)
   { # Begin listing subnet
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
      
       if($subnet['enable_ddns'] == 'yes')
       { # Begin enable-ddns option
          $text .= '		ddns-hostname = concat("dhcp-",binary-to-ascii(10,8,"-",leased-address));';
       } # End enable-ddns option
       
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
      	
      	if(isset($subnet['pool2']) && $subnet['pool2'] != "")
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
   } # End listing subnet
   return $text;
} # End function subnet_include



function group_hosts($grp_hosts)
{ # Begin function group_hosts
   # Establish DB object. (Re-establish due to scope issues)
   $ghdbh = new db_iface('database');

   $text = NULL;
   $hostcount = '0';
   
   foreach($grp_hosts as $host)
   { # Begin foreach($grp_hosts)
      # Start writing hosts.
      $hostcount++;
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
         $text .= '  ddns-hostname ' . $host['name'] . ";\n";
         $text .= "  }\n\n";
      } # End pool+static entry
      
      # Begin the entry for wireless
      if($host['wireless_subnet'])
      { # Begin wireless entry
         clear_query();
         $query = "SELECT hosts.id,hosts.wireless_subnet,subnet.zone as wireless_zone FROM hosts,subnet WHERE hosts.id=? AND hosts.wireless_subnet=subnet.subnet";
         $query_terms[] = $host['id'];
         $wireless_stuff = $ghdbh->query($query, $query_terms);
         $host['wireless_zone'] = $wireless_stuff[0]['wireless_zone'];
         $text .= '  host ' . $host['name'] . '.' . $host['wireless_zone'] . " {\n";
         $text .= '  hardware ethernet ' . $host['wireless_mac'] . ";\n";
         if($host['wireless_ip'] != 'pool')
      	 { # Begin wireless assigned IP
      	   $text .= '  fixed-address ' . $host['ip'] . ";\n";
      	 } # End wireless assigned IP
      	 else 
      	 {
      	    $text .= '  ddns-hostname ' . $host['name'] . ";\n";
      	 }
         $text .= "  }\n\n";
       } # End wireless entry
      
   } # End foreach($grp_hosts)
   $arr = array($text, $hostcount);
   return $arr;
} # End function group_hosts
