<?php

# This takes the data from delete_form.php and actually deletes it.
# First, though, make sure that nothing fishy is going on.  The 
# SESSION[delete_form] variable is set in the delete form, and unset
# here, so if it's not set, someone is trying to break things.

if((!isset($_SESSION['delete_form'])) || ($_SESSION['delete_form'] != 'yes'))
 { # Begin error routine
  $_SESSION['temp_message'] = "You have attempted to delete a record without using the delete form.  This attempt has been logged, and you have been redirected to the full department list.";
  $log_msg = "User " . $_SESSION['uid'] . " attempted to delete record " . $_GET['record'] . " from table " . $_SESSION['mod_table'] . " without using the delete form.";
  logger(error, $log_msg);
 } # End error routine
# If the request came from the delete form, wipe the session variable and keep
# going.
else
 { # Begin delete routine
  unset($_SESSION['delete_form']);

  $query = "DELETE from " . $_SESSION['mod_table'] . " WHERE id=?";
  $search_terms[] = $_GET['record'];
  
  $dbo = new db_iface('database');
  
  $result = $dbo->query($query, $search_terms);
  
  if($result)
   {
    $_SESSION['temp_message'] =  "Record " . $_GET['record'] . " deleted by " . $_SESSION['uid'];
    $_SESSION['temp_message_level'] = 'info';
   }
  else
   {
    $_SESSION['temp_message'] =  "Unable to delete record " . $_GET['record'] . "!";
    $_SESSION['temp_message_level'] = 'dnl';
   }

 } # End delete routine
# No matter what happened, go back to the quicklist.
header("Location: core.php?core_func=mod&module=departments&mod_func=qlist");
?>

