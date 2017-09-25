var g_map;



////////////////////
/*MAP ABSTRACTION*/
//////////////////
function getMapZoomLevel() {
    return g_map.getZoom();
}



/////////////////////
/*MAP FUNCTIONALITY*/
////////////////////

var g_markers = [];
var g_markerOnClickEventHandler = null;
var g_prevZoom = -1;


function markerGetPos(marker) {
    if (marker.position !== undefined) {
        return marker.position;
    }
    else {
        return marker.center;
    }
}

function getLat(point) {
    return (typeof point.lat === "function") ? point.lat() : point.lat;
}

function getLng(point) {
    return (typeof point.lng === "function") ? point.lng() : point.lng;
}

function setInfo(marker, mapMarkerId, jobId) {
    if (mapMarkerId === undefined || mapMarkerId === null) {
        // For now the marker is getting a temporary id. 
        mapMarkerId = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
        });
    }

    marker.set('markerId', mapMarkerId);
    marker.set('jobId', jobId);
    
    setOnClick(marker);
    
    g_markers.push(marker);
    
    return marker;
}

/*Creates the basic google maps marker*/
function createMapMarker(latLngPos, mapMarkerId, jobId) {
    var marker = new google.maps.Marker({
        position: latLngPos,
        map: g_map
        //icon: "/images/TMC_Marker.png"
    });
    
    return setInfo(marker, mapMarkerId, jobId);
}

function removeMapMarker(markerId) {
    for (var i = 0; i < g_markers.length; ++i) {
        if (g_markers[i].get('markerId') == markerId) {
            // Remove the marker from the map.
            g_markers[i].setMap(null);
            // Remove the marker from the cache data.
            g_markers.splice(i, 1);
            break;
        }
    }
}

function changeMapMarkerId(prevId, newId) {
    for (var i = 0; i < g_markers.length; ++i) {
        if (g_markers[i].get('markerId') == prevId) {
            g_markers[i].set('markerId', newId);
            break;
        }
    }
}

function setOnClick(markerObj) {
    markerObj.addListener("click", g_markerOnClickEventHandler);
}

function getZoomFactor() {
    var zoomLevel = getMapZoomLevel();
    
    var constLevel = 17.0;
    var minZoom = 10.0;
    var base = 2.4;
    
    var zoomFactor;
    if (zoomLevel >= constLevel) {
        zoomFactor = 1.0;
    }
    else {
        var diff = constLevel - zoomLevel;
        
        zoomFactor = (Math.pow(base, Math.min(diff, minZoom)));
    }
    
    return zoomFactor;
}

function createMapCircle(latLngPos, mapMarkerId, jobId) {
    var zoomFactor = getZoomFactor();
    var radius = 3.0 * zoomFactor;
    // The max zoom is the default.
    
    var circle = new google.maps.Circle({
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "FF0000",
        fillOpacity: 0.35,
        map: g_map,
        center: latLngPos, 
        radius: radius
    });
    
    return setInfo(circle, mapMarkerId, jobId);
}

function createMapRectangle(latLngPos, mapMarkerId, jobId) {
    // Calculate the bounds for the rectangle.
    var zoomFactor = getZoomFactor();
    var sizeLat = 0.0001 * zoomFactor;
    var sizeLng = 0.0001 * zoomFactor;
    // The max zoom is the default.
    
    var rectangle = new google.maps.Rectangle({
        strokeColor: "#FF0000",
        strokeOpacity: 0.8,
        strokeWeight: 2,
        fillColor: "FF0000",
        fillOpacity: 0.35,
        map: g_map,
        bounds: { 
            north: getLat(latLngPos) + (sizeLng / 2.0),
            south: getLat(latLngPos) - (sizeLng / 2.0),
            east: getLng(latLngPos) + (sizeLat / 2.0),
            west: getLng(latLngPos) - (sizeLat / 2.0)
        }
    });
    rectangle.position = latLngPos;
    
    return setInfo(rectangle, mapMarkerId, jobId);
}

function createMapIcon(latLng, studyType, markerId, jobId) {
    studyType = Number(studyType);
    switch (studyType) {
        case -1:
        case 0:
            // This was trying to render an invalid study type.
            break;
        case STUDY_TYPE_TMC:
            return createMapMarker(latLng, markerId, jobId);
        case STUDY_TYPE_ROADWAY:
            return createMapCircle(latLng, markerId, jobId);
        case STUDY_TYPE_ORIGINDESTINATION:
            return createMapRectangle(latLng, markerId, jobId);
        case STUDY_TYPE_ADT: 
            // For now just create a map marker as the ADT marker.
            return createMapMarker(latLng, markerId, jobId);
        default: 
            // Just put down a marker as the default.
            return createMapMarker(latLng, markerId, jobId);
    }
}


function isPointInBounds(nw, se, point) {
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

function clearMapMarkers(nw, se, forceClear) {
    for (var i = 0; i < g_markers.length; ++i) {
        var isInBounds = isPointInBounds(nw, se, g_markers[i]);
        var isPermanentMarker = g_markers[i].get('markerId').indexOf("-") === -1;
        var needsToResize = ((g_markers[i] instanceof google.maps.Rectangle) || 
            (g_markers[i] instanceof google.maps.Circle)) && g_prevZoom != getMapZoomLevel();
        
        // If it is not in the bounds and a permanent marker than remove it.
        if ((forceClear || needsToResize || !isInBounds) && isPermanentMarker) {
            // Remove the marker from the map.
            g_markers[i].setMap(null);
            g_markers.splice(i, 1);
            i--;
        }
    }
}

function renderMapMarkers(forceClear) {
    var nwLat = g_map.getBounds().getNorthEast().lat();
    var seLng = g_map.getBounds().getNorthEast().lng();
    var seLat = g_map.getBounds().getSouthWest().lat();
    var nwLng = g_map.getBounds().getSouthWest().lng();
    
    clearMapMarkers({lat: nwLat, lng: nwLng}, {lat: seLat, lng: seLng}, forceClear);
    
    // Now get if the job sites for the task match the search filter and our view port.
    var studyTypes;
    if (g_filter.studyType == null) {
        studyTypes = null;
    }
    else {
        studyTypes = [g_filter.studyType];
    }
    
    JobSiteManagerGetJobSites(INFO_LEVEL_BASIC, studyTypes, null, g_filter.jobId, g_filter.taskId, 
                         g_filter.keywords, 
                         nwLat, nwLng, seLat, seLng, g_filter.sinceDate,
                         function (context, textStatus, response, resultStr, jobSiteVal) {
        if (textStatus != "success" || response != "success") {
            displayFatalError("Could not search job sites.", resultStr);
            return;
        }

        // The study type was passed in the context.
        var studyType = context;

        // Render the job sites.
        for (var i = 0; i < jobSiteVal.length; ++i) {
            var jobSiteId = jobSiteVal[i].jobsiteid;
            
            // Is the marker already on the map? 
            var found = false;
            for (var j = 0; j < g_markers.length; ++j) {
                if (g_markers[j].get('markerId') == jobSiteId) {
                    found = true;
                    break;
                }
            }
            
            if (found) {
                // Skip rendering this marker.
                continue;
            }
            
            // Get the task for the job site.
            var nullCount = 0;
            
            JobSiteGetInfo(jobSiteId, INFO_LEVEL_BASIC, function(context, textStatus, response, resultStr, jobDetails, jobSiteDetails) {
                if (textStatus != "success" || response != "success") {
                    if (resultStr.indexOf("not found") > -1) {
                        nullCount++;
                    }
                    else {
                        displayFatalError("Could not fetch job for job site.", resultStr);
                    }
                }
                else {
                    if (jobDetails.studytype === undefined || jobDetails.studytype == -1 || jobDetails.studytype === null) {
                        nullCount++;
                    }
                    else {
                        createMapIcon({
                            lat: Number(jobSiteDetails.latitude),
                            lng: Number(jobSiteDetails.longitude)
                        }, jobDetails.studytype, jobSiteDetails.jobsiteid, jobDetails.jobid);
                        
                        // Have all of the markers been rendered?
                        if ((g_markers.length + nullCount) == jobSiteVal.length) {      
                            // Reload the job listing.
                            if (g_currentView instanceof JobsViewFiller) {
                                // Reload the view.
                                navToJobsView();
                            }
                        }
                    }
                }
            }, null);
        }
        
        if (jobSiteVal.length == 0) {
            if (g_currentView instanceof JobsViewFiller) {
                // Jobs still must be reloaded even if none are being displayed.
                navToJobsView();
            }
        }
    }, null);
}


function viewportChanged() {
    // Everything must be re-rendered as the viewports are now different.
    renderMapMarkers();
    g_prevZoom = getMapZoomLevel();
}