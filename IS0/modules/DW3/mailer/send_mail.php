<?php

#echo "testing 1 2 3<br>";
#print_r($_POST);
#echo '<br>';

# This file takes input from the main script (module.php), parses it,
# and sends out the necessary information to the group contact.

# The From: line should always be the same.
$headers = 'From: net@chem.umass.edu';

# Before I do anything else, I need to build some javascript.  This will
# be used in the advanced and send-to-all type mailings.

?>

<script type="text/javascript">

function processing(div)
 {
	newMessage = '<span style="color:yellow;">Processing...</span>';
	document.getElementById(div).innerHTML = newMessage;
 }

function success(div)
 {
	newMessage = '<span style="color:green;">Message Sent!</span>';
	document.getElementById(div).innerHTML = newMessage;
 }

function failure(div)
 {
	newMessage = '<span style="color:red;">Sending failed!</span>';
	document.getElementById(div).innerHTML = newMessage;
 }

</script>

<?php

if($_POST['mail_type'] == 'single')
 { # Start sending a single message
	# First, pull the group contact address, and set it as the
	# recipient address.  We'll also need the name of the group.

	$dbh = new db_iface('database');
	$query = 'SELECT * from ' . $_SESSION['mod_table'] . ' WHERE grp=?';
	unset($query_terms);
	$query_terms[] = $_POST['group'];


	$result = $dbh->query($query, $query_terms);

	$group = $result[0]['grp'];
	if($_POST['override'] != NULL)
	 { 
		$recipient = $_POST['override'];
	 }
	else
	 {
		//$recipient = $result[0]['contact_email'];
####
$recipient = 'alex@chem.umass.edu';
	 }

	# This is also a good place to set the CC
	if(isset($_POST['cc']))
	 {
		$headers .= "\r\n" . "CC: " . $_POST['cc'];
	 }
	# Second, set the subject line
	$subject = 'Computer inventory information for group ' . $group;

	# Third, generate the body of the message.

	unset($query);
	$query = 'SELECT * FROM hosts WHERE grp=?';
	unset($query_terms);
	$query_terms[] = $group;
	unset($result);
	$result = $dbh->query($query, $query_terms);

	$message = "Please contact the IT Team if any of the following information is incorrect.\n\n";

	foreach($result as $row)
	 { # Begin generating message
		$message .= <<<EOE
$row[name]			IP: $row[ip]	    
	MAC: $row[mac]
	Make/Model: $row[model]
	OS: $row[os]
	Location: $row[location]
	Owner: $row[owner]
	Group: $row[grp]
	Department: $row[department]


EOE;

	} # End generating message


	# Finally, send the message and update the screen and log.
	if(mail($recipient, $subject, $message, $headers))
	 { # Update the screen
		$output = "Inventory sent to $recipient for group $group";
   	echo '<p>' . $output . '</p>';
   	logger(info, $output);
	 } # End updating the screen
	else 
	 { # Start error output
		$output = "Unable to send inventory to $recipient for group $group!";
		echo '<p>' . $output . '</p>';
   	logger(error, $output);
	 } # End error output 
 } # End sending a single message

elseif($_POST['mail_type'] == 'default_all')
 { # Begin generating mail to all

	# First, create the list of everyone, with a div containing the word 
	# "pending...", and a column with the email address the message will
	# be sent to.

	$dbh = new db_iface('database');

	unset($query);
	unset($query_terms);
	$query = 'SELECT * FROM ' . $_SESSION['mod_table'] . ' ORDER BY grp';
	$query_terms[] = "";
	$group_list = $dbh->query($query, $query_terms);
 
	echo '<center>';
	echo '<table width="50%" border="1">';
	echo <<<EOT
<tr>
<th>Group Name</th>
<th>Contact EMail</th>
<th>Action</th>
</tr>
EOT;
	foreach($group_list as $glr)
	 { # Begin generating table
		echo <<<EOT
<tr>
<td>$glr[grp]</td>
<td>$glr[contact_email]</td>
<td><div id="$glr[grp]_div"><span style="color:red;">Pending...</span></div></td>
</tr>
EOT;

	 } # End generating table
	
	echo '</table>';
	echo '</center>';

	# Next, step through the groups again.  At each one, do the following:
	# 1) Change "Pending..." to "Processing..."(Yellow)
	# 2) Build the message and attempt to send it.
	# 3a) If the message failed, change "Processing..." to "Failed!"(Red)
	# 3b) If the message succeeded, change "Processing..." to "Finished!"(Green)
	
	foreach($group_list as $glr)
	 { # Begin processing messages
		echo "<script language='javascript'>processing('testdiv');</script>";
		//$recipient = $glr['contact_email'];
		$recipient = 'alex@chem.umass.edu';
		$subject = 'Computer inventory information for group ' . $glr['grp'];
		$message = "Please contact the IT Team if any of the following information is incorrect.\n\n";
		
		# Find the full list of hosts in the group
		unset($query);
		unset($query_terms);
		$query = "SELECT * FROM hosts WHERE grp=?";
		$query_terms[] = $glr['grp'];

		$comps = $dbh->query($query, $query_terms);

	   foreach($comps as $row)
	    { # Begin generating message
   	   $message .= <<<EOE
$row[name]        IP: $row[ip]
   MAC: $row[mac]
   Make/Model: $row[model]
   OS: $row[os]
   Location: $row[location]
   Owner: $row[owner]
   Group: $row[grp]
   Department: $row[department]


EOE;
   	 } # End generating message
	
		# Finally, we can send the message and mark it a success or failure
		if(mail($recipient, $subject, $message, $headers))
		 { # Deal with a successfully sent message
			echo '<script language="javascript">success("' . $glr['grp'] . '_div");</script>';
			$output = "Inventory sent to $recipient for group " . $glr['grp'];
	      logger(info, $output);
    } # End updating the screen
   else
    { # Start error output
			echo '<script language="javascript">failure("' . $glr['grp'] . '_div");</script>';
      $output = "Unable to send inventory to $recipient for group " . $glr['grp'] . "!";
      logger(error, $output);
    } # End error output 

	 } # End processing messages		
 } # End generating mail to all

?>
