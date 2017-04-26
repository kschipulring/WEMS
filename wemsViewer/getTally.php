<?php 
/*
$locationObj = new location();
$locationStates = $locationObj->getLocationCleanStates();

//when called via json
if ( basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) {
	echo json_encode( $locationStates );
} else {
	//when this is called like as a php include
	extract($locationStates);
}
*/

require '../wemsDatabase.php';

$c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

//now load classes easily without worrying about including their files
spl_autoload_register(function ($class) {
    include_once "../classes/{$class}Class.php";
});

    function dashesToCamelCase($string, $capitalizeFirstCharacter = false){
        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }

    //$states = array("dirty", "inProgress", "halfClean", "clean");
    $states = array("dirty", "in_progress", "half_clean", "clean");
    $shapes = array("s" => "circle", "i" => "triangle", "p" => "rect");

    $locationStates = array();

    $superQryStr = "select DISTINCT \n";
    $superQryArr = array();

    for($i=0; $i<count($states); $i++){
        $ii = $i + 1;
        $iLabel = $states[$i];

        foreach($shapes as $key=>$value){
            $uk = strtoupper($key);

            $superQryArr[] = " (SELECT count(DISTINCT l.MARKERID) from " . config::LOCATION_TABLE . " l
		left join " . config::CLEANABLE_TARGET_TABLE . " ct on l.MARKERID = ct.MARKERID
		where l.LOC_CD = '{$uk}' AND ct.TYPE = l.LOC_CD AND l.STATUS = {$ii} ) AS " . $key . "_" . ucwords($states[$i]);
        }
    }

    $superQryStr .= implode(",\n", $superQryArr);

    $superQryStr .= " from " . config::LOCATION_TABLE;

    $parse = oci_parse($c, $superQryStr)
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

    oci_execute($parse);

    while( ($row = oci_fetch_array($parse)) != false ){
        foreach($row as $k=>$v){
            if( gettype($k) === "string" ){
                $nk = dashesToCamelCase( strtolower($k) );
                	
                $locationStates[$nk] = $v;
            }
        }
    }

    ksort($locationStates);

    //when called via json
    if ( basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) {
        echo json_encode( $locationStates );
    } else {
        //when this is called like as a php include
        extract($locationStates);
    }