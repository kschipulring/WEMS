
<?php 
/*
$task =  isset($_POST['SUBMIT']) ? $_POST['SUBMIT'] : "";

if($task == "Create Storm")
{
   echo"TEST";
}




<span class="buttons">
		
					<?php echo "<input class=\"submitAdd\" type=\"submit\" value=\"Create Storm\" name=\"SUBMIT\" id=\"SUBMIT\" />"; ?>
    				<?php echo "<input class=\"submitAdd\" type=\"submit\" value=\"Assign Gang\" name=\"SUBMIT\" id=\"SUBMIT\" />"; ?>
    				<?php echo "<input class=\"submitAdd\" type=\"submit\" value=\"Reports\" name=\"SUBMIT\" id=\"SUBMIT\" />"; ?>
    				
    				
    				
    				
    				
    				
         		</span>   





*/

 



//$test = "";

session_start();

if (!isset($_SESSION['loggedin']) )
{
    $_SESSION['loggedin'] = false;
}
/*
require '../wemsDatabase.php';

$c = oci_pconnect ($wemsDBusername, $wemsDBpassword, $wemsDatabase)
OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');


//$gisid = 28000067;

$cDirty = 0;
$cClean = 0;
$cCleaning = 0;

$tDirty = 0;
$tClean = 0;
$tCleaning = 0;

$qry = oci_parse($c, "select MARKERID, MARKERTYPE from location where GIS_JOIN_ID is not null and LOC_CD != 'V'")
OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');

//oci_bind_by_name($qry, ":GIS_ID", $gisid, -1);

oci_execute($qry);

while($row = oci_fetch_array($qry)){
    
    
    $markerID = $row['MARKERID'];
    $markerType = $row['MARKERTYPE'];
    
    $qry2 = oci_parse($c, "select 
    pc.PLATFORM,
	pc.ISDIRTY,
	pc.ISASSIGNED
	from 
    WEMS.PLANT_COMPONENT pc,
    WEMS.CLEANABLE_TARGET ct,
    WEMS.LOCATION l, 
    WEMS.MARKER_CT mc
    Where 
    pc.CTID = ct.CTID and
    mc.MARKERID = l.MARKERID and
    ct.CTID = mc.CTID and
    l.MARKERID = :MARKERID order by ct.SORTKEY")
    OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
    
    oci_bind_by_name($qry2, ":MARKERID",  $markerID, -1);
    
    oci_execute($qry2);
    
   
    
    $isAssigned = 0;
    
    $isdirty = 0;
    
    $cnt = 0;
    
    
    
    while($row = oci_fetch_array($qry2)){
        
        $isAssigned = $isAssigned + $row['ISASSIGNED'];
        
        $isdirty = $isdirty + $row['ISDIRTY'];
        
        
        $cnt = $cnt + 1;
        
                 
        
    }
    
    
    if($isAssigned > 0)
    {
        
        if($markerType == "C")
        {$cCleaning = $cCleaning + 1;}
        else
        {$tCleaning = $tCleaning + 1;}
        
        $qry3 = oci_parse($c, "UPDATE location set STATUS = 0 where MARKERID = :MARKERID")
        OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
        
        oci_bind_by_name($qry3, ":MARKERID", $markerID, -1);
        
        oci_execute($qry3);
        
    }
    else {
            if($cnt == $isdirty) {
                if($markerType == "C")
                    {$cDirty = $cDirty + 1;}
                else 
                    {$tDirty = $tDirty + 1;}
                
               $qry4 = oci_parse($c, "UPDATE location set STATUS = 1 where MARKERID = :MARKERID")
               OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
               
               oci_bind_by_name($qry4, ":MARKERID", $markerID, -1);
               
               oci_execute($qry4);
               
            }
            else if($isdirty > 0)
            {
                if($markerType == "C")
                    {$cBlue = $cBlue + 1;}
                else 
                    {$tBlue = $tBlue + 1;}
                
                
                 $qry5 = oci_parse($c, "UPDATE location set STATUS = 2 where MARKERID = :MARKERID")
                 OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                
                 oci_bind_by_name($qry5, ":MARKERID", $markerID, -1);
                
                  oci_execute($qry5);
                
                
                
            }
            else {
                if($markerType == "C")
                {$cClean = $cClean + 1;}
                else 
                {$tClean = $tClean + 1;}
                
                $qry6 = oci_parse($c, "UPDATE location set STATUS = 3 where MARKERID = :MARKERID")
                OR die('Oracle error, in parse. Error: <pre>' . print_r(oci_error($c), 1) . '</pre>');
                
                oci_bind_by_name($qry6, ":MARKERID", $markerID, -1);
                
                oci_execute($qry6);
            }
            
        }
            

}

*/
 

?>


<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
        
        <title>LIRR M7 Fault Map Viewer</title>
        <link rel="stylesheet" type="text/css" href="//js.arcgis.com/3.14compact/esri/css/esri.css">
        <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" href="css/theme/dbootstrap/dbootstrap.css">
        <link rel="stylesheet" type="text/css" href="css/main.css">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
    </head>
    <body class="dbootstrap">
    
    <form id="HomePage" name="HomePage" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
    
    
    
  	
            
        <div class="appHeader">
            <div class="headerLogo">
                <img alt="logo" src="images/MTA_NYC_logo.svg" height="36" />
            </div>
            <div class="headerTitle">
             
                <span id="headerTitleSpan">
                  WEMS<!--  <font color="#4272AF">...................</font> -->    
                </span>
                
                
                
				
				<span id="headerStatusIconsSpan">  	
					<?php
					include 'getTally.php';
					include_once "../classes/HLfuncs.php";
					?>
					
					Stations:
					<?=HLfuncs::item($sDirty, "dirty") ?>
					<?=HLfuncs::item($sInProgress, "inprogress") ?>
					<?=HLfuncs::item($sClean, "clean", "circle") ?>
					<span class="HLspacer">.....</span> 
					
					<!-- Interlockings:
					<?=HLfuncs::item($iDirty, "dirty", "triangle") ?>
					<?=HLfuncs::item($iInProgress, "inprogress", "triangle") ?>
					<?=HLfuncs::item($iClean, "clean", "triangle") ?>
					<span class="HLspacer">.....</span> 
					 -->
					
					Parking Lots:
					<?=HLfuncs::item($pDirty, "dirty", "rect") ?>
					<?=HLfuncs::item($pInProgress, "inprogress", "rect") ?>
					<?=HLfuncs::item($pClean, "clean", "rect") ?>
				</span>       

                <div id="subHeaderTitleSpan" class="subHeaderTitle">
                
                
                </div>
            </div>
             
            <div class="search wems_gis">
                <div id='geocodeDijit'>
                </div>
            </div>
            <div class="headerLinks wems_gis">
           		<div id="helpDijit">
                </div>
            <div> 
		
		
    	
   
    
    
    </div>
            </div>
            
           
           
        </div>
         
        <script type="text/javascript">
            var dojoConfig = {
                async: true,
                packages: [{
                    name: 'viewer',
                    location: location.pathname.replace(/[^\/]+$/, '') + 'js/viewer'
                },{
                    name: 'config',
                    location: location.pathname.replace(/[^\/]+$/, '') + 'js/config'
                },{
                    name: 'gis',
                    location: location.pathname.replace(/[^\/]+$/, '') + 'js/gis'
                }]
            };
        </script>
        <!--[if lt IE 9]>
            <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/es5-shim/4.0.3/es5-shim.min.js"></script>
        <![endif]-->
        <script type="text/javascript" src="//js.arcgis.com/3.14compact/"></script>
        <script type="text/javascript" src="js/viewer/getTally.js"></script>
        <script type="text/javascript">
            // get the config file from the url if present
            var file = 'config/viewer', s = window.location.search, q = s.match(/config=([^&]*)/i);
            if (q && q.length > 0) {
                file = q[1];
                if(file.indexOf('/') < 0) {
                    file = 'config/' + file;
                }
            }
            require(['viewer/Controller', file], function(Controller, config){
                Controller.startup(config);
            });
        </script>
        <div class="Botton">
             
          
         </div>
          </form>
    </body>
</html>
