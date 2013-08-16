<?php

#########################################################################
#                                                                       #
#  This is somewhat more complex than most of the scripts in the module.#
# It handles creating the form for both adding and modifying an entry,  #
# and therefore actually has to contact the db if it's going to edit.   #
# That said, it still basically just queries the DB if there is a       #
# record number available, then builds the form.  The form submits to   #
# add_mod_submit.php, which does the hard part.				#
#									#
#########################################################################

# First, figure out whether we're supposed to be adding or modifying.
# If a record number is specified, we can assume we're supposed to be modifying
# a record.  Otherwise, we're almost certainly supposed to be adding.

$search_result[]="";


if(isset($_GET['record']))
 {
  
  $dbo = new db_iface('database');

  $query = "SELECT * from " . $_SESSION['mod_table'] . " WHERE id=?";
  $search_terms[] = $_GET['record'];
  $GLOBALS['search_result'] = $dbo->query($query, $search_terms);
 }

//echo "search_result is "; print_r($search_result); echo "<br>\n";

# If there is an add_mod_jscript.php file, include it.
# This should pretty much always exist, with the testValue
# function in it.

$js_file = $mod_to_include . '/add_mod_jscript.php';
#$js_file = $mod_to_include . '/add_mod_jscript.html';
if(file_exists($js_file)) 
	{ include_once($js_file); }

# If there is an add_mod_tests.php file, include it.

$test_file = $mod_to_include . '/add_mod_tests.php';
if(file_exists($test_file)) 
	{ include_once($test_file); }




# At this point, we can build the form.  The key right now is to remember to 
# offer a default of the current value, if there is one

echo '<form id="add_mod_form" method="post" action="' . $mod_top . '&mod_func=add_mod_submit">' . "\n";

echo '<center><table width="45%">' . "\n";

# Generate the header for the table
echo '<tr><th align="center" colspan="2">' . "\n";
if (isset($_GET['record'])) { echo 'Editing record ' . $search_result[0]['subnet']; }
else { echo "Adding new " . $record_name; }
echo '</th></tr>' . "\n";

# $col_array is stored either in a config file (module_variables.inc) or 
# in header.inc

foreach($col_array as $col=>$val)
{ # Begin generating table rows
  echo '<tr>' . "\n";
  echo '<td align="right">' . $val['hr'];
  if(isset($val['prompt'])) { echo " (" . $val['prompt'] . ")"; }
  echo ': </td>' . "\n";
  if((!isset($val['type'])) || ($val['type'] == "text"))
   { # Begin default/text type
     echo '<td align="left"><input name="' . $col . '" type="text" value="';
     if(isset($search_result[0][$col])) { echo $search_result[0][$col]; }
     elseif(isset($val['default'])) { echo $val['default']; }
     echo '" size="';
     if(isset($val['size'])) { echo $val['size']; }
     elseif($col == 'comments') { echo '60'; }
     else { echo '30'; }
	  echo '"';
	  if (isset($val['max_length']))
		{ # Begin max_length
			echo ' maxlength="' . $val['max_length'] . '"';
		} # End max_length
	  if ($val['test'] == 'yes')
		{  # Begin writing test statement
			 echo ' onkeyup="testValue(this.name,this.value,' . "'" . $col . "_div')" . ';"';
 		}  # End writing test statement
	  if (($val['jscript'] == 'yes') && (isset($val['js_on_what'])) && (isset($val['js_do_what'])))
	   { # Begin writing js statement
			echo ' ' . $val['js_on_what'] . '="' . $val['js_do_what'] . '"';
	   } # End writing js statement
     echo '>' . "\n";
    } # End default/text type

	elseif($val['type'] == 'textarea')
	 { # Begin textarea type
		echo '<td align="left">' . "\n";
      echo '<textarea name="' . $col . '"';
		echo ' rows="';
		if(isset($val['rows'])) { echo $val['rows']; }
		else { echo '4'; }
		echo '" cols="';
		if(isset($val['columns'])) { echo $val['columns']; }
		else { echo '40'; }
		echo '">';
		if(isset($search_result[0][$col])) { echo $search_result[0][$col]; }
      elseif(isset($val['default'])) { echo $val['default']; }
		echo '</textarea>' . "\n";
	 } # End textarea type

   elseif($val['type'] == 'dropfromtable')
    { # Begin dropbox from table type
      echo '<td align="left">' . "\n";
      if (isset($val['db'])) { $db_loc = $val['db']; }
      else { $db_loc = 'database'; }
      $dfth = new db_iface($db_loc);
      $query = 'SELECT ' . $val['column'] . ' FROM ' . $val['table'];
      if (isset($val['sql'])) { $query = $query . ' ' . $val['sql']; }
      $search_terms[] = "";
      $col_vals = $dfth->query($query, $search_terms);

      echo '<select name="' . $col . '"';
		if (($val['jscript'] == 'yes') && (isset($val['js_on_what'])) && (isset($val['js_do_what'])))
		 { # Begin writing js statement
			echo ' ' . $val['js_on_what'] . '="' . $val['js_do_what'] . '"';
		 } # End writing js statement
		echo '>' . "\n";
      echo '<option value="">--Select ' . $col . '--</option>' . "\n";
      $i = '0';
      while($i < count($col_vals))
       { # Start populating drop box
        #$temp_column = $val['column'];
        echo '<option value="' . $col_vals[$i][$val['column']] . '"';
        if($col_vals[$i][$val['column']] == $search_result[0][$col])
          { echo ' selected="selected"'; }
        elseif(($val['default'] == $col_vals[$i][$val['column']]) && (!isset($search_result[0][$col]))) 
          { echo ' selected="selected"'; }
        echo '>' . $col_vals[$i][$val['column']]/* . ' sr0col is ' . $search_result[0][$col]*/ . '</option>' . "\n";
        $i++;
       } # End populating drop box 
      echo '</select>' . "\n";
    } # End dropbox from table type

	if($val['type'] == 'dropfromarray')
	 { # Start dropbox from array type
      echo '<td align="left">' . "\n";
		echo '<select name="' . $col . '"'; 
		if (($val['jscript'] == 'yes') && (isset($val['js_on_what'])) && (isset($val['js_do_what'])))
		 { # Begin writing js statement
			echo ' ' . $val['js_on_what'] . '="' . $val['js_do_what'] . '"';
		 } # End writing js statement
		echo '>' . "\n";
      foreach($val['options'] as $optname => $optval)
     	  { # Begin generating options
			echo '<option value="' . $optval . '"';
         if(isset($val['default']) && ($val['default'] == $optval))
				{ echo 'selected="selected"'; }
			echo '>' . $optname . '</option>' . "\n";	
		  } # Stop generating options
		echo '</select>' . "\n";
	 } # End dropbox from array type

	if($val['type'] == 'checkbox')
	 { # Begin checkbox type
      echo '<td align="left">' . "\n";
      #echo '<input type="checkbox" name="' . $col . '" value="yes"';
      echo '<input type="checkbox" name="' . $col . '" value="' . $val['value'] . '"';
      if((isset($val['checked'])) && ($val['checked'] == 'yes') || ((isset($search_result[0][$col])) && $search_result[0][$col] == $val['value']))
		  { echo ' checked'; }
		if (($val['jscript'] == 'yes') && (isset($val['js_on_what'])) && (isset($val['js_do_what'])))
		 { # Begin writing js statement
			echo ' ' . $val['js_on_what'] . '="' . $val['js_do_what'] . '"';
		 } # End writing js statement
      echo '>' . "\n";
	 } # End checkbox type

  if(($val['div'] == 'yes') or ($val['test'] == 'yes'))
	{
    echo '<div id="' . $col . '_div"></div>' . "\n";
	}
  echo '</td>' . "\n"; 
  echo '</tr>' . "\n";
} # End generating table rows


## If you want to generate these manually, here's a generic form.
## I'm just going to use the autogenerated one, though. 
#echo '<tr>' . "\n";
#echo '<td align="right">Zone name: </td> ';
#echo '<td align="left"><input name="zone" type="text" value="';
#     if(isset($search_result[0]['zone'])) { echo $search_result[0]['zone']; }
#echo '"></td>' . "\n";

if(!isset($_GET['record']))
 {
  echo '<tr>' . "\n";
  echo '<td align="right">Add another?  </td>' . "\n";
  echo '<td align="left"><input name="add_more" type="checkbox" value="Y">' . "\n";
  echo '</td>' . "\n";
  echo '</tr>' . "\n";
  echo '<input name="add" type="hidden" value="add">' . "\n";
 }
else
 {
  echo '<input name="record" type="hidden" value="' . $_GET['record'] . '">' . "\n";
 }

echo '<tr>' . "\n";
echo '<td align="center" colspan="2">' . "\n";
echo '<input type="submit">' . "\n";
echo '</form>' . "\n";
echo '</td></tr>' . "\n";
echo '</table>' . "\n";
echo '</center>' . "\n";


echo '<p id="debug"></p>' . "\n";

?>
