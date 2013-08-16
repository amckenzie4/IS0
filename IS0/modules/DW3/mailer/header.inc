<?php
# Any variables you might want to change are here.  This should form a
# pretty generic top-bar with add, show all, quick-search, advanced search,
# and module-top links.
include_once('module_variables.inc');

echo "<center>";
echo '<table width="75%" border="1">' . "\n";
echo '<tr><th align="center" colspan="5">'. $module_header . '</th></tr>' . "\n";
echo '<tr>' . "\n";
echo '<td width="25%">' . "\n";
# This is just a single button that will send out every inventory report
# at once.  It should probably be the most common way to send out reports.
echo '<center><form id="send_all" method="post" action="' . $mod_top . '&mod_func=send_mail">' . "\n";
echo '<input name="mail_type" type="hidden" value="default_all">' . "\n";
echo '<input type="submit" value="Send inventory to everyone">' . "\n";
echo '</form>';
echo '</center>' . "\n";
echo '</td>' . "\n";
echo '<td>' . "\n";

# Now we need an "advanced" option.
echo '<center>' . "\n";
echo "<a href='$mod_top&mod_func=advanced_select'>Advanced mailing</a>\n";
echo '</center>' . "\n";

echo '</td>' . "\n";

# We might as well have a way to go back to the beginning...

echo '<td><center><a href="' . $mod_top . '">Module Top</a></center></td>' . "\n";
# The second row is a single column that allows a send to a single group.

echo '<tr>' . "\n";
echo '<td colspan="3">' . "\n";

# First, get a list of all the available groups
$dbh = new db_iface('database');

$query = 'SELECT grp,id from ' . $_SESSION['mod_table'] . ' ORDER BY grp';
$query_terms[] = "";
$info_data = $dbh->query($query, $query_terms);

echo '<form id="send_one" method="post" action="' . $mod_top . '&mod_func=send_mail">' . "\n";
echo '<input type="hidden" name="mail_type" value="single">' . "\n";

echo '<select name="group">' . "\n";
 foreach($info_data as $row)
  { # Define options
   echo '<option value="' . $row['grp'] . '"';
   echo '>' . $row['grp'] . '</option>' . "\n";
  } # End options
echo '</select>' . "\n";

echo '  CC: <input type="text" name="cc">' . "\n";
echo '  Send-to (override default):  <input type="text" name="override">' . "\n";
echo '  <input type="submit" value="Send to single group">' . "\n";

echo '</td></tr>' . "\n";
echo '</table>' . "\n";
echo '<br/>' . "\n";
echo '</center>' . "\n";


?>
