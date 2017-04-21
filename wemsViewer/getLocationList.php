 <?php 
 //now load classes easily without worrying about including their files
 spl_autoload_register(function ($class) {
 	include_once "../classes/{$class}Class.php";
 });
 
 $json = ''; 
 if(isset( $_GET['eventId'] )) {
     $locationDetail = array();
     $ctnObj = new cleanableTarget();
     $result = $ctnObj->getCleanableTargetByEventId($_GET['eventId']);     
     if($result){
         $json = "[";
         $json .= "{\"MARKERID\": \"0\",\"MARKERNAME\": \"\"},";
         while( ($row = oci_fetch_array($result[0])) !== false ){
             $locationDetail = $row;
             $json .= "{\"MARKERID\": \"$row[MARKERID]\",\"MARKERNAME\": \"$row[MARKERNAME]\"},";
         }
         $json .= "]";
     }
 }
$json = str_replace(",]", "]", $json);
 echo $json; 
?>
