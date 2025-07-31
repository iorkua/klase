<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sectional Titling Certificate of Occupancy</title>
    <style>
      /* Page setup for printing */
      @page {
        size: A4;
        margin: 6mm;
      }

      /* Base document styling */
      body {
        font-family: "Times New Roman", serif;
        margin: 0;
        padding: 5mm 10mm;
        line-height: 1.1;
        font-size: 10pt;
        text-align: justify;
        width: 100%;
      }

      /* Main certificate container */
      .certificate-container {
        width: 100%;
        height: 277mm;
        position: relative;
      }

      /* Header section with title and file info */
      .header-container {
        display: flex;
        justify-content: center;
        position: relative;
        margin-bottom: 15mm;
        padding-top: 12mm;
      }

      /* Header text content */
      .header-content {
        text-align: center;
        flex: 1;
        max-width: 70%;
      }

      /* Main title styling */
      .header h1 {
        font-size: 16pt;
        margin: 0;
        text-transform: uppercase;
        text-decoration: underline;
        font-weight: normal;
      }

      /* File number and type styling */
      .file-info {
        text-align: center;
        margin: 1mm 0;
        font-size: 11pt;
      }

      /* Passport photo section */
      .passport-section {
        position: absolute;
        right: 0;
        top: 15mm;
        width: 30mm;
      }

      /* Passport photo placeholder */
      .passport-slot {
        width: 28mm;
        height: 38mm;
        border: 1px dashed #000;
        position: relative;
      }

      /* Passport photo label */
      .passport-slot::after {
        content: "Passport Photo";
        position: absolute;
        bottom: 1mm;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 6pt;
        color: #666;
      }

      /* Main content area */
      .main-content {
        margin-top: 10mm;
      }

      /* Certificate holder information */
      .certify-text {
        margin: 1mm 0;
        font-size: 11pt;
      }

      /* Holder name styling */
      .holder-name {
        font-weight: bold;
        margin-left: 5mm;
      }

      /* Holder address styling */
      .holder-address {
        margin-left: 5mm;
      }

      /* Terms and conditions section */
      .terms {
        margin: 1mm 0;
        font-size: 10pt;
      }

      /* Remove default margins */
      .terms p,
      .terms ol {
        margin: 0;
        padding: 0;
      }

      /* Ordered list indentation */
      .terms ol {
        padding-left: 5mm;
      }

      /* List item spacing */
      .terms ol li {
        margin-bottom: 0;
      }

      /* Date section styling */
      .date-section {
        font-weight: bold;
        text-align: center;
        margin: 3mm 0;
        font-size: 9pt;
        width: 100%;
      }

      /* Signature section */
      .signature-section {
        text-align: right;
        margin: 3mm 0 0 auto;
        width: fit-content;
      }

      /* Signature line */
      .signature-line {
        border-top: 1px solid #000;
        width: 50mm;
        margin-left: auto;
        margin-top: 1mm;
      }

      /* Signature name */
      .signature-name {
        margin: 0;
        padding: 1mm;
        font-size: 10pt;
      }

      /* Signature title */
      .signature-title {
        margin: 0;
        padding: 0;
        font-style: italic;
        font-size: 9pt;
      }

      /* Print controls container */
      .controls {
        text-align: center;
        margin: 2mm 0;
      }

      /* Print button styling */
      .print-btn {
        background-color: #004080;
        color: white;
        border: none;
        padding: 2mm 4mm;
        font-size: 9pt;
        cursor: pointer;
      }

      /* Print-specific styles */
      @media print {
        .controls {
          display: none;
        }
        body {
          padding: 0;
          margin: 0;
        }
        .certificate-container {
          height: auto;
        }
      }
    </style>
  </head>
  <body>
    <!-- Back Button and Print Button -->
    <div class="controls no-print mb-4 bg-white p-4 rounded-md shadow-sm">
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

    <!-- Main certificate container -->
    <div class="certificate-container">
      <!-- Header with title and passport photo -->
      <div class="header-container">
        <div class="header-content">
          <div class="header">
            <h1>SECTIONAL TITLING CERTIFICATE OF OCCUPANCY</h1>
          </div>
          <div class="file-info">
            New File No: <span id="fileNumber">{{ $cofo->file_no ?? 'ST/COM/2025/001' }}</span><br />
            <span id="landuse">{{ $cofo->land_use ?? 'Insert Landuse' }}</span><br />
            <span id="unitDescription">
                @if(!empty($cofo->unit_description))
                    {{ $cofo->unit_description }}
                @else
                    Plot No: {{ $cofo->plot_no ?? 'N/A' }}, 
                    Block No: {{ $cofo->block_no ?? 'N/A' }}, 
                    Floor No: {{ $cofo->floor_no ?? 'N/A' }}, 
                    Flat No: {{ $cofo->flat_no ?? 'N/A' }}
                @endif
            </span>
          </div>
        </div>
        <div class="passport-section" id="passportSection">
          <div class="passport-slot">
            @if($unit_owner && $unit_owner->passport && $unit_owner->passport !== 'default-passport.jpg')
                <img src="{{ asset('storage/app/public/' . $unit_owner->passport) }}" alt="Passport Photo" style="width: 100%; height: 100%; object-fit: cover;">
            @else
                <div style="display: flex; align-items: center; justify-content: center; height: 100%; font-size: 7px; color: #666; text-align: center;">
                    PASSPORT<br>
                    PHOTOGRAPH<br>
                    HERE
                </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Main content area -->
      <div class="main-content">
        <!-- Certificate holder information -->
        <div class="certify-text">
          This is to certify that:-
          <span class="holder-name" id="holderName">{{ $cofo->file_no ?? '[Insert FileNo]' }}</span><br />
          Whose address is
          <span class="holder-address" id="holderAddress">{{ $cofo->holder_address ?? '[Insert Address]' }}</span>
        </div>

        <!-- Terms and conditions -->
        <div class="terms">
          <p>
            (Herein after called the holder, which terms shall include any
            person/persons in title) is hereby granted a right of occupancy for
            in and over the land described in the schedule, and more
            particularly in the plan printed hereto for a term of
            <strong>{{ $cofo->total_term ?? '[Tenancy]' }}</strong> commencing from
            <strong>{{ isset($cofo->start_date) ? date('jS F, Y', strtotime($cofo->start_date)) : '[Insert Certificate Date]' }}</strong> according to the true
            intent and meaning of the Kano State Sectional and Systematic Land
            Titling Registration Law, 2024 and subject to the provisions thereof
            and to the following special terms and conditions:
          </p>

          <ol>
            <li>
              To pay in advance without demand to the Government of the State
              (herein after referred to as the Governor) or any other officer or
              agency appointed by the Governor of the State:
              <ol type="a">
                <li>
                  Whatever is the computed revised and the current ground rent
                  from the first day of January of each year or
                </li>
                <li>
                  Such revised ground rent as the Governor may from time to time
                  prescribe.
                </li>
                <li>
                  Such penal rent as the Governor may from time to time impose.
                </li>
              </ol>
            </li>
            <li>
              To pay and discharge all rates (including utilities), assessment
              and impositions, whatsoever which shall at any time be charged or
              imposed on the said land or any building thereon, or upon the
              occupier or occupiers thereof.
            </li>
            <li>
              To pay forthwith to the Kano State Government through Ministry of
              Land and Physical Planning or such other body or agency appointed
              by the Governor (if not sooner paid) all survey fees and other
              charges due in respect of the preparation, registration and
              issuance of this certificate.
            </li>
            <li>
              Within two years from the day of the commencement of the right of
              occupancy to erect and complete on the said land building(s) or
              other works specified in the related plans approved or to be
              approved by the Kano State Government or any other agency
              empowered to do so. The approval may be revoked after two (2)
              years.
            </li>
            <li>
              To maintain in good and substantial repair to the satisfaction of
              Kano State Government or any other officer appointed by the
              Governor, all buildings on the said land and appurtenances
              thereof, and to do other works, properly maintained in clean and
              good sanitary condition around all of the land and surroundings of
              the buildings.
            </li>
            <li>
              Upon the expiration of the said term to deliver up to the Governor
              in good and tenable state to the satisfaction of the Kano State
              Government or any other agency appointed by the State Governor,
              the said land and building(s) thereon.
            </li>
            <li>
              Not to erect build or permit to be erected or built on the land,
              buildings other than those permitted to be erected by virtue of
              this certificate of occupancy nor to make or permit to be made any
              addition or alteration to the said building(s) already erected on
              the land except in accordance with the plans and specifications
              approved by the Governor and or any officer authorized by him on
              his behalf.
            </li>
            <li>
              The Governor or any public officer duly authorized by the Governor
              on his behalf, shall have the power to enter upon and inspect the
              land comprised in any statutory right of occupancy or any
              improvements effected thereon, at any reasonable hour during the
              day and the occupier shall permit and give free access to the
              Governor or any such officer to enter and so inspect.
            </li>
            <li>
              Not to alienate the right of occupancy hereby granted or any part
              thereof by sale, assignment, mortgage, transfer of possessions,
              sub-lease or bequest, or otherwise howsoever without the prior
              consent of the Governor.
            </li>
            <li>
              To use the said land only for
              <strong>{{ $cofo->land_use ?? '[Insert Landuse]' }}</strong> purpose.
            </li>
            <li>
              Not to contravene any of the provisions of the Kano State
              Sectional and Systematic Land Titling Registration Law, 2024 and
              to conform and comply with all rules and regulations laid down
              from time to time by Kano State Government.
            </li>
            <li>
              To become joint owner of the common property of the Sectional
              Titling Land and actively participate in all quotas that benefit
              or burden sections.
            </li>
            <li>
              To exclusively use certain parts and share undivided sections of
              the common property e.g, Garage, Garden, Parking space Storeroom
              among others.
            </li>
            <li>
              For the purpose of the rent to be paid under this certificate of
              occupancy:
              <ol type="a">
                <li>
                  The term of the Right of Occupancy shall be divided into
                  periods of five years and Governor may, at the expiration of
                  each period of five years, revise the rent and fix the sum
                  which shall be payable for the next period of five years. If
                  the Governor shall so revise the rent, he shall cause a notice
                  to be sent to the holder/holders and the rent so fixed or
                  revised shall commenced to be payable one calendar month from
                  the date of the receipt of such notice.
                </li>
                <li>
                  If any rent for the time being payable in respect of the land
                  or any part hereof shall be in arrears for the period of three
                  months whether same shall or shall not have been legally
                  demanded of if the holder/holders become bankrupt or make a
                  composition with creditors or enter into liquidation, whether
                  compulsory or voluntarily or if there shall be any breach or
                  non-observance of any of the occupier's covenants or
                  agreements herein contained. Then and in any of the said cases
                  it shall be lawful for the Governor at any given time
                  thereafter to hold and enjoy the same as if the right of
                  occupancy had not been granted but without prejudice to Right
                  of Action or remedy of Governor for any antecedent breach of
                  covenant by the holder/holders.
                </li>
              </ol>
            </li>
          </ol>
        </div>

        <!-- Date section -->
        <div class="date-section">
          DATED This ____________________ day of ________________,
          20___________<br />
          <br /><em>Given under my hand the day and year above written</em>
        </div>

        <!-- Signature section -->
        <div class="signature-section">
          <div class="signature-line"></div>
          <div class="signature-name">{{ $cofo->signed_by ?? 'Alh. Abduljabbar Mohammed Umar' }}</div>
          <div class="signature-title">
            {{ $cofo->signed_title ?? 'Honorable Commissioner of Land and Physical Planning' }}
          </div>
          <div class="signature-title">Kano State, Nigeria</div>
        </div>
      </div>
    </div>

    <!-- JavaScript for adding passport slots -->
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        function addPassportSlot() {
          const passportSection = document.getElementById("passportSection");
          const newSlot = document.createElement("div");
          newSlot.className = "passport-slot";
          passportSection.appendChild(newSlot);
        }
      });
    </script>
  </body>
</html>