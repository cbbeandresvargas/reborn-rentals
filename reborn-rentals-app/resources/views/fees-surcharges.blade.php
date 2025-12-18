@extends('layouts.app')

@section('title', 'Fees and Surcharges - Reborn Rentals')

@section('content')
    <main class="max-w-5xl mx-auto px-4 md:px-8 py-12">
        <h1 class="text-4xl font-bold mb-4">Fees and Surcharges</h1>
        <p class="mb-8"><strong>[Updated April 23, 2025]</strong></p>

        <h2 class="text-2xl font-bold mt-8 mb-4">Equipment Transportation Charges</h2>
        <ul class="list-disc list-inside mb-6 space-y-2 text-gray-700">
                <li>"Delivery" Fee (Standard Rate.)</li>
                <li>"Pick–Up" Fee (Standard Rate.)</li>
                <li>"Rough Terrain" Charge</li>
                <li>"Delivery" Fee (Long Haul Rate.)</li>
                <li>"Pick–Up" Fee (Long Haul Rate.)</li>
            </ul>

        <h2 class="text-2xl font-bold mt-8 mb-4">General Charges</h2>
        <ul class="list-disc list-inside mb-6 space-y-2 text-gray-700">
                <li>"Dry Run" Fee</li>
                <li>"Waiting" Fee</li>
                <li>"On Hold" Fee</li>
                <li>"Delivery" Fee (Standard Rate.)</li>
                <li>"Pick–Up" Fee (Standard Rate.)</li>
            </ul>

        <h2 class="text-2xl font-bold mt-8 mb-4">Cancellation Fees & Policy</h2>
        <p class="mb-6 text-gray-700 leading-relaxed">Please refer to our <a href="{{ route('terms') }}" class="text-[#CE9704] hover:underline">Terms & Conditions</a> for detailed information about cancellation policies.</p>
</main>
@endsection

