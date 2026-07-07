var iconRed		= 'images/red-dot.png';
var iconGreen		= 'images/green-dot.png';
var iconLtBlue		= 'images/mm_20_gray.png';
var iconLtYellow	= 'images/mm_20_yellow.png';
var iconLtPurple	= 'images/mm_20_purple.png';
var arrowIcons		= [];
for (angle = 0; angle < 360; angle += 45) {
	arrowIcons.push('images/arrow' + angle + '.png');
}

function Trip(name, user) {
	this.name = name;
	this.user = user;
	this.markers = [];
}

Trip.prototype.lastMarker = function() {
	return this.markers[this.markers.length - 1];
}

Trip.prototype.appendMarker = function(data, icon) {
	data.index = this.markers.length;
	data.trip = this;
	if (icon === true)
		icon = getIcon(data);
	var theIcon = L.icon({iconUrl: icon, iconAnchor: [6, 10],});
	var point = L.latLng(data.latitude, data.longitude);
	var marker = L.marker(point, {icon: theIcon}).addTo(map);

	data.date = fromISO(data.timestamp);
	if (this.markers.length > 0) {
		data.distance = calcDistance(this.lastMarker().getLatLng(), point);
		var totalTime = (data.date.getTime() - this.markers[0].data.date.getTime()) / 1000;
	} else {
		data.distance = 0;
		var totalTime = 0;
	}
	data.distanceToHere = this.totalDistance() + data.distance;
	data.totalTime = (leadingZeros(totalTime / 3600) + ':' +
			leadingZeros(totalTime / 60 % 60) + ':' +
			leadingZeros(totalTime % 60))
	marker.bindPopup("", {maxWidth: 440, className: "trackme-popup"});
	marker.on("click", function() {
		marker._popup.setContent(createMarkerText(data));
		marker.openPopup();
	});
	if (bounds == null) {
		bounds = L.latLngBounds(marker.getLatLng().toBounds(200))
	} else {
		bounds.extend(marker.getLatLng());
	}
	marker.data = data;
	this.markers.push(marker);
	markersLatLng.push(marker.getLatLng());
	return marker;
}

Trip.prototype.totalDistance = function() {
	if (this.markers.length > 0)
		return this.lastMarker().data.distanceToHere;
	else
		return 0;
}

Trip.prototype.avgSpeed = function() {
	// Return m/s, the same as data.speed and this.totalSpeed
	if (this.markers.length > 0) {
		var totalTime = (this.lastMarker().data.date.getTime() - this.markers[0].data.date.getTime()) / 1000;
		return this.totalDistance() * 1000 / totalTime;
	} else {
		return 0;
	}
}

function getIcon(data, lastMarker) {
	if (data.index == 0) {
		return iconGreen;
	} else if (lastMarker !== undefined && lastMarker) {
		return iconRed;
	} else if (showBearings && 'bearing' in data) {
		var direction = Math.floor((data.bearing + 22.5) / 45) % 8;
		return arrowIcons[direction];
	} else if (data['photo']) {
		return iconLtYellow;
	} else if (data['comment']) {
		return iconLtPurple;
	} else {
		return iconLtBlue;
	}
}

function createMarkerText(data) {
	if (useMetric) {
		var speedUnit = lang.get('unit-speed-metric');
		var heightUnit = lang.get('unit-height-metric');
		var distanceUnit = lang.get('unit-distance-metric');
	} else {
		var speedUnit = lang.get('unit-speed-imperial');
		var heightUnit = lang.get('unit-height-imperial');
		var distanceUnit = lang.get('unit-distance-imperial');
	}
	var safeComment = data.comment ? data.comment : "";
	var html = "";
	speed = toMiles(data.speed * 3.6);
	avgSpeed = toMiles(data.trip.avgSpeed() * 3.6);
	totalDistance = toMiles(data.distanceToHere);
	altitude = data.altitude;
	if (!useMetric) {
		altitude *= 3.2808399; // feet = 1 m
	}
	html += "<div class='trackme-balloon'>";
	html += " <div class='trackme-balloon-header'>";
	html += "  <div class='trackme-balloon-chip'><span class='trackme-balloon-chip-label'>" + lang.get('balloon-user') + "</span><span class='trackme-balloon-chip-value'>" + data.trip.user + "</span></div>";
	html += "  <div class='trackme-balloon-chip trackme-balloon-chip-trip'><span class='trackme-balloon-chip-label'>" + lang.get('balloon-trip') + "</span><span class='trackme-balloon-chip-value'>" + data.trip.name + "</span></div>";
	html += " </div>";
	html += " <div class='trackme-balloon-metrics'>";
	html += "  <div class='trackme-balloon-metric'><span class='trackme-balloon-label'>" + lang.get('balloon-time') + "</span><span class='trackme-balloon-value'>" + data.formattedTS + "</span></div>";
	html += "  <div class='trackme-balloon-metric'><span class='trackme-balloon-label'>" + lang.get('balloon-total-time') + "</span><span class='trackme-balloon-value'>" + data.totalTime + "</span></div>";
	html += "  <div class='trackme-balloon-metric'><span class='trackme-balloon-label'>" + lang.get('balloon-speed') + "</span><span class='trackme-balloon-value'>" + speed.toFixed(2) + " " + speedUnit + "</span></div>";
	html += "  <div class='trackme-balloon-metric'><span class='trackme-balloon-label'>" + lang.get('balloon-avg-speed') + "</span><span class='trackme-balloon-value'>" + avgSpeed.toFixed(2) + " " + speedUnit + "</span></div>";
	html += "  <div class='trackme-balloon-metric'><span class='trackme-balloon-label'>" + lang.get('balloon-altitude') + "</span><span class='trackme-balloon-value'>" + altitude.toFixed(2) + " " + heightUnit + "</span></div>";
	html += "  <div class='trackme-balloon-metric'><span class='trackme-balloon-label'>" + lang.get('balloon-total-distance') + "</span><span class='trackme-balloon-value'>" + totalDistance.toFixed(2) + " " + distanceUnit + "</span></div>";
	html += " </div>";
	if (data.comment) {
		html += " <div class='trackme-balloon-comment'><span class='trackme-balloon-label'>" + lang.get('balloon-comment') + "</span><div class='trackme-balloon-comment-text'>" + safeComment + "</div></div>";
	}
	html += " <div class='trackme-balloon-footer'>" + lang.get('balloon-point') + " " + lang.get('balloon-point-val', data.index + 1, data.trip.markers.length) + "</div>";
	if (data.photo) {
		html += " <a class='trackme-balloon-photo' href='" + data.photo + "' target='_blank'><img src='" + data.photo + "' alt='" + lang.get('photo-alt') + "'></a>";
	}
	html += "</div>";
	return html
}

function leadingZeros(number)
{
	number = Math.floor(number)
	if (number < 10)
		return '0' + number
	else
		return number
}

function fromISO(isoDate) {
	var match = /(\d{4})-(\d?\d)-(\d?\d)[ _](\d?\d):(\d?\d):(\d?\d)/.exec(isoDate);
	return new Date(match[1], match[2] - 1, match[3], match[4], match[5], match[6]);
}

var query = function(url, callback) {
	var req = new XMLHttpRequest();
	req.onreadystatechange = function() {
		if (req.readyState == 4 && req.status == 200)
			callback(req.responseText);
	};
	req.open('GET', url, true);
	req.send(null);
}

/* Math helpers */
function toMiles(distance) {
	if (!useMetric)
		distance *= 0.621371192;
	return distance
}

function toRadians (angle) {
	return angle * (Math.PI / 180);
}

function toDegrees (angle) {
	return angle * (180 / Math.PI);
}

function calcDistance(point1, point2) {
	lat1 = toRadians(point1.lat);
	lat2 = toRadians(point2.lat);
	delta_lon = toRadians(point1.lng - point2.lng);
	if (Math.abs(lat1 - lat2) < 0.0000001 && Math.abs(delta_lon) < 0.0000001)
		return 0;
	dist = Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(delta_lon);
	// Previous it was 1.1515 statue miles/min (1 min = 1/60 deg)
	// This is the corresponding radius in kilometers
	return Math.acos(dist) * 6370.69349; // Average Earth radius in km
}
