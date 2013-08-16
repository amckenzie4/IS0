<?php

#### Everything from here down should always appear in the footer.
# We want to remove the module and function settings from $_SESSION, to make
# sure we go to the right place next time.

unset($_SESSION["module"]);
unset($_SESSION["mod_func"]);

?>

