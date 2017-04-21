<?php
function oci_pconnect($wemsDBusername, $wemsDBpassword, $wemsDatabase){
		
	$tns = "(DESCRIPTION=
  (ADDRESS=(PROTOCOL=TCP)(HOST=lirr-oradbhdev.lirr.org)(PORT=10100))
  (CONNECT_DATA=(SID=$wemsDatabase))
)";
	$db_username = $wemsDBusername;
	$db_password = $wemsDBpassword;
		
	$GLOBALS["egm"] = "";
		
	try{
		$conn = new PDO("oci:dbname=".$tns,$db_username,$db_password);
			
		return $conn;
	}catch(PDOException $e){
		$GLOBALS["egm"] = $e->getMessage();
	}
}
	
function oci_error(){
	return $GLOBALS["egm"];
}
	
function oci_parse($conn, $sql_text){
	$stmt = $conn->prepare( $sql_text );
	
	$GLOBALS["temp_stmnt"] = $stmt;
		
	return $stmt;
}

function oci_bind_by_name($statement, $bv_name, &$variable){
	
	$statement->bindParam($bv_name, $variable);
	
	return true;
}
	
function oci_execute($statement){
	$statement->execute();
	
	return true;
}
	
function oci_fetch_array($statement){
	return $statement->fetch(PDO::FETCH_BOTH);
}

function oci_close($connection){
	$connection = null;
}

function oci_new_cursor($connection){
	return $GLOBALS["temp_stmnt"];
}

function oci_free_statement($statement){
	$statement->closeCursor();
	
	return true;
}

define("OCI_DEFAULT", 0);
?>