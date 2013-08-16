README
------

  This should provide a basic layout for a new module.  The search
pages should all work reliably as they are, although you'll need to change
the names of the columns in the quick-search section of search_submit.php.

  The add_mod_form and add_mod_submit files will need substantial changes:
they control adding and modifying records, and they need to accurately 
reflect the table the module refers to.  Similarly, list.php will need to be
completely rebuilt to reflect the table in question.

  header.inc supplies basic information like the table name to everything else,
so it must be updated first.  My recommended sequence is:

1) Modify header.inc to reflect the correct table, and any other changes for
this module.

2) Modify search_submit.php to use the correct columns in the quicksearch
section.

3) Modify (module).php to reflect the correct table.

4) Re-write list.php to deal correctly with the table.

3) If other changes are needed, make them then. 
