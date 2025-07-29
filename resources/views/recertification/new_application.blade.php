 
<script>
// Tailwind config
tailwind.config = {
  theme: {
    extend: {
      colors: {
        primary: '#3b82f6',
        'primary-foreground': '#ffffff',
        muted: '#f3f4f6',
        'muted-foreground': '#6b7280',
        border: '#e5e7eb',
        destructive: '#ef4444',
        'destructive-foreground': '#ffffff',
        secondary: '#f1f5f9',
        'secondary-foreground': '#0f172a',
      }
    }
  }
}
</script>

<style>
/* Modal backdrop */
.modal-backdrop {
  background-color: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(4px);
}

/* Custom radio button styles */
.radio-item {
  position: relative;
  display: flex;
  align-items: center;
  padding: 0.5rem;
  border-radius: 0.375rem;
  cursor: pointer;
  transition: background-color 0.2s;
}

.radio-item:hover {
  background-color: #f9fafb;
}

.radio-item input[type="radio"] {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.radio-circle {
  width: 1rem;
  height: 1rem;
  border: 2px solid #d1d5db;
  border-radius: 50%;
  margin-right: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.radio-item input[type="radio"]:checked + .radio-circle {
  border-color: #3b82f6;
  background-color: #3b82f6;
}

.radio-circle::after {
  content: '';
  width: 0.25rem;
  height: 0.25rem;
  border-radius: 50%;
  background-color: white;
  opacity: 0;
  transition: opacity 0.2s;
}

.radio-item input[type="radio"]:checked + .radio-circle::after {
  opacity: 1;
}

/* Custom checkbox styles */
.checkbox-item {
  position: relative;
  display: flex;
  align-items: center;
  cursor: pointer;
}

.checkbox-item input[type="checkbox"] {
  position: absolute;
  opacity: 0;
  pointer-events: none;
}

.checkbox-box {
  width: 1rem;
  height: 1rem;
  border: 2px solid #d1d5db;
  border-radius: 0.25rem;
  margin-right: 0.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.checkbox-item input[type="checkbox"]:checked + .checkbox-box {
  border-color: #3b82f6;
  background-color: #3b82f6;
}

.checkbox-box::after {
  content: '✓';
  color: white;
  font-size: 0.75rem;
  opacity: 0;
  transition: opacity 0.2s;
}

.checkbox-item input[type="checkbox"]:checked + .checkbox-box::after {
  opacity: 1;
}

/* Step indicator styles */
.step-indicator {
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.step-circle {
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 500;
  transition: all 0.3s;
}

.step-circle.active {
  background-color: #3b82f6;
  color: white;
}

.step-circle.inactive {
  background-color: #f3f4f6;
  color: #6b7280;
}

.step-line {
  width: 3rem;
  height: 0.125rem;
  margin: 0 0.5rem;
  transition: all 0.3s;
}

.step-line.active {
  background-color: #3b82f6;
}

.step-line.inactive {
  background-color: #f3f4f6;
}

/* Form validation styles */
.form-field.error input,
.form-field.error select,
.form-field.error textarea {
  border-color: #ef4444;
  box-shadow: 0 0 0 2px rgba(239, 68, 68, 0.1);
}

.form-field.error .error-message {
  display: block;
}

.error-message {
  display: none;
  color: #ef4444;
  font-size: 0.75rem;
  margin-top: 0.25rem;
}

/* Loading spinner */
.loading-spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid #e5e7eb;
  border-top: 2px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Fade in animation */
.fade-in {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}

/* Photo upload area */
.photo-upload-area {
  border: 2px dashed #d1d5db;
  border-radius: 0.5rem;
  padding: 1rem;
  text-align: center;
  height: 12rem;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.photo-upload-area:hover {
  border-color: #3b82f6;
  background-color: #f8fafc;
}

/* Signature area */
.signature-area {
  height: 6rem;
  border: 1px solid #d1d5db;
  border-radius: 0.375rem;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: #fafafa;
  color: #6b7280;
}
</style>
 

<!-- New Recertification Application Modal -->
<div id="new-recertification-modal" class="fixed inset-0 z-50 flex items-center justify-center modal-backdrop" style="display: none;">
  <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full mx-4 max-h-[90vh] overflow-y-auto fade-in">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <div class="text-center flex-1">
          <div class="space-y-1">
            <div class="font-bold text-lg">KANO STATE GEOGRAPHIC INFORMATION SYSTEMS (KANGIS)</div>
            <div class="text-sm text-gray-600">MINISTRY OF LAND AND PHYSICAL PLANNING KANO STATE</div>
            <div class="text-sm font-semibold">APPLICATION FOR RE-CERTIFICATION OR RE-ISSUANCE OF C-of-O</div>
            <div class="text-xs text-gray-500">INDIVIDUAL FORM AR01-01</div>
          </div>
        </div>
        <button id="close-modal" class="text-gray-400 hover:text-gray-600 transition-colors ml-4">
          <i data-lucide="x" class="h-5 w-5"></i>
        </button>
      </div>
    </div>
    
    <!-- Step Indicator -->
    <div class="p-6 pb-0">
      <div class="step-indicator">
        <div id="step-1" class="step-circle active">1</div>
        <div id="line-1" class="step-line inactive"></div>
        <div id="step-2" class="step-circle inactive">2</div>
        <div id="line-2" class="step-line inactive"></div>
        <div id="step-3" class="step-circle inactive">3</div>
        <div id="line-3" class="step-line inactive"></div>
        <div id="step-4" class="step-circle inactive">4</div>
        <div id="line-4" class="step-line inactive"></div>
        <div id="step-5" class="step-circle inactive">5</div>
        <div id="line-5" class="step-line inactive"></div>
        <div id="step-6" class="step-circle inactive">6</div>
      </div>
    </div>
    
    <div class="p-6">
      <form id="recertification-form">
        
        <!-- Step 1: Applicant Personal Details -->
        <div id="step-content-1" class="step-content">
          <div class="bg-white border border-gray-200 rounded-lg">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold flex items-center gap-2">
                <i data-lucide="user" class="h-5 w-5"></i>
                SECTION A: APPLICANT PERSONAL DETAILS
              </h3>
            </div>
            <div class="p-4 space-y-4">
              <div class="grid grid-cols-12 gap-4">
                <div class="col-span-9 space-y-4">
                  <div class="form-field">
                    <label for="applicationDate" class="block text-sm font-medium text-gray-700 mb-1">
                      Application Date <span class="text-red-500">*</span>
                    </label>
                    <input
                      type="date"
                      id="applicationDate"
                      name="applicationDate"
                      required
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                    />
                    <div class="error-message">Application date is required</div>
                  </div>
                  
                  <div class="grid grid-cols-3 gap-4">
                    <div class="form-field">
                      <label for="surname" class="block text-sm font-medium text-gray-700 mb-1">
                        Surname <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="surname"
                        name="surname"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="SURNAME"
                      />
                      <div class="error-message">Surname is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="firstName"
                        name="firstName"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="FIRST NAME"
                      />
                      <div class="error-message">First name is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="middleName" class="block text-sm font-medium text-gray-700 mb-1">
                        Other Names (Middle Name or Initials)
                      </label>
                      <input
                        type="text"
                        id="middleName"
                        name="middleName"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="MIDDLE NAME"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div class="form-field">
                      <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                      <select
                        id="title"
                        name="title"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      >
                        <option value="">Select Title</option>
                        <option value="MR">MR</option>
                        <option value="MRS">MRS</option>
                        <option value="MISS">MISS</option>
                        <option value="DR">DR</option>
                        <option value="PROF">PROF</option>
                        <option value="ENG">ENG</option>
                        <option value="ARC">ARC</option>
                        <option value="ALHAJI">ALHAJI</option>
                        <option value="HAJIYA">HAJIYA</option>
                      </select>
                    </div>
                    
                    <div class="form-field">
                      <label for="occupation" class="block text-sm font-medium text-gray-700 mb-1">
                        Occupation <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="occupation"
                        name="occupation"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="OCCUPATION"
                      />
                      <div class="error-message">Occupation is required</div>
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-3 gap-4">
                    <div class="form-field">
                      <label for="dateOfBirth" class="block text-sm font-medium text-gray-700 mb-1">
                        Date of Birth <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="date"
                        id="dateOfBirth"
                        name="dateOfBirth"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      />
                      <div class="error-message">Date of birth is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">
                        Nationality <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="nationality"
                        name="nationality"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="NIGERIAN"
                      />
                      <div class="error-message">Nationality is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="stateOfOrigin" class="block text-sm font-medium text-gray-700 mb-1">
                        State of Origin <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="stateOfOrigin"
                        name="stateOfOrigin"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="STATE OF ORIGIN"
                      />
                      <div class="error-message">State of origin is required</div>
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div class="form-field">
                      <label for="lgaOfOrigin" class="block text-sm font-medium text-gray-700 mb-1">LGA of Origin</label>
                      <input
                        type="text"
                        id="lgaOfOrigin"
                        name="lgaOfOrigin"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="LGA OF ORIGIN"
                      />
                    </div>
                    
                    <div class="form-field">
                      <label for="nin" class="block text-sm font-medium text-gray-700 mb-1">NIN</label>
                      <input
                        type="text"
                        id="nin"
                        name="nin"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        placeholder="NATIONAL IDENTIFICATION NUMBER"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div class="form-field">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Gender <span class="text-red-500">*</span>
                      </label>
                      <div class="flex gap-4">
                        <label class="radio-item">
                          <input type="radio" name="gender" value="male" required />
                          <div class="radio-circle"></div>
                          <span class="text-sm">Male</span>
                        </label>
                        <label class="radio-item">
                          <input type="radio" name="gender" value="female" />
                          <div class="radio-circle"></div>
                          <span class="text-sm">Female</span>
                        </label>
                      </div>
                      <div class="error-message">Gender is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        Marital Status <span class="text-red-500">*</span>
                      </label>
                      <div class="flex gap-4 flex-wrap">
                        <label class="radio-item">
                          <input type="radio" name="maritalStatus" value="single" required />
                          <div class="radio-circle"></div>
                          <span class="text-sm">Single</span>
                        </label>
                        <label class="radio-item">
                          <input type="radio" name="maritalStatus" value="married" />
                          <div class="radio-circle"></div>
                          <span class="text-sm">Married</span>
                        </label>
                        <label class="radio-item">
                          <input type="radio" name="maritalStatus" value="divorced" />
                          <div class="radio-circle"></div>
                          <span class="text-sm">Divorced</span>
                        </label>
                        <label class="radio-item">
                          <input type="radio" name="maritalStatus" value="widowed" />
                          <div class="radio-circle"></div>
                          <span class="text-sm">Widowed</span>
                        </label>
                      </div>
                      <div class="error-message">Marital status is required</div>
                    </div>
                  </div>
                  
                  <div class="form-field">
                    <label for="maidenName" class="block text-sm font-medium text-gray-700 mb-1">
                      Maiden Name (if applicable)
                    </label>
                    <input
                      type="text"
                      id="maidenName"
                      name="maidenName"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                      placeholder="MAIDEN NAME"
                    />
                  </div>
                </div>
                
                <div class="col-span-3">
                  <div class="photo-upload-area">
                    <i data-lucide="camera" class="h-8 w-8 mb-2 text-gray-400"></i>
                    <div class="text-xs font-semibold mb-2">PASSPORT PHOTOGRAPH</div>
                    <div class="text-xs text-gray-500 mb-2">(2" X 2")</div>
                    <button type="button" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50">
                      Upload Photo
                    </button>
                    <div class="text-xs text-red-600 mt-2">
                      NOTE: DO NOT put a staple pin over the face region of the photo
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 2: Contact Details -->
        <div id="step-content-2" class="step-content hidden">
          <div class="bg-white border border-gray-200 rounded-lg">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold">SECTION A1 & A2: CONTACT DETAILS</h3>
            </div>
            <div class="p-4 space-y-6">
              <div>
                <h4 class="font-semibold mb-4">A1. CONTACT DETAILS OF APPLICANT:</h4>
                <div class="space-y-4">
                  <div class="grid grid-cols-3 gap-4">
                    <div class="form-field">
                      <label for="phoneNo" class="block text-sm font-medium text-gray-700 mb-1">
                        Phone No <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="tel"
                        id="phoneNo"
                        name="phoneNo"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        placeholder="Phone Number"
                      />
                      <div class="error-message">Phone number is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="whatsappPhoneNo" class="block text-sm font-medium text-gray-700 mb-1">Whatsapp Phone No</label>
                      <input
                        type="tel"
                        id="whatsappPhoneNo"
                        name="whatsappPhoneNo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        placeholder="WhatsApp Number"
                      />
                    </div>
                    
                    <div class="form-field">
                      <label for="alternatePhoneNo" class="block text-sm font-medium text-gray-700 mb-1">Alternate Phone No</label>
                      <input
                        type="tel"
                        id="alternatePhoneNo"
                        name="alternatePhoneNo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        placeholder="Alternate Number"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div class="form-field">
                      <label for="addressLine1" class="block text-sm font-medium text-gray-700 mb-1">
                        Address Line 1 <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="addressLine1"
                        name="addressLine1"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="ADDRESS LINE 1"
                      />
                      <div class="error-message">Address line 1 is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="addressLine2" class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                      <input
                        type="text"
                        id="addressLine2"
                        name="addressLine2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="ADDRESS LINE 2"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-3 gap-4">
                    <div class="form-field">
                      <label for="cityTown" class="block text-sm font-medium text-gray-700 mb-1">
                        City/Town <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="cityTown"
                        name="cityTown"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="CITY/TOWN"
                      />
                      <div class="error-message">City/Town is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="state" class="block text-sm font-medium text-gray-700 mb-1">
                        State <span class="text-red-500">*</span>
                      </label>
                      <input
                        type="text"
                        id="state"
                        name="state"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="STATE"
                      />
                      <div class="error-message">State is required</div>
                    </div>
                    
                    <div class="form-field">
                      <label for="emailAddress" class="block text-sm font-medium text-gray-700 mb-1">E-Mail Address</label>
                      <input
                        type="email"
                        id="emailAddress"
                        name="emailAddress"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        placeholder="email@example.com"
                      />
                    </div>
                  </div>
                </div>
              </div>
              
              <div>
                <h4 class="font-semibold mb-4">A2: CONTACT DETAILS OF AUTHORISED REPRESENTATIVE:</h4>
                <div class="space-y-4">
                  <div class="grid grid-cols-4 gap-4">
                    <div class="form-field">
                      <label for="repSurname" class="block text-sm font-medium text-gray-700 mb-1">Surname</label>
                      <input
                        type="text"
                        id="repSurname"
                        name="repSurname"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="SURNAME"
                      />
                    </div>
                    
                    <div class="form-field">
                      <label for="repFirstName" class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                      <input
                        type="text"
                        id="repFirstName"
                        name="repFirstName"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="FIRST NAME"
                      />
                    </div>
                    
                    <div class="form-field">
                      <label for="repMiddleName" class="block text-sm font-medium text-gray-700 mb-1">Other Name</label>
                      <input
                        type="text"
                        id="repMiddleName"
                        name="repMiddleName"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="MIDDLE NAME"
                      />
                    </div>
                    
                    <div class="form-field">
                      <label for="repTitle" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                      <input
                        type="text"
                        id="repTitle"
                        name="repTitle"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="TITLE"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div class="form-field">
                      <label for="repRelationship" class="block text-sm font-medium text-gray-700 mb-1">Relationship to Applicant/Designation</label>
                      <input
                        type="text"
                        id="repRelationship"
                        name="repRelationship"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="RELATIONSHIP/DESIGNATION"
                      />
                    </div>
                    
                    <div class="form-field">
                      <label for="repPhoneNo" class="block text-sm font-medium text-gray-700 mb-1">Phone No</label>
                      <input
                        type="tel"
                        id="repPhoneNo"
                        name="repPhoneNo"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        placeholder="Phone Number"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 3: Title Holder Details -->
        <div id="step-content-3" class="step-content hidden">
          <div class="bg-white border border-gray-200 rounded-lg">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold">SECTION B: TITLE HOLDER DETAILS</h3>
            </div>
            <div class="p-4 space-y-4">
              <div class="grid grid-cols-4 gap-4">
                <div class="form-field">
                  <label for="titleHolderSurname" class="block text-sm font-medium text-gray-700 mb-1">
                    Surname <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="titleHolderSurname"
                    name="titleHolderSurname"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="SURNAME"
                  />
                  <div class="error-message">Title holder surname is required</div>
                </div>
                
                <div class="form-field">
                  <label for="titleHolderFirstName" class="block text-sm font-medium text-gray-700 mb-1">
                    First Name <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="titleHolderFirstName"
                    name="titleHolderFirstName"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="FIRST NAME"
                  />
                  <div class="error-message">Title holder first name is required</div>
                </div>
                
                <div class="form-field">
                  <label for="titleHolderMiddleName" class="block text-sm font-medium text-gray-700 mb-1">Other Names</label>
                  <input
                    type="text"
                    id="titleHolderMiddleName"
                    name="titleHolderMiddleName"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="MIDDLE NAME"
                  />
                </div>
                
                <div class="form-field">
                  <label for="titleHolderTitle" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                  <input
                    type="text"
                    id="titleHolderTitle"
                    name="titleHolderTitle"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="TITLE"
                  />
                </div>
              </div>
              
              <div class="form-field">
                <label for="cofoNumber" class="block text-sm font-medium text-gray-700 mb-1">
                  CofO No or RofO No <span class="text-red-500">*</span>
                </label>
                <input
                  type="text"
                  id="cofoNumber"
                  name="cofoNumber"
                  required
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                  placeholder="CERTIFICATE NUMBER"
                />
                <div class="error-message">Certificate number is required</div>
              </div>
              
              <div class="border-t pt-4">
                <h4 class="font-semibold mb-4">SECTION B2. TITLE REGISTRATION DETAILS:</h4>
                <div class="grid grid-cols-4 gap-4 mb-4">
                  <div class="form-field">
                    <label for="registrationNo" class="block text-sm font-medium text-gray-700 mb-1">Registration No</label>
                    <input
                      type="text"
                      id="registrationNo"
                      name="registrationNo"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="REG NO"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label for="registrationVolume" class="block text-sm font-medium text-gray-700 mb-1">Volume</label>
                    <input
                      type="text"
                      id="registrationVolume"
                      name="registrationVolume"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="VOLUME"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label for="registrationPage" class="block text-sm font-medium text-gray-700 mb-1">Page</label>
                    <input
                      type="text"
                      id="registrationPage"
                      name="registrationPage"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="PAGE"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label for="registrationNumber" class="block text-sm font-medium text-gray-700 mb-1">No.</label>
                    <input
                      type="text"
                      id="registrationNumber"
                      name="registrationNumber"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="NO"
                    />
                  </div>
                </div>
                
                <div class="space-y-4">
                  <div class="form-field">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Plot Ownership: Is the Applicant the original owner of the Plot? <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                      <label class="radio-item">
                        <input type="radio" name="isOriginalOwner" value="yes" required />
                        <div class="radio-circle"></div>
                        <span class="text-sm">Yes</span>
                      </label>
                      <label class="radio-item">
                        <input type="radio" name="isOriginalOwner" value="no" />
                        <div class="radio-circle"></div>
                        <span class="text-sm">No</span>
                      </label>
                    </div>
                    <div class="error-message">Please select plot ownership status</div>
                  </div>
                  
                  <div id="ownership-details" class="space-y-4 border-l-4 border-blue-200 pl-4 hidden">
                    <div>
                      <label class="block text-sm font-medium text-gray-700 mb-2">
                        If No, provide details of legal transaction through which the Plot was acquired:
                      </label>
                      <div class="mt-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">i. Registered Instrument(s)</label>
                        <div class="grid grid-cols-2 gap-2">
                          <label class="radio-item">
                            <input type="radio" name="instrumentType" value="deed-sub-lease" />
                            <div class="radio-circle"></div>
                            <span class="text-sm">Deed of Sub - Lease</span>
                          </label>
                          <label class="radio-item">
                            <input type="radio" name="instrumentType" value="deed-assignment" />
                            <div class="radio-circle"></div>
                            <span class="text-sm">Deed of Assignment</span>
                          </label>
                          <label class="radio-item">
                            <input type="radio" name="instrumentType" value="deed-gift" />
                            <div class="radio-circle"></div>
                            <span class="text-sm">Deed of Gift</span>
                          </label>
                          <label class="radio-item">
                            <input type="radio" name="instrumentType" value="power-attorney" />
                            <div class="radio-circle"></div>
                            <span class="text-sm">Power of Attorney</span>
                          </label>
                          <label class="radio-item">
                            <input type="radio" name="instrumentType" value="devolution-order" />
                            <div class="radio-circle"></div>
                            <span class="text-sm">Devolution Order</span>
                          </label>
                          <label class="radio-item">
                            <input type="radio" name="instrumentType" value="others" />
                            <div class="radio-circle"></div>
                            <span class="text-sm">Others, please specify:</span>
                          </label>
                        </div>
                      </div>
                    </div>
                    
                    <div class="form-field">
                      <label for="titleHolderName" class="block text-sm font-medium text-gray-700 mb-1">ii. Name of Title Holder</label>
                      <input
                        type="text"
                        id="titleHolderName"
                        name="titleHolderName"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                        placeholder="TITLE HOLDER NAME"
                      />
                    </div>
                  </div>
                  
                  <div class="grid grid-cols-2 gap-4">
                    <div class="form-field">
                      <label for="commencementDate" class="block text-sm font-medium text-gray-700 mb-1">Commencement Date</label>
                      <input
                        type="date"
                        id="commencementDate"
                        name="commencementDate"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      />
                    </div>
                    
                    <div class="form-field">
                      <label for="grantTerm" class="block text-sm font-medium text-gray-700 mb-1">Grant Term</label>
                      <input
                        type="text"
                        id="grantTerm"
                        name="grantTerm"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        placeholder="GRANT TERM"
                      />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 4: Mortgage & Encumbrance Details -->
        <div id="step-content-4" class="step-content hidden">
          <div class="bg-white border border-gray-200 rounded-lg">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold">MORTGAGE & ENCUMBRANCE DETAILS</h3>
            </div>
            <div class="p-4 space-y-4">
              <div class="form-field">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Is the plot Encumbered? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                  <label class="radio-item">
                    <input type="radio" name="isEncumbered" value="yes" required />
                    <div class="radio-circle"></div>
                    <span class="text-sm">Yes</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="isEncumbered" value="no" />
                    <div class="radio-circle"></div>
                    <span class="text-sm">No</span>
                  </label>
                </div>
                <div class="error-message">Please select encumbrance status</div>
              </div>
              
              <div id="encumbrance-reason" class="form-field hidden">
                <label for="encumbranceReason" class="block text-sm font-medium text-gray-700 mb-1">If yes, state the reason(s):</label>
                <textarea
                  id="encumbranceReason"
                  name="encumbranceReason"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                  placeholder="STATE REASON FOR ENCUMBRANCE"
                ></textarea>
              </div>
              
              <div class="form-field">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Is there a subsisting Mortgage on the Property Rights over the Plot? <span class="text-red-500">*</span>
                </label>
                <div class="flex gap-4">
                  <label class="radio-item">
                    <input type="radio" name="hasMortgage" value="yes" required />
                    <div class="radio-circle"></div>
                    <span class="text-sm">Yes</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="hasMortgage" value="no" />
                    <div class="radio-circle"></div>
                    <span class="text-sm">No</span>
                  </label>
                </div>
                <div class="error-message">Please select mortgage status</div>
              </div>
              
              <div id="mortgage-details" class="space-y-4 border-l-4 border-orange-200 pl-4 hidden">
                <div class="form-field">
                  <label for="mortgageeName" class="block text-sm font-medium text-gray-700 mb-1">Name of Mortgagee Institution</label>
                  <input
                    type="text"
                    id="mortgageeName"
                    name="mortgageeName"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="MORTGAGEE INSTITUTION NAME"
                  />
                </div>
                
                <div class="grid grid-cols-4 gap-4">
                  <div class="form-field">
                    <label for="mortgageRegistrationNo" class="block text-sm font-medium text-gray-700 mb-1">Mortgage Registration No</label>
                    <input
                      type="text"
                      id="mortgageRegistrationNo"
                      name="mortgageRegistrationNo"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="REG NO"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label for="mortgageVolume" class="block text-sm font-medium text-gray-700 mb-1">Volume</label>
                    <input
                      type="text"
                      id="mortgageVolume"
                      name="mortgageVolume"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="VOLUME"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label for="mortgagePage" class="block text-sm font-medium text-gray-700 mb-1">Page</label>
                    <input
                      type="text"
                      id="mortgagePage"
                      name="mortgagePage"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="PAGE"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label for="mortgageNumber" class="block text-sm font-medium text-gray-700 mb-1">No.</label>
                    <input
                      type="text"
                      id="mortgageNumber"
                      name="mortgageNumber"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="NO"
                    />
                  </div>
                </div>
                
                <div class="form-field">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Mortgage Released?</label>
                  <div class="flex gap-4">
                    <label class="radio-item">
                      <input type="radio" name="mortgageReleased" value="yes" />
                      <div class="radio-circle"></div>
                      <span class="text-sm">Yes</span>
                    </label>
                    <label class="radio-item">
                      <input type="radio" name="mortgageReleased" value="no" />
                      <div class="radio-circle"></div>
                      <span class="text-sm">No</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Step 5: Plot Details -->
        <div id="step-content-5" class="step-content hidden">
          <div class="bg-white border border-gray-200 rounded-lg">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold flex items-center gap-2">
                <i data-lucide="building" class="h-5 w-5"></i>
                SECTION C: PLOT DETAILS
              </h3>
            </div>
            <div class="p-4 space-y-4">
              <div class="grid grid-cols-3 gap-4">
                <div class="form-field">
                  <label for="plotNumber" class="block text-sm font-medium text-gray-700 mb-1">
                    Plot Number or Piece of Land <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="plotNumber"
                    name="plotNumber"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="PLOT NUMBER"
                  />
                  <div class="error-message">Plot number is required</div>
                </div>
                
                <div class="form-field">
                  <label for="fileNumber" class="block text-sm font-medium text-gray-700 mb-1">
                    File Number <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="fileNumber"
                    name="fileNumber"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="FILE NUMBER"
                  />
                  <div class="error-message">File number is required</div>
                </div>
                
                <div class="form-field">
                  <label for="plotSize" class="block text-sm font-medium text-gray-700 mb-1">
                    Plot Size (Ha) <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="plotSize"
                    name="plotSize"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                    placeholder="PLOT SIZE"
                  />
                  <div class="error-message">Plot size is required</div>
                </div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div class="form-field">
                  <label for="layoutDistrict" class="block text-sm font-medium text-gray-700 mb-1">
                    Layout/District <span class="text-red-500">*</span>
                  </label>
                  <input
                    type="text"
                    id="layoutDistrict"
                    name="layoutDistrict"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                    placeholder="LAYOUT/DISTRICT"
                  />
                  <div class="error-message">Layout/District is required</div>
                </div>
                
                <div class="form-field">
                  <label for="lga" class="block text-sm font-medium text-gray-700 mb-1">
                    LGA <span class="text-red-500">*</span>
                  </label>
                  <select
                    id="lga"
                    name="lga"
                    required
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                  >
                    <option value="">Select LGA</option>
                    <option value="Kano Municipal">Kano Municipal</option>
                    <option value="Fagge">Fagge</option>
                    <option value="Gwale">Gwale</option>
                    <option value="Dala">Dala</option>
                    <option value="Tarauni">Tarauni</option>
                    <option value="Nassarawa">Nassarawa</option>
                  </select>
                  <div class="error-message">LGA is required</div>
                </div>
              </div>
              
              <div class="form-field">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                  Current Land Use <span class="text-red-500">*</span>
                </label>
                <div class="grid grid-cols-4 gap-2">
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="residential" required />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Residential</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="commercial" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Commercial</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="industrial" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Industrial</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="agricultural" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Agricultural</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="educational" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Educational</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="religious" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Religious</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="public" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Public</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="ngo" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">NGO</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="social" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Social (Hospital, etc)</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="petrol-station" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">Petrol Filling Station</span>
                  </label>
                  <label class="radio-item">
                    <input type="radio" name="currentLandUse" value="gkn" />
                    <div class="radio-circle"></div>
                    <span class="text-xs">GKN</span>
                  </label>
                </div>
                <div class="error-message">Current land use is required</div>
              </div>
              
              <div class="grid grid-cols-2 gap-4">
                <div class="form-field">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Plot Status <span class="text-red-500">*</span>
                  </label>
                  <div class="flex gap-4">
                    <label class="radio-item">
                      <input type="radio" name="plotStatus" value="developed" required />
                      <div class="radio-circle"></div>
                      <span class="text-sm">Developed</span>
                    </label>
                    <label class="radio-item">
                      <input type="radio" name="plotStatus" value="undeveloped" />
                      <div class="radio-circle"></div>
                      <span class="text-sm">Undeveloped</span>
                    </label>
                  </div>
                  <div class="error-message">Plot status is required</div>
                </div>
                
                <div class="form-field">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Mode of Allocation <span class="text-red-500">*</span>
                  </label>
                  <div class="flex gap-4">
                    <label class="radio-item">
                      <input type="radio" name="modeOfAllocation" value="direct-allocation" required />
                      <div class="radio-circle"></div>
                      <span class="text-sm">Direct Allocation</span>
                    </label>
                    <label class="radio-item">
                      <input type="radio" name="modeOfAllocation" value="resettlement" />
                      <div class="radio-circle"></div>
                      <span class="text-sm">Resettlement</span>
                    </label>
                  </div>
                  <div class="error-message">Mode of allocation is required</div>
                </div>
              </div>
              
              <div class="form-field">
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input
                  type="date"
                  id="startDate"
                  name="startDate"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- Step 6: Payment & Terms -->
        <div id="step-content-6" class="step-content hidden">
          <div class="bg-white border border-gray-200 rounded-lg">
            <div class="p-4 border-b border-gray-200">
              <h3 class="text-lg font-semibold flex items-center gap-2">
                <i data-lucide="credit-card" class="h-5 w-5"></i>
                SECTION D: PAYMENT & TERMS
              </h3>
            </div>
            <div class="p-4 space-y-6">
              <div>
                <h4 class="font-semibold mb-4">D2: PAYMENT INFORMATION SECTION</h4>
                <div class="grid grid-cols-3 gap-4">
                  <div class="form-field">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                      Method of Payment <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-4">
                      <label class="radio-item">
                        <input type="radio" name="paymentMethod" value="online" required />
                        <div class="radio-circle"></div>
                        <span class="text-sm">Online</span>
                      </label>
                      <label class="radio-item">
                        <input type="radio" name="paymentMethod" value="pos" />
                        <div class="radio-circle"></div>
                        <span class="text-sm">PoS</span>
                      </label>
                      <label class="radio-item">
                        <input type="radio" name="paymentMethod" value="bank" />
                        <div class="radio-circle"></div>
                        <span class="text-sm">Bank</span>
                      </label>
                    </div>
                    <div class="error-message">Payment method is required</div>
                  </div>
                  
                  <div class="form-field">
                    <label for="receiptNo" class="block text-sm font-medium text-gray-700 mb-1">Receipt No/Teller No</label>
                    <input
                      type="text"
                      id="receiptNo"
                      name="receiptNo"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                      placeholder="RECEIPT/TELLER NUMBER"
                    />
                  </div>
                  
                  <div class="form-field">
                    <label for="bankName" class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                    <input
                      type="text"
                      id="bankName"
                      name="bankName"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                      placeholder="BANK NAME"
                    />
                  </div>
                </div>
              </div>
              
              <div class="border-t pt-4">
                <h4 class="font-semibold mb-4">Declaration & Terms & Conditions of Service</h4>
                <div class="space-y-4 text-sm">
                  <div class="bg-yellow-50 p-4 rounded-lg">
                    <p class="font-semibold mb-2">Terms & Conditions:</p>
                    <ul class="space-y-2 text-xs">
                      <li>
                        a. It is a criminal offence to provide false information or make misleading inputs when
                        completing this form.
                      </li>
                      <li>
                        b. You may be prosecuted if we find out that your Certificate of Occupancy or Land of Grant
                        (RofO) is Fake or Falsified.
                      </li>
                      <li>
                        c. Payment of Re-certification processing fee is non-refundable and does not guarantee issuance
                        of new Digital Certificate.
                      </li>
                    </ul>
                  </div>
                  
                  <div class="form-field">
                    <label class="checkbox-item">
                      <input type="checkbox" id="agreeTerms" name="agreeTerms" required />
                      <div class="checkbox-box"></div>
                      <span class="text-sm">
                        I agree with the above terms and conditions of service <span class="text-red-500">*</span>
                      </span>
                    </label>
                    <div class="error-message">You must agree to the terms and conditions</div>
                  </div>
                </div>
              </div>
              
              <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                <div class="text-center mb-4">
                  <div class="font-semibold">Signature/Thumb Print</div>
                  <div class="text-xs text-gray-500">
                    (please sign/Thumb Print clearly within the box provided)
                  </div>
                </div>
                <div class="signature-area">
                  <span class="text-gray-500">Signature Area</span>
                </div>
              </div>
              
              <div class="bg-blue-50 p-4 rounded-lg">
                <h5 class="font-semibold mb-2">Contact Information:</h5>
                <div class="text-xs space-y-1">
                  <p>KANGIS Complex 2 Dr Bala Muhammad Way, Nassarawa G.R.A. Kano.</p>
                  <p>Tel: +234 (0)900 0000 00, +234 (0) 900 000 000 +234 (0) 810 0000 000</p>
                  <p>Email: recertification@kangis.gov.ng, info@kangis.gov.ng, support@kangis.gov.ng</p>
                  <p>Website: https://kangis.gov.ng</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>

      <!-- Form Navigation -->
      <div class="flex justify-between pt-4 border-t">
        <button
          type="button"
          id="prev-btn"
          class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          Previous
        </button>
        
        <button
          type="button"
          id="next-btn"
          class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
        >
          <span class="next-text">Next</span>
          <div class="loading-spinner hidden"></div>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2">
  <!-- Toast messages will be inserted here -->
</div>

<script>
// Application state
let currentStep = 1;
const totalSteps = 6;

// Form data state
let formData = {};

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
  // Initialize Lucide icons
  lucide.createIcons();
  
  // Set up event listeners
  setupEventListeners();
  
  // Set current date
  setCurrentDate();
  
  // Initialize form
  updateStepDisplay();
});

function setupEventListeners() {
  // Modal controls
  document.getElementById('close-modal').addEventListener('click', closeModal);
  
  // Navigation buttons
  document.getElementById('prev-btn').addEventListener('click', previousStep);
  document.getElementById('next-btn').addEventListener('click', nextStep);
  
  // Form field updates
  const form = document.getElementById('recertification-form');
  form.addEventListener('input', handleFormInput);
  form.addEventListener('change', handleFormChange);
  
  // Conditional field displays
  setupConditionalFields();
  
  // Close modal on backdrop click
  document.getElementById('new-recertification-modal').addEventListener('click', function(e) {
    if (e.target === this) {
      closeModal();
    }
  });
}

function setupConditionalFields() {
  // Original owner conditional fields
  document.querySelectorAll('input[name="isOriginalOwner"]').forEach(radio => {
    radio.addEventListener('change', function() {
      const ownershipDetails = document.getElementById('ownership-details');
      if (this.value === 'no') {
        ownershipDetails.classList.remove('hidden');
      } else {
        ownershipDetails.classList.add('hidden');
      }
    });
  });
  
  // Encumbrance conditional fields
  document.querySelectorAll('input[name="isEncumbered"]').forEach(radio => {
    radio.addEventListener('change', function() {
      const encumbranceReason = document.getElementById('encumbrance-reason');
      if (this.value === 'yes') {
        encumbranceReason.classList.remove('hidden');
      } else {
        encumbranceReason.classList.add('hidden');
      }
    });
  });
  
  // Mortgage conditional fields
  document.querySelectorAll('input[name="hasMortgage"]').forEach(radio => {
    radio.addEventListener('change', function() {
      const mortgageDetails = document.getElementById('mortgage-details');
      if (this.value === 'yes') {
        mortgageDetails.classList.remove('hidden');
      } else {
        mortgageDetails.classList.add('hidden');
      }
    });
  });
}

function handleFormInput(event) {
  const { name, value } = event.target;
  if (name) {
    formData[name] = value;
    clearFieldError(name);
  }
}

function handleFormChange(event) {
  const { name, value, type, checked } = event.target;
  if (name) {
    if (type === 'checkbox') {
      formData[name] = checked;
    } else {
      formData[name] = value;
    }
    clearFieldError(name);
  }
}

function setCurrentDate() {
  const today = new Date().toISOString().split('T')[0];
  document.getElementById('applicationDate').value = today;
  formData.applicationDate = today;
}

function updateStepDisplay() {
  // Hide all step contents
  document.querySelectorAll('.step-content').forEach(content => {
    content.classList.add('hidden');
  });
  
  // Show current step content
  document.getElementById(`step-content-${currentStep}`).classList.remove('hidden');
  
  // Update step indicators
  for (let i = 1; i <= totalSteps; i++) {
    const stepCircle = document.getElementById(`step-${i}`);
    const stepLine = document.getElementById(`line-${i}`);
    
    if (i <= currentStep) {
      stepCircle.classList.remove('inactive');
      stepCircle.classList.add('active');
    } else {
      stepCircle.classList.remove('active');
      stepCircle.classList.add('inactive');
    }
    
    if (stepLine) {
      if (i < currentStep) {
        stepLine.classList.remove('inactive');
        stepLine.classList.add('active');
      } else {
        stepLine.classList.remove('active');
        stepLine.classList.add('inactive');
      }
    }
  }
  
  // Update navigation buttons
  const prevBtn = document.getElementById('prev-btn');
  const nextBtn = document.getElementById('next-btn');
  const nextText = nextBtn.querySelector('.next-text');
  
  prevBtn.disabled = currentStep === 1;
  
  if (currentStep === totalSteps) {
    nextText.textContent = 'Submit Application';
  } else {
    nextText.textContent = 'Next';
  }
}

function previousStep() {
  if (currentStep > 1) {
    currentStep--;
    updateStepDisplay();
  }
}

async function nextStep() {
  if (currentStep < totalSteps) {
    if (validateCurrentStep()) {
      currentStep++;
      updateStepDisplay();
    }
  } else {
    // Submit form
    await submitForm();
  }
}

function validateCurrentStep() {
  const currentStepElement = document.getElementById(`step-content-${currentStep}`);
  const requiredFields = currentStepElement.querySelectorAll('[required]');
  let isValid = true;
  
  // Clear previous errors
  currentStepElement.querySelectorAll('.form-field').forEach(field => {
    field.classList.remove('error');
  });
  
  // Validate required fields
  requiredFields.forEach(field => {
    const value = field.type === 'checkbox' ? field.checked : field.value;
    const isRadioGroup = field.type === 'radio';
    
    if (isRadioGroup) {
      const radioGroup = currentStepElement.querySelectorAll(`input[name="${field.name}"]`);
      const isChecked = Array.from(radioGroup).some(radio => radio.checked);
      if (!isChecked) {
        showFieldError(field.name);
        isValid = false;
      }
    } else if (!value || (typeof value === 'string' && value.trim() === '')) {
      showFieldError(field.name);
      isValid = false;
    }
  });
  
  // Additional validation for step 6 (terms agreement)
  if (currentStep === 6) {
    const agreeTerms = document.getElementById('agreeTerms');
    if (!agreeTerms.checked) {
      showFieldError('agreeTerms');
      isValid = false;
    }
  }
  
  if (!isValid) {
    showToast('Please fill in all required fields correctly', 'error');
  }
  
  return isValid;
}

function showFieldError(fieldName) {
  const field = document.querySelector(`[name="${fieldName}"]`);
  if (field) {
    const formField = field.closest('.form-field');
    if (formField) {
      formField.classList.add('error');
    }
  }
}

function clearFieldError(fieldName) {
  const field = document.querySelector(`[name="${fieldName}"]`);
  if (field) {
    const formField = field.closest('.form-field');
    if (formField) {
      formField.classList.remove('error');
    }
  }
}

async function submitForm() {
  const nextBtn = document.getElementById('next-btn');
  const nextText = nextBtn.querySelector('.next-text');
  const loadingSpinner = nextBtn.querySelector('.loading-spinner');
  
  // Show loading state
  nextBtn.disabled = true;
  nextText.textContent = 'Submitting...';
  loadingSpinner.classList.remove('hidden');
  
  try {
    // Collect all form data
    const form = document.getElementById('recertification-form');
    const currentFormData = new FormData(form);
    const applicationData = Object.fromEntries(currentFormData.entries());
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 3000));
    
    console.log('Recertification application submitted:', applicationData);
    
    // Show success message
    showToast('Application submitted successfully!', 'success');
    
    // Close modal after short delay
    setTimeout(() => {
      closeModal();
    }, 2000);
    
  } catch (error) {
    console.error('Error submitting application:', error);
    showToast('Failed to submit application. Please try again.', 'error');
  } finally {
    // Reset loading state
    nextBtn.disabled = false;
    nextText.textContent = 'Submit Application';
    loadingSpinner.classList.add('hidden');
  }
}

function closeModal() {
  const modal = document.getElementById('new-recertification-modal');
  modal.style.display = 'none';
  resetForm();
}

function resetForm() {
  // Reset step
  currentStep = 1;
  updateStepDisplay();
  
  // Reset form
  document.getElementById('recertification-form').reset();
  
  // Reset form data
  formData = {};
  
  // Clear all errors
  document.querySelectorAll('.form-field').forEach(field => {
    field.classList.remove('error');
  });
  
  // Hide conditional fields
  document.getElementById('ownership-details').classList.add('hidden');
  document.getElementById('encumbrance-reason').classList.add('hidden');
  document.getElementById('mortgage-details').classList.add('hidden');
  
  // Set current date again
  setCurrentDate();
}

function showToast(message, type = 'info') {
  const toastContainer = document.getElementById('toast-container');
  const toastId = `toast-${Date.now()}`;
  
  const typeClasses = {
    success: 'bg-green-600 text-white',
    error: 'bg-red-600 text-white',
    warning: 'bg-yellow-600 text-white',
    info: 'bg-blue-600 text-white'
  };
  
  const typeIcons = {
    success: 'check-circle',
    error: 'alert-circle',
    warning: 'alert-triangle',
    info: 'info'
  };
  
  const toast = document.createElement('div');
  toast.id = toastId;
  toast.className = `${typeClasses[type]} px-4 py-2 rounded-md shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300`;
  toast.innerHTML = `
    <i data-lucide="${typeIcons[type]}" class="h-4 w-4"></i>
    <span>${message}</span>
    <button onclick="removeToast('${toastId}')" class="ml-2 hover:bg-black/20 rounded p-1">
      <i data-lucide="x" class="h-3 w-3"></i>
    </button>
  `;
  
  toastContainer.appendChild(toast);
  lucide.createIcons();
  
  // Animate in
  setTimeout(() => {
    toast.classList.remove('translate-x-full');
  }, 100);
  
  // Auto remove after 5 seconds
  setTimeout(() => {
    removeToast(toastId);
  }, 5000);
}

function removeToast(toastId) {
  const toast = document.getElementById(toastId);
  if (toast) {
    toast.classList.add('translate-x-full');
    setTimeout(() => {
      toast.remove();
    }, 300);
  }
}

// API for external usage (can be called from parent page)
window.NewRecertificationDialog = {
  open: function() {
    document.getElementById('new-recertification-modal').style.display = 'flex';
  },
  close: closeModal,
  reset: resetForm
};
</script>
 
