<?php

# This page simply assumes that $search_result[] is populated, and draws
# a table of the contents.


echo '<center>';
if (isset($num_results)) { echo $num_results . " records found.<br>"; }

# If there are results, list them.  Otherwise, say so and do nothing more.
if(isset($search_result))
{ # Begin listing
   echo '<table width="50%" border="1">';
   echo '<tr><th align="center" colspan="4">' . ucfirst($record_name) . 's</th></tr>';


   foreach ($search_result as $row)
    {
     echo '<tr>';
   ### START building the listing
     echo '<td>';
     echo '<b>Magazine Title:  ' . $row['title'] . '</b><br>';
     echo 'First Issue Owned:  ' . $row['first_issue'] . '<br>';
     echo 'Comments:  ' . $row['comments'] . '<br>';
     echo '</td>';
   ### STOP building the listing
     # Now create a column for the delete and modify links.
     $delstring = "core.php?core_func=mod&module=" . $_SESSION["module"] . "&mod_func=delete_form&record=" . $row["id"];
     $modstring = "core.php?core_func=mod&module=" . $_SESSION["module"] . "&mod_func=add_mod_form&record=" . $row["id"];
     echo '<td>';
     echo '<a href="' . $delstring . '">Delete record</a><br>';
     echo '<a href="' . $modstring . '">Modify record</a>';
     echo '</td>';
   
     echo '</tr>';
    } 
} # End listing
echo '</table>';
echo '</center>';
?>
