<?php

#########################################################################
#                                                                       #
#  This is somewhat more complex than most of the scripts in the module.#
# It handles creating the form for both adding and modifying an entry,  #
# and therefore actually has to contact the db if it's going to edit.   #
# That said, it still basically just queries the DB if there is a       #
# record number available, then builds the form.  The form submits to   #
# add_mod_submit.php, which does the hard part.				#
#									#
#########################################################################

# First, figure out whether we're supposed to be adding or modifying.
# If a record number is specified, we can assume we're supposed to be modifying
# a record.  Otherwise, we're almost certainly supposed to be adding.

$search_result[]="";


if(isset($_GET['record']))
 {
  
  $dbo = new db_iface('database');

  $query = "SELECT * from " . $_SESSION['mod_table'] . " WHERE id=?";
  $search_terms[] = $_GET['record'];
  $GLOBALS['search_result'] = $dbo->query($query, $search_terms);
 }

# At this point, we can build the form.  The key right now is to remember to 
# offer a default of the current value, if there is one

echo '<form method="post" action="core.php?core_func=mod&module=departments&mod_func=add_mod_submit">';

echo '<center><table width="45%">';

echo '<tr><th align="center" colspan="2">';
if (isset($_GET['record'])) { echo 'Editing record ' . $search_result[0]['department']; }
else { echo "Adding new department"; }
echo '</th></tr>';

echo '<tr>';
echo '<td align="right">Department name: </td> ';
echo '<td align="left"><input name="dept_name" type="text" value="';
     if(isset($search_result[0]['department'])) { echo $search_result[0]['department']; }
echo '"></td>';
echo '</tr>';

echo '<tr>';
echo '<td align="right">LDAP Department Number: </td> ';
echo '<td align="left"><input name="dept_number" type="text" value="';
     if(isset($search_result[0]['ldap_departmentNumber'])) { echo $search_result[0]['ldap_departmentNumber']; }
echo '"></td>';
echo '</tr>';

echo '<tr>';
echo '<td align="right">Comments: </td> ';
echo '<td align="left"><input name="comment" type="text" size="40%" value="';
     if(isset($search_result[0]['comment'])) { echo $search_result[0]['comment']; }
echo '"></td>';
echo '</tr>';

if(!isset($_GET['record']))
 {
  echo '<tr>';
  echo '<td align="right">Add another?  </td>';
  echo '<td align="left"><input name="add_more" type="checkbox" value="Y">';
  echo '</td>';
  echo '</tr>';
  echo '<input name="add" type="hidden" value="add">';
 }
else
 {
  echo '<input name="record" type="hidden" value="' . $_GET['record'] . '">';
 }

echo '<tr>';
echo '<td align="center" colspan="2">';
echo '<input type="submit">';
echo '</form>';
echo '</td></tr>';
echo '</table>';
echo '</center>';

?>
