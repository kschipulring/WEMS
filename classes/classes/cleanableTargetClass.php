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
                $qry = "select distinct(l.MARKERID), l.MARKERNAME from WEMS_CLEANABLE_TARGET ct inner join WEMS_CLEANABLE_TARGET_NOTES ctn on ct.CTID = ctn.CTID  inner join LOCATION l on l.MARKERID = ct.MARKERID
where ctn.EVENTID = ". $eventId." order by l.MARKERNAME";
                $result = $this->getRecord($qry);                
            }
            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }
    
}
?>