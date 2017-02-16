
<!DOCTYPE html>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta name="viewport" content="initial-scale=1, maximum-scale=1,user-scalable=no">
    <title>Identify with Popup</title>

    <link rel="stylesheet" href="https://js.arcgis.com/3.17/esri/css/esri.css">
    <style>
      html, body{
        height:100%;
        width:100%;
        margin:0;
        padding:0;
      }
      
    #header{
        height:10%;
        width:100%;
        margin:0;
        padding:0;
      }
      
        #map{
        height:80%;
        width:100%;
        margin:0;
        padding:0;
      }
        #container{
        height:100%;
        width:100%;
        margin:0;
        padding:0;
      }
    </style>

    <script src="https://js.arcgis.com/3.17/"></script>
    <script>
      var map;

      require([
        "esri/map",
        "esri/InfoTemplate",
        "esri/layers/ArcGISDynamicMapServiceLayer",
        "esri/symbols/SimpleFillSymbol",
        "esri/symbols/SimpleLineSymbol",
        "esri/tasks/IdentifyTask",
        "esri/tasks/IdentifyParameters",
        "esri/dijit/Popup",
        "dojo/_base/array",
        "esri/Color",
        "dojo/dom-construct",
        "dojo/domReady!"
      ], function (
        Map, InfoTemplate, ArcGISDynamicMapServiceLayer, SimpleFillSymbol,
        SimpleLineSymbol, IdentifyTask, IdentifyParameters, Popup,
        arrayUtils, Color, domConstruct
      ) {

        var identifyTask, identifyParams;

        var popup = new Popup({
          fillSymbol: new SimpleFillSymbol(SimpleFillSymbol.STYLE_SOLID,
            new SimpleLineSymbol(SimpleLineSymbol.STYLE_SOLID,
              new Color([255, 0, 0]), 2), new Color([255, 255, 0, 0.25]))
        }, domConstruct.create("div"));

        map = new Map("map", {
          basemap: "satellite",
          center: [ -73.200, 40.800],
          zoom: 10,
          infoWindow: popup
        });

        map.on("load", mapReady);

        
        function executeIdentifyTask (event) 
        {
          identifyParams.geometry = event.mapPoint;
          identifyParams.mapExtent = map.extent;

          var deferred = identifyTask
            .execute(identifyParams)
            .addCallback(function (response) 
                    {
              // response is an array of identify result objects
              // Let's return an array of features.
              return arrayUtils.map(response, function (result) 
                      {
                var feature = result.feature;
                var layerName = result.layerName;
/*
                feature.attributes.layerName = layerName;
                if (layerName === 'Tax Parcels') 
                    {
                  var taxParcelTemplate = new InfoTemplate("",
                    "${Postal Address} <br/> Owner of record: ${First Owner Name}");
                  feature.setInfoTemplate(taxParcelTemplate);
                }
                else if (layerName === 'Building Footprints') 
                    {
                  console.log(feature.attributes.PARCELID);
                  var buildingFootprintTemplate = new InfoTemplate("",
                    "Parcel ID: ${PARCELID}");
                  feature.setInfoTemplate(buildingFootprintTemplate);
                }
                */
                return feature;
              });
          
            });

          // InfoWindow expects an array of features from each deferred
          // object that you pass. If the response from the task execution
          // above is not an array of features, then you need to add a callback
          // like the one above to post-process the response and return an
          // array of features.
          map.infoWindow.setFeatures([deferred]);
          map.infoWindow.show(event.mapPoint);
        }
      });
    </script>
  </head>

  <body>
  	<div id="container">
  		<div id="header">header</div>
    	<div id="map"></div>
    	Container
    </div>
  </body>

</html>