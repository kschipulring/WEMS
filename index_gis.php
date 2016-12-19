
<?php
	
	$dbusername = "e_wmds_dev";
        $dbpassword = "wayside";
        $database   = "WMDS";

      $link = oci_pconnect ($dbusername, $dbpassword, $database)
        OR die('Unable to connect to the database. Error: <pre>' . print_r(oci_error(),1) . '</pre>');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
 
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>M7 Tracking</title>
    <script type="text/javascript" src="http://serverapi.arcgisonline.com/jsapi/arcgis/?v=1.5"></script>
    <style type="text/css">
      @import "http://serverapi.arcgisonline.com/jsapi/arcgis/1/js/dojo/dijit/themes/tundra/tundra.css";

      
      
     
    </style>
    <script language="javascript" type="text/javascript">
	 
      dojo.require("esri.tasks.identify");
      dojo.require("dijit.layout.ContentPane");
      dojo.require("dijit.layout.TabContainer");


        
      dojo.require("esri.map");
      
     
      dojo.require("dijit.form.Button");
 

      
    var map; //represents esri.map
	var xMin, yMin, xMax, yMax;
   	var topoMap; //arcgisonline topomap data
 	var mapType = "topoMap";	
     
    var identifyTask, identifyParams, symbol;
    var layer2results, layer3results, layer4results;
 
            
 	
      function init()
	  {
	  		//alert("init");
	  
      		esriConfig.defaults.map.sliderLabel = null;
      		//map = new esri.Map("map");
			map = new esri.Map("map", { extent: new esri.geometry.Extent(-74.500, 40.442, -72.200, 41.075, new esri.SpatialReference({wkid:4326})) });
			
			//var tiledMapServiceLayer = new esri.layers.ArcGISTiledMapServiceLayer("http://server.arcgisonline.com/ArcGIS/rest/services/ESRI_StreetMap_World_2D/MapServer");
			//var tiledMapServiceLayer = new esri.layers.ArcGISTiledMapServiceLayer("http://arcgis-jam/ArcGIS/rest/services/M7LocLIRRBackground/MapServer");
         //map.addLayer(new esri.layers.ArcGISDynamicMapServiceLayer("http://arcgis-jam/ArcGIS/rest/services/AVLM/MapServer"));

			//var locator = new esri.tasks.Locator("http://sampleserver1.arcgisonline.com/ArcGIS/rest/services/Locators/ESRI_Geocode_USA/GeocodeServer");
			//var infoTemplate = new esri.InfoTemplate("Location", "Street: ${Address}<br />City: ${City}<br />State: ${State}<br />Zip: ${Zip}");
        var symbol = new esri.symbol.SimpleMarkerSymbol(esri.symbol.SimpleMarkerSymbol.STYLE_CIRCLE, 15, new esri.symbol.SimpleLineSymbol(esri.symbol.SimpleLineSymbol.STYLE_SOLID, new dojo.Color([0,0,255]), 2), new dojo.Color([0,0,255]));


     		
        	
			//topoMap = new esri.layers.ArcGISTiledMapServiceLayer("http://server.arcgisonline.com/ArcGIS/rest/services/ESRI_StreetMap_World_2D/MapServer", {id:"4326"});
        	//map.addLayer(topoMap);
        	
        	//topoMap = new esri.layers.ArcGISTiledMapServiceLayer("http://arcgis-jam/ArcGIS/rest/services/M7Locations280/MapServer", {id:"4326"});
        	//map.addLayer(new esri.layers.ArcGISDynamicMapServiceLayer("http://arcgis-jam/ArcGIS/rest/services/AVLM IS/MapServer"));

        	
        	setInterval("checkdata()", 30000);
            checkdata();  
			
			
			/*dojo.connect(locator, "onLocationToAddressComplete", function(candidate) {
			
          if (candidate.address) {
            var graphic = new esri.Graphic(candidate.location, symbol, candidate.address, infoTemplate);
            //map.graphics.add(graphic);
            map.infoWindow.setTitle(graphic.getTitle());
			//alert(graphic.getTitle());
            map.infoWindow.setContent(graphic.getContent());
			//alert(graphic.getContent());
            var screenPnt = map.toScreen(candidate.location);

            map.infoWindow.show(screenPnt,map.getInfoWindowAnchor(screenPnt));
          }
		  
        });*/

        //dojo.connect(map, "onClick", graphicInfo);

		  dojo.connect(map, 'onLoad', function(evt){
		  
		  	dojo.connect(evt.graphics, "onClick", graphicInfo);

		  
		  });	
			
       	} 
	

		dojo.connect(map, "onLoad", init);
		
		
	function graphicInfo(evt)
	{
			//map.infoWindow.setTitle("Graphic Info");
        	//map.infoWindow.setContent("lat/lon : " + evt.mapPoint.y + ", " + evt.mapPoint.x +
         	// "<br />screen x/y : " + evt.screenPoint.x + ", " + evt.screenPoint.y + "<br /> Description:");
			
			//map.infoWindow.setContent("lat/lon : " );
			
        	map.infoWindow.show(evt.screenPoint,map.getInfoWindowAnchor(evt.screenPoint));

	}

	
     
	function addPointToMap(lon, lat, info) 
	{

		//map.graphics.cear();

	    var point = new esri.geometry.Point(lon, lat, map.spatialReference);
	    var symbol = new esri.symbol.SimpleMarkerSymbol().setColor(new dojo.Color([255, 255, 0]));
		var infoTemp = new esri.InfoTemplate();
		
		infoTemp.setTitle("Details");
	    
		infoTemp.setContent(info);
		
		
		
		//var graphic = new esri.Graphic(point, symbol, infoTemplate);
		var graphic = new esri.Graphic(point, symbol, infoTemp);
		
		//graphic = graphic + graphic.attributes.FIELD_NAME + " Field (<A href='#' onclick='showFeature(featureSet.features[" + i + "]);'>show</A>)<br/>";
		
		//content = content + graphic.attributes.FIELD_NAME + " Field (<A href='#' onclick='showFeature(featureSet.features[" + i + "]);'>show</A>)<br/>";
		
		map.graphics.add(graphic);
		graphic.setInfoTemplate(infoTemp);
		
		/*
		for (var i=0; i<numFeatures; i++) 
		{
          var graphic = featureSet.features[i];
          
        }
*/
		
  
    }
	
	
	
	
	
	
	
	
	function showFeature() {
       /*map.graphics.clear();

        //set symbol
        var symbol = new esri.symbol.SimpleFillSymbol(esri.symbol.SimpleFillSymbol.STYLE_SOLID, new esri.symbol.SimpleLineSymbol(esri.symbol.SimpleLineSymbol.STYLE_SOLID, new dojo.Color([255,0,0]), 2), new dojo.Color([255,255,0,0.5]));
        feature.setSymbol(symbol);

        //construct infowindow title and content
        var attr = feature.attributes;
        var title = attr.FIELD_NAME;
        var content = "Field ID : " + attr.FIELD_KID
                    + "<br />Produces Gas : " + attr.PROD_GAS
                    + "<br />Produces Oil : " + attr.PROD_OIL
                    + "<br />Status : " + attr.STATUS;
        map.graphics.add(feature);

        map.infoWindow.setTitle(title);
        map.infoWindow.setContent(content);

        (evt) ? map.infoWindow.show(evt.screenPoint,map.getInfoWindowAnchor(evt.screenPoint)) : null;
		*/
		
		alert("showFeature");
      }

	

	
	
	function checkdata()
   	{
     
   			

      			if (window.XMLHttpRequest)
				{
      				var request = new XMLHttpRequest();
      			}
       			 else
      			{
      				if (window.ActiveXObject)
					{
						var request = new ActiveXObject("Microsoft.XMLHTTP");
      				}
    			}

  				var url = "fetchData.php";
				
  				request.open("GET", url, true);

  				request.onreadystatechange=function() {
    				if (request.readyState==4) {
      					if (request.status==200) {
						
					    parseResponse(request.responseText);
					
					
      				}
				}
  			}
					
			request.send(null);
  			return true;
			

 				
		
		 <?php 
?>

  	}	
		
		
	function parseResponse(data)
    {
		//var infoAboutFault = "This is information about the fault that we will get from the database";
		map.graphics.clear();
		//alert(data);
		var parse = eval("(" + data + ")");
		
		
		//alert(data);
		//alert(parse.address[0].city);
		//alert(parse.latlon[0].lat);
		//alert(parse.latlon.length);
		
		for(var i = 0; i < parse.latlon.length; i++)
      	{
			

			//addPointToMap(parse.latlon[i].lon, parse.latlon[i].lat, infoAboutFault) 
			
			var infoAboutFault = parse.latlon[i].desc + "<br> Fault Number: " +  parse.latlon[i].code +  "<br>" + parse.latlon[i].dteTm;
			
			//addPointToMap(parse.latlon[i].lon, parse.latlon[i].lat, parse.latlon[i].desc, parse.latlon[i].code);
			addPointToMap(parse.latlon[i].lon, parse.latlon[i].lat, infoAboutFault)
			//alert(parse.latlon[i].lat);
			//alert(parse.latlon[i].lon);
	  	}

	}
	
	
      dojo.addOnLoad(init);

    </script>
  </head>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" bgcolor="white">

   <body class="tundra">
   
    
            <div style="position:relative; left: 0px; top: 0px; width: 800px;">
            <div id="map" style="width:1200px; height:600px; border:1px solid #000;"></div></Div>              

    <!--  
 	Longitude <input type="text" id="lon" value="-73.41046214103699" />

    Latitude <input type="text" id="lat" value="40.85274696350098" />
    -->
    <!--Latitude <input type="text" id="lat" value="40.85274696350098" />-->
    <!--  <button onclick="addPointToMap(parseFloat(dojo.byId('lon').value), parseFloat(dojo.byId('lat').value));">Add Point to Map</button>-->
    
    
    
   
            
           
    
    
    
    
    
    

  </body>

</html>






