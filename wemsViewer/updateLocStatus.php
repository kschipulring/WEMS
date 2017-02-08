<?php 

function updateLoc($loc, $eventID)
{

require '../wemsDatabase.php';

$c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');



    //$loc = isset($_GET['loc']) ? $_GET['loc'] : -1;
    //$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;

    //$loc = 61100300; // ALBERTSON for testing purpose
    //$eventID = 68;
    
    $statusNum = 0;
    
    $isAssigned = 0;
    $isClean = 0;
    $isDirty = 0;
   
    
    
    
    
    $passNum = 0;
    
    
    
    
    $qry = oci_parse($c, "select CT_STATUS, CT_PASSNUM from WEMS_CLEANABLE_TARGET where MARKERID = :LOC")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    
    oci_bind_by_name($qry, ":LOC", $loc, -1);
    
    oci_execute($qry);
    
    while($row = oci_fetch_array($qry))
    {
    
       
        $stat = $row['CT_STATUS'];
        
        if($stat == 1) $isDirty = 1;
        if($stat == 4) $isClean = 4;
        if($stat == 2) $isAssigned = 2;
    
        
        $pass = $row['CT_PASSNUM'];
       
        if($pass > $passNum ) $passNum = $pass;
        
        /*
        1 = DIRTY
        2 = In Progress
        4 = Clean
       
        if all is 1 then Loc is 1
        if all is 2 then Loc is 2
        if all is 4 then Loc is 4
        if conponent is 1 and other is 2 then Loc is 2
        if a conponent is 1 and any other is 2 then Loc is 2
        if a conponent is 4 and any other is 2 then Loc is 3 / Half Clean
        
        Pass number is always the larger number 
        
        This logic is for GIS viewing only! Specific conponent information is in the WEMS_CEANABLE_TARGET table. 
        
        
       */ 
        
   
    
    }
    
    
    
    if($isAssigned > 0)
    {
        if($isClean > 0)
        {
            $statusNum = 3; 
        }
        else 
        {
            $statusNum = 2;
        }
    }
    else
    {
        if(($isDirty > 0) && ($isClean > 0))
        {
            $statusNum = 3;
        }
        elseif ($isDirty > 0)
        {
            $statusNum = 1;
        }
        elseif ($isClean > 0)
        {
            $statusNum = 4;
        }
    }
    
    //echo $statusNum;
    
    
    $qry = oci_parse($c, "update WEMS_LOCATION SET STATUS = :STATUS, LOCATION_PASSNUM = :PASSNUM WHERE MARKERID = :MARKERID")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    
    oci_bind_by_name($qry, ":MARKERID", $loc, -1);
    oci_bind_by_name($qry, ":PASSNUM", $passNum, -1);
    oci_bind_by_name($qry, ":STATUS", $statusNum, -1);
    
    oci_execute($qry);

}
?>