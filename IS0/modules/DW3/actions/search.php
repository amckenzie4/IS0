<?php
//search.php

# This page allows more complex searches than either the quicksearch or
# intermediate search does.

# I want to be able to re-use this, so I'm making it generic.
# That means I want to be able to decide how many rows of search boxes 
# to generate easily.  Four should usually be enough.
# This should be in human-sensible format.  That is, starting with 1.

$search_rows = '4';

#### Commented out because this is being done in the header.  Left in
#### because I may move it OUT of the header.
# The first thing we need is to auto generate a list of columns, including ID.
# This will be used later, in building the form.
#$dbo = new db_iface('database');
#
#$query = 'SELECT * from ' . $_SESSION['mod_table'] . " limit 1";
#$query_terms[] = "";
#$all_data = $dbo->query($query, $query_terms);

#if ($all_data)
# { # begin generate column array
#  foreach($all_data as $row)
#   {
#    foreach($row as $key=>$val)
#     {
#      $columns[] = $key;
#     }
#   }
# } #end generate column array

# Because I'm lazy, I'm going to grab any previous search data out of _SESSION 
# now, rather than having to type it out each time.

if(isset($_SESSION['last_asearch']));
 {
  $prev_data = $_SESSION['last_asearch'];
 }

if($_GET['reset'] == 'yes')
 {
  unset($_SESSION['last_asearch']);
  unset($prev_data);
 }

#echo "prev_data = "; print_r($prev_data); echo "<BR>";
 
# Now that we have a list of columns, we can start generating the search
# form.

# Generate the table:
echo "<center>";
echo "<table width='75%' border='1'>";
echo "<tr><th colspan='5'>Advanced Search:  Department</th></tr>";

# Start the form
echo '<form method="post" action="' . $mod_top . '&mod_func=search_submit">';

# These are a few things we want passed to the search_submit file.
echo '<input type="hidden" name="advanced_search" value="yes">';
echo '<input type="hidden" name="max_terms" value="' . $search_rows . '">';
$i = '0';



while ($i < $search_rows)
 { # Begin form generation
  # Start a new row
  echo "<tr>";
  # Create the and/or drop box
  # This should only appear in lines 2 and up.
  echo '<td width="15%">';
  if ($i > 0)
  {
   $ao = "andor" . $i;
   echo '<select name="' . $ao . '">';
   echo '<option value="">--Select And/Or--</option>';
   echo '<option value="and"';
    if ($prev_data[$ao] == 'and') { echo ' selected="selected"';}
    echo '>And</option>';
   echo '<option value="or"';
    if ($prev_data[$ao] == 'or') { echo ' selected="selected"';}
    echo '>Or</option>';
   echo '</select>';
  }
  echo '</td>';
  # Finished creating and/or drop box

  # Create the column drop box
  echo '<td width="25%">';
  $cn = "column" . $i;
  echo '<select name="' . $cn . '">';
  echo '<option value="">--Select Column to search--</option>';
  foreach($columns as $col)
   {
    echo '<option value="' . $col . '"';
    if ($prev_data[$cn] == $col) { echo ' selected="selected"';}
    echo '>' . $col . '</option>';
   }
  echo '</select>';
  echo '</td>';
  # Finished creating column drop box

  # create is/contains drop box
  echo '<td width="15%">';
  $comp = "comparison" . $i;
  echo '<select name="' . $comp . '">';
  echo '<option value="">--Select comparison:--</option>';
  echo '<option value="="';
   if ($prev_data[$comp] == '=') { echo ' selected="selected"'; }
   echo '>Is</option>';
  echo '<option value="LIKE"';
   if ($prev_data[$comp] == 'LIKE') { echo ' selected="selected"'; }
   echo '>Contains</option>';
  echo '<option value="!="';
   if ($prev_data[$comp] == '!=') { echo ' selected="selected"'; }
   echo '>Does not equal</options>';
  echo '<option value="NOT LIKE"';
   if ($prev_data[$comp] == 'NOT LIKE') { echo ' selected="selected"'; }
   echo '>Does not contain</option>';
  # I'm commenting out < and >, because they're not really going to be
  # useful in any of the upcoming modules.  If I ever want them, they'll
  # be there.
  #echo '<option value=">"';
  # if ($prev_data[$comp] == '>') { echo ' selected="selected"'; }
  # echo '>Greater than</option>';
  #echo '<option value="<"';
  # if ($prev_data[$comp] == '<') { echo ' selected="selected"'; }
  # echo '>Less than</option>';
  echo '</td>';
  # Finished creating is/contains drop box

  # Create text box
  echo '<td>';
  $qt = "query_term" . $i;
  echo '<input type="text" size="40" name="' . $qt . '"';
   if(isset($prev_data[$qt])) echo ' value = "' . $prev_data[$qt] . '"';
   echo '>';
  echo '</td>';
  # Finished creating text box

  # Create groupings check boxes
  echo '<td width="14%">';
  $gs = "start" . $i;
  $ge = "end" . $i;
  echo '<input type="checkbox" name="' . $gs . '" value="y"';
   if ($prev_data[$gs] == "y") { echo ' checked="checked"'; }
   echo '>Start Grouping</input>';
  echo '<br/>';
  echo '<input type="checkbox" name="' . $ge . '" value="y"';
   if ($prev_data[$ge] == "y") { echo ' checked="checked"'; }
   echo '>End Grouping</input>';
  echo '</td>';
  # Finished groupings check boxes
  
  # End the row
  echo '</tr>';

  # Increment $i
  $i++;

 } # End form generation

unset($prev_data);
#echo "prev_data = "; print_r($prev_data); echo "<BR>";


echo "</table>";
echo '<br/>';
echo '<input type="submit" value="Search">';
echo '</form>';
echo '&nbsp;&nbsp;<a href="' . $mod_top . '&mod_func=search&reset=yes">Reset Form</a>';

echo "</center>";

?>
