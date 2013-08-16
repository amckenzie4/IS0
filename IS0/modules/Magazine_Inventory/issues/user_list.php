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
echo '<b>' . $row['title'] . '</b>; ' . $row['issue_no'] . '<br>';
$div_id = $row['title'] . $row['issue_no'];
echo 'Headers: (<a href="#" onclick="toggle_visibility(\'' . $div_id . '\');">show/hide</a>)<br>';
echo '<div id="' . $div_id . '" style=\'display:none\'>';
echo 'Contents:  <br>' . nl2br($row['contents']) . '<br>';
echo '</div>';
echo 'Comments:  ' . $row['comments'] . '<br>';
echo '</td>';

?>