<?php
function check_auth_ldap () {

  $sessionTimeoutSecs = 60;

  if (!isset($_SESSION)) session_start();

  if (!empty($_SESSION['lastactivity']) && $_SESSION['lastactivity'] > time() - $sessionTimeoutSecs && !isset($_GET['logout'])) {

    // Session has expired or a logout was requested
    unset($_SESSION['lastactivity'], $_SESSION['username'], $_SESSION['password']);
    header("Location: http://webappdev.lirr.org/dev/CMCentral/login.php");
    exit;
  }


}
?>