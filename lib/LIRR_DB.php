 <?php
    class LIRR_DB {
		public $wemsDBusername;
		public $wemsDBpassword;
		public $wemsDatabase;
		public $connection = null;
		public $connectionFactory = "oracle";
        
        
          
        public function __construct($cmcDBusername, $cmcDBpassword, $cmcDatabase)
        {
            $this->username = $cmcDBusername;
            $this->password = $cmcDBpassword;
            $this->descriptor = $cmcDatabase;
        }
        
        public function ZendConnect($db_params) {
            require_once "Zend/Registry.php";
            require_once "Zend/Db.php";
            
            if (!extension_loaded('oci8')) {
                throw new Exception('The OCI8 extension is required for this adapter but the extension is not loaded', 399);
            }
    
            try {
                $db = Zend_Db::factory($this->connectionFactory, $db_params);
                $db->getConnection();
                if ($this->connectionFactory == "oracle") {
                    $db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'MM/DD/YYYY HH24:MI:SS'");
                }
                $this->connection = $db;
            } catch (Zend_Db_Adapter_Exception $e) {
                print "perhaps a failed login credential, or perhaps the RDBMS is not running: " . $e->getMessage()."\n";
            } catch (Zend_Exception $e) {
                print "perhaps factory() failed to load the specified Adapter class: ". $e->getMessage()."\n";
            }
        }   
    }
    
    ?>