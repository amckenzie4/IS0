<?php

$config = parse_ini_file("../../../config/config.ini",true);
$db_config = parse_ini_file("../../../config/config_db.ini",true);


include_once("../../../includes/common_logger.inc");
include_once("../../../includes/common_db.inc");
include_once("../../../includes/common_subnet.inc");


#$q = "128.119.39.0/24";
$q = $_GET["q"];

/*
# This outputs to a local temp.txt file with the name and input entered.
$msg = "subnet is $q";
$cmd = "echo $msg >> ./temp.txt";
exec($cmd);
*/


$subnet = new subnet($q);

$mask = $subnet->generate_mask();

echo $mask;


?>
