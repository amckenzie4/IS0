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

  echo '<table>';
  echo '<tr>';
  echo '<td colspan="3"><b>' .$row['zone'] . '</b>';
	if($row['active'] == 'no')
	 { echo ' (<i>Inactive</i>)'; }
	echo '</td><td>(MySQL ID: ' . $row['id'] . ')</td>';
  echo '</tr><tr>';
  echo '<td width="15%"></td>';  # Indent for lower rows
  echo '<td>TTL:  ' . $row['ttl'] . '</td><td width="15%"></td><td>Serial:  ' . $row['serial'] . '</td>';
  echo '</tr><tr>';
  echo '<td width="15%"></td>';  # Indent for lower rows
  echo '<td>Refresh: ' . $row['refresh'] . '</td><td width="15%"></td><td>Retry: ' . $row['retry'] . '</td>';
  echo '</tr><tr>';
  echo '<td width="15%"></td>';  # Indent for lower rows
  echo '<td>Expiration: ' . $row['expiration'] . '</td><td width="15%"></td><td>Minimum: ' . $row['minimum'] . '</td>';
  echo '</tr><tr>';
  echo '<td width="15%"></td>';  # Indent for lower rows
  echo '<td>NS1: ' . $row['ns1'] . '</td><td width="15%"></td><td>NS2 ' . $row['ns2'] . '</td>';
  echo '</tr><tr>';
  echo '<td width="15%"></td>';  # Indent for lower rows
  echo '<td colspan="3">Admin Email: ' . $row['admin_email'] . '</td>';
  echo '</tr><tr>';
  echo '<td width="15%"></td>';  # Indent for lower rows
  echo '<td colspan="3">Comments: ' . $row['comments'] . '</td>';
  echo '</tr>';

  echo '</table>'; 

  echo '</td>';
?>