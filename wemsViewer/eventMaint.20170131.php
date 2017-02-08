<?php
  
//session_start(); //echo $_SESSION['group']

$eventErrMsg = "";
$gangErrMsg = "";
$locationErrMsg = "";


$eventSuccessMsg = "";
$gangSuccessMsg = "";
$interlockingErrMsg = "";
//$locationSuccessMsg = "";

$parkingLotErrMsg = "";
$parkingLotSuccessMsg = "";

$lStatus = "";
$gStartTm = "";
$lLoc = "";

$tabindex = 0;


$locationSuccessMsg = "";

$returnPage = "eventMaint.php";

session_start();

$inactive = 600;  //600 = 10 min

if(isset($_SESSION['timeout']) ) {

    $session_life = time() - $_SESSION['timeout'];

    if($session_life > $inactive)
    {
        session_destroy();
        header("Location: login.php?returnPage=$returnPage");
    }
}
$_SESSION['timeout'] = time();

 

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == false)
{
    header("Location: login.php?returnPage=$returnPage");
}

if(isset($_POST['Logout'])) { // logout

    session_destroy();
    header("Location: login.php?returnPage=$returnPage");
     

}

//$_SESSION['group'] = "WEMS_Admin";


if($_SESSION['group'] != "WEMS_Admin")
{
    session_destroy();
    header("Location: login.php?returnPage=$returnPage");
   
}
else 
{

    require '../wemsDatabase.php';
    
    $c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
    OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
    
    // Check if there is a storm open already
   
    $eventID = 0;
    $externalID = 0;
    $eventType = 0;
    $openTime = "";
    $activeUser = "";
    
    
   
    
    
    $qry = oci_parse($c, "select EVENTID, EXTERNALID, EVENTTYPE, TO_CHAR(OPENTIME, 'MM/DD/YYYY') as OPENTIME, OPENUSER from WEMS_EVENT where CLOSETIME is null")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
     	
    oci_execute($qry);
    
    while(($row = oci_fetch_array($qry)) !== false)
    {
        $eventID = $row['EVENTID'];
        $externalID = $row['EXTERNALID'];
        $eventType = $row['EVENTTYPE'];
        $openTime = $row['OPENTIME'];
        $activeUser = $row['OPENUSER'];
    }
    

 
    $task =  isset($_POST['SUBMIT']) ? $_POST['SUBMIT'] : false;
    
   
    
    if($task == "GIS")
    {
       
        //header("Location: http://arcgisupg.lirr.org/gisweb/wemsViewer/WEMS_GIS.html");
        header("Location: http://webz8dev.lirr.org/~tebert/wems/wemsViewer/WEMS_GIS.php");
    }
    
    
    //Create storm if there is not one already open. 
    
    //$task =  isset($_POST['SUBMIT']) ? $_POST['SUBMIT'] : false;
    
    
    
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    
    if($task == "Re-Open Storm")
    {
       
        $pastEvent = isset($_POST['pastStorms'])  ? $_POST['pastStorms'] : "";
        
        include 'openOldEvent.php';
        
        
        $qry = oci_parse($c, "select EVENTID, EXTERNALID, EVENTTYPE, TO_CHAR(OPENTIME, 'MM/DD/YYYY') as OPENTIME, OPENUSER from WEMS_EVENT where EVENTID = :EVENTID")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        oci_bind_by_name($qry, ":EVENTID",  $pastEvent, -1);
        oci_execute($qry);
        
        while(($row = oci_fetch_array($qry)) !== false)
        {
            $eventID = $row['EVENTID'];
            $externalID = $row['EXTERNALID'];
            $eventType = $row['EVENTTYPE'];
            $openTime = $row['OPENTIME'];
            $activeUser = $row['OPENUSER'];
        }
        
        
        $qry2 = oci_parse($c, "UPDATE WEMS_ABLE_TARGET SET ASSIGNED_CREWSIZE = NULL, ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = 1, CT_PASSNUM = NULL, CT_BAGS = NULL")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
       
        oci_execute($qry2);
        
        
        $qry3 = oci_parse($c, "UPDATE WEMS_LOCATION SET STATUS = 1, LOCATION_PASSNUM = NULL")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        $qry4 = oci_parse($c, "UPDATE WEMS_EVENT SET CLOSETIME = NULL WHERE EVENTID = :EVENTID")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        oci_bind_by_name($qry4, ":EVENTID",  $eventID, -1);
         
        oci_execute($qry4);
        
        
        
        reOpen($eventID);
        
      
        
        
        
    }
    
    if($task == "Assign Parking Lot")
    {
       
         
         $lPassNum = 0;
         $lNumBags = 0;
         
    
        $lLoc = isset($_POST['plLoc'])  ? $_POST['plLoc'] : "";
        $plConponent = "";
        $lForman = isset($_POST['plForman'])  ? $_POST['plForman'] : "";
        $lStatus = isset($_POST['plStatus'])  ? $_POST['plStatus'] : "";
    
    
        $lNoteTime = isset($_POST['plNoteTime'])  ? $_POST['plNoteTime'] : "";
        $lHour = isset($_POST['plStartHr'])  ? $_POST['plStartHr'] : "";
        $lMin = isset($_POST['plStartMin'])  ? $_POST['plStartMin'] : "";
        $lAmPm = isset($_POST['plStartAmPm'])  ? $_POST['plStartAmPm'] : "";
        if($lAmPm == 0)$lAmPm = "AM";else $lAmPm = "PM";
        $lNoteTime = $lNoteTime . " " . $lHour . ":" . $lMin . " " . $lAmPm;
    
    
        $lEndTime = isset($_POST['plEndTime'])  ? $_POST['plEndTime'] : "";
        $lcomments = isset($_POST['plcomments'])  ? $_POST['plcomments'] : "";
    
    
        $lUser = $_SESSION['user'];
    
    
        $interlockingErrMsg = $plConponent;
        
        $lconponentl = "allConponents";

        $plConponent = "";
       
        $Loc_Type = 'P';
         
         
        $filesToUpload = "";
         
        
        require_once('assignLocation.php');
         
        $Loc_Type = 'P';
         
        updateLocationGang($lconponentl, $plConponent, $eventID, $lForman, $lNoteTime, $lStatus, $lLoc,
            $lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);
         
        
        require_once('uploadFile.php');
        uploadFile($eventID, $lLoc);
        
        
        
        
        
        
        
        
        
    
    /*
        $qry = oci_parse($c, "update WEMS_ABLE_TARGET SET NOTIFYTIME = to_date(:NOTIFYTIME, 'mm/dd/yyyy hh:mi AM'),
         ASSIGNED_SITEFOREMEN = :ASSIGNED_SITEFOREMEN, CT_STATUS = :CT_STATUS, CT_PASSNUM = :CTPASSNUM, CT_BAGS = :CTBAGS
         WHERE CTID = :CTID ")
             OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
             oci_bind_by_name($qry, ":NOTIFYTIME",  $lNoteTime, -1);
             oci_bind_by_name($qry, ":CT_STATUS",  $lStatus, -1);
             oci_bind_by_name($qry, ":ASSIGNED_SITEFOREMEN", $lForman , -1);
             oci_bind_by_name($qry, ":CTID", $plConponent, -1);
             oci_bind_by_name($qry, ":CTPASSNUM", $lPassNum, -1);
             oci_bind_by_name($qry, ":CTBAGS", $lNumBags, -1);
    
    
             oci_execute($qry);
    
             //Notes
    
             $qry = oci_parse($c, "insert into WEMS_ABLE_TARGET_NOTES (EVENTID, CTID, CTNOTES, FORMANID, CTSTATUS, CTPASSNUM, CTBAGS, CTSTARTTIME, CTNOTEUSER, ENTER_DATETIME)
         VALUES(:EVENTID, :CTID, :CTNOTES, :FORMANID, :CTSTATUS, :CTPASSNUM, :CTBAGS, to_date(:CTSTARTTIME, 'mm/dd/yyyy hh:mi AM'), :CTNOTEUSER, SYSDATE) ")
             OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    
             oci_bind_by_name($qry, ":EVENTID",  $eventID, -1);
             oci_bind_by_name($qry, ":CTID",  $plConponent, -1);
             oci_bind_by_name($qry, ":CTNOTES", $lcomments , -1);
             oci_bind_by_name($qry, ":FORMANID", $lForman, -1);
             oci_bind_by_name($qry, ":CTSTATUS",  $lStatus, -1);
             oci_bind_by_name($qry, ":CTPASSNUM",  $lPassNum, -1);
             oci_bind_by_name($qry, ":CTBAGS", $lNumBags , -1);
             oci_bind_by_name($qry, ":CTSTARTTIME", $lNoteTime, -1);
             oci_bind_by_name($qry, ":CTNOTEUSER", $lUser, -1);
    
    
             oci_execute($qry);
    
    
    
    
             //Once a gang is assigned Add a Note to the WEMS_GANG_NOTES
    
    
    
             $addGangNoteQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, FORMANID, NOTETIME, NOTEUSER, EVENTUPDATE, ENTER_DATETIME, ASSIGN_LOC)
         VALUES(:EVENTID, :FORMANID, sysdate, :NOTEUSER, 'Gang Assigned', sysdate, :ASSIGN_LOC ) ")
             OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    
             oci_bind_by_name( $addGangNoteQry, ":EVENTID",  $eventID, -1);
             oci_bind_by_name( $addGangNoteQry, ":FORMANID", $lForman, -1);
             oci_bind_by_name( $addGangNoteQry, ":NOTEUSER", $lUser, -1);
             oci_bind_by_name( $addGangNoteQry, ":ASSIGN_LOC",  $plConponent, -1);
    
             oci_execute($addGangNoteQry);
    
    
             //if the gang was unassigned then remove the location in WEMS_GANG
             $qry = oci_parse($c, "update WEMS_GANG Set ASSIGN_LOC = null where ASSIGN_LOC = :ASSIGNLOC AND EVENTID = :EVENTID")
             OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
             oci_bind_by_name($qry, ":ASSIGNLOC",  $plConponent, -1);
             //oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
             oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
    
             oci_execute($qry);
    
             //Mark the Gang as assigned
             $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = :ASSIGNLOC WHERE FORMANID = :FORMANID and EVENTID = :EVENTID")
             OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
             oci_bind_by_name($qry, ":ASSIGNLOC",  $plConponent, -1);
             oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
             oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
    
             oci_execute($qry);
    
             if($lStatus == 4)
             {
                 $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = NULL WHERE EVENTID = :EVENTID and FORMANID = :FORMANID")
                 OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
                 oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
                 oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
    
                 oci_execute($qry);
             }
    
    
    
    
             $tabindex = 2;
    
    */
    
    }//if($task == "Assign Parking Lot")
    
    
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    
    
    
    
    
    
    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    
    
    
    if($task == "Assign Interlocking")
    {
        //$interlockingErrMsg = "HELP";
    
        
         $lLoc = isset($_POST['iLoc'])  ? $_POST['iLoc'] : "";
        // $iConponent = isset($_POST['iConponent'])  ? $_POST['iConponent'] : "";
        
         $lconponentl = "allConponents";
         $lConponent = "";
         $lPassNum = 0;
         $lNumBags = 0;
         $lForman = isset($_POST['iForman'])  ? $_POST['iForman'] : "";
         $lStatus = isset($_POST['iStatus'])  ? $_POST['iStatus'] : "";
    
    
         $lNoteTime = isset($_POST['iNoteTime'])  ? $_POST['iNoteTime'] : "";
         $lHour = isset($_POST['iStartHr'])  ? $_POST['iStartHr'] : "";
         $lMin = isset($_POST['iStartMin'])  ? $_POST['iStartMin'] : "";
         $lAmPm = isset($_POST['iStartAmPm'])  ? $_POST['iStartAmPm'] : "";
         if($lAmPm == 0)$lAmPm = "AM";else $lAmPm = "PM";
         $lNoteTime = $lNoteTime . " " . $lHour . ":" . $lMin . " " . $lAmPm;
    
    
         $lEndTime = isset($_POST['iEndTime'])  ? $_POST['iEndTime'] : "";
         $lcomments = isset($_POST['icomments'])  ? $_POST['icomments'] : "";
          
         $Loc_Type = 'I';
         
    
    
         //$interlockingErrMsg = $iConponent;

         $lUser = $_SESSION['user'];
         
         
         $filesToUpload = "";
         

         require_once('assignLocation.php');
         
         $Loc_Type = 'I';
         
         updateLocationGang($lconponentl, $lConponent, $eventID, $lForman, $lNoteTime, $lStatus, $lLoc,
             $lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);
         
          
         require_once('uploadFile.php');
         uploadFile($eventID,  $lLoc);
         
         
         
         
         
         
    
         /* 
         $qry = oci_parse($c, "update WEMS_ABLE_TARGET SET NOTIFYTIME = to_date(:NOTIFYTIME, 'mm/dd/yyyy hh:mi AM'),
         ASSIGNED_SITEFOREMEN = :ASSIGNED_SITEFOREMEN, CT_STATUS = :CT_STATUS, CT_PASSNUM = :CTPASSNUM, CT_BAGS = :CTBAGS
         WHERE CTID = :CTID ")
         OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
         oci_bind_by_name($qry, ":NOTIFYTIME",  $lNoteTime, -1);
         oci_bind_by_name($qry, ":CT_STATUS",  $lStatus, -1);
         oci_bind_by_name($qry, ":ASSIGNED_SITEFOREMEN", $lForman , -1);
         oci_bind_by_name($qry, ":CTID", $iConponent, -1);
         oci_bind_by_name($qry, ":CTPASSNUM", $lPassNum, -1);
         oci_bind_by_name($qry, ":CTBAGS", $lNumBags, -1);
    
    
         oci_execute($qry);
          
         //Notes
          
         $qry = oci_parse($c, "insert into WEMS_ABLE_TARGET_NOTES (EVENTID, CTID, CTNOTES, FORMANID, CTSTATUS, CTPASSNUM, CTBAGS, CTSTARTTIME, CTNOTEUSER, ENTER_DATETIME)
         VALUES(:EVENTID, :CTID, :CTNOTES, :FORMANID, :CTSTATUS, :CTPASSNUM, :CTBAGS, to_date(:CTSTARTTIME, 'mm/dd/yyyy hh:mi AM'), :CTNOTEUSER, SYSDATE) ")
         OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
          
    
         oci_bind_by_name($qry, ":EVENTID",  $eventID, -1);
         oci_bind_by_name($qry, ":CTID",  $iConponent, -1);
         oci_bind_by_name($qry, ":CTNOTES", $lcomments , -1);
         oci_bind_by_name($qry, ":FORMANID", $lForman, -1);
         oci_bind_by_name($qry, ":CTSTATUS",  $lStatus, -1);
         oci_bind_by_name($qry, ":CTPASSNUM",  $lPassNum, -1);
         oci_bind_by_name($qry, ":CTBAGS", $lNumBags , -1);
         oci_bind_by_name($qry, ":CTSTARTTIME", $lNoteTime, -1);
         oci_bind_by_name($qry, ":CTNOTEUSER", $lUser, -1);
    
          
         oci_execute($qry);
          
          
          
          
         //Once a gang is assigned Add a Note to the WEMS_GANG_NOTES
    
          
          
         $addGangNoteQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, FORMANID, NOTETIME, NOTEUSER, EVENTUPDATE, ENTER_DATETIME, ASSIGN_LOC)
         VALUES(:EVENTID, :FORMANID, sysdate, :NOTEUSER, 'Gang Assigned', sysdate, :ASSIGN_LOC ) ")
         OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
          
         oci_bind_by_name( $addGangNoteQry, ":EVENTID",  $eventID, -1);
         oci_bind_by_name( $addGangNoteQry, ":FORMANID", $lForman, -1);
         oci_bind_by_name( $addGangNoteQry, ":NOTEUSER", $lUser, -1);
         oci_bind_by_name( $addGangNoteQry, ":ASSIGN_LOC",  $iConponent, -1);
    
         oci_execute($addGangNoteQry);
          
    
         //if the gang was unassigned then remove the location in WEMS_GANG
          
         $qry = oci_parse($c, "update WEMS_GANG Set ASSIGN_LOC = null where ASSIGN_LOC = :ASSIGNLOC AND EVENTID = :EVENTID")
         OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
         oci_bind_by_name($qry, ":ASSIGNLOC",  $iConponent, -1);
         //oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
         oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
    
         oci_execute($qry);
    
         //Mark the Gang as assigned
         $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = :ASSIGNLOC WHERE FORMANID = :FORMANID and EVENTID = :EVENTID")
         OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
          
         oci_bind_by_name($qry, ":ASSIGNLOC",  $iConponent, -1);
         oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
         oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
          
         oci_execute($qry);
          
         if($lStatus == 4)
         {
         $qry = oci_parse($c, "update WEMS_GANG SET ASSIGN_LOC = NULL WHERE EVENTID = :EVENTID and FORMANID = :FORMANID")
         OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
         oci_bind_by_name($qry, ":FORMANID",   $lForman, -1);
         oci_bind_by_name($qry, ":EVENTID",   $eventID, -1);
    
         oci_execute($qry);
         }
    
    
          
         $tabindex = 1;
        
    */
    
    
    }//if($task == "Assign Location")
    

    //%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%
    


    if($task == "Download")
    {
       
        $selectedFile = isset($_POST['downloadFile'])  ? $_POST['downloadFile'] : "";
        $lConponent = isset($_POST['lConponent'])  ? $_POST['lConponent'] : "";
    
        $locationSuccessMsg = "ID: " . $selectedFile . ", CONPONENT: " . $lConponent . ", EVENT: " . $eventID;
        
        //echo $selectedFile;
    
    
    
   
        //include('config.php');
    
        //echo $ID;
        $dlqry = oci_parse($c, "SELECT BLOB_COL, ID FROM WEMS_LOCDOCS WHERE ID = :ID AND EVENTID = :EVENTID AND MARKERID = :MARKERID")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
       
    
        oci_bind_by_name($dlqry, ":ID",  $selectedFile, -1);
        oci_bind_by_name($dlqry, ":EVENTID",  $eventID, -1);
        oci_bind_by_name($dlqry, ":MARKERID",  $lConponent, -1);
        
        oci_execute($dlqry);
    
        while($row = oci_fetch_array($dlqry)){
    
         
            $id =  $row['ID'];
            $blob = $row['BLOB_COL']->load();
    
  
    
    
            $tmp = explode(".",$id);
    
            switch ($tmp[count($tmp)-1])
            {
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
            header("Cache-Control: private",false); // required for certain browsers
            //header("Content-Type: application/msword");
            header("Content-type: $ctype");
            header("Content-Type: application/force-download");
            header("Content-Type: application/octet-stream");
            header("Content-Type: application/download");;
            header("Content-Disposition: attachment; filename=".$id );
            header("Content-Transfer-Encoding: binary");
            //header("Content-Length: ".filesize("upload_doc/$id")); //??
    
            ob_();
            flush();
    
            echo $blob;
    
    
    
            exit;
    
    
        }
    
    }
    
    if($task == "Create Storm")
    {
        
        
        $stormID = 0;
        $externalID = 0;
        $eventType = isset($_POST['eventType'])  ? $_POST['eventType'] : "";
        
        
        
        $opentime = isset($_POST['opentime'])  ? $_POST['opentime'] : "";
        
        
        $openHour = isset($_POST['openStartHr'])  ? $_POST['openStartHr'] : "";
        $openMin = isset($_POST['openStartMin'])  ? $_POST['openStartMin'] : "";
        $openAmPm = isset($_POST['openAmPm'])  ? $_POST['openAmPm'] : "";
        if($openAmPm == 0)$openAmPm = "AM";else $openAmPm = "PM";
        $opentime = $opentime . " " . $openHour . ":" . $openMin . " " . $openAmPm;
        
        
        $yr = 0;
        $noteUpdate = isset($_POST['sComments'])  ? $_POST['sComments'] : "";
        if($noteUpdate == "") $noteUpdate ="Storm Open";
        
        if($eventType == 0)$eventErrMsg .= "<li>Please enter an Event Type</li>";
        if($opentime == "")$eventErrMsg .= "<li>Please a enter a Date for this event</li>";
        
        
        
        if(!strlen($eventErrMsg))
        {
            	
        
        $qrySI = oci_parse($c, "select MAX(EVENTID) as MAXNUM, to_CHAR(SYSDATE, 'YYYYMMDD') as DT, to_CHAR(SYSDATE, 'YYYY') as YR from WEMS_EVENT")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        oci_execute($qrySI);
        
        while($row = oci_fetch_array($qrySI))
        {
             $stormID = $row['MAXNUM'];
             $externalID = $row['DT'];
             $yr = $row['YR'];
        }
        
       //if($stormID == null)$stormID=1;
        
        $stormID =  $stormID + 1;
      
        $user = $_SESSION['user'];
        
     
        $createQry = oci_parse($c, "insert into WEMS_EVENT (EVENTID, EXTERNALID, EVENTTYPE, OPENTIME, EVENTYEAR, OPENUSER)
							VALUES (:EVENTID, :EXTERNALID, :EVENTTYPE, to_date(:OPENTIME, 'mm/dd/yyyy hh:mi am'), :EVENTYEAR, :OPENUSER)")
        							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        							oci_bind_by_name($createQry, ":EVENTID",  $stormID, -1);
        							oci_bind_by_name($createQry, ":EXTERNALID", $externalID, -1);
        							oci_bind_by_name($createQry, ":EVENTTYPE", $eventType, -1);
        							oci_bind_by_name($createQry, ":OPENTIME", $opentime, -1);
        							oci_bind_by_name($createQry, ":EVENTYEAR", $yr, -1);
        							oci_bind_by_name($createQry, ":OPENUSER", $user, -1);
        
        oci_execute($createQry);

        
        $eventID = $stormID;
        $activeUser = $user;
        //$openTime = $opentime;
        
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
        							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        								
        							
        							oci_execute($openQry);
        							
        							
        $openQry2 = oci_parse($c, "Update WEMS_LOCATION set LOCATION_PASSNUM = NULL, STATUS = 1")
        							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        							
        							 
        							oci_execute($openQry2);
        							
        							$successMsg = "Success";
        							
        							
        } //if(!strlen($errMsg)) 							
     							
        							
        							
    }
    
    
    
    if($task == "Update Storm")
    {   
        
        $eventType = isset($_POST['eventType'])  ? $_POST['eventType'] : "";
        $noteUpdate = isset($_POST['sComments'])  ? $_POST['sComments'] : "";
        $user = $_SESSION['user'];
        
        
        
        
        
        $opentime = isset($_POST['opentime'])  ? $_POST['opentime'] : "";
        
        
        
        $openHour = isset($_POST['openStartHr'])  ? $_POST['openStartHr'] : "";
        $openMin = isset($_POST['openStartMin'])  ? $_POST['openStartMin'] : "";
        $openAmPm = isset($_POST['openAmPm'])  ? $_POST['openAmPm'] : "";
        if($openAmPm == 0)$openAmPm = "AM";else $openAmPm = "PM";
        $opentime = $opentime . " " . $openHour . ":" . $openMin . " " . $openAmPm;
        
        if($eventType == 0)$eventErrMsg .= "<li>Please enter an Event Type</li>";
        
        
        
        if(!strlen($eventErrMsg))
        {
        
        
        
       
        
        $updateQry = oci_parse($c, "insert into WEMS_EVENT_NOTES (EVENTID, EVENTTYPE, NOTETIME, NOTEUSER, EVENTUPDATE, ENTER_DATETIME)
							VALUES (:EVENTID, :EVENTTYPE, sysdate, :NOTEUSER, :EVENTUPDATE, to_date(:ENTER_DATETIME, 'mm/dd/yyyy HH:MI PM'))")
        							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        							oci_bind_by_name($updateQry, ":EVENTID",  $eventID, -1);
        							oci_bind_by_name($updateQry, ":EVENTTYPE", $eventType, -1);
        							oci_bind_by_name($updateQry, ":NOTEUSER", $user, -1);
        							oci_bind_by_name($updateQry, ":EVENTUPDATE", $noteUpdate, -1);
        							oci_bind_by_name($updateQry, ":ENTER_DATETIME", $opentime, -1);
        
        
        							oci_execute($updateQry);
        							
        							
        $updateQry2 = oci_parse($c, "update WEMS_EVENT SET EVENTTYPE = :EVENTTYPE, OPENUSER = :NOTEUSER, OPENTIME = to_date(:OPENTIME, 'mm/dd/yyyy HH:MI PM') where EVENTID  = :EVENTID")
        														OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        							
        														oci_bind_by_name($updateQry2, ":EVENTID",  $eventID, -1);
        														oci_bind_by_name($updateQry2, ":EVENTTYPE", $eventType, -1);
        														oci_bind_by_name($updateQry2, ":NOTEUSER", $user, -1);
        														oci_bind_by_name($updateQry2, ":OPENTIME", $opentime, -1);
        							                             
        							
        														oci_execute($updateQry2);
        							
        							
        							
        							
        							$eventSuccessMsg = "Event has been updated";
        							
        }// if(!strlen($errMsg))
  
    }
    
    
    if($task == "Close Storm")
    {
        
        $eventErrMsg = "";
        
        $eventType = isset($_POST['eventType'])  ? $_POST['eventType'] : "";
        $noteUpdate = isset($_POST['sComments'])  ? $_POST['sComments'] : "";
        if($noteUpdate == "") $noteUpdate ="Storm Closed";
        $user = $_SESSION['user'];
        
        
        
        
        
        
        if(!strlen($eventErrMsg))
        {
         
           
    
        $updateQry = oci_parse($c, "insert into WEMS_EVENT_NOTES (EVENTID, EVENTTYPE, NOTETIME, NOTEUSER, EVENTUPDATE)
							VALUES (:EVENTID, :EVENTTYPE, sysdate, :NOTEUSER, :EVENTUPDATE)")
    							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    							oci_bind_by_name($updateQry, ":EVENTID",  $eventID, -1);
    							oci_bind_by_name($updateQry, ":EVENTTYPE", $eventType, -1);
    							oci_bind_by_name($updateQry, ":NOTEUSER", $user, -1);
    							oci_bind_by_name($updateQry, ":EVENTUPDATE", $noteUpdate, -1);
    
    
    							oci_execute($updateQry);
    
    
    	$closeQry = oci_parse($c, "Update WEMS_EVENT set CLOSETIME = sysdate, CLOSEUSER = :CLOSEUSER where EVENTID = :EVENTID")
    						OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    							
    						oci_bind_by_name($closeQry, ":CLOSEUSER", $user, -1);
    						oci_bind_by_name($closeQry, ":EVENTID", $eventID, -1);
    						oci_execute($closeQry);
    						
    						
    	$updateQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, NOTETIME, NOTEUSER, EVENTUPDATE)
							VALUES (:EVENTID, sysdate, :NOTEUSER, :EVENTUPDATE)")
    							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    							oci_bind_by_name($updateQry, ":EVENTID",  $eventID, -1);
    							oci_bind_by_name($updateQry, ":NOTEUSER", $user, -1);
    							oci_bind_by_name($updateQry, ":EVENTUPDATE", $noteUpdate, -1);
    
    
    							oci_execute($updateQry);
    							
    	$closeQry = oci_parse($c, "Update WEMS_GANG set CLOSETIME = sysdate, CLOSEUSER = :CLOSEUSER where EVENTID = :EVENTID")
    							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    								
    							oci_bind_by_name($closeQry, ":CLOSEUSER", $user, -1);
    							oci_bind_by_name($closeQry, ":EVENTID", $eventID, -1);
    							
    							oci_execute($closeQry);
			
    							
    	$closeCTQry = oci_parse($c, "Update WEMS_CLEANABLE_TARGET_NOTES set CTENDTIME = sysdate, CTNOTEUSER = :CLOSEUSER where EVENTID = :EVENTID AND CTENDTIME is NULL")
    							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    							
    							oci_bind_by_name($closeCTQry, ":CLOSEUSER", $user, -1);
    							oci_bind_by_name($closeCTQry, ":EVENTID", $eventID, -1);
    								
    							oci_execute($closeCTQry);
    							/*
    							
    	$closeLocQry = oci_parse($c, "Update WEMS_CLEANABLE_TARGET set NOTIFYTIME = NULL, ASSIGNED_CREWSIZE = NULL, ASSIGNED_SITEFOREMEN = NULL, CT_STATUS = NULL, CT_PASSNUM = NULL, CT_BAGS = NULL")
    							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    								
    							
    							oci_execute($closeLocQry);
    							*/
    
    						$eventID = 0;
    						$eventType = 0;
    						$openTime = "";
    						
    						$EventSuccessMsg = "Success";
    						
        }// if(!strlen($errMsg))
    }
    
    
    if($task == "Enter Gang")
    {
    
        
        
        $forman = isset($_POST['forman'])  ? $_POST['forman'] : "";
        $gEmpNum = isset($_POST['gEmpNum'])  ? $_POST['gEmpNum'] : "";
        $gStartTm = isset($_POST['gStartTm'])  ? $_POST['gStartTm'] : "";
        
        
        $gUser = $_SESSION['user'];
        
        $gangErrMsg = "";
        
        if($forman == 0)$gangErrMsg .= "<li>Please enter a Forman</li>";
        if($gStartTm == "")$gangErrMsg .= "<li>Please enter a start date</li>";
     
        
                
        
        $gHour = isset($_POST['gStartHr'])  ? $_POST['gStartHr'] : "";
        $gMin = isset($_POST['gStartMin'])  ? $_POST['gStartMin'] : "";
        $gAmPm = isset($_POST['gAmPm'])  ? $_POST['gAmPm'] : "";
        if($gAmPm == 0)$gAmPm = "AM";else $gAmPm = "PM";
        $gStartTm = $gStartTm . " " . $gHour . ":" . $gMin . " " . $gAmPm;
        $gEndTm = isset($_POST['gEndTm'])  ? $_POST['gEndTm'] : "";
        $gComments = isset($_POST['gComments'])  ? $_POST['gComments'] : "";
        if($gComments == "") $gComments ="Gang Created";
        
        
       
        
        
        
        if(!strlen($gangErrMsg))
        {
        
        
        
    
        $qry = oci_parse($c, "insert into WEMS_GANG (EVENTID, FORMANID, EMP_ASSIGNED, OPENTIME, OPENUSER)
							VALUES (:EVENTID, :FORMANID, :EMP_ASSIGNED, to_date(:OPENTIME, 'mm/dd/yyyy HH:MI AM'), :OPENUSER)")
    							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    							oci_bind_by_name($qry, ":EVENTID",  $eventID, -1);
    							oci_bind_by_name($qry, ":FORMANID", $forman, -1);
    							oci_bind_by_name($qry, ":EMP_ASSIGNED", $gEmpNum , -1);
    							oci_bind_by_name($qry, ":OPENTIME", $gStartTm, -1);
    							//oci_bind_by_name($qry, ":CLOSETIME", $gEndTm, -1);
    							oci_bind_by_name($qry, ":OPENUSER", $gUser, -1);
    
    
    							oci_execute($qry);
    							
    	$updateQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, FORMANID, NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED, ENTER_DATETIME)
							VALUES (:EVENTID, :FORMANID, to_date(:NOTETIME, 'mm/dd/yyyy HH:MI AM'), :NOTEUSER, :EVENTUPDATE, :EMP_ASSIGNED, SYSDATE)")
    														OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    							
    							oci_bind_by_name($updateQry, ":EVENTID",  $eventID, -1);
    							oci_bind_by_name($updateQry, ":FORMANID", $forman, -1);
    							oci_bind_by_name($updateQry, ":NOTETIME", $gStartTm, -1);
    							oci_bind_by_name($updateQry, ":NOTEUSER", $gUser, -1);
    							oci_bind_by_name($updateQry, ":EVENTUPDATE", $gComments, -1);
    							oci_bind_by_name($updateQry, ":EMP_ASSIGNED", $gEmpNum, -1);
    
    							oci_execute($updateQry);
    							
    							$successMsg = "Success";
        }// if(!strlen($errMsg))
            
        
       
        
    }
    
    
    if($task == "Update Gang")
    {
        
        
        $forman = isset($_POST['forman'])  ? $_POST['forman'] : "";
        $gEmpNum = isset($_POST['gEmpNum'])  ? $_POST['gEmpNum'] : "";
        $gStartTm = isset($_POST['gStartTm'])  ? $_POST['gStartTm'] : "";
        $gHour = isset($_POST['gStartHr'])  ? $_POST['gStartHr'] : "";
        $gMin = isset($_POST['gStartMin'])  ? $_POST['gStartMin'] : "";
        $gAmPm = isset($_POST['gAmPm'])  ? $_POST['gAmPm'] : "";
        if($gAmPm == 0)$gAmPm = "AM";else $gAmPm = "PM";
        $gStartTm = $gStartTm . " " . $gHour . ":" . $gMin . " " . $gAmPm;
        
        
       // $gEndTm = isset($_POST['gEndTm'])  ? $_POST['gEndTm'] : "";
        $gComments = isset($_POST['gComments'])  ? $_POST['gComments'] : "";
        if($gComments == "") $gComments ="Gang Created";
        
        $gUser = $_SESSION['user'];
        
        
        $gangErrMsg = "";
        
        
        
        if(!strlen($gangErrMsg))
        {
        
        
        $qry = oci_parse($c, "Update WEMS_GANG SET EVENTID = :EVENTID, FORMANID = :FORMANID, EMP_ASSIGNED = :EMP_ASSIGNED, OPENTIME = to_date(:OPENTIME, 'mm/dd/yyyy HH:MI AM'), 
                                        OPENUSER = :OPENUSER where EVENTID = :EVENTID and FORMANID = :FORMANID")
        							OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        							oci_bind_by_name($qry, ":EVENTID",  $eventID, -1);
        							oci_bind_by_name($qry, ":FORMANID", $forman, -1);
        							oci_bind_by_name($qry, ":EMP_ASSIGNED", $gEmpNum , -1);
        							oci_bind_by_name($qry, ":OPENTIME", $gStartTm, -1);
        							//oci_bind_by_name($qry, ":CLOSETIME", $gEndTm, -1);
        							oci_bind_by_name($qry, ":OPENUSER", $gUser, -1);
                                    oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
                                    oci_bind_by_name($qry, ":FORMANID", $forman, -1);
            
        							oci_execute($qry);
        							
        							
        $updateQry = oci_parse($c, "insert into WEMS_GANG_NOTES (EVENTID, FORMANID, NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED, ENTER_DATETIME)
							VALUES (:EVENTID, :FORMANID, to_date(:NOTETIME, 'mm/dd/yyyy HH:MI AM'), :NOTEUSER, :EVENTUPDATE, :EMP_ASSIGNED, SYSDATE)")
        														OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        															
        														oci_bind_by_name($updateQry, ":EVENTID",  $eventID, -1);
        														oci_bind_by_name($updateQry, ":FORMANID", $forman, -1);
        														oci_bind_by_name($updateQry, ":NOTETIME", $gStartTm, -1);
        														oci_bind_by_name($updateQry, ":NOTEUSER", $gUser, -1);
        														oci_bind_by_name($updateQry, ":EVENTUPDATE", $gComments, -1);
        														oci_bind_by_name($updateQry, ":EMP_ASSIGNED", $gEmpNum, -1);
        							
        														oci_execute($updateQry);
        														
        														$successMsg = "Success";
        

        }
    
    }//if(!strlen($errMsg))
    
    }//if($task == "Update Gang")
    
        
    if($task == "Assign Location")
    {
        
        
       
            $lLoc = isset($_POST['lLoc'])  ? $_POST['lLoc'] : "";
            $lConponent = isset($_POST['lConponent'])  ? $_POST['lConponent'] : "";
            $lForman = isset($_POST['lForman'])  ? $_POST['lForman'] : "";
            $lStatus = isset($_POST['lStatus'])  ? $_POST['lStatus'] : "";
            $lPassNum = isset($_POST['lPassNum'])  ? $_POST['lPassNum'] : "";
            $lNumBags = isset($_POST['lNumBags'])  ? $_POST['lNumBags'] : "";
        
            $lNoteTime = isset($_POST['lNoteTime'])  ? $_POST['lNoteTime'] : "";
            $lHour = isset($_POST['staStartHr'])  ? $_POST['staStartHr'] : "";
            $lMin = isset($_POST['staStartMin'])  ? $_POST['staStartMin'] : "";
            $lAmPm = isset($_POST['staStartAmPm'])  ? $_POST['staStartAmPm'] : "";
            if($lAmPm == 0)$lAmPm = "AM";else $lAmPm = "PM";
            $lNoteTime = $lNoteTime . " " . $lHour . ":" . $lMin . " " . $lAmPm;
        
        
            $lEndTime = isset($_POST['lEndTime'])  ? $_POST['lEndTime'] : "";
            $lcomments = isset($_POST['lcomments'])  ? $_POST['lcomments'] : "";
            
            $lconponentl = isset($_POST['allConponents'])  ? $_POST['allConponents'] : "";
       
        
        $lUser = $_SESSION['user'];
        
        
        $filesToUpload = "";
        
        $locationErrMsg = "";

        require_once('assignLocation.php');
        
        $Loc_Type = 'S';
        
        updateLocationGang($lconponentl, $lConponent, $eventID, $lForman, $lNoteTime, $lStatus, $lLoc,
            $lPassNum, $lNumBags, $lcomments, $lUser, $Loc_Type);
        
       
        require_once('uploadFile.php');
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
    
    <script type="text/javascript" src="../lib/jscalendar/calendar.js"></script>
    <script type="text/javascript" src="../lib/jscalendar/lang/calendar-en.js"></script>
    <script type="text/javascript" src="../lib/jscalendar/calendar-setup.js"></script>
    
    <link href="template1/tabcontent.css" rel="stylesheet" type="text/css" /> 
    <script src="tabcontent.js" type="text/javascript"></script>
    
    <link rel="stylesheet" type="text/css" href="styles.css">
    <script type="text/javascript" src="jquery-1.11.2.js"></script>
    
    
    <META HTTP-EQUIV="Pragma" CONTENT="no-cache"/>
    <META HTTP-EQUIV="Expires" CONTENT="-1"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    
    
    </head>
    
    <!-- <body onload="getEmployees();getILEmployees();getPLEmployees();"> -->
    <body>
    <div>
    
   <img src="wemsPhoto.jpg" alt="Mountain View", style="float:right;height:42px;">
   <br></br>
    </div>
    
    <div>
    
    	<ul class="tabs" data-persist="true"> 
    		<!--  <li><a href="#view1">Home</a></li> -->
    		<li><a href="#view1">Event</a></li> 
    		<li><a href="#view2">Gang Assignments</a></li> 
    		<li><a href="#view3">Location Assignments</a></li> 
    		<li><a href="#view4">Reports</a></li> 
    		<li><a href="#view5">Visualization</a></li> 
    	</ul> 
    	</div>
    	<div class="tabcontents"> 
    	
    	
    	<!-- 
    
    		<div style="background-color:#FFF2F2;" id="view1"  > 
    		
    
     
      			<form action="<?php //echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform">
              
               
      				<fieldset id="home">
        				<legend>Events</legend>
    	
    	
        				<table align = "center" class="table" cellpadding="1" cellspacing="1" border="0" width=100%>
                      
 
             			</table>
    	
      				</fieldset>
 
      , Thanks
      
      
    			</form>
     		</div>
     	 -->	
     		
     <!--
     ************************************************************************************************************************************************
                                                    EVENT
     ************************************************************************************************************************************************
     -->
     
    
     
    		<div style="background-color:#FFF2F2;" id="view1"  > 
      
      			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
             
               
      				<fieldset id="event">
        				<legend>Event Maintenance </legend>

        				<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >
        				
        				
        			<?php
				 
					if(strlen($eventErrMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#FF0000\" > $eventErrMsg </td></tr>";
					}
					
					if(strlen($eventSuccessMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#00FF00\" > $eventSuccessMsg </td></tr>";
					}
				  
				    ?>
        				
        				
        				
        				
        				
        				
                      		<tr ><th colspan = "2" align="center">Event</th></tr>
                      		
                      		<?php
                      			if($eventID > 0)
                      			{
                      			    echo"<tr><td>Storm ID:</td> <td><input type=\"text\" name=\"sID\" value=\"$externalID\" readonly></td></tr>";
                      			}
								
							?>
								<tr>
									<td>Storm Level:</td>
									<td><select name="eventType" id = "eventType"> <option value= 0 selected>  
									
									 <?php 

                                   $qry = oci_parse($c, "SELECT EVENTTYPE, EVENTDESC from EVENTTYPE order by EVENTDESC desc")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EVENTTYPE'];
                                     $desc = $row['EVENTDESC'];
									
										if($id == $eventType)
										echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										else
										echo "<option value=\"$id\" > $desc </option>";

                                   }

                                  
					               ?> 
									
									
									</option></select></td>
									
									
								</tr>
								<tr>
									
									<?php 
									if($eventID > 0)
									{
									   echo"<td>Assigned By:</td><td><input type=\"text\" name=\"sAssigned\" value=\" $activeUser\" readonly></td>";
									}
									
									
									?>
									
								</tr>
								<tr>
									
									
									<td>Start Date: </td><td><input readonly type="text" name="opentime" size="20" tabindex="24" id="opentime" value="<?php echo $openTime;?>"/><img src="cal.gif" width="16" border="0" id="startCalbutton" alt="Click here to pick date" />
									
									
									<select name="openStartHr" id="openStartHr">
													
													<?php
													for ($x = 1; $x <= 12; $x++) {
														    
						
														    echo "<option value= \"$x\"> $x </option>";
														}
														?>
													 </select> 
													 
													 : <select name="openStartMin" id="openStartMin">
													 
													 
													 
													  <?php
														
														for ($x = 0; $x <= 59; $x++) {
														    if($x < 10) $x = "0".  $x;
														   
														    echo "<option value= \"$x\"> $x </option>";
														}
														
														
														?>
													 
													 </select>
													 
													 <select name="openAmPm" id="openAmPm">
													 
													
													 <option value= "0">AM</option>
													 
													 <option value= "1">PM</option>
													 </select>
									
									
									
									
									
									
									
									</td>
									
								</tr> 
								<?php
								if($eventID > 0)
								{
									//echo"<tr><td>End Date: </td><td><input readonly type=\"text\" name=\"endDate\" size=\"20\" tabindex=\"24\" id=\"endDate\" /><img src=\"cal.gif\" width=\"16\" border=\"0\" id=\"endCalbutton\" alt=\"Click here to pick date\" /></td></tr>";
								}
								 ?>
								<tr>
									<td>Comments</td>
									<td><textarea rows="4" cols="50" name="sComments"></textarea></td>
								</tr>
								<tr>
								<?php
									if($eventID > 0)
									{
									    echo"<td colspan = \"1\" align=\"center\"><input class=\"Update Storm\" type=\"submit\" value=\"Update Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
									    echo"<td colspan = \"1\" align=\"center\"><input class=\"Close Storm\" type=\"submit\" value=\"Close Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";
									}
									else
									{
									    echo"<td colspan = \"2\" align=\"center\"><input class=\"Create Storm\" type=\"submit\" value=\"Create Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
									    
									}
								?>
             
        				</table>
        				
        				<br></br>
        									
        									<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >

												<tr ><th colspan = "2" align="center">Storm History</th></tr>
												
												<tr>
													
													<td><textarea rows="10" cols="100">
													<?php 
													if($eventID > 0)
													{
													   $qry = oci_parse($c, "select et.EVENTDESC, TO_CHAR(e.ENTER_DATETIME, 'MM/DD/YYYY hh:miAM') as NOTETIME, 
													                         e.NOTEUSER, e.EVENTUPDATE  
													                         from WEMS_EVENT_NOTES e, EVENTTYPE et
													                         where e.EVENTID = :EVENTID and et.EVENTTYPE = e.EVENTTYPE")
													       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
													
													       oci_bind_by_name($qry, ":EVENTID",  $eventID, -1);
													   oci_execute($qry);
													
													   while(($row = oci_fetch_array($qry)) !== false)
													   {

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
												if($eventID == 0)
												{
												    
												    
												    echo"<tr><td colspan = \"2\" align=\"center\">__________________________________________</td></tr>";
												    echo"<tr><td colspan = \"2\" align=\"center\">Past Storms:";
												    echo"<select name=\"pastStorms\" id = \"pastStorms\" > <option value= 0 selected>";
												    	
												    
												    
												    $qry = oci_parse($c, "SELECT EXTERNALID, EVENTID from WEMS_EVENT order by EVENTID")
												    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
												    
												    oci_execute($qry);
												    
												    while($row = oci_fetch_array($qry)){
												        $id = $row['EVENTID'];
												        $desc = $row['EXTERNALID'] . " - " . $id;
												        	
												        if($id == $eventType)
												            echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
												            else
												                echo "<option value=\"$id\" > $desc </option>";
												    
												    }
												    
												    echo"</option></select></td></tr>";
												    
												    
													echo"<td colspan = \"2\" align=\"center\"><input class=\"Re-Open Storm\" type=\"submit\" value=\"Re-Open Storm\" name=\"SUBMIT\" id=\"SUBMIT\" /></td>";
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
    
   
    
    
    	<div style="background-color:#FFF2F2;" id="view2"  > 
      
     			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
              		
              		<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >
              		
              		
              		<?php
				 
					if(strlen($gangErrMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#FF0000\" > $gangErrMsg </td></tr>";
					}
					
					if(strlen($gangSuccessMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#00FF00\" > $gangSuccessMsg </td></tr>";
					}
				  
				    ?>
              		

					<tr><th colspan = "2" align="center">Create Gang</th></tr>
					<tr>
					<td>Forman:</td>
					<td><select name="forman" id = "forman" onchange="getGangData()">
					<option value= "" >  </option>
													
													<?php 

                                   $qry = oci_parse($c, "SELECT EMPLOYEEID, NAME from EMPLOYEE where DEPTCODE is not NULL order by NAME")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['EMPLOYEEID'];
                                     $desc = $row['NAME'];
									
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
														    if($gEmpNum == $x)
														    echo "<option value= \"$x\" selected=\"selected\"> $x </option>";
														    else
														    echo "<option value= \"$x\"> $x </option>";
														}
														
														
														?>
														</select>
													</td>
												</tr>	
												<tr>
													<td>Start Date Time</td>
													<td><input readonly type="text" name="gStartTm" size="20" tabindex="24" id="gStartTm" value=""/><img src="cal.gif" width="16" border="0" id="gangStartTm" alt="Click here to pick date" />  
													<select name="gStartHr" id="gStartHr">
													
													<?php
													for ($x = 1; $x <= 12; $x++) {
														    
						
														    echo "<option value= \"$x\"> $x </option>";
														}
														?>
													 </select> 
													 
													 : <select name="gStartMin" id="gStartMin">
													 
													 
													 
													  <?php
														
														for ($x = 0; $x <= 59; $x++) {
														    if($x < 10) $x = "0".  $x;
														   
														    echo "<option value= \"$x\"> $x </option>";
														}
														
														
														?>
													 
													 </select>
													 
													 <select name="gAmPm" id="gAmPm">
													 
													
													 <option value= "0">AM</option>
													 
													 <option value= "1">PM</option>
													 </select>
													 
													 </td>
												</tr>
												
												
												
												<tr>
													<td>Comments</td> 
													<td><textarea rows="4" cols="50" name="gComments"></textarea></td>
												</tr>
												<?php
												
												//if($eventID > 0) echo"<tr><td colspan = \"2\" align=\"center\"><input class=\"Enter Gang\" type=\"submit\" value=\"Enter Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
												if($task == "Enter Gang") echo"<tr><td colspan = \"2\" align=\"center\"><input class=\"Update Gang\" type=\"submit\" value=\"Update Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
												else 
												    if($eventID > 0) echo"<tr><td colspan = \"2\" align=\"center\"><input class=\"Enter Gang\" type=\"submit\" value=\"Enter Gang\" name=\"SUBMIT\" id=\"gangEnterUpdate\" /></td></tr>";
												    
												?>
             
        									</table>
        									
        								
        									
        									<br></br>
        									
        									<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >

												<tr ><th colspan = "2" align="center">Gang History</th></tr>
												
												<tr>
													
													<td><td><textarea rows="10" cols="100" name="gHistory" id="gHistory">
													<?php 
                                                    $qry = oci_parse($c, "SELECT TO_CHAR(ENTER_DATETIME, 'MM/DD/YYYY HH:MI PM') as NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED
								                        from WEMS_GANG_NOTES where FORMANID = :FORMANID and EVENTID = :EVENTID order by NOTETIME")
       								                     OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       								                     oci_bind_by_name($qry, ":FORMANID", $forman, -1);
       								                     oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       								                     oci_execute($qry);
       								                     $comments = "";

       								                     while($row = oci_fetch_array($qry))
       								                     {

       								                         $noteTime = $row['NOTETIME'];
       								                         $user = $row['NOTEUSER'];
       								                         $note = $row['EVENTUPDATE'];
       								                         $noteEmpAssigned = $row['EMP_ASSIGNED'];
       								    
       				                                         $comments .= $noteTime . ",  user: " . $user . ",  " . $note . ", Employees assigned: " . $noteEmpAssigned . "\r\n";
       				                    

       								                     }
       								                     echo $comments;

													?>
													</textarea></td></td>
												</tr>
												
												
												
												
												
								<?php 
								/*
     		                     $qry = oci_parse($c, "select distinct w.formanid ,d.deptname DEPT, w.emp_assigned as CNT
								from wems_gang w, dept d, employee e
								where d.deptcode = e.deptcode
								and e.employeeid = w.formanid
								and d.deptcode is not null
								and eventID = :EVENTID
								order by d.deptname")
       								                     OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       								                     
       							oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       							oci_execute($qry);
     	
     	
     							while($row = oci_fetch_array($qry))
       							{
       							     $dept = $row['DEPT'];
       							     $empCnt = $row['CNT'];
       							     
       							     
       							    
       								echo "<tr><td colspan=\"2\"> " . $dept . " - " .  $empCnt . "</td></tr>";
       								
       								
       								$deptchange = $row['DEPT'];
       							
       							
       							}
     	                      */
								if($eventID > 0)
								{
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
     	
     	
      <div style="background-color:#FFF2F2;" id="view3"  > 
      
      
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
				<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >
				
				
				
				<?php
				 
					if(strlen($locationErrMsg)) 
					{
						//echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#FF0000\" > $locationErrMsg </td></tr>";
					}
					
					if(strlen($locationSuccessMsg)) 
					{
						//echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#00FF00\" > $interlockingErrMsg </td></tr>";
					}
				  
				    ?>
				

												<tr ><th colspan = "2" align="center">location Maintenance</th></tr>
												<tr>
													<td>Location:</td>
													<td><select name="lLoc" id = "lLoc" onchange="getConponentData()"> <option value= 0 selected>  </option>
													

													<?php 

                                                    $qry = oci_parse($c, "SELECT MARKERID, MARKERNAME from WEMS_LOCATION where LOC_CD = 'S' order by MARKERNAME")
                                                    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                                    oci_execute($qry);

                                                    while($row = oci_fetch_array($qry))
                                                    {
                                                        $id = $row['MARKERID'];
                                                        $desc = $row['MARKERNAME'];
									
										              if($id == $lLoc)
										                  echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
										              else
										                  echo "<option value=\"$id\" > $desc </option>";

                                                    }

                                  
					                               ?> 

													</select></td>
												</tr>
												
												<tr>
													<td>Component:</td>
													<td>
														<select name="lConponent" id = "lConponent" onchange="getConponentDetails();getEmployees(); "> 
														
														<?php 
																						
																											
														$qry = oci_parse($c, "SELECT CTID, FULLNAME FROM WEMS_CLEANABLE_TARGET where MARKERID = :MARKERID and TYPE = 'S'")
														OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
														 
														oci_bind_by_name($qry, ":MARKERID", $lLoc, -1);
														 
														oci_execute($qry);
														while($row = oci_fetch_array($qry))
														{
														    if($lConponent == $row[CTID])
														    {
														    echo "<option value= \"$row[CTID]\" selected=\"selected\"> $row[FULLNAME] </option>";
														    }
														    else {
														        echo "<option value= \"$row[CTID]\" > $row[FULLNAME] </option>";
														    }
														    													
														}
														
														?>
														
														</select>
														
														<input type="checkbox" name="allConponents" value ="allConponents" id = "allConponents"> Apply to all conponents<br>
														
													</td>
													
												</tr>
												<tr>
													<td>Gang:</td>
													<td><select name="lForman" id = "lForman" > 
													<option value= 0 selected>  </option>

													
                                   					</select>
      
													</td>
												</tr>
												<tr>
													<td>Status:</td>
													<td><select name="lStatus" id = "lStatus"> 
													
														
													<?php 

                                   $qry = oci_parse($c, "SELECT STATUSID, STATUS from WEMS_LOCATION_STATUS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['STATUSID'];
                                     $desc = $row['STATUS'];
                                     
									    if($id == $lStatus)
									    echo "<option value=\"$id\" selected=\"selected\"> $desc </option>";
									    else
										echo "<option value=\"$id\" > $desc </option>";

                                   }
                                                    ?>
													</select></td>
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
													<td><select name="lNumBags" id = "lNumBags">
														
														
														<?php
														//echo "<option value= \"0\">1</option>";
														for ($x = 0; $x <= 40; $x++) {
														    if($x == $lNumBags)
														        echo "<option value= \"$x\" selected=\"selected\"> $x </option>";
														        else
														    echo "<option value= \"$x\"> $x </option>";
														}
														
														
														?>
														
														 
														
																									</select></td>
												</tr>
												<tr>
													<td> Support Document:</td><td> <input name="fileToUpload[]" id= "rDoc" size="75" type="file" multiple="multiple" value="<?php echo $supportDoc; ?>" />	<?php //echo $supportDoc; ?></td></tr>
												</tr>
												 <tr>
						
						
						
												<td>  Support Documents attached:</td>
						
												<td><select name="downloadFile" id = "downloadFile"> 
						
												<option value= 0 selected>  </option>
												
												<?php 
																						
																											
														$qryDoc = oci_parse($c, "SELECT ID from WEMS_LOCDOCS where EVENTID = :EVENTID and MARKERID = :CTID")
                                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
          
                                                        oci_bind_by_name($qryDoc, ":EVENTID", $eventID, -1);
                                                        oci_bind_by_name($qryDoc, ":CTID", $lConponent, -1);
          
                                                        oci_execute($qryDoc);
         
														while($row = oci_fetch_array($qryDoc))
														{
														   
														        echo "<option value= \"$row[ID]\" > $row[ID] </option>";
													
														}
														
														?>
												
												
												
						
												</select>
						
												<input class="Download" type="submit" value="Download" name="SUBMIT" id="SUBMIT" />
												</td>
												
						
												</tr>	
												<tr>
												
													<td>Date/Time</td>
													<td><input readonly type="text" name="lNoteTime" size="20" tabindex="24" id="lNoteTime" value="<?php echo date('m/d/Y');?>"/><img src="cal.gif" width="16" border="0" id="locationStartTime" alt="Click here to pick date" />
													<select name="staStartHr" id="staStartHr">
													 
													 <option value= "1">1</option>
													 <option value= "2">2</option>
													 <option value= "3">3</option>
													 <option value= "4">4</option>
													 <option value= "5">5</option>
													 <option value= "6">6</option>
													 <option value= "7">7</option>
													 <option value= "8">8</option>
													 <option value= "9">9</option>
													 <option value= "10">10</option>
													 <option value= "11">11</option> 
													 <option value= "12">12</option>
													 </select> 
													 
													 : <select name="staStartMin" id="staStartMin">
													
													 <?php
														
														for ($x = 0; $x <= 59; $x++) {
														    if($x < 10) $x = "0".  $x;
														    echo "<option value= \"$x\"> $x </option>";
														}
														
														
														?>
													 
													 </select>
													 
													 <select name="staAmPm" id="staAmPm">
													 
													 <option value= "0">AM</option>
													 <option value= "1">PM</option>
													 </select>
													</td>
												</tr>
												
												<tr>
													<td>Comments</td>
													<td><textarea rows="4" cols="50" name="lcomments"></textarea></td>
												</tr>
												
             									<?php
												if($eventID > 0) echo"<tr><td colspan = \"2\" align=\"center\"><input class=\"Assign Location\" type=\"submit\" value=\"Assign Location\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";
												?>
        									</table>
        									
        									<br></br>
        									
        									<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >

												<tr ><th colspan = "2" align="center">Location History</th></tr>
												
												<tr>
													
													<td><td><textarea rows="10" cols="100" name="lHistory" id="lHistory">
													<?php
													$locComments = "";
													$qry = oci_parse($c, "SELECT TO_CHAR(t.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME, t.CTNOTES, e.NAME, t.CTSTATUS, t.CTPASSNUM,
                                											t.CTBAGS, t.CTNOTEUSER
																			from WEMS_CLEANABLE_TARGET_NOTES t, EMPLOYEE e where CTID = :CTID and EVENTID = :EVENTID and t.FORMANID = e.EMPLOYEEID")
       																		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       																		oci_bind_by_name($qry, ":CTID", $lConponent, -1);
       																		oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       																		oci_execute($qry);
       

       																		while($row = oci_fetch_array($qry))
       																		{

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
													</textarea></td></td>
												</tr>
												
												
												
             
        									</table>
      			</form>
    										
    									</div>
    								</li>
    								
    								
    								     
    								<li>
    									<div id="lview1">
    										
    										
    										
    										
    										
    										
    										
    										
    										
    										
    										
    										
    										
    										
    										
<!--
     ************************************************************************************************************************************************
                                                       Interlockings Interlockings Interlockings Interlockings Interlockings
   Interlockings Interlockings Interlockings Interlockings Interlockings                                                     
                                                                 Interlockings Interlockings Interlockings Interlockings Interlockings
                        Interlockings Interlockings Interlockings Interlockings Interlockings                       
                                            Interlockings Interlockings Interlockings Interlockings Interlockings
     ************************************************************************************************************************************************
     -->  
			
			
			

												
        								<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
				<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >
				
				
				
				<?php
				 
					if(strlen($interlockingErrMsg)) 
					{
						//echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#FF0000\" > $interlockingErrMsg </td></tr>";
					}
					
					if(strlen($locationSuccessMsg)) 
					{
						//echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#00FF00\" > $interlockingErrMsg </td></tr>";
					}
				  
				    ?>
				

												<tr ><th colspan = "2" align="center">INTERLOCKING Maintenance</th></tr>
												

                                  
					               

													</select></td>
												</tr>
												
												<tr>
													<td>Interlocking:</td>
													<td>
														<select name="iLoc" id = "iLoc" onchange="getILEmployees();getILConponentDetails();"> 
														<option value= "" ></option>
														 
														<?php 
														
														$qry = oci_parse($c, "SELECT MARKERID, MARKERNAME from WEMS_LOCATION where LOC_CD = 'I' order by MARKERNAME")
														OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
														
														oci_execute($qry);
														
														while($row = oci_fetch_array($qry))
														{
														    $id = $row['MARKERID'];
														    $desc = $row['MARKERNAME'];
														    	
														    if($id == $lLoc)
														        echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
														        else
														            echo "<option value=\"$id\" > $desc </option>";
														
														}
														
																						
														/*													
														$qry = oci_parse($c, "select t.CTID, t.FULLNAME from WEMS_CLEANABLE_TARGET t, WEMS_LOCATION l where t.MARKERID = l.MARKERID and l.LOC_CD = 'I' order by t.FULLNAME")
														OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
														 
														oci_bind_by_name($qry, ":MARKERID", $iLoc, -1);
														 
														oci_execute($qry);
														while($row = oci_fetch_array($qry))
														{
														    if($iConponent == $row[CTID])
														    {
														    echo "<option value= \"$row[CTID]\" selected=\"selected\"> $row[FULLNAME] </option>";
														    }
														    else {
														        echo "<option value= \"$row[CTID]\" > $row[FULLNAME] </option>";
														    }
														    													
														}
														*/
														?>
														
														</select>
														
														
														
													</td>
												</tr>
												
												
												
												
												
												
												<tr>
												
												
												
												
												
												
													<td>Gang:</td>
													<td><select name="iForman" id = "iForman" > 
													<option value= 0 selected>  </option>
													
													

													
                                   					</select>
      
													</td>
												</tr>
												<tr>
													<td>Status:</td>
													<td><select name="iStatus" id = "iStatus"> 
													
														
													<?php 

                                   $qry = oci_parse($c, "SELECT STATUSID, STATUS from WEMS_LOCATION_STATUS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['STATUSID'];
                                     $desc = $row['STATUS'];
                                     
									    if($id == $iStatus)
									    echo "<option value=\"$id\" selected=\"selected\"> $desc </option>";
									    else
										echo "<option value=\"$id\" > $desc </option>";

                                   }
                                                    ?>
													</select></td>
												</tr>
												
												<tr>
													<td> Support Document:</td><td> <input name="fileToUpload[]" id= "rDoc" size="75" type="file" multiple="multiple" value="<?php echo $isupportDoc; ?>" />	<?php //echo $supportDoc; ?></td></tr>
												</tr>
												 <tr>
						
						
						
												<td>  Support Documents attached:</td>
						
												<td><select name="ilDownloadFile" id = "ilDownloadFile"> 
						
												<option value= 0 selected>  </option>
												
												<?php 
																						
																											
														$qryDoc = oci_parse($c, "SELECT ID from WEMS_LOCDOCS where EVENTID = :EVENTID and MARKERID = :MARKERID")
                                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
          
                                                        oci_bind_by_name($qryDoc, ":EVENTID", $eventID, -1);
                                                        oci_bind_by_name($qryDoc, ":MARKERID", $iloc, -1);
          
                                                        oci_execute($qryDoc);
         
														while($row = oci_fetch_array($qryDoc))
														{
														   
														        echo "<option value= \"$row[ID]\" > $row[ID] </option>";
													
														}
														
														?>
												
												
												
						
												</select>
						
												<input class="Download" type="submit" value="Download" name="SUBMIT" id="SUBMIT" />
												</td>
												
						
												</tr>	
												
												<tr>
												
													<td>Date/Time</td>
													<td><input readonly type="text" name="iNoteTime" size="20" tabindex="24" id="iNoteTime" value="<?php echo date('m/d/Y');?>"/><img src="cal.gif" width="16" border="0" id="interlockingStartTime" alt="Click here to pick date" />
													<select name="iStartHr" id="iStartHr">
													 
													 <option value= "1">1</option>
													 <option value= "2">2</option>
													 <option value= "3">3</option>
													 <option value= "4">4</option>
													 <option value= "5">5</option>
													 <option value= "6">6</option>
													 <option value= "7">7</option>
													 <option value= "8">8</option>
													 <option value= "9">9</option>
													 <option value= "10">10</option>
													 <option value= "11">11</option> 
													 <option value= "12">12</option>
													 </select> 
													 
													 : <select name="iStartMin" id="iStartMin">
													
													 <?php
														
														for ($x = 0; $x <= 59; $x++) {
														    if($x < 10) $x = "0".  $x;
														    echo "<option value= \"$x\"> $x </option>";
														}
														
														
														?>
													 
													 </select>
													 
													 <select name="iAmPm" id="iAmPm">
													 
													 <option value= "0">AM</option>
													 <option value= "1">PM</option>
													 </select>
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
        									
        									<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >

												<tr ><th colspan = "2" align="center">Interlocking History</th></tr>
												
												<tr>
													
													<td><td><textarea rows="10" cols="100" name="iHistory" id="iHistory">
													<?php
													/*
													$interlocComments = "";
													$qry = oci_parse($c, "SELECT TO_CHAR(t.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME, t.CTNOTES, e.NAME, t.CTSTATUS, 
                                											 t.CTNOTEUSER
																			from WEMS_CLEANABLE_TARGET_NOTES t, EMPLOYEE e where CTID = :CTID and EVENTID = :EVENTID and t.FORMANID = e.EMPLOYEEID")
       																		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       																		oci_bind_by_name($qry, ":CTID", $iConponent, -1);
       																		oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       																		oci_execute($qry);
       

       																		while($row = oci_fetch_array($qry))
       																		{

       								    										$nNoteTime = $row['CTSTARTTIME'];
       								    										$nUser = $row['CTNOTEUSER'];
       								    										$nNote = $row['CTNOTES'];
       								    										$nForman = $row['NAME'];
       								    										
       								    										$nStatus = $row['CTSTATUS'];
       								    
       									$interlocComments .= $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . ", Status: " .  $nStatus . "\r\n";
       				                    

       								}
									   echo $interlocComments;	
									   */			
									?>
													</textarea></td></td>
												</tr>
												
												
												
             
        									</table>
      			</form>
    										
    									
             
        								
    									</div>
    								</li>
    								
    								<li>
    									<div id="lview2">
    										
<!-- 
____________________________________________________________________________________________________________________________________________________
____________________________________________________________PARKING LOTS__________________________________________________________________________
___________________________________________________________________________________________________________________________________________________
 -->
	
										
	
												
        								<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
				<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >
				
				
				
				<?php
				 
					if(strlen($parkingLotErrMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#FF0000\" > $parkingLotErrMsg </td></tr>";
					}
					
					if(strlen($parkingLotSuccessMsg)) 
					{
						echo "<tr><td colspan = \"6\" align = \"center\" bgcolor=\"#00FF00\" > $parkingLotErrMsg </td></tr>";
					}
				  
				    ?>
				

												<tr ><th colspan = "2" align="center">Parking Lot Maintenance</th></tr>
												

                                  
					               

													</select></td>
												</tr>
												
												<tr>
													<td>Parking Lots:</td>
													<td>
														<select name="plLoc" id = "plLoc" onchange="getPLConponentDetails();getPLEmployees(); "> 
														<option value= "" ></option>
														 
														<?php 
																						
																											
														$qry = oci_parse($c, "select MARKERID, MARKERNAME from WEMS_LOCATION where LOC_CD = 'P' order by MARKERNAME")
														OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
														 
														
														 
														oci_execute($qry);
                                                        while($row = oci_fetch_array($qry))
														{
														    $id = $row['MARKERID'];
														    $desc = $row['MARKERNAME'];
														    	
														    if($id == $lLoc)
														        echo "<option value= \"$id\" selected=\"selected\"> $desc </option> ";
														        else
														            echo "<option value=\"$id\" > $desc </option>";
														
														}
														
														?>
														
														
														
														</select>
														
														
														
													</td>
												</tr>
												<tr>
													<td>Gang:</td>
													<td><select name="plForman" id = "plForman" > 
													<option value= 0 selected>  </option>

													
                                   					</select>
      
													</td>
												</tr>
												<tr>
													<td>Status:</td>
													<td><select name="plStatus" id = "plStatus"> 
													
														
													<?php 

                                   $qry = oci_parse($c, "SELECT STATUSID, STATUS from WEMS_LOCATION_STATUS")
                                       OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

                                   oci_execute($qry);

                                   while($row = oci_fetch_array($qry)){
                                     $id = $row['STATUSID'];
                                     $desc = $row['STATUS'];
                                     
									    if($id == $iStatus)
									    echo "<option value=\"$id\" selected=\"selected\"> $desc </option>";
									    else
										echo "<option value=\"$id\" > $desc </option>";

                                   }
                                                    ?>
													</select></td>
												</tr>
												
												<tr>
													<td> Support Document:</td><td> <input name="fileToUpload[]" id= "rDoc" size="75" type="file" multiple="multiple" value="<?php echo $plsupportDoc; ?>" /></td>
												</tr>
												
												<tr>
						
						
						
												<td>  Support Documents attached:</td>
						
												<td><select name="plDownloadFile" id = "plDownloadFile"> 
						
												<option value= 0 selected>  </option>
												
												<?php 
																						
																											
														$qryDoc = oci_parse($c, "SELECT ID from WEMS_LOCDOCS where EVENTID = :EVENTID and MARKERID = :MARKERID")
                                                        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
          
                                                        oci_bind_by_name($qryDoc, ":EVENTID", $eventID, -1);
                                                        oci_bind_by_name($qryDoc, ":MARKERID", $plConponent, -1);
          
                                                        oci_execute($qryDoc);
         
														while($row = oci_fetch_array($qryDoc))
														{
														   
														        echo "<option value= \"$row[ID]\" > $row[ID] </option>";
													
														}
														
														?>
												
												
												
						
												</select>
						
												<input class="Download" type="submit" value="Download" name="SUBMIT" id="SUBMIT" />
												</td>
												
						
												</tr>	
												
												<tr>
												
													<td>Date/Time</td>
													<td><input readonly type="text" name="plNoteTime" size="20" tabindex="24" id="plNoteTime" value="<?php echo date('m/d/Y');?>"/><img src="cal.gif" width="16" border="0" id="parkingLotStartTime" alt="Click here to pick date" />
													<select name="plStartHr" id="plStartHr">
													 
													 <option value= "1">1</option>
													 <option value= "2">2</option>
													 <option value= "3">3</option>
													 <option value= "4">4</option>
													 <option value= "5">5</option>
													 <option value= "6">6</option>
													 <option value= "7">7</option>
													 <option value= "8">8</option>
													 <option value= "9">9</option>
													 <option value= "10">10</option>
													 <option value= "11">11</option> 
													 <option value= "12">12</option>
													 </select> 
													 
													 : <select name="plStartMin" id="plStartMin">
													
													 <?php
														
														for ($x = 0; $x <= 59; $x++) {
														    if($x < 10) $x = "0".  $x;
														    echo "<option value= \"$x\"> $x </option>";
														}
														
														
														?>
													 
													 </select>
													 
													 <select name="plAmPm" id="plAmPm">
													 
													 <option value= "0">AM</option>
													 <option value= "1">PM</option>
													 </select>
													</td>
												</tr>
												
												<tr>
													<td>Comments</td>
													<td><textarea rows="4" cols="50" name="plcomments"></textarea></td>
												</tr>
												
             									<?php
												if($eventID > 0) echo"<tr><td colspan = \"2\" align=\"center\"><input class=\"Assign parking Lot\" type=\"submit\" value=\"Assign Parking Lot\" name=\"SUBMIT\" id=\"SUBMIT\" /></td></tr>";
												?>
        									</table>
        									
        									<br></br>
        									
        									<table  align = "center" class="table" cellpadding="1" cellspacing="1" border="0" >

												<tr ><th colspan = "2" align="center">Parking Lot History</th></tr>
												
												<tr>
													
													<td><td><textarea rows="10" cols="100" name="plHistory" id="plHistory">
													<?php
													$plComments = "";
													$qry = oci_parse($c, "SELECT TO_CHAR(t.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME, t.CTNOTES, e.NAME, t.CTSTATUS, 
                                											t.CTNOTEUSER
																			from WEMS_CLEANABLE_TARGET_NOTES t, EMPLOYEE e where CTID = :CTID and EVENTID = :EVENTID and t.FORMANID = e.EMPLOYEEID")
       																		OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
       
       																		oci_bind_by_name($qry, ":CTID", $plConponent, -1);
       																		oci_bind_by_name($qry, ":EVENTID", $eventID, -1);
       																		oci_execute($qry);
       

       																		while($row = oci_fetch_array($qry))
       																		{

       								    										$nNoteTime = $row['CTSTARTTIME'];
       								    										$nUser = $row['CTNOTEUSER'];
       								    										$nNote = $row['CTNOTES'];
       								    										$nForman = $row['NAME'];
       								    										
       								    										$nStatus = $row['CTSTATUS'];
       								    
       									$plComments .= $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . ", Status: " .  $nStatus . "\r\n";
       				                    

       								}
									   echo $plComments;				
									?>
													</textarea></td></td>
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
     
     
     
     
     <div style="background-color:#FFF2F2;" id="view4"  > 
      
     			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
              
               
      				<fieldset id="reports">
        				<legend>Reports </legend>
    	
    	
        				<table align = "center" class="table" cellpadding="1" cellspacing="1" border="0" width=100%>
                      
							
                      		<a href="http://webz8dev.lirr.org/~hdesai/WEMS/wemsViewer/eventMaint.php">Reports</a>
        				</table>
        
      				</fieldset>

      
      			</form>
     
     	</div>
     
   <!--
     ************************************************************************************************************************************************
                                                                GIS
     ************************************************************************************************************************************************
     -->
    		
      
       <div  id="view5"  > 
      
     			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >
              

      				
      				<input class="GIS" type="submit" value="GIS" name="SUBMIT" id="SUBMIT" />
      				
      				

      			</form>
     
     	</div>
     
     <!--
     ************************************************************************************************************************************************
                                                        Make logout a button
     ************************************************************************************************************************************************
     -->
   
    		
      
       <div style="background-color:#FFF2F2;" id="view6"  > 
      
     			<form action="<?php echo $_SERVER['PHP_SELF']; ?>"  method="post" enctype="multipart/form-data" name="new_inquiry" id="mainform" >

      			</form>
     
     	</div> 
     
    <!-- ____________________________________________________________________________________________________ --> 
    </div> <!-- end <div class="tabcontents"> -->

    </body>

      <script language="JavaScript" type="text/javascript">

      Calendar.setup(
        		{
        			inputField : "opentime",
        			ifFormat   : "%m/%d/%Y",
        			displayArea: "start_display",
        			daFormat   : "%m/%d/%Y",
        			button     : "startCalbutton",
        			weekNumbers: false

        		}  );

				Calendar.setup(
        		{
        			inputField : "gStartTm",
        			ifFormat   : "%m/%d/%Y",
        			displayArea: "start_display",
        			daFormat   : "%m/%d/%Y",
        			button     : "gangStartTm",
        			weekNumbers: false

        		}  );

				
				Calendar.setup(
		        		{
		        			inputField : "lNoteTime",
		        			ifFormat   : "%m/%d/%Y",
		        			displayArea: "start_display",
		        			daFormat   : "%m/%d/%Y",
		        			button     : "locationStartTime",
		        			weekNumbers: false

		        		}  );
				
				Calendar.setup(
		        		{
		        			inputField : "iNoteTime",
		        			ifFormat   : "%m/%d/%Y",
		        			displayArea: "start_display",
		        			daFormat   : "%m/%d/%Y",
		        			button     : "interlockingStartTime",
		        			weekNumbers: false

		        		}  );
				
				

      	var tabs = $('#tabs-titles li'); //grab tabs
      	var contents = $('#tabs-contents li'); //grab contents
      
      	tabs.bind('click',function()
      	{
        	contents.hide(); //hide all contents
        	tabs.removeClass('current'); //remove 'current' classes
      
        	$(contents[$(this).index()]).show(); //show tab content that matches tab title index
        	$(this).addClass('current'); //add current class on clicked tab title


        	//$("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
          	//  var id = $(e.target).attr("href").substr(1);
          	//  window.location.hash = id;
            //	alert(id);
          	//});

        	
    	}
    	);

      

      	// on load of the page: switch to the currently selected tab
      	
      	var hash = window.location.hash;
      	//alert(hash);
      	//$('#myTab a[href="' + hash + '"]').tab('show');
      	contents.hide(); //hide all contents
    	tabs.removeClass('current'); //remove 'current' classes

    	//alert(hash.charAt(6));

    	var tabindex = <?php echo $tabindex; ?>; 

    	 
    	       
    	

      	$(contents[tabindex]).show(); //show tab content that matches tab title index
		
//------------------------------------------------------------------------------------------------------------------------------

    	function getEmployees() 
        {
	 			
     	 
         var conponent = document.getElementById('lConponent').value;
          var loc = document.getElementById('lLoc').value;
          //alert(loc);
          var eventId = "<?php echo $eventID; ?>";
          var locCD = 0;
         
 		  
 		 
          if (window.XMLHttpRequest)
          {
                // If IE7, Mozilla, Safari, etc: Use native object
                var client = new XMLHttpRequest();
          }
          else
          {
                if (window.ActiveXObject)
                {
 	           // ...otherwise, use the ActiveX control for IE5.x and IE6
 	           var client = new ActiveXObject("Microsoft.XMLHTTP");
                }
          }
 
         // var url = "getCatInfo.php?param=" + param;
          // var url = "getCatInfo.php";
          //client.open("GET", url, true);
          
           client.onreadystatechange = function() {GangListDetailhandler(client)};
           client.open("GET", "getGangList.php?loc=" + loc + "&eventId=" + eventId+"&conponent="+conponent+"&locCD="+locCD);
           client.send("");

               
        } //getData() 
 
        function GangListDetailhandler(obj)
        {
           var forman = document.getElementById('lForman');
           var location = document.getElementById('lLoc').value;
           var conponent = document.getElementById('lConponent').value;
           
           forman.options.length = 0;
          
           
           if(obj.readyState == 4 && obj.status == 200)
           {

             var val = eval('(' + obj.responseText + ')');

             for(var i = 0; i < val.length; i++)
             {

                 var opt = document.createElement('option');
          		  
          		  opt.innerHTML = val[i].NAME;
        		  opt.value = val[i].FORMANID;
        		  var assignLoc = document.createElement('text');
          		  var assignLoc = val[i].LOCATION;
          		  
          		  
          		 if(assignLoc == conponent)
          		 {

              		 
              		   opt.setAttribute("selected","selected");
            			forman.appendChild(opt);
            			
            			//alert(assignLoc);
          		 }
          		 else
          		 {
            		 
          		  		forman.appendChild(opt);
              		  	
          		 }  
          		        

              } //end for(var i = 0; i < val.length; i++)
            } // end if(obj.readyState == 4 && obj.status == 200)
 
          } //handler(obj)


//-------------------------------------------------------------------------------------------------------------------------------
		
           function getGangData() 
           {
            
        	 //var param = document.getElementById('eventID').value;
             var param = document.getElementById('forman').value;
             
            // document.getElementById('forman').options.length = 0;
             
             var eventId = "<?php echo $eventID; ?>";
            //alert("Param= " + eventId);
    		 
    		 
    		 
             if (window.XMLHttpRequest)
             {
                   // If IE7, Mozilla, Safari, etc: Use native object
                   var client = new XMLHttpRequest();
             }
             else
             {
                   if (window.ActiveXObject)
                   {
    	           // ...otherwise, use the ActiveX control for IE5.x and IE6
    	           var client = new ActiveXObject("Microsoft.XMLHTTP");
                   }
             }
    
            // var url = "getCatInfo.php?param=" + param;
             // var url = "getCatInfo.php";
             //client.open("GET", url, true);
    
              client.onreadystatechange = function() {gangHandler(client)};
              client.open("GET", "getGangInfo.php?param=" + param + "&eventId=" + eventId);
              client.send("");
    
           } //getData() 
    
           function gangHandler(obj)
           {
 
          	 
              var empNum = document.getElementById('gEmpNum');
              var comments = document.getElementById('gHistory');
              
              var gangButton = document.getElementById('gangEnterUpdate');
              
              empNum.value = "";
              comments.value = "";
              //gangButton.value = "Enter Gang";
			  
              if(obj.readyState == 4 && obj.status == 200)
              {

                var val = eval('(' + obj.responseText + ')');
                
                
                
                

                for(var i = 0; i < val.length; i++)
                {

                      var txtNew = document.createElement('text');

                     //alert(val[i].EMP_ASSIGNED);
                	 //alert(val[i].COMMENTS);

                     txtNew.text = val[i].EMP_ASSIGNED;
                     empNum.value = txtNew.text;

                     txtNew.text = val[i].COMMENTS;
                     comments.value = txtNew.text;

                     txtNew.text = val[i].BUTTON;
                     gangButton.value = txtNew.text;
						
                    


                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)
    
             } //handler(obj)

//___________________________________________________________________________________________________________________________
//____________________________________________________________________________________________________________________________

	 		function getConponentDetails() 
           {
	 			
        	 
             var param = document.getElementById('lConponent').value;
             
            
             var eventId = "<?php echo $eventID; ?>";
           
             //alert(param);
    		  
    		 
             if (window.XMLHttpRequest)
             {
                   // If IE7, Mozilla, Safari, etc: Use native object
                   var client = new XMLHttpRequest();
             }
             else
             {
                   if (window.ActiveXObject)
                   {
    	           // ...otherwise, use the ActiveX control for IE5.x and IE6
    	           var client = new ActiveXObject("Microsoft.XMLHTTP");
                   }
             }
    
            // var url = "getCatInfo.php?param=" + param;
             // var url = "getCatInfo.php";
             //client.open("GET", url, true);
             
              client.onreadystatechange = function() {conponentDetailhandler(client)};
              client.open("GET", "getStationInfo.php?param=" + param + "&eventId=" + eventId);
              client.send("");

                  
           } //getData() 
    
           function conponentDetailhandler(obj)
           {
              var status = document.getElementById('lStatus');
              var comments = document.getElementById('lHistory');
              var forman = document.getElementById('lForman');
             //forman.options.length = 0;
              var pass = document.getElementById('lPassNum');
              var bags = document.getElementById('lNumBags');
              
           
              var gangButton = document.getElementById('gangEnterUpdate');




            
              var downloadFile = document.getElementById('downloadFile');
              downloadFile.options.length = 0;
             // var length = downloadFile.options.length;
             // for (i = 0; i < length; i++) {
           	 //  downloadFile.options[i] = null;
             // }
       	  



              
              
             // empNum.value = "";
             // comments.value = "";
              
			    
              if(obj.readyState == 4 && obj.status == 200)
              {

                var val = eval('(' + obj.responseText + ')');

                for(var i = 0; i < val.length; i++)
                {

                      var txtNew = document.createElement('text');

                     //alert(val[i].STATUS);
                    // alert(val[i].COMMENTS);

                     txtNew.text = val[i].STATUS;
                     status.value = txtNew.text;

                     txtNew.text = val[i].COMMENTS;
                     comments.value = txtNew.text;

                     txtNew.text = val[i].PASS;
                     pass.value = txtNew.text;

                     txtNew.text = val[i].BAGS;
                     bags.value = txtNew.text;

                     var doctxt = txtNew.text;


                     //-------------------------------
                     //Grab all the attached documents and list in a option	
					
                     txtNew.text = val[i].SUPPORTDOCS;
                     
                     var doctxt = txtNew.text;
                     	
					  var docArray = new Array();
					  
					  docArray = doctxt.split(",");
					  
					  for(var x=0;x<docArray.length;x++){
							
							var opt = docArray[x];
	                          var el = document.createElement("option");

		                        el.innerHTML= opt;
	                          	el.value = opt;
	                          downloadFile.appendChild(el);
					  }
            		 //---------------------------------
            		 
            		 txtNew.text = val[i].GANG;
            		 forman.value = txtNew.text;
            		 
            		 txtNew.text = val[i].GANG;
            		 forman.value = txtNew.text;
  					
            		 txtNew.text = val[i].BAGS;
                     bags.value = txtNew.text;  
						
                    


                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)
    
             } //handler(obj)

//___________________________________________________________________________________________________________________________
//____________________________________________________________________________________________________________________________
	









//----------------------------------------------------------------------------------------------------------------------------
//---------------------------------------------------------------------------------------------------------------------------

         //THIS WILL FILL THE SELECT BOX WITH THE CONPONENT LIST.  DATA FILL WILL BE ON THE CONPONENT CLICK EVENT 

           function getConponentData() 
           {
        	   	var param = document.getElementById('lLoc').value;

        	   	
        	   	if (window.XMLHttpRequest)
               	{
                     var client = new XMLHttpRequest();
               	}
               	else
               	{
                     if (window.ActiveXObject)
                     {
      	           		var client = new ActiveXObject("Microsoft.XMLHTTP");
                     }
               	}
      
              
                client.onreadystatechange = function() {conponentHandler(client)};
                client.open("GET", "getConponentInfo.php?param=" + param);
                client.send("");
      
           

           } // getConponentData() 


           
           function conponentHandler(obj)
           {


        	   var status = document.getElementById('lStatus');
        	   var forman = document.getElementById('lForman');
        	   var pass = document.getElementById('lPassNum');
        	   var bags = document.getElementById('lNumBags');
        	   var comments = document.getElementById('lHistory');
        	   
        	   var chkBox = document.getElementById('allConponents');
        	   chkBox.checked = false;
        	   
        	   var txtNew = document.createElement('text');

               txtNew.text = "";
               status.value = txtNew.text;

               txtNew.text = "";
               forman.value = txtNew.text;
               
               txtNew.text = "";
               pass.value = txtNew.text;

               txtNew.text = "";
               bags.value = txtNew.text;

               txtNew.text = "";
               comments.value = txtNew.text;
 
				
              var lConponent = document.getElementById('lConponent');
              
              document.getElementById('lConponent').options.length = 0;
              
              var length = lConponent.options.length;
              for (i = 0; i < length; i++) {
            	  lConponent.options[i] = null;
            	}
              
              if(obj.readyState == 4 && obj.status == 200)
              {	
            	  
               	 var val = eval('(' + obj.responseText + ')');
        		  	
            	  for (var i = 0; i < val.length; i++)
                  {
            	      
            		  var opt = document.createElement('option');
            		  
            		  opt.innerHTML = val[i].FULLNAME;
          		   	  opt.value = val[i].CTID;
            		  lConponent.appendChild(opt);  
            		        
            	     
            	  }


                  
               } // end if(obj.readyState == 4 && obj.status == 200)
    
             } //conponentHandler(obj)
    			 


//__________________________________________________________________________________________________________________
             //______________________________________________________________________________________________________
             			//____________________________________________________________________________________________
   									//________________________________________________________________________________
												//_____________________________________________________________________
															//_________________________________________________________
																		//___________________________________________











             //----------------------------------------------------------------------------------------------------------------------------
             //---------------------------------------------------------------------------------------------------------------------------

                      //THIS WILL FILL THE SELECT BOX WITH THE CONPONENT LIST.  DATA FILL WILL BE ON THE CONPONENT CLICK EVENT 
/*
                        function getILConponentData() 
                        {
                     	   	var param = document.getElementById('iLoc').value;

                     	   	
                     	   	if (window.XMLHttpRequest)
                            	{
                                  var client = new XMLHttpRequest();
                            	}
                            	else
                            	{
                                  if (window.ActiveXObject)
                                  {
                   	           		var client = new ActiveXObject("Microsoft.XMLHTTP");
                                  }
                            	}
                   
                           
                             client.onreadystatechange = function() {ilConponentHandler(client)};
                             client.open("GET", "getConponentInfo.php?param=" + param);
                             client.send("");
                   
                        

                        } // getConponentData() 


                        
                        function ilConponentHandler(obj)
                        {


                     	   var status = document.getElementById('iStatus');
                     	   var forman = document.getElementById('iForman');
                     	  // var pass = document.getElementById('iPassNum');
                     	  // var bags = document.getElementById('iNumBags');
                     	   var comments = document.getElementById('iHistory');
                     	   
                     	   var txtNew = document.createElement('text');

                            txtNew.text = "";
                            status.value = txtNew.text;

                            txtNew.text = "";
                            forman.value = txtNew.text;
                            
                          //  txtNew.text = "";
                          //  pass.value = txtNew.text;

                           // txtNew.text = "";
                           // bags.value = txtNew.text;

                            txtNew.text = "";
                            comments.value = txtNew.text;

             				
                           var lConponent = document.getElementById('iConponent');
                           document.getElementById('iConponent').options.length = 0;
                           var length = lConponent.options.length;
                           for (i = 0; i < length; i++) {
                         	  lConponent.options[i] = null;
                         	}
                           
                           if(obj.readyState == 4 && obj.status == 200)
                           {	
                         	  
                            	 var val = eval('(' + obj.responseText + ')');
                     		  	
                         	  for (var i = 0; i < val.length; i++)
                               {
                         	      
                         		  var opt = document.createElement('option');
                         		  
                         		  opt.innerHTML = val[i].FULLNAME;
                       		   	  opt.value = val[i].CTID;
                         		  lConponent.appendChild(opt);  
                         		        
                         	     
                         	  }


                               
                            } // end if(obj.readyState == 4 && obj.status == 200)
                 
                          } //conponentHandler(obj)

*/
//__________________________________________________________________________________________________________________________________________________

//__________________________________________________________________________________________________________________________________________________


                        function getILEmployees() 
                        {
                	 			
                     	 
                          //var loc = document.getElementById('iConponent').value;
                          var loc = document.getElementById('iLoc').value;
                          //alert(loc);
                          var eventId = "<?php echo $eventID; ?>";
                        
                         
                 		  
                 		 
                          if (window.XMLHttpRequest)
                          {
                                // If IE7, Mozilla, Safari, etc: Use native object
                                var client = new XMLHttpRequest();
                          }
                          else
                          {
                                if (window.ActiveXObject)
                                {
                 	           // ...otherwise, use the ActiveX control for IE5.x and IE6
                 	           var client = new ActiveXObject("Microsoft.XMLHTTP");
                                }
                          }
                 
                         // var url = "getCatInfo.php?param=" + param;
                          // var url = "getCatInfo.php";
                          //client.open("GET", url, true);
                          
                           client.onreadystatechange = function() {ilGangListDetailhandler(client)};
                           //client.open("GET", "getGangList.php?loc=" + loc + "&eventId=" + eventId + "&locCd=" + locCD);
                           client.open("GET", "getGangListNoConponent.php?loc="+loc+"&eventId="+eventId);
                           client.send("");

                               
                        } //getData() 
                 
                        function ilGangListDetailhandler(obj)
                        {
                           var iforman = document.getElementById('iForman');
                           var location = document.getElementById('iLoc').value;
                           
                           iforman.options.length = 0;
                           
                          
                           
                           if(obj.readyState == 4 && obj.status == 200)
                           {
                        	   
                             var val = eval('(' + obj.responseText + ')');

                             for(var i = 0; i < val.length; i++)
                             {

                                 var opt = document.createElement('option');
                          		  
                          		  opt.innerHTML = val[i].NAME;
                        		  opt.value = val[i].FORMANID;
                        		  var assignLoc = document.createElement('text');
                          		  var assignLoc = val[i].LOCATION;
                          		  
                            		
                          		 if(assignLoc == location)
                          		 {
                              		    opt.setAttribute("selected","selected");
                            			iforman.appendChild(opt);
                            			
                            			//alert(assignLoc);
                            			//alert(location);
                          		 }
                          		 else
                          		 {
                          		  		iforman.appendChild(opt);
                              		  	//alert(assignLoc);
                          		 }  
                          		        

                              } //end for(var i = 0; i < val.length; i++)
                            } // end if(obj.readyState == 4 && obj.status == 200)
                 
                          } //handler(obj)

                		
                          //___________________________________________________________________________________________________________________________
                          //____________________________________________________________________________________________________________________________


                          	 		function getILConponentDetails() 
                                     {
                          	 			
                                  	 
                                       var loc = document.getElementById('iLoc').value;
                                       
                                      
                                       var eventId = "<?php echo $eventID; ?>";
                                     
                                       
                              		  
                              		 
                                       if (window.XMLHttpRequest)
                                       {
                                             // If IE7, Mozilla, Safari, etc: Use native object
                                             var client = new XMLHttpRequest();
                                       }
                                       else
                                       {
                                             if (window.ActiveXObject)
                                             {
                              	           // ...otherwise, use the ActiveX control for IE5.x and IE6
                              	           var client = new ActiveXObject("Microsoft.XMLHTTP");
                                             }
                                       }
                              
                                      // var url = "getCatInfo.php?param=" + param;
                                       // var url = "getCatInfo.php";
                                       //client.open("GET", url, true);
                                       //alert(param);
                                        client.onreadystatechange = function() {ILconponentDetailhandler(client)};
                                        client.open("GET", "getStationInfoNoConponent.php?loc=" + loc + "&eventId=" + eventId);
                                        client.send("");

                                            
                                     } //getData() 
                              
                                     function ILconponentDetailhandler(obj)
                                     {
                                        var status = document.getElementById('iStatus');
                                        var comments = document.getElementById('iHistory');
                                       
                                        //var forman = document.getElementById('iForman');
                                       	//forman.options.length = 0;
                                        //var pass = document.getElementById('lPassNum');
                                        //var bags = document.getElementById('lNumBags');
                                        
                                        var downloadFile = document.getElementById('ilDownloadFile');
             							downloadFile.options.length = 0;
                                     
           							 
                                        if(obj.readyState == 4 && obj.status == 200)
                                        {

                                          var val = eval('(' + obj.responseText + ')');

                                          for(var i = 0; i < val.length; i++)
                                          {

                                                var txtNew = document.createElement('text');

                                              //alert(val[i].STATUS);
                                              //alert(val[i].COMMENTS);

                                               txtNew.text = val[i].STATUS;
                                               status.value = txtNew.text;

                                               txtNew.text = val[i].COMMENTS;
                                               comments.value = txtNew.text;

                                      		   txtNew.text = val[i].GANG;
                                      		   forman.value = txtNew.text;



                                         		//-------------------------------
                                               //Grab all the attached documents and list in a option	
                          					 alert(val[i].SUPPORTDOCS);
                                               txtNew.text = val[i].SUPPORTDOCS;
                                              
                                               var doctxt = txtNew.text;
                                               	
                          					  var docArray = new Array();
                          					  
                          					  docArray = doctxt.split(",");
                          					  
                          					  for(var x=0;x<docArray.length;x++){
                          						
                          							var opt = docArray[x];
                          	                        var el = document.createElement("option");
                            	                      		
                          		                    el.innerHTML= opt;
                          	                        el.value = opt;
                          	                        downloadFile.appendChild(el);
                          					  }
                                      		 //---------------------------------







                                      		   

                                           } //end for(var i = 0; i < val.length; i++)
                                         } // end if(obj.readyState == 4 && obj.status == 200)
                              
                                       } //handler(obj)
                       

                          //___________________________________________________________________________________________________________________________
                          //____________________________________________________________________________________________________________________________
                          			 


//__________________________________________________________________________________________________________________
            //______________________________________________________________________________________________________
                  			//____________________________________________________________________________________________
            									//________________________________________________________________________________
           												//_____________________________________________________________________
           															//_________________________________________________________
           																		//___________________________________________


//__________________________________________________________________________________________________________________
             //______________________________________________________________________________________________________
             			//____________________________________________________________________________________________
   									//________________________________________________________________________________
												//_____________________________________________________________________
															//_________________________________________________________
																		//___________________________________________










//__________________________________________________________________________________________________________________________________________________


                        function getPLEmployees() 
                        {
                	 			
                     	
                          var loc = document.getElementById('plLoc').value;
                          
                          //alert(loc);
                          var eventId = "<?php echo $eventID; ?>";
                        
                         
                 		  
                 		 
                          if (window.XMLHttpRequest)
                          {
                                // If IE7, Mozilla, Safari, etc: Use native object
                                var client = new XMLHttpRequest();
                          }
                          else
                          {
                                if (window.ActiveXObject)
                                {
                 	           // ...otherwise, use the ActiveX control for IE5.x and IE6
                 	           var client = new ActiveXObject("Microsoft.XMLHTTP");
                                }
                          }
                 
                         // var url = "getCatInfo.php?param=" + param;
                          // var url = "getCatInfo.php";
                          //client.open("GET", url, true);
                         
                           client.onreadystatechange = function() {plGangListDetailhandler(client)};
                           client.open("GET", "getGangListNoConponent.php?loc="+loc+"&eventId="+eventId);
                           client.send("");

                               
                        } //getData() 
                 
                        function plGangListDetailhandler(obj)
                        {
                       	 //alert("test");
                           var plforman = document.getElementById('plForman');
                           var location = document.getElementById('plLoc').value;
                           
                           plforman.options.length = 0;
                          
                          
                           if(obj.readyState == 4 && obj.status == 200)
                           {
                        	  // alert("TEST");
                             var val = eval('(' + obj.responseText + ')');

                             for(var i = 0; i < val.length; i++)
                             {
                            	
                                 var opt = document.createElement('option');
                          		  
                          		  opt.innerHTML = val[i].NAME;
                        		  opt.value = val[i].FORMANID;
                        		  var assignLoc = document.createElement('text');
                          		  var assignLoc = val[i].LOCATION;
                          		  
                            		if(assignLoc == location)
                            		 {
                                		    opt.setAttribute("selected","selected");
                                		    plForman.appendChild(opt);
                              			
                              			//alert(assignLoc);
                              			//alert(location);
                            		 }
                            		 else
                            		 {
                            			 plForman.appendChild(opt);
                                		  	//alert(assignLoc);
                            		 }  
                          		

                              } //end for(var i = 0; i < val.length; i++)
                            } // end if(obj.readyState == 4 && obj.status == 200)
                 
                          } //handler(obj)

                		




//????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????

                        

                        





//???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
                          //___________________________________________________________________________________________________________________________
                          //____________________________________________________________________________________________________________________________


                          	 		function getPLConponentDetails() 
                                     {
                          	 			
                                  	 
                                       var loc = document.getElementById('plLoc').value;
                                       
                                      
                                       var eventId = "<?php echo $eventID; ?>";
                                     
                                       var downloadFile = document.getElementById('plDownloadFile');
           							downloadFile.options.length = 0;
                                      // alert(eventId);
                              		  
                              		 
                                       if (window.XMLHttpRequest)
                                       {
                                             // If IE7, Mozilla, Safari, etc: Use native object
                                             var client = new XMLHttpRequest();
                                       }
                                       else
                                       {
                                             if (window.ActiveXObject)
                                             {
                              	           // ...otherwise, use the ActiveX control for IE5.x and IE6
                              	           var client = new ActiveXObject("Microsoft.XMLHTTP");
                                             }
                                       }
                              
                                      // var url = "getCatInfo.php?param=" + param;
                                       // var url = "getCatInfo.php";
                                       //client.open("GET", url, true);
                                       
                                        client.onreadystatechange = function() {PLconponentDetailhandler(client)};
                                        client.open("GET", "getStationInfoNoConponent.php?loc=" + loc + "&eventId=" + eventId);
                                        client.send("");

                                            
                                     } //getData() 
                              
                                     function PLconponentDetailhandler(obj)
                                     {
                                        var plStatus = document.getElementById('plStatus');
                                        var plHistory = document.getElementById('plHistory');
                                        var plForman = document.getElementById('plForman');
                                       //forman.options.length = 0;
                                        //var pass = document.getElementById('lPassNum');
                                        //var bags = document.getElementById('lNumBags');
                                        
                                     
                                     
                          			    
                                        if(obj.readyState == 4 && obj.status == 200)
                                        {
                                        	//alert('test');
                                          var val = eval('(' + obj.responseText + ')');

                                          for(var i = 0; i < val.length; i++)
                                          {

                                                var txtNew = document.createElement('text');

                                               
                                               
                                               // alert(val[i].STATUS);
                                               txtNew.text = val[i].STATUS;
                                               plStatus.value = txtNew.text;
                                               
                                              // alert(val[i].COMMENTS);
                                               txtNew.text = val[i].COMMENTS;
                                               plHistory.value = txtNew.text;
                                               
                                              // alert(val[i].GANG);
                                      		   txtNew.text = val[i].GANG;
                                        	   plForman.value = txtNew.text;


                                          	 //-------------------------------
                                               //Grab all the attached documents and list in a option	
                          					
                                               txtNew.text = val[i].SUPPORTDOCS;
                                               
                                               var doctxt = txtNew.text;
                                               	
                          					  var docArray = new Array();
                          					  
                          					  docArray = doctxt.split(",");
                          					  
                          					  for(var x=0;x<docArray.length;x++){
                          						
                          							var opt = docArray[x];
                          	                        var el = document.createElement("option");
                            	                     // alert(el);		
                          		                    el.innerHTML= opt;
                          	                        el.value = opt;
                          	                        downloadFile.appendChild(el);
                          					  }
                                      		 //---------------------------------
                                        	   
                                        	   

                                           } //end for(var i = 0; i < val.length; i++)
                                         } // end if(obj.readyState == 4 && obj.status == 200)
                              
                                       } //handler(obj)
                       

                          //___________________________________________________________________________________________________________________________
                          //____________________________________________________________________________________________________________________________
                          			 


//__________________________________________________________________________________________________________________
            //______________________________________________________________________________________________________
                  			//____________________________________________________________________________________________
            									//________________________________________________________________________________
           												//_____________________________________________________________________
           															//_________________________________________________________
           																		//___________________________________________



             
    
    		   $(document).on("keydown", function (e)
    	       {
    		        if (e.which === 8 && !$(e.target).is("input:not([readonly]):not([type=radio]):not([type=checkbox]), textarea, [contentEditable], [contentEditable=true]")) {
    		        e.preventDefault();
    		   }
    		});


    		
    		
    		
    		
       </script>
    
    
    
    
    
