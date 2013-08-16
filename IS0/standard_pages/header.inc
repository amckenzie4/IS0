<?php

echo '<CENTER>';
echo '<TABLE WIDTH="100%">';
echo '<TR><TH ALIGN=LEFT>';
if ( isset($_SESSION['guest']) )
 { echo "Browsing " . $_SESSION['uid'] ."'s collection"; }
else
 { echo "Welcome, " . $_SESSION['uid'] . "."; }
echo "</TH>";
echo "<TD ALIGN=RIGHT>".date("H:i T --  d F Y")."</TD></TR>";
echo "<tr>";
echo '<TD ALIGN=RIGHT colspan="2"><a href="./core.php?core_func=main">Main Page</a>  <a href="./core.php?core_func=logout">Log Off</a>';
echo "</TABLE>";
echo '</CENTER>';

# Since it's annoying to have to include the common_db file in every page
# manually, this does it automatically.  It's slightly less efficient, but
# it is much easier.
$db_inc = $config['system']['include_dir'] . "/common_db.inc";
include_once($db_inc);

# In some situations you may need to relay a message without being able to
# simply echo it.  If it is written to _SESSION['temp_message'] it will be
# written here the next time a page loads, and then erased from _SESSION.
if(isset($_SESSION['temp_message']))
 {
  echo "<center>";
  echo "<p>" . $_SESSION['temp_message'] . "</p>";
  echo "</center>";
  if (isset($_SESSION['temp_message_level'])) { $level = $_SESSION['temp_message_level']; }
 else { $level = 'warning'; }
 if ($level != 'dnl') { logger(warning, $_SESSION['temp_message']); }
 unset($_SESSION['temp_message']);
 unset($_SESSION['temp_message_level']);
 }

?>
