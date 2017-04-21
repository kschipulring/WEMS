define([
   'esri/units',
   'esri/geometry/Extent',
   'esri/config',
   'esri/tasks/GeometryService',
   'esri/layers/ImageParameters',
   'esri/dijit/Basemap', 
   'esri/dijit/BasemapLayer',
   'esri/geometry/Point'
], function (units, Extent, esriConfig, GeometryService, ImageParameters) {

    // url to your proxy page, must be on same machine hosting you app. See proxy folder for readme.
    esriConfig.defaults.io.proxyUrl = 'proxy/proxy.ashx';
	//esriConfig.defaults.io.proxyUrl = 'http://arcgisupg.lirr.org/DotNet/proxy.ashx'
    esriConfig.defaults.io.alwaysUseProxy = false;
	// url to your geometry server.
    esriConfig.defaults.geometryService = new GeometryService('http://arcgisprod10.lirr.org/arcgis/rest/services/Utilities/Geometry/GeometryServer');

    //image parameters for dynamic services, set to png32 for higher quality exports.
    var imageParameters = new ImageParameters();
    imageParameters.format = 'png32';

    return {
        // used for debugging your app
        isDebug: true,

        //default mapClick mode, mapClickMode lets widgets know what mode the map is in to avoid multipult map click actions from taking place (ie identify while drawing).
        defaultMapClickMode: 'identify',
        // map options, passed to map constructor. see: https://developers.arcgis.com/javascript/jsapi/map-amd.html#map1
		
        mapOptions: {
			navigationMode: 'classic',
			basemap: 'streets',
			center: [-73.7918315, 40.6985694],
            zoom: 11,
            sliderStyle: 'small',
			minZoom: 3,
			showLabels: false, 
			showAttribution: false
	
						
        },
		

         panes: {
         	left: {
         		splitter: true
        	},
         //	right: {
         //		id: 'sidebarRight',
         //		placeAt: 'outer',
         //		region: 'right',
        // 		splitter: true,
        // 		collapsible: true
        // 	},
        // 	bottom: {
        //		id: 'sidebarBottom',
        // 		placeAt: 'outer',
        //		splitter: true,
        // 		collapsible: true,
        // 		region: 'bottom'
        // 	},
		//	top: {
        // 		id: 'sidebarTop',
        // 		placeAt: 'outer',
        // 		collapsible: true,
        // 		splitter: true,
        // 		region: 'top'
        // 	}
         },
        // collapseButtonsPane: 'center', //center or outer

        // operationalLayers: Array of Layers to load on top of the basemap: valid 'type' options: 'dynamic', 'tiled', 'feature'.
        // The 'options' object is passed as the layers options for constructor. Title will be used in the legend only. id's must be unique and have no spaces.
        // 3 'mode' options: MODE_SNAPSHOT = 0, MODE_ONDEMAND = 1, MODE_SELECTION = 2
        operationalLayers: [
	{
            type: 'dynamic',
            url: 'http://arcgisprod10.lirr.org/arcgis/rest/services/WEMS/MapServer', 
            title: 'WEMS',
            options: {
                id: 'WEMS',
                opacity: 1.0,
                visible: true,
                outFields: ['*'], 
                mode: 1
            }
  }, 
	{
      type: 'dynamic',
      url: 'http://arcgisprod10.lirr.org/arcgis/rest/services/LIRR_DRAINAGE/MapServer',
      title: 'Drainage',
      options: {
          id: 'lirremergency',
          opacity: 1.0,
          visible: false,
          imageParameters: imageParameters
      },
      legendLayerInfos: {
          exclude: true
      },
      layerControlLayerInfos: {
          swipe: true,
          metadataUrl: true,
          expanded: false
      }
},{
      type: 'dynamic',
      url: 'http://arcgisprod10.lirr.org/arcgis/rest/services/LIRR_CADASTRAL/MapServer',
      title: 'LIRR SA Property',
      options: {
          id: 'lirrtrack',
          opacity: 1.0,
          visible: false,
          imageParameters: imageParameters
      },
      legendLayerInfos: {
          exclude: true
      },
      layerControlLayerInfos: {
          swipe: true,
          metadataUrl: true,
          expanded: false 
      }
},{
      type: 'dynamic',
      url: 'http://arcgisprod10.lirr.org/arcgis/rest/services/LIRR_INFRASTRUCTURE/MapServer',
      title: 'LIRR Infrastructure',
      options: {
          id: 'lirrinfrastructure',
          opacity: 1.0,
          visible: true,
          imageParameters: imageParameters
      },
      legendLayerInfos: {
          exclude: true
      },
      layerControlLayerInfos: {
          swipe: true,
          metadataUrl: true,
          expanded: false
      }
},{
      type: 'dynamic',
      url: 'http://www.orthos.dhses.ny.gov/arcgis/rest/services/elevation_contours/MapServer',
      /*url: 'http://arcgisprod10.lirr.org/arcgis/rest/services/elevation_contours/MapServer',*/
      title: 'USGS 10 Meter Contours',
      options: {
          id: 'nfhl',
          opacity: 1.0,
          visible: true,
          outFields: ['*'], 
          mode: 1
      }
}
  ],
        // set include:true to load. For titlePane type set position the the desired order in the sidebar
        widgets: {
            growler: {
                include: true,
                id: 'growler',
                type: 'domNode',
                path: 'gis/dijit/Growler',
                srcNodeRef: 'growlerDijit',
                options: {}
            },
            geocoder: {
                include: false,
                id: 'geocoder',
                type: 'domNode',
                path: 'gis/dijit/Geocoder',
                srcNodeRef: 'geocodeDijit',
                options: {
                    map: true,
                    mapRightClickMenu: true,
                    geocoderOptions: {
                        autoComplete: true,
                        arcgisGeocoder: {
                            placeholder: 'Enter an address or place'
                        }
                    }
                }
            },
			
			
			timer: {
               include: true,
				id: 'timer',
				type: 'domNode',
				path: 'gis/dijit/Timer',
				title: 'Timer',
				srcNodeRef: 'timerDijit',
			options: {
				map: true,
				mapRightClickMenu: false,
				mapClickMode: false,
				interval: 30000,
				layerIDsForRefresh: ['WEMS']
				}
			},
			
            identify: {
                include: true,
                id: 'identify',
                type: 'titlePane',
                path: 'gis/dijit/Identify',
                title: 'Identify',
                open: true,
                position: 3,
                options: 'config/identify'
            },

           // wems: {
           //     include: true,
           //     id: 'wems',
           //     type: 'titlePane',
           //     path: 'gis/dijit/wems',
           //     title: 'WEMS',
           //     open: true,
            //    position: 3,
            //    options: 'config/wems'
            //},

            basemaps: {
                include: true,
                id: 'basemaps',
                type: 'domNode',
                path: 'gis/dijit/Basemaps',
                srcNodeRef: 'basemapsDijit',
                options: 'config/basemaps'
            },
            mapInfo: {
                include: false,
                id: 'mapInfo',
                type: 'domNode',
                path: 'gis/dijit/MapInfo',
                srcNodeRef: 'mapInfoDijit',
                options: {
                    map: false,
                    mode: 'dms',
                    firstCoord: 'y',
                    unitScale: 3,
                    showScale: false,
                    xLabel: '',
                    yLabel: '',
                    minWidth: 286
                }
            },
            scalebar: {
                include: false,
                id: 'scalebar',
                type: 'map',
                path: 'esri/dijit/Scalebar',
                options: {
                    map: true,
                    attachTo: 'bottom-left',
                    scalebarStyle: 'line',
                    scalebarUnit: 'dual'
                }
            },
            locateButton: {
                include: false,
                id: 'locateButton',
                type: 'domNode',
                path: 'gis/dijit/LocateButton',
                srcNodeRef: 'locateButton',
                options: {
                    map: true,
                    publishGPSPosition: true,
                    highlightLocation: true,
                    useTracking: true,
                    geolocationOptions: {
                        maximumAge: 0,
                        timeout: 15000,
                        enableHighAccuracy: true
                    }
                }
            },
            overviewMap: {
                include: false,
                id: 'overviewMap',
                type: 'map',
                path: 'esri/dijit/OverviewMap',
                options: {
                    map: true,
                    attachTo: 'bottom-right',
                    color: '#0000CC',
                    height: 200,
                    width: 225,
                    opacity: 0.30,
                    visible: false
                }
            },
            homeButton: {
                include: true,
                id: 'homeButton',
                type: 'domNode',
                path: 'esri/dijit/HomeButton',
                srcNodeRef: 'homeButton',
                options: {
                    map: true,
                    extent: new Extent({
                        xmin: -76,
                        ymin: 40.48,
                        xmax: -71.39,
                        ymax: 40.19,
                        spatialReference: {
                            wkid: 4326
                        }
                    })
                }
            },
            legend: {
                include: true,
                id: 'legend',
                type: 'titlePane',
                path: 'esri/dijit/Legend',
                title: 'Legend',
                open: false,
                position: 0,
                options: {
                    map: true,
                    legendLayerInfos: true
                }
            },
            layerControl: {
                include: true,
                id: 'layerControl',
                type: 'titlePane',
                path: 'gis/dijit/LayerControl',
                title: 'Layers',
                open: true,
                position: 0,
                options: {
                    map: true,
                    layerControlLayerInfos: true,
                    separated: true,
                    vectorReorder: true,
                    overlayReorder: false
                }
            },
            bookmarks: {
                include: false,
                id: 'bookmarks',
                type: 'titlePane',
                path: 'gis/dijit/Bookmarks',
                title: 'Bookmarks',
                open: false,
                position: 2,
                options: 'config/bookmarks'
            },
            find: {
                include: false,
                id: 'find',
                type: 'titlePane',
                canFloat: true,
                path: 'gis/dijit/Find',
                title: 'Find',
                open: false,
                position: 3,
                options: 'config/find'
            },
            draw: {
                include: false,
                id: 'draw',
                type: 'titlePane',
                canFloat: true,
                path: 'gis/dijit/Draw',
                title: 'Draw',
                open: false,
                position: 4,
                options: {
                    map: true,
                    mapClickMode: true
                }
            },
            measure: {
                include: false,
                id: 'measurement',
                type: 'titlePane',
                canFloat: true,
                path: 'gis/dijit/Measurement',
                title: 'Measurement',
                open: false,
                position: 5,
                options: {
                    map: true,
                    mapClickMode: true,
                    defaultAreaUnit: units.SQUARE_MILES,
                    defaultLengthUnit: units.MILES
                }
            },
            print: {
                include: false,
                id: 'print',
                type: 'titlePane',
                canFloat: true,
                path: 'gis/dijit/Print',
                title: 'Print',
                open: false,
                position: 6,
                options: {
                    map: true,
                    printTaskURL:  'http://arcgisupg.lirr.org/arcgis/rest/services/Utilities/PrintingTools/GPServer/Export%20Web%20Map%20Task',            //'http://sampleserver6.arcgisonline.com/arcgis/rest/services/Utilities/PrintingTools/GPServer/Export%20Web%20Map%20Task',
                    copyrightText: 'Copyright 2015',
                    authorText: 'Me',
                    defaultTitle: 'Viewer Map',
                    defaultFormat: 'PDF',
                    defaultLayout: 'Letter ANSI A Landscape'
                }
            },
			
			/*print: {
				include: false,
				id: 'print',
				type: 'titlePane',
				path: 'gis/dijit/PrintPlus',
				canFloat: true,
				title: 'Print Plus',
				open: false,
				position: 0,
				options: 'config/printplusWidget'
			},

            directions: {
                include: false,
                id: 'directions',
                type: 'titlePane',
                path: 'gis/dijit/Directions',
                title: 'Directions',
                open: false,
                position: 7,
                options: {
                    map: true,
                    mapRightClickMenu: true,
                    options: {
                        routeTaskUrl: 'http://sampleserver3.arcgisonline.com/ArcGIS/rest/services/Network/USA/NAServer/Route',
                        routeParams: {
                            directionsLanguage: 'en-US',
                            directionsLengthUnits: units.MILES
                        },
                        active: false //for 3.12, starts active by default, which we dont want as it interfears with mapClickMode
                    }
                }
            },
			*/
            editor: {
                include: false,
                id: 'editor',
                type: 'titlePane',
                path: 'gis/dijit/Editor',
                title: 'Editor',
                open: false,
                position: 8,
                options: {
                    map: true,
                    mapClickMode: true,
                    editorLayerInfos: true,
                    settings: {
                        toolbarVisible: true,
                        showAttributesOnClick: true,
                        enableUndoRedo: true,
                        createOptions: {
                            polygonDrawTools: ['freehandpolygon', 'autocomplete']
                        },
                        toolbarOptions: {
                            reshapeVisible: true,
                            cutVisible: true,
                            mergeVisible: true
                        }
                    }
                }
            },
            streetview: {
                include: false,
                id: 'streetview',
                type: 'titlePane',
                canFloat: true,
                position: 9,
                path: 'gis/dijit/StreetView',
                title: 'Google Street View',
                options: {
                    map: true,
                    mapClickMode: true,
                    mapRightClickMenu: true
                }
            },
			disclaimer: {
			include: false,
			id: 'disclaimer',
			type: 'floating',
			path: 'gis/dijit/Disclaimer',
			title: 'Disclaimer',
			options: {

				// you can customize the button text
				i18n: {
					accept: 'Accept!',
					decline: 'Decline'
				},

				// pre-define the height so the dialog is centered properly
				style: 'height:295px;width:375px;',

				// you can put your content right in the config
				content: '<div align="center" style="background-color:black;color:white;font-size:10px;padding:25px;">The EGIS maps was developed by the LIRR and is provided solely for informational purposes. The LIRR makes no representation as to the accuracy of the information or to its suitability for any purpose. The LIRR disclaims any liability for errors that may be contained herein and shall not be responsible for any damages consequential or actual, arising out of or in connection with the use of this information. LIRR makes no warranties, express or implied, including, but not limited to, implied warranties of merchantability and fitness for a particular purpose as to the quality, content, accuracy, or completeness of the information, text graphics, links and other items contained in the application.</div>'

				// or you can provide the url for another page that includes the content
				//href: './disclaimer.html',

				// the url to go to if the user declines.
				//declineHref: 'http://esri.com/'

			}
		},
			
			//add wms layer
			wmslayer: {
				include: false,
				id: 'wmslayer',
				type: 'titlePane',
				canFloat: true,
				position: 17,
				path: 'gis/dijit/WMSLayer',
				placeAt: 'left',
				title: 'Add WMS Layer',
				options: {
					map: true
			  }
			},
			wmslayer2: {
				include: false,
				id: 'wmslayer',
				type: 'titlePane',
				canFloat: true,
				position: 18,
				path: 'gis/dijit/WMSLayer2',
				placeAt: 'left',
				title: 'Add WMS Layer',
				options: {
					map: true
				}
			},
            help: {
                include: false,
                id: 'help',
                type: 'floating',
                path: 'gis/dijit/Help',
                title: 'Help',
                options: {
					openOnStartup: false
				}
            }

        }
    };
});