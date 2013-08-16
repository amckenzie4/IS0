<?php

$config = parse_ini_file("../../../config/config.ini",true);
$db_config = parse_ini_file("../../../config/config_db.ini",true);


include_once("../../../includes/common_logger.inc");
include_once("../../../includes/common_db.inc");
include_once("../../../includes/common_subnet.inc");

$q = $_GET["q"];

#$q = "128.119.39.0/24";

$subnet = new subnet($q);

$first = $subnet->first_long();
$last = $subnet->last_long();

$dbh = new db_iface('database');

$query = "SELECT reserve FROM subnet WHERE subnet=?";
$query_term[] = $q;

$result = $dbh->query($query, $query_term);
if(isset($result) && (isset($result[0]['reserve'])))
	{ $offset = $result[0]['reserve']; }
else
	{ $offset = '10'; }

unset($query);
unset($query_term);
unset($result);

$query = "SELECT ip FROM hosts where ip=?";

$ip = $first;

if(isset($offset)) 
	{ $ip = $ip + $offset; }

while($ip <= $last)
 {
	$query_term[] = long2ip($ip);
	
	$result = $dbh->query($query, $query_term);

	if ( $result )
	 {
		$ip++;
		unset($query_term);
	 }
	else
	 {
		$free = long2ip($ip);
		break;
	 }
 } # End while loop

if (isset($free))
 { # Begin success
	echo $free;
 } # End success
else
 {
	echo "No free IP in this subnet.";
 }


?>
