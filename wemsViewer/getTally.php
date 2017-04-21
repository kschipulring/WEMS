<?php 
$locationObj = new location();
$locationStates = $locationObj->getLocationCleanStates();

//when called via json
if ( basename(__FILE__) == basename($_SERVER["SCRIPT_FILENAME"]) ) {
	echo json_encode( $locationStates );
} else {
	//when this is called like as a php include
	extract($locationStates);
}
