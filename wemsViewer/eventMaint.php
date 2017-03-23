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

//converting prefix type to DB type
function getDBtype($pre){
	$type = "S";

	switch($pre){
		case "l":
			$type = "S";
			break; 
		case "i":
			$type = "I";
			break;
		case "pl":
			$type = "P";
			break;
	}

	return $type;
}

function rrLocationMenu($pre, $loc){
	global $c;
	
	$dbType = getDBtype($pre);
	
	$outStr = "<select name='{$pre}Loc' id='{$pre}Loc' onchange='getConponentData(\"$pre\"); getConponentDetails(\"$pre\"); getEmployees(\"$pre\");'>";
	//$outStr .= "<option value='0' selected='selected'>  </option>";
	$outStr .= "<option value='0'>  </option>";
	
	$qry = oci_parse($c, "SELECT MARKERID, MARKERNAME from WEMS_LOCATION where LOC_CD = '$dbType' order by MARKERNAME")
	OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	
	oci_execute($qry);
	
	while( ($row = oci_fetch_array($qry)) != false ){
		$id = $row['MARKERID'];
		$desc = $row['MARKERNAME'];
		if($id == $loc){
			$outStr .= "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
		}else{
			$outStr .= "<option value=\"$id\" > $desc </option>";
		}
	}
	
	$outStr .= "</select>";
	
	return $outStr;
}

function rrComponentFields($pre, $component){
	global $c;
	
	$dbType = getDBtype($pre);
	
	$outStr = "<select name='{$pre}Conponent' id='{$pre}Conponent' onchange='getConponentDetails(\"$pre\");getEmployees(\"$pre\");'>";
	 											
	$qry = oci_parse($c, "SELECT CTID, FULLNAME FROM WEMS_CLEANABLE_TARGET where MARKERID = :MARKERID and TYPE = '{$dbType}'")
	OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	 
	oci_bind_by_name($qry, ":MARKERID", $lLoc, -1);
	 
	oci_execute($qry);
	while( ($row = oci_fetch_array($qry)) != false ){
		$isSelected = '';
		
		if($component == $row[CTID]){
			$isSelected = 'selected=\"selected\"';
		}
		
		$outStr .= "<option value= \"$row[CTID]\" $isSelected> $row[FULLNAME] </option>";
	}
	
	$outStr .= "</select><input type='checkbox' name='allConponents' value='allConponents' id='{$pre}AllConponents' /> Apply to all conponents <br/>";
	
	return $outStr;
}

function statusMenu($pre, $status){
	global $c;
	
	$outStr = "<select name='{$pre}Status' id='{$pre}Status'>";

	$qry = oci_parse($c, "SELECT STATUSID, STATUS from WEMS_LOCATION_STATUS")
	OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
		
	oci_execute($qry);
		
	while (($row = oci_fetch_array($qry)) != false) {
		$id   = $row['STATUSID'];
		$desc = $row['STATUS'];
			
		if ($id == $status) {
			$outStr .= "<option value=\"$id\" selected=\"selected\"> $desc </option>";
		} else {
			$outStr .= "<option value=\"$id\"> $desc </option>";
		}
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
	<input readonly="readonly" type="text" name="{$startTmNm}" size="20" tabindex="24" id="{$startTmNm}" value="{$startTm}"/>
	<img src="cal.gif" width="16" border="0" id="{$calNm}" alt="Click here to pick date" />  
EOD;
	
	//hour box
	$outStr .= "<select name=\"{$pre}StartHr\" id=\"{$pre}StartHr\">";
	for ($x = 1; $x <= 12; $x++) {
		$tempSel = (!empty($hour) && trim($hour) == $x) ? "selected" : "";
	
		$outStr .= "<option value= \"$x\" $tempSel> $x </option>";
	}
	$outStr .= "</select>";
	
	//minute box
	$outStr .= " : <select name=\"{$pre}StartMin\" id=\"{$pre}StartMin\">";
	for ($x = 0; $x <= 59; $x++) {
		if($x < 10) $x = "0".  $x;
	
		$tempSel = (!empty($min) && trim($min) == $x) ? "selected" : "";
		$outStr .= "<option value= \"$x\" $tempSel> $x </option>";
	}
	$outStr .= "</select>";
	
	//am and pm box
	$outStr .= "<select name='{$pre}AmPm' id='{$pre}AmPm'>";
	$outStr .= "<option value='0' " . ((!empty($amPm) && trim($amPm) == "0") ? "selected" : "") . ">AM</option>";
	$outStr .= "<option value='1'" . ((!empty($amPm) && trim($amPm) == "1") ? "selected" : "") . ">PM</option>";
	$outStr .= "</select>";

	return $outStr;
}

function downloadFields($pre, $bType="CTID"){
	global $c;

	$outStr = "<select name='{$pre}DownloadFile' id='{$pre}DownloadFile'><option value='0' selected='selected'>  </option>";

	$qryDoc = oci_parse($c, "SELECT ID from WEMS_LOCDOCS where EVENTID = :EVENTID and MARKERID = :$bType")
	OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

	oci_bind_by_name($qryDoc, ":EVENTID", $eventID, -1);
	oci_bind_by_name($qryDoc, ":$bType", $lConponent, -1);

	oci_execute($qryDoc);

	while (($row = oci_fetch_array($qryDoc)) != false) {
		$outStr .= "<option value= \"$row[ID]\" > $row[ID] </option>";
	}

	$outStr .= "</select><input class='Download' type='submit' value='Download' name='SUBMIT' id='{$pre}SUBMIT' />";

	return $outStr;  
}

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
// $locationSuccessMsg = "";
$parkingLotErrMsg = "";
$parkingLotSuccessMsg = "";
$lStatus = "";
$gStartTm = "";
$lLoc = "";
$tabindex = 0;cd 

// missing vars newly added
$gEmpNum = 0;
$lPassNum = 0;
$iStatus = 0;
$returnPage = "eventMaint.php";
$forman = 0;
$supportDoc = "";
$plsupportDoc = "";
$isupportDoc = "";
$inactive = 600; //600 = 10 min

$lConponent="";
$iConponent="";
$plConponent="";

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

if($_SESSION['group'] != "WEMS_Admin"){
	session_destroy();
	header("Location: login.php?returnPage=$returnPage");
}else{
	require '../wemsDatabase.php';
	require_once('tcpdf/tcpdf.php');
	require_once '../classes/databaseClass.php';
	require_once '../classes/eventClass.php';
	require_once '../classes/gangClass.php';
	require_once '../classes/locationClass.php';
	require_once '../classes/cleanableTargetClass.php';

	$c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
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

	$task =  isset($_POST['SUBMIT']) ? $_POST['SUBMIT'] : false;

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
		$qry2 = oci_parse($c, "UPDATE WEMS_CLEANABLE_TARGET SET ASSIGNED_CREWSIZE = NULL, ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = 1, CT_PASSNUM = NULL, CT_BAGS = NULL")
		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');

		oci_execute($qry2);
		$qry3 = oci_parse($c, "UPDATE WEMS_LOCATION SET STATUS = 1, LOCATION_PASSNUM = NULL") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
		$qry4 = oci_parse($c, "UPDATE WEMS_EVENT SET CLOSETIME = NULL WHERE EVENTID = :EVENTID") OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c) , 1) . '</pre>');
		oci_bind_by_name($qry4, ":EVENTID", $eventID, -1);
		oci_execute($qry4);
		reOpen($eventID);
	}

	if ($task == "Assign Parking Lot") {
		$lPassNum = 0;
		$lNumBags = 0;
		$plLoc = isset($_POST['plLoc']) ? $_POST['plLoc'] : "";
		$plConponent = "";
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
		$plConponent = "";
		$Loc_Type = 'P';
		$filesToUpload = "";
		require_once ('assignLocation.php');

		$Loc_Type = 'P';

		updateLocationGang($lconponentl, $plConponent, $eventID, $lForman, $lNoteTime, $lStatus, $plLoc,
			$lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);

		require_once('uploadFile.php');
		uploadFile($eventID, $lLoc);
	} //if($task == "Assign Parking Lot")

	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
	if ($task == "Assign Interlocking") {
		$lLoc = isset($_POST['iLoc']) ? $_POST['iLoc'] : "";
		$lconponentl = "allConponents";
		$lConponent = "";
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
		
		$iConponent = isset($_POST['iConponent']) ? $_POST['iConponent'] : "";

		require_once ('assignLocation.php');

		$Loc_Type = 'I';
		updateLocationGang($lconponentl, $lConponent, $eventID, $lForman, $lNoteTime, $lStatus, $lLoc,
			$lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);

		require_once ('uploadFile.php');

		uploadFile($eventID, $lLoc);
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

if ($task == "Assign Location") {
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
			<img src="wemsPhoto.jpg" alt="Mountain View" style="float:right;height:42px;" />
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
			</ul> 
		</div>
    		<div class="tabcontents"> 
     <!--
     ************************************************************************************************************************************************
                                                    EVENT
     ************************************************************************************************************************************************
     -->
    			<div style="background-color:#FFF2F2;" id="view1"> 
      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
      				<fieldset id="event">
        				<legend>Event Maintenance </legend>

        				<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" >
						<?php echo getErScRows($eventErrMsg, $eventSuccessMsg); ?>

                      		<tr><th colspan = "2" align="center">Event</th></tr>
                      		
                      			<?php
                      			if($eventID > 0){
                      			    echo "<tr><td>Storm ID:</td> <td><input type=\"text\" name=\"sID\" value=\"$externalID\" readonly /></td></tr>";
                      			}
							?>
								<tr>
									<td>Storm Level:</td>
									<td><select name="eventType" id = "eventType">
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
									   echo"<td>Assigned By:</td><td><input type=\"text\" name=\"sAssigned\" value=\" $activeUser\" readonly></td>";
									}
									?>	
								</tr>
								<tr>
									<td>Start Date: </td>
									<td>
									<?php 
									$timeData = array( "startTm" => $openTime, "startTmNm" => "opentime", "calNm" => "startCalbutton" );
									echo dateTimeFields("open", $timeData);
									?>					
									</td>
								</tr>
								<tr>
									<td>Comments</td>
									<td><textarea rows="4" cols="50" name="sComments"></textarea></td>
								</tr>

								<?php
								echo "<tr>";
								if($eventID > 0){
									echo "<td colspan = \"1\" align=\"center\"><input class=\"Update Storm\" type=\"submit\" value=\"Update Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
									echo "<td colspan = \"1\" align=\"center\"><input class=\"Close Storm\" type=\"submit\" value=\"Close Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";
								}else{
									echo "<td colspan = \"2\" align=\"center\"><input class=\"Create Storm\" type=\"submit\" value=\"Create Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
								}
								?>
        				</table>
        				
        				<br></br>

        				<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" >
						<tr ><th colspan = "2" align="center">Storm History</th></tr>
												
						<tr>
							<td><textarea rows="10" cols="100">
							<?php 
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
							?>
							</textarea></td>
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

							$outStr .= "<td colspan = \"2\" align=\"center\"><input class=\"Re-Open Storm\" type=\"submit\" value=\"Re-Open Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
							
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
    	<div style="background-color:#FFF2F2;" id="view2"> 
     	<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
              	<table align="center" class="table" cellpadding="1" cellspacing="1" border="0">
              		
				<?php echo getErScRows($gangErrMsg, $gangSuccessMsg); ?>

			<tr><th colspan = "2" align="center">Create Gang</th></tr>
			<tr>
				<td>Foreman:</td>
				<td><select name="forman" id = "forman" onchange="getGangData()">
					<option value= "" >  </option>						
					<?php 
                                  $qry = oci_parse($c, "SELECT EMPLOYEENUMBER, FST_NME, LST_NME from WEMS_EMPLOYEE where DIV_CD = '1' order by LST_NME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPLOYEENUMBER'];
                                     $desc = $row['LST_NME'] . ", ". $row['FST_NME'];
									
										if($id == $forman)
										{
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										}
										else
										{
										echo "<option value= \"$id\" > $desc </option>";
										}
										
					}
					?> 
				</select></td>
			</tr>
			<tr>
				<td>Number Of Employees</td>
				<td>
					<select name="gEmpNum" id="gEmpNum">
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
				<td>Status</td>
				<td>
					<select name="gStatus" id="gStatus">
						<option value= "0" <?= (isset($_POST['gStatus']) && trim($_POST['gStatus']) == "0") ? "selected" : "" ?>>  </option>
						<option value= "1" <?= (isset($_POST['gStatus']) && trim($_POST['gStatus']) == "1") ? "selected" : "" ?>> Assigned </option>
						<option value= "2" <?= (isset($_POST['gStatus']) && trim($_POST['gStatus']) == "2") ? "selected" : "" ?>> Closed </option>
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
				<td>Start Date Time</td>
				<td>
					<?php 
					$timeData = array( "startTm" => $gStartTm, "startTmNm" => "gStartTm", "calNm" => "gangStartTm", "hour" => $gHour, "min" => $gMin, "amPm" => $gAmPm );
					echo dateTimeFields("g", $timeData);
					?>
				 </td>
			</tr>						
			<tr>
				<td>Comments</td> 
				<td><textarea rows="4" cols="50" name="gComments"></textarea></td>
			</tr>
			<?php
			
			//if($eventID > 0) echo"<tr><td colspan = \"2\" align=\"center\"><input class=\"Enter Gang\" type=\"submit\" value=\"Enter Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
			if($task == "Enter Gang") echo "<tr><td colspan = \"2\" align=\"center\"><input class=\"Update Gang\" type=\"submit\" value=\"Update Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
			else 
			    if($eventID > 0) echo "<tr><td colspan = \"2\" align=\"center\"><input class=\"Enter Gang\" type=\"submit\" value=\"Enter Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
			    
			?>
				</table>

        			<br></br>
        									
        			<table align="center" class="table" cellpadding="1" cellspacing="1" border="0">
					<tr><th colspan = "2" align="center">Gang History</th></tr>
					<tr>
						<td></td>
						<td><textarea rows="10" cols="100" name="gHistory" id="gHistory">
						<?php 
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
				</textarea></td>
			</tr>
			</table>
			<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" width = "80%">
			
			<?php 
					if($eventID > 0){
					    include 'getTotalByDepartment.php';
					    echo "<tr><td colspan=\"2\"> " . $output . "</td></tr>";
					}
       	               ?>
        			</table>
      		</form>
     	</div>
     	
     	<!--
     ************************************************************************************************************************************************
                                                        LOCATION

     ************************************************************************************************************************************************
     -->  
      <div style="background-color:#FFF2F2;" id="view3">
		<fieldset id="Assignment">
        		<legend>Assignments </legend>
    			<div id="content">
       			<div id="tab-container">
          			<ul id="tabs-titles"  class="content" data-persist="true">
	             			<li><a href="#lview0">Assign Gang To Station</a></li>
	    		 			<li><a href="#lview1">Assign Gangs To Interlocking</a></li>
	    		 			<li><a href="#lview2">Assign Gangs To Parking Lot</a></li>
             			</ul>
    				</div>
    				<div id="main-container">
    					<ul id="tabs-contents" style="list-style: none;">
    						<li>
    							<div id="lview0">
    								<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
    									<table align="center" class="table" cellpadding="1" cellspacing="1" border="0">
										<?php //echo getErScRows($locationErrMsg, $locationSuccessMsg); ?>
										<tr><th colspan = "2" align="center">location Maintenance</th></tr>
										<tr>
											<td>Location:</td>
											<td>
												<?php
												echo rrLocationMenu("l", $lLoc);
												?>
											</td>
										</tr>
										<tr>
											<td>Component:</td>
											<td>
												<?php echo rrComponentFields("l", $lConponent); ?>
											</td>
										</tr>
										<tr>
											<td>Gang:</td>
											<td>
												<select name="lForman" id="lForman">
												<?php
													 
													if(!empty($lConponent) && $lConponent != ""){
													    echo "<option value= \"\" >  </option>";
													    $ASSIGNED_SITEFORMEN = "";
	
	                                                            $qry2 = oci_parse($c, "select ASSIGNED_SITEFOREMEN from WEMS_CLEANABLE_TARGET WHERE MARKERID = :MARKERID and CTID = :CTID")
	                                                            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	
	                                                            oci_bind_by_name($qry2, "MARKERID",  $lLoc, -1);
	                                                            oci_bind_by_name($qry2, "CTID", $lConponent, -1);
	    
	                                                            oci_execute($qry2);
	    
	                                                            while( ($row = oci_fetch_array($qry2)) != false ) {
	                                                                $ASSIGNED_SITEFORMEN = $row['ASSIGNED_SITEFOREMEN'];
	                                                            }
	
		                                                       $qry = oci_parse($c, "select g.FORMANID, e.FST_NME, e.LST_NME, g.ASSIGN_LOC from WEMS_GANG g, WEMS_EMPLOYEE e where g.EVENTID = :EVENTID and g.FORMANID = e.EMPLOYEENUMBER ")
	                                                            OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
	
	                                  
	                                                            oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
	
	                                                            oci_execute($qry);
	
	                                                            while( ($row = oci_fetch_array($qry)) != false ){
	                                                                $assign_loc = $row['ASSIGN_LOC'];
	                                                                $forman = $row['FORMANID'];
	                                   
	                                                                if(($lLoc == $assign_loc) or ($assign_loc =="")){
	                                                                    if($ASSIGNED_SITEFORMEN == $forman){
	                                                                        echo "<option value= \"$forman\" selected=\"selected\"> $row[NAME] </option>";
	                                                                    }else{
	                                                                        echo "<option value= \"$forman\" > $row[NAME] </option>";
	                                                                    }
	                                                                }
	                                                            }		   
	                                                        }
	                                                        
												?>
                                   							</select>
													</td>
												</tr>
												<tr>
													<td>Status:</td>
													<td>
													<?php echo statusMenu("l", $lStatus); ?>
													</td>
												</tr>
												<tr> 
													<td>Pass Number:</td>
													<td>
														<select name="lPassNum" id = "lPassNum">
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
													<td># of Bags:</td>
													<td>
														<select name="lNumBags" id="lNumBags">
														<?php
														for ($x = 0; $x <= 40; $x++) {
														    echo "<option value= \"$x\"> $x </option>";
														}
														?>
														</select>
													</td>
												</tr>
												<tr>
													<td> Support Document:</td>
													<td>
														<input name="fileToUpload[]" id= "rDoc" size="75" type="file" multiple="multiple" value="<?= $supportDoc ?>" />
													</td>
												</tr>
												 <tr>
													<td>  Support Documents attached:</td>
													<td>
														<?php
														echo downloadFields("l", "CTID");
														?>
													</td>
												</tr>	
												<tr>
													<td>Date/Time</td>
													<td>
													<?php 
													$timeData = array( "startTmNm" => "lNoteTime", "calNm" => "locationStartTime" );
													echo dateTimeFields("sta", $timeData);
													?>
													</td>
												</tr>
												
												<tr>
													<td>Comments</td>
													<td><textarea rows="4" cols="50" name="lcomments"></textarea></td>
												</tr>
												
             									<?php
											if($eventID > 0) echo "<tr><td colspan = \"2\" align=\"center\"><input class=\"Assign Location\" type=\"submit\" value=\"Assign Location\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";
											?>
        									</table>
        									
        									<br></br>

        									<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" >
												<tr><th colspan = "2" align="center">Location History</th></tr>
												<tr>
													<td></td><td><textarea rows="10" cols="100" name="lHistory" id="lHistory">
													<?php
													$locComments = "";
													$qry = oci_parse($c, "SELECT TO_CHAR(WEMS_CLEANABLE_TARGET_NOTES.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME,
                                            WEMS_CLEANABLE_TARGET_NOTES.CTNOTES, EMPLOYEE.NAME, WEMS_CLEANABLE_TARGET_NOTES.CTSTATUS, 
                                            WEMS_CLEANABLE_TARGET_NOTES.CTPASSNUM, WEMS_CLEANABLE_TARGET_NOTES.CTBAGS, 
                                            WEMS_CLEANABLE_TARGET_NOTES.CTNOTEUSER
                                            FROM WEMS_CLEANABLE_TARGET_NOTES
                                            LEFT JOIN EMPLOYEE ON EMPLOYEE.EMPLOYEEID = WEMS_CLEANABLE_TARGET_NOTES.FORMANID 
                                            where WEMS_CLEANABLE_TARGET_NOTES.CTID = :CTID and 
                                            WEMS_CLEANABLE_TARGET_NOTES.EVENTID = :EVENTID and  
                                            ((WEMS_CLEANABLE_TARGET_NOTES.FORMANID = EMPLOYEE.EMPLOYEEID) or (WEMS_CLEANABLE_TARGET_NOTES.FORMANID = 0))ORDER BY ENTER_DATETIME")
       												OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       												oci_bind_by_name($qry, ":CTID", $lConponent, -1);
       												oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       												oci_execute($qry);

													while( ($row = oci_fetch_array($qry)) != false ){
       								    					$nNoteTime = $row['CTSTARTTIME'];
       								    					$nUser = $row['CTNOTEUSER'];
       								    					$nNote = $row['CTNOTES'];
       								    					$nForman = $row['NAME'];
       								    					$nBags = $row['CTBAGS'];
       								    					$nPass = $row['CTPASSNUM'];
       								    					$nStatus = $row['CTSTATUS'];
       								    
       									$locComments .= $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . ", Bags: " . $nBags . ", Pass #: " . $nPass . ", Status: " .  $nStatus . "\r\n";
       				                  

       								}
									   echo $locComments;				
									?>
													</textarea></td>
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
										<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform">
											<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" >
												<tr><th colspan = "2" align="center">INTERLOCKING Maintenance</th></tr>
												<tr>
													<td>Interlocking:</td>
													<td>
														<?php echo rrLocationMenu("i", $lLoc); ?>
													</td>
												</tr>

												<tr>
													<td>Component:</td>
													<td> <?php echo rrComponentFields("i", $iConponent); ?> </td>
												</tr>

												<tr>
													<td>Gang:</td>
													<td>
														<select name="iForman" id = "iForman" > 
															<option value="0" selected="selected">  </option>
	                                   						</select>
													</td>
												</tr>
												<tr>
													<td>Status:</td>
													<td>
													<?php echo statusMenu("i", $iStatus); ?>
													</td>
												</tr>
												<tr>
													<td> Support Document:</td>
													<td>
														<input name="fileToUpload[]" id= "rDoc" size="75" type="file" multiple="multiple" value="<?php echo $isupportDoc; ?>" />
													</td>
												</tr>
												
												<tr>
													<td>  Support Documents attached:</td>
													<td>
														<?php echo downloadFields("i", "MARKERID"); ?>
													</td>
												</tr>	
												
												<tr>
													<td>Date/Time</td>
													<td>
													<?php 
													$timeData = array( "startTmNm" => "iNoteTime", "calNm" => "interlockingStartTime" );
													echo dateTimeFields("i", $timeData);
													?>
													</td>
												</tr>
												
												<tr>
													<td>Comments</td>
													<td><textarea rows="4" cols="50" name="icomments"></textarea></td>
												</tr>
												
             									<?php
											if($eventID > 0) echo"<tr><td colspan = \"2\" align=\"center\"><input class=\"Assign Interlocking\" type=\"submit\" value=\"Assign Interlocking\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";
											?>
        									</table>
        									
        									<br></br>

        									<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" >
											<tr><th colspan="2" align="center">Interlocking History</th></tr>
											<tr>
												<td></td>
												<td><textarea rows="10" cols="100" name="iHistory" id="iHistory"></textarea></td>
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
        									<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
											<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" >
												<?php echo getErScRows($parkingLotErrMsg, $parkingLotSuccessMsg); ?>
				
												<tr><th colspan="2" align="center">Parking Lot Maintenance</th></tr>						
												<tr>
													<td>Parking Lots:</td>
													<td>
														<?php echo rrLocationMenu("pl", $lLoc); ?>
													</td>
												</tr>	

												<tr>
													<td>Component:</td>
													<td> <?php echo rrComponentFields("pl", $plConponent); ?> </td>
												</tr>
												<tr>
													<td>Gang:</td>
													<td>
														<select name="plForman" id="plForman"> 
															<option value="0" selected="selected">  </option>
														</select>
													</td>
												</tr>
												<tr>
													<td>Status:</td>
													<td>
														<?php echo statusMenu("pl", $iStatus); ?>
													</td>
												</tr>
												<tr>
													<td> Support Document:</td>
													<td> <input name="fileToUpload[]" id="rDoc" size="75" type="file" multiple="multiple" value="<?=$plsupportDoc ?>" /></td>
												</tr>
												<tr>
													<td>  Support Documents attached:</td>
													<td>
														<?php echo downloadFields("pl", "MARKERID"); ?>
													</td>
												</tr>	
												
												<tr>
													<td>Date/Time</td>
													<td>
													<?php 
													$timeData = array( "startTmNm" => "plNoteTime", "calNm" => "parkingLotStartTime" );
													echo dateTimeFields("pl", $timeData);
													?>									
													</td>
												</tr>
												<tr>
													<td>Comments</td>
													<td><textarea rows="4" cols="50" name="plcomments"></textarea></td>
												</tr>

             										<?php
												if($eventID > 0) echo "<tr><td colspan = \"2\" align=\"center\"><input class=\"Assign parking Lot\" type=\"submit\" value=\"Assign Parking Lot\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";

												?>
        									</table>
        									
        									<br></br>
        									
        									<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" >
											<tr><th colspan="2" align="center">Parking Lot History</th></tr>
											<tr>	
												<td></td>
												<td>
													<textarea rows="10" cols="100" name="plHistory" id="plHistory">
													<?php
													$plComments = "";
													$qry = oci_parse($c, "SELECT TO_CHAR(t.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME, t.CTNOTES, e.LST_NME, t.CTSTATUS, t.CTNOTEUSER
																	from WEMS_CLEANABLE_TARGET_NOTES t, WEMS_EMPLOYEE e
																	where CTID = :CTID and EVENTID = :EVENTID and t.FORMANID = e.EMPLOYEENUMBER")
       																OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       												oci_bind_by_name($qry, ":CTID", $plConponent, -1);
       												oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       												oci_execute($qry);

       												while( ($row = oci_fetch_array($qry)) != false ){
       								    					$nNoteTime = $row['CTSTARTTIME'];
       								    					$nUser = $row['CTNOTEUSER'];
       								    					$nNote = $row['CTNOTES'];
       								    					$nForman = $row['NAME'];
       								    										
       								    					$nStatus = $row['CTSTATUS'];
       								    
														$plComments .= $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . ", Status: " .  $nStatus . "\r\n";
       												}
       												echo $plComments;
       												?>
													</textarea>
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
		<div style="background-color:#FFF2F2;" id="view4">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" target="WEMS_REPORT" onsubmit="validateEventLocation();">
      			<fieldset id="reports">
        				<legend>Platform Assignment Report </legend>
        				<table align = "center" class="table" cellpadding="1" cellspacing="1" border="0" width="100%">
<tr>
							     <td>Event:</td>
								 <td><select name="eventId" id = "eventId" onChange="getLocationByEvent()"> <option value='0' selected> Select Event  </option>
								<?php 

                                $eventObj = new event();
                                $result = $eventObj->getEventList();
                                if($result){
                                    while($row = oci_fetch_array($result[0])){                                    
                                        $id = $row['EVENTID'];
                                        $desc = $row['EXTERNALID'];
    									echo "<option value=\"$id\" > $desc : $id </option>";
                                    }
                                }
                                oci_free_statement($result[0]);
				               ?> 
                                </select></td>
							</tr>
							<tr><td></td><td></td></tr>
                            <tr>
							     <td>Location:</td>
								 <td><select name="locationId[]" id = "locationId" multiple size ="1" width = "100%">
								 <option selected="selected">Select Location</option>
                                </select></td>
							</tr>
                            <tr><td colspan =1><input type="submit" name="SUBMIT" id="SUBMIT" value="Create Platform Assignment PDF" /></td></tr> 
						<?php 
						if(isset($task) && $task == 'create PDF') {
							include_once '../classes/eventPDFClass.php';
							$pdf = new eventPDF();
							$pdf->createEventPDF($_POST);
						}
						?>
        				</table>
      			</fieldset>
			</form>
			<br></br>
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="gangPDFForm" id="gangPDFForm" target="WEMS_REPORT2" onsubmit="validateEvent();">
				<fieldset id="departmentWiseReport">
					<legend>Department Wise Report</legend>    
					<table align="center" class="table" cellpadding="1" cellspacing="1" border="0" width=100%>
						<tr>
							<td>Event:</td>
							<td>
								<select name="eventId" id = "eventId"> <option value='0' selected> Select Event  </option>
								<?php 
								$eventObj = new event();
								$result = $eventObj->getEventList();
								if($result){
									while($row = oci_fetch_array($result[0])){                                    
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

                            <tr><td colspan =1><input type="submit" name="SUBMIT" id="SUBMIT" value="Create Department Wise Employee PDF" /></td></tr> 
                            
                            <?php 
                            if(isset($task) && $task == 'Create Department Wise Employee PDF') {
                                include_once '../classes/employeePDFClass.php';
                                $pdf = new employeePDF();                               
                                $pdf->createEmployeePDF($_POST);
                            }
                            ?>

        				</table>
      				</fieldset>
      			</form><br></br>
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="datewiseEventPDFForm" id="datewiseEventPDFForm" target="WEMS_REPORT2" onsubmit="validateDatewiseEvent();">
                <fieldset id="DateRangeReports">
        				<legend>Platform Assignment Report For Data Range</legend>
        				<table align = "center" class="table" border="0" width="100%">
                            <tr>
							     <td width="10%">Date From:</td>
								 <td width="40%"><input readonly type="text" name="reportFromDate" size="20" tabindex="24" id="reportFromDate" value=""/><img src="cal.gif" width="16" border="0" id="reportFromTime" alt="Click here to pick date" /></td>
								 <td width="10%"> To:</td>
								 <td width="40%"><input readonly type="text" name="reportToDate" size="20" tabindex="24" id="reportToDate" value=""/><img src="cal.gif" width="16" border="0" id="reportToTime" alt="Click here to pick date" /></td>
							</tr>							
							<tr><td></td><td></td></tr>
                            <tr>
							     <td>Location:</td>
								 <td><select name="locationId" id = "locationId"> <option value='0' selected> Select Location  </option>
								<?php                                
                                $locationObj = new location();
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
                            <tr><td colspan =1><input type="submit" name="SUBMIT" id="SUBMIT" value="Create Datewise Platform Assignment PDF" /></td></tr> 
                            
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
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
				<button onclick="StationsMap('sta')">Stations Map</button>
				<br></br>
				<button onclick="StationsMap('sen')">Sentinel Map</button>
				
			</form>
		</div>
     
     <!--
     ************************************************************************************************************************************************
                                                        Make logout a button
     ************************************************************************************************************************************************
     -->
		<div style="background-color:#FFF2F2;" id="view6"> 
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
			</form>
		</div> 
     
    <!-- ____________________________________________________________________________________________________ --> 
    </div> <!-- end <div class="tabcontents"> -->
    </body>
    <script language="JavaScript" type="text/javascript">
	window.tabindex = <?=$tabindex?>;
	window.eventId = <?=$eventID?>;
	</script>
</html>
