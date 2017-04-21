<?php 

  
      
function updateLocationGang($all, $lConponent, $eventID, $lForman, $lNoteTime, $lStatus, $lLoc, 
                            $lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type)
{
      
      $crewsize = 0;
      
        require '../wemsDatabase.php';
        $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
        
        $crewSize = oci_parse($c, "select EMP_ASSIGNED from WEMS_GANG WHERE FORMANID = :FORMANID AND EVENTID = :EVENTID")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        
        oci_bind_by_name($crewSize, ":FORMANID",  $lForman, -1);
        oci_bind_by_name($crewSize, ":EVENTID",  $eventID, -1);
        
        oci_execute($crewSize);
        
        //for each conponent make a note that a gang has been assigned / unassigned........
        while($row = oci_fetch_array($crewSize))
        {
            $crewsize = $row['EMP_ASSIGNED'];
        }
        
               
                
        if($all == "allConponents") 
        {
            
            
            
           
            
            $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = NULL WHERE EVENTID = :EVENTID AND ASSIGN_LOC = :ASSIGN_LOC ")
            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
            
            
            oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
            oci_bind_by_name($qry, ":ASSIGN_LOC",  $lLoc, -1);
            
            oci_execute($qry);
            
            
            
            
            
            
            
            
            
            
            //this will update two conponents because it is updating my location not conponent
            $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = :ASSIGNLOC WHERE FORMANID = :FORMANID and EVENTID = :EVENTID")
            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
            
            oci_bind_by_name($qry, ":ASSIGNLOC",  $lLoc, -1);
            oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
            oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
            
            oci_execute($qry);
            
            
            if($lStatus ==4)
            {
                
                $qry = oci_parse($c, "update WEMS_CLEANABLE_TARGET SET NOTIFYTIME = to_date(:NOTIFYTIME, 'mm/dd/yyyy hh:mi AM'),
                                        ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = :CT_STATUS, CT_PASSNUM = :CTPASSNUM, CT_BAGS = :CTBAGS, CREWSIZE = :CREWSIZE
                                        WHERE MARKERID = :MARKERID AND TYPE = :TYPE")
                                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                
                                                        oci_bind_by_name($qry, ":NOTIFYTIME",  $lNoteTime, -1);
                                                        oci_bind_by_name($qry, ":CT_STATUS",  $lStatus, -1);
                                                        oci_bind_by_name($qry, ":MARKERID", $lLoc, -1);
                                                        oci_bind_by_name($qry, ":CTPASSNUM", $lPassNum, -1);
                                                        oci_bind_by_name($qry, ":CTBAGS", $lNumBags, -1);
                                                        oci_bind_by_name($qry, ":TYPE", $Loc_Type, -1);
                                                        oci_bind_by_name($qry, ":CREWSIZE",  $crewsize, -1);
                
                                                        oci_execute($qry);
                                                        
                                            
                        $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = NULL WHERE FORMANID = :FORMANID and EVENTID = :EVENTID")
                                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                                        
                                                        oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
                                                        oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
                                                        
                                                        oci_execute($qry);
                  
            }
            else 
            {
                        $qry = oci_parse($c, "update WEMS_CLEANABLE_TARGET SET NOTIFYTIME = to_date(:NOTIFYTIME, 'mm/dd/yyyy hh:mi AM'),
                                        ASSIGNED_SITEFOREMEN = :ASSIGNED_SITEFOREMEN, CT_STATUS = :CT_STATUS, CT_PASSNUM = :CTPASSNUM, CT_BAGS = :CTBAGS, CREWSIZE = :CREWSIZE
                                        WHERE MARKERID = :MARKERID AND TYPE = :TYPE")
                                                    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
            
                                                    oci_bind_by_name($qry, ":NOTIFYTIME",  $lNoteTime, -1);
                                                    oci_bind_by_name($qry, ":CT_STATUS",  $lStatus, -1);
                                                    oci_bind_by_name($qry, ":ASSIGNED_SITEFOREMEN", $lForman , -1);
                                                    oci_bind_by_name($qry, ":MARKERID", $lLoc, -1);
                                                    oci_bind_by_name($qry, ":CTPASSNUM", $lPassNum, -1);
                                                    oci_bind_by_name($qry, ":CTBAGS", $lNumBags, -1);
                                                    oci_bind_by_name($qry, ":TYPE", $Loc_Type, -1);
                                                    oci_bind_by_name($qry, ":CREWSIZE",  $crewsize, -1);
            
            
                                                    oci_execute($qry);
                                                    
            }                                      
                                                    //for each conponent
              $locQry = oci_parse($c, "select CTID from WEMS_CLEANABLE_TARGET WHERE MARKERID = :MARKERID AND TYPE = :LOCTYPE") 
                                                    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                                    
                                                    
                                                    oci_bind_by_name($locQry, ":MARKERID",  $lLoc, -1);
                                                    oci_bind_by_name($locQry, ":LOCTYPE",  $Loc_Type, -1);
                                                    
                                                    oci_execute($locQry);
                                                    
                                                    //for each conponent make a note that a gang has been assigned / unassigned........
                                                    while($row = oci_fetch_array($locQry))
                                                    {
                                                        $ctid = $row['CTID'];
                                                       
                                                        $ctQry = oci_parse($c, "insert into WEMS_CLEANABLE_TARGET_NOTES (EVENTID, CTID, CTNOTES, FORMANID, CTSTATUS, CTPASSNUM, CTBAGS, CTSTARTTIME, CTNOTEUSER, ENTER_DATETIME, MARKERID, CREWSIZE)
                                                                                 VALUES(:EVENTID, :CTID, :CTNOTES, :FORMANID, :CTSTATUS, :CTPASSNUM, :CTBAGS, to_date(:CTSTARTTIME, 'mm/dd/yyyy hh:mi AM'), :CTNOTEUSER, SYSDATE, :MARKERID, :CREWSIZE) ")
                                                                                                  OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                                                                                   
                                                        
                                                                                                  oci_bind_by_name($ctQry, ":EVENTID",  $eventID, -1);
                                                                                                  oci_bind_by_name($ctQry, ":CTID",  $ctid, -1);
                                                                                                  oci_bind_by_name($ctQry, ":CTNOTES", $lcomments , -1);
                                                                                                  oci_bind_by_name($ctQry, ":FORMANID", $lForman, -1);
                                                                                                  oci_bind_by_name($ctQry, ":CTSTATUS",  $lStatus, -1);
                                                                                                  oci_bind_by_name($ctQry, ":CTPASSNUM",  $lPassNum, -1);
                                                                                                  oci_bind_by_name($ctQry, ":CTBAGS", $lNumBags , -1);
                                                                                                  oci_bind_by_name($ctQry, ":CTSTARTTIME", $lNoteTime, -1);
                                                                                                  oci_bind_by_name($ctQry, ":CTNOTEUSER", $lUser, -1);
                                                                                                  oci_bind_by_name($ctQry, ":MARKERID", $lLoc, -1);
                                                                                                  oci_bind_by_name($ctQry, ":CREWSIZE", $crewsize, -1);
                                                                                                   
                                                                                                  oci_execute($ctQry);
                                                                                                   
                                                       
                                                                                                  
                                                                                                  
                                                                                                  
                                                       }
                                                    
                                                    
                                                 
  //_______________________________________________________________________________________________________________________________________
  //))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))))
  //________________________________________________________________________________________________________________________________________
  //((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((((
  //__________________________________________________________________________________________________________________________________________
           
        }
        else 
        {
            
           
            
            if($lStatus == 4) // 4 is clean free up the gange for other locations
            {
                    $qry = oci_parse($c, "update WEMS_CLEANABLE_TARGET SET NOTIFYTIME = to_date(:NOTIFYTIME, 'mm/dd/yyyy hh:mi AM'),
                                        ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = :CT_STATUS, CT_PASSNUM = :CTPASSNUM, CT_BAGS = :CTBAGS, CREWSIZE = :CREWSIZE
                                        WHERE CTID = :CTID")
                                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                
                                        oci_bind_by_name($qry, ":NOTIFYTIME",  $lNoteTime, -1);
                                        oci_bind_by_name($qry, ":CT_STATUS",  $lStatus, -1);
                                        oci_bind_by_name($qry, ":CTID", $lConponent, -1);
                                        oci_bind_by_name($qry, ":CTPASSNUM", $lPassNum, -1);
                                        oci_bind_by_name($qry, ":CTBAGS", $lNumBags, -1);
                                        oci_bind_by_name($qry, ":CREWSIZE", $crewsize, -1);
                
                
                                        oci_execute($qry);
                                                        
                   
                                                        
                                                       
                      $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = NULL WHERE FORMANID = :FORMANID and EVENTID = :EVENTID")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                                        
                                       oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
                                       oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
                                                        
                                       oci_execute($qry);
                                                                                       
            }
            else 
            {
                    $qry = oci_parse($c, "update WEMS_CLEANABLE_TARGET SET NOTIFYTIME = to_date(:NOTIFYTIME, 'mm/dd/yyyy hh:mi AM'),
                                        ASSIGNED_SITEFOREMEN = :ASSIGNED_SITEFOREMEN, CT_STATUS = :CT_STATUS, CT_PASSNUM = :CTPASSNUM, CT_BAGS = :CTBAGS,
                                        CREWSIZE = :CREWSIZE
                                        WHERE CTID = :CTID ")
                                                    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
            
                                                    oci_bind_by_name($qry, ":NOTIFYTIME",  $lNoteTime, -1);
                                                    oci_bind_by_name($qry, ":CT_STATUS",  $lStatus, -1);
                                                    oci_bind_by_name($qry, ":ASSIGNED_SITEFOREMEN", $lForman , -1);
                                                    oci_bind_by_name($qry, ":CTID", $lConponent, -1);
                                                    oci_bind_by_name($qry, ":CTPASSNUM", $lPassNum, -1);
                                                    oci_bind_by_name($qry, ":CTBAGS", $lNumBags, -1);
                                                    oci_bind_by_name($qry, ":CREWSIZE", $crewsize, -1);
            
                                                    oci_execute($qry);
                                                    
                                                    
                                                    
                                                    
                                                    
                    $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = :ASSIGNLOC WHERE FORMANID = :FORMANID and EVENTID = :EVENTID")
                                                    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                                    
                                                    oci_bind_by_name($qry, ":ASSIGNLOC",  $lLoc, -1);
                                                    oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
                                                    oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
                                                    
                                                    oci_execute($qry);
                                                    
                                                    
                                                    
                                                    
                                                    
            }                                                  
                                                    
              $qry = oci_parse($c, "insert into WEMS_CLEANABLE_TARGET_NOTES (EVENTID, CTID, CTNOTES, FORMANID, CTSTATUS, CTPASSNUM, CTBAGS, CTSTARTTIME, CTNOTEUSER, ENTER_DATETIME, MARKERID, CREWSIZE)
                                          VALUES(:EVENTID, :CTID, :CTNOTES, :FORMANID, :CTSTATUS, :CTPASSNUM, :CTBAGS, to_date(:CTSTARTTIME, 'mm/dd/yyyy hh:mi AM'), :CTNOTEUSER, SYSDATE, :LOC, :CREWSIZE) ")
                                                                                              OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                                                                               
                                                    
                                          oci_bind_by_name($qry, ":EVENTID",  $eventID, -1);
                                          oci_bind_by_name($qry, ":CTID",  $lConponent, -1);
                                          oci_bind_by_name($qry, ":CTNOTES", $lcomments , -1);
                                          oci_bind_by_name($qry, ":FORMANID", $lForman, -1);
                                          oci_bind_by_name($qry, ":CTSTATUS",  $lStatus, -1);
                                          oci_bind_by_name($qry, ":CTPASSNUM",  $lPassNum, -1);
                                          oci_bind_by_name($qry, ":CTBAGS", $lNumBags , -1);
                                          oci_bind_by_name($qry, ":CTSTARTTIME", $lNoteTime, -1);
                                          oci_bind_by_name($qry, ":CTNOTEUSER", $lUser, -1);
                                          oci_bind_by_name($qry, ":LOC", $lLoc, -1);
                                          oci_bind_by_name($qry, ":CREWSIZE", $crewsize, -1);
                                          
                                          oci_execute($qry);
                                                                                               
                                                    
              
                                          
                                          $LocisAssignedAlready = "";
                                          
                                          
                                          
                                         
                                          
         $locCnt = 0;
          
            $qry = oci_parse($c, "select count(*) CNT from WEMS_CLEANABLE_TARGET WHERE MARKERID = :MARKERID and ASSIGNED_SITEFOREMEN != 0")
                                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
            
            
            
                                    oci_bind_by_name($qry, ":MARKERID",  $lLoc, -1);
                                                        
                                                        
                                    oci_execute($qry);
            
                                    while($row = oci_fetch_array($qry))
                                    {
                                        $locCnt = $row['CNT'];
                                    }
            
            if($locCnt == 0)
            {
                
                $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = NULL WHERE EVENTID = :EVENTID AND ASSIGN_LOC = :ASSIGN_LOC ")
                OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                
                
                oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
                oci_bind_by_name($qry, ":ASSIGN_LOC",  $lLoc, -1);
                
                oci_execute($qry);
                
                
                
            }
                     
        } // end else                
       
        $addGangNoteQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, FORMANID, NOTETIME, NOTEUSER, EVENTUPDATE, ENTER_DATETIME, ASSIGN_LOC)
                                          VALUES(:EVENTID, :FORMANID, sysdate, :NOTEUSER, 'Gang Assigned', sysdate, :ASSIGN_LOC ) ")
        					                                          OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        					                                           
        					
        					  oci_bind_by_name( $addGangNoteQry, ":EVENTID",  $eventID, -1);
        					  oci_bind_by_name( $addGangNoteQry, ":FORMANID", $lForman, -1);
        					  oci_bind_by_name( $addGangNoteQry, ":NOTEUSER", $lUser, -1);
                              oci_bind_by_name( $addGangNoteQry, ":ASSIGN_LOC",  $lLoc, -1);
                              
                              //The Forman is now assigned to a Location and then a conponent. To find out what conponant see ceanable targets
        					      					          					                                           
        					  oci_execute($addGangNoteQry);
        					

        					
        					$tabindex = 0;
        					
        					include 'updateLocStatus.php';
        					
        					updateLoc($lLoc, $eventID);
        					
}
        					

?>