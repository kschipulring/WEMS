 <?php 
 ob_end_clean();
 //ob_end_flush();

 
 

 
  require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $MARKERID = isset($_GET['markerid']) ? $_GET['markerid'] : -1;
    //$eventID = isset($_GET['eventId']) ? $_GET['eventId'] : -1;
    $locNote = "";
   
    //$MARKERID = 61100300;
       
          
    $qry = oci_parse($c, "select ct.ASSIGNED_SITEFOREMEN as EMP, s.STATUS, ct.CT_BAGS, ct.FULLNAME, ct.CT_PASSNUM
                            from WEMS_CLEANABLE_TARGET ct, WEMS_LOCATION_STATUS s
                            where MARKERID = :MARKERID and ct.CT_STATUS in s.STATUSID")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
     
    oci_bind_by_name($qry, ":MARKERID", $MARKERID, -1);
     
    oci_execute($qry);
    
    
    $qry3 = oci_parse($c, "select LOC_NOTE from WEMS_LOCATION where MARKERID = :MARKERID")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
     
    oci_bind_by_name($qry3, ":MARKERID", $MARKERID, -1);
     
    oci_execute($qry3);
    
    while($row2 = oci_fetch_array($qry3))
    {
        $locNote .= $row2['LOC_NOTE'];
        //$locNote = "TEST";
    }
    
    
    //ob_start();
    $json = "[";
     
    
    
 while($row = oci_fetch_array($qry))
    {
    
        $foreman = $row['EMP'];
        $status = $row['STATUS'];
        $bags = $row['CT_BAGS'];
        $conponentName = $row['FULLNAME'];
        $passNum = $row['CT_PASSNUM'];
        $dept = "";
        
        
        if($foreman != "")
        {
            $qry2 = oci_parse($c, "select e.FST_NME, e.LST_NME, d.DEPT_NAME from WEMS_EMPLOYEE e, WEMS_DEPT d where d.dept_NUM = e.DEPTCODE and e.EMPLOYEENUMBER = :FOREMAN")
                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                                         
                                        oci_bind_by_name($qry2, ":FOREMAN", $foreman, -1);
                                         
                                        oci_execute($qry2);
                                        
                                        while($row = oci_fetch_array($qry2))
                                        {
                                            $foreman = $row['FST_NME'] . " " . $row['LST_NME'];
                                            
                                            $dept = $row['DEPT_NAME'];
                                        }
                                        
                                        
                                        
            
        } 
        
        
        
        
        
         $json .= "{\"FOREMAN\": \"$foreman\",\"DEPT\": \"$dept\",\"STATUS\": \"$status\",\"BAGS\": \"$bags\",\"FULLNAME\": \"$conponentName \",\"PASSNUM\": \"$passNum\",\"NOTE\": \"$locNote\"},";
         
    }
    //$json .= "{\"CTID\": \"99999999\",\"FULLNAME\": \"All Conponents\"},";
    
    $json .= "]";
    $json .= "~";
   // ob_end_clean();
  
    
    
    echo $json;
    
    
    oci_close($c);
    
    
    /*
    $json = "[";
    $locNote = "";
    $qry3 = oci_parse($c, "select LOC_NOTE from WEMS_LOCATION where MARKERID = :MARKERID")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
     
    oci_bind_by_name($qry3, ":MARKERID", $MARKERID, -1);
     
    oci_execute($qry3);
    
    while($row2 = oci_fetch_array($qry3))
    {
        $locNote .= $row2['LOC_NOTE'];
         $json .= "{\"NOTE\": \"$locNote\"},";
    }
    $json .= "{\"NOTE\": \"$locNote\"},";
    $json .= "]";
   echo $json;
    oci_close($c);
    */
    ?>
    
        