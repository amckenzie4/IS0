<?php

#  This file holds variables, and nothing else.  Basically it's a config file
# for just this module.  Most of these were originally stored in header.inc,
# but that was getting ridiculously long -- over 130 lines, almost all of which
# were either comments or variables.  This file allows me to offload all of 
# that and allow header.inc to contain only a header, as was originally
# intended.

# module_header sets the title that appears in the header box
$module_header = "Host tools";
# mod_top defines the top level of the module
$mod_top = 'core.php?core_func=mod&module=' . $_SESSION['module'];
# SESSION[mod_table] tells the sql queries which table to search for.
# Note there is no way to set multiple tables:  if you want to do things
# that way, you're on your own.
$_SESSION['mod_table'] = "hosts";

# record name is just what we call a record in the table.  It's not very 
# important, except cosmetically.
$record_name = "hosts";
# If we need to include a file, do it in the form $mod_to_include/file
$mod_to_include = 'modules/' . $_SESSION['module'];

$module_name = 'hosts';

# These control the quicksearch.  If you don't want all three, comment out
# them out starting with number 3.
$quicksearch_col1 = "name";
$quicksearch_col2 = "grp";
$quicksearch_col3 = "ip";

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
	'type' => array('hr' => 'Record Type',
					    'type' => 'dropfromarray',
						 'default' => 'A',
						 'options' => array('A' => 'A',
												  'CNAME' => 'CNAME',
												  'MX' => 'MX')),
   'name' => array('hr' => 'Host Name',
						 'max_length' => '15',
						 'test' => 'yes'),
   'mac' => array('hr' => 'MAC Address',
						'test' => 'yes'),
   'subnet' => array('hr' => 'Subnet',
				         'type' => 'dropfromtable',
		    				'table' => 'subnet',
		    				'column' => 'subnet',
							'jscript' => 'yes',
							'js_on_what' => 'onchange',
							'js_do_what' => "nextFree(this.value,'ip')"),
   'ip' => array('hr' => 'IP Address',
					 'size' => '15',
					 'div' => 'yes',
					 'test' => 'yes',
					 'prompt' => 'Or target if CNAME'),
	'pool' => array('hr' => 'Pool',
						 'prompt' => 'Check to allowed pooling',
						 'type' => 'checkbox',
						 'value' => 'yes'),
   'wireless_mac' => array('hr' => 'Wireless MAC Address',
						'test' => 'yes'),
   'wireless_subnet' => array('hr' => 'Wireless Subnet',
				         'type' => 'dropfromtable',
		    				'table' => 'subnet',
		    				'column' => 'subnet',
							'jscript' => 'yes',
							'js_on_what' => 'onchange',
							'js_do_what' => "nextFree(this.value,'wireless_ip')"),
   'wireless_ip' => array('hr' => 'Wireless IP Address',
					 'size' => '15',
					 'div' => 'yes',
					 'test' => 'yes'),
   'department' => array('hr' => 'Department',
								'type' => 'dropfromtable',
								'table' => 'department',
								'column' => 'department'),
	'grp' => array('hr' => 'Group Name',
						'type' => 'dropfromtable',
                  'table' => 'org_grp',
						'column' => 'grp'),
	'location' => array('hr' => 'Location',
  							  'prompt' => "ie, 'BLDG1234a'"),
	'owner' => array('hr' => 'owner'),
	'prime_user' => array('hr' => 'Primary User',
								 'default' => 'group'),
	'model' => array('hr' => 'Make and model'),
	'ser_num' => array('hr' => 'Serial Number'),
	'os' => array('hr' => 'Operating System',
					  'type' => 'dropfromtable',
					  'table' => 'operating_system',
					  'column' => 'os'),
	'asset_num' => array('hr' => 'Asset Number'),
   'comments' => array('hr' => 'Comments'),
   'status' => array('hr' => 'Inactive',
							'type' => 'checkbox',
							'prompt' => 'If checked, system will not get an IP!',
							'value' => 'off')
 );


?>
