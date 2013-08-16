<?php
# Start sessioning.  Nothing else will work without this.
# It's important to make sure the session id is actually random, 
# since otherwise everyone will share the same session.
#$sessid="AIS" . rand(100000, 999999);
#session_id($sessid);
session_start();

# We want the logger, so we can keep track of what's going on.
include_once("includes/common_logger.inc");
# common_mini.inc includes some useful functions, so include it.
include_once("includes/common_mini.inc");

#########################################################################
#                                                                       #
#   This page is the ringmaster in the little circus we've got going.   #
# It doesn't do much at all on its own, but it makes all the decisions  #
# about what sub-scripts to call, and when to call them.  It also       #
# does most of the session handling, since it's generally running       #
# before anything else.                                                 #
#                                                                       #
#########################################################################



# This is as good a time as any to grab the system config variables,
# and we're going to need them soon, so let's do it. The only catch is 
# that this will overwrite them if they've changed, but they really ought
# to be read-only anyway, it's just not possible to make a constant array.

$config = parse_ini_file("config/config.ini",true);
$db_config = parse_ini_file("config/config_db.ini",true);

logger('debug', 'Done with base includes');

# I really want to force https for this page, but I need to allow an out.
if ($config["system"]["force_https"] != "no")
 {
   if(empty($_SERVER["HTTPS"])) 
    { 
     $newurl = "https://" . $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
     header("Location: $newurl"); 
     exit(); 
    }
  }


# Let's think about security... this deals with session timeouts, and
# does some important things.  It also checks to see whether the browser
# string and IP address have changed, which should give some protection
# from session hijacking.

$sec_file = $config['system']['include_dir'] . "/session_security.inc";
include_once($sec_file);

session_security();

# If $_SESSION["core_func"] isn't set, that's a problem.  We should check right
# away to see if someone has tried to set it somewhere, and if so, do what they
# wanted.

if(isset($_POST["core_func"])) {$_SESSION["core_func"] = $_POST["core_func"];}
if(isset($_GET["core_func"])) { $_SESSION["core_func"] = $_GET["core_func"]; }

# Next, check whether $_SESSION is set.  If not, assume the user has never
# been here before, and set some basic session variables.

if((!isset($_SESSION['uid'])) && ($_SESSION['core_func'] != 'login'))
 {
  logger('info', "SESSION unset -- setting base vars");  
  session_start();
  $_SESSION["firstload"] = "yes";
  $_SESSION["core_func"] = "frontpage";
  $_SESSION["testcount"] = "0";
 }

# This is cleanup:  if there's still a session version of mod_func set,
# but it wasn't set by the previous page, it should be wiped out.

if(!isset($_POST['mod_func']))
 {
	unset($_SESSION['mod_func']);
 }


?>

<html>
<head>
  <title><?php print $config["interface"]["page_title"];?></title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link rel="stylesheet" href="style.css" />
</head>
<body bgcolor="#A5A5A5">
<div class="wrapper">
<?php

# These are here for debugging, to confirm that I know what's going on.
#echo "<p>";
#if(session_id()) { echo "session id is " . session_id() . ".<br>";}
#echo "_POST contains: "; print_r($_POST); echo "<br>";
#echo "_GET contains: "; print_r($_GET); echo "<br>";
#echo "_SESSION contains: "; print_r($_SESSION);
#echo "</p>";


# So here we get to the main show.  The first function we want is the ability
# to log in.  We use the $_SESSION["core_func"] variable to determine, what,
# exactly, we want to do.  There are two components to logging in.  The first,
# frontpage, provides a log-in screen.  The second, login, does the actual
# testing, and either lets you log in or doesn't.

if ($_SESSION["core_func"] == "frontpage")
 { # Begin frontpage function
  $_SESSION["firstload"] = "no";
  include("./standard_pages/frontpage.php");
 } # End frontpage function

if ($_SESSION["core_func"] == "login")
 { # Begin login function
  logger('debug', "About to include login.php");
  include("./standard_pages/login.php");
  exit();
 } # End login function



# Logging out is critical.  As of this writing, it's a simple function:
# destroy the session, erase everything in the session, get, and post arrays,
# and then reincludes this file.

#print_r($_SESSION);

if ($_SESSION['core_func'] == "logout")
 {
  logger('debug', "About to include logout.php");
  include('./standard_pages/logout.php');
 }


# It's likely that we want to have a common header on all pages once 
# we've authenticated.  If so, here's where to add it.

if ( (file_exists($config["system"]["header_file"])) && ($_SESSION["core_func"] != "frontpage") )
 { include_once($config["system"]["header_file"]); }

# Next is the main page function:  From here you can get to any of the sub-
# functions, or log out.

if ($_SESSION["core_func"] == "main")
 { # Begin main function

  logger('debug', "Entering core_func");
  include("standard_pages/main_page.html"); 
 } # End main function


if ($_SESSION["core_func"] == "mod")
 {
  # Now we need to check if either _POST or _GET contains a module directive.
  if((isset($_POST["module"])) || (isset($_GET["module"])))
   {
    # Set the get or post module directive to session. 
    if (isset($_POST["module"])) { $_SESSION['module'] = $_POST["module"]; } 
    if (isset($_GET["module"])) { $_SESSION['module'] = $_GET["module"]; }
    # Set the get or post mod_func directive to session. 
    if (isset($_POST["mod_func"])) { $_SESSION['mod_func'] = $_POST["mod_func"]; }
    if (isset($_GET["mod_func"])) { $_SESSION['mod_func'] = $_GET["mod_func"]; } 
    $log_text = "_SESSION['mod_func'] is now " . $_SESSION['mod_func'];
    $log_text = $log_text . "_SESSION['module'] is now " . $_SESSION['module'];
    logger('debug', $log_text);
  
    # Each module may include its own header and footer.  This checks for the
    # header, and includes it if it's there.
    $mod_header = $config["system"]["module_dir"] . "/" . $_SESSION["module"] . "/header.inc";
    if(file_exists($mod_header)) { include($mod_header); }
  
    # By default, the module will go to the main module page.  However, 
    # modules can do a lot more.  This next piece controls what function
    # is used;  mod_func will determine which page in the module is loaded.
  
    if (isset($_SESSION['mod_func']))
     {
      $mod_file = $config["system"]["module_dir"] . "/" . $_SESSION["module"] . "/" . $_SESSION["mod_func"] . ".php";
      include($mod_file);
     } # End module function
    else
     {
#      $mod_file = $config["system"]["module_dir"] . "/" . $_SESSION["module"] . "/" . $_SESSION["module"] . ".php";
      $mod_file = $config["system"]["module_dir"] . "/" . $_SESSION["module"] . "/" . "module.php";
      include($mod_file);
     }
  
   } # End module code
 } # End core_func=mod

# This div tag causes the footer to actually be at the bottom of the page.
# If it's missing, the footer will simply be at the bottom of whatever
# element comes last.
echo '<div class="push"></div>';
echo '</div>';
echo '<div class="footer">';

# This has to be here, instead of with the rest of the module code, 
# so that it can be properly pushed to the bottom of the screen with the
# overall footer.
if((isset($_POST["module"])) || (isset($_GET["module"])))
 {
  #  # Each module may include its own header and footer.  This checks for the
  #  # footer, and includes it if it's there.
    $mod_footer = $config["system"]["module_dir"] . "/" . $_SESSION["module"] . "/footer.inc";
    if(file_exists($mod_footer)) { include($mod_footer); }
 }

# The last function is the footer:  if there is one, we want to add it once
# everything else has been printed, as long as someone is logged in.
if ( (file_exists($config["system"]["footer_file"])) && ($_SESSION["core_func"] != "frontpage") )
 { include_once($config["system"]["footer_file"]); }


?>
</div>
</body>
</html>
