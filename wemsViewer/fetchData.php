<?php

	$dbusername = "e_wmds_dev";
        $dbpassword = "wayside";
        $database   = "WMDS";

      $link = oci_pconnect ($dbusername, $dbpassword, $database)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $link = oci_pconnect ($dbusername, $dbpassword, $database)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

	function jsonescape($str) {
  		$temp =  str_replace("\\", "\\\\", $str);
  		$temp =  str_replace("\"", "\\\"", $temp);
  		$temp =  str_replace("/", "\/", $temp);

  		return $temp;
	}


	

	$json = "{\"latlon\": ["; 


$query = oci_parse($link, "select  
			to_number(wmds_lirr_pkg.convertLatLong(hb.LATITUDE)) latitude , 
			to_number(wmds_lirr_pkg.convertLatLong(hb.longitude)) longitude,
			EDF.SHORT_DESCRIPTION, EDF.CODE, to_char(f.lc_last_set_timestamp, 'MM-DD-YY HH12:MI:SS') as TIMESTAMP
			from 
			e_wmds_dev.wmds_rsms_fault_logs f, e_wmds_dev.wmds_cars c   , 
			e_wmds_dev.wmds_nodes n  , e_wmds_dev.WMDS_EDF_FAULT_DEFINITIONS EDF  , 
			e_wmds_dev.wmds_train_heartbeats hb  
			where 
			f.last_update_date > sysdate - 15/60/24 
			and f.lc_last_set_timestamp >= trunc(sysdate)  and f.lc_last_set_timestamp+0 < sysdate + 3    
			and c.car_id = f.car_id  and n.nod_id = f.nod_id  and edf.edf_id = f.edf_id  
			AND hb.longitude <> 0  AND hb.latitude <> 0 and 
			hb.thb_id(+) = wmds_lirr_pkg.getClosestHeartbeatId(f.train_nbr, f.last_set_timestamp)")
         OR die('Oracle error, in parse. Error: ' . print_r(oci_error($link), 1)); 
 
  
       oci_execute($query)
         OR die('Oracle error with execute. Error: ' . print_r(oci_error($query), 1));

      
	   
       while($result = oci_fetch_array($query))
       {
         //$REFID = $result['MAX(REF_ID)'];
		 
		  	$lat = $result['LATITUDE'];
			$lon = $result['LONGITUDE'];
			$desc = $result['SHORT_DESCRIPTION'];
			$code = $result['CODE'];
			$dteTm = $result['TIMESTAMP'];
			//alert($desc);
			
			//$desc = str_replace(chr(13).chr(10), "<br/>", htmlspecialchars($result['DESCRIPTION']));
			
			//$desc = stripslashes($desc);
			
			//$desc = "TEST";			
 			//$json .= "{\"lat\": \"" . $lat . "\", \"lon\": " . "\"" . $lon . "\"},";
			
         	$json .= "{\"lat\": \"" . $lat . "\", \"lon\": " . "\"" . $lon . "\", \"desc\": " . "\"" . $desc . "\", \"code\": " . "\"" . $code . "\", \"dteTm\": " . "\"" . $dteTm . "\"},";
			//(     , \"lon\": " . "\"" . $lon . "\"     ) use whats in backets to add feilds
			
			
			
       } 


		$json .= "]}";
		
		



oci_close($link);



 
print($json);


?>
