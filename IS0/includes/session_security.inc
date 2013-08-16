<?php

function session_security()
 { # Begin function session_security

  # First, deal with session timeouts.  Yes, there's similar
  # functionality built in, but it's a pain to use.  The timeout
  # may be specified in a config file:  if so, make use of it.
  if(isset($GLOBALS['config']['session']['session_timeout']))
   {
    $timeout = $GLOBALS['config']['session']['session_timeout'];
   }
  else { $timeout = 60*60; }// If it's not in the config, set timeout to 1 hour 

  # Now check if the session timeout length is set.  If so, find out if the
  # session has timed out, and act accordingly.
  if(isset($_SESSION['timeout']) ) 
   {
    $session_life = time() - $_SESSION['timeout'];
    if($session_life > $_SESSION['timeout'])
     { 
      echo "session_life > _SESSION['timeout']<br>";
      session_destroy(); 
      $_SESSION = array();
      $_SESSION["core_func"] = 'logout';  
     }
   }
  
  # Regardless of what else has happened, set _SESSION[timeout] to now.
  $_SESSION['timeout'] = time();

 } # End function session-security
?>
