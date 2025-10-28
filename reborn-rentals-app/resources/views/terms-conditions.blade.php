@extends('layouts.app')

@section('title', 'Terms & Conditions - Reborn Rentals')

@section('content')
<style>
    .terms-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 32px 16px 64px;
        font-family: system-ui, sans-serif;
        color: #1f2937;
        line-height: 1.7;
    }
    .terms-container h1 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 1rem;
        color: #111827;
    }
    .terms-container h2 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
        color: #111827;
    }
    .terms-container p {
        margin-bottom: 1rem;
        text-align: justify;
    }
    .terms-container ol {
        list-style-type: decimal;
        margin-left: 1.25rem;
    }
    .terms-container li {
        margin-bottom: 0.75rem;
    }
    .terms-container strong {
        font-weight: 600;
    }
</style>

<main class="terms-container">
    <h1>Terms &amp; Conditions</h1>

    <p>This contract is entered into at Cheyenne, Wyoming between Magenta Goldenrod LLLP, duly executed on its behalf by RBCMSWUSA LLC ("RebornRental"), located at 401 Ryland St. Ste 200 A, Reno, NV 89502, USA, and ("Customer"), whereby RebornRental rents various equipment ("Equipment") per the following terms and conditions ("Contract"):</p>

    <ol class="space-y-6">
        <li>
            <strong>Contract Term.</strong> The term of the Contract shall begin on the Effective Date and shall continue until terminated by either party ("Term") by providing written notice to the other party of such intent; termination to be effective fifteen (15) days from the date notice is received except that this Contract shall remain effective through the completion of all rentals outstanding as of the date this Contract would otherwise terminate.
        </li>
        <li>
            <strong>Equipment Description.</strong> The Equipment to be rented for any Rental under this Contract is described in the sales order(s) ("Sales Order") and invoice(s) ("Invoice") sent to Customer via email from RebornRental.
        </li>
        <li>
            <strong>Rental Rate and Period.</strong> The Customer's initial charges ("Initial Charges") for any rental of Equipment will consist of (i) a base rent ("Base Rent") estimated upon the Customer's representation of the estimated rental period ("Estimated Rental Period"); (ii) delivery and pickup charges which rates vary by Equipment.
        </li>
        <li>
            <strong>Payment Method.</strong> Unless RebornRental extends credit to Customer as specified herein, RebornRental requires payment for the Initial Charges prior to Equipment delivery.
        </li>
        <li>
            <strong>Customer Liability.</strong> Customer assumes all risk and liability for the loss of or damage to Equipment from time of delivery and until pickup.
        </li>
    </ol>

    <p class="mt-8">
        For the complete Terms & Conditions document, please contact us at <a href="mailto:Support@RebornRental.com" class="text-[#CE9704] hover:underline">Support@RebornRental.com</a>.
    </p>
</main>
@endsection

