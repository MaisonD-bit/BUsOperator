let routeMap;
let startMarker, endMarker;
let routeLayer;
let stopMarkers = [];
let stops = [];
let isAddingStop = false;

// Fare rates
const FARE_RATES = {
    BASE_FARE: 15,
    LOW_RATE: 2.50,
    MID_RATE: 3.00,
    HIGH_RATE: 3.50,
    AIRCON_MARKUP: 0.30
};

const CEBU_COORDINATES = {
    center: [123.8854, 10.3157],
    zoom: 12
};

const CEBU_NORTH_TERMINAL = {
    coordinates: [123.920994, 10.311008],
    name: "Cebu North Bus Terminal (SM City)"
};

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `position-fixed top-0 end-0 m-3 alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
    toast.style.zIndex = '9999';
    toast.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        if (toast.parentNode) {
            toast.parentNode.removeChild(toast);
        }
    }, 3000);
}

function clearValidationErrors() {
    const inputs = document.querySelectorAll('.form-control, .form-select');
    inputs.forEach(input => {
        input.classList.remove('is-invalid');
    });
    const errorDivs = document.querySelectorAll('.invalid-feedback');
    errorDivs.forEach(div => {
        div.textContent = '';
    });
}

function showValidationErrors(errors) {
    clearValidationErrors();
    for (const [field, messages] of Object.entries(errors)) {
        const input = document.getElementById(field === 'code' ? 'route_code' : field === 'name' ? 'route_name' : field === 'status' ? 'route_status' : field);
        const errorDiv = document.getElementById(`${field}_error`);
        if (input && errorDiv) {
            input.classList.add('is-invalid');
            errorDiv.textContent = messages[0];
        }
    }
}

function initializeMap() {
    if (routeMap) {
        routeMap.remove();
    }
    routeMap = new mapboxgl.Map({
        container: 'routeMap',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: CEBU_COORDINATES.center,
        zoom: CEBU_COORDINATES.zoom
    });
    routeMap.addControl(new mapboxgl.NavigationControl());

    startMarker = new mapboxgl.Marker({ color: 'green' })
        .setLngLat(CEBU_NORTH_TERMINAL.coordinates)
        .addTo(routeMap);

    // Listen for map clicks
    routeMap.on('click', function(e) {
        if (isAddingStop) {
            addStop(e.lngLat);
        } else if (!endMarker) {
            setEndPoint(e.lngLat);
        }
    });

    routeMap.on('load', function() {
        console.log('Cebu map loaded with North Terminal as start point');
    });
}

function setEndPoint(coords) {
    if (endMarker) {
        endMarker.remove();
    }
    endMarker = new mapboxgl.Marker({ color: 'red' })
        .setLngLat(coords)
        .addTo(routeMap);

    getPlaceName(coords.lng, coords.lat, function(placeName) {
        document.getElementById('end_location').value = placeName;
        document.getElementById('end_coordinates').value = `${coords.lng},${coords.lat}`;
        autoGenerateRouteCode(placeName);
        showToast('Destination set! You can now add stops or save route.', 'success');
        calculateRouteWithStops();
    });
}

function autoGenerateRouteCode(placeName) {
    if (!placeName) return;
    let code = 'RT-' + placeName.split(',')[0].replace(/[^A-Za-z0-9]/g, '').substring(0, 6).toUpperCase();
    document.getElementById('route_code').value = code;
}

function addStop(coords) {
    // Prevent adding stop if near the current route line
    if (window.lastRouteGeometry && isPointNearLine(coords, window.lastRouteGeometry.coordinates)) {
        showToast('Stop is too close to the existing route. Please select a different location.', 'warning');
        return;
    }
    getPlaceName(coords.lng, coords.lat, function(placeName) {
        const stop = {
            lng: coords.lng,
            lat: coords.lat,
            name: placeName
        };
        stops.push(stop);
        const marker = new mapboxgl.Marker({ color: 'blue' })
            .setLngLat([coords.lng, coords.lat])
            .setPopup(new mapboxgl.Popup().setText(`Stop: ${placeName}`))
            .addTo(routeMap);
        stopMarkers.push(marker);
        updateStopsList();
        calculateRouteWithStops();
        showToast(`Stop added: ${placeName}`, 'success');
    });
}

function updateStopsList() {
    const stopsList = document.getElementById('stopsList');
    stopsList.innerHTML = '';
    stops.forEach((stop, idx) => {
        stopsList.innerHTML += `
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>
                    <span class="badge bg-primary me-2">${idx + 1}</span>
                    <span>${stop.name}</span>
                </div>
                <button type="button" class="btn btn-sm btn-danger ms-2" style="width:7px;min-width:7px;padding:0;" onclick="removeStop(${idx})" title="Remove">
                    <i class="fas fa-trash" style="font-size:0.8rem;"></i>
                </button>
            </div>
        `;
    });
    document.getElementById('stops_data').value = JSON.stringify(stops);
}

window.removeStop = function(idx) {
    if (stopMarkers[idx]) {
        stopMarkers[idx].remove();
    }
    stops.splice(idx, 1);
    stopMarkers.splice(idx, 1);
    updateStopsList();
    calculateRouteWithStops();
};

document.addEventListener('DOMContentLoaded', function() {
    const addStopBtn = document.getElementById('addStopBtn');
    if (addStopBtn) {
        addStopBtn.addEventListener('click', function() {
            if (!endMarker) {
                showToast('Please select a destination first.', 'error');
                return;
            }
            isAddingStop = !isAddingStop;
            if (isAddingStop) {
                addStopBtn.classList.add('btn-success');
                addStopBtn.classList.remove('btn-outline-success');
                addStopBtn.innerHTML = '<i class="fas fa-map-pin me-1"></i>Click on map to add stop';
                showToast('Click on the map to add a stop. Click again for more stops. Click "Add Stop" again to stop adding.', 'info');
            } else {
                addStopBtn.classList.remove('btn-success');
                addStopBtn.classList.add('btn-outline-success');
                addStopBtn.innerHTML = '<i class="fas fa-map-pin me-1"></i>Add Stop';
                showToast('Stopped adding stops.', 'info');
            }
        });
    }
});

window.clearStops = function() {
    stopMarkers.forEach(marker => marker.remove());
    stopMarkers = [];
    stops = [];
    updateStopsList();
    calculateRouteWithStops();
};

function calculateRouteWithStops() {
    if (!endMarker) return;
    let coordinates = [CEBU_NORTH_TERMINAL.coordinates];
    stops.forEach(stop => {
        coordinates.push([stop.lng, stop.lat]);
    });
    coordinates.push([endMarker.getLngLat().lng, endMarker.getLngLat().lat]);

    let coordsStr = coordinates.map(coord => coord.join(',')).join(';');
    fetch(`https://api.mapbox.com/directions/v5/mapbox/driving/${coordsStr}?geometries=geojson&steps=true&overview=full&access_token=${mapboxgl.accessToken}`)
        .then(response => response.json())
        .then(data => {
            if (data.routes && data.routes.length > 0) {
                const route = data.routes[0];
                const distanceKm = (route.distance / 1000);
                const durationMins = Math.round(route.duration / 60);

                document.getElementById('distance_km').value = distanceKm.toFixed(1);
                document.getElementById('estimated_duration').value = durationMins;

                calculateFare();
                drawRoute(route.geometry);

                document.getElementById('geometry_data').value = JSON.stringify(route.geometry);

                const bounds = route.geometry.coordinates.reduce(function (bounds, coord) {
                    return bounds.extend(coord);
                }, new mapboxgl.LngLatBounds(route.geometry.coordinates[0], route.geometry.coordinates[0]));
                routeMap.fitBounds(bounds, { padding: { top: 50, bottom: 50, left: 50, right: 50 } });

                showToast(`Route calculated! ${distanceKm.toFixed(1)}km, ${durationMins} minutes`, 'success');
            } else {
                showToast('Unable to calculate route with stops.', 'error');
            }
        })
        .catch(error => {
            console.error('Error calculating route:', error);
            showToast('Error calculating route. Please try again.', 'error');
        });
}

function drawRoute(geometry) {
    window.lastRouteGeometry = geometry; // Save for proximity check
    if (routeMap.getLayer('route')) {
        routeMap.removeLayer('route');
    }
    if (routeMap.getSource('route')) {
        routeMap.removeSource('route');
    }
    routeMap.addSource('route', {
        type: 'geojson',
        data: {
            type: 'Feature',
            properties: {},
            geometry: geometry
        }
    });
    routeMap.addLayer({
        id: 'route',
        type: 'line',
        source: 'route',
        layout: {
            'line-join': 'round',
            'line-cap': 'round'
        },
        paint: {
            'line-color': '#3b82f6',
            'line-width': 5,
            'line-opacity': 0.8
        }
    });
}

function clearEndPoint() {
    if (endMarker) {
        endMarker.remove();
        endMarker = null;
    }
    clearStops();

    const addStopBtn = document.getElementById('addStopBtn');
    if (addStopBtn) {
        isAddingStop = false;
        addStopBtn.classList.remove('btn-success');
        addStopBtn.classList.add('btn-outline-success');
        addStopBtn.innerHTML = '<i class="fas fa-map-pin me-1"></i>Add Stop';
    }

    if (routeMap && routeMap.getLayer('route')) {
        routeMap.removeLayer('route');
        routeMap.removeSource('route');
    }
    document.getElementById('end_location').value = '';
    document.getElementById('end_coordinates').value = '';
    document.getElementById('distance_km').value = '';
    document.getElementById('estimated_duration').value = '';
    document.getElementById('suggested_regular').value = '';
    document.getElementById('suggested_aircon').value = '';
    document.getElementById('regular_price').value = '';
    document.getElementById('aircon_price').value = '';
    document.getElementById('geometry_data').value = '';
    showToast('Destination cleared. Click on map to set new destination.', 'info');
}

function centerMapToCebu() {
    if (routeMap) {
        routeMap.flyTo({
            center: CEBU_COORDINATES.center,
            zoom: CEBU_COORDINATES.zoom,
            duration: 2000
        });
        showToast('Map centered to Cebu City', 'info');
    }
}

// Form visibility functions
function showAddRouteForm() {
    document.getElementById('routeForm').reset();
    document.getElementById('route_id').value = '';
    document.getElementById('method_field').value = '';
    document.getElementById('start_location').value = CEBU_NORTH_TERMINAL.name;
    document.getElementById('start_coordinates').value = `${CEBU_NORTH_TERMINAL.coordinates[0]},${CEBU_NORTH_TERMINAL.coordinates[1]}`;
    document.getElementById('formTitle').innerHTML = '<i class="fas fa-route me-2"></i>Add New Route';
    document.getElementById('saveRouteBtn').innerHTML = '<i class="fas fa-save me-2"></i>Save Route';
    clearValidationErrors();
    document.getElementById('routeFormSection').style.display = 'block';
    document.getElementById('routeFormSection').scrollIntoView({ behavior: 'smooth' });
    setTimeout(() => {
        initializeMap();
        showToast('Click on the map to select destination from North Terminal', 'info');
    }, 100);
}

function isPointNearLine(point, lineCoords, thresholdMeters = 50) {
    // Haversine formula for distance between two points
    function haversine(lon1, lat1, lon2, lat2) {
        const R = 6371000; // meters
        const toRad = x => x * Math.PI / 180;
        const dLat = toRad(lat2 - lat1);
        const dLon = toRad(lon2 - lon1);
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }
    // Check each segment of the line
    for (let i = 0; i < lineCoords.length - 1; i++) {
        const [lon1, lat1] = lineCoords[i];
        const [lon2, lat2] = lineCoords[i+1];
        // Project point onto segment, get closest point
        const A = {x: lon1, y: lat1};
        const B = {x: lon2, y: lat2};
        const P = {x: point.lng, y: point.lat};
        const AB = {x: B.x - A.x, y: B.y - A.y};
        const AP = {x: P.x - A.x, y: P.y - A.y};
        const ab2 = AB.x*AB.x + AB.y*AB.y;
        const ap_ab = AP.x*AB.x + AP.y*AB.y;
        let t = ab2 === 0 ? 0 : ap_ab / ab2;
        t = Math.max(0, Math.min(1, t));
        const closest = {x: A.x + AB.x*t, y: A.y + AB.y*t};
        const dist = haversine(P.x, P.y, closest.x, closest.y);
        if (dist < thresholdMeters) return true;
    }
    return false;
}

function hideRouteForm() {
    document.getElementById('routeFormSection').style.display = 'none';
    document.getElementById('routeForm').reset();
    if (routeMap) {
        routeMap.remove();
        routeMap = null;
    }
    startMarker = endMarker = null;
    clearStops();
}

// Form submission handling
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('routeForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!document.getElementById('end_location').value) {
                showToast('Please select a destination on the map.', 'error');
                return;
            }
            const formData = new FormData(form);
            formData.set('stops_data', JSON.stringify(stops));
            formData.set('geometry_data', document.getElementById('geometry_data').value);
            const saveBtn = document.getElementById('saveRouteBtn');
            const originalText = saveBtn.innerHTML;
            const routeId = document.getElementById('route_id').value;
            const isEdit = routeId !== '';
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            const url = isEdit ? `/routes/${routeId}` : '/routes';
            if (isEdit) {
                formData.append('_method', 'PUT');
            }
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showToast(data.message || 'Route saved successfully', 'success');
                    hideRouteForm();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (error.errors) {
                    showValidationErrors(error.errors);
                } else {
                    let errorMessage = 'Error saving route';
                    if (error.errors) {
                        const errors = Object.values(error.errors).flat();
                        errorMessage = errors.join('\n');
                    } else if (error.message) {
                        errorMessage = error.message;
                    }
                    showToast(errorMessage, 'error');
                }
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = originalText;
            });
        });
    }
    // Close modal when clicking outside
    const viewModal = document.getElementById('viewRouteModal');
    if (viewModal) {
        viewModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideViewModal();
            }
        });
    }
});

// Make functions globally available
window.showAddRouteForm = showAddRouteForm;
window.hideRouteForm = hideRouteForm;
window.clearEndPoint = clearEndPoint;
window.centerMapToCebu = centerMapToCebu;
window.calculateFare = calculateFare;
window.clearStops = clearStops;

// CRUD functions for AJAX
function editRoute(id) {
    fetch(`/routes/${id}`)
        .then(res => res.json())
        .then(route => {
            showAddRouteForm();
            document.getElementById('route_id').value = route.id;
            document.getElementById('method_field').value = 'PUT';
            document.getElementById('route_code').value = route.code;
            document.getElementById('route_name').value = route.name;
            document.getElementById('start_location').value = route.start_location;
            document.getElementById('end_location').value = route.end_location;
            document.getElementById('start_coordinates').value = route.start_coordinates;
            document.getElementById('end_coordinates').value = route.end_coordinates;
            document.getElementById('distance_km').value = route.distance_km;
            document.getElementById('estimated_duration').value = route.estimated_duration;
            document.getElementById('regular_price').value = route.regular_price;
            document.getElementById('aircon_price').value = route.aircon_price;
            document.getElementById('route_status').value = route.status;
            document.getElementById('description').value = route.description || '';
            document.getElementById('geometry_data').value = JSON.stringify(route.geometry_data || {});
            document.getElementById('stops_data').value = JSON.stringify(route.stops_data || []);
            // Optionally, re-render stops and route on map
        });
}

function deleteRoute(id) {
    if (!confirm('Are you sure you want to delete this route?')) return;
    fetch(`/routes/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert(data.message || 'Failed to delete route.');
        }
    });
}

function viewRoute(id) {
    fetch(`/routes/${id}`)
        .then(res => res.json())
        .then(route => {
            let stopsHtml = '';
            if (route.stops_data && route.stops_data.length) {
                stopsHtml = '<ol class="mb-0 ps-3">';
                route.stops_data.forEach((stop, idx) => {
                    stopsHtml += `<li class="mb-1">${stop.name}</li>`;
                });
                stopsHtml += '</ol>';
            } else {
                stopsHtml = '<em>No stops for this route.</em>';
            }
            document.getElementById('viewRouteContent').innerHTML = `
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2"></i>General Info</h6>
                        <div class="mb-2"><strong>Code:</strong> ${route.code}</div>
                        <div class="mb-2"><strong>Name:</strong> ${route.name}</div>
                        <div class="mb-2"><strong>Status:</strong> <span class="badge ${route.status === 'active' ? 'bg-success' : 'bg-secondary'}">${route.status}</span></div>
                        <div class="mb-2"><strong>Description:</strong> ${route.description || '-'}</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="fw-bold mb-3"><i class="fas fa-route me-2"></i>Route Details</h6>
                        <div class="mb-2"><strong>Start:</strong> ${route.start_location}</div>
                        <div class="mb-2"><strong>End:</strong> ${route.end_location}</div>
                        <div class="mb-2"><strong>Distance:</strong> ${route.distance_km} km</div>
                        <div class="mb-2"><strong>Duration:</strong> ${route.estimated_duration} mins</div>
                        <div class="mb-2"><strong>Regular Fare:</strong> ₱${route.regular_price}</div>
                        <div class="mb-2"><strong>Aircon Fare:</strong> ₱${route.aircon_price}</div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="fw-bold mb-2"><i class="fas fa-map-pin me-2"></i>Stops</h6>
                        ${stopsHtml}
                    </div>
                </div>
            `;
            document.getElementById('viewRouteModal').style.display = 'block';
        });
}

function hideViewModal() {
    document.getElementById('viewRouteModal').style.display = 'none';
}

window.editRoute = editRoute;
window.deleteRoute = deleteRoute;
window.viewRoute = viewRoute;
window.hideViewModal = hideViewModal;

// Utility: Get place name from coordinates (Mapbox reverse geocoding)
function getPlaceName(lng, lat, callback) {
    fetch(`https://api.mapbox.com/geocoding/v5/mapbox.places/${lng},${lat}.json?access_token=${mapboxgl.accessToken}`)
        .then(res => res.json())
        .then(data => {
            if (data.features && data.features.length > 0) {
                callback(data.features[0].place_name);
            } else {
                callback(`${lng},${lat}`);
            }
        })
        .catch(() => callback(`${lng},${lat}`));
}

// Utility: Fare calculation
function calculateFare() {
    const distance = parseFloat(document.getElementById('distance_km').value) || 0;
    const fareType = document.getElementById('fare_type') ? document.getElementById('fare_type').value : 'mid';
    let rate = FARE_RATES.MID_RATE;
    if (fareType === 'low') rate = FARE_RATES.LOW_RATE;
    if (fareType === 'high') rate = FARE_RATES.HIGH_RATE;
    const regular = FARE_RATES.BASE_FARE + (distance * rate);
    const aircon = regular + (regular * FARE_RATES.AIRCON_MARKUP);
    document.getElementById('suggested_regular').value = regular.toFixed(2);
    document.getElementById('suggested_aircon').value = aircon.toFixed(2);
}