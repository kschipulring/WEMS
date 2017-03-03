
<?php
	/*
	$dbusername = "e_wmds_dev";
        $dbpassword = "wayside";
        $database   = "WMDS";

      $link = oci_pconnect ($dbusername, $dbpassword, $database)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');

    $link = oci_pconnect ($dbusername, $dbpassword, $database)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
    
     */   


    $trainNumber = isset($_GET['trainNumber']) ? $_GET['trainNumber'] : -1;
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>M7 Tracking</title>
    <script type="text/javascript" src="http://serverapi.arcgisonline.com/jsapi/arcgis/?v=1.4"></script>
    <style type="text/css">
      @import "http://serverapi.arcgisonline.com/jsapi/arcgis/1/js/dojo/dijit/themes/tundra/tundra.css";
      	
    </style>
    <script type="text/javascript">
        
      dojo.require("esri.map");
      
     
      dojo.require("dijit.form.Button");
 

      
    var map; //represents esri.map
	var xMin, yMin, xMax, yMax;
   	var topoMap; //arcgisonline topomap data
 	var mapType = "topoMap";	
     
     
            
 	
      function init()
	  {
      		esriConfig.defaults.map.sliderLabel = null;
      		map = new esri.Map("map");

     		var startExtent = new esri.geometry.Extent(-73.400, 40.442, -73.050, 41.145,
          	//new esri.SpatialReference({ wkid: 4326 }));
          	
			new esri.SpatialReference({ wkid: 102113 }));
			
      		map.setExtent(startExtent);
            //topoMap = new esri.layers.ArcGISTiledMapServiceLayer("http://gis440.lirr.org", {id:"4326"});   
        	topoMap = new esri.layers.ArcGISTiledMapServiceLayer("http://server.arcgisonline.com/ArcGIS/rest/services/ESRI_Imagery_World_2D/MapServer", {id:"4326"});
        	map.addLayer(topoMap);
        	
        	setInterval("checkdata()", 10000);
        	
        	checkdata();
       	} 
	

		dojo.connect(map, "onLoad", init);

	
     
	function addPointToMap(lon, lat, info) 
	{
		//map.graphics.clear();
		
	    var point = new esri.geometry.Point(lon, lat, map.spatialReference);
	    var symbol = new esri.symbol.SimpleMarkerSymbol().setColor(new dojo.Color([255, 255, 0]));
	    var infoTemp = new esri.InfoTemplate();
	    
	    //var graphic = new esri.Graphic(point, symbol);

	    

        infoTemp.setTitle("Details");
	    
		infoTemp.setContent(info);

		var graphic = new esri.Graphic(point, symbol, infoTemp);
		//map.graphics.cear();
		map.graphics.add(graphic);

		graphic.setInfoTemplate(infoTemp);

		
		
		/*var point = new esri.geometry.Point("-73.52888703346252", "40.76708793640137", map.spatialReference);
	    var symbol = new esri.symbol.SimpleMarkerSymbol().setColor(new dojo.Color([255, 255, 0]));
	    var graphic = new esri.Graphic(point, symbol);
		map.graphics.add(graphic)
		*/
		
		 
		 
		
		
		
	 	//  alert(map.extent.xmax);
	    
	    
	    
	  
	    /*
		
		This will zoom toward the point.
		
		xMin = lon-0.50;
	    yMin = lat - 0.50;
	    xMax = lon + 0.50;
	    yMax = lat + 0.50;
	   	var newExtent = new esri.geometry.Extent();
	   	newExtent.xmin = xMin;
	  	 newExtent.ymin = yMin;
	   	newExtent.xmax = xMax;
	   	newExtent.ymax = yMax;
	   	newExtent.spatialReference = new esri.SpatialReference({ wkid: 4326 });

      	//  alert(map.extent.xmax);
	    map.setExtent(newExtent);
		*/
  
    }
	
	function checkdata()
   	{

		map.graphics.clear();
		//alert("checkdata");
 		//var param = document.getElementById('sysMaintSelect').value;
		 
         if (window.XMLHttpRequest)
         {
               // If IE7, Mozilla, Safari, etc: Use native object
               var client = new XMLHttpRequest();
         }
         else
         {
               if (window.ActiveXObject)
               {
	           // ...otherwise, use the ActiveX control for IE5.x and IE6
	           var client = new ActiveXObject("Microsoft.XMLHTTP");
               }
         }

         
        client.onreadystatechange = function() {handler(client)};
        client.open("GET", "http://webz8dev.lirr.org/~dotero/wmds/classes/ws.php?method=getFleetView");
        client.send("");
       


            function handler(obj)
            {

           	 //alert("2");
           	 //alert(obj.readyState);
			 //alert(obj.status);
			 
               if(obj.readyState == 4 && obj.status == 200)
               {
			   
			  // alert(obj.responseText);
					
                 var val = eval('(' + obj.responseText + ')');

                 //alert(val.items.length);

                 for(var i = 0; i < val.items.length; i++)
                 {

                      
					
                	 var THB = document.createElement('text');
                	 var TN = document.createElement('text');
                     var time = document.createElement('text');
                     var fault = document.createElement('text');
                     var speed = document.createElement('text');
                     var loc = document.createElement('text');
                     var station = document.createElement('text');
					 var POI_code = document.createElement('text');
				     var POI_name = document.createElement('text');
				     var PI_FLAG_IND = document.createElement('text');
				     var SOURCE_CAR_NBR = document.createElement('text');
				     var LAT = document.createElement('text');
				     var LON = document.createElement('text');
                     var MSG_TIMESTAMP = document.createElement('text');
                     var ACTIVE_CAB = document.createElement('text');
                     var HEAD_CAB = document.createElement('text');
                     var TRAIN_DIRECTION = document.createElement('text');
                       	  
				     THB.text = val.items[i].THB_ID;
				     TN.text = val.items[i].TRAIN_NBR;
				     time.text = val.items[i].MSG_TIMESTAMP_DATE_AND_TIME;
				     fault.text = val.items[i].FAULT_STATUS;
				     speed.text = val.items[i].SPEED;
				     loc.text = val.items[i].REPORTING_LOCATION;
				     station.text = val.items[i].STATION;
				     POI_code.text = val.items[i].POI_CODE;
				     POI_name.text = val.items[i].POI_NAME;
				     PI_FLAG_IND.text = val.items[i].PI_FLAG_IND;
				     SOURCE_CAR_NBR.text = val.items[i].SOURCE_CAR_NBR;
				     LAT.text = val.items[i].LATITUDE;
				     LON.text = val.items[i].LONGITUDE;
				     MSG_TIMESTAMP.text = val.items[i].MSG_TIMESTAMP;
				     ACTIVE_CAB.text = val.items[i].ACTIVE_CAB;
				     HEAD_CAB.text = val.items[i].HEAD_CAB;
				     TRAIN_DIRECTION.text = val.items[i].TRAIN_DIRECTION;

				     var trn_info = document.createElement('text');

				     trn_info.text = "Train HB: " + THB.text + "<BR>" + 
				                     "Train Number: " + TN.text + "<BR>" +
				                     "Time: " + time.text + "<BR>" + 
				                     "Fault: " + fault.text + "<BR>" + 
				                     "Speed: " + speed.text + "<BR>" + 
				                     "Location: " + loc.text + "<BR>" + 
				                     "Station: " + station.text + "<BR>" + 
				                     "POI Code: " + POI_code.text + "<BR>" + 
				                     "POI Name: " + POI_name.text + "<BR>" + 
				                     "PI Flag Ind: " + PI_FLAG_IND.text + "<BR>" + 
				                     "Source Car Number: " + SOURCE_CAR_NBR.text + "<BR>" + 
				                     "Msg Time stamp: " + MSG_TIMESTAMP.text + "<BR>" + 
				                     "Active Cab: " + ACTIVE_CAB.text + "<BR>" + 
				                     "Head Car: " + HEAD_CAB.text + "<BR>" + 
				                     "Train Direction: " + TRAIN_DIRECTION.text;
				     //alert(trn_info.text);
				     addPointToMap(LON.text, LAT.text, trn_info.text);
				     
				     //map.graphics.cear();

                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)
         
             }
		

            
	 

	     
		/*
      	//alert("checkdata");

     
	  		if (window.XMLHttpRequest)
			{
      			// If IE7, Mozilla, Safari, etc: Use native object
      			var request = new XMLHttpRequest();
      		}
  			else
    		{
      			if (window.ActiveXObject){
				// ...otherwise, use the ActiveX control for IE5.x and IE6
				var request = new ActiveXObject("Microsoft.XMLHTTP");
      		}
    	}

 
  		
	  		station = station.substring(3,6);

  		//var url = "fetchmessagesasjson.php?loc=" + station;
  		
  		request.open("GET", url, true);

  		request.onreadystatechange=function() {
    	if (request.readyState==4) {
      	if (request.status==200) {
			parseResponse(request.responseText);
		//alert(parseResponse(request.responseText););
      	}
    	
		*/
		 <?php
		
		
		/* 
		$query = oci_parse($link, "select  
			to_number(wmds_lirr_pkg.convertLatLong(hb.LATITUDE)) latitude , 
			to_number(wmds_lirr_pkg.convertLatLong(hb.longitude)) longitude 
			from 
			e_wmds_dev.wmds_rsms_fault_logs f, e_wmds_dev.wmds_cars c   , 
			e_wmds_dev.wmds_nodes n  , e_wmds_dev.WMDS_EDF_FAULT_DEFINITIONS EDF  , 
			e_wmds_dev.wmds_train_heartbeats hb  
			where 
			f.last_update_date > sysdate - 15/60/24 
			and f.lc_last_set_timestamp >= trunc(sysdate)  and f.lc_last_set_timestamp+0 < sysdate + 3    
			and c.car_id = f.car_id  and n.nod_id = f.nod_id  and edf.edf_id = f.edf_id  
			AND hb.longitude <> 0  AND hb.latitude <> 0 and 
			hb.thb_id(+) = wmds_lirr_pkg.getClosestHeartbeatId(f.train_nbr, f.last_set_timestamp)")
         OR die('Oracle error, in parse. Error: ' . print_r(oci_error($link), 1)); 
 
  
       oci_execute($query)
         OR die('Oracle error with execute. Error: ' . print_r(oci_error($query), 1));

      
	   
       while($result = oci_fetch_array($query))
       {
         //$REFID = $result['MAX(REF_ID)'];
		 
		  	$lat = $result['LATITUDE'];
			$lon = $result['LONGITUDE'];
						
 			//echo "addPointToMap(parseFloat(dojo.byId('$lon').value), parseFloat(dojo.byId('$lat').value)); ";
			echo "addPointToMap($lon, $lat); ";
       } 
       
       this works!!!
       
	*/ 
?>
    

		
  	}	
		

      dojo.addOnLoad(init);

    </script>
  </head>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" bgcolor="white">

   <body class="tundra" onload="checkdata();">

    
            <div style="position:relative; left: 0px; top: 0px; width: 800px;">
            <div id="map" style="width:1200px; height:600px; border:1px solid #000;"></div></Div>              

    
 	<!--Longitude <input type="text" id="lon" value="-73.41046214103699" />
 	Latitude <input type="text" id="lat" value="40.85274696350098" />-->
 	
 	Longitude <input type="text" id="lon" value="-73.78841" />
 	Latitude <input type="text" id="lat" value="40.70496" />
 	
    <!--Latitude <input type="text" id="lat" value="40.85274696350098" />-->
    <button onclick="addPointToMap(parseFloat(dojo.byId('lon').value), parseFloat(dojo.byId('lat').value));">Add Point to Map</button>

  </body>

</html>






