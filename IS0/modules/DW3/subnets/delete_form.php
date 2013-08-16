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

  echo '<center>';
  echo "<p><b>Please confirm you wish to delete this record:  this cannot be undone!</b></p>";

echo '<table>';

# $col_array is stored either in a config file (module_variables.inc) or 
# in header.inc
foreach ($col_array as $col=>$val)
 { # Begin generating table rows
   echo '<tr>';
   echo '<td align="right"><b>' . $val['hr'] . ':</b></td>';
   echo '<td align="left">' . $result[0][$col] . '</td>';
   echo '</tr>';
 } # End generating table rows

echo '</table>';

  echo '<p>';
  echo '<a href="' . $mod_top . '&mod_func=delete_submit&record=' . $_GET['record'] . '">Delete it!</a></p>';

  echo '</center>';
 }
else 
 { 
  $_SESSION['temp_message'] = "Something has gone wrong.  You have been returned to the full list.";
  logger(warning, "Attempt to delete nonexistent record in department table!");
  $location_string = "Location: " . $mod_top . '&mod_func=qlist';
  header($location_string);
 }

?> 
