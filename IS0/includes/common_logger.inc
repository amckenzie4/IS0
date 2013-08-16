<?php

//common_logger.inc

#########################################################################
#                                                                       #
#  This is a fairly simple tool.  By default, it takes two arguments:   #
# 1) Log Level -- a number (1-255) or word -- higher numbers mean       #
#                 higher levels of logging                              #
# 2) Message -- a message to be written to the log.			#
#									#
# A third option is available, which is output type -- normally it will #
# either read from the config file or act according to my defaults,     #
# but you can change that on the fly if necessary.  (NOT YET WRITTEN)   #
#									#
# The log levels available if not using numbers are debug, info,        #
# warning, error, and critical.						#
#									#
#########################################################################


# If there is no config file already defined, point to one here, 
# or the default of ./logs/error.log will be used.

# $log_file = "logs/error.log";

# If there is no config file and you haven't set it elsewhere,
# set the default level to record logs at here.

# $log_record = "warning";

function logger($log_level, $message, $output = "file")
 { # Begin function logger
  # First, figure out where the logger was called from, in case we need that.
  $self = $_SERVER["PHP_SELF"];

  # Make the log-level be all upper case.
  $log_level = strtoupper($log_level);

  # Next, figure out what the log file is.
  if (isset($GLOBALS["log_file"]))
   { $log_file = $GLOBALS["log_file"]; }
  if (isset($GLOBALS["config"]["system"]["log_file"]))
   { $log_file = $GLOBALS["config"]["system"]["log_file"]; }
  else { $log_file = "logs/error.log"; }

  
  # Now we need to set up log levels.  We want to make sure that we can
  # track a given level and above, so we need to associate log levels with
  # numbers.
  $levels = array(
	"NONE" => 0, 
	"DEBUG" => 1, 
	"INFO" => 50, 
	"WARNING" => 100, 
	"ERROR" => 150, 
 	"CRITICAL" => 200);

  # Now, check on the default log checking level
  if (isset($GLOBALS["config"]["system"]["log_record"]))
   { $log_record = $GLOBALS["config"]["system"]["log_record"]; }
  elseif (isset($GLOBALS["log_record"]))
   { $log_record = $GLOBALS["log_record"]; }
  else { $log_record = "WARNING"; }



  $log_record = strtoupper($log_record);
  $log_record = $levels[$log_record];
  # Now that we have a log recording level and we know what level the input
  # message is, see if it should be recorded.
  $ll = $levels[$log_level];

#echo "ll is " . $ll . "\n";
#echo "log_record is " . $log_record . "\n";

  if ( $ll >= $log_record)
   {
    $log_string = date('(Y-m-d, H:i:s)') . "  " . $log_level . ":  " . $message . "\n";
    if($GLOBALS["config"]["system"]["logs_to_screen"] != 'no')
     {
      echo "<br>";
      echo $log_string;
      echo "<br>";
     }
#    echo 'logfile is ' . $log_file . '<br>';
    error_log($log_string, 3, $log_file);
   }

 } # End function logger

?>
