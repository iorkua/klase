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

                    <!-- Applicant Type -->
                    <div class="form-field">
                        <label for="applicantType" class="block text-sm font-medium text-gray-700 mb-1">
                            Applicant Type <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="applicantType"
                            name="applicantType"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                        >
                            <option value="Individual" selected>Individual</option>
                            <option value="Corporate">Corporate</option>
                            <option value="Government Body">Government Body</option>
                            <option value="Multiple Owners">Multiple Owners</option>
                        </select>
                        <div class="error-message">Applicant type is required</div>
                    </div>
                    
                    <!-- Individual Fields -->
                    <div id="individual-fields" class="space-y-4">
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

                    <!-- Corporate Fields -->
                    <div id="corporate-fields" class="space-y-4 hidden">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="form-field col-span-2">
                                <label for="organisationName" class="block text-sm font-medium text-gray-700 mb-1">
                                    Organisation Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="organisationName"
                                    name="organisationName"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                                    placeholder="ORGANISATION NAME"
                                />
                                <div class="error-message">Organisation name is required</div>
                            </div>
                            <div class="form-field col-span-2 md:col-span-1">
                                <label for="cacRegistrationNo" class="block text-sm font-medium text-gray-700 mb-1">
                                    CAC Registration No. <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="cacRegistrationNo"
                                    name="cacRegistrationNo"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10"
                                    placeholder="RC/BN NUMBER"
                                />
                                <div class="error-message">CAC Registration number is required</div>
                            </div>
                            <div class="form-field col-span-2 md:col-span-1">
                                <label for="typeOfOrganisation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Type of Organisation <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="typeOfOrganisation"
                                    name="typeOfOrganisation"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                                    placeholder="E.G. PRIVATE LTD, NGO"
                                />
                                <div class="error-message">Type of organisation is required</div>
                            </div>
                            <div class="form-field col-span-2 md:col-span-1">
                                <label for="typeOfBusiness" class="block text-sm font-medium text-gray-700 mb-1">
                                    Type of Business <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="typeOfBusiness"
                                    name="typeOfBusiness"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm transition-all focus:outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-600/10 uppercase"
                                    placeholder="E.G. CONSTRUCTION, TRADING"
                                />
                                <div class="error-message">Type of business is required</div>
                            </div>
                        </div>
                    </div>
                    <!-- Multiple Owners Fields -->
                    <div id="multiple-owners-fields" class="space-y-4 hidden">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold">Owners</h4>
                            <button type="button" id="add-owner-btn" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-3 py-1.5 transition-all cursor-pointer bg-blue-600 text-white hover:bg-blue-700">
                                Add Owner
                            </button>
                        </div>
                        <div id="owners-list" class="space-y-4"></div>
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