README
------

  For the most part, these tools are quite generic, and quite a lot of 
effort has gone into keeping them that way.  That means that building a new 
module can be quite easy, if it's simple, or somewhat challenging, if it's 
especially complex.  For the purposes of this document, a 'simple module' is
one that relies on one and only one table:  no columns in the table are 
references to any other table, so it can be treated as a stand-alone entity. 
A 'complex module' is one where one or more of the columns most match an entry
from another table.

Building a simple Module
------------------------

  This should be quite easy.  Here are the steps, assuming a module called 
"newmodule".

1) Copy modules/skel to modules/newmodule.

2) Add the line "newmodule" to modules/active_mods.txt

3) CD to modules/newmodule.

4) Edit the variables in module_variables.ph.  See docs/module_variables.txt
for details.

5) Edit list.php to create the listing setup you want.  Your code should go
between the triple hash-marks ("### START building the listing" and 
"### STOP building the listing").  Every column is available as $row['column'].

6) If you want some informational details to display on the front page, add 
them to module.php.  Your code should go between the triple hash-marks 
( "### START front page listing" and "### STOP front page listing").  
You can find some useful notes about this in docs/module.php.txt.


Building a complex Module
-------------------------

  This is harder, but not necessarily much harder.

1) Copy modules/skel to modules/newmodule.

2) Add the line "newmodule" to modules/active_mods.txt

3) CD to modules/newmodule.

4) Edit the variables in module_variables.ph.  See docs/module_variables.txt
for details.  This is where things change.
