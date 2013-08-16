<?php

# This page simply assumes that $search_result[] is populated, and draws
# a table of the contents.

# This is a standard script to hide or show a div.
?>
<script type="text/javascript">
<!--
    function toggle_visibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
//-->
</script>

<?php

$spaces = '&nbsp;&nbsp;&nbsp;';
echo '<center>';
if (isset($num_results)) { echo $num_results . " records found.<br>"; }
echo '<table width="50%" border="1">';
echo '<tr><th align="center" colspan="4">Departments</th></tr>';

foreach ($search_result as $row)
 {
   echo '<tr>';
   echo '<td>';
   
   # Include the user generated list setup.
   $list_file = $mod_to_include . '/user_list.php';
   include($list_file);

	echo '</td>' . "\n";
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
