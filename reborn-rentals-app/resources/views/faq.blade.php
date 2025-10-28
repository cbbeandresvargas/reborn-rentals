@extends('layouts.app')

@section('title', 'FAQ - Reborn Rentals')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4 md:px-8">
        <!-- Header Section -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">FAQ</h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Ask everything you need to know about our products and service.
            </p>
        </div>

        <!-- FAQ Content -->
        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-inset" onclick="toggleFAQ('faq1')">
                    <h3 class="text-lg font-semibold text-gray-800">What is your cancellation policy?</h3>
                    <svg id="faq1-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq1" class="hidden px-6 pb-4 transition-all duration-300 ease-in-out overflow-hidden">
                    <p class="text-gray-600">Your may can your reservation for free up to 5 (five) business days before the reservation date, later cancellations will have a penalty 50% of your reservation, excluding delivery charges. you can learn more at <a href="{{ route('fees') }}" class="text-[#CE9704] hover:underline">Fees and Surcharges</a></p>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-inset" onclick="toggleFAQ('faq2')">
                    <h3 class="text-lg font-semibold text-gray-800">How far do you deliver from Denver?</h3>
                    <svg id="faq2-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq2" class="hidden px-6 pb-4 transition-all duration-300 ease-in-out overflow-hidden">
                    <p class="text-gray-600">Deliveries and Pickups are free of charge inside the metro area of Denver, beyond it $500 charge applies for each trip. Deliveries beyond 200 miles are charged on a seperate long-haul fare please contact <a href="mailto:sales@rebornrental.com" class="text-[#CE9704] hover:underline">sales@rebornrental.com</a> for more information. Please ensure you have your foreman available for drop-off and pick-up, dry run fees may apply, for more information please see <a href="{{ route('fees') }}" class="text-[#CE9704] hover:underline">Fees and Surcharges</a></p>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-inset" onclick="toggleFAQ('faq3')">
                    <h3 class="text-lg font-semibold text-gray-800">What if I need to self-pick-up / self-drop-off on a weekend or outside working hours?</h3>
                    <svg id="faq3-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq3" class="hidden px-6 pb-4 transition-all duration-300 ease-in-out overflow-hidden">
                    <p class="text-gray-600">Please contact support in advance for a prior notice, a $35 fee applies for pickups and deliveries outside of working hours.</p>
                </div>
            </div>

            <!-- FAQ Item 4 -->
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-inset" onclick="toggleFAQ('faq4')">
                    <h3 class="text-lg font-semibold text-gray-800">How can I track my delivery?</h3>
                    <svg id="faq4-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq4" class="hidden px-6 pb-4 transition-all duration-300 ease-in-out overflow-hidden">
                    <p class="text-gray-600">Please contact support with your Order ID # for a live-location update on your delivery driver.</p>
                </div>
            </div>

            <!-- FAQ Item 5 -->
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-inset" onclick="toggleFAQ('faq5')">
                    <h3 class="text-lg font-semibold text-gray-800">What payment methods do you accept?</h3>
                    <svg id="faq5-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq5" class="hidden px-6 pb-4 transition-all duration-300 ease-in-out overflow-hidden">
                    <p class="text-gray-600">We accept all major credit cards (Visa, Mastercard, American Express, Discover), Klarna, Apple Pay, and Google Pay. We also accept direct ACH wire transfers. All transactions are secure and encrypted. Note: Cash is not accepted.</p>
                </div>
            </div>

            <!-- FAQ Item 6 -->
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-inset" onclick="toggleFAQ('faq6')">
                    <h3 class="text-lg font-semibold text-gray-800">When are your working hours?</h3>
                    <svg id="faq6-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq6" class="hidden px-6 pb-4 transition-all duration-300 ease-in-out overflow-hidden">
                    <p class="text-gray-600">Monday to Friday between 9am and 6pm Mountain time (GMT-6).</p>
                </div>
            </div>

            <!-- FAQ Item 7 -->
            <div class="bg-white rounded-lg shadow-md">
                <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-inset" onclick="toggleFAQ('faq7')">
                    <h3 class="text-lg font-semibold text-gray-800">Do you offer discounts for bulk orders?</h3>
                    <svg id="faq7-icon" class="w-5 h-5 text-gray-500 transform transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div id="faq7" class="hidden px-6 pb-4 transition-all duration-300 ease-in-out overflow-hidden">
                    <p class="text-gray-600">Yes, we offer volume discounts for rental orders over 20 units or rentals for periods longer than 6 (six) months. Please contact our sales team at <a href="mailto:sales@rebornrental.com" class="text-[#CE9704] hover:underline">sales@rebornrental.com</a> with your specific requirements to receive a customized quote.</p>
                </div>
            </div>
        </div>

        <!-- Newsletter Popup -->
        <div class="mt-12 bg-[#4B4441] rounded-lg p-8 text-center">
            <h2 class="text-2xl font-bold text-white mb-4">Stay Updated</h2>
            <p class="text-gray-300 mb-6">Subscribe to our newsletter for the latest equipment updates and special offers.</p>
            <form class="max-w-md mx-auto" id="newsletter-form">
                <div class="flex gap-3">
                    <input 
                        type="email" 
                        placeholder="Enter your email address" 
                        class="flex-1 px-4 py-3 rounded-lg border bg-white border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:border-transparent text-gray-900"
                        required
                    />
                    <button 
                        type="submit" 
                        class="bg-[#CE9704] text-white px-6 py-3 rounded-lg font-semibold hover:bg-[#B8860B] transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-[#CE9704] focus:ring-offset-2 focus:ring-offset-[#4B4441]"
                    >
                        Subscribe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleFAQ(faqId) {
    const faq = document.getElementById(faqId);
    const icon = document.getElementById(faqId + '-icon');
    
    if (!faq || !icon) return;
    
    if (faq.classList.contains('hidden')) {
        faq.classList.remove('hidden');
        faq.style.maxHeight = '0px';
        faq.style.opacity = '0';
        faq.offsetHeight;
        faq.style.maxHeight = faq.scrollHeight + 'px';
        faq.style.opacity = '1';
        icon.style.transform = 'rotate(180deg)';
        setTimeout(() => {
            faq.style.maxHeight = 'none';
        }, 300);
    } else {
        faq.style.maxHeight = faq.scrollHeight + 'px';
        faq.style.opacity = '1';
        faq.offsetHeight;
        faq.style.maxHeight = '0px';
        faq.style.opacity = '0';
        icon.style.transform = 'rotate(0deg)';
        setTimeout(() => {
            faq.classList.add('hidden');
            faq.style.maxHeight = '';
            faq.style.opacity = '';
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                alert('Successfully subscribed! Thank you for joining our newsletter.');
                this.reset();
            }
        });
    }
});
</script>
@endsection

