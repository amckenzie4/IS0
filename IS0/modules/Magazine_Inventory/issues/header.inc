<?php
# Any variables you might want to change are here.  This should form a
# pretty generic top-bar with add, show all, quick-search, advanced search,
# and module-top links.
include_once('module_variables.inc');

#$module_header = "Zone tools";
#$mod_top = 'core.php?core_func=mod&module=' . $_SESSION['module'];
#$_SESSION['mod_table'] = "zone";
#$record_name = "zone";
#$mod_to_include = 'modules/' . $_SESSION['module'];
#
## $col_array
#
## These control the quicksearch.  It is important that all three be filled.
#$quicksearch_col1 = "zone";
#$quicksearch_col2 = "admin_email";
#$quicksearch_col3 = "comment";

# I really don't see why anyone would use this, but it was easy to put in,
# so I did, for the sake of completeness.  If you set $retain_quicksearch
# to "yes", the last search term will be retained and inserted as the default 
# value in the quick-search box.

#$retain_quicksearch = 'yes';


# I want to store the search parameters for a while.  Basically, I want to
# be able to return to the advanced search and retain the old search terms.
# There's not much point in retaining the quicksearch data, so I'm not going
# to do so.  I'll put the code here in case someone wants it for some reason.
if(isset($_POST['quicksearch'])) { $_SESSION['last_qsearch'] = $_POST;}
if(isset($_POST['intermediate_search'])) { $_SESSION['last_isearch'] = $_POST;}
if(isset($_POST['advanced_search'])) { $_SESSION['last_asearch'] = $_POST;}


# We want to wipe out all retained searches by going to the main page or the 
# show all page.
if( !isset($_GET['mod_func']) || $_GET['mod_func'] === 'qlist')
  {
   unset($_SESSION['last_qsearch']);
   unset($_SESSION['last_isearch']);
   unset($_SESSION['last_asearch']);
  }

#echo "_SESSION = "; print_r($_SESSION); echo "<br>";
#echo "_GET = "; print_r($_GET); echo "<br>";
#echo "_POST = "; print_r($_POST); echo "<br>";

# Row 1 contains general navigation tools, and a quick-search tool.


echo "<center>";
echo '<table width="75%" border="1">';
echo '<tr><th align="center" colspan="5">'. $module_header . '</th></tr>';
echo '<tr>';
echo '<td><center><a href="' . $mod_top . '&mod_func=add_mod_form">Add ' . $record_name . '</a></center></td>';

echo '<td><center><a href="' . $mod_top . '&mod_func=qlist">Show All</a></center></td>';

echo '<td><center><form method="post" action="' . $mod_top .'&mod_func=search_submit">';
echo '<input type="hidden" name="quicksearch" value="yes">';
echo '<input name="query_term" type="text" value="';
if ($retain_quicksearch == 'yes' && isset($_SESSION['last_qsearch']['query_term']))
 { echo $_SESSION['last_qsearch']['query_term'];}
echo '">';

echo '<input type="submit" value="Quick Search"></form></center></td>';
echo '<td><center><a href="' . $mod_top . '&mod_func=search">Advanced Search</a></center></td>';
echo '<td><center><a href="' . $mod_top . '">Module Top</a></center></td>';
echo '</tr>';

# Row 2 is just the intermediate search. First we grab a list of all
# available rows ($columns[]), then we build the row.


$dbh = new db_iface('database');

#$query = 'SELECT * from ' . $_SESSION['mod_table'] . " limit 1";
#$query_terms[] = "";
#$all_data = $dbh->query($query, $query_terms);
#
#if ($all_data)
# {
#  foreach($all_data as $row)
#   {
#    #print_r($row);
#    foreach($row as $key=>$val)
#     {
#      $columns[] = $key;
#     }
#   }
# }

$query = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name=?';
$query_terms[] = $_SESSION['mod_table'];
$info_data = $dbh->query($query, $query_terms);

if ($info_data)
 {
  foreach($info_data as $row)
   {
    #print_r($row);
    foreach($row as $key=>$val)
     {
      $columns[] = $val;
     }
   }
 }
  

echo '<form method="post" action="'. $mod_top . '&mod_func=search_submit">';
echo '<input type="hidden" name="intermediate_search" value="yes">';

echo '<tr>';
echo '<td align="center">';
echo '<select name="column">';
 $i = 1;
 while ($i < count($columns))
  { # Define options
   echo '<option value="' . $columns[$i] . '"';
   if ($_SESSION['last_isearch']['column'] == $columns[$i])
    { echo ' selected="selected"'; }
   echo '>' . $columns[$i] . '</option>';
   $i++;
  } # 
echo '</select>';
echo '</td><td>';
echo '<select name="search_type">';
 echo '<option value="like"';
  if ($_SESSION['last_isearch']['search_type'] == 'like')
   { echo ' selected="selected"'; }
  echo '>contains</option>';
 echo '<option value="="';
  if ($_SESSION['last_isearch']['search_type'] == '=')
   { echo ' selected="selected"'; }
  echo '>is</option>';
echo '</select>';

echo '</td><td colspan="2">';
echo '<input name="query_term" type="text" size="45"';
 if ($_SESSION['last_isearch']['query_term'])
  { echo ' value="' . $_SESSION['last_isearch']['query_term'] . '"'; }
 echo '>';

echo '</td><td>';
echo '<input type="submit" value="Search">';
echo '</form></td></tr>';

echo '</table>';
echo '<br/>';
echo '</center>';
?>
