<?php
class cleanableTarget extends database {
            
    public function getCleanableTargetByLocation($eventId, $markerId){
        try{        
            $result = array();
            if(isset($eventId) && isset($markerId)){
                $qry = "select ct.CTID,ct.FULLNAME, ctn.EVENTID, CTN.FORMANID, ctn.CTBAGS, TO_CHAR(ctn.CTSTARTTIME , 'DD/MM/YYYY HH:MI:SS')  CTSTARTTIME, TO_CHAR(ctn.CTENDTIME , 'DD/MM/YYYY HH:MI:SS') CTENDTIME, ctn.CTNOTEUSER, e.NAME EMPNAME , d.DEPTABBR, ctn.CREWSIZE  from WEMS_CLEANABLE_TARGET ct inner join WEMS_CLEANABLE_TARGET_NOTES ctn on ct.CTID = ctn.CTID  inner join EMPLOYEE e on e.EMPLOYEEID = ctn.FORMANID
inner join DEPT d on d.DEPTCODE = e.DEPTCODE where ct.MARKERID = ". $markerId ." and ctn.EVENTID = ". $eventId." order by ctn.CTID, ct.FULLNAME, CTSTARTTIME DESC";
                $result = $this->getRecord($qry);            
            }
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
    
    public function getCleanableTargetByEventId($eventId){
        try{
            $result = array();
            if(isset($eventId)){
                $qry = "select distinct(l.MARKERID), CASE 
WHEN l.LOC_CD = 'S' THEN CONCAT(l.MARKERNAME, CONCAT(' ', 'Station'))
WHEN l.LOC_CD = 'I' THEN CONCAT(l.MARKERNAME, CONCAT(' ', 'Interlocking'))
WHEN l.LOC_CD = 'P' THEN CONCAT(l.MARKERNAME, CONCAT(' ', 'Parking Lot'))
WHEN l.LOC_CD = 'Y' THEN CONCAT(l.MARKERNAME, CONCAT(' ', 'Y'))
WHEN l.LOC_CD = 'V' THEN CONCAT(l.MARKERNAME, CONCAT(' ', 'V'))
END AS MARKERNAME
from WEMS_CLEANABLE_TARGET ct inner join WEMS_CLEANABLE_TARGET_NOTES ctn on ct.CTID = ctn.CTID  inner join WEMS_LOCATION l on l.MARKERID = ct.MARKERID
where ctn.EVENTID = ". $eventId." order by MARKERNAME";
                $result = $this->getRecord($qry);                
            }
            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }
    
    public function getCleanableTargetByDatewise($reportFromDate, $reportToDate, $markerId){
        try{
            $result = array();
            //if(isset($eventId) && isset($markerId)){
                $qry = "select ct.CTID,ct.FULLNAME, ctn.EVENTID, (select CONCAT(t.EVENTDESC, CONCAT('-', e.EXTERNALID)) from WEMS_EVENT e inner join EVENTTYPE t on e.EVENTTYPE = t.EVENTTYPE  where EVENTID = ctn.EVENTID) as EVENTNAME, CTN.FORMANID, ctn.CTBAGS, 
    TO_CHAR(ctn.CTSTARTTIME , 'DD/MM/YYYY HH:MI:SS') CTSTARTTIME, TO_CHAR(ctn.CTENDTIME , 'DD/MM/YYYY HH:MI:SS') CTENDTIME, 
    ctn.CTNOTEUSER, e.NAME EMPNAME , d.DEPTABBR, ctn.CREWSIZE 
from WEMS_CLEANABLE_TARGET ct 
    inner join WEMS_CLEANABLE_TARGET_NOTES ctn on ct.CTID = ctn.CTID 
    inner join EMPLOYEE e on e.EMPLOYEEID = ctn.FORMANID 
    inner join DEPT d on d.DEPTCODE = e.DEPTCODE 
where ctn.CTSTARTTIME BETWEEN TO_DATE('".$reportFromDate."' , 'MM/DD/YYYY') and TO_DATE('".$reportToDate."' , 'MM/DD/YYYY')" ;
                if(isset($markerId) && $markerId  !== 0){
                   $qry .= " and ct.MARKERID = ". $markerId;
                }
                $qry .= " order by ctn.EVENTID, CTSTARTTIME DESC";    
                          
                $result = $this->getRecord($qry);
           // }
            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }
    
}
?>