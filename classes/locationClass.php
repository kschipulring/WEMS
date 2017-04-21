<?php
class location extends database {
    protected $markerId;
    protected $markerName;
    protected $markerType;
    protected $xCoordinate;
    protected $yCoordinate;
    protected $dateStamp;
    protected $lat;
    protected $lon;
    protected $gis_join_id;
    protected $loc_id;
    protected $county;
    protected $status;
    protected $location_passnum;
    protected $eventId;
    private $tableName = "WEMS_LOCATION";
    
    public function __construct() {
        parent::__construct();
    }
    
    public function __get($property) {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
    
    public function __set($property, $value) {
        if (property_exists($this, $property)) {
            $this->$property = $value;
        }
    }
    
    public function getLocationList(){
        try{        
            $result = array();
            $qry = "select * from ". $this->tableName ." order by MARKERNAME";
            $result = $this->getRecord($qry);            
            return $result;
        } catch (Exception $e) {
            return $e;       
        }
    }
    
    public function getLocationById($markerId){
        try{
            $result = array();
            $qry = "select * from ". $this->tableName ." where MARKERID = " . $markerId;            
            $result = $this->getRecord($qry);
            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }
    
	public function getLocationIfAssignedResult($markerId, $eventId){
		try{
			$result = array();
			$qry = "select distinct count(1) AS count from " . config::GANG_NOTES_TABLE . "
			where ASSIGN_LOC = {$markerId} AND EVENTID = {$eventId}
			and ROWNUM = 1";
			
			$result = $this->getRecord($qry);
			return $result;
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getLocationIfAssigned($markerId, $eventId){
		$ifAssigned = 0;
		
		$result = $this->getLocationIfAssignedResult($markerId, $eventId);

		if( gettype( $result ) == "array" ){
			while (($row = oci_fetch_array($result[0])) !== false) {
				$ifAssigned = intval($row["COUNT"]);
			}
		}
		
		return $ifAssigned;
	}
	
	public function getStationInfoResult($CTID, $eventID){
		try{
			$qry = "SELECT ID from " . config::LOCDOCS_TABLE . " where EVENTID = :EVENTID and MARKERID = :CTID";
			
			$binderArray = array(
				array("key" => ":EVENTID", "val" => $eventID),
				array("key" => ":CTID", "val" => $CTID)
			);

			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getCleanableTargetsByTypeResult($dbType, $loc){
		try{
			$qry = "SELECT CTID, FULLNAME FROM " . config::CLEANABLE_TARGET_TABLE . " where MARKERID = :MARKERID and TYPE = '{$dbType}'";
				
			$binderArray = array(
				array("key" => ":MARKERID", "val" => $loc)
			);
		
			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getCleanableTargetsByType($pre, $component, $loc){
		$dbType = Utilities::getDBtype($pre);
		
		$result = $this->getCleanableTargetsByTypeResult($dbType, $loc);
		
		$jsonArr = array();
		
		while (($row = oci_fetch_array($result[0])) !== false) {
			$selected = false;
			if ($component == $row["CTID"]) {
				$selected = true;
			}
			
			$jsonArr[] = array(
				"CTID" => $row['CTID'],
				"FULLNAME" => $row['FULLNAME'],
				"SELECTED" => $selected
			);
		}
		
		return $jsonArr;
	}
	
	public function getCleanableTargetResult($CTID){
		try{
			$qry = "SELECT NOTIFYTIME, ASSIGNED_CREWSIZE, ASSIGNED_SITEFOREMEN, CT_STATUS, CT_PASSNUM, CT_BAGS, NAME
				from " . config::CLEANABLE_TARGET_TABLE . "  where CTID = :CTID";
				
			$binderArray = array(
				array("key" => ":CTID", "val" => $CTID)
			);
		
			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getCleanableTargetNotesResult($CTID, $eventID){
		try{
			$qry = "SELECT TO_CHAR(W.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME, W.CTNOTES, E.LST_NME, W.CTSTATUS, W.CTPASSNUM, W.CTBAGS, W.CTNOTEUSER
				from " . config::CLEANABLE_TARGET_NOTES_TABLE . " W
	               LEFT JOIN " . config::WEMS_EMPLOYEE_TABLE . " E ON E.EMPLOYEENUMBER = W.FORMANID 
	               where W.CTID = :CTID and 
	               W.EVENTID = :EVENTID and  
	               ((W.FORMANID = E.EMPLOYEENUMBER) or (W.FORMANID is NULL)) ORDER BY ENTER_DATETIME";
		
			$binderArray = array(
				array("key" => ":EVENTID", "val" => $eventID),
				array("key" => ":CTID", "val" => $CTID)
			);
		
			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getLocationStatusesResult(){
		try{
			$qry = "SELECT STATUSID, STATUS from " . config::LOCATION_STATUS_TABLE;
		
			return $this->getRecord($qry);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getLocationStatuses($status){
		$jsonArr = array();
		
		$result = $this->getLocationStatusesResult();

		if( gettype( $result ) == "array" ){
			while (($row = oci_fetch_array($result[0])) !== false) {
				$id   = $row['STATUSID'];
				$desc = $row['STATUS'];
				
				$selected = false;
				
				if ($id == $status) {
					$selected = true;
				}
				
				$jsonArr[] = array(
					"ID" => $id,
					"DESC" => $desc,
					"SELECTED" => $selected
				);
			}
		}
		
		return $jsonArr;
	}
	
	public function getLocationStatusResult($nStatus){
		try{
			$qry = "SELECT STATUS from " . config::LOCATION_STATUS_TABLE . " WHERE STATUSID = :STATUSID";
		
			$binderArray = array(
				array("key" => ":STATUSID", "val" => $nStatus)
			);
		
			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getStationInfo($CTID, $eventID){
		$comments = "";
		$empAssigned= "";
		$button = "";
		$noteTime = "";
		$forman = "";
		$crewSize = "";
		$bags = "";
		$pass = "";
		$status = "";
		$formanName = "";
		$supportDocs = "";
		
		$result = $this->getStationInfoResult($CTID, $eventID);
		
		if (gettype($result) != "array") {
			return $result;
		}
		
		while (($row = oci_fetch_array($result[0])) !== false) {
			$supportDocs = $supportDocs . $row['ID'] . ",";
		}
		
		$result = $this->getCleanableTargetResult($CTID);
		
		if (gettype($result) != "array") {
			return $result;
		}
		
		while (($row = oci_fetch_array($result[0])) !== false) {
			$noteTime = $row['NOTIFYTIME'];
			$forman = $row['ASSIGNED_SITEFOREMEN'];
			// $formanName = $row['NAME'];
			$crewSize = $row['ASSIGNED_CREWSIZE'];
			$bags = $row['CT_BAGS'];
			$pass = $row['CT_PASSNUM'];
			$status = $row['CT_STATUS'];
			$button = "Update Station";
		}
		
		
		$result = $this->getCleanableTargetNotesResult($CTID, $eventID);

		if (gettype($result) != "array") {
			return $result;
		}
		
		while (($row = oci_fetch_array($result[0])) !== false) {
			$nNoteTime = $row['CTSTARTTIME'];
			$nUser = $row['CTNOTEUSER'];
			$nNote = $row['CTNOTES'];
			$nForman = $row['LST_NME'];
			$nBags = $row['CTBAGS'];
			$nPass = $row['CTPASSNUM'];
			$nStatus = $row['CTSTATUS'];
			
			$result2 = $this->getLocationStatusResult($nStatus);
			
			while (($row = oci_fetch_array($result2[0])) !== false) {
				$nStatus = $row['STATUS'];
			}
			
			$comments .= $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . ", Bags: " . $nBags . ", Pass #: " . $nPass . ", Status: " .  $nStatus . "\\n";
		}
		
		$jsonArr = array(
			"GANG" => $forman,
			"FORMANNAME" => $formanName,
			"COMMENTS" => $comments,
			"STATUS" => $status,
			"PASS" => $pass,
			"BAGS" => $bags,
			"TIME" => $noteTime,
			"BUTTON" => $button,
			"SUPPORTDOCS" => $supportDocs
		);
		
		return $jsonArr;
	}
	
	public function getLocationsByTypeResult($dbType){
		try{
			$qry = "SELECT MARKERID, MARKERNAME from " . config::LOCATION_TABLE . " where LOC_CD = '$dbType' order by MARKERNAME";

			return $this->getRecord($qry);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getLocationsByType($pre, $loc){
		$dbType = utilities::getDBtype($pre);
		
		$result = $this->getLocationsByTypeResult($dbType);
		
		$jsonArr = array();
		
		while (($row = oci_fetch_array($result[0])) !== false) {		
			$selected = false;
			if ($row['MARKERID'] == $loc) {
				$selected = true;
			}
			
			$jsonArr[] = array(
				"MARKERID" => $row['MARKERID'],
				"DESC" => $row['MARKERNAME'],
				"SELECTED" => $selected
			);
		}
		
		return $jsonArr;
	}
	
	public function getDownloadFieldsResult($eventID, $component, $bType="CTID"){
		try{
			$qry = "SELECT ID from " . config::LOCDOCS_TABLE . " where EVENTID = :EVENTID and MARKERID = :$bType";
			
			$binderArray = array(
				array("key" => ":EVENTID", "val" => $eventID),
				array("key" => ":$bType", "val" => $component)
			);

			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getDownloadFields($eventID, $component, $bType="CTID"){
		$result = $this->getDownloadFieldsResult($eventID, $component, $bType);
	
		$jsonArr = array();
	
		while (($row = oci_fetch_array($result[0])) !== false) {
			$jsonArr[] = $row;
		}
	
		return $jsonArr;
	}
	
	public function getLocationHistoryResult($eventID, $ctId){
		try{
			$ctable = config::CLEANABLE_TARGET_NOTES_TABLE;
			$etable = config::WEMS_EMPLOYEE_TABLE;
			
			$qry = <<<EOD
SELECT TO_CHAR(t.CTSTARTTIME, 'MM/DD/YYYY HH:MI PM') as CTSTARTTIME,
t.CTNOTES, e.LST_NME || ', ' || e.FST_NME AS NAME, t.CTSTATUS, t.CTPASSNUM, t.CTBAGS, t.CTNOTEUSER
FROM $ctable t
LEFT JOIN $etable e ON e.EMPLOYEENUMBER = t.FORMANID 
where t.CTID = :CTID
and t.EVENTID = :EVENTID
and ((t.FORMANID = e.EMPLOYEENUMBER) or (t.FORMANID = 0)) ORDER BY ENTER_DATETIME
EOD;
				
			$binderArray = array(
				array("key" => ":EVENTID", "val" => $eventID),
				array("key" => ":CTID", "val" => $ctId)
			);
		
			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getLocationHistory($pre, $eventID, $ctId){
		$result = $this->getLocationHistoryResult($eventID, $ctId);
		
		$jsonArr = array();
		
		while (($row = oci_fetch_array($result[0])) !== false) {
			$nNoteTime = $row['CTSTARTTIME'];
			$nUser = $row['CTNOTEUSER'];
			$nNote = $row['CTNOTES'];
			$nForman = $row['NAME'];
			$nStatus = $row['CTSTATUS'];
			
			$nBags = $row['CTBAGS'];
			$nPass = $row['CTPASSNUM'];
			
			$extra = '';
			if($pre === "l"){
				$extra = ", Bags: " . $nBags . ", Pass #: " . $nPass;
			}
				
			$jsonArr[] = $nNoteTime . ",  user: " . $nUser . ",  " . $nNote . ", Forman: " . $nForman . $extra . ", Status: " .  $nStatus . "\r\n";
		}
		
		return $jsonArr;
	}
	
	public function getLocationCleanStatesResult($states, $shapes){
		try{
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
			
			return $this->getRecord($superQryStr);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getLocationCleanStates($states=null, $shapes=null){
		if( $states === null ){
			$states = array("dirty", "in_progress", "half_clean", "clean");
		}
		if( $shapes === null ){
			$shapes = array("s" => "circle", "i" => "triangle", "p" => "rect");
		}
		
		$result = $this->getLocationCleanStatesResult($states, $shapes);
		
		$locationStates = array();
		
		while (($row = oci_fetch_array($result[0])) !== false) {
			foreach($row as $k=>$v){
				if( gettype($k) === "string" ){
					$nk = utilities::dashesToCamelCase( strtolower($k) );
						
					$locationStates[$nk] = $v;
				}
			}
		}//end while
		
		ksort($locationStates);
		
		return $locationStates;
	}
}