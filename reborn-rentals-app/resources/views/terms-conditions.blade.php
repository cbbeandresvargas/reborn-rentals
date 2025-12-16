@extends('layouts.app')

@section('title', 'Terms & Conditions - Reborn Rentals')

@push('styles')
<style>
    .terms-container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 32px 16px 64px;
            font-family: system-ui, -apple-system, sans-serif;
        color: #1f2937;
            line-height: 1.75;
    }

        /* Título principal */
    .terms-container h1 {
        font-size: 2rem;
        font-weight: 700;
            margin-bottom: 1.5rem;
            color: #111827;
        }

        /* Subtítulos de sección */
        .terms-container h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-top: 2rem;
        margin-bottom: 1rem;
        color: #111827;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
    }

        /* Subtítulos de subsección */
        .terms-container h3 {
        font-size: 1.125rem;
        font-weight: 600;
        margin-top: 1.5rem;
            margin-bottom: 0.75rem;
            color: #374151;
    }

        /* Párrafos */
    .terms-container p {
            margin-bottom: 1.25rem;
        text-align: justify;
            color: #1f2937;
        }

        /* Párrafos con más espacio */
        .terms-container p.large {
            margin-bottom: 1.5rem;
            font-size: 1.0625rem;
        }

        /* Listas ordenadas */
    .terms-container ol {
        list-style-type: decimal;
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
            padding-left: 0.5rem;
        }

        /* Listas no ordenadas */
        .terms-container ul {
            list-style-type: disc;
            margin-left: 1.5rem;
            margin-bottom: 1.5rem;
            padding-left: 0.5rem;
        }

        /* Items de lista */
    .terms-container li {
            margin-bottom: 1rem;
            line-height: 1.7;
        }

        /* Items de lista con más espacio */
        .terms-container li.spaced {
            margin-bottom: 1.5rem;
        }

        /* Texto en negrita */
    .terms-container strong {
        font-weight: 600;
            color: #111827;
        }

        /* Enlaces */
        .terms-container a {
            color: #CE9704;
            text-decoration: underline;
            transition: color 0.2s;
        }

        .terms-container a:hover {
            color: #B8860B;
        }

        /* Secciones */
        .terms-section {
            margin-bottom: 2.5rem;
        }

        /* Espaciado adicional */
        .terms-container .mt-large {
            margin-top: 2rem;
        }

        .terms-container .mb-large {
            margin-bottom: 2rem;
        }

        /* Responsive - Tablet */
        @media (max-width: 1024px) {
            .terms-container {
                max-width: 95%;
                padding: 28px 20px 56px;
            }

            .terms-container h1 {
                font-size: 1.875rem;
            }

            .terms-container h2 {
                font-size: 1.1875rem;
            }

            .terms-container h3 {
                font-size: 1.0625rem;
            }

            .terms-container ol,
            .terms-container ul {
                margin-left: 1.25rem;
                padding-left: 0.75rem;
            }
        }

        /* Responsive - Mobile */
        @media (max-width: 768px) {
            .terms-container {
                padding: 20px 12px 40px;
                line-height: 1.6;
            }

            .terms-container h1 {
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
            }

            .terms-container h2 {
                font-size: 1.125rem;
                margin-top: 1.5rem;
                padding-top: 0.75rem;
            }

            .terms-container h3 {
                font-size: 1rem;
                margin-top: 1.25rem;
            }

            .terms-container p {
                text-align: left;
                margin-bottom: 1rem;
                font-size: 0.9375rem;
            }

            .terms-container p.large {
                font-size: 1rem;
                margin-bottom: 1.25rem;
            }

            .terms-container ol,
            .terms-container ul {
                margin-left: 1rem;
                padding-left: 0.5rem;
                margin-bottom: 1.25rem;
            }

            .terms-container li {
                margin-bottom: 0.875rem;
                font-size: 0.9375rem;
            }

            .terms-container li.spaced {
                margin-bottom: 1.25rem;
            }

            .terms-container strong {
                font-size: 0.9375rem;
            }

            .terms-section {
                margin-bottom: 2rem;
            }
        }

        /* Responsive - Small Mobile */
        @media (max-width: 480px) {
            .terms-container {
                padding: 16px 10px 32px;
            }

            .terms-container h1 {
                font-size: 1.375rem;
            }

            .terms-container h2 {
                font-size: 1.0625rem;
            }

            .terms-container h3 {
                font-size: 0.9375rem;
            }

            .terms-container p,
            .terms-container li {
                font-size: 0.875rem;
            }

            .terms-container ol,
            .terms-container ul {
                margin-left: 0.75rem;
                padding-left: 0.5rem;
            }
    }
</style>
@endpush

@section('content')
<main class="terms-container">
    <h1>Terms &amp; Conditions</h1>

        <!-- Introducción -->
        <p class="large">
            This contract is entered into at Cheyenne, Wyoming between Magenta Goldenrod LLLP, duly executed on its behalf
            by RBCMSWUSA LLC ("RebornRental"), located at 401 Ryland St. Ste 200 A, Reno, NV 89502, USA, and ("Customer"),
            whereby RebornRental rents various equipment ("Equipment") per the following terms and conditions ("Contract"):
        </p>

        <!-- Sección: Términos Principales -->
        <div class="terms-section">

            <ol>
                <li class="spaced">
                    <strong>Contract Term.</strong> The term of the Contract shall begin on the Effective Date and shall
                    continue until terminated by either party (“Term”) by providing written notice to the other party of
                    such intent; termination to be effective fifteen (15) days from the date notice is received except that
                    this Contract shall remain effective through the completion of all rentals outstanding as of the date
                    this Contract would otherwise terminate. During the Term, RebornRental, on a non-exclusive basis, may
                    rent Equipment to Customer on multiple occasions which rentals will all be subject to the terms and
                    conditions of this Contract regardless of the date of each (“Rental”). In the event of a conflict
                    between any Rental, Customer’s purchase order or any other Customer document and this Contract, the
                    terms of this Contract shall govern.
                </li>

                <li class="spaced">
                    <strong>Equipment Description.</strong> The Equipment to be rented for any Rental under this Contract is
                    described in the sales order(s) (“Sales Order”) and invoice(s) (“Invoice”) sent to Customer via email
                    from RebornRental, which, as they arise, are to be incorporated herein by this reference and made a part
                    hereof. As one of the purposes of this Contract is to promote the facility of the parties doing multiple
                    rental transactions within this Agreement, the parties hereby mutually acknowledge and agree that Sales
                    Order and Invoice prepared and submitted pursuant to this Contract do not require separate and/or
                    additional signatures and that the signature and/or acknowledgement of the Customer appearing hereon
                    shall suffice with respect to satisfying the obligations and/or needs of the parties to adhere to
                    written formalities. Customer acknowledges that RebornRental is not the manufacturer, designer, or owner
                    of any of the Equipment.

                </li>

                <li class="spaced">
                    <strong>Rental Rate and Period.</strong> The Customer’s initial charges (“Initial Charges”) for any
                    rental of Equipment will consist of (i) a base rent (“Base Rent”) estimated upon the Customer’s
                    representation of the estimated rental period (“Estimated Rental Period”); (ii) delivery and pickup
                    charges which rates vary by area and Equipment. Delivery beyond two-hundred and twenty (220) miles from
                    closest delivery hub could incur additional charges, which will be determined and agreed upon at time of
                    request and noted on the Invoice; (iii) applicable state and local sales tax; (iv) a fifteen (15)
                    percent rental protection plan fee (“RPP”) if applicable, as specified in Paragraph 8; and (v) an up to
                    three (3) percent processing fee to cover any indirect environmental related expenses, any specialty
                    rental fees and
                    surcharges, and any other costs that may be incurred. The fee is not a tax or governmentally mandated
                    charge. Rather, it is a fee that RebornRental collects at its sole discretion, all of which will be
                    stated on each Invoice. The Base Rent covers use for one shift being not more than: (i) eight (8) hours
                    per day, (ii) forty (40) hours per week (a week is 5 full business days), and (iii) one hundred sixty
                    (160) hours per twenty-eight (28) days (a 28-day period consists of twenty (20) full business days)
                    unless otherwise noted (each a “One Shift”).
                </li>

                <li class="spaced">
                    <strong>Additional Rent.</strong> Customer may incur the following additional rental charges
                    (“Additional Rent”) and be responsible to RebornRental for payment upon billing as a result of any of
                    the following: (i) an extension of the Estimated Rental Period and any applicable fees as described in
                    Paragraph 3 by agreement or according to terms as specified in Paragraph 16, (ii) additional use(s)
                    beyond One Shift or meter overage, (iii) delayed pickup of Equipment due to fault of Customer, and (iv)
                    period of time for receipt of Customer payment to RebornRental for (a) repair damage to Equipment or
                    return of Equipment to the required Rental Ready condition as received by Customer and further set forth
                    in Paragraph 18, (b) any diminution of Equipment’s value caused by the damage and/or repair of
                    Equipment, or (c) the full replacement cost of Equipment as a result of total loss or destruction of
                    Equipment or Customer’s inability or failure to return it for any reason whatsoever (“Extended Rental
                    Period”). The “Actual Rental Period” shall be the Estimated Rental Period if that is the extent of the
                    Rental; otherwise, the Estimated and Extended Rental Periods constitute the Actual Rental Period for
                    which Customer will be billed.
                </li>

                <li class="spaced">
                    <strong>Additional Charges and Fees.</strong> For each Equipment rented during the Term, Customer is
                    also responsible to pay upon billing if the following is incurred: (i) re-delivery or moving fees; (ii)
                    driver waiting fees; (iii) “Dry Run” fees when Equipment is not made accessible by Customer for pickup,
                    (iv) toll charges; (v) a fuel surcharge calculated on the weight of Equipment being delivered, zip code
                    actual distance and current diesel rates; (vi) fuel used during the Actual Rental Period that is not
                    refilled at a rate of up to $13.99/gallon; (vii) any cleaning fee; (viii) fees for lost keys; (ix) fines
                    for use of dyed diesel fuel in on road Equipment; (x) one day rental rate, delivery and pickup for any
                    cancellation requested by Customer less than 1 full business day (Monday through Friday, 8am - 5pm PST
                    excluding federal holidays) before rental delivery time; (xi) mileage charges on vehicle rentals; (xii)
                    rush fees – non-refundable with order changes or cancellation; (xiii) all towing expenses if Equipment
                    becomes stuck in mud or snow; (xiv) charges and expenses in connection with the transport of loaded
                    dumpsters to landfills, including any expenses, penalties and/or fines assessed by a landfill or third
                    party in connection with the Customer’s failure to comply with dumpster weight restrictions; (xv) an
                    overload fee per ton for dumpsters in excess of the applicable weight restriction on the Equipment, or
                    any other incurred expenses if Equipment is unable to be hauled due to weight or volume overload,
                    assessed in the sole discretion of RebornRental; (xvi) any fines or penalties incurred relating to
                    Customer’s storage and/or transportation of hazardous substances (Collectively, “Additional Charges”.)
                </li>
                <li class="spaced">
                    <strong>Payment Without Offsets, Deductions or Claims.</strong> Customer shall pay the Initial Charges,
                    Additional Rent and Additional Charges without any offsets, deductions or claims and Customer agrees to
                    notify RebornRental in writing of any dispute(s) within 10 days of Customer’s receipt of Invoice. If no
                    dispute is made by Customer, the Invoice shall be deemed to be valid, due, and owing.
                </li>
                <li class="spaced">
                    <strong>Customer Liability.</strong> Customer assumes all risk and liability for the loss of or damage
                    to Equipment from time of delivery and until pickup.
                </li>
                <li class="spaced">
                    <strong>Method of Payment.</strong> Unless RebornRental extends credit to Customer as specified herein,
                    RebornRental requires payment for the Initial Charges prior to Equipment delivery. Customer gives
                    RebornRental authorization to charge Customer’s credit card for any Additional Rent and/or Additional
                    Charges incurred by Customer. If RebornRental elects in its sole discretion to extend credit to the
                    Customer, terms per credit application and approval letter apply (“Credit Terms”). RebornRental shall
                    have the right to limit the amount of credit available to Customer and may increase or decrease this
                    limit without notice to any person, including Customer and Guarantor(s).
                </li>
                <li class="spaced">
                    <strong>Rental Protection Plan.</strong> The Rental Protection Plan ("RPP") is not insurance nor is it a
                    warranty. It is an option RebornRental may offer you to limit liability for loss or damage to the
                    Equipment. If Customer does not accept RPP, Customer must provide evidence of insurance for
                    rented/leased equipment coverage in accordance with Paragraph 24. The benefit from RPP is limited by the
                    deductible in subparagraph (a) and excludes the specific conditions or events shown in subparagraph (b).
                    (a) If this Contract is in compliance and if RebornRental in its discretion has offered and Customer has
                    accepted, RPP, then RebornRental
                    agrees to waive, to the extent specified in this paragraph, Customer's responsibility for damage to the
                    Equipment to the extent that it exceeds a deductible of $1,000 if Equipment rented has a value of
                    $25,000 or less or $2,500 if Equipment rented value is greater than $25,000 or 10% of the repair cost
                    plus tax, whichever is less (“Deductible"). In the event Customer fails to pay Deductible within thirty
                    (30) days of receipt of the damage Invoice, RPP for the Equipment will be considered to be waived by
                    Customer, any RPP amount paid by Customer for the Equipment will be applied to the outstanding damage
                    Invoice and Customer will be responsible for all outstanding balance of the Equipment damage Invoice in
                    full. RPP can only be accepted prior to the scheduled delivery of the Rental. (b) Notwithstanding
                    Customer's acceptance of RPP, Customer's responsibility for loss or damage will not be limited by
                    subparagraph (a) to the extent
                    such loss or damage results from operator's gross negligence or from: (i) vandalism, malicious mischief,
                    theft, mysterious disappearance or conversion of the Equipment; (ii) striking an overhead object with
                    the Equipment; (iii) leaving keys, if any, in the Equipment while that Equipment is not locked or
                    otherwise secured; (iv) exposure to corrosive materials; (v) overloading of a boom or dumpster, or
                    otherwise exceeding rated capacity of the Equipment; (vi) loss or damage to motors or other electrical
                    appliance devices caused by artificial current; or (vii) any damages or loss resulting from use of
                    Equipment in violation of any provision of this Contract, violation of any law, ordinance or regulation
                    or operation in an improper or negligent manner. In the event any of the above exceptions apply, RPP
                    does not apply, and in that event, Customer is obligated to pay RebornRental for costs incurred by
                    RebornRental for the damage. In the event the Equipment is lost, stolen, destroyed, seized by
                    governmental action, or in RebornRental's opinion, irreparably damaged, Customer shall be responsible for
                    the purchase price of a new piece of equipment of similar kind, in addition to any other damages
                    RebornRental may incur. RPP is not insurance and does not protect Customer from liability to
                    RebornRental or others arising out of possession or operation of Equipment, including injury or damage
                    to persons, or property.
                </li>
                <li class="spaced">
                    <strong>Delivery of Equipment.</strong> Any apparent employee or agent at the delivery address
                    (“Jobsite”) will be considered as authorized to accept delivery of Equipment and if Customer requests
                    and RebornRental agrees, Customer can authorize Equipment to be left at the place of delivery without
                    requirement of a written receipt.

                </li>
                <li class="spaced">
                    <strong>Customer Inspection and Waiver.</strong> Customer warrants and represents that Customer will
                    have inspected Equipment upon delivery to confirm that it is in good
                    condition, safe and serviceable, without defects, including readable decals and operating and safety
                    instructions, and is suitable for Customer's intended use. If after inspection, Customer has a proper
                    objection to Equipment, Customer shall notify RebornRental immediately in writing before use of
                    Equipment. Customer acknowledges and agrees that should Customer fail to notify RebornRental within the
                    time specified that it will be conclusively presumed that Equipment is in good working condition and
                    that Customer is satisfied with and has accepted Equipment for all purposes and waives any right to
                    object to Equipment thereafter.
                </li>
                <li class="spaced">
                    <strong>Customer Use.</strong> Customer acknowledges and agrees that RebornRental has no control over
                    the manner in which Equipment is operated during the Actual Rental Period and that Equipment may be
                    dangerous if used improperly or by untrained parties. Customer represents and warrants: (i) the delivery
                    site will be reasonably accessible, safe, and secure, and that Equipment will be operated on a safe
                    location with a solid and level surface; (ii) Equipment will not be subject to neglect, carelessness,
                    misuse, or abuse, including but not limited to, being overloaded or taxed beyond its capacity (including
                    rigging weight capacity limits) or be used for transportation, storage, use or removal of explosives or
                    hazardous products or materials as may be defined by federal, state or local regulatory or enforcement
                    agencies; (iii) Equipment will be: operated only by authorized individuals who are not under the
                    influence of drugs or alcohol or otherwise impaired and who are properly trained and qualified to use
                    Equipment; used with protective equipment according to legal and industry standards, and in a careful,
                    proper and legal manner; used in compliance with all operational and safety instructions provided on, in
                    or with Equipment, including the manufacturer’s specifications, and all federal, state and local laws,
                    ordinances, rules, standards and regulations; and kept in a secure location. Customer acknowledges that
                    Customer is solely responsible to obtain training that Customer desires or deems necessary prior to the
                    use of Equipment and Customer disclaims any obligation or responsibility of RebornRental to Customer or
                    any operator of Equipment. Equipment will not be used when overloaded, or to carry persons or property
                    for hire; (iv) Customer will not allow the use of Equipment in any publication (print, in audiovisual or
                    electronic); (v) Customer will pay for any fees for licenses, registrations, permits, and other
                    certificates that may be required for the lawful operation of Equipment; (vi) Customer or its employees
                    and agents will not alter or cover of any decals or insignia on Equipment or remove any operational or
                    safety instructions; and (vii) Customer will not remove Equipment from the Jobsite without written
                    approval from RebornRental. If Equipment is a dumpster, Customer shall not move, transport or attempt to
                    move or transport (either directly or indirectly) the dumpster without prior notice to and consent from
                    RebornRental.
                </li>
                <li class="spaced">
                    <strong>Customer Maintenance.</strong> Customer shall keep Equipment in good working condition and
                    perform at its expense, routine, but not scheduled, maintenance and cleaning of Equipment including
                    routine inspections and maintenance of fuel and oil levels, lubricants, lubrications, leaks, cooling
                    system, water, batteries, filters, cutting edges, belts, hoses and cleaning. Customer will also comply
                    with preventative maintenance suggested in the manufacturer’s operation and maintenance manual.

                </li>
                <li class="spaced">
                    <strong>Malfunctioning Equipment and Replacement.</strong> Should Equipment malfunction or require
                    repair, Customer shall cease use of Equipment and notify RebornRental immediately. If such condition is
                    the result of normal operation or inherent defect, RebornRental will cause Equipment to be promptly
                    repaired or replaced with similar Equipment in good working order. Customer’s sole remedy for any such
                    failure or defect in Equipment shall be the repair or replacement as set further herein and abeyance of
                    any rent for the period of time between the failure and completion of repair or replacement with any
                    Rental paid in advance being adjusted accordingly and promptly credited to Customer. Separate and apart
                    from malfunctioning Equipment, RebornRental has the right to replace Equipment with other similar
                    Equipment at any time and for any reason. Customer is solely responsible for malfunction or damage to
                    Equipment that is determined to be the result of other causes, as set forth in more detail in Paragraph
                    18.
                </li>
                <li class="spaced">
                    <strong>Operated & Maintained Equipment.</strong> If Rental is for operated and/or maintained Equipment,
                    RebornRental shall provide to Customer one or more persons experienced in operating and maintaining the
                    Equipment (which may consist of an operator and/or crew person(s) (collectively, “Operator”)), which
                    Operator shall at all times operate and maintain the Equipment under the direction and control of
                    Customer. Customer acknowledges and agrees that at no time shall the Equipment be operated, in any
                    fashion or for any purpose, by anyone other than the Operator. Customer agrees that
                    the Equipment and all persons operating the Equipment, including Operator, are under Customer's
                    exclusive jurisdiction, possession, supervision and control. Customer is responsible for providing
                    overall jobsite safety and responsible for providing Operator accurate load weights and accepts all
                    liability from its failure to do so. Customer assumes responsibility for, control of, and supervision of
                    rigging, hooking and unhooking loads. If not provided by RebornRental, Customer agrees to provide
                    competent and qualified personnel to supervise and direct the operation of the Equipment, including
                    signal persons to direct the Operator. Customer warrants and represents that any signal person(s) and
                    rigger(s) supplied by Customer or Customer’s designee for whom Customer is responsible are qualified as
                    defined by OSHA Regulation, 29 CFR §§ 1926, 1425 & 1428, and that documentation of such qualification is
                    available onsite. The Equipment shall be operated in a safe and lawful manner at all times, and in
                    accordance with the manufacturer’s operators manual, OSHA, all laws and regulations thereunder, together
                    with all applicable ANSI standards (including, but not limited, the Standard Crane and Derrick Signals
                    in accordance with ASME/ANSI B30.5-3.3 (amended 2007) and MSHA. The operation of the Equipment shall not
                    exceed the manufacturer's safety requirements and rate load capacities. If the Equipment is a crane, it
                    is to be used as a lift crane only; demolition, dynamic compaction, pile driving, and clamming work
                    require additional documentation and Equipment authorized only by RebornRental.
                </li>
                <li class="spaced">
                    <strong>Lifting Lugs, Rigging and Apparatus.</strong> Customer hereby assumes all responsibility and
                    liability for the adequacy of design and strength of any lifting lug or device embedded in or attached
                    to any object, and any and all rigging and lifting apparatus. Customer will indemnify, defend and hold
                    RebornRental harmless from any and all actions, causes of action, claims, suits, demands,
                    investigations, obligations, judgments, losses, costs, liabilities, damages, fines, penalties and
                    expenses, including attorneys’ fees arising therefrom.
                </li>
                <li class="spaced">
                    <strong>Customer’s Responsibilities for Extension and Pick-up.</strong> Unless requested otherwise, for
                    a prepaid Rental, if Customer would like to extend the Estimated Rental Period, Customer must contact
                    RebornRental at least four (4) business hours before the scheduled end date and time listed on the Sales
                    Order, during normal business hours. If RebornRental does not receive request to extend, it will
                    terminate the Rental at the original end date and time listed on the Sales Order and schedule a pickup
                    accordingly. Unless requested otherwise, for a Rental rented on Credit Terms, the Rental will be
                    automatically extended beyond the Estimated Rental Period. Base Rent, Additional Rent and Additional
                    Charges will continue to be incurred until the Customer notifies RebornRental that Equipment
                    is ready for pickup. If a driver is dispatched to pick up Equipment, and Equipment is not available for
                    pick-up or release, the Customer will be charged a Dry Run and other applicable charges in Paragraphs
                    3-5. Customer will be responsible for the safe keeping of Equipment until the pickup takes place.
                    Depending upon scheduling availability, pickup may take days or possibly weeks.
                </li>
                <li class="spaced">
                    <strong>Communications from Customer.</strong> Except where the form of communication is specified in
                    this Agreement, RebornRental will make a commercial effort to act upon instructions delivered to
                    RebornRental by Customer or an apparent authorized representative in any form, including in person, by
                    phone, text message, electronic mail, EDI, and via RebornRental’s online platform or website, and will
                    assume that all such instructions came from Customer or its authorized representative unless
                    RebornRental has actual knowledge to the contrary. RebornRental is not obligated to further validate
                    instructions given by persons identifying themselves as an authorized representative of Customer.
                    Customer is moreover solely responsible for maintaining the confidentiality and use of Customer’s login
                    credentials and account and order information, and Customer alone is responsible for all acts or
                    omissions that occur via the RebornRental online platform, website, or EDI through the use of Customer’s
                    account. Customer agrees to notify RebornRental immediately of any unauthorized access to or use of
                    Customer’s username and/or password or any breach of security.
                </li>
                <li class="spaced">
                    <strong>Condition of Equipment Upon Return.</strong> Customer will return Equipment together with all
                    accessories, free from all damage, and in the same condition and appearance as when received by
                    Customer, allowing for ordinary wear and tear (“Rental Ready”). Ordinary wear and tear of Equipment
                    shall mean only the normal deterioration of Equipment caused by ordinary and reasonable use during the
                    time used. The following shall not be deemed reasonable wear and tear: (i) damage resulting from the
                    lack of lubrication, insertion of improper fuel, or maintenance of necessary oil, water and air pressure
                    levels; (ii) any damage resulting from lack of servicing or preventative maintenance suggested in the
                    manufacturer’s operation and maintenance manual; (iii) damage resulting from any collision, overturning,
                    or improper operation, including overloading or exceeding the rated capacity of Equipment or operating
                    equipment in extreme temperature environments; (iv) damage in the nature of dents, bending, tearing,
                    staining, corrosion or misalignment to or of Equipment or any part thereof; (v) wear resulting from use
                    in excess of shifts for which rented; and (vi) any other damage to Equipment which is not considered
                    ordinary and reasonable in the equipment rental industry.
                </li>
                <li class="spaced">
                    <strong>Surrender/Abandonment.</strong>                     Any equipment or belongings of Customer or its employees
                    remaining with, in or on Equipment upon return do not constitute a bailment and shall be deemed
                    abandoned and surrendered by the Customer/employees to RebornRental and RebornRental will have no
                    responsibility or obligations of any kind to Customer/employees for such items.
                </li>
                <li>
                    <strong>Right of Inspection and Retrieval of Possession.</strong> In furtherance of Paragraphs 11 and
                    12, Customer has the authority to and hereby grants RebornRental or its designee, which includes but is
                    not limited to, the Equipment supplier or law enforcement (“Designee”), the right, at all times, to
                    enter the physical location of Equipment to inspect Equipment and immediately repossess and remove
                    Equipment without legal process, including prior notice, free of all rights of Customer to Equipment in
                    the event of (i) Customer’s failure to comply with Paragraphs 11, 12 and 16 or otherwise fails or
                    refuses to return Equipment; (ii) an Occurrence as specified in Paragraph 23; (iii) a default as set
                    forth in Paragraph 27, (iv) permanent closure of the store location of Equipment at the time of the
                    Rental, (v) declaration of any emergency, disaster or similar situation by any federal, state or local
                    government; or (vi) as otherwise set forth in this Contract. By these authorizations, Customer
                    specifically waives any right of action Customer might otherwise have arising out of the entry and
                    repossession, and releases RebornRental and its Designee of any claim for trespass or damage caused by
                    reason of the entry, repossession, or removal (“Release”).
                </li>
                <li>
                    <strong>No Sale or Security Interest Intended.</strong> This Contract constitutes a rental lease or
                    bailment of Equipment to Customer as a bailee and is not a sale or the creation of a security interest.
                    Customer will not have, or at any time seek to acquire, any right, title, or interest in Equipment,
                    except the right to possession and use as provided for in this Contract. Between RebornRental and
                    Customer, RebornRental will at all times retain full and rightful interest in Equipment.
                </li>
                <li>
                    <strong>Encumbrances or Liens.</strong> Customer will not pledge, encumber, create a security interest
                    in, permit any levy, writ or lien, or suffer an involuntary transfer of Customer’s interest in this
                    Contract by operation of law. Customer will immediately notify RebornRental of any liens or other
                    encumbrances, threatened or actual, of which Customer has knowledge. Customer will promptly pay or
                    satisfy any obligation from which any lien or encumbrance arises and will otherwise keep Equipment and
                    all title and interest free of any liens and encumbrances. Customer will deliver to RebornRental
                    appropriate satisfactions, waivers, or evidence of payment. RebornRental shall have all rights under
                    applicable law to file a preliminary notice to secure any lien rights and all rights to obtain a lien
                    for nonpayment.
                </li>
                <li>
                    <strong>Notification of Loss/Damage to RebornRental and Authorities.</strong> If Equipment is damaged,
                    lost, stolen, unsafe, disabled, malfunctioning, levied upon, threatened with seizure, or if any other
                    incident concerning Equipment occurs including injury to person or property (“Occurrence”), Customer
                    will immediately notify RebornRental and will file all necessary accident reports, including those
                    required by law and those required by insurers of Equipment and provide all information, including
                    documents of any nature, known to Customer related to the Occurrence.
                </li>
                <li>
                    <strong>Customer's Duty to Insure.</strong> Customer must at its own expense procure and maintain at
                    all times during the Term, the following minimum insurance coverage
                    acceptable to RebornRental in its sole discretion: (a) general liability insurance of not less than
                    $1,000,000 per occurrence, including, but not limited to, coverage for Customer's contractual
                    liabilities herein which includes the Release specified in Paragraph 20 and the Assumption of the Risk
                    and indemnification clauses contained in Paragraph 26; (b) rented/leased Equipment insurance against
                    loss by all risks to Equipment, in an amount at least equal to the Manufacturer’s Suggested List Price
                    (MSLP) thereof and including loss of use and rental income unless RebornRental offered and Customer
                    accepted, RPP, as defined in Paragraph 8; (c) worker's compensation insurance as required by law; and
                    (d) automobile liability insurance (including comprehensive and collision coverage, a non-owned vehicle
                    endorsement and uninsured/underinsured motorist coverage), in the same amounts set forth in subsections
                    (a) and (b), if Equipment is to be used on any roadway. Such policies shall be primary,
                    non-contributory, on an occurrence basis, contain a waiver of subrogation, name RebornRental as an
                    additional insured (including an additional insured endorsement) and loss payee, provide for (i)
                    severability of interests (ii) that an act or omission by the insured party or any additional insured
                    does not avoid or reduce coverage afforded to the insured party or any additional insured, and (iii)
                    RebornRental to receive at
                    least 30 days prior written notice of any cancellation or material change. Any insurance that excludes
                    boom damage or overturns is a breach. Customer shall provide RebornRental a certificate of insurance
                    that recites in summary form compliance with all requirements set forth in this Paragraph relating to
                    specific insurance coverage prior to any Rental and any time upon RebornRental’s request. The
                    requirement of insurance coverage as set forth herein cannot be waived by RebornRental. If Customer has
                    insurance covering such loss or damage Customer shall exercise all rights available to him under said
                    insurance, take all action necessary to process such claims and Customer further agrees to sign said
                    claim and any and all proceeds from such insurance shall be RebornRental’s. Customer to provide
                    RebornRental with complete information concerning insurance coverage carried. The insurance required
                    herein DOES NOT RELIEVE Customer of its responsibilities, indemnification or other obligations provided
                    herein, or for which Customer may be liable by law or otherwise. To the extent RebornRental carries any
                    insurance; RebornRental’s insurance will be considered excess insurance.
                </li>
                <li>
                    <strong>Disclaimer of Warranties by RebornRental.</strong>
                    REBORNRENTAL DOES NOT MAKE NOR GIVE, AND HEREBY DISCLAIMS, ANY EXPRESS, IMPLIED, OR STATUTORY
                    WARRANTIES, OR REPRESENTATIONS TO ANY MATTER WHATSOEVER,
                    INCLUDING THE CONDITION OF EQUIPMENT, ITS MERCHANTABILITY, OR ITS FITNESS FOR ANY PARTICULAR PURPOSE,
                    and as to RebornRental, Customer rents Equipment “AS IS.”
                </li>
                <li>
                    <strong>Customer Liability and Indemnity</strong> Customer assumes all risk and liability for (i) the
                    loss of or damage to Equipment, or the loss of or damage to any property stored in the Equipment
                    (including but not limited to theft, rodent or vermin damage, Acts of God, vandalism, or mildew)
                    from time of delivery and until pickup, (ii) the loss of use and rental income, (iii) the death of or
                    injury to any person or equipment, and (iv) all other risks and liabilities arising from the Customer’s
                    acceptance, possession, transport, use, operation, control, storage, maintenance and/or repair of
                    Equipment, including but not limited to fire, flood, theft, comprehensive losses, artificial
                    electrocution, accident, and Acts of God (“Assumption of Risk”). During and after any termination of
                    this Contract, Customer will defend, indemnify and hold RebornRental, its officers, directors,
                    shareholders, agents, Designees, employees, and every person/entity RebornRental contracts with or who
                    provides any service to RebornRental related to the Rental of Equipment which is the subject of this
                    Contract (“Indemnitee(s)”) harmless against all third-parties as to all actions, causes of action,
                    claims, suits, demands, investigations, obligations, judgments, losses, costs, liabilities, damages,
                    fines, penalties, and expenses, including reasonable attorney's fees, of every kind, nature, and
                    description, (“Claims”), which are incurred by, accrued, asserted, or made against, or recoverable from
                    any of the Indemnitees arising from or relating to, directly or indirectly, (i) the Contract, including
                    the Rental, (ii) Customer's failure to comply with any provision of any insurance policy insuring
                    Customer and RebornRental, (iii) the Customer’s acceptance, possession, transport, use, operation,
                    control, storage, maintenance and/or repair of Equipment, (iv) any direct or consequential damage caused
                    by placement, loading, operation and removal of Equipment (including but not limited to broken or
                    cracked driveways, sidewalks, damage to lawns, trees, shrubs, etc.), (v) Customer’s actual or alleged
                    contamination or other adverse effects on the environment, or any violation of governmental laws,
                    regulations or orders relating to waste handling and disposal, or (vi) loss of use or rental of
                    Equipment due to replacement or repair thereof, whether or not the same arises from damage to real or
                    personal property, injury or death to persons, including but not limited to Customer’s employees, agents
                    and representatives, as well as third parties, to the extent caused in whole or in part by Customer or
                    anyone directly or indirectly employed by Customer or under contract with Customer or anyone for whose
                    acts Customer may be liable; provided however, Customer shall have no obligation to defend, indemnify,
                    or hold harmless RebornRental with respect to a Claim to the extent the applicable Claim arises out of,
                    pertains to, or relates to RebornRental’s sole active negligence or willful misconduct. All of
                    Customer’s indemnification obligations herein shall be joint and several. Customer expressly waives any
                    and all workers’ compensation immunity it may otherwise have in jurisdictions in which the
                    indemnification provided for in this section is broader than that allowed by applicable law, this
                    Paragraph shall be interpreted as providing the broadest indemnification permitted and shall be limited
                    only to the extent necessary to comply with said law. Customer shall cause its employees, agents and
                    other related third parties to cooperate fully with RebornRental, its Indemnitees and insurers, and all
                    insurers providing the insurance under this Contract, in the prompt delivery of any Claim or proceeding
                    at law or equity or threat thereof, and the investigation and the defense of any Claim.
                </li>
                <li>
                    <strong>Customer in Default</strong> Customer shall be in default upon (i) any breach of this Contract,
                    (ii) becoming insolvent (unable or anticipated inability to pay its debts when due, any action regarding
                    its financial conditions such as a relief, assignment, appointment of receiver or the like), (iii)
                    RebornRental’s good faith belief Customer has placed RebornRental’s interest in Equipment at risk, or
                    (iv) dissolution. Upon default, for any reason, Customer and Customer's successor in interest will have
                    no right, title or interest in Equipment, its possession, or its use.
                </li>
                <li>
                    <strong>CRIMINAL WARNING: </strong>The use of false identification to obtain Equipment or the failure to
                    return the Equipment by the end of the Rental Period may be considered a theft subject to criminal
                    prosecution pursuant to applicable criminal or penal code provisions.
                </li>
                <li>
                    <strong>RebornRental’s Rights and Remedies. </strong>Upon Customer’s default, the balance of all unpaid
                    Base Rent, Additional Charges and Other Charges of any kind required of Customer under the Contract are
                    deemed payable immediately, in which event RebornRental will be entitled to the balance due together
                    with interest at the rate of 1.5% percent per month from the date payment is past due to the date of
                    payment by Customer. Customer will reimburse RebornRental for all costs and expenses, including
                    attorneys’ fees, incurred to repossess and remove Equipment, collect monies due, and enforce
                    RebornRental’s rights and remedies herein, together with interest at the rate of 1.5% percent per month
                    from the date incurred. The remedies of RebornRental will be cumulative to the extent permitted by law,
                    and may be exercised partially, concurrently, or separately. The exercise of one remedy will not be
                    deemed to preclude the exercise of any other. No failure or delay by RebornRental to exercise any remedy
                    or right under this Contract will operate as a waiver in any respect. Acceptance by RebornRental of rent
                    or other payments made by Customer after default will not be deemed a waiver of RebornRental’s rights
                    and remedies arising from Customer's default.
                </li>
                <li>
                    <strong>RebornRental in Default. </strong>RebornRental shall not be in default based on a breach of this
                    Contract until it has a reasonable time to cure the basis for the default. In no case is RebornRental
                    liable due to seizure of Equipment by order of governmental authority or any force majeure consisting of
                    an event beyond its control.
                </li>
                <li>
                    <strong>Limitation of RebornRental Liability and Damages to Customer. </strong>To the maximum extent
                    permitted by applicable law and excepting willful misconduct by RebornRental, RebornRental shall not be
                    liable to Customer, and Customer covenants that it shall not assert a claim against RebornRental, under
                    any legal theory, whether in an action based on a contract, breach of warranty, negligence, tort, strict
                    liability, or otherwise provided by statute or law, for any direct or indirect loss, incidental,
                    exemplary, consequential or statutory
                    damages or any damages resulting from lost profits or use of capital, revenue, income or rent,
                    production delays, loss of product, reservoir loss or damage, losses resulting from failure to meet
                    other contractual commitments or deadlines, downtime of facilities, interruption of business, or loss of
                    goodwill, even if RebornRental had been advised of the possibility of such damage, which are caused by,
                    resulting from or in any way connected with the possession, transport, operation, use, control or
                    storage of Equipment of any Rental, including any failure to have Equipment delivered as specified. In
                    the event
                    RebornRental incurs any liability, CUSTOMER AGREES ANY LIABILITY OF REBORNRENTAL FOR ANY RENTAL DURING
                    THE TERM OF THIS CONTRACT, INCLUDING LIABILITY ARISING FROM REBORNRENTAL’S OR ANY THIRD PARTY’S
                    COMPARATIVE, CONCURRENT, CONTRIBUTORY, PASSIVE OR ACTIVE NEGLIGENCE OR THAT ARISES AS A RESULT OF ANY
                    STRICT OR ABSOLUTE LIABILITY, SHALL BE LIMITED AND NOT EXCEED THE TOTAL RENTAL CHARGES AND FEES PAID BY
                    CUSTOMER FOR THE SPECIFIC INVOICED RENTAL OF EQUIPMENT AND THE EXCESS IS DEEMED WAIVED BY CUSTOMER.
                    CUSTOMER ACKNOWLEDGES IT UNDERSTANDS THE PROVISIONS OF UNIFORM COMMERCIAL CODE PARAGRAPHS 2A-503 AND
                    2A-508-522 AND ANY APPLICABLE STATE COUNTERPART PERTAINING TO A LESSEE’S RIGHTS AND REMEDIES AGAINST A
                    LESSOR AND TO THE EXTENT THE LAW ALLOWS, AGREES TO WAIVE ALL SUCH RIGHTS AND REMEDIES. CUSTOMER HEREBY
                    WAIVES ANY CLAIM THAT THESE EXCLUSIONS, LIMITATIONS AND WAIVERS DEPRIVE IT OF AN ADEQUATE REMEDY OR
                    CAUSE THIS CONTRACT TO FAIL IN ITS ESSENTIAL PURPOSE. CUSTOMER AND REBORNRENTAL HEREBY ACKNOWLEDGE AND
                    AGREE THAT ANY WARRANTY DISCLAIMERS AND LIMITATIONS OF LIABILITY PROVISIONS SET FORTH IN THIS CONTRACT
                    HAVE BEEN NEGOTIATED AND ARE FUNDAMENTAL ELEMENTS OF THE BASIS OF THIS CONTRACT IN THAT THEY RECOGNIZE
                    THAT REBORNRENTAL DOES NOT OWN EQUIPMENT AND HAS NOT SEEN IT BUT ONLY LOCATES EQUIPMENT AND RENTS IT TO
                    CUSTOMER.
                </li>
                <li>
                    <strong>Service of Notice.</strong> Except as otherwise expressly provided by law, any notices or other
                    communications required or permitted by this Contract or by law to be served on or given to either party
                    by the other party will be in writing and will be deemed duly served or given when personally delivered
                    the party to whom they are directed, or in lieu of personal service, when deposited in the United States
                    mail, first-class postage prepaid, addressed to Customer address listed on Invoice or to RebornRental at
                    401 Ryland St. Ste 200 A, Reno, NV 89502, USA. Either party may change its address for the purpose of
                    this Paragraph by giving written notice of the change to the other party in the manner provided in this
                    Paragraph.
                </li>
                <li>
                    <strong>Assignment.</strong> RebornRental may assign this Contract or any rights under it at any time without Customer's consent. In
                    the event of any assignment, RebornRental’s assignee will have all the rights and remedies of
                    RebornRental set forth in this Contract. Customer will not sublease, sub-rent, assign or loan Equipment,
                    or assign any interest in this Contract.
                </li>
                <li>
                    <strong>Entire Agreement.</strong> This Contract, including the Sales Order and/or Invoice during the
                    Term constitute the entire agreement between the parties. No agreements, representations, or warranties
                    other than those specifically set forth in this Contract will be binding on any of the parties.
                </li>
                <li>
                    <strong>MANDATORY ARBITRATION.</strong> Except as provided in Paragraph 36, if a dispute arises from or
                    relates to this Contract or a breach of it, including with respect to any individual Rental within this
                    Contract, the parties agree the dispute shall be settled by arbitration administered by the American
                    Arbitration Association (“AAA”), except as otherwise provided in this Paragraph, in accordance with its
                    Commercial Arbitration Rules and the Expedited Procedures contained therein if applicable, both of which
                    will be presented to Customer upon written request, and judgment of the reasoned award rendered by the
                    arbitrator may be entered in any federal or state court having jurisdiction thereof. Customer agrees
                    that the arbitration shall be limited to the dispute between Customer and RebornRental and will not be
                    part of a class-wide or consolidated arbitration proceeding. Arbitration shall be initiated by filing a
                    demand with AAA with notice thereof given to the other party. The arbitrator shall have exclusive
                    authority to resolve the dispute, including but not limited to, the interpretation, applicability,
                    enforceability, validity or formation of the Contract, any claim that all or any part of the Contract is
                    void or voidable, and any claim arising out of the terms and conditions of the Contract or involving the
                    rights and obligations of the parties hereto or its breach. The proceedings before the AAA shall include
                    the following: (i) If not heard by telephonic means according to the parties’ mutual consent, the
                    arbitration hearing shall be held in the City of Reno, Nevada. (ii) In any arbitration or judicial
                    proceeding, this Contract shall be governed and interpreted in accordance with the laws of the State of
                    California unless otherwise preempted by the Federal Arbitration Act. (iii) The arbitrator may grant any
                    remedy or relief that the arbitrator deems just and equitable and within the scope of the applicable law
                    and the Contract. (iv) The administrative costs and attorneys’ fees arising out of the arbitration and
                    any ancillary judicial proceedings necessarily required in the enforcement of the Contract shall be
                    borne by the losing party or shall be borne in such proportions as the arbitrator may determine.
                </li>
                <li>
                    <strong>Collection Proceedings by RebornRental.</strong>RebornRental reserves the right to collect any
                    outstanding monies owed it by means of debt collection through a debt collection agency prior to
                    proceeding to arbitration and by doing so, does not waive its right to arbitrate.
                </li>
                <li>
                    <strong>Contract Survives Partial Invalidity.</strong> If any provision of this Contract or the
                    application of any of its provisions to any party or circumstance is held invalid or unenforceable, the
                    remainder of this Contract, and the application of those provisions to the other parties or
                    circumstances, will remain valid and in full force and effect.
                </li>
                <li>
                    <strong>Terms Survive Contract.</strong> All terms and provisions of this Contract that should by
                    their nature survive the termination, regardless of reason, of this Contract shall so
                    survive, including but not limited to Paragraphs 19, 22, 25, 34 & 35.
        </li>
        <li>
                    <strong>Communication Authorization.</strong>Customer consents to the collection, use and disclosure of
                    data and information Customer voluntarily provides to RebornRental and receipt of marketing emails from
                    RebornRental and its affiliates as described in the Privacy Policy found at
                    https://web.RebornRental.com/privacy-policy. Customer has the right to opt out upon receipt.
        </li>
        <li>
                    <strong>Original Signature Equivalents.</strong>Digital, electronic, photocopy and faxed signatures
                    hereon shall be deemed the equivalent of originals. By ticking the designated box, the customer
                    acknowledges and agrees that this action constitutes their signature for the purposes of this agreement.
                    Such an action shall be deemed the legal equivalent of a handwritten signature, and the agreement shall
                    be considered duly executed by the customer.
        </li>
                <li>
                    <strong>TERMS AND CONDITIONS UPON REQUEST.</strong> A Larger Print Version of this Contract with its
                    terms and conditions is available from RebornRental upon written request.
                </li>
      
    </ol>
        </div>

     
        <!-- Información de contacto -->
        <p class="mt-large">
            The undersigned represents and warrants s/he is of legal age and has the authority and 
            power to sign this Contract and understands that this Contract is valid and enforceable 
            once executed by Customer 
    </p>
</main>
@endsection
