 <?php 
  
  require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $MARKERID = isset($_GET['markerid']) ? $_GET['markerid'] : -1;
    //$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
    
    
    //$MARKERID = 61100300;
       
          
    $qry = oci_parse($c, "select ct.ASSIGNED_SITEFOREMEN as EMP, s.STATUS, ct.CT_BAGS, ct.FULLNAME, ct.CT_PASSNUM
                            from WEMS_CLEANABLE_TARGET ct, WEMS_LOCATION_STATUS s
                            where MARKERID = :MARKERID and ct.CT_STATUS in s.STATUSID")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
     
    oci_bind_by_name($qry, ":MARKERID", $MARKERID, -1);
     
    oci_execute($qry);
     
    $json = "[";
     
   
    while($row = oci_fetch_array($qry))
    {
    
        $foreman = $row['EMP'];
        $status = $row['STATUS'];
        $bags = $row['CT_BAGS'];
        $conponentName = $row['FULLNAME'];
        $passNum = $row['CT_PASSNUM'];
        
        if($foreman != "")
        {
            $qry2 = oci_parse($c, "select NAME from EMPLOYEE where EMPLOYEEID = :FOREMAN")
                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                         
                                        oci_bind_by_name($qry2, ":FOREMAN", $foreman, -1);
                                         
                                        oci_execute($qry2);
                                        
                                        while($row = oci_fetch_array($qry2))
                                        {
                                            $foreman = $row['NAME'];
                                        }
        }
        
        
        
        
        
        
        $json .= "{\"FOREMAN\": \"$foreman\",\"STATUS\": \"$status\",\"BAGS\": \"$bags\",\"FULLNAME\": \"$conponentName \",\"PASSNUM\": \"$passNum\"},";
         
    }
    //$json .= "{\"CTID\": \"99999999\",\"FULLNAME\": \"All Conponents\"},";
    
    $json .= "]";
    
    
     
    
    echo $json;
    
    
    oci_close($c);
    
    ?>
    
        