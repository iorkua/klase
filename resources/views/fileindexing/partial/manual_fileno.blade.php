<div x-data="{ tab: 'mls',
                      mlsPrefix: '', 
                      mlsYear: '', 
                      mlsSerial: '',
                      mlsFileType: 'regular',
                      kangisPrefix: '', kangisNumber: '',
                      newkangisPrefix: '', newkangisNumber: '',
                      mlsPreview() { 
                        if (this.mlsPrefix && this.mlsYear && this.mlsSerial) {
                          const baseNumber = `${this.mlsPrefix}-${this.mlsYear}-${this.mlsSerial}`;
                          if (this.mlsFileType === 'temporary') {
                            return `${baseNumber} (T)`;
                          } else if (this.mlsFileType === 'extension') {
                            return `${baseNumber} AND EXTENSION`;
                          }
                          return baseNumber;
                        }
                        return '';
                      },
                      kangisPreview() {
                        if (this.kangisPrefix && this.kangisNumber) {
                          const n = this.kangisNumber.padStart(5, '0');
                          this.kangisNumber = n;
                          return `${this.kangisPrefix} ${n}`;
                        }
                        return this.kangisPrefix || this.kangisNumber;
                      },
                      newkangisPreview() { return this.newkangisPrefix && this.newkangisNumber ? `${this.newkangisPrefix}${this.newkangisNumber}` : (this.newkangisPrefix || this.newkangisNumber); }
                    }"
     class="bg-green-50 border border-green-100 rounded-md p-4 mb-6" x-cloak>
  <div class="flex items-center mb-2">
    <i data-lucide="file" class="w-5 h-5 mr-2 text-green-600"></i>
    <span class="font-medium">File Number Information</span>
  </div>
  <p class="text-sm text-gray-600 mb-4">Select file number type and enter the details</p>

  <!-- Hidden inputs for form submission -->
  <input type="hidden" name="activeFileTab" :value="tab">
  <input type="hidden" name="mlsFNo" :value="mlsPreview()">
  <input type="hidden" name="kangisFileNo" :value="kangisPreview()">
  <input type="hidden" name="NewKANGISFileno" :value="newkangisPreview()">

  <!-- Tab Navigation -->
  <div class="flex space-x-1 mb-4 bg-gray-100 p-1 rounded-lg">
    <button type="button"
            @click="tab = 'mls'"
            :class="tab === 'mls' ? 'flex-1 px-3 py-2 text-sm font-medium rounded-md bg-white text-blue-600 shadow-sm' : 'flex-1 px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700'">
      MLS
    </button>
    <button type="button"
            @click="tab = 'kangis'"
            :class="tab === 'kangis' ? 'flex-1 px-3 py-2 text-sm font-medium rounded-md bg-white text-blue-600 shadow-sm' : 'flex-1 px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700'">
      KANGIS
    </button>
    <button type="button"
            @click="tab = 'newkangis'"
            :class="tab === 'newkangis' ? 'flex-1 px-3 py-2 text-sm font-medium rounded-md bg-white text-blue-600 shadow-sm' : 'flex-1 px-3 py-2 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700'">
      New KANGIS
    </button>
  </div>

  <!-- MLS Tab Content -->
  <div x-show="tab === 'mls'" class="tab-content-panel">
    <p class="text-sm text-gray-600 mb-3">MLS File Number</p>
    
    <!-- Radio Buttons for File Type -->
    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700 mb-2">File Type</label>
      <div class="flex space-x-4">
        <label class="flex items-center">
          <input type="radio" x-model="mlsFileType" value="regular" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
          <span class="ml-2 text-sm text-gray-700">Regular File</span>
        </label>
        <label class="flex items-center">
          <input type="radio" x-model="mlsFileType" value="temporary" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
          <span class="ml-2 text-sm text-gray-700">Temporary File</span>
        </label>
        <label class="flex items-center">
          <input type="radio" x-model="mlsFileType" value="extension" class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300">
          <span class="ml-2 text-sm text-gray-700">Extension</span>
        </label>
      </div>
    </div>

    <!-- Input Grid -->
    <div class="grid grid-cols-3 gap-4 mb-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">File Prefix</label>
        <select x-model="mlsPrefix" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
          <option value="">Select prefix</option>
          <option>COM</option>
          <option>RES</option>
          <option>CON-COM</option>
          <option>CON-RES</option>
          <option>CON-AG</option>
          <option>CON-IND</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
        <input type="text" x-model="mlsYear" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="2024">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Serial No</label>
        <input type="text" x-model="mlsSerial" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="572">
      </div>
    </div>

    <!-- Full File Number Display -->
    <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
      <label class="block text-sm font-medium text-gray-700 mb-1">Full File Number</label>
      <div class="text-lg font-semibold text-blue-800" x-text="mlsPreview() || 'Enter file details above'"></div>
    </div>
  </div>

  <!-- KANGIS Tab Content -->
  <div x-show="tab === 'kangis'" class="tab-content-panel">
    <p class="text-sm text-gray-600 mb-3">KANGIS File Number</p>
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">File Prefix</label>
        <select x-model="kangisPrefix" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
          <option value="">Select Prefix</option>
          <option>KNML</option>
          <option>MNKL</option>
          <option>MLKN</option>
          <option>KNGP</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
        <input type="text" x-model="kangisNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 0001 or 2500">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full FileNo</label>
        <input type="text" :value="kangisPreview()" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50">
      </div>
    </div>
  </div>

  <!-- New KANGIS Tab Content -->
  <div x-show="tab === 'newkangis'" class="tab-content-panel">
    <p class="text-sm text-gray-600 mb-3">New KANGIS File Number</p>
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">File Prefix</label>
        <select x-model="newkangisPrefix" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
          <option value="">Select Prefix</option>
          <option>KN</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
        <input type="text" x-model="newkangisNumber" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. 1586">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full FileNo</label>
        <input type="text" :value="newkangisPreview()" readonly class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50">
      </div>
    </div>
  </div>
</div>

<!-- Add Alpine.js and Lucide for icons -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.13.3/cdn.min.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.1/umd/lucide.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
  });
</script>