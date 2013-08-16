<?php
//search_submit.php
#########################################################################
#                                                                       #
#  This file does two different things, depending on where it's called  #
# from.									#
#									#
# 1) If it's called from the quick-search box, it assumes that the      #
# given search term could be a name, an ldap name, or a comment, and    #
# that all matches should be in the form LIKE '%term%'.			#
#									#
# 2) If it's called from the intermediate search box, it builds a       #
# simple query based on what it received.				#
#									#
# 3) If called from the search form, it does whatever the search form   #
# requests.								#
#									#
#########################################################################

# Now, check if this is a quicksearch.
if(isset($_POST['quicksearch']))
 { # Begin quicksearch
  $term = '%' . $_POST['query_term'] . '%';

#  $query = "SELECT * from " . $_SESSION['mod_table'] . " WHERE (department LIKE ?) OR (ldap_departmentNumber LIKE ?) or (comment LIKE ?)";
  $query = "SELECT * from " . $_SESSION['mod_table'] . " WHERE (" . $quicksearch_col1 . " LIKE ?)";
  if (isset($quicksearch_col2))
   { $query = $query . " OR (" . $quicksearch_col2 . " LIKE ?)";
     $search_terms[] = $term; 
   }
  if (isset($quicksearch_col3))
   { $query = $query . " OR (" . $quicksearch_col3 . " LIKE ?)"; 
     $search_terms[] = $term; 
   }
  $search_terms[] = $term;


  unset($_POST['quicksearch']);
 } # End quicksearch
# If it's not a quicksearch, is it an intermediate search?
elseif(isset($_POST['intermediate_search']))
 { # Begin intermediate search
  if($_POST['search_type'] == 'like')
   { $_POST['query_term'] = '%' . $_POST['query_term'] . '%';  }

  $query = "SELECT * from " . $_SESSION['mod_table'] . " WHERE " . $_POST['column'] . " " . $_POST['search_type'] . " ?";
  
  $search_terms = array($_POST['query_term']); 

  # We're done searching, unset the intermediate_search variable.
  unset($_POST['intermediate_search']);
 } # End intermediate search
# How about an advanced search?
elseif(isset($_POST['advanced_search']))
 { # Begin advanced search
  # Find out the max number of search terms we have to deal with.
  $max_terms = $_POST['max_terms'];

  # Set up the base query.
  $query = 'SELECT * from ' . $_SESSION['mod_table'] . ' WHERE ';

  # Now we get to the complex part.  We know the maximum number of terms
  # we could have.  For each term, we need to construct part of a query,
  # and append it to the already existing query.  We also need to add the 
  # appropriate term to the array of search terms.

  $i = '0';
  while ($i < $max_terms)
   { # Begin WHERE construction
    # First, construct the terms.
    $andor = 'andor' . $i;
    $column = 'column' . $i;
    $comparison = 'comparison' . $i;
    $query_term = 'query_term' . $i;
    $start = 'start' . $i;
    $end = 'end' . $i;

    if(($_POST[$andor] != "") || $i == '0')
     { # Begin if term
       if ($i > 0)
        {
         $query .= " " . $_POST[$andor] . " ";
        }
      
       # Next, find out whether we should start with a paren.
       if(isset($_POST[$start])) 
        {
         $query = $query . "(";
        }
   
       # Now insert the column name
       $query = $query . $_POST[$column] . " ";
   
       # Now the comparison operator
       $query = $query . $_POST[$comparison] . " ";
       
       # Next comes the search term, or, more accurately, its standin
       $query = $query . "?";
       
       # Close the parens, if necessary.
       if(isset($_POST[$end]))
        {
         $query = $query . ")";
        }
   
       # We still need to insert the search term into the search_terms array
       if(stristr($_POST[$comparison], 'LIKE'))
        {
 	 $_POST[$query_term] = "%" . $_POST[$query_term] . "%";
  	}
       $search_terms[] = $_POST[$query_term];
   
       # Finally, increment i
     } # End if term
       $i++;
   } # End WHERE construction
#
#
#  # Now we're done with it, unset the advanced_search variable, just in case.
  unset($_POST['advanced_search']);

 } # End advanced search
  
# It's not a quick search, it's not intermediate or advanced... something must
# be wrong.
else
 { # Begin failsafe
  $_SESSION['temp_message_level'] = "dnl";
  $_SESSION['temp_message'] = "Please pick a search type before sending data to the search_submit page.";
  $location_string = "Location:" . $mod_top;
  header($location_string);
 } # End failsafe

$dbo = new db_iface('database');



#echo "query is " . $query . '<br>';
#echo "search_terms is "; print_r($search_terms); echo "<br>";

$search_result = $dbo->query($query, $search_terms);


unset($query);
unset($search_terms);

if($search_result)
 {
  $num_results = count($search_result);
  $inc_file = $mod_to_include . "/list.php";
  include($inc_file);
 }
else
 {
  echo "<center><p>No results found</p></center>";
 }





?>
