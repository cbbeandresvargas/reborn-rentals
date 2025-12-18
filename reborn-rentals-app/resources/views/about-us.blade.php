@extends('layouts.app')

@section('title', 'About Us - Reborn Rentals')

@section('content')
<!-- HERO -->
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 md:px-8 py-10 md:py-14">
        <div class="grid grid-cols-1 md:grid-cols-2 items-center gap-8 md:gap-12">
            <!-- Imagen -->
            <div>
                <img
                    src="{{ asset('Machine.png') }}"
                    alt="RebornRental equipment"
                    class="w-full h-auto rounded-xl object-cover"
                    loading="lazy"
                    decoding="async"
                />
            </div>

            <!-- Texto grande -->
            <div class="relative">
                <h2 class="text-3xl md:text-5xl font-extrabold leading-tight tracking-tight text-gray-900">
                    SIMPLE, EFFICIENT, AND TIMELY —
                    YOU BARELY HAVE TO THINK ABOUT IT.
                </h2>
            </div>
        </div>
    </div>
</section>

<!-- DESCRIPCIÓN -->
<section class="bg-white">
    <div class="mx-auto max-w-5xl px-4 md:px-8 pb-10 md:pb-14">
        <h3 class="text-xl md:text-2xl font-semibold text-gray-900 mb-4">
            RebornRental is a premier construction equipment rental company based out of Denver, Colorado.
        </h3>

        <p class="text-gray-900 leading-relaxed mb-4">
            RebornRental founded in 2025, is Rental arm of Reborn Construction™, part of Global Reborn Group with operations
            across the United States, Bolivia, Argentina, United Kingdom, Switzerland, Italy, Spain, Sweden, Ukraine,
            Indonesia and the UAE.
        </p>

        <p class="text-gray-900 leading-relaxed">
            We understand our customers well, and understand their need to navigate a complex construction environment.
            This is why we prioritize to provide our services in such a simple manner, so that you can rest easy that this part is taken care of.
        </p>
    </div>
</section>

<!-- FIND US + MAPA -->
<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 md:px-8 pb-10 md:pb-14">
        <h4 class="text-lg md:text-xl font-semibold text-gray-900 mb-4">Find Us</h4>

        <!-- Google Maps -->
        <div class="rounded-xl overflow-hidden border border-gray-200 mb-8">
            <iframe 
                src="https://www.google.com/maps?q=39.726372,-105.055759&hl=es&z=14&output=embed&markers=39.726372,-105.055759" 
                width="100%" 
                height="420" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>

        <!-- Address, Hours & Social Media -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Address -->
            <div>
                <h5 class="text-base md:text-lg font-semibold text-gray-900 mb-2">Our Address</h5>
                <div class="space-y-1 text-gray-700">
                    <p>39°43'34.9"N 105°03'20.7"W</p>
                    <p>Denver, CO</p>
                    <p>United States</p>
                </div>
            </div>

            <!-- Hours -->
            <div>
                <h5 class="text-base md:text-lg font-semibold text-gray-900 mb-2">Hours</h5>
                <ul class="text-gray-700 space-y-1">
                    <li><span class="font-medium">Mon–Fri:</span> 08:00 – 18:00</li>
                    <li><span class="font-medium">Sat:</span> 09:00 – 14:00</li>
                    <li><span class="font-medium">Sun:</span> Closed</li>
                </ul>
            </div>

            <!-- Social Media -->
            <div class="w-full">
                <h5 class="text-base md:text-lg font-semibold text-gray-900 mb-3">Follow Us</h5>
                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Facebook -->
                    <a href="https://www.facebook.com/rebornrentals/" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-black border-2 border-[#CE9704] rounded-full flex items-center justify-center hover:bg-[#CE9704] hover:border-white transition-all duration-300 transform hover:scale-110 hover:shadow-lg hover:shadow-[#CE9704]/50 flex-shrink-0" aria-label="Facebook">
                        <span class="text-white font-bold text-lg">f</span>
                    </a>
                    <!-- Instagram -->
                    <a href="https://www.instagram.com/reborn_rentals/" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-black border-2 border-[#CE9704] rounded-full flex items-center justify-center hover:bg-[#CE9704] hover:border-white transition-all duration-300 transform hover:scale-110 hover:shadow-lg hover:shadow-[#CE9704]/50 flex-shrink-0" aria-label="Instagram">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/company/reborn-rental/" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-black border-2 border-[#CE9704] rounded-full flex items-center justify-center hover:bg-[#CE9704] hover:border-white transition-all duration-300 transform hover:scale-110 hover:shadow-lg hover:shadow-[#CE9704]/50 flex-shrink-0" aria-label="LinkedIn">
                        <span class="text-white font-bold text-sm">in</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- OSHA Easter Egg -->
<div id="osha-easter-egg" class="fixed bottom-6 right-6 z-50 hidden">
    <div class="relative">
        <!-- OSHA SVG - Clickable -->
        <div 
            onclick="showOshaDialog()"
            class="w-24 h-24 sm:w-32 sm:h-32 cursor-pointer hover:scale-110 transition-all duration-300"
            aria-label="Click to meet OSHA"
        >
            <img 
                src="{{ asset('osha/OSHA REBORN RENTAL-13.svg') }}" 
                alt="OSHA the goat" 
                class="w-full h-full object-contain drop-shadow-2xl"
            />
        </div>
        
        <!-- OSHA Dialog -->
        <div 
            id="osha-dialog" 
            class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden transition-opacity duration-300"
            onclick="hideOshaDialog(event)"
        >
            <div 
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 sm:p-8 relative transform transition-all duration-500 scale-0"
                id="osha-dialog-content"
                onclick="event.stopPropagation()"
            >
                <!-- Close Button -->
                <button 
                    onclick="hideOshaDialog()" 
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors z-10"
                    aria-label="Close"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                
                <!-- Dialog Content -->
                <div class="flex flex-col items-center text-center">
                    <div class="w-32 h-32 mb-4">
                        <img 
                            src="{{ asset('osha/OSHA REBORN RENTAL-13.svg') }}" 
                            alt="OSHA the goat" 
                            class="w-full h-full object-contain"
                        />
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-3">Hi! I'm OSHA 🐐</h3>
                    <p class="text-gray-700 text-base sm:text-lg mb-4 leading-relaxed">
                        I'm Reborn Rentals emotional support manager. You can learn more from me at:
                    </p>
                    <a 
                        href="https://www.instagram.com/osha.reborn/" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-[#CE9704] to-[#B8860B] text-white px-6 py-3 rounded-lg hover:from-[#B8860B] hover:to-[#CE9704] transition-all shadow-md hover:shadow-lg transform hover:scale-105 font-semibold"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                        Follow me on Instagram
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show OSHA trigger after scroll
let oshaShown = false;
let scrollTimeout;

window.addEventListener('scroll', function() {
    const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    
    // Show trigger after 30% scroll
    if (scrollPercent > 30 && !oshaShown) {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            const trigger = document.getElementById('osha-easter-egg');
            if (trigger) {
                trigger.classList.remove('hidden');
                trigger.classList.add('animate-fade-in');
                oshaShown = true;
            }
        }, 500);
    }
});

// Show OSHA dialog
function showOshaDialog() {
    const dialog = document.getElementById('osha-dialog');
    const content = document.getElementById('osha-dialog-content');
    
    if (dialog && content) {
        dialog.classList.remove('hidden');
        dialog.style.opacity = '0';
        
        // Animate dialog fade in
        setTimeout(() => {
            dialog.style.opacity = '1';
        }, 10);
        
        // Animate content scale
        setTimeout(() => {
            content.classList.remove('scale-0');
            content.classList.add('scale-100');
        }, 50);
    }
}

// Hide OSHA dialog
function hideOshaDialog(event) {
    if (event) {
        event.stopPropagation();
    }
    
    const dialog = document.getElementById('osha-dialog');
    const content = document.getElementById('osha-dialog-content');
    
    if (dialog && content) {
        content.classList.remove('scale-100');
        content.classList.add('scale-0');
        
        setTimeout(() => {
            dialog.style.opacity = '0';
            setTimeout(() => {
                dialog.classList.add('hidden');
            }, 300);
        }, 200);
    }
}

// Also show after 10 seconds if user hasn't scrolled much
setTimeout(() => {
    if (!oshaShown && window.scrollY < 200) {
        const trigger = document.getElementById('osha-easter-egg');
        if (trigger) {
            trigger.classList.remove('hidden');
            trigger.classList.add('animate-fade-in');
            oshaShown = true;
        }
    }
}, 10000);
</script>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}

#osha-easter-egg img {
    filter: drop-shadow(0 10px 20px rgba(0, 0, 0, 0.3));
}

#osha-easter-egg:hover img {
    filter: drop-shadow(0 15px 30px rgba(206, 151, 4, 0.5));
}
</style>
@endsection

