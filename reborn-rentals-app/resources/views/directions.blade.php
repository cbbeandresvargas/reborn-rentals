@extends('layouts.app')

@section('title', 'Select Jobsite & Time - Reborn Rentals')

@section('content')
<div class="max-w-6xl mx-auto px-6 mt-20 mb-20">
    <div class="bg-white rounded-lg p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Select Jobsite & Time</h1>
        
        <p class="text-gray-600 text-sm mb-8 leading-relaxed">
            Select date and place of delivery, depending on your location, delivery fees may apply, Long fares apply if jobsite is located 4 hours away or more. Orders outside of Mainland US, not accepted.
        </p>
        
        <form action="{{ route('checkout') }}" method="GET" onsubmit="saveFormData(event)">
            <!-- Date Selection -->
            <div class="mb-8">
                <div class="flex gap-4 mb-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" id="start-date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="end_date" id="end-date" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                        />
                    </div>
                </div>
                <p class="text-gray-600 text-sm leading-relaxed">
                    Choose start and end dates for your rental period. Weekend deliveries are available.
                </p>
            </div>
            
            <!-- Jobsite Address -->
            <div class="mb-8">
                <div class="flex justify-between items-start mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Insert Jobsite Address</h2>
                    <span class="text-sm text-gray-500 underline">*Cancellation Fees may apply.</span>
                </div>
                <div class="relative">
                    <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 z-10">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="jobsite_address" id="jobsite-address" required
                        placeholder="Start typing and select from suggestions..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#CE9704]"
                    />
                </div>
            </div>
            
            <!-- Pickup Options -->
            <div class="mb-8">
                <div class="space-y-4">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="self_pickup" id="self-pickup" value="1"
                            class="w-4 h-4 text-[#CE9704] border-gray-300 rounded focus:ring-[#CE9704] focus:ring-2"
                            onchange="toggleSelfPickup()"
                        />
                        <span class="ml-3 text-gray-700">Self-Pickup</span>
                    </label>
                </div>
                
                <!-- Self-Pickup Details -->
                <div id="self-pickup-details" class="mt-6 p-6 bg-gray-50 rounded-lg border border-gray-200 hidden">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Pickup Location Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Our Address</h4>
                            <div class="space-y-1 text-gray-700">
                                <p>401 Ryland St.</p>
                                <p>Ste 200 A</p>
                                <p>Reno, NV 89502</p>
                                <p>United States</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-base font-semibold text-gray-900 mb-3">Hours</h4>
                            <ul class="text-gray-700 space-y-1">
                                <li><span class="font-medium">Mon–Fri:</span> 08:00 – 18:00</li>
                                <li><span class="font-medium">Sat:</span> 09:00 – 14:00</li>
                                <li><span class="font-medium">Sun:</span> Closed</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Map -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Select Delivery Location on Map</h3>
                <div class="w-full h-96 rounded-lg overflow-hidden border border-gray-300">
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
            </div>
            
            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="bg-[#CE9704] text-white font-bold py-3 px-8 rounded-lg hover:bg-[#B8860B] transition-colors">
                    Proceed to Checkout
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleSelfPickup() {
    const checkbox = document.getElementById('self-pickup');
    const details = document.getElementById('self-pickup-details');
    if (checkbox.checked) {
        details.classList.remove('hidden');
    } else {
        details.classList.add('hidden');
    }
}

function saveFormData(event) {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const address = document.getElementById('jobsite-address').value;
    
    localStorage.setItem('reborn-rentals-directions', JSON.stringify({
        startDate,
        endDate,
        jobsiteAddress: address
    }));
}
</script>
@endsection

