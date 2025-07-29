<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kano State Ministry Document</title>

  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Add Alpine.js -->
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <!-- Add SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@include('actions.parts.pp_css')
    <style>
        /* Print styles for A4 and better margins */
        @media print {
            html, body {
                width: 210mm;
                height: 297mm;
                margin: 0;
                padding: 0;
                font-size: 11pt !important;
                background: #fff !important;
            }
            body {
                box-sizing: border-box;
                margin: 0 !important;
                padding: 0 !important;
            }
            .container {
                width: 100%;
                max-width: 180mm;
                margin: 0 auto !important;
                padding: 18mm 15mm 15mm 15mm !important; /* top, right, bottom, left */
                background: #fff !important;
            }
            .tables-section {
                page-break-before: always;
            }
            .mb-8 { margin-bottom: 0.5rem !important; }
            .mb-6 { margin-bottom: 0.4rem !important; }
            .mb-4 { margin-bottom: 0.3rem !important; }
            #dimensions-table, #dimensions-table th, #dimensions-table td,
            #utilities-table, #utilities-table th, #utilities-table td {
                margin: 0.3rem 0 !important;
                padding: 3px !important;
            }
            #dimensions-table th, #dimensions-table td {
                border: 1px solid #ddd;
            }
            .no-print, .btn, button {
                display: none !important;
            }
        }

        /* Responsive layout */
        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            background: #fff;
        }

        @media screen and (max-width: 768px) {
            .container { padding: 0.5rem; }
            table { 
                display: block;
                overflow-x: auto;
                font-size: 0.95rem;
            }
            .header-section { text-align: center; }
            .flex-wrap-mobile { flex-wrap: wrap; }
        }

        #dimensions-table {
            width: 100%;
            border-collapse: collapse;
        }
        #dimensions-table th,
        #dimensions-table td {
            border: 1px solid #ddd;
            padding: 0.5rem;
        }

        /* Do NOT style #utilities-table input */

        .details-section {
            line-height: 1.4;
            margin-bottom: 1rem;
        }

        .signature-section {
            margin-top: 2rem;
            page-break-inside: avoid;
        }

        /* Signature section styling */
        .signature-section .border-t {
            border-top: 1px solid #6b7280;
        }

        .signature-section p {
            margin: 0.25rem 0;
        }

        /* Print-specific signature styling */
        @media print {
            .signature-section {
                margin-top: 2rem;
                page-break-inside: avoid;
            }
            
            .signature-section .mb-8 {
                margin-bottom: 2rem !important;
            }
            
            .signature-section .mb-4 {
                margin-bottom: 1rem !important;
            }
            
            .signature-section .border-t {
                border-top: 1px solid #000 !important;
            }
        }
    </style>
</head>
<body class="bg-white" data-planning-content data-application-id="{{ $application->id }}" {{ request()->query('url') == 'print' ? 'data-print-page="true"' : '' }}
  x-data="{
    applicationId: '{{ $application->id }}',
    showDimensionModal: false,
    showUtilityModal: false,
    dimensions: [],
    utilities: [],
    currentDimension: { id: '', description: '', dimension: '', order: '' },
    currentUtility: { id: '', utility_type: '', dimension: '', count: '', order: '' },
    editMode: false,

    loadDimensions() {
        this.dimensions = []; // Clear existing dimensions
        console.log('Loading dimensions for application:', this.applicationId);
        
        axios.get(`{{ url('planning-tables/dimensions') }}/${this.applicationId}`)
            .then(response => {
                const uniqueDimensions = [];
                const seenKeys = new Set(); // Use a Set to track unique keys

                response.data.forEach(dim => {
                    const key = `${dim.description}-${dim.dimension}`; // Create unique key
                    if (!seenKeys.has(key)) {
                        uniqueDimensions.push(dim);
                        seenKeys.add(key);
                    }
                });

                this.dimensions = uniqueDimensions;
                console.log('Final unique dimensions:', this.dimensions);
            })
            .catch(error => {
                console.error('Error loading dimensions:', error);
            });
    },
    
    loadUtilities() {
      const url = `{{ url('planning-tables/utilities') }}/${this.applicationId}`;
      console.log('Loading utilities from:', url);
      
      axios.get(url)
        .then(response => {
          console.log('Received utilities data:', response.data);
          // Explicitly set as a new array to trigger reactivity
          this.utilities = [...response.data];
          console.log('Utilities array after update:', this.utilities);
        })
        .catch(error => {
          console.error('Error loading utilities:', error);
        });
    },
    
    openDimensionModal(dimension = null) {
      if (dimension) {
        this.currentDimension = { ...dimension };
      } else {
        this.currentDimension = { id: '', description: '', dimension: '', order: '' };
      }
      this.showDimensionModal = true;
    },
    
    openUtilityModal(utility = null) {
      if (utility) {
        this.currentUtility = { ...utility };
      } else {
        this.currentUtility = { id: '', utility_type: '', dimension: '', count: '', order: '' };
      }
      this.showUtilityModal = true;
    },
    
    saveDimension() {
      const data = {
        ...this.currentDimension,
        application_id: this.applicationId
      };
      
      axios.post('{{ route('planning-tables.save-dimension') }}', data)
        .then(response => {
          this.showDimensionModal = false;
          this.loadDimensions();
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Dimension saved successfully',
            timer: 1500
          });
        })
        .catch(error => {
          console.error('Error saving dimension:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to save dimension. Please try again.'
          });
        });
    },
    
    saveUtility() {
      const data = {
        ...this.currentUtility,
        application_id: this.applicationId
      };
      
      axios.post('{{ route('planning-tables.save-utility') }}', data)
        .then(response => {
          this.showUtilityModal = false;
          this.loadUtilities();
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Utility saved successfully',
            timer: 1500
          });
        })
        .catch(error => {
          console.error('Error saving utility:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to save utility. Please try again.'
          });
        });
    },
    
    deleteDimension(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this dimension?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete('{{ route('planning-tables.delete-dimension') }}', {
            data: { id }
          })
            .then(response => {
              this.loadDimensions();
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Dimension has been deleted.',
                timer: 1500
              });
            })
            .catch(error => {
              console.error('Error deleting dimension:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete dimension. Please try again.'
              });
            });
        }
      });
    },
    
    deleteUtility(id) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'You want to delete this utility?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.isConfirmed) {
          axios.delete('{{ route('planning-tables.delete-utility') }}', {
            data: { id }
          })
            .then(response => {
              this.loadUtilities();
              Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Utility has been deleted.',
                timer: 1500
              });
            })
            .catch(error => {
              console.error('Error deleting utility:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete utility. Please try again.'
              });
            });
        }
      });
    },
    
    debugSharedAreas() {
      const url = `{{ url('planning-tables/debug-shared-areas') }}/${this.applicationId}`;
      axios.get(url)
        .then(response => {
          console.log('Shared areas debug data:', response.data);
          Swal.fire({
            icon: 'info',
            title: 'Shared Areas Data',
            text: JSON.stringify(response.data, null, 2),
            customClass: {
              content: 'text-left'
            }
          });
        })
        .catch(error => {
          console.error('Error fetching debug data:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error fetching shared areas debug data. See console for details.'
          });
        });
    }
  }"
  x-init="() => {
    console.log('Alpine init starting');
    loadDimensions();
    loadUtilities();
  }"
>
  <div class="container">
    <!-- Header Section -->
    <header class="text-center mb-8">
      <img src="{{ asset('assets/logo/logo3.jpeg') }}" alt="Kano State Coat of Arms" class="w-24 h-24 mx-auto mb-4">
      <h1 class="text-xl font-bold uppercase mb-2">Kano State Ministry of Land and Physical Planning</h1>
      <p class="text-sm">No 2 Dr Bala Muhammad Road</p>
      <p class="text-sm">Physical Planning Department</p>
    </header>

    <!-- Reference and Date Section -->
    <div class="flex justify-between items-center mb-6 flex-wrap">
      <div>
        <p class="text-sm">REF............./............./.............</p>
      </div>
      <div>
        <p class="text-sm">DATE {{$application->planning_approval_date }}</p>
      </div>
    </div>

    <!-- Content Sections -->
    <div class="details-section">
      <!-- Applicant Information Section -->
      <div class="mb-6">
        <p class="text-sm mb-1">NAME OF APPLICANT:  {{ strtoupper($application->applicant_title ?? 'N/A') }} {{ strtoupper($application->first_name ?? 'N/A') }} {{ strtoupper($application->surname ?? 'N/A') }}</p>
        <p class="text-sm mb-4">ADDRESS:  {{ strtoupper($application->address ?? 'N/A') }}</p>
      </div>

      <!-- RE: Application Section -->
      <div class="mb-6">
        <p class="text-sm font-bold">RE: APPLICATION FOR SECTIONAL TITLE OVER LKN NO {{ strtoupper($surveyRecord->tp_plan_no ?? 'N/A') }}  WITH SECTIONAL TITLE APPLICATION NO:{{ strtoupper($application->fileno ?? 'N/A') }} AT {{ strtoupper($application->property_house_no ?? 'N/A') }} {{ strtoupper($application->property_plot_no ?? 'N/A') }} {{ strtoupper($application->property_street_name ?? 'N/A') }} {{ strtoupper($application->property_lga ?? 'N/A') }}  PART OF {{ strtoupper($surveyRecord->approved_plan_no ?? 'N/A') }} WITH TP NO: {{($surveyRecord->tp_plan_no ?? 'N/A') }} AND SCHEME PLAN NO {{ strtoupper($application->scheme_no ?? 'N/A') }}</p>
        <p class="text-sm text-justify mt-2">
          Reference to your letter dated {{$application->planning_approval_date ?? 'N/A' }} related to the above subject I am directed to convey the Approval for fragmentation of
          LKN {{ $surveyRecord->tp_plan_no ?? 'N/A' }}  in to multiple units/sections as described in table A, with approved shared compound of @php
            $utilities = DB::connection('sqlsrv')->table('shared_utilities')
            ->where('application_id', $application->id)
            ->get();
            
            $utilityTypes = [];
            foreach($utilities as $utility) {
            $utilityTypes[] = $utility->utility_type;
            }
            
            echo !empty($utilityTypes) ? implode(', ', $utilityTypes) : 'N/A';
          @endphp and utilities
          described in table B. The scheme and its fragmentation are suitable for environment where it is located.
        </p> 
      </div>
    </div>

    <!-- Tables Section -->
    <div class="tables-section space-y-8">
      <!-- Table A Section -->  
      <div class="mb-4">
         @if(request()->query('url') == 'recommendation')
        <div class="flex justify-between items-center mb-2">
          <button @click="openDimensionModal()" class="btn btn-primary">Add Dimension</button>
        </div>
        @endif
      <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
          <p class="text-sm font-bold">TABLE A: APPROVED SITE PLAN DIMENSIONS</p>
        </div>
        <table class="w-full mb-4" id="dimensions-table">
          <thead>
            <tr class="bg-gray-200">
              <th class="w-1/6">SN</th>
              <th class="w-1/3">DESCRIPTION</th>
              <th class="w-1/2">DIMENSION (m)</th>
            </tr>
          </thead>
          <tbody>
            <template x-if="dimensions.length === 0">
              <tr>
                <td colspan="3" class="text-center py-4">No dimensions added yet.</td>
              </tr>
            </template>
            <template x-for="(dimension, index) in dimensions" :key="dimension.description + '-' + dimension.dimension">
              <tr>
                <td x-text="index + 1"></td>
                <td x-text="dimension.description"></td>
                <td x-text="parseFloat(dimension.dimension).toFixed(2)"></td>
              </tr>
            </template>
          </tbody>
        </table>
      </div>

     
      <!-- Dimension Modal - Completely rebuilt -->
      <div x-cloak x-show="showDimensionModal" class="alpine-modal" x-transition>
        <div class="alpine-modal-content" @click.outside="showDimensionModal = false">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-bold">Add/Edit Site Plan Dimension</h2>
            <button @click="showDimensionModal = false" class="text-gray-500 hover:text-gray-700">&times;</button>
          </div>
          
          <form @submit.prevent="saveDimension()">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
              <input type="text" x-model="currentDimension.description" class="w-full p-2 border border-gray-300 rounded">
            </div>
            
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-1">Dimension (m):</label>
              <input type="number" x-model="currentDimension.dimension" step="0.01" class="w-full p-2 border border-gray-300 rounded">
            </div>
            
            <!-- Display Order field removed -->
            <input type="hidden" x-model="currentDimension.order" value="0">
            
            <div class="flex justify-end space-x-2">
              <button type="button" @click="showDimensionModal = false" class="px-4 py-2 bg-gray-200 text-gray-700 rounded">Cancel</button>
              <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded">Save</button>
            </div>
          </form>
        </div>
      </div>


      <!-- Table B Section -->
      <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
          <p class="text-sm font-bold">TABLE B: ARC DESIGN SHARED UTILITIES</p>
        </div>
        <!-- Initialize server utilities data BEFORE rendering the table -->
        @php
        $serverUtilities = [];
        try {
          // Get existing utilities from the database - log to debug
          \Log::info("Retrieving utilities for application: {$application->id}");
          
          // Direct query using DB facade for maximum compatibility
          $dbUtilities = DB::connection('sqlsrv')
              ->table('shared_utilities')
              ->where('application_id', $application->id)
              ->orderBy('order')
              ->get();
          
          \Log::info("Retrieved " . count($dbUtilities) . " utilities");
          
          // Manually convert to array of objects to avoid collection method issues
          $serverUtilities = [];
          foreach ($dbUtilities as $util) {
              $serverUtilities[] = (object)[
                  'id' => $util->id,
                  'application_id' => $util->application_id,
                  'utility_type' => $util->utility_type,
                  'dimension' => $util->dimension, // Don't format here, keep raw value
                  'count' => $util->count,
                  'order' => $util->order ?? 0
              ];
          }
          
          \Log::info("Converted utilities to array: " . count($serverUtilities) . " items");
          
          // If no utilities found, check for shared_areas
          if (empty($serverUtilities)) {
            \Log::info("No utilities found, checking shared_areas");
            
            $sharedAreasJson = DB::connection('sqlsrv')
                ->table('mother_applications')
                ->where('id', $application->id)
                ->value('shared_areas');
            
            $sharedAreas = [];
            if (!empty($sharedAreasJson) && is_string($sharedAreasJson)) {
                $sharedAreas = json_decode($sharedAreasJson, true) ?: [];
                \Log::info("Found shared_areas: " . json_encode($sharedAreas));
            }
            
            // Create temporary utility objects from shared areas
            foreach ($sharedAreas as $index => $area) {
                if (!empty($area)) {
                    $serverUtilities[] = (object)[
                        'id' => null,
                        'application_id' => $application->id,
                        'utility_type' => $area,
                        'dimension' => 0,
                        'count' => 1,
                        'order' => $index + 1
                    ];
                }
            }
            
            \Log::info("Created " . count($serverUtilities) . " temporary utilities from shared_areas");
          }
        } catch (\Exception $e) {
          // Log the error but continue
          \Log::error("Error loading utilities: " . $e->getMessage());
          \Log::error($e->getTraceAsString());
          $serverUtilities = [];
        }
        @endphp
        
        <!-- Functional form for utilities table -->
        <form id="utilities-form">
          <input type="hidden" name="application_id" value="{{ $application->id }}">
          <table class="w-full" id="utilities-table">
            <thead>
              <tr class="bg-gray-200">
                <th class="w-1/6">SN</th>
                <th class="w-1/3">UTILITY TYPE</th>
                <th class="w-1/4">DIMENSION (m²)</th>
                <th class="w-1/4">COUNT</th>
              </tr>
            </thead>
            <tbody>
              @if(!empty($serverUtilities))
                @foreach($serverUtilities as $index => $utility)
                <tr class="utility-row">
                  <td>{{ $index + 1 }}</td>
                    <td>
                    <input type="text" name="utilities[{{ $index }}][utility_type]" value="{{ $utility->utility_type }}" {{ request()->query('url') != 'recommendation' ? 'readonly' : '' }}>
                    <input type="hidden" name="utilities[{{ $index }}][id]" value="{{ $utility->id }}">
                    </td>
                    <td>
                    <input type="number" name="utilities[{{ $index }}][dimension]" value="{{ $utility->dimension }}" step="0.001" {{ request()->query('url') != 'recommendation' ? 'readonly' : '' }}>
                    </td>
                    <td>
                    <input type="number" name="utilities[{{ $index }}][count]" value="{{ $utility->count }}" {{ request()->query('url') != 'recommendation' ? 'readonly' : '' }}>
                    </td>
                </tr>
                @endforeach
              @else
                <tr>
                  <td colspan="4" class="text-center py-4">No utilities added yet.</td>
                </tr>
              @endif
            </tbody>
          </table>
          @if(request()->query('url') == 'recommendation')
            <div class="mt-4 flex items-center">
              <button type="button" id="update-utilities-btn" class="px-4 py-2 bg-blue-500 text-white rounded">Update Utility Dimensions</button>
              <span id="utilities-update-message" class="ml-4 text-green-500 hidden">Utilities updated successfully!</span>
            </div>
          @endif
        </form>
      </div>
    </div>

    <!-- Signature Section -->
    <div class="signature-section">
      <!-- Initial Signature -->
      <div class="mb-8">
        <div class="border-t border-gray-500 w-48"></div>
        <p class="text-sm mt-1">AUWALU RABIU</p>
        <p class="text-sm">(ACTPP)</p>
        <p class="text-sm">For Director</p>
      </div>

      <!-- Director's Counter-Signature -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Director's Counter-Signature -->
        <div class="mb-8">
          <p class="text-sm mb-4 font-medium">DIRECTOR'S COUNTER-SIGNATURE:</p>
          <div class="border-t border-gray-500 w-48 mb-2"></div>
          <p class="text-sm">Signature: ..........................................</p>
          <p class="text-sm">Date: ....................................................</p>
        </div>

        <!-- Permanent Secretary's Endorsement -->
        <div class="mb-4">
          <p class="text-sm mb-4 font-medium">PERMANENT SECRETARY'S ENDORSEMENT:</p>
          <div class="border-t border-gray-500 w-48 mb-2"></div>
          <p class="text-sm">Signature: ..........................................</p>
          <p class="text-sm">Date: ....................................................</p>
          <p class="text-sm mt-2">Official Stamp: ..................................</p>
        </div>
      </div>
    </div>
  </div>
  @include('actions.parts.pp_js')
</body>
</html>