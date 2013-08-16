<?php

//common_login.php


#########################################################################
#									                                    #
#   This file should provide a (more or less) generic set of login 	    #
# functions.  It looks for three things as input:			            #
# 1) Username								                            #
# 2) Password -- NOTE: The password MUST already be hashed as it is     #
#    in the database!  This function does not handle encryption at all.	#
# 3) auth_type -- this can be either DB or LDAP.  Details for either    #
#    are drawn from config_db.inc.  The DB section assumes the          #
#    authentication table has columns named "username" and "password",  #
#    and the LDAP section assumes the relevant columns are "uid" and    #
#    "userPassword".  Eventually I'll arrange things so that can be     #
#    over-ridden.  The default is "db".					                #
#									                                    #
#   Once the login function has tested the user against whatever auth   #
# server is specified, it will return a keyed array.  The possible      #
# key/value pairs are:							                        #
# 1) success:  yes, no, or guest.				                        #
# 2) uid:  This is the username, drawn directly from the input (arg 1). #
#									                                    #
# NOTE:  "guest" is a special keyword.  If arg 1 is "guest", no pw      #
# check will be made.  The return array will be immediately populated   #
# with success="guest" and uid="guest", and the array will be returned. #
# This behaviour can be altered by modifying the first line after this  #
# text box.  If the line is uncommented, the guest keyword will be  	#
# removed, and the user "guest" will be checked as a normal user.       #
# Alternatively, a config variable can be created called 		        #
# $config["system"]["allow_guest"] and set to "no" anywhere else in the #
# program before login.inc is included.					                #
#									                                    #
#########################################################################

# $config["system"]["allow_guest"] = "no";

# Comment the following if you need to.

if(!isset($config["system"]["include_dir"])) 
   { 
      $config["system"]["include_dir"] = "includes"; 
   }

$cf = $config["system"]["include_dir"] . "/common_db.inc";
#echo "cf = " . $cf . "<br>";
include_once($cf);


#echo "<br>Now inside common_login.inc<br>";

function login($username, $password = "",  $auth_type = "db")
{
	# First, check whether guests are treated specially.  If so, and $username is
	# "guest", populate the return array, then return it.
	if (!$GLOBALS["config"]["system"]["allow_guest"] == "no")
	{ # Begin Guest login
		if ($username == "guest")
		{
			$return_array = array ("success" => "guest", "username" => "guest");
			return $return_array;
		}
	} # End Guest login

	#  # Assuming the user isn't a guest, the next step is to create the db
	#  # connection, build the query, and see what happens.
	#
	else
	{ # Begin login for non-guest users
		# Use the db_iface class to build a database handler, feeding it the
		# location of the relevant section of the database config
		$auth_table = $GLOBALS["db_config"]["auth_db"]["auth_table"];
		$auth_dbh = new db_iface('auth_db');
		$name_field = $GLOBALS["db_config"]["auth_db"]["auth_table_uid"];
		$pwd_field = $GLOBALS["db_config"]["auth_db"]["auth_table_pwd"];
		$hash = $GLOBALS["config"]["auth"]["auth_hash"];
        
		if($hash != NULL)
		{  #Begin dealing with hash
		   if ($hash = 'md5')
		   { # Begin md5 hash
		      logger('debug', 'Now inside md5 hash section');
		      $password = md5($password);
		   } # End md5 hash
		}  # End dealing with hash
		
		$query = "SELECT $name_field,$pwd_field FROM $auth_table WHERE $name_field=? AND $pwd_field=?";
		$search_terms = array($username, $password);

		$log_message = "Query is: " . $query;
		logger('debug', $log_message);
		$log_message = "search terms are: username=" . $search_terms[0] . ', password=' . $search_terms[1];
		logger('debug', $log_message);
		unset($log_message);
		
		$search_result = $auth_dbh->query($query, $search_terms);
		if ($search_result)
		{ # Begin successful login
			#$return_array = array("success" => "yes", "username" => $username);
			#return $return_array;
			return "true";
		} # End successful login
		else
		{ # Begin failed login
			#$return_array = array("success" => "no", "username" => NULL);
			#return $return_array;
			return "false";
		} # End failed login
	} # End login for non-guest users

} # End function login


#########################################################################
#									                                    #
#   The validate_ssha() function is designed to deal with the problem   #
# of confirming passwords that were encrypted for OpenLDAP.  OpenLDAP,  #
# unfortunately (in terms of ease of use, anyway) requires passwords in #
# salted sha1 format.  See the password_hash and password_hash_mhash    #
# libraries for details on the creation.  Because the hashes are salted #
# it's not possible to just create a new hash and compare:  the salts   #
# wouldn't be the same.  This function resolves that by taking the old  #
# hash, breaking it down to remove the salt, and then hashing the new   #
# password to see if it matches.  If it matches, the function returns   #
# true.  If not, it returns false.  The inputs are the new password and #
# the one we're checking against.					                    #
#									                                    #
#########################################################################

function validate_ssha($password, $hash)
{
	# First decode the hash, removing the {SSHA} from it.
	$hash = base64_decode(substr($hash, 6));
	# I don't actually need the original hash, but it's here anyway.
	$original_hash = substr($hash, 0, 20);
	# This finds the last 4 bytes, which is the hash.
	$salt = substr($hash, 20);
	# Now, use the old salt to generate a hash from the new pass
	$new_hash = mhash(MHASH_SHA1, $password . $salt);
	# Check whether the original hash and the new are the same.
	if (strcmp($original_hash, $new_hash) == 0)
	{ return true; }
	else
	{ return false; }
}

?>
