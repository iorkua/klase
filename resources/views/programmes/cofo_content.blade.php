<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate of Occupancy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @page {
            size: A4;
            margin: 0;
        }

        @media print {
            html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                overflow: hidden;
            }
            
            /* Hide everything by default */
            body * {
                visibility: hidden;
            }
            
            /* Show only the certificate container and its contents */
            .page-container,
            .page-container * {
                visibility: visible;
            }
            
            /* Position the certificate container for printing */
            .page-container {
                position: fixed;
                left: 0;
                top: 0;
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 8mm;
                box-sizing: border-box;
                box-shadow: none !important;
                font-size: 9px !important;
                line-height: 1.1 !important;
                overflow: hidden;
            }
            
            /* Hide non-printable elements */
            .no-print {
                display: none !important;
            }
            
            /* Ensure proper print colors */
            body {
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }
        }

        .page-container {
            width: 210mm;
            height: 297mm;
            margin: 0 auto;
            padding: 10mm;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            font-family: 'Times New Roman', serif;
            font-size: 10px;
            line-height: 1.2;
            overflow: hidden;
        }

        .passport-frame {
            border: 2px solid #000;
            width: 30mm;
            height: 38mm;
            position: relative;
            background: #f8f8f8;
            overflow: hidden;
        }

        .passport-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .passport-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            font-size: 7px;
            color: #666;
            text-align: center;
        }

        . {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 80px;
            padding-bottom: 1px;
        }

        .checkbox {
            width: 10px;
            height: 10px;
            border: 1px solid #000;
            display: inline-block;
            margin-right: 5px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 160px;
            margin-top: 20px;
        }

        .highlight {
            text-transform: uppercase;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Back Button and Print Button -->
    <div class="no-print mb-4 bg-white p-4 rounded-md shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-2">
            <div class="md:col-span-3 mb-2">
                <a href="javascript:history.back()" class="bg-blue-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back
                </a>
                <button onclick="window.print()" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Print CofO Front Page
                </button>
            </div>
        </div>
    </div>

@php
    // Get the unit owner information from subapplications where fileno matches
    $unit_owner = DB::connection('sqlsrv')->table('subapplications')
        ->where('fileno', $cofo->file_no)
        ->first();
    
    // If no unit owner is found, set passport to default
    if (!$unit_owner) {
        $unit_owner = (object)['passport' => 'default-passport.jpg'];
    }
@endphp

    <div class="page-container">
        <!-- Main Content Area -->
        <div class="flex-1">
            <!-- Header -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1 pr-4">
                    <h1 class="text-lg font-bold text-center mb-3 underline">SECTIONAL TITLING (ST) CERTIFICATE OF OCCUPANCY</h1>
                    <div class="text-sm space-y-1">
                        <p class="text-center"><strong class="font-bold">New File No:</strong> <span class="highlight">{{ $cofo->file_no ?? 'ST/COM/2025/001' }}</span></p>
                        <p class="text-center">{{ $cofo->land_use ?? 'Insert Landuse' }}</p>
                        <p class="text-center font-bold">
                            @if(!empty($cofo->unit_description))
                                {{ $cofo->unit_description }}
                            @else
                                Plot No: {{ $cofo->plot_no ?? 'N/A' }}, 
                                Block No: {{ $cofo->block_no ?? 'N/A' }}, 
                                Floor No: {{ $cofo->floor_no ?? 'N/A' }}, 
                                Flat No: {{ $cofo->flat_no ?? 'N/A' }}
                            @endif
                        </p>
                        <p class="text-center">
                            This is to certify that: - <span class="font-bold highlight">{{ $cofo->file_no ?? '[Insert FileNo]' }}</span>
                        </p>
                        <p class="text-center">
                            Whose address is <span class="font-bold highlight">{{ $cofo->holder_address ?? '[Insert Address]' }}</span>
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0">
                    <div class="passport-frame">
                        @if($unit_owner && $unit_owner->passport && $unit_owner->passport !== 'default-passport.jpg')
                            <img src="{{ asset('storage/app/public/' . $unit_owner->passport) }}" alt="Passport Photo">
                        @else
                            <div class="passport-placeholder">
                                PASSPORT<br>
                                PHOTOGRAPH<br>
                                HERE
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Certificate Body -->
            <div class="mb-3">
                <p class="mb-3 text-justify">
                    (Herein after called the holder, which terms shall include any person/persons in title) is hereby granted a right of occupancy for in and over the land described in the schedule, and more particularly in the plan printed hereto for a term of <span class=" highlight">{{ $cofo->total_term ?? '[Tenancy]' }}</span> commencing from <span class=" highlight">{{ isset($cofo->start_date) ? date('jS F, Y', strtotime($cofo->start_date)) : '[Insert Certificate Date]' }}</span> according to the true intent and meaning of the Kano State Sectional and Systematic Land Titling Registration Law, 2024 and subject to the provisions thereof and to the following special terms and conditions:
                </p>
            </div>

            <!-- Terms and Conditions -->
            <div class="mb-2">
                <div class="mb-1">
                    <p class="font-bold mb-0.5">1) To pay in advance without demand to the Government of the State (herein after referred to as the Governor) or any other officer or agency appointed by the Governor of the State:</p>
                    <div class="ml-4 text-xs">
                        <p class="mb-0.5">a) Whatever is the computed revised and the current ground rent from the first day of January of each year or</p>
                        <p class="mb-0.5">b) Such revised ground rent as the Governor may from time to time prescribe.</p>
                        <p class="mb-0.5">c) Such penal rent as the Governor may from time to time impose.</p>
                    </div>
                </div>

                <p class="mb-1">2) To pay and discharge all rates (including utilities), assessment and impositions, whatsoever which shall at any time be charged or imposed on the said land or any building thereon, or upon the occupier or occupiers thereof.</p>

                <p class="mb-1">3) To pay forthwith to the Kano State Government through Ministry of Land and Physical Planning or such other body or agency appointed by the Governor (if not sooner paid) all survey fees and other charges due in respect of the preparation, registration and issuance of this certificate.</p>

                <p class="mb-1">4) Within two years from the day of the commencement of the right of occupancy to erect and complete on the said land building(s) or other works specified in the related plans approved or to be approved by the Kano State Government or any other agency empowered to do so. The approval may be revoked after two (2) years.</p>

                <p class="mb-1">5) To maintain in good and substantial repair to the satisfaction of Kano State Government or any other officer appointed by the Governor, all buildings on the said land and appurtenances thereof, and to do other works, properly maintained in clean and good sanitary condition around all of the land and surroundings of the buildings.</p>

                <p class="mb-1">6) Upon the expiration of the said term to deliver up to the Governor in good and tenable state to the satisfaction of the Kano State Government or any other agency appointed by the State Governor, the said land and building(s) thereon.</p>

                <p class="mb-1">7) Not to erect build or permit to be erected or built on the land, buildings other than those permitted to be erected by virtue of this certificate of occupancy nor to make or permit to be made any addition or alteration to the said building(s) already erected on the land except in accordance with the plans and specifications approved by the Governor and or any officer authorized by him on his behalf.</p>

                <p class="mb-1">8) The Governor or any public officer duly authorized by the Governor on his behalf, shall have the power to enter upon and inspect the land comprised in any statutory right of occupancy or any improvements effected thereon, at any reasonable hour during the day and the occupier shall permit and give free access to the Governor or any such officer to enter and so inspect.</p>

                <p class="mb-1">9) Not to alienate the right of occupancy hereby granted or any part thereof by sale, assignment, mortgage, transfer of possessions, sub-lease or bequest, or otherwise howsoever without the prior consent of the Governor.</p>

                <p class="mb-1">10) To use the said land only for <span class=" highlight">{{ $cofo->land_use ?? '[Insert Landuse]' }}</span> purpose.</p>

                <p class="mb-1">11) Not to contravene any of the provisions of the Kano State Sectional and Systematic Land Titling Registration Law, 2024 and to conform and comply with all rules and regulations laid down from time to time by Kano State Government.</p>

                <p class="mb-1">12) To become joint owner of the common property of the Sectional Titling Land and actively participate in all quotas that benefit or burden sections.</p>

                <p class="mb-1">13) To exclusively use certain parts and share undivided sections of the common property e.g, Garage, Garden, Parking space, Storeroom among others.</p>

                <div class="mb-1">
                    <p class="font-bold mb-0.5">14) For the purpose of the rent to be paid under this certificate of occupancy:</p>
                    <div class="ml-4 text-xs">
                        <p class="mb-0.5">i. The term of the Right of Occupancy shall be divided into periods of five years and Governor may, at the expiration of each period of five years, revise the rent and fix the sum which shall be payable for the next period of five years. If the Governor shall so revise the rent, he shall cause a notice to be sent to the holder/holders and the rent so fixed or revised shall commenced to be payable one calendar month from the date of the receipt of such notice.</p>
                        <p class="mb-0.5">ii. If any rent for the time being payable in respect of the land or any part hereof shall be in arrears for the period of three months whether same shall or shall not have been legally demanded or if the holder/holders become bankrupt or make a composition with creditors or enter into liquidation, whether compulsory or voluntarily or if there shall be any breach or non-observance of any of the occupier's covenants or agreements herein contained. Then and in any of the said cases it shall be lawful for the Governor at any given time thereafter to hold and enjoy the same as if the right of occupancy had not been granted but without prejudice to Right of Action or remedy of Governor for any antecedent breach of covenant by the holder/holders.</p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-4">
                <p class="mb-2 text-center">DATED This <span class=" ">____________________________</span> day of <span>________________________</span>, 20<span class=" ">_________________________</span></p>
                <p class="mb-2 text-center">Given under my hand the day and year above written</p>
                <div class="flex justify-end">
                    <div class="text-center">
                        <div class="signature-line mb-1 text-center"></div>
                        <div class="text-xs">
                            <p class="font-bold">{{ $cofo->signed_by ?? 'Alh. Abduljabbar Mohammed Umar' }}</p>
                            <p>{{ $cofo->signed_title ?? 'Honorable Commissioner of Land and Physical Planning' }}</p>
                            <p>Kano State, Nigeria</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>