
<?php
	

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
  
    }
	
	function checkdata()
   	{

		map.graphics.clear();
		
		 
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
        client.open("GET", "http://webz8dev.lirr.org/~dotero/wmds/classes/ws.php?method=getTrainInfo&trainNumber=<?php echo $trainNumber ?>");
        //client.open("GET", "http://webz8dev.lirr.org/~dotero/wmds/classes/ws.php?method=getTrainInfo&trainNumber=731");
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
                	
                	 var TN = document.createElement('text');
                	 var SOURCE_CAR_NBR = document.createElement('text');
                	 var LAT = document.createElement('text');
				     var LON = document.createElement('text');
				     var rpt_time = document.createElement('text');
				     var cur_time = document.createElement('text');
				     var speed = document.createElement('text');
				     var TRAIN_DIRECTION = document.createElement('text');
				     var POI_name = document.createElement('text');
				     var POI_code = document.createElement('text');
					 var car1 = document.createElement('text');
					 var car2 = document.createElement('text');
					 var car3 = document.createElement('text');
					 var car4 = document.createElement('text');
					 var car5 = document.createElement('text');
					 var car6 = document.createElement('text');
					 var car7 = document.createElement('text');
					 var car8 = document.createElement('text');
					 var car9 = document.createElement('text');
					 var car10 = document.createElement('text');
					 var car11 = document.createElement('text');
					 var car12 = document.createElement('text');
					 var car13 = document.createElement('text');
					 var car14 = document.createElement('text');

				     TN.text = val.items[i].TRAIN_NBR;
				     SOURCE_CAR_NBR.text = val.items[i].SOURCE_CAR_NBR;
				     LAT.text = val.items[i].LATITUDE;
				     LON.text = val.items[i].LONGITUDE;
				     rpt_time.text = val.items[i].REPORTED_TIME;
				     cur_time.text = val.items[i].CUR_TIME;
				     speed.text = val.items[i].SPEED;
				     TRAIN_DIRECTION.text = val.items[i].DIRECTION;
				     POI_name.text = val.items[i].POI_NAME;
				     POI_code.text = val.items[i].POI_CODE;
				     car1.text = val.items[i].CAR1;
					 car2.text = val.items[i].CAR2;
					 car3.text = val.items[i].CAR3;
					 car4.text = val.items[i].CAR4;
					 car5.text = val.items[i].CAR5;
					 car6.text = val.items[i].CAR6;
					 car7.text = val.items[i].CAR7;
					 car8.text = val.items[i].CAR8;
					 car9.text = val.items[i].CAR9;
					 car10.text = val.items[i].CAR10;
					 car11.text = val.items[i].CAR11;
					 car12.text = val.items[i].CAR12;
					 car13.text = val.items[i].CAR13;
					 car14.text = val.items[i].CAR14;
					

				     
				     var trn_info = document.createElement('text');
				     

				     trn_info.text = "Train Number: " + TN.text + "<BR>" +
				                     "Source Car Number: " + SOURCE_CAR_NBR.text + "<BR>" + 
				                     "Reported Time : " + rpt_time.text + "<BR>" + 
				                     "Speed: " + speed.text + "<BR>" + 
				                     "Train Direction: " + TRAIN_DIRECTION.text + "<BR>" + 
				                     "POI Name: " + POI_name.text + "<BR>" + 
				                     "POI Code: " + POI_code.text + "<BR>" + 
				                     "Car 1: " + car1.text + "<BR>" + 
				                     "Car 2: " + car2.text + "<BR>" + 
				                     "Car 3: " + car3.text + "<BR>" + 
				                     "Car 4: " + car4.text + "<BR>" + 
				                     "Car 5: " + car5.text + "<BR>" + 
				                     "Car 6: " + car6.text + "<BR>" + 
				                     "Car 7: " + car7.text + "<BR>" + 
				                     "Car 8: " + car8.text + "<BR>" + 
				                     "Car 9: " + car9.text + "<BR>" + 
				                     "Car 10: " + car10.text + "<BR>" + 
				                     "Car 11: " + car11.text + "<BR>" + 
				                     "Car 12: " + car12.text + "<BR>" + 
				                     "Car 13: " + car13.text + "<BR>" + 
				                     "Car 14: " + car14.text + "<BR>";








				                     
				     //alert(trn_info.text);
				     addPointToMap(LON.text, LAT.text, trn_info.text);
				     
				   
                 } //end for(var i = 0; i < val.length; i++)
               } // end if(obj.readyState == 4 && obj.status == 200)
         
             }
		

		
  	}	
		

      dojo.addOnLoad(init);

      

    </script>
  </head>
<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" bgcolor="white">

   <body class="tundra" onload="checkdata();">
   
    
            <div style="position:relative; left: 0px; top: 0px; width: 800px;">
            <div id="map" style="width:1200px; height:600px; border:1px solid #000;"></div></Div>              

    
 	
  </body>

</html>






