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
