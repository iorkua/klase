<div class="form-section" id="step4">
    <div class="p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-center text-gray-800">MINISTRY OF LAND AND PHYSICAL PLANNING</h2>
        <button id="closeModal3" class="text-gray-500 hover:text-gray-700">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      
      <div class="mb-6">
        <div class="flex items-center mb-2">
          <i data-lucide="file-text" class="w-5 h-5 mr-2 text-green-600"></i>
          <h3 class="text-lg font-bold">Application for Sectional Titling - Unit Application (Secondary)</h3>
          <div class="ml-auto flex items-center">
            <span class="text-gray-600 mr-2">Land Use:</span>
            <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-sm">{{ $motherApplication->land_use ?? 'N/A' }}</span>
          </div>
        </div>
        <p class="text-gray-600">Complete the form below to submit a new unit application for sectional titling</p>
      </div>

      <div class="flex items-center mb-8">
        <div class="flex items-center mr-4">
          <div class="step-circle inactive-tab">1</div>
        </div>
        <div class="flex items-center mr-4">
          <div class="step-circle inactive-tab">2</div>
        </div>
         <div class="flex items-center mr-4">
          <div class="step-circle inactive-tab">3</div>
        </div>
        <div class="flex items-center mr-4">
          <div class="step-circle active-tab">4</div>
        </div>
        <div class="ml-4">Step 4</div>
      </div>

      <div class="mb-6">
        <div class="flex items-start mb-4">
          <i data-lucide="file-text" class="w-5 h-5 mr-2 text-green-600"></i>
          <span class="font-medium">Application Summary</span>
        </div>
        
        <!-- File Information Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6 bg-blue-50">
          <h4 class="font-medium mb-4 text-blue-800">File Information</h4>
          <div class="grid grid-cols-2 gap-6">
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">STFileNo:</td>
                <td class="py-1 font-medium text-blue-800" id="summary-st-file-number">
                  <span id="stFileNumberDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Scheme No:</td>
                <td class="py-1 font-medium" id="summary-scheme-no">
                  <span id="schemeNoDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Main Application ID:</td>
                <td class="py-1 font-medium" id="summary-main-id">
                  <span id="mainIdDisplay"></span>
                </td>
              </tr>
            </table>
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">Primary Application ID:</td>
                <td class="py-1 font-medium" id="summary-primary-app-id">
                  <span id="primaryAppIdDisplay">{{ $motherApplication->applicationID ?? 'N/A' }}</span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Land Use:</td>
                <td class="py-1 font-medium" id="summary-land-use">
                  <span id="landUseDisplay">{{ $motherApplication->land_use ?? 'N/A' }}</span>
                </td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Applicant Information Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6" id="applicant-info-section">
          <h4 class="font-medium mb-4">Applicant Information</h4>
          <div class="grid grid-cols-2 gap-6">
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">Applicant Type:</td>
                <td class="py-1 font-medium" id="summary-applicant-type">
                  <span id="applicantTypeDisplay">Individual</span>
                </td>
              </tr>
              <tr id="individual-name-row">
                <td class="py-1 text-gray-600">Name:</td>
                <td class="py-1 font-medium" id="summary-applicant-name">
                  <span id="applicantNameDisplay"></span>
                </td>
              </tr>
              <tr id="corporate-name-row" style="display: none;">
                <td class="py-1 text-gray-600">Corporate Name:</td>
                <td class="py-1 font-medium" id="summary-corporate-name">
                  <span id="corporateNameDisplay"></span>
                </td>
              </tr>
              <tr id="corporate-rc-row" style="display: none;">
                <td class="py-1 text-gray-600">RC Number:</td>
                <td class="py-1 font-medium" id="summary-rc-number">
                  <span id="rcNumberDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Email:</td>
                <td class="py-1 font-medium" id="summary-applicant-email">
                  <span id="emailDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Phone:</td>
                <td class="py-1 font-medium" id="summary-applicant-phone">
                  <span id="phoneDisplay"></span>
                </td>
              </tr>
            </table>
            <div>
              <h5 class="font-medium mb-2">Means of Identification</h5>
              <table class="w-full text-sm">
                <tr id="main-identification-row">
                  <td class="py-1 text-gray-600">ID Type:</td>
                  <td class="py-1 font-medium" id="summary-identification-type">
                    <span id="identificationTypeDisplay"></span>
                  </td>
                </tr>
                <tr id="main-identification-status-row">
                  <td class="py-1 text-gray-600">ID Document:</td>
                  <td class="py-1 font-medium" id="summary-identification-status">
                    <span id="identificationStatusDisplay"></span>
                  </td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        <!-- Multiple Owners Section (shown only for multiple owners) -->
        <div class="border border-gray-200 rounded-md p-6 mb-6" id="multiple-owners-section" style="display: none;">
          <h4 class="font-medium mb-4">Multiple Owners Information</h4>
          <div id="multiple-owners-summary" class="space-y-4">
            <!-- Dynamic content will be inserted here -->
          </div>
        </div>

        <!-- Unit Information Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6">
          <h4 class="font-medium mb-4">Unit Information</h4>
          <div class="grid grid-cols-2 gap-6">
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">Unit Type:</td>
                <td class="py-1 font-medium" id="summary-unit-type">
                  <span id="unitTypeDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Ownership Type:</td>
                <td class="py-1 font-medium" id="summary-ownership-type">
                  <span id="ownershipTypeDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Block No:</td>
                <td class="py-1 font-medium" id="summary-block-no">
                  <span id="blockNumberDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Unit Size:</td>
                <td class="py-1 font-medium" id="summary-unit-size">
                  <span id="unitSizeDisplay"></span>
                </td>
              </tr>
            </table>
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">Section (Floor) No:</td>
                <td class="py-1 font-medium" id="summary-floor-no">
                  <span id="floorNumberDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Unit No:</td>
                <td class="py-1 font-medium" id="summary-unit-no">
                  <span id="unitNumberDisplay"></span>
                </td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Address Information Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6" id="address-section">
          <h4 class="font-medium mb-4">Unit Owner's Address</h4>
          <div class="grid grid-cols-2 gap-6">
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">House No:</td>
                <td class="py-1 font-medium" id="summary-house-no">
                  <span id="houseNoDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Street Name:</td>
                <td class="py-1 font-medium" id="summary-street-name">
                  <span id="streetNameDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">District:</td>
                <td class="py-1 font-medium" id="summary-district">
                  <span id="districtDisplay"></span>
                </td>
              </tr>
            </table>
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">LGA:</td>
                <td class="py-1 font-medium" id="summary-lga">
                  <span id="lgaDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">State:</td>
                <td class="py-1 font-medium" id="summary-state">
                  <span id="stateDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Complete Address:</td>
                <td class="py-1 font-medium" id="summary-complete-address">
                  <span id="completeAddressDisplay"></span>
                </td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Shared Areas Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6">
          <h4 class="font-medium mb-4">Shared Areas</h4>
          <div id="shared-areas-summary" class="grid grid-cols-2 gap-4">
            <!-- Dynamic content will be inserted here -->
          </div>
        </div>

        <!-- Payment Information Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6">
          <h4 class="font-medium mb-4">Payment Information</h4>
          <div class="grid grid-cols-2 gap-6">
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600">Application Fee:</td>
                <td class="py-1 font-medium" id="summary-application-fee">
                  <span id="applicationFeeDisplay">₦0</span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Processing Fee:</td>
                <td class="py-1 font-medium" id="summary-processing-fee">
                  <span id="processingFeeDisplay">₦0</span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Survey Fee:</td>
                <td class="py-1 font-medium" id="summary-survey-fee">
                  <span id="surveyFeeDisplay">₦0</span>
                </td>
              </tr>
            </table>
            <table class="w-full text-sm">
              <tr>
                <td class="py-1 text-gray-600 font-medium">Total:</td>
                <td class="py-1 font-bold text-green-600" id="summary-total-fee">
                  <span id="totalFeeDisplay">₦0</span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Receipt Number:</td>
                <td class="py-1 font-medium" id="summary-receipt-number">
                  <span id="receiptNumberDisplay"></span>
                </td>
              </tr>
              <tr>
                <td class="py-1 text-gray-600">Payment Date:</td>
                <td class="py-1 font-medium" id="summary-payment-date">
                  <span id="paymentDateDisplay"></span>
                </td>
              </tr>
            </table>
          </div>
        </div>

        <!-- Comments Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6" id="comments-section" style="display: none;">
          <h4 class="font-medium mb-4">Application Comments</h4>
          <div class="bg-gray-50 p-4 rounded-md">
            <p id="commentsDisplay" class="text-sm text-gray-700"></p>
          </div>
        </div>
        
        <!-- Documents Section -->
        <div class="border border-gray-200 rounded-md p-6 mb-6" id="uploaded-documents-section">
          <h4 class="font-medium mb-4">Uploaded Documents</h4>
          <div class="grid grid-cols-2 gap-4">
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span id="applicationLetterIndicator" class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                <span>Application Letter</span>
              </div>
              <span id="applicationLetterStatus" class="text-sm text-red-600 font-medium">Not Uploaded</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span id="buildingPlanIndicator" class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                <span>Building Plan</span>
              </div>
              <span id="buildingPlanStatus" class="text-sm text-red-600 font-medium">Not Uploaded</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span id="architecturalDesignIndicator" class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                <span>Architectural Design</span>
              </div>
              <span id="architecturalDesignStatus" class="text-sm text-red-600 font-medium">Not Uploaded</span>
            </div>
            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <span id="ownershipDocumentIndicator" class="inline-block w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                <span>Ownership Document</span>
              </div>
              <span id="ownershipDocumentStatus" class="text-sm text-red-600 font-medium">Not Uploaded</span>
            </div>
          </div>
        </div>
        
        <div class="flex justify-between mt-8">
          <div class="flex space-x-4">
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-md" id="backStep4">Back</button>
            <button type="button" id="printApplicationBtn" class="px-4 py-2 bg-white border border-gray-300 rounded-md flex items-center">
              <i data-lucide="printer" class="w-4 h-4 mr-2"></i>
              Print Application Slip
            </button>
          </div>
          <div class="flex items-center">
            <span class="text-sm text-gray-500 mr-4">Step 4 of 4</span>
            <button type="submit" id="submitApplication" class="px-4 py-2 bg-black text-white rounded-md">Submit Application</button>
          </div>
        </div>
      </div>
    </div>
  </div>

<!-- Print Application Slip Template (hidden by default) -->
<div id="printTemplate" class="hidden">
  <div class="print-container">
    <div class="print-header">
      <div class="header-with-logos">
        <div class="logo-left">
          <img src="{{ asset('assets/logo/logo1.jpg') }}" alt="Nigeria Coat of Arms" class="logo-image">
        </div>
        <div class="header-text">
          <h1 class="text-xl font-bold mb-1">MINISTRY OF LAND AND PHYSICAL PLANNING</h1>
          <h2 class="text-lg font-semibold mb-4">APPLICATION FOR SECTIONAL TITLING</h2>
        </div>
        <div class="logo-right">
          <img src="{{ asset('assets/logo/logo3.jpeg') }}" alt="Ministry Logo" class="logo-image">
        </div>
      </div>
      <div class="border-b-2 border-black mb-6"></div>
    </div>

    <div class="print-body">
      <div class="mb-4">
        <h3 class="text-lg font-bold mb-2">Application Receipt</h3>
        <div class="flex justify-between mb-2">
          <span>Application ID: <span id="print-application-id">{{ $motherApplication->applicationID ?? 'N/A' }}</span></span>
          <span>Date: <span id="print-date"></span></span>
        </div>
        <div class="flex justify-between">
          <span>STFileNo: <span id="print-st-file-number"></span></span>
          <span>Land Use: <span id="print-land-use">{{ $motherApplication->land_use ?? 'N/A' }}</span></span>
        </div>
      </div>

      <div class="grid grid-cols-2 gap-6 mb-4">
        <div>
          <h4 class="font-medium mb-2 border-b border-gray-300 pb-1">Applicant Information</h4>
          <table class="w-full text-sm">
            <tr>
              <td class="py-1 text-gray-600 w-1/3">Applicant Type:</td>
              <td class="py-1 font-medium" id="print-applicant-type"></td>
            </tr>
            <tr>
              <td class="py-1 text-gray-600">Name:</td>
              <td class="py-1 font-medium" id="print-applicant-name"></td>
            </tr>
            <tr>
              <td class="py-1 text-gray-600">Email:</td>
              <td class="py-1 font-medium" id="print-email"></td>
            </tr>
            <tr>
              <td class="py-1 text-gray-600">Phone:</td>
              <td class="py-1 font-medium" id="print-phone"></td>
            </tr>
          </table>
        </div>
        
        <div>
          <h4 class="font-medium mb-2 border-b border-gray-300 pb-1">Unit Information</h4>
          <table class="w-full text-sm">
            <tr>
              <td class="py-1 text-gray-600 w-1/2">Unit Type:</td>
              <td class="py-1 font-medium" id="print-unit-type"></td>
            </tr>
            <tr>
              <td class="py-1 text-gray-600">Block No:</td>
              <td class="py-1 font-medium" id="print-block-no"></td>
            </tr>
            <tr>
              <td class="py-1 text-gray-600">Section (Floor) No:</td>
              <td class="py-1 font-medium" id="print-floor-no"></td>
            </tr>
            <tr>
              <td class="py-1 text-gray-600">Unit No:</td>
              <td class="py-1 font-medium" id="print-unit-no"></td>
            </tr>
            <tr>
              <td class="py-1 text-gray-600">Unit Size:</td>
              <td class="py-1 font-medium" id="print-unit-size"></td>
            </tr>
          </table>
        </div>
      </div>

      <div class="mb-4">
        <h4 class="font-medium mb-2 border-b border-gray-300 pb-1">Address Information</h4>
        <table class="w-full text-sm">
          <tr>
            <td class="py-1 text-gray-600 w-1/4">Complete Address:</td>
            <td class="py-1 font-medium" id="print-complete-address"></td>
          </tr>
        </table>
      </div>
      
      <div class="mb-6">
        <h4 class="font-medium mb-2 border-b border-gray-300 pb-1">Payment Information</h4>
        <table class="w-full text-sm">
          <tr>
            <td class="py-1 text-gray-600 w-1/4">Application Fee:</td>
            <td class="py-1 font-medium" id="print-application-fee"></td>
          </tr>
          <tr>
            <td class="py-1 text-gray-600">Processing Fee:</td>
            <td class="py-1 font-medium" id="print-processing-fee"></td>
          </tr>
          <tr>
            <td class="py-1 text-gray-600">Survey Fee:</td>
            <td class="py-1 font-medium" id="print-survey-fee"></td>
          </tr>
          <tr>
            <td class="py-1 text-gray-600 font-medium">Total:</td>
            <td class="py-1 font-bold" id="print-total-fee"></td>
          </tr>
          <tr>
            <td class="py-1 text-gray-600">Receipt Number:</td>
            <td class="py-1 font-medium" id="print-receipt-number"></td>
          </tr>
          <tr>
            <td class="py-1 text-gray-600">Payment Date:</td>
            <td class="py-1 font-medium" id="print-payment-date"></td>
          </tr>
        </table>
      </div>

      <div class="mb-6 grid grid-cols-2 gap-4">
        <div>
          <h4 class="font-medium mb-2 border-b border-gray-300 pb-1">Required Documents</h4>
          <ul class="list-disc pl-5">
            <li class="py-1" id="print-doc-application-letter">Application Letter</li>
            <li class="py-1" id="print-doc-building-plan">Building Plan</li>
            <li class="py-1" id="print-doc-architectural-design">Architectural Design</li>
            <li class="py-1" id="print-doc-ownership-document">Ownership Document</li>
          </ul>
        </div>
        <div>
          <h4 class="font-medium mb-2 border-b border-gray-300 pb-1">For Official Use Only</h4>
          <div class="mt-4">
            <div class="border-t border-gray-300 pt-4 mt-4">
              <div class="text-center">
                <p>Signature & Stamp</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="print-footer mt-6 text-center text-sm">
      <p>This is an official application receipt. Please keep for your records.</p>
      <p>Application submitted on: <span id="print-submission-date"></span></p>
    </div>
  </div>
</div>

<style>
@page {
    size: A4 landscape;
    margin: 8mm;
}

.header-with-logos {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 10px;
}

.logo-left, .logo-right {
    flex: 0 0 60px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.header-text {
    flex: 1;
    text-align: center;
    padding: 0 15px;
}

.logo-image {
    width: 50px;
    height: 50px;
    object-fit: contain;
}

@media print {
    html, body {
        margin: 0;
        padding: 0;
        width: 297mm;
        height: 210mm;
        overflow: hidden;
    }
    body * {
      visibility: hidden;
    }
    #printTemplate, #printTemplate * {
      visibility: visible;
    }
    #printTemplate {
      position: absolute;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      display: block !important;
      box-sizing: border-box;
      page-break-inside: avoid;
      page-break-after: avoid;
      page-break-before: avoid;
    }
    .print-container {
      padding: 6mm;
      width: 100%;
      height: 100%;
      box-sizing: border-box;
      display: flex;
      flex-direction: column;
      page-break-inside: avoid;
    }
    .print-header {
        flex-shrink: 0;
        margin-bottom: 6px;
    }
    .print-body {
        flex: 1;
        font-size: 9px;
        line-height: 1.1;
        overflow: hidden;
    }
    .print-footer {
        flex-shrink: 0;
        margin-top: 6px;
        font-size: 8px;
    }
    .header-with-logos {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 6px;
        width: 100%;
    }
    .logo-left, .logo-right {
        flex: 0 0 60px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .header-text {
        flex: 1;
        text-align: center;
        padding: 0 15px;
    }
    .header-text h1 {
        font-size: 14px;
        margin-bottom: 2px;
        font-weight: bold;
    }
    .header-text h2 {
        font-size: 12px;
        margin-bottom: 0;
        font-weight: 600;
    }
    .logo-image {
        width: 50px;
        height: 50px;
        object-fit: contain;
        display: block;
    }
    .no-print {
      display: none;
    }
    
    /* Compact layout for single page */
    .grid.grid-cols-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 4px;
    }
    
    .print-body h3 {
        font-size: 11px;
        margin-bottom: 4px;
        font-weight: bold;
    }
    
    .print-body h4 {
        font-size: 10px;
        margin-bottom: 3px;
        border-bottom: 1px solid #ccc;
        padding-bottom: 1px;
        font-weight: 600;
    }
    
    .print-body table {
        font-size: 9px;
        line-height: 1.0;
        width: 100%;
    }
    
    .print-body table td {
        padding: 0.5px 0;
        vertical-align: top;
    }
    
    .print-body .mb-4 {
        margin-bottom: 4px;
    }
    
    .print-body .mb-6 {
        margin-bottom: 6px;
    }
    
    .border-b-2 {
        border-bottom: 1px solid #000;
        margin-bottom: 4px;
    }
    
    /* Reduce spacing for compact layout */
    .print-body > div {
        margin-bottom: 3px;
    }
    
    .print-body ul {
        margin: 0;
        padding-left: 12px;
    }
    
    .print-body ul li {
        margin-bottom: 1px;
        font-size: 8px;
        line-height: 1.0;
    }
    
    /* Flex layout for better space utilization */
    .print-body .mb-6.grid.grid-cols-2 {
        display: flex;
        gap: 10px;
    }
    
    .print-body .mb-6.grid.grid-cols-2 > div {
        flex: 1;
    }
    
    /* Compact the main content sections */
    .print-body > .mb-4:first-child {
        margin-bottom: 3px;
    }
    
    .print-body > .grid.grid-cols-2.gap-6.mb-4 {
        margin-bottom: 3px;
        gap: 8px;
    }
    
    .print-body > .mb-4:nth-child(3) {
        margin-bottom: 3px;
    }
    
    .print-body > .mb-6:last-of-type {
        margin-bottom: 3px;
    }
    
    /* Ensure content fits in available space */
    .print-body .flex.justify-between {
        font-size: 9px;
    }
    
    .print-body span {
        font-size: inherit;
    }
}
</style>

<script>
// Function to update the summary
function updateApplicationSummary() {
  // File Information
  const stFileNo = document.querySelector('input[name="fileno"]')?.value || 'N/A';
  const schemeNo = document.querySelector('input[name="scheme_no"]')?.value || 'N/A';
  const mainId = document.getElementById('mainIdHidden')?.value || 'N/A';
  
  document.getElementById('stFileNumberDisplay').textContent = stFileNo;
  document.getElementById('schemeNoDisplay').textContent = schemeNo;
  document.getElementById('mainIdDisplay').textContent = mainId;
  
  // Applicant Information
  const applicantType = document.querySelector('input[name="applicantType"]:checked')?.value || 'Individual';
  document.getElementById('applicantTypeDisplay').textContent = applicantType.charAt(0).toUpperCase() + applicantType.slice(1);
  
  // Show/hide sections based on applicant type
  const individualRows = document.querySelectorAll('#individual-name-row');
  const corporateRows = document.querySelectorAll('#corporate-name-row, #corporate-rc-row');
  const multipleOwnersSection = document.getElementById('multiple-owners-section');
  const addressSection = document.getElementById('address-section');
  const mainIdentificationRows = document.querySelectorAll('#main-identification-row, #main-identification-status-row');
  const applicantInfoSection = document.getElementById('applicant-info-section');
  const uploadedDocumentsSection = document.getElementById('uploaded-documents-section');
  
  if (applicantType === 'individual') {
    individualRows.forEach(row => row.style.display = '');
    corporateRows.forEach(row => row.style.display = 'none');
    multipleOwnersSection.style.display = 'none';
    addressSection.style.display = 'block';
    mainIdentificationRows.forEach(row => row.style.display = '');
    applicantInfoSection.style.display = 'block';
    uploadedDocumentsSection.style.display = 'block';
    
    // Individual name
    const title = document.getElementById('applicantTitle')?.value || '';
    const firstName = document.getElementById('applicantName')?.value || '';
    const middleName = document.getElementById('applicantMiddleName')?.value || '';
    const surname = document.getElementById('applicantSurname')?.value || '';
    
    let fullName = '';
    if (title) fullName += title + ' ';
    if (firstName) fullName += firstName + ' ';
    if (middleName) fullName += middleName + ' ';
    if (surname) fullName += surname;
    
    document.getElementById('applicantNameDisplay').textContent = fullName.trim() || 'N/A';
    
  } else if (applicantType === 'corporate') {
    individualRows.forEach(row => row.style.display = 'none');
    corporateRows.forEach(row => row.style.display = '');
    multipleOwnersSection.style.display = 'none';
    addressSection.style.display = 'block';
    mainIdentificationRows.forEach(row => row.style.display = '');
    applicantInfoSection.style.display = 'block';
    uploadedDocumentsSection.style.display = 'block';
    
    // Corporate information
    document.getElementById('corporateNameDisplay').textContent = document.getElementById('corporateName')?.value || 'N/A';
    document.getElementById('rcNumberDisplay').textContent = document.getElementById('rcNumber')?.value || 'N/A';
    
  } else if (applicantType === 'multiple') {
    individualRows.forEach(row => row.style.display = 'none');
    corporateRows.forEach(row => row.style.display = 'none');
    multipleOwnersSection.style.display = 'block';
    addressSection.style.display = 'none';
    mainIdentificationRows.forEach(row => row.style.display = 'none');
    applicantInfoSection.style.display = 'none';
    uploadedDocumentsSection.style.display = 'none';
    
    // Multiple owners information
    updateMultipleOwnersSummary();
  }
  
  // Main identification information (for individual and corporate)
  if (applicantType !== 'multiple') {
    const identificationType = document.querySelector('input[name="identification_type"]:checked')?.value || 'N/A';
    document.getElementById('identificationTypeDisplay').textContent = identificationType.charAt(0).toUpperCase() + identificationType.slice(1);
    
    const identificationFile = document.getElementById('identification_image');
    const hasIdentificationFile = identificationFile && identificationFile.files && identificationFile.files.length > 0;
    document.getElementById('identificationStatusDisplay').innerHTML = hasIdentificationFile ? 
      '<span class="text-green-600">Uploaded</span>' : '<span class="text-red-600">Not Uploaded</span>';
  }
  
  // Contact Information
  document.getElementById('emailDisplay').textContent = document.querySelector('input[name="owner_email"]')?.value || 'N/A';
  const phoneInputs = document.querySelectorAll('input[name="phone_number[]"]');
  const phoneNumbers = Array.from(phoneInputs).map(input => input.value).filter(value => value);
  document.getElementById('phoneDisplay').textContent = phoneNumbers.join(', ') || 'N/A';
  
  // Unit Information
  updateUnitInformation();
  
  // Address Information (only for individual and corporate)
  if (applicantType !== 'multiple') {
    updateAddressInformation();
  }
  
  // Shared Areas
  updateSharedAreasSummary();
  
  // Payment Information
  updatePaymentInformation();
  
  // Comments
  updateComments();
  
  // Documents
  updateDocumentIndicators();
}

function updateMultipleOwnersSummary() {
  const ownersContainer = document.getElementById('multiple-owners-summary');
  const ownerNameInputs = document.querySelectorAll('input[name="multiple_owners_names[]"]');
  const ownerAddressInputs = document.querySelectorAll('textarea[name="multiple_owners_address[]"]');
  const ownerEmailInputs = document.querySelectorAll('input[name="multiple_owners_email[]"]');
  const ownerPhoneInputs = document.querySelectorAll('input[name="multiple_owners_phone[]"]');
  
  let summaryHTML = '';
  
  ownerNameInputs.forEach((nameInput, index) => {
    const name = nameInput.value || `Owner ${index + 1}`;
    const address = ownerAddressInputs[index]?.value || 'N/A';
    const email = ownerEmailInputs[index]?.value || 'N/A';
    const phone = ownerPhoneInputs[index]?.value || 'N/A';
    
    // Get identification type for this owner
    const identificationTypeInput = document.querySelector(`input[name="multiple_owners_identification_type_${index}"]:checked`);
    const identificationType = identificationTypeInput?.value || 'N/A';
    
    // Check if identification file is uploaded
    const identificationFileInputs = document.querySelectorAll('input[name="multiple_owners_identification_image[]"]');
    const hasIdentificationFile = identificationFileInputs[index] && identificationFileInputs[index].files && identificationFileInputs[index].files.length > 0;
    
    summaryHTML += `
      <div class="border border-gray-100 rounded-md p-4 bg-gray-50">
        <h6 class="font-medium text-sm mb-2">${name}</h6>
        <div class="grid grid-cols-2 gap-4 text-xs">
          <div>
            <span class="text-gray-600">Address:</span>
            <p class="font-medium mb-2">${address}</p>
            <span class="text-gray-600">Email:</span>
            <p class="font-medium mb-2">${email}</p>
            <span class="text-gray-600">Phone:</span>
            <p class="font-medium">${phone}</p>
          </div>
          <div>
            <span class="text-gray-600">ID Type:</span>
            <p class="font-medium">${identificationType.charAt(0).toUpperCase() + identificationType.slice(1)}</p>
            <span class="text-gray-600">ID Document:</span>
            <p class="font-medium ${hasIdentificationFile ? 'text-green-600' : 'text-red-600'}">
              ${hasIdentificationFile ? 'Uploaded' : 'Not Uploaded'}
            </p>
          </div>
        </div>
      </div>
    `;
  });
  
  ownersContainer.innerHTML = summaryHTML || '<p class="text-gray-500">No owners added yet</p>';
}

function updateUnitInformation() {
  // Determine unit type based on land use and selected options
  const landUse = document.getElementById('landUseDisplay').textContent;
  let unitType = 'N/A';
  
  if (landUse.includes('Residential') || landUse.includes('Mixed')) {
    unitType = document.querySelector('input[name="residence_type"]:checked')?.value || 'N/A';
  } else if (landUse.includes('Commercial')) {
    unitType = document.querySelector('input[name="commercial_type"]:checked')?.value || 'N/A';
  } else if (landUse.includes('Industrial')) {
    unitType = document.querySelector('input[name="industrial_type"]:checked')?.value || 'N/A';
  }
  
  document.getElementById('unitTypeDisplay').textContent = unitType;
  
  // Ownership type
  const ownershipType = document.querySelector('input[name="ownershipType"]:checked')?.value || 'N/A';
  const otherOwnership = document.querySelector('input[name="otherOwnership"]')?.value;
  const finalOwnershipType = ownershipType === 'others' && otherOwnership ? otherOwnership : ownershipType;
  document.getElementById('ownershipTypeDisplay').textContent = finalOwnershipType;
  
  // Unit details
  document.getElementById('blockNumberDisplay').textContent = document.querySelector('input[name="block_number"]')?.value || 'N/A';
  document.getElementById('floorNumberDisplay').textContent = document.querySelector('input[name="floor_number"]')?.value || 'N/A';
  document.getElementById('unitNumberDisplay').textContent = document.querySelector('input[name="unit_number"]')?.value || 'N/A';
  document.getElementById('unitSizeDisplay').textContent = document.querySelector('input[name="unit_size"]')?.value || 'N/A';
}

function updateAddressInformation() {
  const houseNo = document.querySelector('input[name="address_house_no"]')?.value || '';
  const streetName = document.querySelector('input[name="address_street_name"]')?.value || '';
  const district = document.querySelector('input[name="address_district"]')?.value || '';
  const lga = document.querySelector('select[name="address_lga"]')?.value || '';
  const state = document.querySelector('select[name="address_state"]')?.value || '';
  
  document.getElementById('houseNoDisplay').textContent = houseNo || 'N/A';
  document.getElementById('streetNameDisplay').textContent = streetName || 'N/A';
  document.getElementById('districtDisplay').textContent = district || 'N/A';
  document.getElementById('lgaDisplay').textContent = lga || 'N/A';
  document.getElementById('stateDisplay').textContent = state || 'N/A';
  
  // Construct complete address
  const addressParts = [houseNo, streetName, district, lga, state].filter(part => part);
  const completeAddress = addressParts.join(', ');
  document.getElementById('completeAddressDisplay').textContent = completeAddress || 'N/A';
}

function updateSharedAreasSummary() {
  const sharedAreasContainer = document.getElementById('shared-areas-summary');
  const sharedAreaCheckboxes = document.querySelectorAll('input[name="shared_areas[]"]:checked');
  const otherAreasTextarea = document.getElementById('other_areas_detail');
  
  let areasHTML = '';
  
  sharedAreaCheckboxes.forEach(checkbox => {
    areasHTML += `<div class="flex items-center"><span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span><span class="text-sm">${checkbox.value}</span></div>`;
  });
  
  if (otherAreasTextarea && otherAreasTextarea.value.trim()) {
    areasHTML += `<div class="flex items-center"><span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span><span class="text-sm">Other: ${otherAreasTextarea.value}</span></div>`;
  }
  
  sharedAreasContainer.innerHTML = areasHTML || '<p class="text-gray-500 text-sm">No shared areas selected</p>';
}

function updatePaymentInformation() {
  const applicationFee = parseFloat(document.querySelector('input[name="application_fee"]')?.value || 0);
  const processingFee = parseFloat(document.querySelector('input[name="processing_fee"]')?.value || 0);
  const surveyFee = parseFloat(document.querySelector('input[name="survey_fee"]')?.value || 0);
  
  document.getElementById('applicationFeeDisplay').textContent = '₦' + applicationFee.toLocaleString();
  document.getElementById('processingFeeDisplay').textContent = '₦' + processingFee.toLocaleString();
  document.getElementById('surveyFeeDisplay').textContent = '₦' + surveyFee.toLocaleString();
  
  const totalFee = applicationFee + processingFee + surveyFee;
  document.getElementById('totalFeeDisplay').textContent = '₦' + totalFee.toLocaleString();
  
  document.getElementById('receiptNumberDisplay').textContent = document.querySelector('input[name="receipt_number"]')?.value || 'N/A';
  
  // Format date
  const paymentDateInput = document.querySelector('input[name="payment_date"]')?.value;
  let formattedDate = 'N/A';
  if (paymentDateInput) {
    const date = new Date(paymentDateInput);
    formattedDate = new Intl.DateTimeFormat('en-US', {
      month: 'numeric',
      day: 'numeric',
      year: 'numeric'
    }).format(date);
  }
  document.getElementById('paymentDateDisplay').textContent = formattedDate;
}

function updateComments() {
  const comments = document.querySelector('textarea[name="application_comment"]')?.value;
  const commentsSection = document.getElementById('comments-section');
  
  if (comments && comments.trim()) {
    document.getElementById('commentsDisplay').textContent = comments;
    commentsSection.style.display = 'block';
  } else {
    commentsSection.style.display = 'none';
  }
}

// Update document indicators based on file uploads
function updateDocumentIndicators() {
  const documents = [
    { id: 'application_letter', indicator: 'applicationLetterIndicator', status: 'applicationLetterStatus' },
    { id: 'building_plan', indicator: 'buildingPlanIndicator', status: 'buildingPlanStatus' },
    { id: 'architectural_design', indicator: 'architecturalDesignIndicator', status: 'architecturalDesignStatus' },
    { id: 'ownership_document', indicator: 'ownershipDocumentIndicator', status: 'ownershipDocumentStatus' }
  ];
  
  documents.forEach(doc => {
    const fileInput = document.getElementById(doc.id);
    const indicator = document.getElementById(doc.indicator);
    const statusElement = document.getElementById(doc.status);
    
    if (fileInput && fileInput.files && fileInput.files.length > 0) {
      indicator.classList.remove('bg-red-500');
      indicator.classList.add('bg-green-500');
      if (statusElement) {
        statusElement.textContent = 'Uploaded';
        statusElement.classList.remove('text-red-600');
        statusElement.classList.add('text-green-600');
      }
    } else {
      indicator.classList.remove('bg-green-500');
      indicator.classList.add('bg-red-500');
      if (statusElement) {
        statusElement.textContent = 'Not Uploaded';
        statusElement.classList.remove('text-green-600');
        statusElement.classList.add('text-red-600');
      }
    }
  });
}

// Initialize form event listeners to update summary
document.addEventListener('DOMContentLoaded', function() {
  // Update summary when the "Next" button on step 3 is clicked
  const nextStep3Button = document.getElementById('nextStep3');
  if (nextStep3Button) {
    nextStep3Button.addEventListener('click', updateApplicationSummary);
  }
  
  // Add event listeners for file uploads to update document indicators in real-time
  const fileInputs = [
    'application_letter',
    'building_plan', 
    'architectural_design',
    'ownership_document'
  ];
  
  fileInputs.forEach(inputId => {
    const fileInput = document.getElementById(inputId);
    if (fileInput) {
      fileInput.addEventListener('change', updateDocumentIndicators);
    }
  });
  
  // Initialize document indicators on page load
  updateDocumentIndicators();
  
  // Set up event listeners for form fields to update address preview
  const addressFields = ['ownerHouseNo', 'ownerStreetName', 'ownerDistrict', 'ownerLga', 'ownerState'];
  addressFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
      field.addEventListener('input', function() {
        const houseNo = document.getElementById('ownerHouseNo')?.value || '';
        const streetName = document.getElementById('ownerStreetName')?.value || '';
        const district = document.getElementById('ownerDistrict')?.value || '';
        const lga = document.getElementById('ownerLga')?.value || '';
        const state = document.getElementById('ownerState')?.value || '';
        
        const parts = [houseNo, streetName, district, lga, state].filter(part => part);
        const fullAddress = parts.join(', ');
        
        const fullContactAddressEl = document.getElementById('fullContactAddress');
        const contactAddressHiddenEl = document.getElementById('contactAddressHidden');
        
        if (fullContactAddressEl) fullContactAddressEl.textContent = fullAddress;
        if (contactAddressHiddenEl) contactAddressHiddenEl.value = fullAddress;
      });
    }
  });
  
  // Listen to changes in fee fields to update total
  const feeFields = ['application_fee', 'processing_fee', 'survey_fee'];
  feeFields.forEach(fieldName => {
    const field = document.querySelector(`input[name="${fieldName}"]`);
    if (field) {
      field.addEventListener('input', function() {
        const applicationFee = parseFloat(document.querySelector('input[name="application_fee"]')?.value || 0);
        const processingFee = parseFloat(document.querySelector('input[name="processing_fee"]')?.value || 0);
        const surveyFee = parseFloat(document.querySelector('input[name="survey_fee"]')?.value || 0);
        
        const totalFee = applicationFee + processingFee + surveyFee;
        const totalFeeEl = document.querySelector('.flex.justify-between.items-center.mb-4 span.font-bold');
        if (totalFeeEl) {
          totalFeeEl.textContent = '₦' + totalFee.toLocaleString();
        }
      });
    }
  });
  
  // Add print functionality
  document.getElementById('printApplicationBtn').addEventListener('click', function() {
    // Populate the print template with current values
    document.getElementById('print-date').textContent = new Date().toLocaleDateString();
    document.getElementById('print-submission-date').textContent = new Date().toLocaleString();
    document.getElementById('print-st-file-number').textContent = document.getElementById('stFileNumberDisplay').textContent;
    
    // Applicant Information
    document.getElementById('print-applicant-type').textContent = document.getElementById('applicantTypeDisplay').textContent;
    document.getElementById('print-applicant-name').textContent = document.getElementById('applicantNameDisplay').textContent;
    document.getElementById('print-email').textContent = document.getElementById('emailDisplay').textContent;
    document.getElementById('print-phone').textContent = document.getElementById('phoneDisplay').textContent;
    
    // Unit Information
    document.getElementById('print-unit-type').textContent = document.getElementById('unitTypeDisplay').textContent;
    document.getElementById('print-block-no').textContent = document.getElementById('blockNumberDisplay').textContent;
    document.getElementById('print-floor-no').textContent = document.getElementById('floorNumberDisplay').textContent;
    document.getElementById('print-unit-no').textContent = document.getElementById('unitNumberDisplay').textContent;
    document.getElementById('print-unit-size').textContent = document.getElementById('unitSizeDisplay').textContent;
    
    // Address Information
    document.getElementById('print-complete-address').textContent = document.getElementById('completeAddressDisplay').textContent;
    
    // Payment Information
    document.getElementById('print-application-fee').textContent = document.getElementById('applicationFeeDisplay').textContent;
    document.getElementById('print-processing-fee').textContent = document.getElementById('processingFeeDisplay').textContent;
    document.getElementById('print-survey-fee').textContent = document.getElementById('surveyFeeDisplay').textContent;
    document.getElementById('print-total-fee').textContent = document.getElementById('totalFeeDisplay').textContent;
    document.getElementById('print-receipt-number').textContent = document.getElementById('receiptNumberDisplay').textContent;
    document.getElementById('print-payment-date').textContent = document.getElementById('paymentDateDisplay').textContent;
    
    // Document status
    const docElements = [
      { id: 'applicationLetterIndicator', printId: 'print-doc-application-letter' },
      { id: 'buildingPlanIndicator', printId: 'print-doc-building-plan' },
      { id: 'architecturalDesignIndicator', printId: 'print-doc-architectural-design' },
      { id: 'ownershipDocumentIndicator', printId: 'print-doc-ownership-document' }
    ];
    
    docElements.forEach(doc => {
      const indicator = document.getElementById(doc.id);
      const printElement = document.getElementById(doc.printId);
      
      if (indicator.classList.contains('bg-green-500')) {
        printElement.innerHTML = printElement.innerHTML + ' <span class="text-green-600">(Uploaded)</span>';
      } else {
        printElement.innerHTML = printElement.innerHTML + ' <span class="text-red-600">(Not Uploaded)</span>';
      }
    });
    
    // Trigger print
    window.print();
  });
});
</script>