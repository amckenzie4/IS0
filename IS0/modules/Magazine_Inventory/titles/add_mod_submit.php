<?php

#########################################################################
#                                                                       #
#  Here's where we actually insert or modify the data into the database.#
# The sequence is:							#
# 1) Determine whether we're adding or modifying.			#
# 2) Create the DB handle						#
# 3) Generate the query and query terms					#
# 4) Send the query and terms to the db.				#
# 5) Display what was done.						#
# 6a) If we were adding a record and "Add another" was checked, include #
#     add_mod_form.php							#
# 6b) Otherwise, include departments.php				#
#									#
#########################################################################

# Step 1 -- check if we're adding.

//echo "post is: ";
//print_r($_POST);


if (isset($_POST['add']))
  {
   include_once('includes/common_db.inc');
   $dbo = new db_iface('database');

  # Create the base queries.
  # query1 is the base query, query2 is the list of question marks.
  $query1 = "INSERT INTO " . $_SESSION['mod_table'] . " (";
  $query2 = ") VALUES (";

  $i = "1";
  # This pulls the search terms from the input, based on $col_array
  foreach($col_array as $col=>$val)
  { # Begin query construction
#echo "i is " . $i . "<br>";
   if ($i == '1') { $key_column = $col; $just_added = $_POST[$col]; }
   if ($i > '1') { $query1 .= ','; }
   $query1 = $query1 . $col;
   if ($i > '1') { $query2 .= ','; }
   $query2 = $query2 . '?';
   $search_terms[] = $_POST[$col];
   $i++;
  } # End query construction

  # The two halves of the query need to be joined, and the second
  # half needs to be closed out.
  $query = $query1 . $query2 . ')';


#echo "query is '" . $query . "'<br>";
#echo "terms are: ";
#print_r($search_terms);
#echo "<br>";

  $query_result = $dbo->query($query, $search_terms);

#echo "query_result is " . $query_result . '<br>';
#echo "record name is" . $just_added . ": key_column is " . $key_column . "<br>";

   if ($query_result)
    {
echo "<p>success!<p>";
     echo '<p align="center">Added "' . $just_added . '" to table.</p>';
     if(isset($_POST['add_more']))
       { $inc = $mod_to_include . "/add_mod_form.php"; include($inc); }
     else 
       { $inc = $mod_to_include . $module_name; include($inc); }
    }
   else
    {
     echo "Failed to add record!  Attempted query '" . $query . "' with terms";
     print_r($search_terms);
     $inc = $mod_to_include . $module_name; 
     include($inc);
    }

  } # End add code
else
 { # Begin mod code
  include_once('includes/common_db.inc');
  $dbo = new db_iface('database');
  
  # Create the base query:
  $query = "UPDATE " . $_SESSION['mod_table'] . " SET ";

  unset($query_terms);
  # Now make it include all the fields.
  $i = '1';
  foreach($col_array as $col=>$val)
   { # Begin query generation
     if($i > '1') { $query .= ','; }
     $query = $query . $col . "=?";
     $query_terms[] = $_POST[$col];
     $i++;
   }

  # Finally, add the WHERE clause...
  $query = $query . " WHERE id=?";
  # and set the final term.
  $query_terms[] = $_POST['record'];

//echo "<br>query is '" . $query . "'<br>";
//echo "<br>terms are: ";
//print_r($query_terms);
//echo "<br>";

  $search_result = $dbo->query($query, $query_terms);

//echo "search result is ";
//print_r($search_result);

  if($search_result)
   {
    echo "<p>Update successful.</p>";
    $inc = $mod_to_include . "/" . $module_name;
    include($inc);
   }
  else
   {
    echo "<p><b>Update failed!</b></p>";
    $inc = $mod_to_include;
    include($inc);
   }

 } # End mod code


?>
