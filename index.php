<?php /*////////////////////////////////////////////////////////////////////////////////////////
//                                                                                            //
//   copyright (c) 2018-2024, marcus fehse                                                    //
//   all rights reserved.                                                                     //
//                                                                                            //
//   a small interactive map coded for rehkitzretzung-brandenburg.com by                      //
//                                                                                            //
//        marcus fehse <marcus@fehse.com>                                                     //
//                                                                                            //
//   based on free map data provided by openStreetMap (openstreetmap.org) and on the          //
//   open-source javascript library leaflet (leafletjs.com) as free software:                 //
//                                                                                            //
//   you can redistribute and/or modify it under the terms of the GNU general public license  //
//   version 3 or later as published by the free software foundation.                         //
//                                                                                            //
//   it is distributed in the hope that it will be useful, but without any warranty; without  //
//   even the implied warranty of merchantability or fitness for a particular purpose.  see   //
//                                                                                            //
//       <https://www.gnu.org/licenses/gpl-3.0.html>                                          //
//                                                                                            //
//   for more details.                                                                        //
//                                                        last edited on 17–JUN-2024 by mrx   //
//                                                                                            //
///////////////////////////////////////////////////////////////////////////////////////////*/ ?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="UTF-8">
		<meta name="author" content="marcus fehse">
		<meta name="generator" content="just cookie-free web code">
		<meta name="robots" content="noarchive">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
		<title>rehkitzrettung brandenburg e.v.&nbsp;&nbsp; &raquo;&nbsp;&nbsp; karte der ansprechpartner</title>
		<link rel="stylesheet" href="font-awesome/6.4.0/css/all.min.css?version=<?php echo filemtime('/font-awesome/6.4.0/css/all.min.css') ?>">
		<link rel="stylesheet" href="leaflet/1.9.4/leaflet.css">
		<script src="leaflet/1.9.4/leaflet.js"></script>
		<style>
			body { padding: 0; margin: 0; }
			html, body { height: 100%; margin: 0; }
			.leaflet-container { height: 400px; width: 600px; max-width: 100%; max-height: 100%; }
			.pilotIcon { color: #FF0066; display: inline-block; font-size: 32px; height: 100%!important; max-width: 100%; overflow: visible; text-align: center; width: auto!important; }
			#map { height: 100%; width: 100vw; }
		</style>
	</head>
	<body>
		<div id='map'></div>
		<script>
			const map = L.map('map').setView( [52.465, 13.371], 8);
			const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
				maxZoom: 19,
				attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
			}).addTo(map);
			const pilotIcon = L.divIcon({
				html: '<i class="fa-solid fa-helicopter-symbol"></i>',
				iconSize: [32, 32],
				className: 'pilotIcon',
			});
			var operationAreasGroup = L.layerGroup().addTo(map);
			var pilotsGroup = L.layerGroup().addTo(map);
			var pilots = [
				// ['name', 'area', 'phone number', 'e-mail address', [location latlng], radius area, [operationCenter latlng],],
				['Philipp', '14532 + Umgebung', '', 'info@rehkitzrettung-brandenburg.com', [52.371716, 13.202617], 40,],
				['Gerd', '16348 + 50 km', '+49 160 96286381', 'gerd.harder@rehkitzrettung-brandenburg.com', [52.762981, 13.516306], 50, [52.691245, 13.452246],],
				['Frank M.', 'Hennigsdorf + 50 km', '+49 152 55186222', 'frank.marcinkowski@rehkitzrettung-brandenburg.com', [52.630765, 13.206474], 50,],
				['Frank N.', 'HVL + Umland', '+49 1575 3708008', 'frank.neumann@rehkitzrettung-brandenburg.com', [52.508305, 13.077033], 80, [52.623082, 12.630686],],
				['Enrico', 'Brandenburg (Nord)', '+49 162 8025614', 'enrico.wolf@rehkitzrettung-brandenburg.com', [52.556864, 13.447943], 50, [53.085128, 13.853745],],
				['Mirko', '15738 + 80 km', '+49 171 1749891', 'mirko.pflock@rehkitzrettung-brandenburg.com', [52.345574, 13.598050], 80, [52.348956, 13.622255],],
				['Marcus', 'Templin + 60 km', '+49 151 61637437', 'marcus.fehse@rehkitzrettung-brandenburg.com', [53.167479, 13.603507], 60, [53.118265, 13.501925],],
				['Ralf', '14165 + 50 km', '+49 176 96969492', 'ralf.kruse@rehkitzrettung-brandenburg.com', [52.418095, 13.251241], 50,],
			];
			function CloseOperationArea(e) {
				operationAreasGroup.clearLayers();
				pilotsGroup.eachLayer(function(layer) { layer.setOpacity(1); });
			};
			function DrawOperatorLocations(array, layerGroup) {
				for (i = 0; i < array.length; i++) {
					if (array[i][0]) { // name defined
						if (array[i][4] && (array[i][4].length == 2)) { // location defined
							if (array[i][5]) { // operation area defined
								var operationCenter = (array[i][6] && (array[i][6].length == 2)) ? array[i][6] : array[i][4]; // different operationCenter
								var operationRadius = array[i][5] * 1000;
							};
							var iconMarker = L.marker(array[i][4], { icon: pilotIcon, });
							iconMarker.operationCenter = operationCenter;
							iconMarker.operationRadius = operationRadius;
							var popup = array[i][1] ? array[i][1] : '(siehe karte)'; // area definded
							popup = array[i][3] ? popup + '<br><a href="mailto:' + array[i][3] + '">' + array[i][0] + '</a>' : popup + '<br>' + array[i][0]; // e-mail address defined
							popup = array[i][2] ? popup + '&nbsp; >>>&nbsp; <a href="tel:' + array[i][2] + '">' + array[i][2] + '</a>' : popup; // phone number defined
							iconMarker.bindPopup(popup);
							layerGroup.addLayer(iconMarker);
						};
					};
				};
			};
			function DrawOperationArea(e) {
				if (!operationAreasGroup.hasLayer(e.popup._source)) {
					operationAreasGroup.clearLayers();
				};
				let popup = e.popup._source._popup._content;
				currentLayerID = e.popup._source._leaflet_id;
				pilotsGroup.eachLayer(function(layer) { (layer._leaflet_id != currentLayerID) ? layer.setOpacity(.4) : layer.setOpacity(1); });
				operationArea = L.circle(e.popup._source.operationCenter, {
					color: '#FF000066',
					fillColor: '#FF6600',
					fillOpacity: 0.1,
					radius: e.popup._source.operationRadius,
					weight: 3,
				}).bindPopup(popup);
				operationAreasGroup.addLayer(operationArea);
				map.flyToBounds(operationArea, { animate: true, duration: 1, });
			};
			function OnLocationFound(e) {
				const radius = e.accuracy * 0.5;
				const locationMarker = L.marker(e.latlng).addTo(map).bindPopup('genauigkeit: ca. ' + radius + ' meter');
				map.flyTo(e.latlng, 12, { animate: true, duration: 2, });
				const locationCircle = L.circle(e.latlng, radius).addTo(map);
			};
			function OnLocationError(e) {
				alert(e.message);
			};
			DrawOperatorLocations(pilots, pilotsGroup);
			map.locate();
			map.on('click', CloseOperationArea);
			map.on('locationerror', OnLocationError);
			map.on('locationfound', OnLocationFound);
			map.on('popupopen', DrawOperationArea);
		</script>
	</body>
</html>