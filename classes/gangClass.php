<?php
class gang extends database {
	public function __construct(){
		parent::__construct();
	}

	public function getGangTotalByDepartment($eventId){
		try {
			$result = array();
			if (isset($eventId)) {
				$qry = "select sum(wg.EMP_ASSIGNED) as NOOFEMPLOYEE, count(we.DEPTCODE) as NOOFFORMAN, sum(wg.EMP_ASSIGNED) + count(we.DEPTCODE) as TOTALCREWSIZE, d.DEPTNAME
from " . config::GANG_TABLE . " wg 
inner join " . config::WEMS_EMPLOYEE_TABLE . " we on wg.FORMANID = we.EMPLOYEENUMBER
inner join " . config::DEPT_TABLE . " d on d.DEPTCODE = we.DEPTCODE
and wg.eventid = {$eventId}
group by d.DEPTNAME";
				$result = $this->getRecord($qry);
			}

			return $result;
		}

		catch(Exception $e) {
			return $e;
		}
	}
	
	public function getGangForemanResult($loc, $conponent){
		try{
			//get the foreman that should be selected in the select box
			$qry = "select DISTINCT ASSIGNED_SITEFOREMEN from " . config::CLEANABLE_TARGET_TABLE . " WHERE MARKERID = :MARKERID and CTID = :CTID";
			
			$binderArray = array(
				array("key" => ":MARKERID", "val" => $loc),
				array("key" => ":CTID", "val" => $conponent)
			);
			
			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getGangMembersResult($eventID){
		try{
			//get all the rest of the foremen
			$qry = "select DISTINCT g.FORMANID, e.LST_NME, e.LST_NME || ', ' || e.FST_NME as NAME, g.ASSIGN_LOC
			from " . config::GANG_TABLE . " g, " . config::WEMS_EMPLOYEE_TABLE . " e
			where g.EVENTID = :EVENTID and g.FORMANID = e.EMPLOYEENUMBER
			order by e.LST_NME";
			
			$binderArray = array(
				array("key" => ":EVENTID", "val" => $eventID)
			);
				
			return $this->getRecord($qry, $binderArray);
		} catch (Exception $e) {
			return $e;
		}
	}
	
	public function getGangListResult($loc, $eventID, $conponent){
		try{
			$qry = "select g.FORMANID, e.LST_NME, e.LST_NME || ', ' || e.FST_NME as NAME, g.ASSIGN_LOC, c.ASSIGNED_SITEFOREMEN
			from " . config::GANG_TABLE . " g
			left join " . config::WEMS_EMPLOYEE_TABLE . " e
			on g.FORMANID = e.EMPLOYEENUMBER
			left join " . config::CLEANABLE_TARGET_TABLE . " c
			on g.FORMANID = c.ASSIGNED_SITEFOREMEN
			where g.EVENTID = :EVENTID
			and MARKERID = :MARKERID
			and CTID = :CTID";

			$binderArray = array(
				array("key" => ":MARKERID", "val" => $loc),
				array("key" => ":CTID", "val" => $conponent),
				array("key" => ":EVENTID", "val" => $eventID)
			);
			
			return $this->getRecord($qry, $binderArray);		
		} catch (Exception $e) {
			return $e;
		}
	}

	public function getGangList($loc, $eventID, $conponent){
		$ASSIGNED_SITEFORMEN = "";
		
		//the foreman for the stated location and component
		$result = $this->getGangForemanResult($loc, $conponent);
		
		if( gettype($result) != "array" ){
			return $result;
		}
		
		while (($row = oci_fetch_array($result[0])) !== false){
			$ASSIGNED_SITEFORMEN = $row['ASSIGNED_SITEFOREMEN'];
		}
		
		
		//the foreman
		$result = $this->getGangForemanResult($loc, $conponent);
		
		if( gettype($result) != "array" ){
			return $result;
		}
		
		while (($row = oci_fetch_array($result[0])) !== false){
			$ASSIGNED_SITEFORMEN = $row['ASSIGNED_SITEFOREMEN'];
		}

		//the gang members
		$result = $this->getGangMembersResult($eventID);
		
		if( gettype($result) != "array" ){
			return $result;
		}
		
		$jsonArr = array();
		$jsonArr[0] = array(
			"FORMANID" => "0",
			"NAME" => "",
			"LOCATION" => ""
		);

		while (($row = oci_fetch_array($result[0])) !== false) {
			$assign_loc = $row['ASSIGN_LOC'];
			$forman = $row['FORMANID'];
			if (($loc == $assign_loc) or ($assign_loc == "")) {
				$tempComp = "";
				if ($ASSIGNED_SITEFORMEN == $forman) {
					$tempComp = $conponent;
				}

				$jsonArr[] = array(
					"FORMANID" => $forman,
					"NAME" => $row["NAME"],
					"LOCATION" => $tempComp
				);
			}
		}

		return $jsonArr;
	}
	
	public function getGangDataResult(){
		try{
			$qry = "SELECT EMPLOYEENUMBER, LST_NME || ', ' || FST_NME AS NAME
			FROM " . config::WEMS_EMPLOYEE_TABLE . " where DEPTCODE is not NULL and DIV_CD = '1' order by LST_NME";
			
			$result = $this->getRecord($qry);
		} catch(Exception $e) {
			return $e;
		}
	}
	
	public function getGangData($forman){
		$result = $this->getGangDataResult();
		
		if( gettype($result) != "array" ){
			return $result;
		}
		
		$jsonArr = array();
		
		while (($row = oci_fetch_array($result[0])) !== false) {
			$id = $row['EMPLOYEENUMBER'];
			$desc = $row['NAME'];
			
			$selected = false;
				
			if($id == $forman){
				$selected = true;
			}
			
			$jsonArr[] = array(
				"FORMANID" => $id,
				"SELECTED" => $selected,
				"DESC" => $desc
			);
		}
		
		return array(
			"json" => $jsonArr
		);
	}
	
	public function getGangForemanInfoResult($formanID, $eventID){
		try{
			$qry = "SELECT EMP_ASSIGNED, ASSIGN_LOC, STATUS, OPENTIME
			from " . config::GANG_TABLE . " where FORMANID = :FORMANID and EVENTID = :EVENTID";
			
			$binderArray = array(
				array("key" => ":FORMANID", "val" => $formanID),
				array("key" => ":EVENTID", "val" => $eventID)
			);
				
			$result = $this->getRecord($qry);
		} catch(Exception $e) {
			return $e;
		}
	}
	
	public function getForemanNotesResult($formanID, $eventID){
		try{
			$qry = "SELECT TO_CHAR(NOTETIME, 'MM/DD/YYYY HH:MI PM') as NOTETIME, NOTEUSER, EVENTUPDATE, EMP_ASSIGNED, ASSIGN_LOC
			from " . config::GANG_TABLE . " where FORMANID = :FORMANID and EVENTID = :EVENTID order by ENTER_DATETIME";

			$binderArray = array(
				array("key" => ":FORMANID", "val" => $formanID),
				array("key" => ":EVENTID", "val" => $eventID)
			);
	
			$result = $this->getRecord($qry);
		} catch(Exception $e) {
			return $e;
		}
	}
	
	public function getMarkerNameResult($loc){
		try{
			$qry = "SELECT FULLNAME from " . config::CLEANABLE_TARGET_TABLE . " where MARKERID = :CTID";
	
			$binderArray = array(
				array("key" => ":CTID", "val" => $loc)
			);
	
			$result = $this->getRecord($qry);
		} catch(Exception $e) {
			return $e;
		}
	}
	
	public function getGangInfo($formanID, $eventID){	
		try{	
			$comments = "";
			
			$empAssigned = "";
			$button = "";
			$loc = "";
			$status = 0;
			$dateTime = "";
			
			if ($formanID >= 0) {				
				
				//basic info on the foreman
				$result = $this->getGangForemanInfoResult($formanID, $eventID);
				
				if (gettype($result) != "array") {
					return $result;
				}
				
				while (($row = oci_fetch_array($result[0])) !== false) {
					$empAssigned = $row['EMP_ASSIGNED'];
					$loc = $row['ASSIGN_LOC'];
					$status = $row['STATUS'];
					$dateTime = $row['OPENTIME'];
				}
				
				//notes on the foreman
				$result = $this->getForemanNotesResult($formanID, $eventID);
				
				if (gettype($result) != "array") {
					return $result;
				}
				
				while (($row = oci_fetch_array($result[0])) !== false) {
					$noteTime = $row['NOTETIME'];
					$user = $row['NOTEUSER'];
					$note = $row['EVENTUPDATE'];
					$noteEmpAssigned = $row['EMP_ASSIGNED'];
	
					$loc = $row['ASSIGN_LOC'];
	
					if ($loc != "") {				
						$result = $this->getMarkerNameResult($loc);
						
						if (gettype($result) != "array") {
							return $result;
						}
						
						while (($row = oci_fetch_array($result[0])) !== false) {
							$loc = $row['FULLNAME'];
						}
	
						$comments .= $noteTime.
						",  user: ".$user.
						", Gang Assigned to: ".$loc.
						"\\n";
					} else {
						$comments .= $noteTime.
						",  user: ".$user.
						",  ".$note.
						", Employees assigned: ".$noteEmpAssigned.
						"\\n";
					}
					
					$button = "Update Gang";
				}
			}
			
			if ($button == "") $button = "Enter Gang";
			
			$jsonArr = array(
				"EMP_ASSIGNED" => $empAssigned,
				"COMMENTS" => $comments,
				"BUTTON" => $button,
				"STATUS" => $status,
				"DATETIME" => $dateTime
			);
			
			return $jsonArr;
		
		} catch(Exception $e) {
			return $e;
		}
	}
}
?>