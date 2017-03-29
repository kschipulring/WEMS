<?php
class event extends database {
    protected $eventId;
    protected $externalId;
    protected $eventType;
    protected $openTime;
    protected $closeTime;
    protected $eventYear;
    protected $openUser;
    protected $closeUser;
    private $tableName = "WEMS_EVENT";
    
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
    
    public function getEventList(){
        try{        
            $result = array();
            $qry = "select * from ". $this->tableName ." order by EVENTID";
            $result = $this->getRecord($qry);            
            return $result;
        } catch (Exception $e) {
            return $e;       
        }
    }
    
    public function getEventById($eventId){
        try{
            $result = array();
            $qry = "select e.*,t.* from ". $this->tableName ." e inner join EVENTTYPE t on e.EVENTTYPE = t.EVENTTYPE  where EVENTID = " . $eventId;
            $result = $this->getRecord($qry);
            return $result;
        } catch (Exception $e) {
            return $e;
        }
    }
}
?>