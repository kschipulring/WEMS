<?php 
session_start();

// error and success msg rows
function getErScRows($erMsg, $scMsg, $colSpan = 6){
	$outStr = "";
	if (strlen($erMsg)) {
		$outStr.= "<tr><td colspan = \"$colSpan\" align = \"center\" bgcolor=\"#FF0000\" > $erMsg </td></tr>";
	}
	if (strlen($scMsg)) {
		$outStr.= "<tr><td colspan = \"$colSpan\" align = \"center\" bgcolor=\"#00FF00\" > $scMsg </td></tr>";
	}
	return $outStr;
}

function rrLocationMenu($pre, $loc){
	global $locationObj;
	
	$dbType = utilities::getDBtype($pre);
	
	$outStr = "<select class='input-box' name='{$pre}Loc' id='{$pre}Loc' onchange='getConponentData(\"$pre\"); getEmployees(\"$pre\");'>";
	$outStr .= "<option value='0'>  </option>";
	
	$glt = $locationObj->getLocationsByType($pre, $loc);
	
	for( $i=0; $i<count($glt); $i++ ){
		$row = $glt[$i];
	
		$isSelected = "";
	
		if( $row["SELECTED"] == true ){
			$isSelected = "selected=\"selected\"";
		}
	
		$outStr .= "<option value=\"$row[MARKERID]\" $isSelected> $row[DESC] </option>";
	}
	
	$outStr .= "</select>";
	
	return $outStr;
}

function rrComponentFields($pre, $component, $loc){
	global $locationObj;
	
	$dbType = utilities::getDBtype($pre);
	
	$outStr = "<select class='input-box' name='{$pre}Conponent' id='{$pre}Conponent' onchange='getConponentDetails(\"$pre\"); getEmployees(\"$pre\");'>";
	
	//NOTE: $gct does NOT represent 'Grand Central Terminal'
	$gct = $locationObj->getCleanableTargetsByType($pre, $component, $loc);
	
	for( $i=0; $i<count($gct); $i++ ){
		$row = $gct[$i];
	
		$isSelected = "";
	
		if( $row["SELECTED"] == true ){
			$isSelected = "selected=\"selected\"";
		}
	
		$outStr .= "<option value=\"$row[CTID]\" $isSelected> $row[FULLNAME] </option>";
	}
	
	$outStr .= "</select><input type='checkbox' name='allConponents' value='allConponents' id='{$pre}AllConponents' /> Apply to all conponents <br/>";
	
	return $outStr;
}

function gangAssignMenu($pre, $conponent, $loc){
	global $gangObj, $eventID;
	
	$out = "<select class='input-box' name='{$pre}Forman' id='{$pre}Forman'>";
	$out .= "<option value= \"\" >  </option>";
	
	//Now it is in seperate functions :)
	if(!empty($conponent) && $conponent != ""){
		$ggl = $gangObj->getGangList($loc, $eventID, $conponent);

		for( $i=0; $i<count($ggl); $i++ ){
			$row = $ggl[$i];
			
			$isSelected = "";
			
			if( $row["LOCATION"] > 0 ){
				$isSelected .= "selected=\"selected\"";
			}
			
			$out .= "<option value= \"$row[FORMANID]\" $isSelected> $row[NAME] </option>";
		}
	}

	$out .= "</select>";
	
	return $out;
}

function statusMenu($pre, $status){
	global $locationObj;
	
	$gls = $locationObj->getLocationStatuses($status);
	
	$outStr = "<select name='{$pre}Status' id='{$pre}Status' class='input-box'>";
	
	for( $i=0; $i<count($gls); $i++ ){
		$row = $gls[$i];
	
		$isSelected = "";
		
		if( $row["SELECTED"] == true ){
			$isSelected = "selected=\"selected\"";
		}
		
		$outStr .= "<option value=\"$row[ID]\" $isSelected> $row[DESC] </option>";
	}

	$outStr .= "</select>";
	
	return $outStr;
}

//for the common date and time fields
function dateTimeFields($pre, array $timeData){
	//$hour, $min, $amPm are the parts of the time object
	extract($timeData, EXTR_PREFIX_SAME, "td_");
	
	$startTm = ( !empty($startTm) && strlen($startTm) > 0 )? $startTm : date('m/d/Y');
	
	$startTmNm = ( !empty($startTmNm) && strlen($startTmNm) > 0 )? $startTmNm : $pre . "StartTm";
	
	//id for the calendar icon by datepicker
	$calNm = ( !empty($calNm) && strlen($calNm) > 0 )? $calNm : $pre . "StartTmPick";
	
	//datepicker
	$outStr = <<<EOD
	<input readonly="readonly" type="text" name="{$startTmNm}" size="20" tabindex="24" id="{$startTmNm}" value="{$startTm}" class='input-box' />
	<img src="cal.gif" width="16" border="0" id="{$calNm}" alt="Click here to pick date" />  
EOD;
	
	//hour box
	$outStr .= "<select name=\"{$pre}StartHr\" id=\"{$pre}StartHr\" class='input-box'>";
	for ($x = 1; $x <= 12; $x++) {
		$tempSel = (!empty($hour) && trim($hour) == $x) ? "selected" : "";
	
		$outStr .= "<option value= \"$x\" $tempSel> $x </option>";
	}
	$outStr .= "</select>";
	
	//minute box
	$outStr .= " : <select name=\"{$pre}StartMin\" id=\"{$pre}StartMin\" class='input-box'>";
	for ($x = 0; $x <= 59; $x++) {
		if($x < 10) $x = "0".  $x;
	
		$tempSel = (!empty($min) && trim($min) == $x) ? "selected" : "";
		$outStr .= "<option value= \"$x\" {$tempSel}> $x </option>";
	}
	$outStr .= "</select>";
	
	//am and pm box
	$outStr .= "<select name='{$pre}AmPm' id='{$pre}AmPm' class='input-box'>";
	$outStr .= "<option value='0' " . ((!empty($amPm) && trim($amPm) == "0") ? "selected" : "") . ">AM</option>";
	$outStr .= "<option value='1'" . ((!empty($amPm) && trim($amPm) == "1") ? "selected" : "") . ">PM</option>";
	$outStr .= "</select>";

	return $outStr;
}

function downloadFields($pre, $component, $bType="CTID"){
	global $eventID, $locationObj;

	$outStr = "<select name='{$pre}DownloadFile' id='{$pre}DownloadFile'><option value='0' selected='selected'>  </option>";

	$gdf = $locationObj->getDownloadFields($eventID, $component, $bType);
	
	for( $i=0; $i<count($gdf); $i++ ){
		$row = $gdf[$i];
	
		$outStr .= "<option value= \"$row[ID]\" > $row[ID] </option>";
	}

	$outStr .= "</select><input class='Download' type='submit' value='Download' name='SUBMIT' id='{$pre}_DL_SUBMIT' />";

	return $outStr;
}

function historyTextarea($pre, $ctId){
	global $eventID, $locationObj;

	$outStr = "<textarea rows='10' cols='100' name='{$pre}History' id='{$pre}History' class='input-box'>";

	$glh = $locationObj->getLocationHistory($pre, $eventID, $ctId);
	
	$outStr .= join('', $glh);
	
	$outStr .= "</textarea>";
	
	return $outStr;
}

function locationSubmitRow($pre, $loc, $labelSuffix){
	global $eventID, $locationObj;
	
	$outStr = "";
	
	if ($eventID > 0 && ( $_SESSION['group'] == "WEMS_Admin" || $_SESSION['group'] == "WEMS_Write" ) ){
		$buttonStr = "Assign";
		
		//if this location currently has a gang assigned to it.
		if( is_numeric($loc) && $locationObj->getLocationIfAssigned($loc, $eventID) == 1 ){
			$buttonStr = "Update";
		}
			
		$outStr = "<tr><td colspan = \"2\" style=\"text-align:center\">
		<input type=\"hidden\" name=\"task\" value=\"{$labelSuffix}\" />
		<input class=\"wideGreenBtn\" type=\"submit\" value=\"{$buttonStr} {$labelSuffix}\" name=\"SUBMIT\" id=\"{$pre}SUBMIT\" />
		</td></tr>";
	}
	
	return $outStr;
}

//now load classes easily without worrying about including their files
spl_autoload_register(function ($class) {
	include_once "../classes/{$class}Class.php";
});

ob_start();
set_include_path(get_include_path() . PATH_SEPARATOR . "/usr/local/zend/var/libraries/tcpdf/6.2.12");
set_include_path(get_include_path() . PATH_SEPARATOR . "/usr/local/zend/var/libraries/LIRR/1.0.4.3");
$library_path = '../lib/';
set_include_path(get_include_path() . PATH_SEPARATOR . $library_path);

$eventErrMsg = "";
$gangErrMsg = "";
$locationErrMsg = "";
$locationSuccessMsg = "";
$eventSuccessMsg = "";
$gangSuccessMsg = "";
$interlockingErrMsg = "";
$locationSuccessMsg = "";
$parkingLotErrMsg = "";
$parkingLotSuccessMsg = "";
$signalErrMsg = "";
$signalSuccessMsg = "";
$lStatus = "";
$gStartTm = "";
$lLoc = "";
$tabindex = 0;

// missing vars newly added
$gEmpNum = 0;
$lPassNum = 0;
$iStatus = 0;
$returnPage = "eventMaint.php";
$forman = 0;
$supportDoc = "";
$isupportDoc = "";
$plsupportDoc = "";
$siSupportDoc = "";

$lLoc = "";
$iLoc = "";
$plLoc = "";
$siLoc = "";

$inactive = 2400; //600 = 10 min

$lConponent="";
$iConponent="";
$plConponent="";
$siConponent="";


if (isset($_SESSION['timeout'])) {
	$session_life = time() - $_SESSION['timeout'];
	if ($session_life > $inactive) {
		session_destroy();
		header("Location: login.php?returnPage=$returnPage");
	}
}
$_SESSION['timeout'] = time();

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false) {
	header("Location: login.php?returnPage=$returnPage");
}

if(isset($_POST['Logout'])) { // logout
	session_destroy();
	header("Location: login.php?returnPage=$returnPage");
}

$groups = config::$approvedGroups;

if (!in_array($_SESSION['group'], $groups)){
	session_destroy();
	header("Location: login.php?returnPage=$returnPage");
}else{
	require '../wemsDatabase.php';
	require_once('tcpdf.php');
	/*require_once '../classes/databaseClass.php';
	require_once '../classes/eventClass.php';
	require_once '../classes/gangClass.php';
	require_once '../classes/locationClass.php';
	require_once '../classes/cleanableTargetClass.php';*/
	
	/*
	global object instances. NOTE, classes, if their definitions are stored within the '../classes/' directory
	AND follow the naming convention of like '(someClassName)Class.php', then they can be directly referenced throughout this file
	*/
	$gangObj = new gang();
	$locationObj = new location();

	$c = oci_pconnect($wemsDBusername, $wemsDBpassword, $wemsDatabase)
	OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

	// Check if there is a storm open already
	$eventID = 0;
	$externalID = 0;
	$eventType = 0;
	$openTime = "";
	$activeUser = "";
	$qry = oci_parse($c, "select EVENTID, EXTERNALID, EVENTTYPE, TO_CHAR(OPENTIME, 'MM/DD/YYYY') as OPENTIME, OPENUSER from WEMS_EVENT where CLOSETIME is null")
	OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

	oci_execute($qry);

	while (($row = oci_fetch_array($qry)) !== false) {
		$eventID = $row['EVENTID'];
		$externalID = $row['EXTERNALID'];
		$eventType = $row['EVENTTYPE'];
		$openTime = $row['OPENTIME'];
		$activeUser = $row['OPENUSER'];
	}

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	//a hidden input seems to be more reliable than input buttons for this role
	if(isset($_POST['task']) && count($_POST['task']) ){
		$task = $_POST['task'];
	}else{
		$task = isset($_POST['SUBMIT']) ? $_POST['SUBMIT'] : false;
	}

	if($task == "GIS"){
		//header("Location: http://webz8dev.lirr.org/~tebert/wems/wemsViewer/WEMS_GIS.php");
		header("Location: WEMS_GIS.php");
	}


	//Create storm if there is not one already open.

	//%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%

	if($task == "Re-Open Storm"){
		$pastEvent = isset($_POST['pastStorms'])  ? $_POST['pastStorms'] : "";

		include 'openOldEvent.php';

		$qry = oci_parse($c, "select EVENTID, EXTERNALID, EVENTTYPE, TO_CHAR(OPENTIME, 'MM/DD/YYYY') as OPENTIME, OPENUSER from WEMS_EVENT where EVENTID = :EVENTID")
		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

		oci_bind_by_name($qry, ":EVENTID", $pastEvent, -1);
		oci_execute($qry);
		while (($row = oci_fetch_array($qry)) !== false) {
			$eventID = $row['EVENTID'];
			$externalID = $row['EXTERNALID'];
			$eventType = $row['EVENTTYPE'];
			$openTime = $row['OPENTIME'];
			$activeUser = $row['OPENUSER'];
		}
		$qry2 = oci_parse($c, "UPDATE WEMS_ABLE_TARGET SET ASSIGNED_CREWSIZE = NULL, ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = 1, CT_PASSNUM = NULL, CT_BAGS = NULL")
		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

		oci_execute($qry2);
		$qry3 = oci_parse($c, "UPDATE WEMS_LOCATION SET STATUS = 1, LOCATION_PASSNUM = NULL") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
		$qry4 = oci_parse($c, "UPDATE WEMS_EVENT SET CLOSETIME = NULL WHERE EVENTID = :EVENTID") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
		oci_bind_by_name($qry4, ":EVENTID", $eventID, -1);
		oci_execute($qry4);
		reOpen($eventID);
	}

	//if ($task == "Assign Parking Lot") {
	if (trim($task) == "Parking Lot") {
		$lPassNum = 0;
		$lNumBags = 0;
		$plLoc = isset($_POST['plLoc']) ? $_POST['plLoc'] : "";
		$lLoc = $plLoc;
		
		//$plConponent = "";
		$plConponent = isset($_POST['plConponent']) ? $_POST['plConponent'] : "";
		$lForman = isset($_POST['plForman']) ? $_POST['plForman'] : "";
		$lStatus = isset($_POST['plStatus']) ? $_POST['plStatus'] : "";
		$lNoteTime = isset($_POST['plNoteTime']) ? $_POST['plNoteTime'] : "";
		$lHour = isset($_POST['plStartHr']) ? $_POST['plStartHr'] : "";
		$lMin = isset($_POST['plStartMin']) ? $_POST['plStartMin'] : "";
		$lAmPm = isset($_POST['plStartAmPm']) ? $_POST['plStartAmPm'] : "";

		if ($lAmPm == 0) $lAmPm = "AM"; else $lAmPm = "PM";

		$lNoteTime = $lNoteTime . " " . $lHour . ":" . $lMin . " " . $lAmPm;
		$lEndTime = isset($_POST['plEndTime']) ? $_POST['plEndTime'] : "";
		$lcomments = isset($_POST['plcomments']) ? $_POST['plcomments'] : "";
		$lUser = $_SESSION['user'];
		$interlockingErrMsg = $plConponent;
		$lconponentl = "allConponents";
		//$plConponent = "";
		$Loc_Type = 'P';
		$filesToUpload = "";
		require_once ('assignLocation.php');

		$Loc_Type = 'P';

		updateLocationGang($lconponentl, $plConponent, $eventID, $lForman, $lNoteTime, $lStatus, $plLoc,
			$lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);

		require_once('uploadFile.php');
		uploadFile($eventID, $plConponent);
	} //if($task == "Assign Parking Lot")

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	//if ($task == "Assign Interlocking") {
	if (trim($task) == "Interlocking") {
		$iLoc = isset($_POST['iLoc']) ? $_POST['iLoc'] : "";
		$lLoc = $iLoc;
		$lconponentl = "allConponents";
		$iConponent = isset($_POST['iConponent']) ? $_POST['iConponent'] : "";
		$lConponent = $iConponent;
		$lPassNum = 0;
		$lNumBags = 0;
		$lForman = isset($_POST['iForman']) ? $_POST['iForman'] : "";
		$lStatus = isset($_POST['iStatus']) ? $_POST['iStatus'] : "";
		$lNoteTime = isset($_POST['iNoteTime']) ? $_POST['iNoteTime'] : "";
		$lHour = isset($_POST['iStartHr']) ? $_POST['iStartHr'] : "";
		$lMin = isset($_POST['iStartMin']) ? $_POST['iStartMin'] : "";
		$lAmPm = isset($_POST['iStartAmPm']) ? $_POST['iStartAmPm'] : "";
		if ($lAmPm == 0) $lAmPm = "AM"; else $lAmPm = "PM";
		$lNoteTime = $lNoteTime . " " . $lHour . ":" . $lMin . " " . $lAmPm;
		$lEndTime = isset($_POST['iEndTime']) ? $_POST['iEndTime'] : "";
		$lcomments = isset($_POST['icomments']) ? $_POST['icomments'] : "";
		$Loc_Type = 'I';
		$lUser = $_SESSION['user'];
		$filesToUpload = "";

		require_once ('assignLocation.php');

		$Loc_Type = 'I';
		updateLocationGang($lconponentl, $lConponent, $eventID, $lForman, $lNoteTime, $lStatus, $lLoc,
			$lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);

		require_once ('uploadFile.php');

		uploadFile($eventID, $lConponent);
	} //if($task == "Assign Interlocking")

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if ($task == "Download") {
		$selectedFile = isset($_POST['lDownloadFile']) ? $_POST['lDownloadFile'] : "";
		$lConponent = isset($_POST['lConponent']) ? $_POST['lConponent'] : "";
		$locationSuccessMsg = "ID: " . $selectedFile . ", CONPONENT: " . $lConponent . ", EVENT: " . $eventID;
			
		$dlqry = oci_parse($c, "SELECT BLOB_COL, ID FROM WEMS_LOCDOCS WHERE ID = :ID AND EVENTID = :EVENTID AND MARKERID = :MARKERID")
		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

		oci_bind_by_name($dlqry, ":ID", $selectedFile, -1);
		oci_bind_by_name($dlqry, ":EVENTID", $eventID, -1);
		oci_bind_by_name($dlqry, ":MARKERID", $lConponent, -1);
		oci_execute($dlqry);

		while (($row = oci_fetch_array($dlqry)) != false) {
			$id = $row['ID'];
			$blob = $row['BLOB_COL']->load();
			$tmp = explode(".",$id);

			switch ($tmp[count($tmp)-1]){
				case "pdf": $ctype="application/pdf"; break;
				case "exe": $ctype="application/octet-stream"; break;
				case "zip": $ctype="application/zip"; break;
				case "docx": $ctype="application/msword"; break;
				case "doc": $ctype="application/msword"; break;
				case "csv":
				case "xls":
				case "xlsx": $ctype="application/vnd.ms-excel"; break;
				case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
				case "gif": $ctype="image/gif"; break;
				case "png": $ctype="image/png"; break;
				case "jpeg":
				case "jpg": $ctype="image/jpg"; break;
				case "tif":
				case "tiff": $ctype="image/tiff"; break;
				case "psd": $ctype="image/psd"; break;
				case "bmp": $ctype="image/bmp"; break;
				case "ico": $ctype="image/vnd.microsoft.icon"; break;
				case "msg": $ctype="application/vnd.ms-outlook;charset=UTF-8"; break;
				default: $ctype="application/force-download";
			}

			header("Pragma: public"); // required
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false); // required for certain browsers
			// header("Content-Type: application/msword");
			header("Content-type: $ctype");
			header("Content-Type: application/force-download");
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/download");
			header("Content-Disposition: attachment; filename=" . $id);
			header("Content-Transfer-Encoding: binary");
			//header("Content-Length: ".filesize("upload_doc/$id")); //??

			ob_clean();
			flush();

			echo $blob;
			exit;
		}
	}

	if ($task == "Create Storm") {
		$stormID = 0;
		$externalID = 0;
		$eventType = isset($_POST['eventType']) ? $_POST['eventType'] : "";
		$opentime = isset($_POST['opentime']) ? $_POST['opentime'] : "";
		$openHour = isset($_POST['openStartHr']) ? $_POST['openStartHr'] : "";
		$openMin = isset($_POST['openStartMin']) ? $_POST['openStartMin'] : "";
		$openAmPm = isset($_POST['openAmPm']) ? $_POST['openAmPm'] : "";
		if ($openAmPm == 0) $openAmPm = "AM"; else $openAmPm = "PM";
		$opentime = $opentime . " " . $openHour . ":" . $openMin . " " . $openAmPm;

		$yr = 0;
		$noteUpdate = isset($_POST['sComments']) ? $_POST['sComments'] : "";
		if ($noteUpdate == "") $noteUpdate = "Storm Open";
		if ($eventType == 0) $eventErrMsg.= "<li>Please enter an Event Type</li>";
		if ($opentime == "") $eventErrMsg.= "<li>Please a enter a Date for this event</li>";
		if (!strlen($eventErrMsg)) {
			$qrySI = oci_parse($c, "select MAX(EVENTID) as MAXNUM, to_CHAR(SYSDATE, 'YYYYMMDD') as DT, to_CHAR(SYSDATE, 'YYYY') as YR from WEMS_EVENT")
			OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

			oci_execute($qrySI);
			while (($row = oci_fetch_array($qrySI)) != false) {
				$stormID = $row['MAXNUM'];
				$externalID = $row['DT'];
				$yr = $row['YR'];
			}
				
			$stormID = $stormID + 1;
			$user = $_SESSION['user'];
			$createQry = oci_parse($c, "insert into WEMS_EVENT (EVENTID, EXTERNALID, EVENTTYPE, OPENTIME, EVENTYEAR, OPENUSER)
							VALUES (:EVENTID, :EXTERNALID, :EVENTTYPE, to_date(:OPENTIME, 'mm/dd/yyyy hh:mi am'), :EVENTYEAR, :OPENUSER)")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

							oci_bind_by_name($createQry, ":EVENTID", $stormID, -1);
							oci_bind_by_name($createQry, ":EXTERNALID", $externalID, -1);
							oci_bind_by_name($createQry, ":EVENTTYPE", $eventType, -1);
							oci_bind_by_name($createQry, ":OPENTIME", $opentime, -1);
							oci_bind_by_name($createQry, ":EVENTYEAR", $yr, -1);
							oci_bind_by_name($createQry, ":OPENUSER", $user, -1);
							oci_execute($createQry);

							$eventID = $stormID;
							$activeUser = $user;

							$noteQry = oci_parse($c, "insert into WEMS_EVENT_NOTES (EVENTID, EVENTTYPE, NOTETIME, NOTEUSER, EVENTUPDATE, ENTER_DATETIME)
							VALUES (:EVENTID, :EVENTTYPE, sysdate, :NOTEUSER, :EVENTUPDATE, to_date(:ENTER_DATETIME, 'mm/dd/yyyy hh:mi am'))")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

							oci_bind_by_name($noteQry, ":EVENTID",  $stormID, -1);
							oci_bind_by_name($noteQry, ":EVENTTYPE", $eventType, -1);
							oci_bind_by_name($noteQry, ":NOTEUSER", $user, -1);
							oci_bind_by_name($noteQry, ":EVENTUPDATE", $noteUpdate, -1);
							oci_bind_by_name($noteQry, ":ENTER_DATETIME", $opentime, -1);
							oci_execute($noteQry);

							$openQry = oci_parse($c, "Update WEMS_CLEANABLE_TARGET set NOTIFYTIME = NULL, ASSIGNED_CREWSIZE = NULL, ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = 1, CT_PASSNUM = NULL, CT_BAGS = NULL")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
							oci_execute($openQry);

							$openQry2 = oci_parse($c, "Update WEMS_LOCATION set LOCATION_PASSNUM = NULL, STATUS = 1")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
							oci_execute($openQry2);

							$successMsg = "Success";
		} //if(!strlen($eventErrMsg))
	}

	if ($task == "Update Storm") {
		$eventType = isset($_POST['eventType']) ? $_POST['eventType'] : "";
		$noteUpdate = isset($_POST['sComments']) ? $_POST['sComments'] : "";
		$user = $_SESSION['user'];
		$opentime = isset($_POST['opentime']) ? $_POST['opentime'] : "";
		$openHour = isset($_POST['openStartHr']) ? $_POST['openStartHr'] : "";
		$openMin = isset($_POST['openStartMin']) ? $_POST['openStartMin'] : "";
		$openAmPm = isset($_POST['openAmPm']) ? $_POST['openAmPm'] : "";
		if ($openAmPm == 0) $openAmPm = "AM";
		else $openAmPm = "PM";
		$opentime = $opentime . " " . $openHour . ":" . $openMin . " " . $openAmPm;
		if ($eventType == 0) $eventErrMsg.= "<li>Please enter an Event Type</li>";
		if (!strlen($eventErrMsg)) {
			$updateQry = oci_parse($c, "insert into WEMS_EVENT_NOTES (EVENTID, EVENTTYPE, NOTETIME, NOTEUSER, EVENTUPDATE, ENTER_DATETIME)
							VALUES (:EVENTID, :EVENTTYPE, sysdate, :NOTEUSER, :EVENTUPDATE, to_date(:ENTER_DATETIME, 'mm/dd/yyyy HH:MI PM'))")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

							oci_bind_by_name($updateQry, ":EVENTID", $eventID, -1);
							oci_bind_by_name($updateQry, ":EVENTTYPE", $eventType, -1);
							oci_bind_by_name($updateQry, ":NOTEUSER", $user, -1);
							oci_bind_by_name($updateQry, ":EVENTUPDATE", $noteUpdate, -1);
							oci_bind_by_name($updateQry, ":ENTER_DATETIME", $opentime, -1);
							oci_execute($updateQry);

							$updateQry2 = oci_parse($c, "update WEMS_EVENT SET EVENTTYPE = :EVENTTYPE, OPENUSER = :NOTEUSER, OPENTIME = to_date(:OPENTIME, 'mm/dd/yyyy HH:MI PM') where EVENTID  = :EVENTID")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

							oci_bind_by_name($updateQry2, ":EVENTID", $eventID, -1);
							oci_bind_by_name($updateQry2, ":EVENTTYPE", $eventType, -1);
							oci_bind_by_name($updateQry2, ":NOTEUSER", $user, -1);
							oci_bind_by_name($updateQry2, ":OPENTIME", $opentime, -1);
							oci_execute($updateQry2);

							$eventSuccessMsg = "Event has been updated";
		} // if(!strlen($errMsg))
	}

	if ($task == "Close Storm") {
		$eventErrMsg = "";
		$eventType = isset($_POST['eventType']) ? $_POST['eventType'] : "";
		$noteUpdate = isset($_POST['sComments']) ? $_POST['sComments'] : "";
		if ($noteUpdate == "") $noteUpdate = "Storm Closed";
		$user = $_SESSION['user'];

		if (!strlen($eventErrMsg)) {
			$updateQry = oci_parse($c, "insert into WEMS_EVENT_NOTES (EVENTID, EVENTTYPE, NOTETIME, NOTEUSER, EVENTUPDATE)
							VALUES (:EVENTID, :EVENTTYPE, sysdate, :NOTEUSER, :EVENTUPDATE)")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
								
							oci_bind_by_name($updateQry, ":EVENTID", $eventID, -1);
							oci_bind_by_name($updateQry, ":EVENTTYPE", $eventType, -1);
							oci_bind_by_name($updateQry, ":NOTEUSER", $user, -1);
							oci_bind_by_name($updateQry, ":EVENTUPDATE", $noteUpdate, -1);
							oci_execute($updateQry);

							$closeQry = oci_parse($c, "Update WEMS_EVENT set CLOSETIME = sysdate, CLOSEUSER = :CLOSEUSER where EVENTID = :EVENTID") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
							oci_bind_by_name($closeQry, ":CLOSEUSER", $user, -1);
							oci_bind_by_name($closeQry, ":EVENTID", $eventID, -1);
							oci_execute($closeQry);

							$updateQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, NOTETIME, NOTEUSER, EVENTUPDATE)
							VALUES (:EVENTID, sysdate, :NOTEUSER, :EVENTUPDATE)") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
							oci_bind_by_name($updateQry, ":EVENTID", $eventID, -1);
							oci_bind_by_name($updateQry, ":NOTEUSER", $user, -1);
							oci_bind_by_name($updateQry, ":EVENTUPDATE", $noteUpdate, -1);
							oci_execute($updateQry);

							$closeQry = oci_parse($c, "Update WEMS_GANG set CLOSETIME = sysdate, CLOSEUSER = :CLOSEUSER where EVENTID = :EVENTID") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
							oci_bind_by_name($closeQry, ":CLOSEUSER", $user, -1);
							oci_bind_by_name($closeQry, ":EVENTID", $eventID, -1);
							oci_execute($closeQry);

							$closeCTQry = oci_parse($c, "Update WEMS_CLEANABLE_TARGET_NOTES set CTENDTIME = sysdate, CTNOTEUSER = :CLOSEUSER where EVENTID = :EVENTID AND CTENDTIME is NULL") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
							oci_bind_by_name($closeCTQry, ":CLOSEUSER", $user, -1);
							oci_bind_by_name($closeCTQry, ":EVENTID", $eventID, -1);
							oci_execute($closeCTQry);

							/*
							 $closeLocQry = oci_parse($c, "Update WEMS_CLEANABLE_TARGET set NOTIFYTIME = NULL, ASSIGNED_CREWSIZE = NULL, ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = NULL, CT_PASSNUM = NULL, CT_BAGS = NULL")
							 OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
							 */
								
							$closeLocQry = oci_parse($c, "Update WEMS_LOCATION set STATUS = 4, LOCATION_PASSNUM = NULL") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
							oci_execute($closeLocQry);

							$eventID = 0;
							$eventType = 0;
							$openTime = "";
							$EventSuccessMsg = "Success";
		} // if(!strlen($errMsg))
	}

	if ($task == "Enter Gang") {
		$forman = isset($_POST['forman']) ? $_POST['forman'] : "";
		$gEmpNum = isset($_POST['gEmpNum']) ? $_POST['gEmpNum'] : "";
		$gStatus = isset($_POST['gStatus']) ? $_POST['gStatus'] : "";
		$gUser = $_SESSION['user'];
		$gStartTm = isset($_POST['gStartTm']) ? $_POST['gStartTm'] : "";
		$gangErrMsg = "";
		if ($forman == 0) $gangErrMsg.= "<li>Please enter a Foreman</li>";
		if ($gStartTm == "") $gangErrMsg.= "<li>Please enter a start date</li>";

		$gHour = isset($_POST['gStartHr']) ? $_POST['gStartHr'] : "";
		$gMin = isset($_POST['gStartMin']) ? $_POST['gStartMin'] : "";
		$gAmPm = isset($_POST['gAmPm']) ? $_POST['gAmPm'] : "";
		if ($gAmPm == 0) $gAmPm = "AM"; else $gAmPm = "PM";
		$gStartTm = $gStartTm . " " . $gHour . ":" . $gMin . " " . $gAmPm;
		$gEndTm = isset($_POST['gEndTm']) ? $_POST['gEndTm'] : "";
		$gComments = isset($_POST['gComments']) ? $_POST['gComments'] : "";
		if ($gComments == "") $gComments = "Gang Created";
		if (!strlen($gangErrMsg)) {
			$qry = oci_parse($c, "insert into WEMS_GANG (EVENTID, FORMANID, EMP_ASSIGNED, OPENTIME, OPENUSER, STATUS)
							VALUES (:EVENTID, :FORMANID, :EMP_ASSIGNED, to_date(:OPENTIME, 'mm/dd/yyyy HH:MI AM'), :OPENUSER, :STATUS)")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

							oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
							oci_bind_by_name($qry, ":FORMANID", $forman, -1);
							oci_bind_by_name($qry, ":EMP_ASSIGNED", $gEmpNum, -1);
							oci_bind_by_name($qry, ":OPENTIME", $gStartTm, -1);
							// oci_bind_by_name($qry, ":CLOSETIME", $gEndTm, -1);
							oci_bind_by_name($qry, ":OPENUSER", $gUser, -1);
							oci_bind_by_name($qry, ":STATUS", $gStatus, -1);
							oci_execute($qry);

							$updateQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, FORMANID, NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED, ENTER_DATETIME, STATUS)
							VALUES (:EVENTID, :FORMANID, to_date(:NOTETIME, 'mm/dd/yyyy HH:MI AM'), :NOTEUSER, :EVENTUPDATE, :EMP_ASSIGNED, SYSDATE, :STATUS)")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

							oci_bind_by_name($updateQry, ":EVENTID", $eventID, -1);
							oci_bind_by_name($updateQry, ":FORMANID", $forman, -1);
							oci_bind_by_name($updateQry, ":NOTETIME", $gStartTm, -1);
							oci_bind_by_name($updateQry, ":NOTEUSER", $gUser, -1);
							oci_bind_by_name($updateQry, ":EVENTUPDATE", $gComments, -1);
							oci_bind_by_name($updateQry, ":EMP_ASSIGNED", $gEmpNum, -1);
							oci_bind_by_name($updateQry, ":STATUS", $gStatus, -1);
							oci_execute($updateQry);

							$successMsg = "Success";
		} // if(!strlen($errMsg))
	}

	if ($task == "Update Gang") {
		$forman = isset($_POST['forman']) ? $_POST['forman'] : "";
		$gEmpNum = isset($_POST['gEmpNum']) ? $_POST['gEmpNum'] : "";
		$gStatus = isset($_POST['gStatus']) ? $_POST['gStatus'] : "";
		$gStartTm = isset($_POST['gStartTm']) ? $_POST['gStartTm'] : "";
		$gHour = isset($_POST['gStartHr']) ? $_POST['gStartHr'] : "";
		$gMin = isset($_POST['gStartMin']) ? $_POST['gStartMin'] : "";
		$gAmPm = isset($_POST['gAmPm']) ? $_POST['gAmPm'] : "";
		if ($gAmPm == 0) $gAmPm = "AM"; else $gAmPm = "PM";
		$gStartTm = $gStartTm . " " . $gHour . ":" . $gMin . " " . $gAmPm;
		// $gEndTm = isset($_POST['gEndTm'])  ? $_POST['gEndTm'] : "";
		$gComments = isset($_POST['gComments']) ? $_POST['gComments'] : "";
		if($gComments == "") $gComments ="Gang Created";

		$gUser = $_SESSION['user'];
		$gangErrMsg = "";
		if (!strlen($gangErrMsg)) {
			$qry = oci_parse($c, "Update WEMS_GANG SET EVENTID = :EVENTID, FORMANID = :FORMANID, EMP_ASSIGNED = :EMP_ASSIGNED, OPENTIME = to_date(:OPENTIME, 'mm/dd/yyyy HH:MI AM'),
                                    OPENUSER = :OPENUSER, STATUS = :STATUS where EVENTID = :EVENTID and FORMANID = :FORMANID")
                                    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
                                         
                                        oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
                                        oci_bind_by_name($qry, ":FORMANID", $forman, -1);
                                        oci_bind_by_name($qry, ":STATUS", $gStatus, -1);
                                        oci_bind_by_name($qry, ":EMP_ASSIGNED", $gEmpNum, -1);
                                        oci_bind_by_name($qry, ":OPENTIME", $gStartTm, -1);
                                        // oci_bind_by_name($qry, ":CLOSETIME", $gEndTm, -1);
                                        oci_bind_by_name($qry, ":OPENUSER", $gUser, -1);
                                        oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
                                        oci_bind_by_name($qry, ":FORMANID", $forman, -1);
                                        oci_execute($qry);
                                         
			$updateQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, FORMANID, NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED, ENTER_DATETIME, STATUS)
							VALUES (:EVENTID, :FORMANID, to_date(:NOTETIME, 'mm/dd/yyyy HH:MI AM'), :NOTEUSER, :EVENTUPDATE, :EMP_ASSIGNED, SYSDATE, :STATUS)")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

							oci_bind_by_name($updateQry, ":EVENTID", $eventID, -1);
							oci_bind_by_name($updateQry, ":FORMANID", $forman, -1);
							oci_bind_by_name($updateQry, ":NOTETIME", $gStartTm, -1);
							oci_bind_by_name($updateQry, ":NOTEUSER", $gUser, -1);
							oci_bind_by_name($updateQry, ":EVENTUPDATE", $gComments, -1);
							oci_bind_by_name($updateQry, ":EMP_ASSIGNED", $gEmpNum, -1);
							oci_bind_by_name($updateQry, ":STATUS", $gStatus, -1);
							oci_execute($updateQry);

							$successMsg = "Success";
		}
	} //if(!strlen($errMsg))
} //if($task == "Update Gang")

//if ($task == "Assign Location") {
if ($task == "Location") {
	$lLoc = isset($_POST['lLoc']) ? $_POST['lLoc'] : "";
	$lConponent = isset($_POST['lConponent']) ? $_POST['lConponent'] : "";
	$lForman = isset($_POST['lForman']) ? $_POST['lForman'] : "";
	$lStatus = isset($_POST['lStatus']) ? $_POST['lStatus'] : "";
	$lPassNum = isset($_POST['lPassNum']) ? $_POST['lPassNum'] : "";
	$lNumBags = isset($_POST['lNumBags']) ? $_POST['lNumBags'] : "";
	$lNoteTime = isset($_POST['lNoteTime']) ? $_POST['lNoteTime'] : "";
	$lHour = isset($_POST['staStartHr']) ? $_POST['staStartHr'] : "";
	$lMin = isset($_POST['staStartMin']) ? $_POST['staStartMin'] : "";
	$lAmPm = isset($_POST['staStartAmPm']) ? $_POST['staStartAmPm'] : "";
	if ($lAmPm == 0) $lAmPm = "AM"; else $lAmPm = "PM";

	$lNoteTime = $lNoteTime . " " . $lHour . ":" . $lMin . " " . $lAmPm;
	$lEndTime = isset($_POST['lEndTime']) ? $_POST['lEndTime'] : "";
	$lcomments = isset($_POST['lcomments']) ? $_POST['lcomments'] : "";
	$lconponentl = isset($_POST['allConponents']) ? $_POST['allConponents'] : "";
	$lUser = $_SESSION['user'];
	$filesToUpload = "";
	$locationErrMsg = "";
	require_once ('assignLocation.php');

	$Loc_Type = 'S';
	updateLocationGang($lconponentl, $lConponent, $eventID, $lForman, $lNoteTime, $lStatus, $lLoc,
		$lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);
	require_once ('uploadFile.php');

	uploadFile($eventID, $lConponent);
	header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}
?>
    
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
	    <title>WEMS</title>
	    
	    <link rel="stylesheet" type="text/css" media="all" href="../lib/jscalendar/skins/aqua/theme.css" title="win2k-cold-1" />
	    <link href="css/wems-styles.css" rel="stylesheet" type="text/css" />
	    
		<script language="JavaScript" type="text/javascript">
		window.tabindex = <?=$tabindex?>;
		window.eventId = <?=$eventID?>;
		</script>
	    
	    <script type="text/javascript" src="js/eventMaint.js"></script>
	    <script type="text/javascript" src="../lib/jscalendar/calendar.js"></script>
	    <script type="text/javascript" src="../lib/jscalendar/lang/calendar-en.js"></script>
	    <script type="text/javascript" src="../lib/jscalendar/calendar-setup.js"></script>
	    
	    <link href="template1/tabcontent.css" rel="stylesheet" type="text/css" /> 
	    <script src="tabcontent.js" type="text/javascript"></script>
	    
	    <link rel="stylesheet" type="text/css" href="styles.css" />
	    <script type="text/javascript" src="jquery-1.11.2.js"></script>
	    <script language="JavaScript" type="text/javascript" src="js/eventMaint.js"></script>
	    
	    <meta http-equiv="Pragma" content="no-cache" />
	    <meta http-equiv="Expires" content="-1" />
	    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    </head>
    
    <!-- <body onload="getEmployees();getILEmployees();getPLEmployees();"> -->
    <body class="eventMaint">
		<div>
			<img src="../images/wemsPhoto.jpg" alt="Mountain View" style="float:right;" />
			<br/><br/>
		</div>
		<div>
			<ul class="tabs" data-persist="true">
				<!--  <li><a href="#view1">Home</a></li> -->
		    		<li><a href="#view1">Event</a></li> 
		    		<li><a href="#view2">Gang Assignments</a></li> 
		    		<li><a href="#view3">Location Assignments</a></li> 
		    		<li><a href="#view4">Reports</a></li> 
		    		<li><a href="#view5">Maps</a></li> 
		    		<li>
					<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" name="logout" id="logoutform">
						<input type="hidden" name="Logout" value="true" />
						<button class="logout logging smallGreenBtn">Log Out</button>
					</form>
				</li> 
			</ul> 
		</div>

    		<div class="tabcontents">
     <!--
     ************************************************************************************************************************************************
                                                    EVENT
     ************************************************************************************************************************************************
     -->

    			<div style="background-color:#f0ffff;" id="view1"> 
      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="eventform" id="eventform" >
      				<fieldset id="event">
        					<legend><span class="bold_green_message">Event Maintenance</span></legend>

	        				<table align="center" class="table grid123" cellpadding="1" cellspacing="1" border="0" >
							<?php echo getErScRows($eventErrMsg, $eventSuccessMsg); ?>
	
	                      		<tr><th colspan="2" align="center">Event</th></tr>
	                      		
                      			<?php
                      			if($eventID > 0){
                      			    echo "<tr><th>Storm ID:</th> <td><input type=\"text\" class=\"input-box\" name=\"sID\" value=\"$externalID\" readonly /></td></tr>";
                      			}
							?>
							<tr>
								<th>Storm Level:</th>
								<td><select class="input-box" name="eventType" id = "eventType">
									<option value="0" selected="selected"> </option>
							
								<?php
	                                   $qry = oci_parse($c, "SELECT EVENTTYPE, EVENTDESC from EVENTTYPE order by EVENTDESC desc")
	                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	
	                                   oci_execute($qry);
	
	                                   while( ($row = oci_fetch_array($qry)) != false ){
									$id = $row['EVENTTYPE'];
									$desc = $row['EVENTDESC'];
										
									if($id == $eventType)
									echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
									else
									echo "<option value=\"$id\" > $desc </option>";
	                                   }
						          ?> 
								</select></td>
							</tr>
							<tr>
								<?php 
								if($eventID > 0){
								   echo"<th>Assigned By:</th><td><input type=\"text\" class='input-box' name=\"sAssigned\" value=\" $activeUser\" readonly></td>";
								}
								?>	
							</tr>
							<tr>
								<th>Start Date: </th>
								<td>
								<?php 
								$timeData = array( "startTm" => $openTime, "startTmNm" => "opentime", "calNm" => "startCalbutton" );
								echo dateTimeFields("open", $timeData);
								?>					
								</td>
							</tr>
							<tr>
								<th>Comments</th>
								<td><textarea rows="4" cols="50" name="sComments"></textarea></td>
							</tr>

							<?php
							if( $_SESSION['group'] == "WEMS_Admin" ){
								echo "<tr>";
								if($eventID > 0){
									echo "<td colspan = \"1\" align=\"center\"><input class=\"wideredBtn\" type=\"submit\" value=\"Close Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
									echo "<td colspan = \"1\" align=\"center\"><input class=\"wideGreenBtn\" type=\"submit\" value=\"Update Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
								}else{
									echo "<td colspan = \"2\" align=\"center\"><input class=\"wideGreenBtn\" type=\"submit\" value=\"Create Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
								}
								
								echo "</tr>";
							}
							?>
        				</table>
        				
        				<br></br>

        				<table align="center" class="table grid123" cellpadding="1" cellspacing="1" border="0" >
						<tr><th colspan = "2" align="center">Storm History</th></tr>
												
						<tr>
							<td><textarea class="input-box" rows="10" cols="100" id="stormHistory"><?php 
							if($eventID > 0){
							   $qry = oci_parse($c, "select et.EVENTDESC, TO_CHAR(e.ENTER_DATETIME, 'MM/DD/YYYY hh:miAM') as NOTETIME, 
							                         e.NOTEUSER, e.EVENTUPDATE  
							                         from WEMS_EVENT_NOTES e, EVENTTYPE et
							                         where e.EVENTID = :EVENTID and et.EVENTTYPE = e.EVENTTYPE")
							       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
							
							       oci_bind_by_name($qry, ":EVENTID",  $eventID, -1);
							   oci_execute($qry);
							
							   while(($row = oci_fetch_array($qry)) !== false){
							       $eType = $row['EVENTDESC'];
							       $eTime = $row['NOTETIME'];
							       $eUser = $row['NOTEUSER'];
							       $eUpdate = $row['EVENTUPDATE'];

							       echo $eTime . ", " . $eType . ", " . $eUser . ", " . $eUpdate . '&#13;&#10;';
							   }
							}
							?></textarea></td>
						</tr>
						<?php 
						if($eventID == 0){
							$outStr = "<tr><td colspan = \"2\" align=\"center\">__________________________________________</td></tr>";
							$outStr .= "<tr><td colspan = \"2\" align=\"center\">Past Storms:";
							$outStr .= "<select name=\"pastStorms\" id = \"pastStorms\" > <option value= 0 selected>";

							$qry = oci_parse($c, "SELECT EXTERNALID, EVENTID from WEMS_EVENT order by EVENTID")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
							oci_execute($qry);

							while( ($row = oci_fetch_array($qry)) != false ){
								$id = $row['EVENTID'];
								$desc = $row['EXTERNALID'] . " - " . $id;

								if($id == $eventType){
									$outStr .= "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
								}else{
									$outStr .= "<option value=\"$id\" > $desc </option>";
								}
							}

							$outStr .= "</option></select></td></tr>";

							$outStr .= "<td colspan = \"2\" align=\"center\"><input class=\"wideGreenButton\" type=\"submit\" value=\"Re-Open Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";

							echo $outStr;
						}
						?>
        				</table>
      			</fieldset>
      		</form>
    		</div> 
     
    <!--
     ************************************************************************************************************************************************
                                                            Create Gangs
     ************************************************************************************************************************************************
     -->    
    	<div style="background-color:#f0ffff;" id="view2"> 
     	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="gangform" id="gangform" >		
			<fieldset id="creategangs">
        			<legend><span class="bold_green_message">Gang Assignments</span></legend>

        			<table align="center" class="table grid123">
        				<?php echo getErScRows($gangErrMsg, $gangSuccessMsg); ?>
					<tr>
						<th colspan="2" align="center">
							<span class="heading_bold"><center>Create Gang</center></span>
						</th>
					</tr>
					<tr>
						<th>Foreman:</th>
						<td><select class="input-box" name="forman" id="forman" onchange="getGangData()">
							<option value= "" >  </option>
							<?php 
							$qry = oci_parse($c, "SELECT EMPLOYEENUMBER, LST_NME || ', ' || FST_NME AS NAME from WEMS_EMPLOYEE where DEPTCODE is not NULL and DIV_CD = '1' order by LST_NME")
							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
		
							oci_execute($qry);
		
							while( ($row = oci_fetch_array($qry)) != false ){
								$id = $row['EMPLOYEENUMBER'];
								$desc = $row['NAME'];
									
								if($id == $forman){
									echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
								}else{
									echo "<option value= \"$id\" > $desc </option>";
								}
							}
							?> 
						</select></td>
					</tr>
					<tr>
						<th>Number Of Employees</th>
						<td>
							<select class="input-box" name="gEmpNum" id="gEmpNum">
							<?php
							for ($x = 0; $x <= 15; $x++) {
								if($gEmpNum == $x){
									echo "<option value= \"$x\" selected=\"selected\"> $x </option>";
								}else{
									echo "<option value= \"$x\"> $x </option>";
								}
							}
							?>
							</select>
						</td>
					</tr>								
					<tr>
						<th>Status</th>
						<td>
							<select name="gStatus" id="gStatus" class="input-box">
								<option value="0" <?= (isset($_POST['gStatus']) && trim($_POST['gStatus']) == "0") ? "selected" : "" ?>>  </option>
								<option value="1" <?= (isset($_POST['gStatus']) && trim($_POST['gStatus']) == "1") ? "selected" : "" ?>> Assigned </option>
								<option value="2" <?= (isset($_POST['gStatus']) && trim($_POST['gStatus']) == "2") ? "selected" : "" ?>> Closed </option>
							</select>
						</td>
					</tr>	
					<?php
					$gStartTm = isset($_POST['gStartTm'])  ? $_POST['gStartTm'] : "";
					$gHour = isset($_POST['gStartHr'])  ? $_POST['gStartHr'] : "";
					$gMin = isset($_POST['gStartMin'])  ? $_POST['gStartMin'] : "";
					$gAmPm = isset($_POST['gAmPm'])  ? $_POST['gAmPm'] : "";
					?>
					<tr>
						<th>Start Date Time</th>
						<td>
							<?php 
							$timeData = array( "startTm" => $gStartTm, "startTmNm" => "gStartTm", "calNm" => "gangStartTm", "hour" => $gHour, "min" => $gMin, "amPm" => $gAmPm );
							echo dateTimeFields("g", $timeData);
							?>
						 </td>
					</tr>						
					<tr>
						<th>Comments</th> 
						<td><textarea rows="4" cols="50" name="gComments"></textarea></td>
					</tr>
				<?php
				if( $_SESSION['group'] == "WEMS_Admin" || $_SESSION['group'] == "WEMS_Write" ){
					if($task == "Enter Gang"){
						echo "<tr><td colspan = \"2\" style=\"text-align: center\"><input class=\"wideGreenBtn\" type=\"submit\" value=\"Update Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
					}elseif($eventID > 0){
						echo "<tr><td colspan = \"2\" style=\"text-align: center\"><input class=\"wideGreenBtn\" type=\"submit\" value=\"Enter Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
					}
				}
				?>
				</table>
					
        			<br></br>
        									
        			<table align="center" class="table grid123">
					<tr><th colspan="2" align="center">Gang History</th></tr>
					<tr>
						<td></td>
						<td>
							<textarea class="input-box" rows="5" cols="100" name="gHistory" id="gHistory"><?php 
	                              $qry = oci_parse($c, "SELECT TO_CHAR(ENTER_DATETIME, 'MM/DD/YYYY HH:MI PM') as NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED
											 from WEMS_GANG_NOTES where FORMANID = :FORMANID and EVENTID = :EVENTID order by NOTETIME")
											OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	       
	       					oci_bind_by_name($qry, ":FORMANID", $forman, -1);
	       					oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
	       					oci_execute($qry);
	       					$comments = "";
	
	       					while( ($row = oci_fetch_array($qry)) != false ){
								$noteTime = $row['NOTETIME'];
								$user = $row['NOTEUSER'];
								$note = $row['EVENTUPDATE'];
								$noteEmpAssigned = $row['EMP_ASSIGNED'];
								       								    
								$comments .= $noteTime . ",  user: " . $user . ",  " . $note . ", Employees assigned: " . $noteEmpAssigned . "\r\n";
	       					}
	       					echo $comments;
							?>
							</textarea>
						</td>
					</tr>
					<tr>
						<th colspan="2" align="center">
			        			<table align="center">
			        				<?php 
								if($eventID > 0){
								    include 'getTotalByDepartment.php';
								    echo "<tr><td colspan=\"4\"> " . $output . "</td></tr>";
								}
			       	               ?>
			        			</table>
						</th>
					</tr>
        			</table>

      		</fieldset>
      	</form>
     </div>
     	
     <!--
     ************************************************************************************************************************************************
                                                        LOCATION

     ************************************************************************************************************************************************
     -->  
      <div style="background-color:#f0ffff;" id="view3">
		<fieldset id="Assignment">
        		<legend><span class="bold_green_message">Assignments</span> </legend>
    			<div id="content">
       			<div id="tab-container">
          			<ul id="tabs-titles" class="content" data-persist="true">
	             			<li><a href="#lview0">Assign Gang To Station</a></li>
	    		 			<li><a href="#lview1">Assign Gangs To Interlocking</a></li>
	    		 			<li><a href="#lview2">Assign Gangs To Parking Lot</a></li>
	    		 			<li><a href="#lview3">Assign Gangs To Signal</a></li>
             			</ul>
    				</div>
    				<div id="main-container">
    					<ul id="tabs-contents" style="list-style: none;">
    						<li>
    							<div id="lview0">
    								<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="locationform_station" id="locationform_station" >
    									<table align="center" class="table grid123">
										<?php //echo getErScRows($locationErrMsg, $locationSuccessMsg); ?>
										<tr>
											<tr><th colspan = "2"><span class="heading_bold"><center>Location Maintenance</center></span></th></tr>
											<th>Location:</th>
											<td>
												<?php
												echo rrLocationMenu("l", $lLoc);
												?>
											</td>
										</tr>
										<tr>
											<th>Component:</th>
											<td>
												<?php echo rrComponentFields("l", $lConponent, $lLoc); ?>
											</td>
										</tr>
										<tr>
											<th>Gang:</th>
											<td>
                                   				<?php echo gangAssignMenu("l", $lConponent, $lLoc); ?>
											</td>
										</tr>
										<tr>
											<th>Status:</th>
											<td>
											<?php echo statusMenu("l", $lStatus); ?>
											</td>
										</tr>
										<tr> 
											<th>Pass Number:</th>
											<td>
												<select class="input-box" name="lPassNum" id = "lPassNum">
												<?php
												for ($x = 0; $x <= 10; $x++) {
												    if($x == $lPassNum)
												    echo "<option value= \"$x\" selected=\"selected\"> $x </option>";
												      else
												    echo "<option value= \"$x\"> $x </option>";
												}
												?>
												</select>
											</td>
										</tr>
										<tr>
											<th># of Bags:</th>
											<td>
												<select class="input-box" name="lNumBags" id="lNumBags">
												<?php
												for ($x = 0; $x <= 40; $x++) {
												    echo "<option value= \"$x\"> $x </option>";
												}
												?>
												</select>
											</td>
										</tr>
										<tr>
											<th> Support Document:</th>
											<td>
												<input class="input-box" name="fileToUpload[]" id= "rDoc" size="75" type="file" multiple="multiple" value="<?= $supportDoc ?>" />
											</td>
										</tr>
										 <tr>
											<th>  Support Documents attached:</th>
											<td>
												<?php
												echo downloadFields("l", $lConponent, "CTID");
												?>
											</td>
										</tr>	
										<tr>
											<th>Date/Time</th>
											<td>
											<?php 
											$timeData = array( "startTmNm" => "lNoteTime", "calNm" => "locationStartTime" );
											echo dateTimeFields("sta", $timeData);
											?>
											</td>
										</tr>
										<tr>
											<th>Comments</th>
											<td><textarea class="input-box" rows="4" cols="50" name="lcomments"></textarea></td>
										</tr>
												
	             							<?php
	             							echo locationSubmitRow("l", $lLoc, "Location");
										?>
        								</table>
        									
        								<br></br>

        								<table align="center" class="table grid123" cellpadding="1" cellspacing="1" border="0" >
										<tr><th colspan = "2" align="center">Location History</th></tr>
										<tr>
											<td></td>
											<td>
												<?php
												echo historyTextarea("l", $lConponent);
												?>
											</td>
										</tr>
        								</table>
    								</form>
    							</div>
    						</li>
<!--
     ************************************************************************************************************************************************
                                                       Interlockings 
  
     ************************************************************************************************************************************************
     --> 
    								<li>
    									<div id="lview1">
										<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="locationform_interlocking" id="locationform_interlocking">
											<table align="center" class="table grid123">
												<tr><th colspan = "2" align="center"><span class="heading_bold"><center>Interlocking Maintenance</center></span></th></tr>
												<tr>
													<th>Interlocking:</th>
													<td>
														<?php echo rrLocationMenu("i", $iLoc); ?>
													</td>
												</tr>
												<tr>
													<th>Component:</th>
													<td> <?php echo rrComponentFields("i", $iConponent, $iLoc); ?> </td>
												</tr>
												<tr>
													<th>Gang:</th>
													<td>
														<!-- <select name="iForman" id="iForman" > 
															<option value="0" selected="selected">  </option>
	                                   						</select>
	                                   						-->
	                                   						<?php
                                   							echo gangAssignMenu("i", $iConponent, $iLoc);
                                   							?>
													</td>
												</tr>
												<tr>
													<th>Status:</th>
													<td>
													<?php echo statusMenu("i", $iStatus); ?>
													</td>
												</tr>
												<tr>
													<th> Support Document:</th>
													<td>
														<input name="fileToUpload[]" id="rDoc" size="75" type="file" multiple="multiple" value="<?php echo $isupportDoc; ?>" />
													</td>
												</tr>
												<tr>
													<th>  Support Documents attached:</th>
													<td>
														<?php echo downloadFields("i", $iConponent, "MARKERID"); ?>
													</td>
												</tr>	
												
												<tr>
													<th>Date/Time</td>
													<td>
													<?php 
													$timeData = array( "startTmNm" => "iNoteTime", "calNm" => "interlockingStartTime" );
													echo dateTimeFields("i", $timeData);
													?>
													</td>
												</tr>
												<tr>
													<th>Comments</th>
													<td><textarea class="input-box" rows="4" cols="50" name="icomments"></textarea></td>
												</tr>
												
             									<?php
             									echo locationSubmitRow("i", $iLoc, "Interlocking");
											?>
        									</table>
        									
        									<br></br>

        									<table align="center" class="table grid123">
											<tr><th colspan="2" align="center">Interlocking History</th></tr>
											<tr>
												<td></td>
												<td><textarea class="input-box" rows="10" cols="100" name="iHistory" id="iHistory"></textarea></td>
											</tr>
        									</table>
										</form>
    									</div>
    								</li>
<!-- 
____________________________________________________________________________________________________________________________________________________
____________________________________________________________PARKING LOTS__________________________________________________________________________
___________________________________________________________________________________________________________________________________________________
 -->
 								<li>
									<div id="lview2">
        									<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="locationform_parking" id="locationform_parking" >
											<table align="center" class="table grid123">
												<?php echo getErScRows($parkingLotErrMsg, $parkingLotSuccessMsg); ?>
				
												<tr>
													<th colspan="2" align="center"><span class="heading_bold"><center>Parking Lot Maintenance</center></span></th>
												</tr>
												<tr>
													<th>Parking Lots:</th>
													<td>
														<?php echo rrLocationMenu("pl", $plLoc); ?>
													</td>
												</tr>
												<tr>
													<th>Component:</th>
													<td> <?php echo rrComponentFields("pl", $plConponent, $plLoc); ?> </td>
												</tr>
												<tr>
													<th>Gang:</th>
													<td>
														<?php
                                   							echo gangAssignMenu("pl", $plConponent, $plLoc);
                                   							?>
													</td>
												</tr>
												<tr>
													<th>Status:</th>
													<td>
														<?php echo statusMenu("pl", $iStatus); ?>
													</td>
												</tr>
												<tr>
													<th> Support Document:</th>
													<td> <input class="input-box" name="fileToUpload[]" id="rDoc" size="75" type="file" multiple="multiple" value="<?=$plsupportDoc ?>" /></td>
												</tr>
												<tr>
													<th>  Support Documents attached:</th>
													<td>
														<?php echo downloadFields("pl", $plConponent, "MARKERID"); ?>
													</td>
												</tr>
												<tr>
													<th>Date/Time</th>
													<td>
													<?php 
													$timeData = array( "startTmNm" => "plNoteTime", "calNm" => "parkingLotStartTime" );
													echo dateTimeFields("pl", $timeData);
													?>
													</td>
												</tr>
												<tr>
													<th>Comments</th>
													<td><textarea class="input-box" rows="4" cols="50" name="plcomments"></textarea></td>
												</tr>

												<?php
												echo locationSubmitRow("pl", $plLoc, "Parking Lot");
												?>
											</table>
        									
        										<br></br>

	        									<table align="center" class="table grid123" cellpadding="1" cellspacing="1" border="0" >
												<tr><th colspan="2" align="center">Parking Lot History</th></tr>
												<tr>	
													<td></td>
													<td>		
														<?php
														echo historyTextarea("pl", $plConponent);
														?>
													</td>
												</tr>
	        									</table>
        									</form>
    									</div>
    								</li>

<!-- 
____________________________________________________________________________________________________________________________________________________
____________________________________________________________SIGNALS__________________________________________________________________________
___________________________________________________________________________________________________________________________________________________
 -->

 								<li>
									<div id="lview3">
        									<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="locationform_signal" id="locationform_signal" >
											<table align="center" class="table grid123" cellpadding="1" cellspacing="1" border="0" >
												<?php echo getErScRows($signalErrMsg, $signalSuccessMsg); ?>
				
												<tr><th colspan="2" align="center">Signals Maintenance</th></tr>						
												<tr>
													<th>Signals:</th>
													<td>
														<?php echo rrLocationMenu("si", $lLoc); ?>
													</td>
												</tr>
												<tr>
													<th>Component:</th>
													<td> <?php echo rrComponentFields("si", $plConponent, $lLoc); ?> </td>
												</tr>
												<tr>
													<th>Gang:</th>
													<td>
														<select name="siForman" id="siForman"> 
															<option value="0" selected="selected">  </option>
														</select>
													</td>
												</tr>
												<tr>
													<th>Status:</th>
													<td>
														<?php echo statusMenu("si", $iStatus); ?>
													</td>
												</tr>
												<tr>
													<th> Support Document:</th>
													<td> <input name="fileToUpload[]" id="siDoc" size="75" type="file" multiple="multiple" value="<?=$siSupportDoc ?>" /></td>
												</tr>
												<tr>
													<th>  Support Documents attached:</th>
													<td>
														<?php echo downloadFields("si", $plConponent, "MARKERID"); ?>
													</td>
												</tr>	
												
												<tr>
													<th>Date/Time</th>
													<td>
													<?php 
													$timeData = array( "startTmNm" => "siNoteTime", "calNm" => "signalStartTime" );
													echo dateTimeFields("si", $timeData);
													?>									
													</td>
												</tr>
												<tr>
													<th>Comments</th>
													<td><textarea rows="4" cols="50" name="sicomments"></textarea></td>
												</tr>

             										<?php
             										echo locationSubmitRow("si", $lLoc, "Signal");
												?>
        									</table>
        									
        									<br></br>

        									<table align="center" class="table grid123" cellpadding="1" cellspacing="1" border="0" >
											<tr><th colspan="2" align="center">Signal History</th></tr>
											<tr>	
												<td></td>
												<td>											
													<?php
													echo historyTextarea("si", $siConponent);
													?>
												</td>
											</tr>
        									</table>
        									</form>
    									</div>
    								</li>
    							</ul>
    						</div>
    					</div>
      		</fieldset>
		</div>
    	
   <!--
     ************************************************************************************************************************************************
                                                        Reports
     ************************************************************************************************************************************************
     -->     
		<div style="background-color:#f0ffff;" id="view4">
     			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="eventPDFForm" id="eventPDFForm" target="WEMS_REPORT" onsubmit="return validateEventLocation();">
                <fieldset id="reports">
        			<legend><span class="bold_green_message">Platform Assignment Reports</span></legend>
					<table align="center" class="table grid123">
						<tr>
							<th>Event:</th>
							<td>
								<select name="eventId" id="eventId" onchange="getLocationByEvent()" class="input-box">
									<option value='0' selected="selected"> Select Event  </option>
									<?php 
									$eventObj = new event();
									$result = $eventObj->getEventList();
									if($result){
										while( ($row = oci_fetch_array($result[0])) != false ){                                    
											$id = $row['EVENTID'];
											$desc = $row['EXTERNALID'];
		    									echo "<option value=\"$id\" > $desc : $id </option>";
										}
									}
									oci_free_statement($result[0]);
									?> 
								</select>
							</td>
						<tr>
						     <th>Location:</td>
							<td>
								<select name="locationId[]" id="locationId" size="1" multiple="multiple" width="100%" class="input-box">
									<option selected="selected">Select Location</option>
								</select>
							</td>
						</tr>
						<tr><td colspan="2"><input class="wideGreenBtn" style="width: auto;" type="submit" name="SUBMIT" id="SUBMIT" value="Create Platform Assignment PDF" /></td></tr> 
                            
						<?php 
						if(isset($task) && $task == 'Create Platform Assignment PDF') {
							include_once '../classes/eventPDFClass.php';
							$pdf = new eventPDF();
							$pdf->createEventPDF($_POST);
						}
						?>
        				</table>
      			</fieldset>
			</form>
			<br></br>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="gangPDFForm" id="gangPDFForm" target="WEMS_REPORT2" onsubmit="return validateEvent();">
				<fieldset id="departmentWiseReport">
					<legend><span class="bold_green_message">Department Wise Report</span></legend>    
					<table align="center" class="table grid123">
						<tr>
							<th>Event:</th>
							<td>
								<select name="eventId" id="eventId" class="input-box"> <option value='0' selected="selected"> Select Event  </option>
								<?php 
								$eventObj = new event();
									$result = $eventObj->getEventList();
									if($result){
										while( ($row = oci_fetch_array($result[0])) != false ){                                    
											$id = $row['EVENTID'];
											$desc = $row['EXTERNALID'];
		    									echo "<option value=\"$id\" > $desc : $id </option>";
										}
									}
									oci_free_statement($result[0]);
									?>
								</select>
							</td>
						</tr>

						<tr>
							<td colspan="2">
								<center>
									<input type="submit" name="SUBMIT" id="SUBMIT" class="wideGreenBtn" style="width:auto" value="Create Department Wise Employee PDF" />
								</center>
							</td>
						</tr> 
						<?php 
						if (isset($task) && $task == 'Create Department Wise Employee PDF') {
							include_once '../classes/employeePDFClass.php';
							$pdf = new employeePDF();                               
							$pdf->createEmployeePDF($_POST);
						}
						?>
					</table>
				</fieldset>
			</form>
			<br></br>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="datewiseEventPDFForm" id="datewiseEventPDFForm" target="WEMS_REPORT2" onsubmit="return validateDatewiseEvent();">
                	<fieldset id="platformAssignmentReport">
					<legend><span class="bold_green_message">Platform Assignment Report For Data Range</span></legend>
					<table align="center" class="table grid123">
                            <tr>
						<th width="10%">Date From:</th>
								 <td width="40%"><input readonly type="text" name="reportFromDate" size="20" tabindex="24" id="reportFromDate" value=""/><img src="cal.gif" width="16" border="0" id="reportFromTime" alt="Click here to pick date" /></td>
								 <td width="10%"> To:</td>
								 <td width="40%"><input readonly type="text" name="reportToDate" size="20" tabindex="24" id="reportToDate" value=""/><img src="cal.gif" width="16" border="0" id="reportToTime" alt="Click here to pick date" /></td>
							</tr> 
							<tr>
							     <th>Location:</th>
								<td colspan="3"><select class="input-box" name="locationId" id="locationId"> <option value='0' selected="selected"> Select Location  </option>
								<?php
								$result = $locationObj->getLocationList();
								if($result){
									while($row = oci_fetch_array($result[0])){                                    
										$id = $row['MARKERID'];
										$desc = $row['MARKERNAME'];                                        
										echo "<option value=\"$id\" > $desc </option>";
									}
								}
								oci_free_statement($result[0]);
								?> 
							</select></td>
						</tr>
						<tr><td colspan="4" style="text-align: center;"><input type="submit" name="SUBMIT" id="SUBMIT" class="wideGreenBtn" value="Create Datewise Platform Assignment PDF" /></td></tr> 
                            
                            <?php 
                            if(isset($task) && $task == 'Create Datewise Platform Assignment PDF') {
                                include_once '../classes/eventPDFClass.php';
                                $pdf = new eventPDF();                                
                                $pdf->createDatewiseEventPDF($_POST);
                            }
                            ?>

        				</table>        
      			</fieldset>
      		</form>
        </div>
     
   <!--
     ************************************************************************************************************************************************
                                                                GIS
     ************************************************************************************************************************************************
     -->

		<div id="view5">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="gisform" id="gisform" >
				<center>
					<button class="wideGreenBtn" onclick="StationsMap('sta')">View Stations Map</button>
					<br /><br />
	               
					<br /><br />
					<button class="wideGreenBtn" onclick="StationsMap('sen')">View Sentinel Map</button>
					<br /><br />
				</center>
			</form>
		</div>
     
     <!--
     ************************************************************************************************************************************************
                                                        Make logout a button
     ************************************************************************************************************************************************
     -->
		<div style="background-color:#FFF2F2;" id="view6"> 
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="logoutform2" id="logoutform2" >
			</form>
		</div> 
     
    <!-- ____________________________________________________________________________________________________ --> 
    </div> <!-- end <div class="tabcontents"> -->
    </body>
</html>