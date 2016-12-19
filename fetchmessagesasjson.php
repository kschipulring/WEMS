<?php

require('../database.php');

function jsonescape($str) {
  $temp =  str_replace("\\", "\\\\", $str);
  $temp =  str_replace("\"", "\\\"", $temp);
  $temp =  str_replace("/", "\/", $temp);

  return $temp;
}

$loc = isset($_GET['loc']) ? $_GET['loc'] : 'NYK';
$system = 'MB_' . $loc;


$link = oci_pconnect ($dbusername, $dbpassword, $database)  
     or die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

$json = "[";

  $sql = "select b.color, b.branch, 
                 bm.sys_datetime, bm.start_datetime, bm.end_datetime, bm.info 
          from 
                 branches b, board_messages_branch bmb, board_messages bm, board_messages_system bms
          where 
                 b.branch = bmb.branch and
                 bmb.sys_datetime = bm.sys_datetime and
                 bm.mod_datetime is null and
                 bm.deleted_datetime is null and
                 bms.sys_datetime = bm.sys_datetime and
                 bms.system = :SYSTEM and
                 bm.msg_type = 'A'
                 and concat(bm.sys_datetime, b.branch) in (
                     select concat(max(b.sys_datetime), b.branch) 
                     from board_messages_branch b, board_messages bm, 
                     board_messages_system s
                     where
                     b.sys_datetime = s.sys_datetime and
                     b.sys_datetime = bm.sys_datetime
                     and
                     s.system = :SYSTEM2
                     and
                     bm.msg_type = 'A'
                     group by b.branch
                 ) order by bm.SORT_ORDER, bmb.sys_datetime";


$query = oci_parse($link, $sql)
     or die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($link), 1) . '</pre>');

oci_bind_by_name($query, ":SYSTEM",  $system, -1);
oci_bind_by_name($query, ":SYSTEM2", $system, -1);
  
oci_execute($query)
     or die('Oracle error with execute. Error: <pre>' . print_r(oci_error($query), 1) . '</pre>');

  
$sysdatetime = "";
while($result = oci_fetch_array($query)) {

  $color  = $result['COLOR'];
  $branch = $result['BRANCH'];
  $info = preg_replace('/\r*\n *\t*\r*\n/', "<div class=\"spacer\"></div>", htmlspecialchars($result['INFO']));
  $info = preg_replace('/\r*\n/', "<br />", $info);
 
  
  $branch = jsonescape($branch);
  $info = jsonescape($info);

  if($sysdatetime != $result['SYS_DATETIME']) { //new item
    if(strlen($json) > 1) // the 1 is the opening ']'
      $json .= "]},";

    $json .= "{\"type\": \"A\", \"sysdate\":  \"" . $result['SYS_DATETIME'] . "\"" .
      "," . "\"message\": \"" . $info . "\"" .
      "," . "\"branches\": [";
    $json .= "{\"branch\": \"" . $branch . "\", \"color\": " . "\"" . $color ."\"}"; 
  }
  else {
    $json .= ", {\"branch\": \"" . $branch . "\", \"color\": " . "\"" . $color ."\"}"; 
  }
  
  $sysdatetime = $result['SYS_DATETIME'];

}

if(strlen($json) > 1) // the 1 is the opening ']'
  $json .= "]}";
else { // no alerts load the informational messages
  $sql = "select b.color, b.branch, bm.header,
                 bm.sys_datetime, bm.start_datetime, bm.end_datetime, bm.info 
          from 
                 branches b, board_messages_branch bmb, board_messages bm, board_messages_system bms
          where 
                 b.branch = bmb.branch and
                 bmb.sys_datetime = bm.sys_datetime and
                 bm.mod_datetime is null and
                 bm.deleted_datetime is null and
                 bms.sys_datetime = bm.sys_datetime and
                 bms.system = :SYSTEM and
                 bm.msg_type = 'I' and
                 bm.start_datetime <= sysdate and
                 bm.end_datetime >= sysdate
                 order by bm.SORT_ORDER, bmb.sys_datetime";

  $query = oci_parse($link, $sql)
    or die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($link), 1) . '</pre>');
  
  oci_bind_by_name($query, ":SYSTEM",  $system, -1);
  
  oci_execute($query)
    or die('Oracle error with execute. Error: <pre>' . print_r(oci_error($query), 1) . '</pre>');
  
  while($result = oci_fetch_array($query)) {
    $color  = $result['COLOR'];
    $branch = $result['BRANCH'];
    $header = isset($result['HEADER']) ? $result['HEADER'] : "";

    $info = preg_replace('/\r*\n *\t*\r*\n/', "<div class=\"spacer\"></div>", htmlspecialchars($result['INFO']));
    $info = preg_replace("/\r*\n/", "<br />", $info);
    //    $info   = str_replace("\n", "<br />", htmlspecialchars($result['INFO']));

    $info = jsonescape($info);
    $branch = jsonescape($branch);
  
    if($sysdatetime != $result['SYS_DATETIME']) { //new item
      if(strlen($json) > 1) // the 1 is the opening ']'
	$json .= "]},";

      $json .= "{\"type\": \"I\", \"sysdate\":  \"" . $result['SYS_DATETIME'] . "\"" .
	"," . "\"message\": \"" . $info . "\"" .
	"," . "\"branches\": [";
      $json .= "{\"branch\": \"" . $branch . "\", \"color\": " . "\"" . $color ."\", \"header\": " . "\"" . $header . "\"}"; 
    }
    else {
      $json .= "{\"branch\": \"" . $branch . "\", \"color\": " . "\"" . $color ."\", \"header\": " . "\"" . $header . "\"}"; 
    }
    
    $sysdatetime = $result['SYS_DATETIME'];
  }
  if(strlen($json) > 1) // the 1 is the opening ']'
    $json .= "]}";

}


$json .= "]";

oci_close($link);
 
print($json);

?>
