<?php

/*
//whether if this is a direct call
if ( basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) {
	//now load classes easily without worrying about including their files
	spl_autoload_register(function ($class) {
		include_once "../classes/{$class}Class.php";
	});
	
	$loc = isset($_GET['loc']) ? $_GET['loc'] : -1;
	$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
	$conponent = isset($_GET['conponent']) ? $_GET['conponent'] : -1;
	
	$gangObj = new gang();
	$ggl = $gangObj->getGangList($loc, $eventID, $conponent);
	
	echo json_encode( $ggl );
}
*/


 require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
    
    $loc = isset($_GET['loc']) ? $_GET['loc'] : -1;
    $eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
   // $conponent = isset($_GET['conponent']) ? $_GET['conponent'] : -1;
   
    
    $ASSIGNED_SITEFORMEN = "";
    
    
       
        
     /*   
        
        $qry2 = oci_parse($c, "select ASSIGNED_SITEFOREMEN from WEMS_CLEANABLE_TARGET WHERE MARKERID = :MARKERID and CTID = :CTID")
            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

        oci_bind_by_name($qry2, "MARKERID",  $loc, -1);
        oci_bind_by_name($qry2, "CTID", $conponent, -1);
    
        oci_execute($qry2);
    
        while($row = oci_fetch_array($qry2))
        {
            $ASSIGNED_SITEFORMEN = $row['ASSIGNED_SITEFOREMEN'];
        }

    
    

    
	    $qry = oci_parse($c, "select g.FORMANID, e.lst_NME, g.ASSIGN_LOC from WEMS_GANG g, WEMS_EMPLOYEE e where g.EVENTID = :EVENTID and g.FORMANID = e.EMPLOYEENUMBER order by e.LST_NME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                  
                                   oci_bind_by_name($qry, ":EVENTID", $eventID, -1);

                                   oci_execute($qry);


    */
    
    $qry = oci_parse($c, "select g.FORMANID, e.LST_NME, g.ASSIGN_LOC from WEMS_GANG g, WEMS_EMPLOYEE e where g.EVENTID = :EVENTID
and g.FORMANID = e.EMPLOYEENUMBER and ((g.ASSIGN_LOC is null) or (g.ASSIGN_LOC = :LOC)) order by e.LST_NME")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    
    oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
    oci_bind_by_name($qry, ":LOC", $loc, -1);
    
    oci_execute($qry);

          $json = "[";
         
         				$json .= "{\"FORMANID\": \"0\",\"NAME\": \"\",\"LOCATION\": \"\"},";
         					while($row = oci_fetch_array($qry))
         					{
         					    $assign_loc = $row['ASSIGN_LOC'];
         					    $forman = $row['FORMANID'];
         					    
         					  // if(($loc == $assign_loc) or ($assign_loc ==""))
         					  //  {
         					  //      if($ASSIGNED_SITEFORMEN == $forman)
         					  //      {
         					            $json .= "{\"FORMANID\": \"$forman\",\"NAME\": \"$row[LST_NME]\",\"LOCATION\": \" $assign_loc\"},";
         					  //      }
         					  //      else 
         					  //      {
         					           // $json .= "{\"FORMANID\": \"$forman\",\"NAME\": \"$row[LST_NME]\",\"LOCATION\": \"\"},";
         					  //      }
         					         
         					  //  }
         
         					}
         								 
         $json .= "]";

     
       

      



   


 echo $json;


  oci_close($c);