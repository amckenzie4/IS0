<?php

#  This file holds variables, and nothing else.  Basically it's a config file
# for just this module.  Most of these were originally stored in header.inc,
# but that was getting ridiculously long -- over 130 lines, almost all of which
# were either comments or variables.  This file allows me to offload all of 
# that and allow header.inc to contain only a header, as was originally
# intended.

# module_header sets the title that appears in the header box
$module_header = "Subnet tools";
# mod_top defines the top level of the module
$mod_top = 'core.php?core_func=mod&module=' . $_SESSION['module'];
# SESSION[mod_table] tells the sql queries which table to search for.
# Note there is no way to set multiple tables:  if you want to do things
# that way, you're on your own.
$_SESSION['mod_table'] = "subnet";

# record name is just what we call a record in the table.  It's not very 
# important, except cosmetically.
$record_name = "subnet";
# If we need to include a file, do it in the form $mod_to_include/file
$mod_to_include = 'modules/' . $_SESSION['module'];

$module_name = 'subnets';

# These control the quicksearch.  If you don't want all three, comment out
# them out starting with number 3.
$quicksearch_col1 = "subnet";
$quicksearch_col2 = "mask";
$quicksearch_col3 = "zone";

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
  'subnet' => array('hr' => 'Subnet',
						  'test' => 'yes',
						  'jscript' => 'yes',
						  'js_on_what' => 'onblur',
						  'js_do_what' => "findMask(this.value,'mask_div');"),
  'department' => array('hr' => 'Department',
			'type' => 'dropfromtable',
			'table' => 'department',
			'column' => 'department'),
  'wins' => array('hr' => 'WINServer',
						'test' => 'yes'),
  'dns1' => array('hr' => 'DNS 1',
						'test' => 'yes'),
  'dns2' => array('hr' => 'DNS 2',
						'test' => 'yes'),
  'gateway' => array('hr' => 'Gateway',
							'test' => 'yes'),
  'mask' => array('hr' => 'Netmask',
						'div' => 'yes'),
  'zone' => array('hr' => 'Zone',
		  'type' => 'dropfromtable',
		  'table' => 'zone',
		  'column' => 'zone'),
	'directives' => array('hr' => 'Subnet Directives',
								 'type' => 'textarea'),
	'shared_network' => array('hr' => 'Shared Network',
									  'default' => 'none',
									  'type' => 'dropfromtable',
									  'table' => 'shared_network',
									  'column' => 'network_name'),
	'pool1' => array('hr' => 'First Pool'),
	'pool1_dirs' => array('hr' => "First Pool Directives",
								 'type' => 'textarea'),
	'pool2' => array('hr' => 'Second Pool'),
	'pool2_dirs' => array('hr' => "Second Pool Directives",
								 'type' => 'textarea'),
	'reserve' => array('hr' => 'Number of reserved IPs',
							 'size' => '5'),
	'dhcpd_active' => array('hr' => 'Exclude from dhcpd.conf',
									'type' => 'checkbox',
									'value' => 'no'),
	'bind_active' => array('hr' => 'Exclude from BIND files',
								  'type' => 'checkbox',
								  'value' => 'no'),
    'ddns_enabled' => array('hr' => 'Enable DDNS',
                            'type' => 'checkbox',
                            'value' => 'no')
 );

?>
