 <?php 
 require '../classes/databaseClass.php';
 require '../classes/cleanableTargetClass.php'; 
 $json = ''; 
 if(isset( $_GET['eventId'] )) {
     $locationDetail = array();
     $ctnObj = new cleanableTarget();     
     $result = $ctnObj->getCleanableTargetByEventId($_GET['eventId']);     
     if($result){
         $json = "[";
         $json .= "{\"MARKERID\": \"0\",\"MARKERNAME\": \"\"},";
         while($row = oci_fetch_array($result[0])){
             $locationDetail = $row;
             $json .= "{\"MARKERID\": \"$row[MARKERID]\",\"MARKERNAME\": \"$row[MARKERNAME]\"},";
         }
         $json .= "]";
     }
 }
 echo $json; 
?>
