@extends('layouts.app')

@section('title', 'Fees and Surcharges - Reborn Rentals')

@section('content')
<main class="max-w-7xl mx-auto px-4 md:px-8 py-12 bg-gray-50">
    <div class="bg-white rounded-lg shadow p-8 md:p-12">
        <h1 class="text-4xl font-bold mb-2">Fees and Charges</h1>
        <p class="text-gray-500 mb-8">[Updated April 23, 2025]</p>

        <section class="mb-8">
            <h3 class="text-xl font-bold mb-4">Equipment Transportation Charges:</h3>
            <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>"Delivery" Fee (Standard Rate.)</li>
                <li>"Pick–Up" Fee (Standard Rate.)</li>
                <li>"Rough Terrain" Charge</li>
                <li>"Delivery" Fee (Long Haul Rate.)</li>
                <li>"Pick–Up" Fee (Long Haul Rate.)</li>
            </ul>
        </section>

        <section class="mb-8">
            <h3 class="text-xl font-bold mb-4">General Charges:</h3>
            <ul class="list-disc list-inside space-y-2 text-gray-700">
                <li>"Dry Run" Fee</li>
                <li>"Waiting" Fee</li>
                <li>"On Hold" Fee</li>
                <li>"Delivery" Fee (Standard Rate.)</li>
                <li>"Pick–Up" Fee (Standard Rate.)</li>
            </ul>
        </section>

        <section>
            <h3 class="text-xl font-bold mb-4">Cancellation Fees & Policy</h3>
            <p class="text-gray-700">Please refer to our <a href="{{ route('terms') }}" class="text-[#CE9704] hover:underline">Terms & Conditions</a> for detailed information about cancellation policies.</p>
        </section>
    </div>
</main>
@endsection

