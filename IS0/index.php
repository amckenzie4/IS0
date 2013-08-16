<?php

echo <<< END
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
   <title>Redirect page</title>
END;

function curPageURL() 
{
   $pageURL = 'https';
   $pageURL .= "://";
   $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

   return $pageURL;
}

$url = curPageURL();

if(preg_match('/index.php/', $url))
{
   $url = preg_replace('/index.php/', 'core.php', $url);
}
else
{
   $url = $url . 'core.php';
}


echo '<meta http-equiv="REFRESH" content="0;url=' . $url . '">';

echo <<< END
</head>
<body>
</body>
</html>
END;

?>


