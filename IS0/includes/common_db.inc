<?php

//common_db.inc

logger('debug','Now in common_db.inc');

#########################################################################
#                                                                       #
#  This file provides a common interface for a DB.  When creating an    #
# instance, the only argument required is the name of the config file   #
# section where information can be found.  The config file should, at a #
# minimum, include the following:										#
# 1) db_type (Current allowed types are pgsql and mysql)				#
# 2) db_host -- where to find the DB									#
# 3) db_name -- the name of the DB.										#
# 4) db_username -- the name of a user with the necessary permissions   #
#    on the db															#
# 5) db_pass -- the user's password.								    #
#																		#
#  Everything else will be explained in-line.							#
#																		#
#########################################################################

# First, parse the config file.  I assume it's called config_db.ini,
# and is in the $config directory, as defined by whatever is calling
# this include file.  The "true" after the config file says to make an
# n-dimensional array keyed to the section, then to the key/value pair.

#$db_config = parse_ini_file("$config_dir/config_db.ini",true);


#echo "inside common_db.inc db_config = ";
#print_r($db_config);
#echo "<br>";


class db_iface
 {
  # These are the private variables we'll need.
  private $db_type;
  private $db_host;
  private $db_port;
  private $db_name;
  private $db_user;
  private $db_pass;

  # The constructor populates the private variables and creates the DB handler.
  public function __construct($config_section)
   {  

    $db_config = $GLOBALS["db_config"];
    
    $log_message = "db_config is $db_config";
    logger('debug',$log_message);
    unset($log_message);
    
    #$this->db_type = $db_config[$config_section]["db_type"];
    $this->db_type = $GLOBALS["db_config"][$config_section]["db_type"];
    $this->db_host = $GLOBALS["db_config"][$config_section]["db_host"];
    $this->db_port = $GLOBALS["db_config"][$config_section]["db_port"];
    $this->db_name = $GLOBALS["db_config"][$config_section]["db_name"];
    $this->db_user = $GLOBALS["db_config"][$config_section]["db_user"];
    $this->db_pass = $GLOBALS["db_config"][$config_section]["db_pass"];



    if ($this->db_type == "mysql")
     {
      $this->dbh = new PDO("$this->db_type:host=$this->db_host;port=$this->db_port;dbname=$this->db_name", $this->db_user, $this->db_pass) or die ("Database Connection Error!");
      return 1; # End successfully
     } # End mysql dbh construction
    elseif ($db_type == "pgsql")
     {
      $this->dbh = new PDO("$this->db_type:host=$this->db_host;port=$this->db_port;dbname=$this->db_name;user=$this->db_user;password=$this->db_pass") or die ("Database Connection Error!");
      return 1; # End successfully
     } # End pgsql dbh construction
    else
     {
      die ("Database type $this->db_type is not supported!\n");
     }
   } # End __construct method

  # Next is the query function:  It takes two arguments, a query string with
  # the search terms in question-mark format, and an array of search terms.
  # This is, of course, slightly more complicated than just using a query, 
  # but it provides some protection from SQL injection attacks.  
  # The return value is either an array of all the search results or a NULL.
  #
  # In an ideal world, there would be four query functions (query, insert, 
  # modify, and delete), so  you could just feed them the columns and rows.
  # Sadly, this isn't an ideal world...  Hopefully I'll add that later, but
  # for now this is what you get.

  public function query($query_string, $search_terms)
   {
    if(!isset($search_terms))
     {
      die("Search terms not defined!");
     }
     $sth = $this->dbh->prepare($query_string);

     $count = $sth->execute($search_terms);

     $pos = stripos($query_string, "select");
     if ($pos === 0) 
       {
        $result = $sth->fetchAll(PDO::FETCH_ASSOC);

        if($result) { return $result; }
        else { return NULL; }
       }
     else { return $count; }
   }


 } # End class db_iface





