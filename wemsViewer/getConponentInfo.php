<?php 
require '../wemsDatabase.php';

$c = oci_pconnect($wemsDBusername, $wemsDBpassword, $wemsDatabase)
OR die('Unable to connect to the database. Error: <pre>'.print_r(oci_error(), 1) . '</pre>');

$LocId = isset($_GET['param']) ? $_GET['param'] : -1;
$type = isset($_GET['type']) && (preg_match("/[A-Z]{1,1}/", trim($_GET['type']) )) ? $_GET['type'] : "S";

if ($LocId >= 0) {
	$qry = oci_parse($c, "SELECT CTID, FULLNAME FROM WEMS_CLEANABLE_TARGET where MARKERID = :MARKERID and TYPE = '{$type}'")
	OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1).
		'</pre>');

	oci_bind_by_name($qry, ":MARKERID", $LocId, -1);

	oci_execute($qry);
	
	$jsonArr = array();
	
	$jsonArr[] = [
		"CTID" => "0",
		"FULLNAME" => "",
	];

	while($row = oci_fetch_array($qry)) {
		$jsonArr[] = [
			"CTID" => $row["CTID"],
			"FULLNAME" => $row["FULLNAME"],
		];
	}
	
	echo json_encode($jsonArr);
}

oci_close($c);
