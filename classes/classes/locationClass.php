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
    
}
?>