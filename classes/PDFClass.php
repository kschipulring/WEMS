<?php
set_include_path(get_include_path() . PATH_SEPARATOR . "/usr/local/zend/var/libraries/tcpdf/6.2.12" );
set_include_path(get_include_path() . PATH_SEPARATOR . "/usr/local/zend/var/libraries/LIRR/1.0.4.3" );
$library_path = '../lib/';
set_include_path(get_include_path() . PATH_SEPARATOR . $library_path);
require_once('tcpdf/tcpdf.php');
class PDF extends TCPDF {
    public function __construct() {
        parent::__construct();
    }
}
?>