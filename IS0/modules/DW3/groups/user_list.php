<?php 
# To include a show/hide button for some elements, follow this example:
# $div_id = $row['file'];
#echo '<a href="#" onclick="toggle_visibility(\'' . $div_id . '\');">Show headers<br></a>';
#echo '<div id="' . $div_id . '" style=\'display:none\'>';
#   echo '<b>Header Text:  </b><br/>' . nl2br($row[header_text]) . '<br>' . "\n";
#echo '</div>';
#
# Note that the above only allows one hidden div per entry;  you can either choose
# to hide all the necessary data in one div, or modify the code to deal with 
# smaller subsets of the data.

  echo '<td>';
  echo '<b>Group Name:  ' . $row['grp'] . '</b><br>';
  echo $spaces . 'Group Head:  ' . $row['head'] . '<br>';
  echo $spaces . 'Contact Name:  ' . $row['contact_name'] . '<br>';
  echo $spaces . 'Contact E-Mail:  ' . $row['contact_email'] . '<br>';
  echo '</td>';
?>