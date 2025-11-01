@extends('layouts.app')

@section('title', 'Select Jobsite & Time - Reborn Rentals')

@section('content')
<!-- Step 2: Select Jobsite & Time -->
<div class="max-w-6xl mx-auto px-3 sm:px-4 md:px-6 mt-8 sm:mt-12 md:mt-20 mb-12 sm:mb-16 md:mb-20">
    <div class="bg-white rounded-lg p-4 sm:p-6 md:p-8">
        <!-- Section Title -->
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3 sm:mb-4">Select Jobsite & Time</h1>
        
        <!-- Introductory Text -->
        <p class="text-gray-600 text-xs sm:text-sm mb-6 sm:mb-8 leading-relaxed">
            Select date and place of delivery, depending on your location, delivery fees may apply, Long fares apply if jobsite is located 4 hours away or more. Orders outside of Mainland US, not accepted.
        </p>
        
        <div>
            <!-- Date Selection Section -->
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mb-4">
                    <!-- Start Date -->
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input 
                            type="date" 
                            class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent"
                            id="start-date"
                            name="start_date"
                            required
                        />
                    </div>
                    
                    <!-- End Date -->
                    <div class="flex-1 w-full">
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <div class="relative">
                            <input 
                                type="date" 
                                class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent pr-10"
                                id="end-date"
                                name="end_date"
                                required
                            />
                            <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Date Selection Instructions -->
                <p class="text-gray-600 text-sm leading-relaxed">
                    Choose start and end dates for your rental period. Weekend deliveries are available. Tap a start date and then an end date to set your delivery period. You can select weekends too. The selected range will be highlighted.
                </p>
            </div>
            
            <!-- Jobsite Address Section -->
            <div class="mb-6 sm:mb-8">
                <div class="flex flex-col sm:flex-row justify-between items-start mb-3 sm:mb-4 gap-2 sm:gap-0">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Insert Jobsite Address</h2>
                    <span class="text-xs sm:text-sm text-gray-500 underline">*Cancellation Fees may apply.</span>
                </div>
                
                <!-- Address Search Input -->
                <div class="relative">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="jobsite_address"
                        placeholder="Start typing and select from suggestions..."
                        class="w-full pl-9 sm:pl-10 pr-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent"
                        id="jobsite-address"
                        autocomplete="off"
                    />
                </div>
            </div>
            
            <!-- Pickup Options -->
            <div class="mb-8">
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="pickup-option" class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704] focus:ring-2" id="self-pickup" value="self-pickup">
                        <span class="ml-3 text-gray-700">Self-Pickup</span>
                    </label>
                    
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="pickup-option" class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704] focus:ring-2" id="no-address" value="no-address">
                        <span class="ml-3 text-gray-700">Jobsite Lot doesn't have an address.</span>
                    </label>
                </div>
                
                <!-- Interactive Map Section -->
                <div class="mt-6 sm:mt-8">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Select Delivery Location on Map</h3>
                    <p class="text-gray-600 text-xs sm:text-sm mb-3 sm:mb-4">
                        Click on the map to select your delivery location. The map will show the route from our office to your selected location.
                    </p>
                    
                    <!-- Map Container -->
                    <div class="relative" style="z-index: 1;">
                        <div id="delivery-map" class="w-full h-64 sm:h-80 md:h-96 rounded-lg overflow-hidden border border-gray-300 shadow-lg">
                            <iframe 
                                src="/map.html"
                                title="Delivery Map"
                                id="delivery-map-iframe"
                                style="border:0; width:100%; height:100%; min-height: 400px;"
                                loading="lazy"
                            ></iframe>
                        </div>
                        
                        <!-- Google Maps iframe (hidden by default, shown for self-pickup and no-address) -->
                        <div id="google-maps-static" class="w-full h-96 rounded-lg overflow-hidden border border-gray-300 shadow-lg hidden">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3077.576787884259!2d-119.80605779999999!3d39.5240405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x809940b3fe48bd53%3A0x4e3afeee9f24c1bc!2s401%20Ryland%20St%20Suite%20200-A%2C%20Reno%2C%20NV%2089502%2C%20EE.%20UU.!5e0!3m2!1ses-419!2sbo!4v1761325010616!5m2!1ses-419!2sbo" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                        
                        <!-- Map Controls -->
                        <div class="absolute top-2 sm:top-4 right-2 sm:right-4 bg-white rounded-lg shadow-lg p-1.5 sm:p-2 space-y-1.5 sm:space-y-2">
                            <button 
                                type="button"
                                id="center-map-btn" 
                                class="bg-[#CE9704] text-white px-2 sm:px-3 py-1.5 sm:py-2 rounded text-xs sm:text-sm font-medium hover:bg-[#B8860B] transition-colors duration-200 block w-full"
                                title="Center on Reborn Rentals Office"
                            >
                                📍 Our Office
                            </button>
                            <button 
                                type="button"
                                id="clear-route-btn" 
                                class="bg-gray-600 text-white px-2 sm:px-3 py-1.5 sm:py-2 rounded text-xs sm:text-sm font-medium hover:bg-gray-700 transition-colors duration-200 block w-full"
                                title="Clear Route"
                            >
                                🗑️ Clear Route
                            </button>
                        </div>
                        
                        <!-- Selected Location Info -->
                        <div id="selected-location-info" class="absolute bottom-4 left-4 right-4 bg-white rounded-lg shadow-lg p-4">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-sm">Selected Location:</h4>
                                    <p id="selected-address" class="text-gray-600 text-sm mt-1">Click on the map to select a delivery location</p>
                                    <p id="distance-info" class="text-[#CE9704] text-xs mt-1"></p>
                                </div>
                                <button 
                                    type="button"
                                    id="use-location-btn" 
                                    class="bg-[#CE9704] text-white px-3 py-1 rounded text-xs font-medium hover:bg-[#B8860B] transition-colors duration-200 ml-2"
                                >
                                    Use This Location
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Map Instructions -->
                    <div class="mt-4 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <div class="flex items-start">
                            <div class="shrink-0">
                                <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-blue-800">How to use the map:</h4>
                                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                                    <li>• Click anywhere on the map to select your delivery location</li>
                                    <li>• The map will automatically show the route from our office to your location</li>
                                    <li>• Use "📍 Our Office" to center the map on our location</li>
                                    <li>• Use "🗑️ Clear Route" to remove the current route and start over</li>
                                    <li>• Click "Use This Location" to set the address in the form above</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Self-Pickup Dropdown -->
                <div id="self-pickup-details" class="mt-6 p-4 sm:p-6 bg-gray-50 rounded-lg border border-gray-200 hidden">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-3 sm:mb-4">Pickup Location Details</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Address -->
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Our Address</h4>
                            <div class="space-y-1 text-gray-700">
                                <p>401 Ryland St.</p>
                                <p>Ste 200 A</p>
                                <p>Reno, NV 89502</p>
                                <p>Denver, CO 80202</p>
                                <p>United States</p>
                            </div>
                        </div>
                        
                        <!-- Hours -->
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Hours</h4>
                            <ul class="text-gray-700 space-y-1">
                                <li><span class="font-medium">Mon–Fri:</span> 08:00 – 18:00</li>
                                <li><span class="font-medium">Sat:</span> 09:00 – 14:00</li>
                                <li><span class="font-medium">Sun:</span> Closed</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Map -->
                    <div class="mt-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-3">Location Map</h4>
                        <div class="rounded-lg overflow-hidden border border-gray-300">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3077.576787884259!2d-119.80605779999999!3d39.5240405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x809940b3fe48bd53%3A0x4e3afeee9f24c1bc!2s401%20Ryland%20St%20Suite%20200-A%2C%20Reno%2C%20NV%2089502%2C%20EE.%20UU.!5e0!3m2!1ses-419!2sbo!4v1761325010616!5m2!1ses-419!2sbo" 
                                width="100%" 
                                height="300" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                class="w-full h-64"
                            ></iframe>
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
        min-height: 400px !important;
        height: 100%;
    }
    .leaflet-container {
        height: 100%;
        width: 100%;
    }
</style>
@endpush

@push('scripts')
<script>
    console.log('=== DIRECTIONS PAGE LOADED ===');
    const USE_IFRAME_MAP = true;
let map;
let selectedMarker;
let officeMarker;
let routeLayer;

// Expose map globally for invalidateSize calls
window.map = null;

// Reborn Rentals Office coordinates
const officeLocation = {
    lat: 39.5240405,
    lng: -119.80605779999999,
    address: "401 Ryland St Suite 200-A, Reno, NV 89502, USA"
};

// Initialize Map
function initMap() {
    console.log('Initializing Leaflet map...');
    
    // Get L from window
    const L = window.L;
    
    // Check if Leaflet is available
    if (typeof L === 'undefined' || typeof L.map !== 'function') {
        console.error('Leaflet not loaded');
        const mapElement = document.getElementById('delivery-map');
        if (mapElement) {
            mapElement.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-600">Map loading failed. Please refresh the page.</div>';
        }
        return;
    }
    
    try {
        // Create map with OpenStreetMap tiles
        map = L.map('delivery-map').setView([officeLocation.lat, officeLocation.lng], 12);
        
        // Expose map globally
        window.map = map;
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        console.log('Map created successfully');
        
        // Ensure map is properly sized
        setTimeout(() => {
            if (map) {
                map.invalidateSize();
            }
        }, 500);

        // Add office marker
        const officeIcon = L.divIcon({
            html: `
                <div style="
                    width: 40px; 
                    height: 40px; 
                    background: #CE9704; 
                    border: 3px solid white; 
                    border-radius: 50%; 
                    display: flex; 
                    align-items: center; 
                    justify-content: center;
                    font-size: 20px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                ">
                    🏢
                </div>
            `,
            className: 'custom-div-icon',
            iconSize: [40, 40],
            iconAnchor: [20, 20]
        });

        officeMarker = L.marker([officeLocation.lat, officeLocation.lng], { icon: officeIcon })
            .addTo(map)
            .bindPopup('Reborn Rentals Office');

        // Add click listener to map
        map.on('click', function(event) {
            const clickedLocation = {
                lat: event.latlng.lat,
                lng: event.latlng.lng
            };
            
            selectLocation(clickedLocation);
        });

        // Center map button
        const centerBtn = document.getElementById('center-map-btn');
        if (centerBtn) {
            centerBtn.addEventListener('click', function() {
                map.setView([officeLocation.lat, officeLocation.lng], 12);
            });
        }

        // Clear route button
        const clearRouteBtn = document.getElementById('clear-route-btn');
        if (clearRouteBtn) {
            clearRouteBtn.addEventListener('click', function() {
                // Remove route layer
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                    routeLayer = null;
                }
                
                // Remove selected marker
                if (selectedMarker) {
                    map.removeLayer(selectedMarker);
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

        // Use location button
        const useLocationBtn = document.getElementById('use-location-btn');
        if (useLocationBtn) {
            useLocationBtn.addEventListener('click', function() {
                const selectedAddress = document.getElementById('selected-address');
                const addressInput = document.getElementById('jobsite-address');
                if (selectedAddress && addressInput && selectedAddress.textContent !== 'Click on the map to select a delivery location') {
                    addressInput.value = selectedAddress.textContent;
                }
            });
        }
        
    } catch (error) {
        console.error('Error creating map:', error);
        const mapElement = document.getElementById('delivery-map');
        if (mapElement) {
            mapElement.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-600">Error loading map. Please refresh the page.</div>';
        }
    }
}

// Select location on map
function selectLocation(location) {
    window.selectLocation = selectLocation;
    const L = window.L;
    
    // Remove previous marker
    if (selectedMarker) {
        map.removeLayer(selectedMarker);
    }

    // Add new marker
    const selectedIcon = L.divIcon({
        html: `
            <div style="
                width: 40px; 
                height: 40px; 
                background: #4A4A4A; 
                border: 3px solid white; 
                border-radius: 50%; 
                display: flex; 
                align-items: center; 
                justify-content: center;
                font-size: 20px;
                box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            ">
                📍
            </div>
        `,
        className: 'custom-div-icon',
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });

    selectedMarker = L.marker([location.lat, location.lng], { icon: selectedIcon })
        .addTo(map)
        .bindPopup('Selected Delivery Location');

    // Show loading state
    const distanceInfo = document.getElementById('distance-info');
    if (distanceInfo) {
        distanceInfo.textContent = 'Getting address...';
    }

    // Use Nominatim for reverse geocoding
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${location.lat}&lon=${location.lng}`)
        .then(response => response.json())
        .then(data => {
            const address = data.display_name || 'Address not found';
            
            // Update UI
            const selectedAddress = document.getElementById('selected-address');
            const addressInput = document.getElementById('jobsite-address');
            
            if (selectedAddress) selectedAddress.textContent = address;
            
            // Automatically update the input field
            if (addressInput) {
                addressInput.value = address;
            }
            
            // Calculate distance and show route
            calculateRoute(location, address);
        })
        .catch(error => {
            console.error('Error getting address:', error);
            const address = `Lat: ${location.lat.toFixed(4)}, Lng: ${location.lng.toFixed(4)}`;
            
            const selectedAddress = document.getElementById('selected-address');
            
            if (selectedAddress) selectedAddress.textContent = address;
            
            calculateRoute(location, address);
        });
}

// Calculate route and distance using OpenRouteService
function calculateRoute(destination, address) {
    const L = window.L;
    
    // Show loading state
    const distanceInfo = document.getElementById('distance-info');
    if (distanceInfo) {
        distanceInfo.textContent = 'Calculating route...';
    }

    // Use free OpenRouteService
    const start = `${officeLocation.lng},${officeLocation.lat}`;
    const end = `${destination.lng},${destination.lat}`;
    
    fetch(`https://api.openrouteservice.org/v2/directions/driving-car?api_key=5b3ce3597851110001cf6248b4b8b8b8&start=${start}&end=${end}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.features && data.features[0]) {
                const route = data.features[0];
                const distance = (route.properties.summary.distance / 1000).toFixed(1);
                const duration = Math.round(route.properties.summary.duration / 60);
                
                // Remove previous route layer
                if (routeLayer) {
                    map.removeLayer(routeLayer);
                }
                
                // Draw route on map
                const coordinates = route.geometry.coordinates.map(coord => [coord[1], coord[0]]);
                routeLayer = L.polyline(coordinates, {
                    color: '#CE9704',
                    weight: 6,
                    opacity: 0.9,
                    dashArray: '10, 5'
                }).addTo(map);
                
                // Update distance info
                if (distanceInfo) {
                    distanceInfo.textContent = `Distance: ${distance} km • Duration: ${duration} min`;
                }
                
                // Fit map to show entire route
                const group = new L.featureGroup([routeLayer]);
                map.fitBounds(group.getBounds().pad(0.1));
                
            } else {
                throw new Error('No route found');
            }
        })
        .catch(error => {
            console.error('Error calculating route:', error);
            
            // Fallback: calculate straight line distance
            const distance = calculateDistance(officeLocation, destination);
            
            // Remove previous route layer
            if (routeLayer) {
                map.removeLayer(routeLayer);
            }
            
            // Draw straight line as fallback
            routeLayer = L.polyline([
                [officeLocation.lat, officeLocation.lng],
                [destination.lat, destination.lng]
            ], {
                color: '#CE9704',
                weight: 4,
                opacity: 0.6,
                dashArray: '5, 5'
            }).addTo(map);
            
            if (distanceInfo) {
                distanceInfo.textContent = `Straight line distance: ${distance.toFixed(1)} km (approximate)`;
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
    const mapControls = document.querySelector('.absolute.top-4.right-4');
    const locationInfo = document.getElementById('selected-location-info');
    
    if (selfPickup.checked || noAddress.checked) {
        // Auto-fill company address when self-pickup or no-address is selected
        if (jobsiteAddress) {
            jobsiteAddress.value = '401 Ryland St Suite 200-A, Reno, NV 89502, USA';
            jobsiteAddress.required = false;
            jobsiteAddress.placeholder = 'Optional: Additional delivery instructions...';
        }
        
        // Hide details and Google Maps
        if (details) details.classList.add('hidden');
        if (googleMaps) googleMaps.classList.add('hidden');
        
        // Keep interactive map but hide controls and location info
        if (interactiveMap) interactiveMap.classList.remove('hidden');
        if (mapControls) mapControls.style.display = 'none';
        if (locationInfo) locationInfo.style.display = 'none';
    } else {
        // Clear address input when unchecked
        if (jobsiteAddress) {
            jobsiteAddress.value = '';
            jobsiteAddress.required = true;
            jobsiteAddress.placeholder = 'Start typing and select from suggestions...';
        }
        
        // Hide details and Google Maps
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
    // Open cart sidebar automatically when on directions page
    // Wait longer to ensure map initializes first
    setTimeout(() => {
        const cartSidebar = document.getElementById('cart-sidebar');
        
        if (cartSidebar) {
            cartSidebar.classList.remove('translate-x-full');
            cartSidebar.classList.add('translate-x-0');
            
            // Show step indicator
            const stepIndicatorContainer = document.getElementById('step-indicator-container');
            if (stepIndicatorContainer) {
                stepIndicatorContainer.style.display = 'block';
            }
            
            // Adjust main content margin
            const mainContent = document.getElementById('main-content');
            if (mainContent) {
                mainContent.classList.add('cart-open');
                if (window.innerWidth >= 1024) {
                    mainContent.style.marginRight = '384px';
                } else if (window.innerWidth >= 640) {
                    mainContent.style.marginRight = '320px';
                }
            }
            
            // Recalculate map size when sidebar opens
            setTimeout(() => {
                if (typeof window.map !== 'undefined' && window.map) {
                    window.map.invalidateSize();
                }
            }, 300);
        }
    }, 2000); // Wait 2 seconds for map to initialize
    
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
    
    // Initialize autocomplete (using Nominatim)
    initNominatimAutocomplete();
    
    // Cargar mapa: si usamos iframe, no inicializamos Leaflet aquí
    if (!USE_IFRAME_MAP) {
        setTimeout(() => {
            try {
                initMap();
            } catch (error) {
                console.error('Error initializing map:', error);
                const mapElement = document.getElementById('delivery-map');
                if (mapElement) {
                    mapElement.innerHTML = '<div class="flex items-center justify-center h-full bg-gray-100 text-gray-600">Error loading map. Please refresh the page.</div>';
                }
            }
        }, 500);
    } else {
        // Ocultar controles del mapa de Leaflet cuando se usa iframe
        const mapControls = document.querySelector('.absolute.top-4.right-4');
        if (mapControls) mapControls.style.display = 'none';
        // Handshake con iframe
        const iframe = document.getElementById('delivery-map-iframe');
        if (iframe) {
            iframe.addEventListener('load', function() {
                try {
                    console.log('Iframe loaded, sending map:parentPing');
                    iframe.contentWindow && iframe.contentWindow.postMessage({ type: 'map:parentPing' }, '*');
                } catch (e) {
                    console.warn('No se pudo enviar postMessage al iframe:', e);
                }
            });
        }
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
    
    // Event listener para el botón "Proceed to Payment"
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

// Initialize Nominatim autocomplete
function initNominatimAutocomplete() {
    const addressInput = document.getElementById('jobsite-address');
    if (!addressInput) return;

    let timeoutId;
    const suggestionsDiv = document.createElement('div');
    suggestionsDiv.id = 'address-suggestions';
    suggestionsDiv.className = 'absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto';
    suggestionsDiv.style.display = 'none';
    addressInput.parentElement.appendChild(suggestionsDiv);

    addressInput.addEventListener('input', function() {
        clearTimeout(timeoutId);
        const query = addressInput.value.trim();

        if (query.length < 3) {
            suggestionsDiv.style.display = 'none';
            return;
        }

        timeoutId = setTimeout(() => {
            // Search only in Colorado, USA - append "Colorado" to the query
            const coloradoQuery = query.includes(', CO') || query.includes('Colorado') ? query : `${query}, Colorado`;
            fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(coloradoQuery)}&limit=5&countrycodes=us`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        suggestionsDiv.style.display = 'none';
                        return;
                    }

                    // Filter results to ensure they're in Colorado
                    const coloradoResults = data.filter(item => {
                        const addr = item.display_name.toLowerCase();
                        return addr.includes('colorado') || addr.includes(', co');
                    });

                    suggestionsDiv.innerHTML = coloradoResults.map(item => {
                        return `
                            <div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0 address-suggestion" 
                                 data-lat="${item.lat}" 
                                 data-lon="${item.lon}" 
                                 data-name="${item.display_name.replace(/"/g, '&quot;')}">
                                <div class="text-sm text-gray-900">${item.display_name}</div>
                            </div>
                        `;
                    }).join('');
                    suggestionsDiv.style.display = coloradoResults.length > 0 ? 'block' : 'none';
                    
                    // Add click listeners to suggestions
                    suggestionsDiv.querySelectorAll('.address-suggestion').forEach(el => {
                        el.addEventListener('click', function() {
                            const lat = parseFloat(this.dataset.lat);
                            const lon = parseFloat(this.dataset.lon);
                            const name = this.dataset.name;
                            if (typeof selectAddressSuggestion === 'function') {
                                selectAddressSuggestion(name, lat, lon);
                            }
                        });
                    });
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    suggestionsDiv.style.display = 'none';
                });
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!addressInput.contains(e.target) && !suggestionsDiv.contains(e.target)) {
            suggestionsDiv.style.display = 'none';
        }
    });
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
    
    // Update map with selected location (both iframe and Leaflet modes)
    setTimeout(() => {
        // Try iframe mode first
        const iframe = document.getElementById('delivery-map-iframe');
        if (iframe && iframe.contentWindow) {
            try {
                console.log('Sending location to iframe:', { lat, lng, address });
                iframe.contentWindow.postMessage({
                    type: 'map:selectLocation',
                    lat: lat,
                    lng: lng,
                    address: address
                }, '*');
            } catch (e) {
                console.warn('Error sending to iframe:', e);
            }
        }
        
        // Also try Leaflet mode if available
        if (typeof window.selectLocation === 'function') {
            window.selectLocation({ lat, lng });
        }
    }, 100);
};

// Escuchar selección desde el iframe del mapa
window.addEventListener('message', function(event) {
    const data = event.data || {};
    try { console.log('Map iframe message:', data); } catch (e) {}
    if (data.type === 'map:locationSelected') {
        const { address, lat, lng, distanceKm, durationMin } = data;
        const selectedAddress = document.getElementById('selected-address');
        const distanceInfo = document.getElementById('distance-info');
        const addressInput = document.getElementById('jobsite-address');
        const fallback = (lat && lng) ? `Lat: ${Number(lat).toFixed(4)}, Lng: ${Number(lng).toFixed(4)}` : '';
        if (selectedAddress) selectedAddress.textContent = address || fallback || 'Location selected';
        if (addressInput) addressInput.value = address || fallback || '';
        if (distanceInfo) {
            if (distanceKm && durationMin != null) {
                distanceInfo.textContent = `Distance: ${distanceKm} km • Duration: ${durationMin} min`;
            } else if (distanceKm) {
                distanceInfo.textContent = `Straight line distance: ${parseFloat(distanceKm).toFixed(1)} km (approximate)`;
            } else {
                distanceInfo.textContent = '';
            }
        }
        // Asegurar que el panel sea visible
        const locationInfo = document.getElementById('selected-location-info');
        if (locationInfo) locationInfo.style.display = 'block';
    } else if (data.type === 'map:cleared') {
        const selectedAddress = document.getElementById('selected-address');
        const distanceInfo = document.getElementById('distance-info');
        const addressInput = document.getElementById('jobsite-address');
        if (selectedAddress) selectedAddress.textContent = 'Click on the map to select a delivery location';
        if (distanceInfo) distanceInfo.textContent = '';
        if (addressInput) addressInput.value = '';
    } else if (data.type === 'map:ready') {
        // El iframe está listo, enviar ping para sincronizar
        const iframe = document.getElementById('delivery-map-iframe');
        if (iframe && iframe.contentWindow) {
            try {
                console.log('Received map:ready, sending map:parentPing');
                iframe.contentWindow.postMessage({ type: 'map:parentPing' }, '*');
            } catch (e) {
                console.warn('No se pudo enviar map:parentPing tras map:ready:', e);
            }
        }
    } else if (data.type === 'map:pong') {
        // Confirmación del handshake
        try { console.log('Handshake OK (map:pong)'); } catch (e) {}
    }
});

// Form validation
function validateForm() {
    const startDate = document.getElementById('start-date');
    const endDate = document.getElementById('end-date');
    const jobsiteAddress = document.getElementById('jobsite-address');
    const selfPickup = document.getElementById('self-pickup');
    const noAddress = document.getElementById('no-address');
    const selectedAddressEl = document.getElementById('selected-address');

    // Copiar dirección del panel si el input está vacío
    if (jobsiteAddress && selectedAddressEl && !jobsiteAddress.value.trim()) {
        const txt = selectedAddressEl.textContent || '';
        if (txt && txt !== 'Click on the map to select a delivery location') {
            jobsiteAddress.value = txt;
        }
    }

    // Autocompletar fechas si faltan: hoy y mañana
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

    // Solo exigir dirección si no es self-pickup ni no-address
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
    
    const formData = {
        startDate: document.getElementById('start-date').value,
        endDate: document.getElementById('end-date').value,
        jobsiteAddress: document.getElementById('jobsite-address').value,
        pickupOption: pickupOption,
        selfPickupChecked: document.getElementById('self-pickup').checked,
        noAddressChecked: document.getElementById('no-address').checked
    };
    
    localStorage.setItem('reborn-rentals-directions', JSON.stringify(formData));
    console.log('Directions data saved:', formData);
}
</script>
@endpush
@endsection
