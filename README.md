# WEMS for MTA
Weather Emergency Management System for MTA Metro North and Long Island Railroad

This system is to assist MTA personnel in real time with rail station issues during inclement weather.

Personnel have the ability to declare a Weather Emergency for one or more stations, update the status of the station 
and assign work crews to service the station in order to return it to regular passenger rail service.

Also included is a ARCGIS map viewer which has all the train stations - with the ability to click on a station in the map view to 
assess the status and also make changes if needed, provided that the particular MTA personnel user has a sufficiently high
privilege tier with the ability to change status or assign cleanup crews.

## TECHNOLOGY USED

- PHP with minor usage of the Zend framework.
- ARCGIS map system - API accessed via CURL on the PHP side and AJAX with client side Javascript.
- Oracle Database
- Javascript with REST API and also SVG for custom drawing and symbols for stations and other elements.
