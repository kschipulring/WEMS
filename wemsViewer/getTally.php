<?php 

require '../wemsDatabase.php';

$c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

$sClean = 0;
$sDirty = 0;
$sInProgress = 0;

$iClean = 0;
$iDirty = 0;
$iInProgress = 0;

$pClean = 0;
$pDirty = 0;
$pInProgress = 0;


$qry = oci_parse($c, "select count(*) as CLEAN from WEMS_LOCATION where LOC_CD = 'S' and STATUS = 4")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

 
oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $sClean = $row['CLEAN'];
}


$qry = oci_parse($c, "select count(*) as INPROGRESS from WEMS_LOCATION where LOC_CD = 'S' and STATUS = 2")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');


oci_execute($qry);

while($row = oci_fetch_array($qry))
{
   $sInProgress = $row['INPROGRESS'];
}


$qry = oci_parse($c, "select count(*) as DIRTY from WEMS_LOCATION where LOC_CD = 'S' and STATUS = 1")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $sDirty = $row['DIRTY'];
}

//_____________________________________________________________________________________________________
//____________________________INTERLOCKINGS____________________________________________________________
//_____________________________________________________________________________________________________

$qry = oci_parse($c, "select count(*) as CLEAN from WEMS_LOCATION where LOC_CD = 'I' and STATUS = 4")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');


oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $iClean = $row['CLEAN'];
}


$qry = oci_parse($c, "select count(*) as INPROGRESS from WEMS_LOCATION where LOC_CD = 'I' and STATUS = 2")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');


oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $iInProgress = $row['INPROGRESS'];
}


$qry = oci_parse($c, "select count(*) as DIRTY from WEMS_LOCATION where LOC_CD = 'I' and STATUS = 1")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $iDirty = $row['DIRTY'];
}


//_____________________________________________________________________________________________________
//____________________________Parking Lots____________________________________________________________
//_____________________________________________________________________________________________________

$qry = oci_parse($c, "select count(*) as CLEAN from WEMS_LOCATION l, WEMS_CLEANABLE_TARGET ct where ct.TYPE = 'P' and ct.MARKERID = l.MARKERID and l.STATUS = 4")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');


oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $pClean = $row['CLEAN'];
}


$qry = oci_parse($c, "select count(*) as INPROGRESS from WEMS_LOCATION l, WEMS_CLEANABLE_TARGET ct where ct.TYPE = 'P' and ct.MARKERID = l.MARKERID and l.STATUS = 2")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');


oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $pInProgress = $row['INPROGRESS'];
}


$qry = oci_parse($c, "select count(*) as DIRTY from WEMS_LOCATION l, WEMS_CLEANABLE_TARGET ct where ct.TYPE = 'P' and ct.MARKERID = l.MARKERID and l.STATUS = 1")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

oci_execute($qry);

while($row = oci_fetch_array($qry))
{
    $pDirty = $row['DIRTY'];
}



?>