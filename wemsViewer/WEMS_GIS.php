<?php 
session_start();

if (!isset($_SESSION['loggedin']) ){
    $_SESSION['loggedin'] = false;
}

//now load classes easily without worrying about including their files
spl_autoload_register(function ($class) {
	include_once "../classes/{$class}Class.php";
});
?>
<!DOCTYPE html>
<html>
	<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        
        <title>LIRR M7 Fault Map Viewer</title>
        <link rel="stylesheet" type="text/css" href="//js.arcgis.com/3.14compact/esri/css/esri.css" />
        <link rel="stylesheet" type="text/css" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="css/theme/dbootstrap/dbootstrap.css" />
        <link rel="stylesheet" type="text/css" href="css/main.css" /> 
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
	</head>
	
	<?php
	//this is to get a css class based upon which browser is being used
	$bodyClass = utilities::getBrowser(true);
	?>
	<body class="dbootstrap <?php echo $bodyClass; ?>">
	    <form id="HomePage" name="HomePage" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	        <div class="appHeader">
	            <div class="headerLogo">
	                <img alt="logo" src="images/MTA_NYC_logo.svg" height="36" />
	            </div>
	            <div class="headerTitle">
	                <span id="headerTitleSpan">
	                  WEMS<!--  <font color="#4272AF">...................</font> -->    
	                </span>
					<span id="headerStatusIconsSpan" rel="test">  	
						<?php
						include 'getTally.php';
						include_once "../classes/HLfuncsClass.php";
						
						//basic svg sprite for the header
						$hlSVG1 = new HLfuncs();
						
						//import the gradient for half clean icons
						$hlStripe = new HLfuncs("../images/half-clean-stripe.svg");
						?>
						
						Stations:
						<?=$hlSVG1->item($sDirty, "dirty", "circle", "Stations Dirty") ?>
						<?=$hlSVG1->item($sInProgress, "inprogress", "circle", "Stations In Progress") ?>
						<?=$hlSVG1->item($sClean, "clean", "circle", "Stations Clean") ?>
						
						<!--  
						<span class="striped circle"></span> 
						<?= HLfuncs::label($sHalfClean, "halfClean") ?>
						-->
						<?=$hlSVG1->item($sHalfClean, "halfClean", "circle", "Stations Half Clean", "gradient_element") ?>
						<span class="HLspacer">.....</span> 
						
						<!-- Interlockings:
						<?=$hlSVG1->item($iDirty, "dirty", "triangle", "Interlockings Dirty") ?>
						<?=$hlSVG1->item($iInProgress, "inprogress", "triangle", "Interlockings In Progress") ?>
						<?=$hlSVG1->item($iClean, "clean", "triangle", "Interlockings Clean") ?>
						<span class="HLspacer">.....</span> 
						 -->
						
						Parking Lots:
						<?=$hlSVG1->item($pDirty, "dirty", "rect", "Parking Lots Dirty") ?>
						<?=$hlSVG1->item($pInProgress, "inprogress", "rect", "Parking Lots In Progress") ?>
						<?=$hlSVG1->item($pClean, "clean", "rect", "Parking Lots Clean") ?>
						<!--<span class="striped rect"></span>-->
					</span>

					<div id="subHeaderTitleSpan" class="subHeaderTitle"></div>
				</div>
				<div class="search wems_gis">
		                <div id='geocodeDijit'></div>
				</div>
				<div class="headerLinks wems_gis">
					<div id="helpDijit"></div>
					<div></div>
				</div>
			</div>
	        <div class="Botton"></div>
		</form>
	</body>
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
	<script type="text/javascript" src="js/viewer/gis.js"></script> 
	
</html>
