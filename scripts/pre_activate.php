<?php
/* The script pre_activate.php should contain code that should make the changes in the server
 * environment so that the application is fully functional. For example, this may include
 * changing symbolic links to "data" directories from previous to current versions,
 * upgrading an existing DB schema, or setting up a "Down for Maintenance"
 * message on the live version of the application
 * The following environment variables are accessable to the script:
 * 
 * - ZS_RUN_ONCE_NODE - a Boolean flag stating whether the current node is
 *   flagged to handle "Run Once" actions. In a cluster, this flag will only be set when
 *   the script is executed on once cluster member, which will allow users to write
 *   code that is only executed once per cluster for all different hook scripts. One example
 *   for such code is setting up the database schema or modifying it. In a
 *   single-server setup, this flag will always be set.
 * - ZS_WEBSERVER_TYPE - will contain a code representing the web server type
 *   ("IIS" or "APACHE")
 * - ZS_WEBSERVER_VERSION - will contain the web server version
 * - ZS_WEBSERVER_UID - will contain the web server user id
 * - ZS_WEBSERVER_GID - will contain the web server user group id
 * - ZS_PHP_VERSION - will contain the PHP version Zend Server uses
 * - ZS_APPLICATION_BASE_DIR - will contain the directory to which the deployed
 *   application is staged.
 * - ZS_CURRENT_APP_VERSION - will contain the version number of the application
 *   being installed, as it is specified in the package descriptor file
 * - ZS_PREVIOUS_APP_VERSION - will contain the previous version of the application
 *   being updated, if any. If this is a new installation, this variable will be
 *   empty. This is useful to detect update scenarios and handle upgrades / downgrades
 *   in hook scripts
 * - ZS_<PARAMNAME> - will contain value of parameter defined in deployment.xml, as specified by
 *   user during deployment.
 */  


/**** Test and processing of environment variable  */
$basedir = getenv('ZS_APPLICATION_BASE_DIR');
chdir($basedir);

$environment = getenv('ZS_APP_ENVIRONMENT');
switch($environment) {
    
    case 'TEST':
        rename('wemsDatabase.test.php', 'wemsDatabase.php');
        unlink('wemsDatabase.prod.php' ) ;
        break;
    
    case 'QA':
        rename('wemsDatabase.test.php', 'wemsDatabase.php');
        unlink('wemsDatabase.prod.php' ) ;
        break;

    
    case 'PRODUCTION':
        rename('wemsDatabase.prod.php', 'wemsDatabase.php');
        unlink('wemsDatabase.test.php' ) ;
        break;
}




/* gets and sets the application version */
file_put_contents('version.php', "<?php \n" . '$_VERSION = "' . getenv('ZS_CURRENT_APP_VERSION') . '";');



$basedir = getenv("ZS_APPLICATION_BASE_DIR");
$applicationLibPath = $basedir . '/lib';

if (file_exists($applicationLibPath)) {
    $calendarLibDirectory = zend_deployment_library_path('jscalendar');
    $target = $applicationLibPath . "/jscalendar";
    symlink($calendarLibDirectory, $target);
    
   
    $excelLibDirectory = zend_deployment_library_path('PHPExcel');
    $eTarget = $applicationLibPath . "/PHPExcel";
    symlink($excelLibDirectory, $eTarget);
    
} else {
    throw new Exception("Missing lib directory", 1201);
}




?>




