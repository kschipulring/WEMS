<html>
  <head>
    <script type="text/javascript">var djConfig = {parseOnLoad: true};</script>
    <script type="text/javascript" src="http://serverapi.arcgisonline.com/jsapi/arcgis/?v=2.4"></script>
    <script type="text/javascript">
      dojo.require("esri.map");
      // var service_url = 'http://sampleserver3.arcgisonline.com/ArcGIS/rest/services/Hurricanes/NOAA_Tracks_1851_2007/MapServer/layers';
      var service_url = 'http://arcgisprod10.lirr.org/arcgis/rest/services/WEMS/MapServer';

      function init() {
        esri.request({
          url: service_url,
          content: { f: 'json' },
          callbackParamName: 'callback',
          load: processServiceInfo,
          error: errorHandler
        });
      }
      // Runs once
      function processServiceInfo(info) {
        console.log('svc info: ', info);
        dojo.byId('info').innerHTML = '';
        dojo.forEach(info.layers, function(lyr) {

          // Add a new div for each Layer
          var lyr_div = dojo.create('div', { 
            id: 'layer_' + lyr.id,
            innerHTML: '<strong>Layer: ' + lyr.name + '</strong><br />'
          }, dojo.byId('info'));

          dojo.forEach(lyr.fields, function(field) {
            lyr_div.innerHTML += 'Name: ' + field.name + '; Alias: ' + field.alias + '<br />';
          });
        });
      }

      function errorHandler(err) {
        console.log('error: ', err);
      }

      dojo.ready(init);
    </script>
  </head>
  <body>
    <div id="info">field names and aliases will show up here.</div>
  </body>
</html>