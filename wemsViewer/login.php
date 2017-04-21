<?php
require_once ('oracle_session.class.php');

session_start();
$inactive = 1200; //600 = 10 min

$returnPage = isset($_POST['returnPage']) ? $_POST['returnPage'] : (isset($_GET['returnPage']) ? $_GET['returnPage'] : "");
$username = isset($_POST['id']) ? $_POST['id'] : "";
$password = isset($_POST['pw']) ? $_POST['pw'] : "";
$error = "";

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {
	$session_life = time() - $_SESSION['timeout'];
	if ($session_life < $inactive) {
		header("location: index.php");
	}
}

// $_SESSION['loggedin'] = false; //disabled but found bug
if (isset($_POST['login'])) {
	if (!strlen($username) || !strlen($password)) {
		$error = "Missing user name and/or password.";
	} else {
		ldapauthenticate($username, $password, $returnPage);
	}
}

 //-------------------------------------------------

function ldapauthenticate($username, $password, $returnPage){
	global $error, $username, $password;
	$userGroup = null;
	$ldaphost = "ldaps://auths.lirr.org";
	$ldap = ldap_connect($ldaphost);
	if (!ldap_bind($ldap)) {
		$error = "Error, could not anonymously bind to ldap server.";
		return false;
	}
	$baseDN = "o=LIRR";
	$LDAPfullname = "fullname";
	$LDAPDisabled = "loginDisabled";
	$LDAPusername = "cn";
	$LDAPGroups = "groupMembership";
	$LDAPemail = "mail";
	$ds = ldap_connect("localhost");
	$filter = "(&(objectClass=organizationalPerson)($LDAPusername=$username))";
	$attributes = array(
		$LDAPfullname,
		$LDAPDisabled,
		$LDAPGroups
	);
	$search = @ldap_search($ldap, $baseDN, $filter, $attributes);
	$count = ldap_count_entries($ldap, $search);
	
	if (ldap_count_entries($ldap, $search) < 1) {
		$error = "Invalid username/password.";
	} else {
		$entry = @ldap_first_entry($ldap, $search);
		$attributes = ldap_get_attributes($ldap, $entry);
		$dn = @ldap_get_dn($ldap, $entry);
		$groups = $attributes[$LDAPGroups];
		// if (@ldap_bind($ldap, "$dn", "$password"))
		//   {
		$_SESSION['loggedin'] = true;
		$_SESSION['timeout'] = time();
		$_SESSION['user'] = $username;
		$userGroup = "";
		
		$approved_groups = array(
			"WEMS_Read",
			"WEMS_Write",
			"WEMS_Admin"
		);
		
		foreach($groups as $group) {
			//echo $group . "<br /><br /> beating a dead horse";
			// if(strpos($group, "WEMS_Admin") !== false) {
			if (preg_match('/WEMS_[A-Za-z]+/', $group, $matches)) {
				if (in_array($matches[0], $approved_groups)) {
					// WEMS_Read, WEMS_Write, and WEMS_ADMIN
					$_SESSION["group"] = $matches[0];
				}
			}
		}
		
		if ($returnPage != "") 
		{
			if (isset($_SESSION['group'])) 
			{
				header("Location: $returnPage");
				return true;
			}
			$error = $_SESSION["group"] . ", " . $_SESSION['user'] = $username . ", " . $_SESSION['loggedin'] . ", " . $returnPage;
			return false;
			
		} 
		else 
		{
			header("index.php");
			
			$error = $_SESSION["group"] . ", " . $_SESSION['user'] = $username . ", " . $_SESSION['loggedin'] = true;
			return false;
		}
		
		$fullnames = ldap_get_values($ldap, $entry, $LDAPfullname);
		$disabled = @ldap_get_values($ldap, $entry, $LDAPDisabled);
		$fullname = isset($fullnames[0]) ? $fullnames[0] : "No Name Entry";
		$_SESSION['php_session_fullname'] = $fullname;
		$userGroup = "OK";
		if (!$userGroup) {
			$error = "You do not have authorization to use this application.";
		}
	}
	if (!empty($disabled) && $disabled[0] != "FALSE" && $error == "") {
		$error = "This account has been disabled contact the help desk.";
	}
	if (!@ldap_bind($ldap, "$dn", "$password") && $error == "") {
		if (ldap_get_option($ldap, LDAP_OPT_ERROR_STRING, $extended_error)) {
		} else {
			$error = "Error Binding to LDAP: No additional information is available.";
		}
	}
	@ldap_close($ldap);
	if ($error == "") {
		return $userGroup;
	}
	return false;
}

//--------------------------------------------------
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>MTA Long Island Rail Road / Weather Emergency Management System   </title>
		<link href="css/wems-styles.css" rel="stylesheet" type="text/css" />
		<link href="cm.css" rel="stylesheet" type="text/css" />
	</head>
	<body id="body">
		<div id="container">
			<div id="bannerMenu"> </div>
			<center>
				<img src="../images/wemsPhoto.jpg" />
				<!-- 
				<h1>Weather Emergency Management System (WEMS) <br/> Login</h1>
				 -->
			</center>
			<!-- center><span class="navybld">Login:</span></center--->

 			<form id="loginbox" name="login" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
				<center><span style="color:red"><?php echo $error ?></span></center>
				<input type="hidden" name="returnPage" value="<?php echo $returnPage ?>"/>
				<table class="grid123" border="24" align="center">
					<tr>
						<th class="tablecaption" width="25%">User Id:</th>
						<td><input class="input-box" type="text" name="id" value="<?php echo $username ?>"/></td>
					</tr>
					<tr>
						<th width="25%">Password:</th>
						<td><input type="password" name="pw" value="<?php echo $password ?>"/></td>
					</tr>
					<tr>
						<td colspan="2" style="text-align:center;"><input type="submit" class="wideGreenBtn" value="Login" name="login" /></td>
					</tr>
				</table>
 			</form>
		</div> <!-- end<div id="container">-->
	</body>
</html>
