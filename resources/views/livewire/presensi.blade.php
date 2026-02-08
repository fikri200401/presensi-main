<div>
    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
    
    <div class="container mx-auto max-w-sm">
         <div class="bg-white p-6 rounded-lg mt-3 shadow-lg">
             <div class="grid grid-cols-1 gap-6 mb-6">
                 <div>
                     <h2 class="text-2xl font-bold mb-2">Informasi Pegawai</h2>
                     <div class="bg-gray-100 p-4 rounded-lg">
                         <p><strong>Nama Pegawai : </strong> {{Auth::user()->name}}</p>
                         <p><strong>Kantor : </strong>{{$schedule->office->name}}</p>
                         <p><strong>Shift : </strong>{{$schedule->shift->name}} ({{$schedule->shift->start_time}} - {{$schedule->shift->end_time}}) wib</p>
                         @if($schedule->is_wfa)
                             <p class="text-green-500"><strong>Status : </strong>WFA</p>
                         @else
                             <p><strong>Status : </strong>WFO</p>
                         @endif
                     </div>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                         <div class="bg-gray-100 p-4 rounded-lg">
                             <h4 class="text-l font-bold mb-2">Waktu Datang</h4>
                             <p><strong>{{$attendance ? $attendance->start_time : '-'}}</p>
                         </div>
                         <div class="bg-gray-100 p-4 rounded-lg">
                             <h4 class="text-l font-bold mb-2">Waktu Pulang</h4>
                             <p><strong>{{$attendance ? $attendance->end_time : '-'}}</p>
                         </div>
                     </div>
                 </div>
 
                 <div>
                     <h2 class="text-2xl font-bold mb-2">Presensi</h2>
                     <div id="map" class="mb-4 rounded-lg border border-gray-300" wire:ignore></div>
                     
                     <!-- GPS Instructions Info Box -->
                     <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                         <div class="flex items-start">
                             <div class="flex-shrink-0">
                                 <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                 </svg>
                             </div>
                             <div class="ml-3">
                                 <p class="text-sm font-semibold text-blue-800">How to set location:</p>
                                 <ol class="mt-2 text-sm text-blue-700 list-decimal list-inside space-y-1">
                                     <li>Click <strong>"📍 Tag Location"</strong> button below</li>
                                     <li>Allow GPS/location permission when browser asks</li>
                                     <li>Wait for your location to be detected on the map</li>
                                     <li>Click <strong>"✅ Submit Presensi"</strong> if you're in office radius</li>
                                 </ol>
                             </div>
                         </div>
                     </div>

                     <!-- Dynamic Notification Box -->
                     <div id="notification" class="mb-4 hidden">
                         <!-- Notifications will be inserted here by JavaScript -->
                     </div>
                     
                     @if (session()->has('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Sukses!</strong>
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                     
                     @if (session()->has('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Error!</strong>
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                     
                     <form class="row g-3 mt-4" wire:submit="store" enctype="multipart/form-data">
                         <button type="button" onclick="tagLocation()" class="w-full px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition font-semibold flex items-center justify-center gap-2">
                             <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                 <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                             </svg>
                             📍 Tag Location (Use My GPS)
                         </button>
                         @if($insideRadius)
                             <button type="submit" class="w-full mt-2 px-4 py-3 bg-green-500 text-white rounded-lg hover:bg-green-600 transition font-semibold flex items-center justify-center gap-2">
                                 <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                     <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                 </svg>
                                 ✅ Submit Presensi
                             </button>
                         @endif
                     </form>
                 </div>
 
             </div>
         </div>
         
 
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
         let map;
         let lat;
         let lng;
         const office = [{{$schedule->office->latitude}}, {{$schedule->office->longitude}}];
         const radius = {{$schedule->office->radius}};
         let component;
         let marker;

         // Function to show notification
         function showNotification(message, type = 'info') {
             const notificationBox = document.getElementById('notification');
             
             let bgColor, borderColor, textColor, icon;
             
             switch(type) {
                 case 'success':
                     bgColor = 'bg-green-100';
                     borderColor = 'border-green-400';
                     textColor = 'text-green-700';
                     icon = '✅';
                     break;
                 case 'error':
                     bgColor = 'bg-red-100';
                     borderColor = 'border-red-400';
                     textColor = 'text-red-700';
                     icon = '❌';
                     break;
                 case 'warning':
                     bgColor = 'bg-yellow-100';
                     borderColor = 'border-yellow-400';
                     textColor = 'text-yellow-700';
                     icon = '⚠️';
                     break;
                 default:
                     bgColor = 'bg-blue-100';
                     borderColor = 'border-blue-400';
                     textColor = 'text-blue-700';
                     icon = 'ℹ️';
             }
             
             notificationBox.innerHTML = `
                 <div class="${bgColor} border ${borderColor} ${textColor} px-4 py-3 rounded relative animate-fade-in" role="alert">
                     <span class="block sm:inline">${icon} ${message}</span>
                     <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                         <svg class="fill-current h-6 w-6 ${textColor}" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                             <title>Close</title>
                             <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                         </svg>
                     </button>
                 </div>
             `;
             notificationBox.classList.remove('hidden');
             
             // Auto hide after 5 seconds
             setTimeout(() => {
                 notificationBox.classList.add('hidden');
             }, 5000);
         }

         document.addEventListener('livewire:initialized', function() {
             component = @this;
             map = L.map('map').setView([{{$schedule->office->latitude}}, {{$schedule->office->longitude}}], 15);
             L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
 
             const circle = L.circle(office, {
                 color: 'red',
                 fillColor: '#f03',
                 fillOpacity: 0.5,
                 radius: radius
             }).addTo(map);
         })
         
 
         function tagLocation() {
             if (navigator.geolocation) {
                 showNotification('Mengambil lokasi GPS Anda...', 'info');
                 
                 navigator.geolocation.getCurrentPosition(
                     function(position) {
                         lat = position.coords.latitude;
                         lng = position.coords.longitude;
                         
                         if (marker) {
                             map.removeLayer(marker);
                         }
 
                         marker = L.marker([lat, lng]).addTo(map);
                         map.setView([lat, lng], 15);
 
                         if (isWithinRadius(lat, lng, office, radius)) {
                             component.set('insideRadius', true);
                             component.set('latitude', lat);
                             component.set('longitude', lng);
                             showNotification('Lokasi terdeteksi! Anda berada dalam radius kantor. Silakan klik Submit Presensi.', 'success');
                         } else {
                             showNotification('Lokasi terdeteksi, tetapi Anda berada di luar radius kantor.', 'warning');
                         }
                     },
                     function(error) {
                         let errorMessage = '';
                         switch(error.code) {
                             case error.PERMISSION_DENIED:
                                 errorMessage = 'GPS Permission Ditolak! Silakan izinkan akses lokasi di browser Anda. Klik ikon gembok di address bar, kemudian izinkan lokasi.';
                                 break;
                             case error.POSITION_UNAVAILABLE:
                                 errorMessage = 'Informasi lokasi tidak tersedia. Silakan periksa pengaturan GPS Anda.';
                                 break;
                             case error.TIMEOUT:
                                 errorMessage = 'Waktu permintaan lokasi habis. Silakan coba lagi.';
                                 break;
                             default:
                                 errorMessage = 'Terjadi kesalahan yang tidak diketahui. Silakan coba lagi.';
                         }
                         showNotification(errorMessage, 'error');
                         console.error('Geolocation error:', error);
                     },
                     {
                         enableHighAccuracy: true,
                         timeout: 10000,
                         maximumAge: 0
                     }
                 );
             } else {
                 showNotification('Geolocation tidak didukung oleh browser Anda. Gunakan browser modern seperti Chrome, Firefox, atau Safari.', 'error');
             }
         }
 
         function isWithinRadius(lat, lng, center, radius) {
             const is_wfa = "{{$schedule->is_wfa}}"
             if (is_wfa) {
                 return true;
             } else {
                 let distance = map.distance([lat, lng], center);
                 return distance <= radius;
             }
             
         }
 
 
 
     </script>
 
 </div>
 