function Trip(name, user)
{
    this.name = name;
    this.user = user;
    this.markers = [];
}

Trip.prototype.lastMarker = function()
{
    return this.markers[this.markers.length - 1];
}

Trip.prototype.appendMarker = function(point, icon, data)
{
    var marker = new GMarker(point, icon);
    GEvent.addListener(marker, "click", function() {marker.openInfoWindowHtml(data);});
    map.addOverlay(marker);
    bounds.extend(marker.getPoint());
    this.markers.push(marker);
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
    else if (data['photo'])
    {
        return iconLtPurple;
    }
    else
    {
        return iconLtBlue;
    }
}
