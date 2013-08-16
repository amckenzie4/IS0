<?php

#   This is a fairly simple piece.  It simply queries the database for all
# entries in the table, and passes the result to list.php

# If it hasn't already been included, we're going to need the database object.
include_once("includes/common_db.inc");

#echo "in qlist.php<br>";

$dbo = new db_iface('database');

#echo "mod_table is " . $_SESSION['mod_table'] . "<br>";

$query = "SELECT * from " . $_SESSION['mod_table'];
$search_terms[] = "";
$search_result = $dbo->query($query, $search_terms);
$num_results = count($search_result);

#echo "results are "; print_r($search_result); echo "<br>";

$temp_inc = $mod_to_include . "/list.php";
include($temp_inc);

?>
