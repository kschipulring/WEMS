<?php 
  
  require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
    
   // $loc = isset($_GET['loc']) ? $_GET['loc'] : -1;
    //$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
    //$conponent = isset($_GET['conponent']) ? $_GET['conponent'] : -1;
   
    
    $ASSIGNED_SITEFORMEN = "";
    
    
       
        $qry2 = oci_parse($c, "select ASSIGNED_SITEFOREMEN from WEMS_CLEANABLE_TARGET WHERE MARKERID = :MARKERID and CTID = :CTID")
            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

        oci_bind_by_name($qry2, "MARKERID",  $lLoc, -1);
        oci_bind_by_name($qry2, "CTID", $lConponent, -1);
    
        oci_execute($qry2);
    
        while($row = oci_fetch_array($qry2))
        {
            $ASSIGNED_SITEFORMEN = $row['ASSIGNED_SITEFOREMEN'];
        }

    
    

    
	    $qry = oci_parse($c, "select g.FORMANID, e.FST_NME, e.LST_NME, g.ASSIGN_LOC from WEMS_GANG g, WEMS_EMPLOYEE e where g.EVENTID = :EVENTID and g.FORMANID = e.EMPLOYEENUMBER ")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                  
                                   oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

                                   oci_execute($qry);


     

          $json = "[";
         
         				$json .= "{\"FORMANID\": \"0\",\"NAME\": \"\",\"LOCATION\": \"\"},";
         					while($row = oci_fetch_array($qry))
         					{
         					    $assign_loc = $row['ASSIGN_LOC'];
         					    $forman = $row['FORMANID'];
         					    
         					   if(($lLoc == $assign_loc) or ($assign_loc ==""))
         					    {
         					        if($ASSIGNED_SITEFORMEN == $forman)
         					        {
         					            $json .= "{\"FORMANID\": \"$forman\",\"NAME\": \"$row[NAME]\",\"LOCATION\": \"$lConponent\"},";
         					        }
         					        else 
         					        {
         					            $json .= "{\"FORMANID\": \"$forman\",\"NAME\": \"$row[NAME]\",\"LOCATION\": \"\"},";
         					        }
         					         
         					    }
         
         					}
         								 
         $json .= "]";

     
       

      



   


 echo $json;


  oci_close($c);

?>
