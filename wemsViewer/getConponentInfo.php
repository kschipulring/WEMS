<?php 
require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $LocId = isset($_GET['param']) ? $_GET['param'] : -1;
    
    

     if($LocId >= 0)
     {

         
         $qry = oci_parse($c, "SELECT CTID, FULLNAME FROM WEMS_CLEANABLE_TARGET where MARKERID = :MARKERID and TYPE = 'S'")
         		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
         
         				oci_bind_by_name($qry, ":MARKERID", $LocId, -1);
         								
         				oci_execute($qry);
         								
         				$json = "[";
         
         				$json .= "{\"CTID\": \"0\",\"FULLNAME\": \"\"},";
         					while($row = oci_fetch_array($qry))
         					{

         					    $json .= "{\"CTID\": \"$row[CTID]\",\"FULLNAME\": \"$row[FULLNAME]\"},";
         
         					}
         					//$json .= "{\"CTID\": \"99999999\",\"FULLNAME\": \"All Conponents\"},";
         								 
         				$json .= "]";
     }

     

  echo $json;


  oci_close($c);

?>

