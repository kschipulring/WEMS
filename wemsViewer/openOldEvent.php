<?php




function reOpen($eventId)
{
    //include 'updateLocStatus.php';
    
    
    require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
    
    //$eventId = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
    
   // $eventId = 92;
    
    $qry = oci_parse($c, "select CTID, FORMANID, CTSTATUS, CTPASSNUM, CTBAGS from WEMS_CLEANABLE_TARGET_NOTES WHERE EVENTID = :EVENTID ORDER BY MARKERID, CTID, ENTER_DATETIME")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    oci_bind_by_name($qry, ":EVENTID", $eventId, -1);
    
    oci_execute($qry);
    
    $ctid = "";
    $ctid2 = "";
    $formanid = "";
    $status = "";
    $passNum = "";
    $bags = "";
    $markerid = "";
    
    
    while(($row = oci_fetch_array($qry)) !== false)
    {
        

        
        $ctid = $row['CTID'];
        $formanid = $row['FORMANID'];
        $status = $row['CTSTATUS'];
        $passNum = $row['CTPASSNUM'];
        $bags = $row['CTBAGS'];
        
        
        if(($ctid != $ctid2)&&($ctid2 != ""))
        {
                        
            //insert into  WEMS_CLEANABLE_TARGET and update Location
            
            
            $qry3 = oci_parse($c, "select MARKERID FROM WEMS_CLEANABLE_TARGET WHERE CTID = :CTID")
            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
            
            oci_bind_by_name($qry3, ":CTID", $ctid, -1);
            
            oci_execute($qry3);
            while(($row = oci_fetch_array($qry3)) !== false)
            {
                $markerid = $row['MARKERID'];
            }
            
            

            reOpenLoc($ctid, $formanid, $status, $passNum, $bags, $markerid, $eventId);
            

            
        } // if(($ctid != $ctid2)&&($ctid2 != ""))
        
        $ctid2 = $ctid;
        
  
        
    }
    
   // updateLoc($ctid, $formanid, $status, $passNum, $bags, $markerid);
    
  
    
    
}   



function reOpenLoc( $uCtid, $uFormanid, $uStatus, $uPassNum, $uBags, $uMarkerid, $eventId)
{
    include_once 'updateLocStatus.php';
    require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $qry2 = oci_parse($c, "update WEMS_CLEANABLE_TARGET SET ASSIGNED_SITEFOREMEN = :ASSIGNED_SITEFOREMEN, CT_STATUS = :CT_STATUS,
                                    CT_PASSNUM = :CT_PASSNUM, CT_BAGS = :CT_BAGS where CTID = :CTID")
                                    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                    oci_bind_by_name($qry2, ":ASSIGNED_SITEFOREMEN", $uFormanid, -1);
                                    oci_bind_by_name($qry2, ":CT_STATUS", $uStatus, -1);
                                    oci_bind_by_name($qry2, ":CT_PASSNUM", $uPassNum, -1);
                                    oci_bind_by_name($qry2, ":CT_BAGS", $uBags, -1);
                                    oci_bind_by_name($qry2, ":CTID", $uCtid , -1);

                                    oci_execute($qry2);


                                    //include 'updateLocStatus.php';
                                     
                                    updateLoc($uMarkerid, $eventId);







}
    
  ?>