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
    marker.addListener("click", function() {
        info.setContent(createMarkerText(data));
        info.open(map, marker);
    });
    marker.setMap(map);
    bounds.extend(marker.getPosition());
    this.markers.push(marker);
    this.polyline.getPath().push(point);
    return marker;
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
    speed = toMiles(data.speed);
    avgSpeed = toMiles(data.avgSpeed);
    totalDistance = toMiles(data.totalDistance);
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

function toMiles(distance)
{
    if (!useMetric)
        distance *= 0.621371192;
    return distance
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
