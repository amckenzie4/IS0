<?php

#echo "You've made it to departments.php!<br>";

unset($_GET);

#########################################################################
#									#
#  This is the central page for dealing with departments.  This should  #
# include some basic information (how many departments there are, for   #
# instance), and offer links to various pages.				#
#									#
#########################################################################


# First, set the department table.  It will be used a lot, and I want it to
# be easy to change.
## This has been moved to the header, and will later be moved to a config file.
##$_SESSION['mod_table'] = "department";

# If it hasn't already been included, we're going to need the database object.
include_once("includes/common_db.inc");

# Let's pull up some basic info.
$dbo = new db_iface('database');

$log_message = "Module table is " . $_SESSION['mod_table'];
logger('debug', $log_message);

$query = "SELECT * from " . $_SESSION['mod_table'];
$search_terms[] = "";
$search_result = $dbo->query($query, $search_terms);

echo "<center>";

if($search_result)
 {
  $num_results = count($search_result);
  echo "<p><b>Found " . $num_results . " " . $record_name . " entries.</b></p>";
  
 } # End good results
else
 { echo "<p>No department entries found.</p>"; }

echo "</center>";
?>
