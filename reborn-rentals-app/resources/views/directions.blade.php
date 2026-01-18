@extends('layouts.app')

@section('title', 'Select Jobsite & Time - Reborn Rentals')

@section('content')
<!-- Step 2: Select Jobsite & Time -->
<div class="max-w-6xl mx-auto px-3 sm:px-4 md:px-6 mt-4 sm:mt-8 md:mt-12 lg:mt-20 mb-8 sm:mb-12 md:mb-16 lg:mb-20">
    <div class="bg-white rounded-lg p-3 sm:p-4 md:p-6 lg:p-8">
        <!-- Section Title -->
        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 mb-2 sm:mb-3 md:mb-4">Select Jobsite & Time</h1>
        
        <!-- Introductory Text -->
        <p class="text-gray-600 text-xs sm:text-sm md:text-base mb-4 sm:mb-6 md:mb-8 leading-relaxed">
            Select date and place of delivery, depending on your location, delivery fees may apply, Long fares apply if jobsite is located 4 hours away or more. Orders outside of Mainland US, not accepted.
        </p>
        
        <div>
            <!-- Date Selection Section -->
            <div class="mb-4 sm:mb-6 md:mb-8">
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-3 sm:mb-4">
                    <!-- Start Date -->
                    <div class="flex-1 w-full">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">Start Date</label>
                        <input 
                            type="date" 
                            class="w-full px-2.5 sm:px-3 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent"
                            id="start-date"
                            name="start_date"
                            required
                        />
                    </div>
                    
                    <!-- End Date -->
                    <div class="flex-1 w-full">
                        <label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1.5 sm:mb-2">End Date</label>
                        <div class="relative">
                            <input 
                                type="date" 
                                class="w-full px-2.5 sm:px-3 py-2 sm:py-2.5 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent pr-8 sm:pr-10"
                                id="end-date"
                                name="end_date"
                                required
                            />
                            <div class="absolute right-2 sm:right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Date Selection Instructions -->
                <p class="text-gray-600 text-xs sm:text-sm leading-relaxed">
                    Choose start and end dates for your rental period. Weekend deliveries are available. Tap a start date and then an end date to set your delivery period. You can select weekends too. The selected range will be highlighted.
                </p>
                
                <!-- Stock Availability Loader -->
                <div id="stock-validation-loader" class="hidden mt-3 sm:mt-4 flex items-center gap-2 text-sm text-gray-600">
                    <svg class="animate-spin h-5 w-5 text-[#CE9704]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Checking product availability...</span>
                </div>
                
                <!-- Stock Availability Results -->
                <div id="stock-availability-results" class="hidden mt-3 sm:mt-4"></div>
            </div>
            
            <!-- Jobsite Address Section -->
            <div class="mb-4 sm:mb-6 md:mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start mb-2 sm:mb-3 md:mb-4 gap-2 sm:gap-0">
                    <h2 class="text-base sm:text-lg md:text-xl font-semibold text-gray-800">Insert Jobsite Address</h2>
                    <span class="text-xs sm:text-sm text-gray-500 underline">*Cancellation Fees may apply.</span>
                </div>
                
                <!-- Address Search Input -->
                <div class="relative">
                    <div class="absolute left-2.5 sm:left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="jobsite_address"
                        placeholder="Type an address or select from suggestions..."
                        class="w-full pl-8 sm:pl-9 md:pl-10 pr-20 sm:pr-24 py-2 sm:py-2.5 md:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent"
                        id="jobsite-address"
                        autocomplete="off"
                    />
                    <button 
                        type="button"
                        id="search-address-btn"
                        class="absolute right-2 sm:right-3 top-1/2 transform -translate-y-1/2 bg-[#CE9704] hover:bg-[#B8860B] text-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium transition-colors duration-200 z-10"
                        title="Search address"
                    >
                        Search
                    </button>
                </div>
            </div>
            
            <!-- Pickup Options -->
            <div class="mb-4 sm:mb-6 md:mb-8">
                <div class="space-y-3 sm:space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="pickup-option" class="w-4 h-4 sm:w-5 sm:h-5 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704] focus:ring-2" id="self-pickup" value="self-pickup">
                        <span class="ml-2 sm:ml-3 text-sm sm:text-base text-gray-700">Self-Pickup</span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="pickup-option" class="w-4 h-4 sm:w-5 sm:h-5 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704] focus:ring-2" id="no-address" value="no-address">
                        <span class="ml-2 sm:ml-3 text-sm sm:text-base text-gray-700">Jobsite Lot doesn't have an address.</span>
                    </label>
                </div>
                
                <!-- Interactive Map Section -->
                <div class="mt-6 sm:mt-8 md:mt-10">
                    <div class="mb-4 sm:mb-5 md:mb-6">
                        <h3 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-2 sm:mb-3">Select Delivery Location on Map</h3>
                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">
                            Click on the map to select your delivery location. The map will automatically show the route from our office to your selected location.
                    </p>
                    </div>
                    
                    <!-- Map Container -->
                    <div class="relative" style="z-index: 1;">
                        <div id="delivery-map" class="w-full h-72 sm:h-96 md:h-[500px] lg:h-[600px] xl:h-[700px] rounded-xl overflow-hidden border-2 border-gray-300 shadow-2xl"></div>
                        
                        <!-- Google Maps iframe (hidden by default, shown for self-pickup and no-address) -->
                        <div id="google-maps-static" class="w-full h-72 sm:h-96 md:h-[500px] lg:h-[600px] xl:h-[700px] rounded-xl overflow-hidden border-2 border-gray-300 shadow-2xl hidden">
                            <iframe 
                                src="https://www.google.com/maps?q=39.726372,-105.055759&hl=es&z=14&output=embed&markers=39.726372,-105.055759" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                        
                        <!-- Map Controls -->
                        <div id="map-controls" class="absolute top-3 sm:top-4 md:top-5 right-3 sm:right-4 md:right-5 bg-white/95 backdrop-blur-sm rounded-xl shadow-xl p-2 sm:p-2.5 md:p-3 space-y-2 border border-gray-200">
                            <button 
                                type="button"
                                id="center-map-btn" 
                                class="bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 md:py-3 rounded-lg text-xs sm:text-sm font-semibold hover:from-[#B8860B] hover:to-[#CE9704] transition-all duration-300 block w-full whitespace-nowrap shadow-md hover:shadow-lg transform hover:scale-105"
                                title="Center on Reborn Rentals Office"
                            >
                                📍 Our Office
                            </button>
                            <button 
                                type="button"
                                id="clear-route-btn" 
                                class="bg-gradient-to-r from-gray-600 to-gray-700 text-white px-3 sm:px-4 md:px-5 py-2 sm:py-2.5 md:py-3 rounded-lg text-xs sm:text-sm font-semibold hover:from-gray-700 hover:to-gray-800 transition-all duration-300 block w-full whitespace-nowrap shadow-md hover:shadow-lg transform hover:scale-105"
                                title="Clear Route"
                            >
                                🗑️ Clear Route
                            </button>
                        </div>
                        
                        <!-- Selected Location Info -->
                        <div id="selected-location-info" class="absolute bottom-3 sm:bottom-4 md:bottom-5 left-3 sm:left-4 md:left-5 right-3 sm:right-4 md:right-5 bg-white/95 backdrop-blur-sm rounded-xl shadow-xl p-3 sm:p-4 md:p-5 border border-gray-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-start gap-2 sm:gap-0">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-bold text-gray-900 text-sm sm:text-base mb-1.5">Selected Location:</h4>
                                    <p id="selected-address" class="text-gray-700 text-sm sm:text-base mt-1 break-words font-medium">Click on the map to select a delivery location</p>
                                    <p id="distance-info" class="text-[#CE9704] text-sm sm:text-base mt-2 font-semibold"></p>
                                </div>
                                <button 
                                    type="button"
                                    id="use-location-btn" 
                                    class="bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white px-4 sm:px-5 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-semibold hover:from-[#B8860B] hover:to-[#CE9704] transition-all duration-300 whitespace-nowrap sm:ml-3 mt-2 sm:mt-0 w-full sm:w-auto shadow-md hover:shadow-lg transform hover:scale-105"
                                >
                                    Use This Location
                                </button>
                            </div>
                        </div>
                    </div>
                    

                </div>
                
                <!-- Self-Pickup Dropdown -->
                <div id="self-pickup-details" class="mt-4 sm:mt-6 p-3 sm:p-4 md:p-6 bg-gray-50 rounded-lg border border-gray-200 hidden">
                    <h3 class="text-sm sm:text-base md:text-lg font-semibold text-gray-800 mb-2 sm:mb-3 md:mb-4">Pickup Location Details</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 md:gap-6">
                        <!-- Address -->
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold text-gray-900 mb-2 sm:mb-3">Our Address</h4>
                            <div class="space-y-0.5 sm:space-y-1 text-xs sm:text-sm text-gray-700">
                                <p>39°43'34.9"N 105°03'20.7"W</p>
                                <p>Denver, CO</p>
                                <p>United States</p>
                            </div>
                        </div>
                        
                        <!-- Hours -->
                        <div>
                            <h4 class="text-sm sm:text-base font-semibold text-gray-900 mb-2 sm:mb-3">Hours</h4>
                            <ul class="text-xs sm:text-sm text-gray-700 space-y-0.5 sm:space-y-1">
                                <li><span class="font-medium">Mon–Fri:</span> 08:00 – 18:00</li>
                                <li><span class="font-medium">Sat:</span> 09:00 – 14:00</li>
                                <li><span class="font-medium">Sun:</span> Closed</li>
                            </ul>
                        </div>
                    </div>
                    

                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #delivery-map {
        min-height: 300px !important;
        height: 100%;
    }
    @media (min-width: 640px) {
        #delivery-map {
            min-height: 400px !important;
        }
    }
    @media (min-width: 768px) {
        #delivery-map {
            min-height: 500px !important;
        }
    }
    @media (min-width: 1024px) {
        #delivery-map {
            min-height: 600px !important;
        }
    }
    @media (min-width: 1280px) {
        #delivery-map {
            min-height: 700px !important;
        }
    }
    .leaflet-container {
        height: 100%;
        width: 100%;
    }
    /* Improve visibility of location panel on mobile devices */
    #selected-location-info {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
    /* Improve autocomplete suggestions on mobile devices */
    #address-suggestions {
        max-height: 200px;
        font-size: 0.875rem;
    }
    @media (min-width: 640px) {
        #address-suggestions {
            max-height: 240px;
            font-size: 1rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    console.log('=== DIRECTIONS PAGE LOADED ===');
    const USE_IFRAME_MAP = false;
let map;
let selectedMarker;
let officeMarker;
let directionsService;
let directionsRenderer;
let fallbackPolyline = null;

// Expose map globally for invalidateSize calls
window.map = null;

// Reborn Rentals Office coordinates
const officeLocation = {
    lat: 39.726372,
    lng: -105.055759,
    address: "39°43'34.9\"N 105°03'20.7\"W, Denver, CO, USA"
};

// Initialize Google Map
function initGoogleMap() {
    console.log('Initializing Google Maps...');
    
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        console.error('Google Maps not loaded');
        const mapElement = document.getElementById('delivery-map');
        if (mapElement) {
            mapElement.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-600">Map loading failed. Please refresh the page.</div>';
        }
        return;
    }
    
    try {
        // Create map
        map = new google.maps.Map(document.getElementById('delivery-map'), {
            center: { lat: officeLocation.lat, lng: officeLocation.lng },
            zoom: 12,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true
        });
        
        // Expose map globally
        window.map = map;
        
        // Initialize directions service and renderer
        directionsService = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map: map,
            suppressMarkers: false, // Show default markers for origin and destination
            polylineOptions: {
                strokeColor: '#CE9704', // Gold color matching brand
                strokeWeight: 6,
                strokeOpacity: 0.9
            },
            markerOptions: {
                // Customize markers if needed
            }
        });
        
        // Store current route destination globally
        window.currentRouteDestination = null;
        
        console.log('Google Map created successfully');
        
        // Add office marker
        officeMarker = new google.maps.Marker({
            position: { lat: officeLocation.lat, lng: officeLocation.lng },
            map: map,
            title: 'Reborn Rentals Office',
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg width="40" height="40" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="20" cy="20" r="18" fill="#CE9704" stroke="white" stroke-width="3"/>
                        <text x="20" y="26" font-size="20" text-anchor="middle" fill="white">🏢</text>
                    </svg>
                `),
                scaledSize: new google.maps.Size(40, 40),
                anchor: new google.maps.Point(20, 20)
            }
        });
        
        // Add click listener to map (prevent clicks when self-pickup is active)
        map.addListener('click', function(event) {
            const selfPickup = document.getElementById('self-pickup');
            if (selfPickup && selfPickup.checked) return;
            
            const clickedLocation = {
                lat: event.latLng.lat(),
                lng: event.latLng.lng()
            };
            selectLocation(clickedLocation);
        });

        // Center map button
        const centerBtn = document.getElementById('center-map-btn');
        if (centerBtn) {
            centerBtn.addEventListener('click', function() {
                map.setCenter({ lat: officeLocation.lat, lng: officeLocation.lng });
                map.setZoom(12);
            });
        }

        // Clear route button
        const clearRouteBtn = document.getElementById('clear-route-btn');
        if (clearRouteBtn) {
            clearRouteBtn.addEventListener('click', function() {
                // Clear directions
                if (directionsRenderer) {
                    directionsRenderer.setDirections({ routes: [] });
                }
                
                // Clear stored destination
                window.currentRouteDestination = null;
                
                // Remove selected marker
                if (selectedMarker) {
                    selectedMarker.setMap(null);
                    selectedMarker = null;
                }
                
                // Clear location info text
                const locationInfo = document.getElementById('selected-location-info');
                if (locationInfo) {
                    const selectedAddress = document.getElementById('selected-address');
                    const distanceInfo = document.getElementById('distance-info');
                    if (selectedAddress) selectedAddress.textContent = 'Click on the map to select a delivery location';
                    if (distanceInfo) distanceInfo.textContent = '';
                }
                
                // Clear address input
                const addressInput = document.getElementById('jobsite-address');
                if (addressInput) {
                    addressInput.value = '';
                }
            });
        }

        // Use location button - Show route from office to selected location (like Google Maps directions)
        const useLocationBtn = document.getElementById('use-location-btn');
        if (useLocationBtn) {
            useLocationBtn.addEventListener('click', function() {
                const selectedAddress = document.getElementById('selected-address');
                const addressInput = document.getElementById('jobsite-address');
                
                // If there's a stored destination, show the route
                if (window.currentRouteDestination) {
                    const location = window.currentRouteDestination;
                    const address = selectedAddress && selectedAddress.textContent !== 'Click on the map to select a delivery location' 
                        ? selectedAddress.textContent 
                        : (addressInput ? addressInput.value : '');
                    
                    // Update input with selected address
                    if (selectedAddress && addressInput && selectedAddress.textContent !== 'Click on the map to select a delivery location') {
                        addressInput.value = selectedAddress.textContent;
                    }
                    
                    // Calculate and show route from office to selected location (like Google Maps)
                    calculateRoute(location, address);
                    
                    // Ensure location info panel is visible
                    const locationInfo = document.getElementById('selected-location-info');
                    if (locationInfo) {
                        locationInfo.style.display = 'block';
                    }
                } else if (selectedAddress && addressInput && selectedAddress.textContent !== 'Click on the map to select a delivery location') {
                    // If no destination stored but there's an address, geocode it and show route
                    const address = selectedAddress.textContent;
                    addressInput.value = address;
                    
                    // Search for the address and show route
                    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                        const geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ address: address }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                const location = {
                                    lat: results[0].geometry.location.lat(),
                                    lng: results[0].geometry.location.lng()
                                };
                                selectLocation(location);
                            }
                        });
                    }
                }
            });
        }
        
        // Resize map when sidebar opens
        setTimeout(() => {
            if (map) {
                google.maps.event.trigger(map, 'resize');
            }
        }, 500);
        
        // Initialize autocomplete after map is ready (wait for Places API to be fully loaded)
        setTimeout(() => {
            if (typeof google !== 'undefined' && typeof google.maps !== 'undefined' && typeof google.maps.places !== 'undefined') {
                initNominatimAutocomplete();
            } else {
                console.warn('Places API not ready yet, retrying...');
                // Retry after a bit more time
                setTimeout(() => {
                    initNominatimAutocomplete();
                }, 500);
            }
        }, 300);
        
    } catch (error) {
        console.error('Error creating Google Map:', error);
        const mapElement = document.getElementById('delivery-map');
        if (mapElement) {
            mapElement.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-600">Error loading map. Please refresh the page.</div>';
        }
    }
};

// Select location on map
function selectLocation(location) {
    if (!map) {
        console.error('Map not initialized');
        return;
    }
    
    // Store destination globally
    window.currentRouteDestination = location;
    
    // Remove previous marker (we'll let DirectionsRenderer handle markers)
    if (selectedMarker) {
        selectedMarker.setMap(null);
        selectedMarker = null;
    }

    // Show loading state
    const distanceInfo = document.getElementById('distance-info');
    if (distanceInfo) {
        distanceInfo.textContent = 'Getting address...';
    }
    
    // Show location info panel
    const locationInfo = document.getElementById('selected-location-info');
    if (locationInfo) {
        locationInfo.style.display = 'block';
    }

    // Use Google Geocoding API for reverse geocoding
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ location: { lat: location.lat, lng: location.lng } }, function(results, status) {
        if (status === 'OK' && results[0]) {
            const address = results[0].formatted_address;
            
            // Update UI
            const selectedAddress = document.getElementById('selected-address');
            const addressInput = document.getElementById('jobsite-address');
            
            if (selectedAddress) selectedAddress.textContent = address;
            
            // Automatically update the input field
            if (addressInput) {
                addressInput.value = address;
            }
            
            // Automatically calculate and show route from office to selected location
            // This will display the route like Google Maps directions
            calculateRoute(location, address);
        } else {
            console.error('Geocoder failed:', status);
            
            // Show user-friendly error message
            if (status === 'REQUEST_DENIED') {
                const distanceInfo = document.getElementById('distance-info');
                if (distanceInfo) {
                    distanceInfo.textContent = 'Please enable Geocoding API in Google Cloud Console';
                    distanceInfo.style.color = '#ef4444';
                }
            }
            
            const address = `Lat: ${location.lat.toFixed(4)}, Lng: ${location.lng.toFixed(4)}`;
            
            const selectedAddress = document.getElementById('selected-address');
            if (selectedAddress) selectedAddress.textContent = address;
            
            // Still calculate route even if geocoding fails
            calculateRoute(location, address);
        }
    });
}

// Make selectLocation globally accessible (only once, after function definition)
window.selectLocation = selectLocation;

// Calculate route and distance using Google Directions API
function calculateRoute(destination, address) {
    if (!directionsService || !directionsRenderer || !map) {
        console.error('Directions service not initialized');
        return;
    }
    
    // Show loading state
    const distanceInfo = document.getElementById('distance-info');
    if (distanceInfo) {
        distanceInfo.textContent = 'Calculating route from office...';
    }

    // Use Google Directions Service - route from office to selected destination
    const request = {
        origin: { lat: officeLocation.lat, lng: officeLocation.lng },
        destination: { lat: destination.lat, lng: destination.lng },
        travelMode: google.maps.TravelMode.DRIVING,
        optimizeWaypoints: false
    };

    directionsService.route(request, function(result, status) {
        if (status === 'OK') {
            // Remove any fallback polyline before drawing the real route
            if (fallbackPolyline) {
                fallbackPolyline.setMap(null);
                fallbackPolyline = null;
            }
            
            // Display the route on the map (this automatically draws the route line like Google Maps)
            // The DirectionsRenderer will show:
            // - A route line from office to destination
            // - Markers for origin (office) and destination
            // - Turn-by-turn directions visualization
            directionsRenderer.setDirections(result);
            
            // Get route information
            const route = result.routes[0];
            const leg = route.legs[0];
            const distance = (leg.distance.value / 1000).toFixed(1); // Convert to km
            const duration = Math.round(leg.duration.value / 60); // Convert to minutes
            
            // Update distance info (like Google Maps shows)
            if (distanceInfo) {
                distanceInfo.textContent = `Distance: ${distance} km • Duration: ${duration} min`;
                distanceInfo.style.color = '#CE9704';
                distanceInfo.style.fontWeight = '600';
            }
            
            // Fit map to show entire route (office to destination) with padding
            // This ensures both origin and destination are visible, just like Google Maps
            const bounds = new google.maps.LatLngBounds();
            
            // Add origin and destination
            bounds.extend({ lat: officeLocation.lat, lng: officeLocation.lng }); // Office
            bounds.extend({ lat: destination.lat, lng: destination.lng }); // Destination
            
            // Also extend bounds with all route waypoints to show complete route
            route.legs.forEach(leg => {
                bounds.extend(leg.start_location);
                bounds.extend(leg.end_location);
            });
            
            // Fit bounds with padding (like Google Maps does)
            map.fitBounds(bounds, { top: 50, right: 50, bottom: 50, left: 50 });
            
        } else {
            console.error('Directions request failed:', status);
            
            // Show user-friendly error message
            if (status === 'REQUEST_DENIED') {
                if (distanceInfo) {
                    distanceInfo.innerHTML = '<span style="color: #ef4444;">Please enable Directions API in Google Cloud Console</span>';
                }
                
                // Fallback: draw a simple line between points
                // Remove previous fallback polyline if it exists
                if (fallbackPolyline) {
                    fallbackPolyline.setMap(null);
                }
                
                fallbackPolyline = new google.maps.Polyline({
                    path: [
                        { lat: officeLocation.lat, lng: officeLocation.lng },
                        { lat: destination.lat, lng: destination.lng }
                    ],
                    geodesic: true,
                    strokeColor: '#CE9704',
                    strokeOpacity: 0.6,
                    strokeWeight: 4
                });
                fallbackPolyline.setMap(map);
                
                // Calculate straight line distance
                const distance = calculateDistance(officeLocation, destination);
                if (distanceInfo) {
                    distanceInfo.innerHTML = `<span style="color: #ef4444;">API not enabled. Straight line: ${distance.toFixed(1)} km (approximate)</span>`;
                }
            } else {
                // Fallback: calculate straight line distance
                const distance = calculateDistance(officeLocation, destination);
                
                if (distanceInfo) {
                    distanceInfo.textContent = `Straight line distance: ${distance.toFixed(1)} km (approximate)`;
                    distanceInfo.style.color = '#ef4444';
                }
            }
        }
    });
}

// Calculate straight line distance
function calculateDistance(point1, point2) {
    const R = 6371; // Earth's radius in km
    const dLat = (point2.lat - point1.lat) * Math.PI / 180;
    const dLng = (point2.lng - point1.lng) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(point1.lat * Math.PI / 180) * Math.cos(point2.lat * Math.PI / 180) *
                Math.sin(dLng/2) * Math.sin(dLng/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Toggle self-pickup details
function toggleSelfPickup() {
    const selfPickup = document.getElementById('self-pickup');
    const noAddress = document.getElementById('no-address');
    const details = document.getElementById('self-pickup-details');
    const jobsiteAddress = document.getElementById('jobsite-address');
    const interactiveMap = document.getElementById('delivery-map');
    const googleMaps = document.getElementById('google-maps-static');
    const mapControls = document.getElementById('map-controls');
    const locationInfo = document.getElementById('selected-location-info');
    
    if (selfPickup && selfPickup.checked || (noAddress && noAddress.checked)) {
        // Auto-fill company address when self-pickup or no-address is selected
        if (jobsiteAddress) {
            jobsiteAddress.value = '39°43\'34.9"N 105°03\'20.7"W, Denver, CO, USA';
            jobsiteAddress.required = false;
            jobsiteAddress.placeholder = 'Optional: Additional delivery instructions...';
        }
        
        // Show details dropdown (horarios de atención), keep main interactive map, hide extra Google Maps iframe
        if (details) details.classList.remove('hidden');
        if (interactiveMap) interactiveMap.classList.remove('hidden');
        if (googleMaps) googleMaps.classList.add('hidden');
        if (mapControls) mapControls.style.display = 'none';
        if (locationInfo) locationInfo.style.display = 'none';
    } else {
        // Clear address input when unchecked
        if (jobsiteAddress) {
            jobsiteAddress.value = '';
            jobsiteAddress.required = true;
            jobsiteAddress.placeholder = 'Start typing and select from suggestions...';
        }
        
        // Hide details and Google Maps static iframe
        if (details) details.classList.add('hidden');
        if (googleMaps) googleMaps.classList.add('hidden');
        
        // Show interactive map with controls
        if (interactiveMap) interactiveMap.classList.remove('hidden');
        if (mapControls) mapControls.style.display = 'block';
        if (locationInfo) locationInfo.style.display = 'block';
    }
}

// Make checkboxes work like radio buttons (only one selected at a time)
function initDirectionsPage() {
    // Open cart sidebar immediately when on directions page (only on desktop/tablet, not mobile)
    const cartSidebar = document.getElementById('cart-sidebar');
    
    if (cartSidebar) {
        // Only open sidebar on desktop/tablet (>= 640px), keep closed on mobile
        if (window.innerWidth >= 640) {
            // Ensure sidebar is open from the start (desktop only)
            cartSidebar.classList.remove('translate-x-full');
            cartSidebar.classList.add('translate-x-0');
            
            // Show step indicator
            const stepIndicatorContainer = document.getElementById('step-indicator-container');
            if (stepIndicatorContainer) {
                stepIndicatorContainer.style.display = 'block';
            }
            
            // Set z-index for open cart sidebar (same as in app.blade.php)
            const nav = document.querySelector('nav');
            if (nav) {
                nav.classList.add('sidebar-open');
                nav.style.zIndex = '5';
            }
            const cartContainer = document.getElementById('cart-sidebar-container');
            if (cartContainer) {
                cartContainer.style.zIndex = '10';
            }
            cartSidebar.style.zIndex = '10';
        } else {
            // On mobile, ensure sidebar stays closed
            cartSidebar.classList.remove('translate-x-0');
            cartSidebar.classList.add('translate-x-full');
        }
        
        // Use Tailwind classes that are already handled in app.blade.php
        // Don't modify margins here to avoid layout shift
        const mainContent = document.getElementById('main-content');
        if (mainContent) {
            // The padding is already handled in app.blade.php when sidebar opens
            // Just ensure it's applied
            if (window.innerWidth >= 640) {
                mainContent.classList.add('pr-80');
            }
            if (window.innerWidth >= 1024) {
                mainContent.classList.add('lg:pr-96');
            }
        }
        
        // Recalculate map size after sidebar is open
        setTimeout(() => {
            if (typeof window.map !== 'undefined' && window.map && typeof google !== 'undefined') {
                google.maps.event.trigger(window.map, 'resize');
            }
        }, 100);
        
        // Restore selected location from localStorage if available
        setTimeout(() => {
            const saved = localStorage.getItem('reborn-rentals-directions');
            if (saved && map && typeof selectLocation === 'function') {
                try {
                    const data = JSON.parse(saved);
                    if (data.latitude && data.longitude) {
                        selectLocation({ lat: data.latitude, lng: data.longitude });
                    }
                } catch (e) {
                    console.error('Error restoring location from localStorage:', e);
                }
            }
        }, 500);
    }
    
    const selfPickup = document.getElementById('self-pickup');
    const noAddress = document.getElementById('no-address');
    
    if (selfPickup) {
        selfPickup.addEventListener('change', function() {
            if (selfPickup.checked) {
                if (noAddress) noAddress.checked = false;
                toggleSelfPickup();
            } else {
                toggleSelfPickup();
            }
        });
    }
    
    if (noAddress) {
        noAddress.addEventListener('change', function() {
            if (noAddress.checked) {
                if (selfPickup) selfPickup.checked = false;
                toggleSelfPickup();
            } else {
                toggleSelfPickup();
            }
        });
    }
    
    // Load Google Maps API with callback (will initialize autocomplete after map loads)
    if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
        @php
            $googleMapsApiKey = config('services.google.maps_api_key');
        @endphp
        
        @if(empty($googleMapsApiKey))
            console.error('Google Maps API key is not configured. Please set GOOGLE_MAPS_API_KEY in your .env file.');
            const mapElement = document.getElementById('delivery-map');
            if (mapElement) {
                mapElement.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-red-600 p-4 text-center"><div><p class="font-semibold mb-2">Google Maps Error</p><p class="text-sm">API key not configured. Please contact the administrator.</p></div></div>';
            }
        @else
            const script = document.createElement('script');
            script.src = 'https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places,geometry&callback=initGoogleMap';
            script.async = true;
            script.defer = true;
            document.head.appendChild(script);
        @endif
    } else {
        // Google Maps already loaded, initialize immediately
        initGoogleMap();
        // Initialize autocomplete after a short delay to ensure Places API is ready
        setTimeout(() => {
            initNominatimAutocomplete();
        }, 100);
    }
    
    // Update when-where button based on cart
    function updateWhenWhereButton() {
        const whenWhereBtn = document.getElementById('when-where-btn');
        if (whenWhereBtn) {
            whenWhereBtn.disabled = false;
            whenWhereBtn.classList.remove('bg-gray-600', 'text-gray-400', 'cursor-not-allowed');
            whenWhereBtn.classList.add('bg-[#CE9704]', 'text-white', 'hover:bg-[#B8860B]');
        }
    }
    
    // Update button on page load
    updateWhenWhereButton();
    
    // Also update when cart changes
    if (typeof window.updateCartDisplay === 'function') {
        const originalUpdate = window.updateCartDisplay;
        window.updateCartDisplay = function() {
            originalUpdate();
            updateWhenWhereButton();
        };
    }
    
    // Add event listeners to date inputs for stock checking
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            // Small delay to allow end date to be set if user is selecting both
            setTimeout(() => {
                checkStockAvailability();
            }, 300);
        });
        startDateInput.addEventListener('input', function() {
            // Small delay to allow end date to be set if user is selecting both
            setTimeout(() => {
                checkStockAvailability();
            }, 300);
        });
    }
    
    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            checkStockAvailability();
        });
        endDateInput.addEventListener('input', function() {
            checkStockAvailability();
        });
    }
    
    // Event listener para el botón "Continue to Checkout"
    const whenWhereBtn = document.getElementById('when-where-btn');
    if (whenWhereBtn) {
        whenWhereBtn.addEventListener('click', function(e) {
            if (!this.disabled) {
                // Validate form before proceeding
                if (validateForm()) {
                    // Save form data
                    saveFormData();
                    
                    // Redirect to checkout page
                    window.location.href = '{{ route("checkout") }}';
                }
            }
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDirectionsPage);
} else {
    initDirectionsPage();
}

// Initialize Google Places Autocomplete
function initNominatimAutocomplete() {
    const addressInput = document.getElementById('jobsite-address');
    if (!addressInput) {
        console.warn('Address input not found');
        return;
    }

    // Check if Google Places API is loaded
    if (typeof google === 'undefined' || typeof google.maps === 'undefined' || typeof google.maps.places === 'undefined') {
        console.warn('Google Places API not loaded yet, will retry...');
        // Retry after a delay
        setTimeout(() => {
            initNominatimAutocomplete();
        }, 500);
        return;
    }

    // Create autocomplete using Google Places API
    try {
        const autocomplete = new google.maps.places.Autocomplete(addressInput, {
            componentRestrictions: { country: 'us' },
            fields: ['geometry', 'formatted_address', 'address_components', 'name'],
            types: ['address', 'establishment', 'geocode']
        });

        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            
            if (!place.geometry) {
                console.error('No geometry found for place');
                return;
            }

            const location = {
                lat: place.geometry.location.lat(),
                lng: place.geometry.location.lng()
            };

            // Update map with selected location and show route automatically
            selectLocation(location);
            
            // Center map on selected location first, then route will be shown
            if (map) {
                map.setCenter(location);
                map.setZoom(14);
            }
        });
        
        console.log('Google Places Autocomplete initialized successfully');
        
    } catch (error) {
        console.error('Error initializing Google Places Autocomplete:', error);
    }
    
    // Always add Enter key and Search button functionality (works with or without autocomplete)
    addressInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            searchAddress();
        }
    });
    
    const searchBtn = document.getElementById('search-address-btn');
    if (searchBtn) {
        searchBtn.addEventListener('click', function() {
            searchAddress();
        });
    }
}

// Search address function (works with or without autocomplete)
function searchAddress() {
    const addressInput = document.getElementById('jobsite-address');
    if (!addressInput || !addressInput.value.trim()) {
        return;
    }
    
    const query = addressInput.value.trim();
    
    // Show loading state
    const distanceInfo = document.getElementById('distance-info');
    if (distanceInfo) {
        distanceInfo.textContent = 'Searching address...';
    }
    
    // Use Google Geocoding API to search for the address
    if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
        const geocoder = new google.maps.Geocoder();
        
        geocoder.geocode({ address: query }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const location = {
                    lat: results[0].geometry.location.lat(),
                    lng: results[0].geometry.location.lng()
                };
                
                // Update input with formatted address
                addressInput.value = results[0].formatted_address;
                
                // Select location on map
                selectLocation(location);
            } else {
                console.error('Geocoding failed:', status);
                if (distanceInfo) {
                    if (status === 'REQUEST_DENIED') {
                        distanceInfo.textContent = 'Please enable Geocoding API in Google Cloud Console';
                    } else {
                        distanceInfo.textContent = 'Address not found. Please try a different address.';
                    }
                    distanceInfo.style.color = '#ef4444';
                    setTimeout(() => {
                        distanceInfo.textContent = '';
                        distanceInfo.style.color = '';
                    }, 5000);
                }
            }
        });
    } else {
        console.error('Google Maps not loaded');
    }
}

// Select address suggestion
window.selectAddressSuggestion = function(address, lat, lng) {
    const addressInput = document.getElementById('jobsite-address');
    const suggestionsDiv = document.getElementById('address-suggestions');
    
    if (addressInput) {
        addressInput.value = address;
    }
    if (suggestionsDiv) {
        suggestionsDiv.style.display = 'none';
    }
    
    // Update map with selected location using Google Maps
    if (typeof window.selectLocation === 'function') {
        window.selectLocation({ lat, lng });
    }
};

// Google Maps is now integrated, no need for iframe message handling

// Check stock availability when dates are selected
function checkStockAvailability() {
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const loader = document.getElementById('stock-validation-loader');
    const resultsDiv = document.getElementById('stock-availability-results');
    
    if (!startDate.value || !endDate.value) return;
    
    // Validate dates
    if (new Date(startDate.value) >= new Date(endDate.value)) {
        if (loader) loader.classList.add('hidden');
        if (resultsDiv) resultsDiv.classList.add('hidden');
        return;
    }
    
    // Show loader
    if (loader) loader.classList.remove('hidden');
    if (resultsDiv) {
        resultsDiv.classList.add('hidden');
        resultsDiv.innerHTML = '';
    }
    
    // Get cart from session/localStorage or fetch from server
    fetch('/cart', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(cartData => {
        if (!cartData.cart || Object.keys(cartData.cart).length === 0) {
            if (loader) loader.classList.add('hidden');
            if (resultsDiv) resultsDiv.classList.add('hidden');
            return;
        }
        
        // Check stock for each product in cart
        const productIds = Object.keys(cartData.cart);
        const stockChecks = productIds.map(productId => {
            const quantity = cartData.cart[productId];
            return fetch(`/stock/check?product_id=${productId}&start_date=${startDate.value}&end_date=${endDate.value}&quantity=${quantity}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => ({
                productId: productId,
                productName: cartData.products?.find(p => p.id == productId)?.name || `Product #${productId}`,
                quantity: quantity,
                ...data
            }))
            .catch(error => {
                console.error('Error checking stock for product:', productId, error);
                return {
                    productId: productId,
                    productName: cartData.products?.find(p => p.id == productId)?.name || `Product #${productId}`,
                    quantity: quantity,
                    allowed: true, // Allow on error to not block user
                    error: true
                };
            });
        });
        
        // Wait for all stock checks to complete
        return Promise.all(stockChecks);
    })
    .then(results => {
        // Hide loader
        if (loader) loader.classList.add('hidden');
        
        // Display results
        displayStockAvailabilityResults(results);
    })
    .catch(error => {
        console.error('Error checking stock availability:', error);
        if (loader) loader.classList.add('hidden');
    });
}

// Display stock availability results
function displayStockAvailabilityResults(results) {
    const resultsDiv = document.getElementById('stock-availability-results');
    if (!resultsDiv) return;
    
    const unavailableProducts = results.filter(r => !r.allowed && !r.error);
    const availableProducts = results.filter(r => r.allowed && !r.error);
    const errorProducts = results.filter(r => r.error);
    
    if (unavailableProducts.length === 0 && errorProducts.length === 0) {
        // All products available
        resultsDiv.innerHTML = `
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm sm:text-base font-semibold text-green-800">All products are available for the selected dates</span>
                </div>
            </div>
        `;
        resultsDiv.classList.remove('hidden');
        return;
    }
    
    // Build results HTML
    let html = '';
    
    if (unavailableProducts.length > 0) {
        html += `
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4 mb-3">
                <div class="flex items-start gap-2 mb-2">
                    <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-sm sm:text-base font-semibold text-red-800 mb-2">Some products are not available:</h4>
                        <ul class="space-y-2">
        `;
        
        unavailableProducts.forEach(product => {
            html += `
                <li class="text-sm text-red-700">
                    <span class="font-medium">${escapeHtml(product.productName)}</span> - 
                    Requested: ${product.quantity} units, Available: ${product.available_stock || 0} units
                    ${product.message ? `<br><span class="text-xs text-red-600 italic">${escapeHtml(product.message)}</span>` : ''}
                </li>
            `;
        });
        
        html += `
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }
    
    if (availableProducts.length > 0 && unavailableProducts.length > 0) {
        html += `
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 sm:p-4">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm sm:text-base font-semibold text-green-800">${availableProducts.length} product(s) available</span>
                </div>
            </div>
        `;
    }
    
    if (html) {
        resultsDiv.innerHTML = html;
        resultsDiv.classList.remove('hidden');
        
        // Show notifications for unavailable products
        if (unavailableProducts.length > 0 && typeof window.showStockNotification === 'function') {
            unavailableProducts.forEach(product => {
                window.showStockNotification(
                    'error',
                    `${product.productName} - Not Available`,
                    product.message || 'This product is not available for rent during the selected dates.',
                    product.reservation_periods || null,
                    null
                );
            });
        }
    }
}

// Helper function to escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Form validation
function validateForm() {
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const jobsiteAddress = document.getElementById('jobsite-address');
    const selfPickup = document.getElementById('self-pickup');
    const noAddress = document.getElementById('no-address');
    const selectedAddressEl = document.getElementById('selected-address');

    // Copy address from panel if input is empty
    if (jobsiteAddress && selectedAddressEl && !jobsiteAddress.value.trim()) {
        const txt = selectedAddressEl.textContent || '';
        if (txt && txt !== 'Click on the map to select a delivery location') {
            jobsiteAddress.value = txt;
        }
    }

    // Auto-fill dates if missing: today and tomorrow
    if (startDate && !startDate.value) {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        startDate.value = `${yyyy}-${mm}-${dd}`;
    }
    if (endDate && !endDate.value) {
        const t = new Date();
        t.setDate(t.getDate() + 1);
        const yyyy = t.getFullYear();
        const mm = String(t.getMonth() + 1).padStart(2, '0');
        const dd = String(t.getDate()).padStart(2, '0');
        endDate.value = `${yyyy}-${mm}-${dd}`;
    }

    if (!startDate.value || !endDate.value) {
        alert('Please select both start and end dates.');
        return false;
    }

    if (new Date(startDate.value) >= new Date(endDate.value)) {
        alert('End date must be after start date.');
        return false;
    }

    // Only require address if not self-pickup or no-address
    if (!selfPickup.checked && !noAddress.checked && !jobsiteAddress.value.trim()) {
        alert('Please enter a jobsite address or select a pickup option.');
        return false;
    }

    return true;
}

// Save form data
function saveFormData() {
    const checked = document.querySelector('input[name="pickup-option"]:checked');
    const pickupOption = checked ? checked.value : null;
    const selfPickup = document.getElementById('self-pickup').checked;
    const noAddress = document.getElementById('no-address').checked;
    
    // Get coordinates from selected location on map
    let latitude = null;
    let longitude = null;
    let jobsiteAddress = '';
    
    // Si es self-pickup, guardar directamente "Self Pickup"
    if (selfPickup) {
        jobsiteAddress = 'Self Pickup';
    } else if (noAddress) {
        jobsiteAddress = 'Jobsite Lot doesn\'t have an address';
    } else if (window.currentRouteDestination) {
        // Si hay ubicación seleccionada en el mapa, crear link de Google Maps
        latitude = window.currentRouteDestination.lat;
        longitude = window.currentRouteDestination.lng;
        const addressText = document.getElementById('jobsite-address').value || '';
        
        // Crear link de Google Maps con las coordenadas
        const googleMapsLink = `https://www.google.com/maps?q=${latitude},${longitude}`;
        
        // Guardar el link junto con la dirección de texto
        if (addressText) {
            jobsiteAddress = `${addressText} | ${googleMapsLink}`;
        } else {
            jobsiteAddress = googleMapsLink;
        }
    } else {
        // Si no hay coordenadas pero hay texto en el input
        const addressInput = document.getElementById('jobsite-address');
        jobsiteAddress = addressInput ? addressInput.value : '';
    }
    
    const formData = {
        startDate: document.getElementById('start-date').value,
        endDate: document.getElementById('end-date').value,
        jobsiteAddress: jobsiteAddress,
        latitude: latitude,
        longitude: longitude,
        pickupOption: pickupOption,
        selfPickupChecked: selfPickup,
        noAddressChecked: noAddress,
        notes: document.getElementById('jobsite-address').value || ''
    };
    
    localStorage.setItem('reborn-rentals-directions', JSON.stringify(formData));
    console.log('Directions data saved:', formData);
}
</script>
@endpush
@endsection
