<?php
require '../wemsDatabase.php';

$c = oci_pconnect($wemsDBusername, $wemsDBpassword, $wemsDatabase)
OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(), 1) . '</pre>');

$loc     = isset($_GET['loc']) ? $_GET['loc'] : -1;
$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;

$comments = "";

//$eventID = 0;
$noteTime    = "";
$forman      = "";
$crewSize    = "";
$bags        = "";
$pass        = "";
$status      = "";
$formanName  = "";
$supportDocs = "";
$button      = "";


$qryDoc = oci_parse($c, "SELECT ID from WEMS_LOCDOCS where EVENTID = :EVENTID and MARKERID = :CTID")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_bind_by_name($qryDoc, ":EVENTID", $eventID, -1);
oci_bind_by_name($qryDoc, ":CTID", $loc, -1);

oci_execute($qryDoc);

while ( ($row = oci_fetch_array($qryDoc)) != false  ) {				
	$supportDocs = $supportDocs . $row['ID'] . ",";			
}

$CTID = "";
$qry5 = oci_parse($c, "SELECT CTID from WEMS_CLEANABLE_TARGET  where MARKERID = :MARKERID")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_bind_by_name($qry5, "MARKERID", $loc, -1);
//oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

oci_execute($qry5);

while ( ($row = oci_fetch_array($qry5)) != false ) {
	$CTID = $row['CTID'];
}


$qry = oci_parse($c, "SELECT NOTIFYTIME, ASSIGNED_CREWSIZE, ASSIGNED_SITEFOREMEN, CT_STATUS, CT_PASSNUM, CT_BAGS, NAME
				from WEMS_CLEANABLE_TARGET  where CTID = :CTID")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_bind_by_name($qry, ":CTID", $CTID, -1);
//oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

oci_execute($qry);


while ( ($row = oci_fetch_array($qry)) != false ) {
	$noteTime = $row['NOTIFYTIME'];
	$forman   = $row['ASSIGNED_SITEFOREMEN'];
	// $formanName = $row['NAME'];
	$crewSize = $row['ASSIGNED_CREWSIZE'];
	$bags     = $row['CT_BAGS'];
	$pass     = $row['CT_PASSNUM'];
	$status   = $row['CT_STATUS'];
	$button   = "Update Station";
}

$qry = oci_parse($c, "SELECT TO_CHAR(w.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME,
                      w.CTNOTES, e.FST_NME || ' ' || e.LST_NME as NAME, w.CTSTATUS, w.CTPASSNUM, w.CTBAGS, w.CTNOTEUSER
                      FROM WEMS_CLEANABLE_TARGET_NOTES w
                      LEFT JOIN WEMS_EMPLOYEE e ON e.EMPLOYEENUMBER = w.FORMANID 
                      where w.CTID = :CTID
                      and w.EVENTID = :EVENTID
                      and ((w.FORMANID = e.EMPLOYEENUMBER) or (w.FORMANID = 0))
                      ORDER BY ENTER_DATETIME")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_bind_by_name($qry, ":CTID", $CTID, -1);
oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
oci_execute($qry);


while ( ($row = oci_fetch_array($qry)) != false ) {				
	$nNoteTime = $row['CTSTARTTIME'];
	$nUser     = $row['CTNOTEUSER'];
	$nNote     = $row['CTNOTES'];
	$nForman   = $row['NAME'];
	$nBags     = $row['CTBAGS'];
	$nPass     = $row['CTPASSNUM'];
	$nStatus   = $row['CTSTATUS'];
	
	$qry2 = oci_parse($c, "SELECT STATUS from WEMS_LOCATION_STATUS WHERE STATUSID = :STATUSID")
	OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	
	oci_bind_by_name($qry2, ":STATUSID", $nStatus, -1);
	
	oci_execute($qry2);
	
	while ( ($row = oci_fetch_array($qry2)) != false ) {
		$nStatus = $row['STATUS'];
	}

	$comments .= $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . ", Bags: " . $nBags . ", Pass #: " . $nPass . ", Status: " . $nStatus . "\\n";
}

$json = "[{
        \"GANG\": \"$forman\",
        \"FORMANNAME\": \"$formanName\",
        \"COMMENTS\": \"$comments\",
        \"STATUS\": \"$status\",
        \"PASS\": \"$pass\",
        \"BAGS\": \"$bags\",
        \"TIME\": \"$noteTime\",
        \"BUTTON\": \"$button\",
        \"SUPPORTDOCS\": \"$supportDocs\"
        }]";


echo $json;

oci_close($c);
