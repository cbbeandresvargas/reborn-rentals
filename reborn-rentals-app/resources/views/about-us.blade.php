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
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3077.576787884259!2d-119.80605779999999!3d39.5240405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x809940b3fe48bd53%3A0x4e3afeee9f24c1bc!2s401%20Ryland%20St%20Suite%20200-A%2C%20Reno%2C%20NV%2089502%2C%20EE.%20UU.!5e0!3m2!1ses-419!2sbo!4v1761325010616!5m2!1ses-419!2sbo" 
                width="100%" 
                height="420" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade"
            ></iframe>
        </div>

        <!-- Address & Hours -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Address -->
            <div>
                <h5 class="text-base md:text-lg font-semibold text-gray-900 mb-2">Our Address</h5>
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
                <h5 class="text-base md:text-lg font-semibold text-gray-900 mb-2">Hours</h5>
                <ul class="text-gray-700 space-y-1">
                    <li><span class="font-medium">Mon–Fri:</span> 08:00 – 18:00</li>
                    <li><span class="font-medium">Sat:</span> 09:00 – 14:00</li>
                    <li><span class="font-medium">Sun:</span> Closed</li>
                </ul>
            </div>
        </div>
    </div>
</section>
@endsection

