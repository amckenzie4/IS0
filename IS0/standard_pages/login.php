<?php

$common_login = $config["system"]["include_dir"] . "/common_login.inc";
$common_db = $config["system"]["include_dir"] . "/common_db.inc";
$common_logger = $config["system"]["include_dir"] . "/common_logger.inc";
include_once($common_login);
include_once($common_db);
include_once($common_logger);
include_once('includes/common_mini.inc');
$auth_type = $config['auth']['auth_type'];

# Define the authentication source
$auth_db = new db_iface('auth_db');

logger('debug', 'Now inside login.php');

# SQL login
# If you're using a simple database backend, uncomment this and comment
# out the section for LDAP
#@todo build sql format login
if($auth_type == 'sql')
{ # Begin SQL authentication
   $login_success = login($_POST["username"], $_POST["user_pass"]);
} # End SQL authentication

if($auth_type == 'sql-ldap')
{ # Begin SQL-LDAP authentication
   # MySQL-LDAP login
   # This is built for use with a mysql backend for LDAP.  LDAP requires oddly hashed
   # passwords, which we can't easily compare against.  Therefore, we need to go 
   # through a more drawn out procedure to make them work.
   
   logger(debug, "Entering login section 'sql-ldap'");

   $query = "select uid,userPassword,departmentNumber,description from users where uid = ?";
   
   $search_terms = array($_POST["username"]);
   
   $search_result = $auth_db->query($query, $search_terms);
   
   
   # For the time being, we only want admins to be able to log in.
   # So, we check, and only actually test the password if description = admin.
   # If not, we automatically set login_success to false, and move on.
   if($search_result[0]["description"] == "admin")
    { 
     logger(debug, "Entering admin check in login.php");
     #$log_msg = "Userpass = " . $_POST["user_pass"] . ", search result = " . $search_result[0]["userPassword"];
     #logger(debug, $log_msg);
     
     $valid_pass = validate_ssha($_POST["user_pass"], $search_result[0]["userPassword"]);
     
     $log_msg = "valid_pass = $valid_pass";
     logger(debug, $log_msg);
     unset($log_msg);
     
     if($valid_pass == '1')
     {
        $login_success = 'true';
     }
     else
     {
        $login_success = 'false';
     }
    }
   else { $login_success = 'false'; }
   
   if ($login_success == true)
    { # Begin succesful login
     #$_POST["core_func"] = NULL;
     #$_GET["core_func"] = NULL;
     #$_SESSION["core_func"] = "main";
     #$log_msg = "Successful login for " . $_POST['username'];
     #logger('info', $log_msg);
     $_SESSION["uid"] = $search_result[0]["uid"];
     $_SESSION["department"] = $search_result[0]["departmentNumber"];
     $_SESSION["description"] = $search_result[0]["description"];
   
     
    } # End succesful login
    
} # End SQL-LDAP login

if($login_success == 'true')
{ # Begin Succesfull login (universal section)
   $_POST["core_func"] = NULL;
   $_GET["core_func"] = NULL;
   $_SESSION["core_func"] = "main";
   $log_msg = "Successful login for " . $_POST['username'];
   logger('info', $log_msg);
   $_SESSION["uid"] = $_POST["username"];
   
   # We also want to make sure that there is an entry for the user in the
   # user prefs table.
   $db = new db_iface('database');
   $query = "select * from user_prefs where uid=?";
   unset($search_terms);
   $search_terms[] = $_SESSION["uid"];
   $search_result = $db->query($query, $search_terms);
   if(!$search_result) 
   { # Begin user_prefs
      $log_msg = "Creating entry for " . $_SESSION["uid"] . " in user preferences table<br>";
      logger('warning', $log_msg);
      $query = "insert into user_prefs (uid) values (?)";
      $search_terms = array($_SESSION["uid"]);
      $search_result = $db->query($query, $search_terms);
     
      if($search_result)
      {
         logger('warning', 'Insert of user into user_prefs failed!');
      }
   } # end user_prefs
   include("./core.php");
   
} # End Successfull login (universal section)


 
 
# Failed logins are universal:  eithere they work, or they don't. 
else
 { # Begin failed login
  echo "<br><b>Login Failed!</b><br>";
  $_POST["core_func"] = NULL;
  $_SESSION["core_func"] = "frontpage";
  include("./core.php");
 } # Begin failed login

?>
