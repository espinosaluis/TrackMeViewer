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

Trip.prototype.appendMarker = function(point, icon, data)
{
    var marker = new google.maps.Marker({position: point,
                                         icon: icon});
    marker.addListener("click", function() {
        info.setContent(data);
        info.open(map, marker);
    });
    marker.setMap(map);
    bounds.extend(marker.getPosition());
    this.markers.push(marker);
    this.polyline.getPath().push(point);
    return marker;
}

function getIcon(data)
{
    if (showBearings && 'bearing' in data)
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
