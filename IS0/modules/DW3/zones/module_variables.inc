<?php

#  This file holds variables, and nothing else.  Basically it's a config file
# for just this module.  Most of these were originally stored in header.inc,
# but that was getting ridiculously long -- over 130 lines, almost all of which
# were either comments or variables.  This file allows me to offload all of 
# that and allow header.inc to contain only a header, as was originally
# intended.

# module_header sets the title that appears in the header box
$module_header = "Zone tools";
# mod_top defines the top level of the module
$mod_top = 'core.php?core_func=mod&module=' . $_SESSION['module'];
# SESSION[mod_table] tells the sql queries which table to search for.
# Note there is no way to set multiple tables:  if you want to do things
# that way, you're on your own.
$_SESSION['mod_table'] = "zone";

# record name is just what we call a record in the table.  It's not very 
# important, except cosmetically.
$record_name = "zone";
# If we need to include a file, do it in the form $mod_to_include/file
$mod_to_include = 'modules/' . $_SESSION['module'];

$module_name = 'zones';

# These control the quicksearch.  If you don't want all three, comment out
# them out starting with number 3.
$quicksearch_col1 = "zone";
$quicksearch_col2 = "admin_email";
$quicksearch_col3 = "comments";

# I really don't see why anyone would use this, but it was easy to put in,
# so I did, for the sake of completeness.  If you set $retain_quicksearch
# to "yes", the last search term will be retained and inserted as the default 
# value in the quick-search box.

#$retain_quicksearch = 'yes';


# $col_array allows autogeneration of add and modify forms for simple 
# modules.  If it's not set, the add_mod_form.php file will try to auto-
# generate something based on the contents of the table itself.
# Anything more complex than a text box will require manual intervention.

# The general form for $col_array is:
#  myslql column => array(hr => Human readable form,
#		          default => default value)

$col_array = array(
	'zone' => array('hr' => "Zone"),
	'ttl' => array('hr' => 'TTL',
						'default' => '3600'),
	'serial' => array('hr' => 'Serial',
							'default' => '1'),
	'refresh' => array('hr' => "Refresh Time",
							 'default' => '1h'),
	'retry' => array('hr' => 'Retry time',
						  'default' => '30m'),
	'expiration' => array('hr' => "Expiration",
								 'default' => '7d'),
	'minimum' => array('hr' => 'Minimum',
							 'default' => '1h'),
	'ns1' => array('hr' => 'Nameserver 1'),
	'ns2' => array('hr' => 'Nameserver 2'),
	'admin_email' => array('hr' => 'Admin email',
								  'default' => 'net-l.chem.umass.edu'),
	'comments' => array('hr' => 'Comments'),
	'active' => array('hr' => 'Inactive',
							'prompt' => 'Check to remove zone from bind',
							'type' => 'checkbox',
							'value' => 'no')
  );

?>
