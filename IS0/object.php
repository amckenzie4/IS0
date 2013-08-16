#!/usr/bin/php -q

<?php

class testClass
{
private $db_type = "mysql";
private $db_user = "dhcpdns";
#$pass = "dhcpdns";  # There isn't currently a password set
private $db_host = "ziggy.chem.umass.edu";
private $db_port = "3306";
private $db_name = "dhcpdns";
private $db_pass = NULL;
 
public function __construct()
{
 $this->dbhandle = new PDO("$this->db_type:host=$this->db_host;port=$this->db_port;dbname=$this->db_name", $this->db_user, $this->db_pass) or die ("Database Connection Error!");
 
}

####public function query($query_string)
public function query($query_string, $search_terms)
{
# $this->result = $this->dbhandle->query($query_string);
# $this->temp = $this->result->fetchAll(PDO::FETCH_ASSOC);
# if (count($this->temp) == 1)
# { print_r($this->temp); }
# if (count($this->temp) < 1)
# { echo "no results\n"; }
# if (count($this->temp) > 1)
# { echo "More than one result\n"; }

#$sth = $this->dbhandle->prepare($query_string);
#$sth->execute();
#if ($sth->fetch())
# { echo "Result found\n"; }
#else { echo "No results\n"; }

$sth = $this->dbhandle->prepare($query_string);
$sth->execute($search_terms);

$result = $sth->fetchAll(PDO::FETCH_ASSOC);

return $result;

##$result = $this->dbhandle->query($query_string);
#if (!$result->fetch()) { echo "no results\n"; }
#else { 
#print_r($bob = $result->fetch(PDO::FETCH_ASSOC));
#echo "name is " . $bob["name"] . "\n"; }

##if ($bob = $result->fetch(PDO::FETCH_ASSOC))
##{
## echo "name is " . $bob["name"] . "\n";
##}
##else { echo "problem\n"; }

}

public function __destruct()
{
 $this->dhbandle = NULL;
}
}


## this is the test of the class
$obj = new testClass();
$query = "select name,ip,mac,location from hosts where owner = ?";
$search_terms[] = "Alex McKenzie";
$search_result = $obj->query($query, $search_terms);

print_r($search_result);
?>
