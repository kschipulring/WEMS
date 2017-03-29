define([
    'esri/dijit/Basemap',
    'esri/dijit/BasemapLayer',
    'esri/layers/osm'
], function (Basemap, BasemapLayer, osm ) {
    return {
        map: true, // needs a refrence to the map
        mode: 'custom', //must be either 'agol' or 'custom'
        title: 'Basemaps', // tilte for widget
        mapStartBasemap: 'gray', // must match one of the basemap keys below
        //basemaps to show in menu. define in basemaps object below and reference by name here
        // TODO Is this array necessary when the same keys are explicitly included/excluded below?
        //basemapsToShow: ['streets', 'satellite', 'hybrid', 'topo', 'lightGray', 'gray', 'national-geographic', 'osm', 'oceans'],
		
		basemapsToShow: ['lirrbase', 'streets', 'googleStreetOnline', 'satellite', 'hybrid', 'gray'],
        // define all valid custom basemaps here. Object of Basemap objects. For custom basemaps, the key name and basemap id must match.
       basemaps: { // agol basemaps
           /* streets: {
                title: 'Streets'
            },
            satellite: {
                title: 'Satellite'
            },
            hybrid: {
                title: 'Hybrid'
            },
            topo: {
                title: 'Topo'
            },
            gray: {
                title: 'Gray'
            },
            oceans: {
                title: 'Oceans'
            },
            'national-geographic': {
                title: 'Nat Geo'
            },
            osm: {
                title: 'Open Street Map'
            }*/
			
			
            // examples of custom basemaps 
    	   /*
    	   lirrbase: {
               title: 'LIRR_BASEMAP',
               basemap: new Basemap({
                   id: 'lirrbase',
                   layers: [new BasemapLayer({
                       url: 'http://arcgisprod10.lirr.org/arcgis/rest/services/LIRR_BASEMAP_V1/MapServer'
                   })]
               })
           },
           */
			streets: {
                title: 'Streets',
                basemap: new Basemap({
                    id: 'streets',
                    layers: [new BasemapLayer({
                        url: 'http://services.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer'
                    })]
                })
            },
			googleStreetOnline: {
                    title: 'Google Street',
                    basemap: new Basemap({
                        id: 'googleStreetOnline',
                        layers: [new BasemapLayer({
                            url: "http://mt${subDomain}.google.com/vt/lyrs=m&hl=en&gl=en&x=${col}&y=${row}&z=${level}&s=png",
                            copyright: "Google Street Map",
                            id: "googleStreetOnline",
                            subDomains: ["0", "1", "2", "3"],
                            type:"WebTiledLayer"
                            })
                        ]
                    })
        },
            satellite: {
                title: 'Satellite',
                basemap: new Basemap({
                    id: 'satellite',
                    layers: [new BasemapLayer({
                        url: 'http://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer'
                    })]
                })
            },
            hybrid: {
                title: 'Hybrid',
                basemap: new Basemap({
                    id: 'hybrid',
                    layers: [new BasemapLayer({
                        url: 'http://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer'
                    }), new BasemapLayer({
                        url: 'http://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer',
                        isReference: true,
                        displayLevels: [0, 1, 2, 3, 4, 5, 6, 7]
                    }), new BasemapLayer({
                        url: 'http://services.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer',
                        isReference: true,
                        displayLevels: [8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19]
                    })]
                })
            },
            gray: {
                title: 'Light Gray Canvas',
                basemap: new Basemap({
                    id: 'gray',
                    layers: [new BasemapLayer({
                        url: 'http://services.arcgisonline.com/ArcGIS/rest/services/Canvas/World_Light_Gray_Base/MapServer'
                    })]
                })
            },
			nysortho: {
                title: 'NYS Ortho 2012 & 2013',
                basemap: new Basemap({
                    id: 'nysortho',
                    layers: [new BasemapLayer({
                        url: 'http://www.mokoon.dhses.ny.gov/ArcGIS/rest/services/2013/MapServer'
                    }), new BasemapLayer({
                        url: 'http://www.cacaroon.dhses.ny.gov/ArcGIS/rest/services/2012/MapServer',
                        isReference: true
                    })]
                })
            } 
        }
    };
});