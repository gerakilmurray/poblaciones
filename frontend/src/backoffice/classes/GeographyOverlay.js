export default GeographyOverlay;

import axiosClient from '@/common/js/axiosClient';

function GeographyOverlay(map, geographyId) {
	this.map = map;
	this.geographyId = geographyId;
  this.tileSize = new google.maps.Size(256, 256);
}

GeographyOverlay.prototype.getTile = function (coord, zoom, ownerDocument) {
	var div = ownerDocument.createElement('div');
	var tileUrl = '/services/geographies/GetGeography';
	var args = {
		x: coord.x,
		y: coord.y,
		z: zoom,
		a: this.geographyId
	};

  //div.innerHTML = coord;
  div.style.width = this.tileSize.width + 'px';
  div.style.height = this.tileSize.height + 'px';
 /* div.style.fontSize = '10';
  div.style.borderStyle = 'solid';
  div.style.borderWidth = '1px';
  div.style.borderColor = '#AAAAAA';
	*/
	div.dataLayer = new google.maps.Data();
	div.dataLayer.setStyle({
		fillOpacity: 0,
		strokeColor: '#984ee6',
		strokeWeight: 1
			});
	var loc = this;
	axiosClient.getPromise(tileUrl, args,
		'obtener la cartografía').then(function (response) {
			div.dataLayer.addGeoJson(response.Data);
		});
  div.dataLayer.setMap(loc.map);
	return div;
};

GeographyOverlay.prototype.releaseTile = function (tile) {
	if (tile.dataLayer) {
			tile.dataLayer.setMap(null);
    }
};
