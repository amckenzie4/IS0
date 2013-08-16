<?php

session_destroy();
unset($_SESSION);
unset($_POST);
unset($_GET);

#include("core.php");
echo '<META HTTP-EQUIV="Refresh" Content="0; URL=./core.php">';
exit;

?>
