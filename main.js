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
