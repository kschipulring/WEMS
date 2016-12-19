<?php

class Session {
  //quick flag allow easy switching to php file sessions
  const USE_DATABASE = true;

  private $life_time;   //how long the session data should live
  private $db_link;
  private $db_table_name;

  private $db_name;
  private $db_username;
  private $db_password;

  private $app_id;

  private $session_save_path;
  private $session_name;

  private $last_error;

  private static $instance;
    
  // The start function should be called before php's session_start()
  public static function start($app_id, $db_name, $db_username, $db_password, $table_name = "PHP_SESSION") {
    if(!empty(self::$instance))
      return;

    if(session_id() == "") {
      self::$instance = new Session($app_id, $db_name, $db_username, $db_password, $table_name);
      session_start();
    }
  }

  // Returns a reference to the Session object, shouldn't be needed in most cases
  public static function instance() {
    return self::$instance;
  }

  // returns true if no errors have occured
  public function is_ok() {
    return empty($this->last_error);
  }

  // Dumps the contets of the Session, useful for debugging
  public function dump($as_str = false) {
    $dump = '<pre>' . print_r($_SESSION, 1) . '</pre>';
    if(!$as_str)
      echo $dump;
    return $dump;
  }

  // Retrieve the last error that occued, 99/100 times this will be an oci error.
  public function get_last_error() {
    return $this->last_error;
  }

  // The following functions are used by php and should not be called directly
  //Callback function for opening the session
  public function open($save_path, $session_name) {
    $this->session_save_path = $save_path;
    $this->session_name = $session_name;
    return true;
  }

  //Callback function for closing the session
  public function close() {
    return true;
  }

  //Callback function for reading data into the session
  public function read($id) {
    $time = time();
    $data = '';

    $sql = "select session_data from $this->db_table_name 
            where session_id = :session_id 
            and expires > :expires and application_id = :application";

    if(!$query = oci_parse($this->db_link, $sql)) {
      $this->last_error = print_r(oci_error($this->db_link), 1); 
      return $data;
    }

    oci_bind_by_name($query, ":session_id", $id, -1);
    oci_bind_by_name($query, ":expires", $time, -1); 
    oci_bind_by_name($query, ":application", $this->app_id, -1); 

    if(!oci_execute($query)) {
      $this->last_error = print_r(oci_error($this->db_link), 1);
      return $data;
    }
    
    if($result = oci_fetch_array($query)) {
      $data = $result['SESSION_DATA']->load();
    }
    return $data;
  }

  //Callback function to write data from php into the database.
  public function write($id, $data) {
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $time = time() + $this->life_time;

    $sql = "select session_id from $this->db_table_name where
            session_id = :session_id and
            application_id = :application";

    if(!$query = oci_parse($this->db_link, $sql)) {
      $this->last_error = print_r(oci_error($this->db_link), 1); 
      return false;
    }
    
    oci_bind_by_name($query, ":session_id", $id, -1);
    oci_bind_by_name($query, ":application", $this->app_id, -1);

    
    if(!oci_execute($query)) {
      $this->last_error = print_r(oci_error($this->db_link), 1);
      return false;
    }
    
    if($result = oci_fetch_array($query)) { //update
      $sql = "update $this->db_table_name set expires = :expires,
                session_data = EMPTY_CLOB() where
                session_id = :session_id and
                application_id = :application and
                ip_address = :ip_address
                returning session_data into :session_data";
      
      if(!$query = oci_parse($this->db_link, $sql)) {
	$this->last_error = print_r(oci_error($this->db_link), 1); 
	return false;
      }

      $temp_clob = oci_new_descriptor($this->db_link, OCI_D_LOB);

      oci_bind_by_name($query, ":expires", $time, -1);
      oci_bind_by_name($query, ":session_data", $temp_clob, -1, OCI_B_CLOB);
      oci_bind_by_name($query, ":session_id", $id, -1);
      oci_bind_by_name($query, ":ip_address", $ip_address, -1);
      oci_bind_by_name($query, ":application", $this->app_id, -1);
      
      if(!oci_execute($query, OCI_DEFAULT)) {
	$this->last_error = print_r(oci_error($this->db_link), 1);
	return false;
      }
      if(!$temp_clob->save($data)) {
	$this->last_error = print_r(oci_error($this->db_link), 1); 
	oci_rollback($this->db_link);
	return false;
      }

      oci_commit($this->db_link);
      $temp_clob->free();
    } 
    else { //insert
      $sql = "insert into $this->db_table_name(session_id, expires, session_data, application_id, ip_address) 
              values(:session_id, :expires, EMPTY_CLOB(), :application, :ip_address) 
              returning session_data into :session_data";
	
      if(!$query = oci_parse($this->db_link, $sql)) {
	$this->last_error = print_r(oci_error($this->db_link), 1); 
	return false;
      }

      $temp_clob = oci_new_descriptor($this->db_link, OCI_D_LOB);

      oci_bind_by_name($query, ":session_id", $id, -1);
      oci_bind_by_name($query, ":expires", $time, -1);
      oci_bind_by_name($query, ":session_data", $temp_clob, -1, OCI_B_CLOB);
      oci_bind_by_name($query, ":ip_address", $ip_address, -1);
      oci_bind_by_name($query, ":application", $this->app_id, -1);

      if(!oci_execute($query, OCI_DEFAULT)) {
	$this->last_error = print_r(oci_error($this->db_link), 1);
	return false;
      }

      if(!$temp_clob->save($data)) {
	$this->last_error = print_r(oci_error($this->db_link), 1); 
	oci_rollback($this->db_link);
	return false;
      }
      oci_commit($this->db_link);
      $temp_clob->free();
    }
    return true;
  }

  //Callback function for destroying session data
  public function destroy($id) {
    $sql = "delete from $this->db_table_name where 
            session_id = :session_id and application_id = :application";

    if(!$query = oci_parse($this->db_link, $sql)) {
      $this->last_error = print_r(oci_error($this->db_link), 1); 
      return false;
    }

    oci_bind_by_name($query, ":session_id", $id, -1);
    oci_bind_by_name($query, ":application", $this->app_id, -1);

    if(!oci_execute($query)) {
      $this->last_error = print_r(oci_error($this->db_link), 1);
      return false;
    }

    return true;
  }

  //Callback function for the garbage collection
  public function gc() {
    $time = time();
    $sql = "delete from $this->db_table_name where 
            expires <= :expires and application_id = :application";

    if(!$query = oci_parse($this->db_link, $sql)) {
      $this->last_error = print_r(oci_error($this->db_link), 1); 
      return false;
    }

    oci_bind_by_name($query, ":expires", $time, -1);
    oci_bind_by_name($query, ":application", $this->app_id, -1);
    
    if(!oci_execute($query)) {
      $this->last_error = print_r(oci_error($this->db_link), 1);
      return false;
    }

    return true;
  }

  private function __construct($app_id, $db_name, $db_username, $db_password, $table_name) {
    $this->life_time = get_cfg_var("session.gc_maxlifetime");
    
    $this->db_table_name = $table_name;
    $this->db_name       = $db_name;
    $this->db_username   = $db_username;
    $this->db_password   = $db_password;
    $this->app_id        = $app_id;

    if(self::USE_DATABASE) {
      //connect to the database
      $this->db_link = @oci_pconnect($this->db_username, $this->db_password, $this->db_name) or
	$this->error_message('Failed to connect to database: ' . print_r(oci_error(), 1));
      
      session_set_save_handler(array(&$this, "open"),
			       array(&$this, "close"),
			       array(&$this, "read"),
			       array(&$this, "write"),
			       array(&$this, "destroy"),
			       array(&$this, "gc")
			       );
    }
  }

  public function __destruct() {
    @session_write_close();
    if($this->db_link)
      oci_close($this->db_link);
  }

  private function error_message($text) {
    $this->last_error = $text;
  }

}

?>