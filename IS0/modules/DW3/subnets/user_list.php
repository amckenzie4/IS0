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

	echo '<b>Subnet: ' . $row['subnet'] . '</b> ' . $spaces . $spaces . $spaces . '(mySQL ID #:  ' . $row['id'] . ')<br>' . "\n";
	echo $spaces . 'Shared network: ' . $row['shared_network'] . '<br>' . "\n";
	echo $spaces . 'WINServer: ' . $row['wins'] . '<br>' . "\n";
  	echo $spaces . 'DNS1: ' . $row['dns1'] . '<br>' . "\n";
  	echo $spaces . 'DNS2: ' . $row['dns2'] . '<br>' . "\n";
  	echo $spaces . 'Gateway: ' . $row['gateway'] . '<br>' . "\n";
  	echo $spaces . 'Netmask: ' . $row['mask'] . '<br>' . "\n";
  	echo $spaces . 'Zone: ' . $row['zone'] . '<br>' . "\n";
	echo $spaces . 'Department: ' . $row['department'] . '<br>' . "\n";
	if(isset($row['directives']))
	 { # Begin directives display
		echo $spaces . 'Directives: <br>';
		echo $spaces. $spaces . preg_replace('/\r\n/', "<br>\n$spaces$spaces", $row['directives']);
		echo "<br>\n";
	 } # End directives display
	if($row['reserve'])
	 {
		echo $spaces . 'Reserved IPs: ' . $row['reserve'] . '<br>' . "\n";
	 }
	if($row['pool1'])
	 {
		echo $spaces . 'Pool 1 Directives: <br>';
		echo $spaces . $spaces . preg_replace('/\r\n/', "<br>\n$spaces$spaces", $row['pool1_dirs']) . '<br>' . "\n";
		echo $spaces . 'Pool 1: ' . $row['pool1'] . '<br>' . "\n";
	 }
	if($row['pool2'])
	 {
		echo $spaces . 'Pool 2 Directives: <br>';
		echo $spaces . $spaces . preg_replace('/\r\n/', "<br>\n$spaces$spaces", $row['pool2_dirs']) . '<br>' . "\n";
		echo $spaces . 'Pool 2: ' . $row['pool2'] . '<br>' . "\n";
	 }
	if(isset($row['dhcpd_active']))
	 {
		echo $spaces . 'NOT in dhcpd.conf<br>' . "\n";
	 }
	if(isset($row['bind_active']))
	 {
		echo $spaces . 'NOT in DNS<br>' . "\n";
	 }
?>