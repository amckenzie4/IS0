<?php

# This is just a confirmation form, to make sure someone is deleting the record
# they meant to delete.


$query = "SELECT * from " . $_SESSION['mod_table'] . " WHERE id=?";
$search_terms[] = $_GET['record'];


$dbo = new db_iface('database');

$result = $dbo->query($query, $search_terms);


if($result)
 {

  $_SESSION['delete_form'] = "yes";

  echo "<p><b>Please confirm you wish to delete this record:  this cannot be undone!</b></p>";

  echo "Department Name: " . $result[0]['department'] . "<br>";
  echo "LDAP Name: " . $result[0]['ldap_departmentNumber'] . "<br>";
  echo "Comment: " . $result[0]['comment'] . "<br>";

  echo '<p>';
  echo '<a href="core.php?core_func=mod&module=departments&mod_func=delete_submit&record=' . $_GET['record'] . '">Delete it!</a></p>';
 }
else 
 { 
  $_SESSION['temp_message'] = "Something has gone wrong.  You have been returned to the full list.";
  logger(warning, "Attempt to delete nonexistent record in department table!");
  header("Location: core.php?core_func=mod&module=departments&mod_func=qlist");
 }

?> 
