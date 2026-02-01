@extends('layouts.app')

@section('title', 'Edit Office')
@section('page-title', 'Edit Office')

@section('content')
<div class="max-w-4xl">
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <form method="POST" action="{{ route('office.update', $office) }}" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Office Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $office->name) }}" required 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       placeholder="e.g., Jakarta Head Office">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                <textarea id="address" name="address" rows="3" required 
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                          placeholder="Full office address...">{{ old('address', $office->address) }}</textarea>
                @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Map Section -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">📍 Office Location</label>
                <div class="bg-blue-50 border border-blue-200 rounded-md p-3 mb-3">
                    <p class="text-sm text-blue-800">
                        <strong>How to update location:</strong><br>
                        1️⃣ <strong>Search location</strong> by typing address/place name<br>
                        2️⃣ Click <strong>"Use My GPS"</strong> to auto-detect your current location<br>
                        3️⃣ Or <strong>click anywhere on the map</strong> to set new office location<br>
                        4️⃣ Or <strong>drag the marker</strong> to reposition
                    </p>
                </div>
                
                <!-- Search Box -->
                <div class="mb-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" id="searchLocation" 
                               placeholder="🔍 Search location (e.g., 'Jakarta City Hall', 'Sudirman Street')" 
                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div id="searchResults" class="hidden mt-2 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto z-10"></div>
                </div>
                
                <div class="mb-3 flex gap-2">
                    <button type="button" onclick="useMyGPS()" 
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-green-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Use My GPS
                    </button>
                    <button type="button" onclick="centerOnMarker()" 
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md shadow-sm text-sm font-medium hover:bg-indigo-700 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Center Map
                    </button>
                </div>
                
                <div id="map" class="h-96 rounded-lg border-2 border-gray-300 shadow-sm mb-3"></div>
                
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label for="latitude" class="block text-xs font-medium text-gray-600 mb-1">Latitude</label>
                        <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude', $office->latitude) }}" required readonly
                               class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('latitude')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-xs font-medium text-gray-600 mb-1">Longitude</label>
                        <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude', $office->longitude) }}" required readonly
                               class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        @error('longitude')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="radius" class="block text-xs font-medium text-gray-600 mb-1">Radius (meters)</label>
                        <input type="number" step="1" id="radius" name="radius" value="{{ old('radius', $office->radius) }}" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                               onchange="updateCircleRadius()">
                        @error('radius')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('office.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Update Office
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    let map;
    let marker;
    let circle;
    let searchTimeout;
    
    // Get current office location
    const currentLat = {{ old('latitude', $office->latitude) }};
    const currentLng = {{ old('longitude', $office->longitude) }};
    const currentRadius = {{ old('radius', $office->radius) }};
    
    // Initialize map
    document.addEventListener('DOMContentLoaded', function() {
        map = L.map('map').setView([currentLat, currentLng], 17);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Add marker (draggable)
        marker = L.marker([currentLat, currentLng], {
            draggable: true
        }).addTo(map);
        
        // Add circle
        circle = L.circle([currentLat, currentLng], {
            color: 'blue',
            fillColor: '#3b82f6',
            fillOpacity: 0.2,
            radius: currentRadius
        }).addTo(map);
        
        // Update coordinates when marker is dragged
        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateLocation(position.lat, position.lng);
        });
        
        // Click on map to set location
        map.on('click', function(e) {
            updateLocation(e.latlng.lat, e.latlng.lng);
        });
        
        // Setup search functionality
        setupSearch();
    });
    
    function setupSearch() {
        const searchInput = document.getElementById('searchLocation');
        const searchResults = document.getElementById('searchResults');
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length < 3) {
                searchResults.classList.add('hidden');
                return;
            }
            
            // Show loading
            searchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">🔍 Searching...</div>';
            searchResults.classList.remove('hidden');
            
            // Debounce search
            searchTimeout = setTimeout(() => {
                searchLocation(query);
            }, 500);
        });
        
        // Click outside to close
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    }
    
    async function searchLocation(query) {
        const searchResults = document.getElementById('searchResults');
        
        try {
            // Using Nominatim (OpenStreetMap) for geocoding
            const response = await fetch(
                `https://nominatim.openstreetmap.org/search?` + 
                `format=json&q=${encodeURIComponent(query)}&` +
                `countrycodes=id&limit=5&addressdetails=1`
            );
            
            if (!response.ok) throw new Error('Search failed');
            
            const results = await response.json();
            
            if (results.length === 0) {
                searchResults.innerHTML = '<div class="p-3 text-gray-500 text-sm">❌ No results found. Try different keywords.</div>';
                return;
            }
            
            // Display results
            let html = '<div class="divide-y divide-gray-200">';
            results.forEach(result => {
                html += `
                    <div class="p-3 hover:bg-gray-50 cursor-pointer transition" 
                         onclick="selectLocation(${result.lat}, ${result.lon}, '${escapeHtml(result.display_name)}')">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">${result.display_name}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Lat: ${parseFloat(result.lat).toFixed(6)}, Lng: ${parseFloat(result.lon).toFixed(6)}</p>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            
            searchResults.innerHTML = html;
            
        } catch (error) {
            console.error('Search error:', error);
            searchResults.innerHTML = '<div class="p-3 text-red-500 text-sm">⚠️ Search service temporarily unavailable. Please try again.</div>';
        }
    }
    
    function selectLocation(lat, lng, displayName) {
        updateLocation(lat, lng);
        document.getElementById('searchResults').classList.add('hidden');
        document.getElementById('searchLocation').value = displayName;
        showNotification('✅ Location selected: ' + displayName.substring(0, 50) + '...', 'success');
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function updateLocation(lat, lng) {
        // Update marker position
        marker.setLatLng([lat, lng]);
        circle.setLatLng([lat, lng]);
        
        // Update form inputs
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        
        // Center map on new location
        map.setView([lat, lng], map.getZoom());
    }
    
    function updateCircleRadius() {
        const radius = parseInt(document.getElementById('radius').value) || 100;
        circle.setRadius(radius);
    }
    
    function centerOnMarker() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(document.getElementById('longitude').value);
        map.setView([lat, lng], 17);
        showNotification('📍 Map centered on office location', 'info');
    }
    
    function useMyGPS() {
        if (navigator.geolocation) {
            // Show loading state
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<svg class="animate-spin h-5 w-5 mr-2 inline" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Getting GPS...';
            btn.disabled = true;
            
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    updateLocation(position.coords.latitude, position.coords.longitude);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                    
                    // Show success message
                    showNotification('✅ GPS location detected successfully!', 'success');
                },
                function(error) {
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                    
                    let errorMsg = 'Unable to get GPS location. ';
                    if (error.code === 1) {
                        errorMsg += 'Please allow location permission.';
                    } else if (error.code === 2) {
                        errorMsg += 'Location unavailable.';
                    } else {
                        errorMsg += 'Request timeout.';
                    }
                    showNotification('❌ ' + errorMsg, 'error');
                }
            );
        } else {
            showNotification('❌ GPS not supported by your browser.', 'error');
        }
    }
    
    function showNotification(message, type) {
        const colors = {
            success: 'bg-green-100 border-green-400 text-green-700',
            error: 'bg-red-100 border-red-400 text-red-700',
            info: 'bg-blue-100 border-blue-400 text-blue-700'
        };
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${colors[type]} border px-4 py-3 rounded-lg shadow-lg z-50 max-w-md`;
        notification.innerHTML = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
</script>
@endpush
@endsection
