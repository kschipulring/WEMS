<?php
class database{
    protected $databaseName = "WEATTEST";
    protected $databaseUserName = "wems";
    protected $databasePassword = "wems";
    protected $conn; 
    
    public function __construct() {
        $this->conn = $this->connect($this->databaseName, $this->databaseUserName, $this->databasePassword);
    }
    
    public function connect($databaseName, $databaseUserName, $databasePassword) {
        try {
             $db = oci_pconnect($databaseUserName, $databasePassword, $databaseName);
            if ($db) {
                return $db;
            } else {
                throw new Exception(" Database Connection Error");
            }
        } catch (Exception $e) {
            return $e;
        }
    }
    
    public function addRecord($insertQry){
        try {
            $stmt = oci_parse($this->conn, $insertQry);
            $refcur = oci_new_cursor($this->conn);
            $exec = oci_execute($stmt, OCI_DEFAULT);
            oci_execute($refcur);
            if($exec){
                return array($exec, $refcur);
            } else {
                throw new Exception(" Insert Query Error ");
            }
        } catch (Exception $e) {
            return $e;
        }
    }
    
    public function getRecord($selectQry){
        try {
            $rs = array();
            $stmt = oci_parse($this->conn, $selectQry);
            $refcur = oci_new_cursor($this->conn);
            $exec = oci_execute($stmt, OCI_DEFAULT);
            oci_execute($refcur);
            if($exec){              
               return array($stmt, $refcur);
            } else {
                throw new Exception(" Select Query Error ");
            }
        } catch (Exception $e) {
            return $e;
        }
    }
    
}
?>