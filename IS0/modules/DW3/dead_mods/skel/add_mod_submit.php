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

#echo "post is: ";
#print_r($_POST);


if (isset($_POST['add']))
  {
   include_once('includes/common_db.inc');
   $dbo = new db_iface('database');
   $query = "INSERT INTO " . $_SESSION['mod_table'] . "(department,ldap_departmentNumber,comment) VALUES (?,?,?)";
   $search_terms = array($_POST['dept_name'], $_POST['dept_number'], $_POST['comment']);

#echo "query is '" . $query . "'<br>";
#echo "terms are: ";
#print_r($search_terms);
#echo "<br>";


   $query_result = $dbo->query($query, $search_terms);

   if ($query_result)
    {
     echo '<p align="center">Added department "' . $_POST['dept_name'] . '" to table.</p>';
     if(isset($_POST['add_more']))
       { include('modules/departments/add_mod_form.php'); }
     else 
       { include('modules/departments/departments.php'); }
    }
   else
    {
     echo "Failed to add record!  Attempted query '" . $query . "' with terms";
     print_r($search_terms);
     include('modules/departments/departments.php');
    }

  } # End add code
else
 { # Begin mod code
  include_once('includes/common_db.inc');
  $dbo = new db_iface('database');
  
  $query = "UPDATE " . $_SESSION['mod_table'] . " SET department=?,ldap_departmentNumber=?,comment=? WHERE id=?";
  $query_terms = array($_POST['dept_name'], $_POST['dept_number'], $_POST['comment'], $_POST['record']);

  $search_result = $dbo->query($query, $query_terms);

#echo "search result is ";
#print_r($search_result);

  if($search_result)
   {
    echo "<p>Update successful.</p>";
    include('modules/departments/departments.php');
   }
  else
   {
    echo "<p><b>Update failed!</b></p>";
    include('modules/department/departments.php');
   }



 } # End mod code


?>
