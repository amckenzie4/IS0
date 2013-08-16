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
   # Start the inner table
   echo '<table>';
   echo '<tr>';
	echo '<td colspan="3"><b>' . $row['name'] . '</b></td><td>(MySQL ID: ' . $row['id'] . ')</td>';
	echo '</tr><tr>';
	echo '<td width="10%"></td>';  # Indent for lower rows
   echo '<td>IP Address: ' . $row['ip'] . '</td><td width="10%"></td><td>MAC: ' . $row['mac'] . '<td>';
	echo '</tr><tr>';
	echo '<td width="10%"></td>';  # Indent for lower rows
   echo '<td>OS: ' . $row['os'] . '</td><td width="10%"></td><td>Serial Number: ' . $row['ser_num'] . '<td>';
	echo '</tr><tr>';
	echo '<td width="10%"></td>';  # Indent for lower rows
   echo '<td>Location: ' . $row['location'] . '</td><td width="10%"></td><td>Asset Number: ' . $row['asset_num'] . '<td>';
	echo '</tr><tr>';
	echo '<td width="10%"></td>';  # Indent for lower rows
   echo '<td>Group: ' . $row['grp'] . '</td><td width="10%"></td><td>Department: ' . $row['department'] . '<td>';
	echo '</tr><tr>';
	echo '<td width="10%"></td>';  # Indent for lower rows
   echo '<td>Owner: ' . $row['owner'] . '</td><td width="10%"></td><td>Primary User: ' . $row['prime_user'] . '<td>';
	echo '</tr><tr>';
	echo '<td width="10%"></td>';  # Indent for lower rows
   echo '<td>Status: ' . $row['status'] . '</td><td width="10%"></td><td>Date Added: ' . $row['date'] . '<td>';
	echo '</tr><tr>';
	echo '<td width="10%"></td>';  # Indent for lower rows
   echo '<td colspan="5">Comments: ' . $row['comments'] . '</td>';
   echo '</tr>';
   echo '</table>';
   echo '</td>';


?>