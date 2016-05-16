var info = new google.maps.InfoWindow();
var iconRed = 'red-dot.png';
var iconLtBlue = 'mm_20_gray.png';
var iconLtYellow = 'mm_20_yellow.png';
var iconLtPurple = 'mm_20_purple.png';
var iconGreen = 'green-dot.png';

var arrowIcons = [];
for (angle = 0; angle < 360; angle += 45)
{
    arrowIcons.push('arrow' + angle + '.png');
}

function Trip(name, user)
{
    this.name = name;
    this.user = user;
    this.markers = [];
    this.polyline = new google.maps.Polyline({strokeColor: "#000000",
                                              strokeWeight: 3,
                                              strokeOpacity: 1,
                                              map: map});
}

Trip.prototype.lastMarker = function()
{
    return this.markers[this.markers.length - 1];
}

Trip.prototype.appendMarker = function(data, icon)
{
    data.index = this.markers.length;
    data.trip = this;
    if (icon === true)
        icon = getIcon(data);
    var point = new google.maps.LatLng(data.latitude, data.longitude);
    var marker = new google.maps.Marker({position: point,
                                         icon: icon});
    data.date = fromISO(data.timestamp);
    if (this.markers.length > 0) {
        data.distance = distance(this.lastMarker().getPosition(),
                                 point);
        var totalTime = (data.date.getTime() - this.markers[0].data.date.getTime()) / 1000;
    } else {
        data.distance = 0;
        var totalTime = 0;
    }
    data.distanceToHere = this.totalDistance() + data.distance;
    data.totalTime = (Math.floor(totalTime / 3600) + ':' +
                      formatFloat(totalTime / 60 % 60) + ':' +
                      formatFloat(totalTime % 60))
    marker.addListener("click", function() {
        info.setContent(createMarkerText(data));
        info.open(map, marker);
    });
    marker.setMap(map);
    bounds.extend(marker.getPosition());
    marker.data = data;
    this.markers.push(marker);
    this.polyline.getPath().push(point);
    return marker;
}

Trip.prototype.totalDistance = function()
{
    if (this.markers.length > 0)
        return this.lastMarker().data.distanceToHere;
    else
        return 0;
}

Trip.prototype.avgSpeed = function()
{
    // Return m/s, the same as data.speed and this.totalSpeed
    if (this.markers.length > 0) {
        var totalTime = (this.lastMarker().data.date.getTime() - this.markers[0].data.date.getTime()) / 1000;
        return this.totalDistance() * 1000 / totalTime;
    } else {
        return 0;
    }
}

function getIcon(data, lastMarker)
{
    if (data.index == 0)
    {
        return iconGreen;
    }
    else if (lastMarker !== undefined && lastMarker)
    {
        return iconRed;
    }
    else if (showBearings && 'bearing' in data)
    {
        var direction = Math.floor((data.bearing + 22.5) / 45) % 8;
        return arrowIcons[direction];
    }
    else if (data['photo'])
    {
        return iconLtYellow;
    }
    else if (data['comment'])
    {
        return iconLtPurple;
    }
    else
    {
        return iconLtBlue;
    }
}

function createMarkerText(data)
{
    if (useMetric) {
        var speedUnit = lang.get('unit-speed-metric');
        var heightUnit = lang.get('unit-height-metric');
        var distanceUnit = lang.get('unit-distance-metric');
    } else {
        var speedUnit = lang.get('unit-speed-imperial');
        var heightUnit = lang.get('unit-height-imperial');
        var distanceUnit = lang.get('unit-distance-imperial');
    }
    var html = ("<table border='0'><tr><td align='center'><b>" + lang.get('balloon-user') + ": </b>" +
                data.trip.user + "</td><td align='right'><b>" + lang.get('balloon-trip') + ": </b>" + data.trip.name +
                "</td></tr><tr><td colspan='2'><hr width='400'><\/td><\/tr><tr>" +
                "<td align='left'><b>" + lang.get('balloon-time') + ": </b>" + data.formattedTS +
                "</td><td align='right'><b>" + lang.get('balloon-total-time') + ": </b>" +
                data.totalTime + "</td></tr>");
    speed = toMiles(data.speed * 3.6);
    avgSpeed = toMiles(data.trip.avgSpeed() * 3.6);
    totalDistance = toMiles(data.distanceToHere);
    altitude = data.altitude;
    if (!useMetric) {
        altitude *= 3.2808399;  // feet = 1 m
    }
    html += ("<tr><td align='left'><b>" + lang.get('balloon-speed') + ": </b>" + speed.toFixed(2) + " " + speedUnit +
             "</td><td align='right'><b>" + lang.get('balloon-avg-speed') + ": </b>" + avgSpeed.toFixed(2) + " " + speedUnit +
             "</td></tr><tr><td align='left'><b>" + lang.get('balloon-altitude') + ": </b>" + altitude.toFixed(2) + " " + heightUnit +
             "</td><td align='right'><b>" + lang.get('balloon-total-distance') + ": </b>" + totalDistance.toFixed(2) + " " + distanceUnit +
             "</td></tr>");
    if (data.comment)
    {
        html += ("<tr><td colspan='2' align='left' width='400'><b>" +
                 lang.get('balloon-comment') + ":</b> " + data.comment + "</td></tr>");
    }
    html += "        <tr><td colspan='2'>" + lang.get('balloon-point') + " " + lang.get('balloon-point-val', data.index + 1, data.trip.markers.length) + "</td></tr>";
    if (data.photo)
    {
        html += ("    <tr><td colspan='2'><a href='" + data.photo +
                 "' target='_blank'><img src='" + data.photo +
                 "' width='200' border='0'></a></td></tr>");
    }
    html += "        <tr><td colspan='2'>&nbsp;<\/td><\/tr><\/table>";
    return html
}

function formatFloat(number)
{
    number = Math.floor(number)
    if (number < 10)
        return '0' + number
    else
        return number
}

function fromISO(isoDate)
{
    var match = /(\d{4})-(\d?\d)-(\d?\d)[ _](\d?\d):(\d?\d):(\d?\d)/.exec(isoDate);
    return new Date(match[1], match[2] - 1, match[3], match[4], match[5], match[6]);
}

var query = function(url, callback)
{
    var req = new XMLHttpRequest();
    req.onreadystatechange = function() {
        if (req.readyState == 4 && req.status == 200)
            callback(req.responseText);
    };
    req.open('GET', url, true);
    req.send(null);
}

/* Math helpers */

function toMiles(distance)
{
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

function distance(point1, point2) {
    lat1 = toRadians(point1.lat());
    lat2 = toRadians(point2.lat());
    delta_lon = toRadians(point1.lng() - point2.lng());
    if (Math.abs(lat1 - lat2) < 0.0000001 && Math.abs(delta_lon) < 0.0000001)
        return 0;
    dist = Math.sin(lat1) * Math.sin(lat2) + Math.cos(lat1) * Math.cos(lat2) * Math.cos(delta_lon);
    // Previous it was 1.1515 statue miles/min (1 min = 1/60 deg)
    // This is the corresponding radius in kilometers
    return Math.acos(dist) * 6370.69349;  // Average Earth radius in km
}
