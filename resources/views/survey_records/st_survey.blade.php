@extends('layouts.app')
@section('page-title')
    {{ __('SECTIONAL TITLING  MODULE') }}
@endsection


@include('sectionaltitling.partials.assets.css')
@section('content')
<div class="flex-1 overflow-auto">
    <!-- Header -->
   @include('admin.header')
    <!-- Dashboard Content -->
    <div class="p-6">
      <!-- Stats Cards -->
        
      
      <!-- Secondary Applications Table - Screenshot 135 -->
      <div class="bg-white rounded-md shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-xl font-bold">ST Survey Record</h2>
          
          <div class="flex items-center space-x-4">
        
           
             

          <style>
              button:hover {
                  background-color: #fed7aa;
              }
          </style>
            <button class="flex items-center space-x-2 px-4 py-2 border border-gray-200 rounded-md">
              <i data-lucide="download" class="w-4 h-4 text-gray-600"></i>
              <span>Export</span>
            </button>
            
           
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead>
            <tr class="text-xs">
            <th  class="table-header text-green-500">PrimaryID</th>
            <th  class="table-header text-green-500">SchemeNo</th>
            <th  class="table-header text-green-500">Mother FileNo</th>
            <th  class="table-header text-green-500">STFileNo</th>

             
            
            <th  class="table-header text-green-500">Land Use</th>
            <th  class="table-header text-green-500">Original Owner</th>
            <th  class="table-header text-green-500">Unit Owner</th>
            <th  class="table-header text-green-500">Unit</th>
            <th  class="table-header text-green-500">Phone Number</th>
             
            
            <th  class="table-header text-green-500">Actions</th> 
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($SecondaryApplications as $app)
              <tr class="text-xs">
            <td class="table-cell px-1 py-1 truncate">{{ $app->main_id ?? 'N/A' }}</td>
            <td class="table-cell px-1 py-1 truncate">{{ $app->scheme_no ?? 'N/A' }}</td>
            <td class="table-cell px-1 py-1 truncate">{{ $app->mother_fileno ?? 'N/A' }}</td>
            <td class="table-cell px-1 py-1 truncate">{{ $app->fileno ?? 'N/A' }}</td>
           
            <td class="table-cell px-1 py-1 truncate">{{ $app->land_use ?? 'N/A' }}</td>
            <td class="table-cell px-1 py-1">
              <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                  @if(!empty($app->mother_passport))
                    <img src="{{ asset('storage/app/public/' . $app->mother_passport) }}" 
                         alt="Original Owner Passport" 
                         class="w-full h-full rounded-full object-cover cursor-pointer"
                         onclick="showPassportPreview('{{ asset('storage/app/public/' . $app->mother_passport) }}', 'Original Owner Passport')">
                  @elseif(!empty($app->mother_multiple_owners_passport))
                    @php
                      $passports = is_array($app->mother_multiple_owners_passport) ? 
                        $app->mother_multiple_owners_passport : 
                        json_decode($app->mother_multiple_owners_passport, true);
                      $firstPassport = !empty($passports) && isset($passports[0]) ? $passports[0] : null;
                    @endphp
                    @if($firstPassport)
                      <img src="{{ asset('storage/app/public/' . $firstPassport) }}" 
                           alt="Original Owner Passport" 
                           class="w-full h-full rounded-full object-cover cursor-pointer"
                           onclick="showMultipleOwners(
                             @json(is_array($app->mother_multiple_owners_names) ? $app->mother_multiple_owners_names : json_decode($app->mother_multiple_owners_names, true)), 
                             @json($passports)
                           )">
                    @else
                      <i data-lucide="{{ !empty($app->mother_corporate_name) ? 'building' : (!empty($app->mother_multiple_owners_names) ? 'users' : 'user') }}" class="w-3 h-3 text-gray-500"></i>
                    @endif
                  @else
                    <i data-lucide="{{ !empty($app->mother_corporate_name) ? 'building' : (!empty($app->mother_multiple_owners_names) ? 'users' : 'user') }}" class="w-3 h-3 text-gray-500"></i>
                  @endif
                </div>
                <div>
                  @if(!empty($app->mother_corporate_name))
                    <span>{{ $app->mother_corporate_name }}</span>
                  @elseif(!empty($app->mother_multiple_owners_names))
                    @php
                      $names = $app->mother_multiple_owners_names;
                      $decoded = [];
                      if (!empty($names)) {
                        $decoded = is_array($names) ? $names : json_decode($names, true);
                        if (!is_array($decoded)) $decoded = [];
                      }
                    @endphp
                    <span>{{ !empty($decoded) && isset($decoded[0]) ? $decoded[0] : '' }}</span>
                    @if(!empty($decoded))
                      <span class="ml-1 cursor-pointer text-blue-500"
                            onclick="showMultipleOwners(
                              @json($decoded), 
                              @json(is_array($app->mother_multiple_owners_passport) ? $app->mother_multiple_owners_passport : json_decode($app->mother_multiple_owners_passport, true))
                            )">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </span>
                    @endif
                  @else
                    <span>{{ $app->mother_applicant_title ?? '' }} {{ $app->mother_first_name ?? '' }} {{ $app->mother_surname ?? '' }}</span>
                  @endif
                </div>
              </div>
            </td>
            <td class="table-cell px-1 py-1">
              <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-2">
                  @if(!empty($app->passport))
                    <img src="{{ asset('storage/app/public/' . $app->passport) }}" 
                         alt="Unit Owner Passport" 
                         class="w-full h-full rounded-full object-cover cursor-pointer"
                         onclick="showPassportPreview('{{ asset('storage/app/public/' . $app->passport) }}', 'Unit Owner Passport')">
                  @elseif(!empty($app->multiple_owners_passport))
                    @php
                      $passports = is_array($app->multiple_owners_passport) ? 
                        $app->multiple_owners_passport : 
                        json_decode($app->multiple_owners_passport, true);
                      $firstPassport = !empty($passports) && isset($passports[0]) ? $passports[0] : null;
                    @endphp
                    @if($firstPassport)
                      <img src="{{ asset('storage/app/public/' . $firstPassport) }}" 
                           alt="Unit Owner Passport" 
                           class="w-full h-full rounded-full object-cover cursor-pointer"
                           onclick="showMultipleOwners(
                             @json(is_array($app->multiple_owners_names) ? $app->multiple_owners_names : json_decode($app->multiple_owners_names, true)), 
                             @json($passports)
                           )">
                    @else
                      <i data-lucide="{{ !empty($app->corporate_name) ? 'building' : (!empty($app->multiple_owners_names) ? 'users' : 'user') }}" class="w-3 h-3 text-gray-500"></i>
                    @endif
                  @else
                    <i data-lucide="{{ !empty($app->corporate_name) ? 'building' : (!empty($app->multiple_owners_names) ? 'users' : 'user') }}" class="w-3 h-3 text-gray-500"></i>
                  @endif
                </div>
                <div>
                  @if(!empty($app->corporate_name))
                    <span>{{ $app->corporate_name }}</span>
                  @elseif(!empty($app->multiple_owners_names))
                    @php
                      $names = $app->multiple_owners_names;
                      $decoded = [];
                      if (!empty($names)) {
                        if (is_array($names)) {
                          $decoded = $names;
                        } else {
                          $tryJson = json_decode($names, true);
                          if (is_array($tryJson)) {
                            $decoded = $tryJson;
                          } else {
                            $decoded = array_map('trim', str_getcsv($names));
                          }
                        }
                      }
                    @endphp
                    <span>{{ !empty($decoded) && isset($decoded[0]) ? $decoded[0] : '' }}</span>
                    @if(!empty($decoded))
                      <span class="ml-1 cursor-pointer text-blue-500"
                            onclick="showMultipleOwners(
                              @json($decoded), 
                              @json(is_array($app->multiple_owners_passport) ? $app->multiple_owners_passport : json_decode($app->multiple_owners_passport, true))
                            )">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                      </span>
                    @endif
                  @else
                    <span>{{ $app->applicant_title ?? '' }} {{ $app->first_name ?? '' }} {{ $app->surname ?? '' }}</span>
                  @endif
                </div>
              </div>
            </td>
            <td class="table-cell px-1 py-1 truncate">{{ $app->unit_number ?? 'N/A' }}</td>
            <td class="table-cell px-1 py-1 truncate">
              @if(!empty($app->phone_number) && str_contains($app->phone_number, ','))
                @php
                  $phones = array_map('trim', explode(',', $app->phone_number));
                  $firstPhone = $phones[0];
                  $allPhones = implode('<br>', $phones);
                @endphp
                <div class="relative group">
                  <span>{{ $firstPhone }}</span>
                  <i data-lucide="more-horizontal" class="inline-block w-3 h-3 text-gray-500 ml-1"></i>
                  <div class="absolute hidden group-hover:block bg-white border border-gray-200 shadow-lg rounded-md p-2 z-10 text-xs">
                    {!! $allPhones !!}
                  </div>
                </div>
              @else
                {{ $app->phone_number ?? 'N/A' }}
              @endif
            </td>
            
         
            <td class="table-cell px-1 py-1">
              @include('survey_records.action_menu.sub_action')
            </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="flex justify-between items-center mt-6 text-sm">
          <div class="text-gray-500"></div>
          <div class="flex items-center space-x-2">
            <button class="px-3 py-1 border border-gray-200 rounded-md flex items-center">
              <i data-lucide="chevron-left" class="w-4 h-4 mr-1"></i>
              <span>Previous</span>
            </button>
            <button class="px-3 py-1 border border-gray-200 rounded-md flex items-center">
              <span>Next</span>
              <i data-lucide="chevron-right" class="w-4 h-4 ml-1"></i>
            </button>
          </div>
        </div>
      </div>
    
    </div>
    <!-- Footer -->
    @include('admin.footer')
  </div>
 
@endsection

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.showFullNames = function(owners) {
  if (!Array.isArray(owners)) {
    owners = [];
  }
  if (owners.length > 0) {
    Swal.fire({
      title: 'Full Names of Multiple Owners',
      html: '<ul>' + owners.map(name => `<li>${name}</li>`).join('') + '</ul>',
      icon: 'info',
      confirmButtonText: 'Close'
    });
  } else {
    Swal.fire({
      title: 'Full Names of Multiple Owners',
      text: 'No owners available',
      icon: 'info',
      confirmButtonText: 'Close'
    });
  }
}
</script>
