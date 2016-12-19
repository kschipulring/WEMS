<?php
     
     require_once("LIRR_DB.php");
     
     class Utilities {
         public static $last_error;
     }
     
    function loadBalanceDatabaseCheck($DB, $interval, $filename) {
        $systemFileName = $filename;
        if (ini_get('upload_tmp_dir')) {
            $systemFileName = ini_get('upload_tmp_dir')."/".$filename;
        }
        if (file_exists($systemFileName)) {
            $Diff = (time() - filemtime($systemFileName));
            //Check the database again if the file modification time is greater than the specified time
            if ($Diff > $interval) {
                return verifyDatabase($DB, $systemFileName);
            } else {
                //We are not doing a DB connection here so set the default header to a valid number.
                return  array("code" => 200, "message" => "You shall pass without a database check.");  #'HTTP/1.0: 201 Ok';
            }
        } else {//file does not exist so create it.
            return verifyDatabase($DB, $systemFileName);
        }
    }
    
    function verifyDatabase($DB, $systemFileName) {
        //Try to make the database connection
        $conn = get_db_connection($DB->username, $DB->password, $DB->descriptor);
        
        //Check to see if a valid connection was made.
        if ($conn) {
            //TODO: Add a query and verify we can retreive results.
            //update the modification time of the file
            if (touch($systemFileName)) {
               //set the http header to a successful state
                return array("code" => 200, "message" => "You Shall Pass");
            } else {
                //set the http header to a failed state because the automation couldn't make the temporary 
                return array("code" => 300, "message" => "Temporay file error with $DB->descriptor: $systemFileName");
            }
        } else {
            //set the http header to a failed state
            return array("code" => 400, "message" => "Bad Database Connection Attempted to: $DB->descriptor");
        }
    }
    
    function get_db_connection($username, $password, $db) {
        try {
            $db = oci_connect($username, $password, $db);
            if ($db) {
                return $db;
            } else {
                return null;
            }
        }
        catch (Exception $Error) {
            return null;
        }
    }
    
    function validateParameters($parameters, $validationArray) {
        if (is_array($parameters)) {
            if (is_array($validationArray)) {
                //Loop through the validation array and look for a matching key.  The method will fail
                //immediately if it does not find a match.
                $validation_bool = true;
                foreach ($validationArray as $key) {
                    if (! isset($parameters[$key])) {
                       $parameterString = implode(" ", $parameters);
                       Utilities::$last_error = "Missing key: $key in $parameterString";
                       $validation_bool = false;
                       break;
                   }
               }
               return $validation_bool;
           } else {
               //Look to see if the passed in validation key exists.
               if (! isset($parameters[$validationArray])) {
                   return false;
               } else {
                   return true;
               }
           }
       } else {
           Utilities::$last_error = "function validateParameters requires an array as the first parameter: $parameters";
           return false;
       }
   }
   
   function set_status_header($code = 200, $text = '') {
       $stati = array(
           200 => 'OK',
           201 => 'Created',
           202 => 'Accepted',
           203 => 'Non-Authoritative Information',
           204 => 'No Content',
           205 => 'Reset Content',
           206 => 'Partial Content',
           
           300 => 'Multiple Choices',
           301 => 'Moved Permanently',
           302 => 'Found',
           304 => 'Not Modified',
           305 => 'Use Proxy',
           307 => 'Temporary Redirect',
           
           400 => 'Bad Request',
           401 => 'Unauthorized',
           403 => 'Forbidden',
           404 => 'Not Found',
           405 => 'Method Not Allowed',
           406 => 'Not Acceptable',
           407 => 'Proxy Authentication Required',
           408 => 'Request Timeout',
           409 => 'Conflict',
           410 => 'Gone',
           411 => 'Length Required',
           412 => 'Precondition Failed',
           413 => 'Request Entity Too Large',
           414 => 'Request-URI Too Long',
           415 => 'Unsupported Media Type',
           416 => 'Requested Range Not Satisfiable',
           417 => 'Expectation Failed',
           451     => 'Censored',
           
           500 => 'Internal Server Error',
           501 => 'Not Implemented',
           502 => 'Bad Gateway',
           503 => 'Service Unavailable',
           504 => 'Gateway Timeout',
           505 => 'HTTP Version Not Supported'
       );
       
       if (isset($stati[$code]) AND $text == '') {
         $text = $stati[$code];
       }
     
       $server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;
     
       if (substr(php_sapi_name(), 0, 3) == 'cgi') {
           header("Status: {$code} {$text}", TRUE);
       } elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0') {
           header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
           header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, proxy-revalidate"); // HTTP/1.1
           header("Pragma: no-cache");
           header("HTTP/1.1 $code $text", TRUE, $code);
       } else {
           header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
           header("Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, proxy-revalidate"); // HTTP/1.1
           header("Pragma: no-cache");
           header("HTTP/1.1 $code $text", TRUE, $code);
       }
   }
   
   
   ?>