<?php

session_start();

$inactive = 600;

$session_life = time() - $_session['timeout'];
if($session_life > $inactive)
{  
  session_destroy(); 
  header("Location: http://webappdev.lirr.org/dev/CMCentral/login.php");
}
$_SESSION['timeout'] = time();

?>
