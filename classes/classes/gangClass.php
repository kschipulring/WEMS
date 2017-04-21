<?php
class gang extends database {
    public function __construct() {
        parent::__construct();
    }
    
    public function getGangTotalByDepartment($eventId){
        try{
            $result = array();
            if(isset($eventId)){
                $qry = "select sum(wg.EMP_ASSIGNED) as NOOFEMPLOYEE, count(we.DEPTCODE) as NOOFFORMAN, sum(wg.EMP_ASSIGNED) + count(we.DEPTCODE) as TOTALCREWSIZE, d.DEPTNAME  from WEMS_GANG wg 
inner join EMPLOYEE we on wg.FORMANID = we.EMPLOYEEID
inner join  DEPT d on d.DEPTCODE = we.DEPTCODE
and wg.eventid = ". $eventId."
group by d.DEPTNAME";
                $result = $this->getRecord($qry);
            }
            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }
}
?> 