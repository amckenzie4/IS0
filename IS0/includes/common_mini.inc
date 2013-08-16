<?php


# These are just little bits of code I find useful, and don't like
# cluttering up my main programs with.

# use:  $string = karrtostr($array)
# this just turns a keyed array into a string, with commas between the
# entries.
function karrtostr($array, $separater=", ")
 {
  $string = "";
  $count = 0;
  foreach($array as $key => $value)
   { 
    if ($count > 0) { $string = $string . $separater; }
    $string = $string . $key . " = " . $value;
    $count++;
   }
  return $string;
 }


# use: clear_query()
# this wipes out the query and query_terms variables.
function clear_query()
 {
	unset($GLOBALS['query']);
	unset($GLOBALS['query_terms']);
 }
