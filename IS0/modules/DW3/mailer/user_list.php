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

echo '<b>' . $row[file] . '</b>' . $spaces . $spaces .'ID: ' . $row[id] . '<br>' . "\n";
$div_id = $row['file'];
echo 'Headers: (<a href="#" onclick="toggle_visibility(\'' . $div_id . '\');">show/hide</a>)<br>';
echo '<div id="' . $div_id . '" style=\'display:none\'>';
echo nl2br($row[header_text]) . '</br>' . "\n";
echo '</div>';
echo '<b>Comments: </b>' . $row[comments] . '<br>' . "\n";

?>