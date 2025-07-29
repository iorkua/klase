<div class="modal-dialog shadow-none" role="document">
    <div class="max-w-6xl mx-auto">
        <div class="bg-white rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">
                    @if (\Auth::user()->type == 'super admin')
                        {{ __('Create Customer') }}
                    @else
                        {{ __('Create User') }}
                    @endif
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 absolute top-4 right-4" data-dismiss="modal" aria-label="Close">
                    <span class="sr-only">Close</span>
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{ Form::open(['url' => 'users', 'method' => 'post']) }}

            <div class="p-6 overflow-y-auto max-h-[80vh]" x-data="{
                selectedDept: '',
                showAll: false,
                userTypeId: '',
                userTypeName: '',
                userLevelId: '',
                userLevelName: '',
                availableLevels: [],
                
                async fetchUserLevels(userTypeId) {
                    if (!userTypeId) {
                        this.availableLevels = [];
                        return;
                    }
                    
                    try {
                        const response = await fetch('/users/get-levels/' + userTypeId);
                        const levels = await response.json();
                        this.availableLevels = levels;
                        
                        if (levels.length === 1) {
                            this.userLevelId = levels[0].id;
                            this.userLevelName = levels[0].name;
                        } else {
                            this.userLevelId = '';
                            this.userLevelName = '';
                        }
                    } catch (error) {
                        console.error('Error fetching user levels:', error);
                        this.availableLevels = [];
                    }
                },
                
                checkAll() {
                    document.querySelectorAll('#roles_grid > div').forEach(el => {
                        const isVisible = el.style.display !== 'none' && !el.hasAttribute('x-show') || 
                                         (el.hasAttribute('x-show') && el.offsetParent !== null);
                        if (isVisible) {
                            const checkbox = el.querySelector('input[type=checkbox]');
                            if (checkbox) checkbox.checked = true;
                        }
                    });
                },
                
                uncheckAll() {
                    document.querySelectorAll('#roles_grid > div').forEach(el => {
                        const isVisible = el.style.display !== 'none' && !el.hasAttribute('x-show') || 
                                         (el.hasAttribute('x-show') && el.offsetParent !== null);
                        if (isVisible) {
                            const checkbox = el.querySelector('input[type=checkbox]');
                            if (checkbox) checkbox.checked = false;
                        }
                    });
                },
                
                filterByDept() {
                    this.selectedDept = document.getElementById('department_id').value;
                    this.showAll = false;
                },
                
                showAllRoles() {
                    this.showAll = true;
                    this.selectedDept = '';
                },
                
                shouldShowRole(roleUserType, roleLevel, roleName, roleDeptId) {
                    // If showing all roles, show everything
                    if (this.showAll) {
                        return true;
                    }
                    
                    // Department filtering
                    if (this.selectedDept) {
                        const roleDepId = String(roleDeptId);
                        const selectedDepId = String(this.selectedDept);
                        
                        // Hide roles that belong to other departments (unless they're universal)
                        if (roleDepId !== 'null' && roleDepId !== '' && roleDepId !== 'undefined' && roleDepId !== selectedDepId) {
                            return false;
                        }
                    }
                    
                    // User Type and Level filtering
                    if (this.userTypeName && this.userLevelName) {
                        // Always show ALL user_type roles
                        if (roleUserType === 'ALL') {
                            return true;
                        }
                        
                        // Show roles that match the selected user type and level
                        if (roleUserType === this.userTypeName && roleLevel === this.userLevelName) {
                            return true;
                        }
                        
                        // Hierarchical access: higher levels can access lower level roles
                        if (this.userTypeName === 'Management') {
                            // Management can access Operations and User roles
                            if (roleUserType === 'Operations' || roleUserType === 'User') {
                                return true;
                            }
                        }
                        
                        if (this.userTypeName === 'Operations') {
                            // Operations can access User roles
                            if (roleUserType === 'User') {
                                return true;
                            }
                        }
                        
                        if (this.userTypeName === 'System') {
                            // System can access all role types
                            return true;
                        }
                        
                        // If we reach here and user type/level are selected, hide roles that don't match
                        return false;
                    }
                    
                    // If only user type is selected (no level yet)
                    if (this.userTypeName && !this.userLevelName) {
                        // Always show ALL user_type roles
                        if (roleUserType === 'ALL') {
                            return true;
                        }
                        
                        // Show roles that match the selected user type (any level)
                        if (roleUserType === this.userTypeName) {
                            return true;
                        }
                        
                        // Apply hierarchical access rules
                        if (this.userTypeName === 'Management') {
                            if (roleUserType === 'Operations' || roleUserType === 'User') {
                                return true;
                            }
                        }
                        
                        if (this.userTypeName === 'Operations') {
                            if (roleUserType === 'User') {
                                return true;
                            }
                        }
                        
                        if (this.userTypeName === 'System') {
                            return true;
                        }
                        
                        return false;
                    }
                    
                    // If no user type/level selected, show all roles (filtered by department only)
                    return true;
                }
            }">
                <div class="flex flex-wrap -mx-2">
                    @if (\Auth::user()->type != 'super admin')
                        <div class="w-full px-3">
                            {{-- Inputs Section --}}
                            <div class="flex flex-wrap -mx-2 mb-4">
                                {{-- 1. Username --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('username', __('Username'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        {{ Form::text('username', null, [
                                            'class' => 'w-full p-2 border border-gray-300 rounded-md text-sm',
                                            'placeholder' => __('Enter Username'),
                                            'required' => 'required'
                                        ]) }}
                                    </div>
                                </div>
                                {{-- 2. Password --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('password', __('Password'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        {{ Form::password('password', [
                                            'class' => 'w-full p-2 border border-gray-300 rounded-md text-sm',
                                            'placeholder' => __('Enter password'),
                                            'required' => 'required',
                                            'minlength' => '6'
                                        ]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap -mx-2 mb-4">
                                {{-- 3. First Name --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('first_name', __('First Name'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        {{ Form::text('first_name', null, [
                                            'class' => 'w-full p-2 border border-gray-300 rounded-md text-sm',
                                            'placeholder' => __('Enter First Name'),
                                            'required' => 'required'
                                        ]) }}
                                    </div>
                                </div>
                                {{-- 4. Last Name --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('last_name', __('Last Name'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        {{ Form::text('last_name', null, [
                                            'class' => 'w-full p-2 border border-gray-300 rounded-md text-sm',
                                            'placeholder' => __('Enter Last Name'),
                                            'required' => 'required'
                                        ]) }}
                                    </div>
                                </div>
                            </div>

                            <div class="flex flex-wrap -mx-2 mb-4">
                                {{-- 5. Phone Number --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('phone', __('Phone Number'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        {{ Form::text('phone', null, [
                                            'class' => 'w-full p-2 border border-gray-300 rounded-md text-sm',
                                            'placeholder' => __('Enter Phone Number'),
                                            'required' => 'required'
                                        ]) }}
                                    </div>
                                </div>
                                {{-- 6. Email --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('email', __('Email'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        {{ Form::text('email', null, [
                                            'class' => 'w-full p-2 border border-gray-300 rounded-md text-sm',
                                            'placeholder' => __('Enter email'),
                                            'required' => 'required'
                                        ]) }}
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap -mx-2 mb-4">
                                {{-- 7. Department --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('department_id', __('Department'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        {{ Form::select('department_id', $departments, null, [
                                            'class' => 'w-full p-2 border border-gray-300 rounded-md text-sm',
                                            'required' => 'required',
                                            'id' => 'department_id',
                                            'placeholder' => 'Select Department',
                                            '@change' => 'selectedDept = $event.target.value; showAll = !$event.target.value;'
                                        ]) }}
                                    </div>
                                </div>
                                {{-- 8. User Type --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('user_type', __('User Type'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        <select name="user_type" id="user_type"
                                            class="w-full p-2 border border-gray-300 rounded-md text-sm"
                                            @change="userTypeId = $event.target.value; userTypeName = $event.target.selectedOptions[0].text; fetchUserLevels(userTypeId);"
                                            required>
                                            <option value="">Select User Type</option>
                                            @foreach($userTypes as $userType)
                                                <option value="{{ $userType->id }}">{{ $userType->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-wrap -mx-2 mb-4">
                                {{-- 9. User Level --}}
                                <div class="w-full md:w-1/2 px-2 mb-4">
                                    <div>
                                        {{ Form::label('user_level', __('User Level'), ['class' => 'block text-sm font-medium text-gray-700 mb-1']) }}
                                        <div x-show="!userTypeId">
                                            <select name="user_level" id="user_level"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-100"
                                                disabled>
                                                <option value="">Select User Type First</option>
                                            </select>
                                        </div>
                                        <div x-show="availableLevels.length === 1 && userTypeId">
                                            <input type="text" id="user_level_display"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm bg-gray-100"
                                                x-bind:value="userLevelName"
                                                readonly>
                                            <input type="hidden" name="user_level" x-bind:value="userLevelName">
                                        </div>
                                        <div x-show="availableLevels.length > 1 && userTypeId">
                                            <select name="user_level" id="user_level"
                                                class="w-full p-2 border border-gray-300 rounded-md text-sm"
                                                @change="userLevelId = $event.target.value; userLevelName = $event.target.selectedOptions[0].text;"
                                                required>
                                                <option value="">Select User Level</option>
                                                <template x-for="level in availableLevels" :key="level.id">
                                                    <option :value="level.id" x-text="level.name"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{-- 10. Select role(s) --}}
                        <div class="mt-6" id="roles_container">
                            {{ Form::label('user_role', __('Select role(s)'), ['class' => 'block text-sm font-medium text-gray-700 mb-2']) }}
                            
                            <!-- User Type and Level Summary -->
                            <div class="mb-3 p-3 bg-blue-50 border border-blue-200 rounded-md text-sm" x-show="userTypeName">
                                <div class="font-medium text-blue-800 mb-1">Role Filtering Active:</div>
                                <div class="text-blue-700">
                                    <span class="font-medium">User Type:</span> <span x-text="userTypeName"></span> |
                                    <span class="font-medium">Level:</span> <span x-text="userLevelName || 'Not Set'"></span> |
                                    <span class="font-medium">Department:</span> <span x-text="selectedDept ? 'Selected' : 'All'"></span>
                                </div>
                                <div class="text-xs text-blue-600 mt-1">
                                    <strong>Access Rules:</strong><br>
                                    <span x-show="userTypeName === 'Management'">• Can access Management, Operations, and User roles</span>
                                    <span x-show="userTypeName === 'Operations'">• Can access Operations and User roles</span>
                                    <span x-show="userTypeName === 'User'">• Can access User roles only</span>
                                    <span x-show="userTypeName === 'System'">• Can access all role types</span>
                                    <br>• ALL user_type roles are always visible
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="mb-3 flex gap-2">
                                    <button type="button" @click="checkAll" class="text-xs py-1 px-2 rounded bg-green-500 text-white hover:bg-green-600">Check All Visible</button>
                                    <button type="button" @click="uncheckAll" class="text-xs py-1 px-2 rounded bg-red-500 text-white hover:bg-red-600">Uncheck All</button>
                                </div>
                                <div class="grid grid-cols-3 gap-3" id="roles_grid">
                                    @foreach ($userRoles as $role)
                                        <div class="flex items-start role-item" 
                                            x-show="shouldShowRole('{{ $role->user_type ?? '' }}', '{{ $role->level ?? '' }}', {{ json_encode($role->name) }}, '{{ $role->department_id ?? 'null' }}')"
                                            data-dept-id="{{ $role->department_id ?? 'null' }}"
                                            data-user-type="{{ $role->user_type ?? '' }}"
                                            data-level="{{ $role->level ?? '' }}">
                                            <div class="flex items-center h-5">
                                                <input type="checkbox" name="user_role[]" value="{{ $role->name }}" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label class="font-medium text-gray-700">{{ $role->name }}</label>
                                                <small class="text-gray-500 block">{{ $role->user_type ?? 'N/A' }} - {{ $role->level ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Role Loading Helper Button -->
                            <div class="mt-3 text-right">
                                <button type="button" id="showAllRolesBtn" @click="showAllRoles()" 
                                    :class="{'bg-indigo-600 text-white': showAll, 'text-indigo-600 border border-indigo-600': !showAll}"
                                    class="text-sm py-1 px-2 rounded">
                                    Show All Roles
                                </button>
                                <button type="button" id="filterRolesBtn" @click="filterByDept()" 
                                    :class="{'bg-indigo-600 text-white': !showAll, 'text-indigo-600 border border-indigo-600 bg-white': showAll}"
                                    class="text-sm py-1 px-2 rounded ml-2">
                                    Apply Filters
                                </button>
                            </div>
                            <!-- Department Filter Status Message -->
                            <div class="mt-2 text-sm" x-show="!showAll && (selectedDept || userTypeName)">
                                <span class="text-green-600">Filters applied - showing relevant roles only</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="px-6 py-3 bg-gray-50 text-right">
                {{ Form::submit(__('Create'), ['class' => 'inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']) }}
                <button type="button" class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" data-dismiss="modal">
                    {{ __('Cancel') }}
                </button>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>