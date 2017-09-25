////////////////////
/*MAP ABSTRACTION*/
//////////////////
function mapGetZoomLevel(map)
{
    return map.getZoom();
}

function mapGetZoomFactor(map)
{
    var zoomLevel = mapGetZoomLevel(map);
    
    var constLevel = 17.0;
    var minZoom = 10.0;
    var base = 2.4;
    var zoomFactor;

    if (zoomLevel >= constLevel)
    {
        zoomFactor = 1.0;
    }
    else
    {
        var diff = constLevel - zoomLevel;
        
        zoomFactor = (Math.pow(base, Math.min(diff, minZoom)));
    }

    return zoomFactor;
}

/////////////////////
/*MAP FUNCTIONALITY*/
////////////////////

function markerGetPos(marker)
{
    if (marker.position !== undefined)
    {
        return marker.position;
    }
    else
    {
        return marker.center;
    }
}

function getLat(point)
{
    return (typeof point.lat === "function") ? point.lat() : point.lat;
}

function getLng(point)
{
    return (typeof point.lng === "function") ? point.lng() : point.lng;
}

/*Creates the basic google maps marker*/
function mapCreateCircle(map, latLngPos, clickHandler, context)
{
    var zoomFactor = mapGetZoomFactor(map);
    var radius = 3.0 * zoomFactor;
    // The max zoom is the default.
    
    var circle = new google.maps.Circle({
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "FF0000",
        fillOpacity: 0.35,
        map: map,
        center: latLngPos,
        radius: radius
    });

    circle.addListener("click", function(e) { clickHandler(e, context); });

    return circle;
}

function mapCreateMapRectangle(map, latLngPos, clickHandler, context)
{
    // Calculate the bounds for the rectangle.
    var zoomFactor = mapGetZoomFactor(map);
    var sizeLat = 0.0001 * zoomFactor;
    var sizeLng = 0.0001 * zoomFactor;
    // The max zoom is the default.
    
    var rectangle = new google.maps.Rectangle({
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "FF0000",
        fillOpacity: 0.35,
        map: map,
        bounds: {
            north: getLat(latLngPos) + (sizeLng / 2.0),
            south: getLat(latLngPos) - (sizeLng / 2.0),
            east: getLng(latLngPos) + (sizeLat / 2.0),
            west: getLng(latLngPos) - (sizeLat / 2.0)
        }
    });

    rectangle.position = latLngPos;
    rectangle.addListener("click", function(e) { clickHandler(e, context); });

    return rectangle;
}

function isPointInBounds(nw, se, point)
{
    var nwLat = nw.lat;
    var nwLng = nw.lng;
    var seLat = se.lat;
    var seLng = se.lng;
    var pointPos = markerGetPos(point);

    return (nwLat > getLat(pointPos) && 
            nwLng < getLng(pointPos) &&
            seLat < getLat(pointPos) &&
            seLng > getLng(pointPos));
}

function clearMapMarkers(nw, se, forceClear)
{
    for (var i = 0; i < g_markers.length; ++i)
    {
        var isInBounds = isPointInBounds(nw, se, g_markers[i]);
        var isPermanentMarker = g_markers[i].get('markerId').indexOf("-") === -1;
        var needsToResize = ((g_markers[i] instanceof google.maps.Rectangle) || 
            (g_markers[i] instanceof google.maps.Circle)) && g_prevZoom != getMapZoomLevel();
        
        // If it is not in the bounds and a permanent marker than remove it.
        if ((forceClear || needsToResize || !isInBounds) && isPermanentMarker)
        {
            // Remove the marker from the map.
            g_markers[i].setMap(null);
            g_markers.splice(i, 1);
            i--;
        }
    }
}

function rad2degr(rad) { return rad * 180 / Math.PI; }
function degr2rad(degr) { return degr * Math.PI / 180; }

/**
 * @param latLngInDeg array of arrays with latitude and longtitude
 *   pairs in degrees. e.g. [[latitude1, longtitude1], [latitude2
 *   [longtitude2] ...]
 *
 * @return array with the center latitude longtitude pairs in 
 *   degrees.
 */
function getLatLngCenter(latLngInDegr)
{
    var sumX = 0;
    var sumY = 0;
    var sumZ = 0;

    for (var i=0; i<latLngInDegr.length; i++)
    {
        var lat = degr2rad(latLngInDegr[i].lat);
        var lng = degr2rad(latLngInDegr[i].lng);

        // sum of cartesian coordinates
        sumX += Math.cos(lat) * Math.cos(lng);
        sumY += Math.cos(lat) * Math.sin(lng);
        sumZ += Math.sin(lat);
    }

    var avgX = sumX / latLngInDegr.length;
    var avgY = sumY / latLngInDegr.length;
    var avgZ = sumZ / latLngInDegr.length;

    // convert average x, y, z coordinate to latitude and longtitude
    var lng = Math.atan2(avgY, avgX);
    var hyp = Math.sqrt(avgX * avgX + avgY * avgY);
    var lat = Math.atan2(avgZ, hyp);

    return ( { lat:rad2degr(lat), lng:rad2degr(lng) } );
}
