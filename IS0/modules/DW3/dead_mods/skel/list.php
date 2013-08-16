<?php

# This page simply assumes that $search_result[] is populated, and draws
# a table of the contents.

echo '<center>';
if (isset($num_results)) { echo $num_results . " records found.<br>"; }
echo '<table width="50%" border="1">';
echo '<tr><th align="center" colspan="4">Departments</th></tr>';

foreach ($search_result as $row)
 {
  echo '<tr>';
  echo '<td><b>' . $row["department"] . '</b>(' . $row['ldap_departmentNumber'] . ')';
  if (isset($row["comment"]))
   {
    echo '<br>Comments:  ' . $row["comment"];
   }
  echo '</td>';

  # Now create a column for the delete and modify links.
  $delstring = "core.php?core_func=mod&module=" . $_SESSION["module"] . "&mod_func=delete_form&record=" . $row["id"];
  $modstring = "core.php?core_func=mod&module=" . $_SESSION["module"] . "&mod_func=add_mod_form&record=" . $row["id"];
  echo '<td>';
  echo '<a href="' . $delstring . '">Delete record</a><br>';
  echo '<a href="' . $modstring . '">Modify record</a>';
  echo '</td>';

  echo '</tr>';
 } 
echo '</table>';
echo '</center>';
?>
