define({
	map: true,
	mapClickMode: true,
	mapRightClickMenu: true,
	identifyLayerInfos: true,
	identifyTolerance: 5,
	
	// config object definition:
	//	{<layer id>:{
	//		<sub layer number>:{
	//			<pop-up definition, see link below>
	//			}
	//		},
	//	<layer id>:{
	//		<sub layer number>:{
	//			<pop-up definition, see link below>
	//			}
	//		}
	//	}

	// for details on pop-up definition see: https://developers.arcgis.com/javascript/jshelp/intro_popuptemplate.html
	identifies: {
		Interlockings: {
				0: {
					title: 'Interlockings',
					fieldInfos: [{
						//geodatabase field name is 'FACILITYID'
						//geodatabase field alias is not defined therefore defaults to 'FACILITYID'
						//MXD altered the field alias to 'Pole Number', therefore attribute value will not be displayed
						//'fieldName:' must match the MXD's field alias
						include: true,
						fieldName: 'GISADMIN.INF_LIRR_SYS_SIG_INTERLOCK_PT.NAME',
						label: 'NAME',
						visible: true
					}]
				}
			},
				 
	}		
});





