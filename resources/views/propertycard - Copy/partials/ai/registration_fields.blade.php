<!-- Registration Number Components (Vanilla JS Version for AI Assistant) -->
<div class="space-y-1" id="registration-section">
    <label class="text-sm">Registration Number</label>
    <div class="grid grid-cols-5 gap-2">
        <div>
            <label for="serialNo" class="text-xs">Serial No.</label>
            <input id="serialNo" name="serialNo" class="form-input text-xs py-1" placeholder="e.g. 1" oninput="updatePageNoFromSerial()">
        </div>
        <div>
            <label for="pageNo" class="text-xs text-gray-500">Page No. (Auto-filled)</label>
            <input id="pageNo" name="pageNo" readonly class="form-input text-xs py-1 bg-gray-100 text-gray-500 cursor-not-allowed" placeholder="Same as Serial No.">
        </div>
        <div>
            <label for="volumeNo" class="text-xs">Volume No.</label>
            <input id="volumeNo" name="volumeNo" class="form-input text-xs py-1" placeholder="e.g. 2" oninput="updateRegistrationPreview()">
        </div>
        <div>
            <label for="regDate" class="text-xs">Reg Date</label>
            <input id="regDate" name="regDate" type="date" class="form-input text-xs py-1" onchange="validateRegistrationDate(this.value)">
        </div>
        <div>
            <label for="regTime" class="text-xs">Reg Time (8AM-5PM)</label>
            <input id="regTime" name="regTime" type="time" class="form-input text-xs py-1" min="08:00" max="17:00" onchange="validateRegistrationTime(this.value)">
        </div>
    </div>
    <div id="registration-preview" class="hidden mt-2 p-3 bg-blue-50 border-2 border-blue-200 rounded-lg shadow-sm">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-semibold text-blue-700">Registration Number:</span>
            </div>
            <span id="registration-display" class="text-lg font-bold text-blue-800 tracking-wider">Not set</span>
        </div>
        <div class="mt-1.5 flex justify-between items-center">
            <div class="text-xs text-blue-600">Format: Serial No/Page No/Volume No</div>
            <div id="registration-complete" class="hidden text-xs font-medium text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">Complete</div>
        </div>
    </div>
</div>

<script>
// Registration fields functionality for AI Assistant (Vanilla JS)
function updatePageNoFromSerial() {
    const serialNo = document.getElementById('serialNo').value;
    const pageNo = document.getElementById('pageNo');
    
    // Auto-fill Page No with Serial No value
    pageNo.value = serialNo;
    
    // Update preview
    updateRegistrationPreview();
}

function updateRegistrationPreview() {
    const serialNo = document.getElementById('serialNo').value || '';
    const pageNo = document.getElementById('pageNo').value || '';
    const volumeNo = document.getElementById('volumeNo').value || '';
    
    const preview = document.getElementById('registration-preview');
    const display = document.getElementById('registration-display');
    const complete = document.getElementById('registration-complete');
    
    // Show preview if any field has value
    if (serialNo || pageNo || volumeNo) {
        preview.classList.remove('hidden');
        
        // Update display
        const regNoDisplay = [serialNo, pageNo, volumeNo].filter(Boolean).join('/') || 'Not set';
        display.textContent = regNoDisplay;
        
        // Show complete indicator if all fields are filled
        if (serialNo && pageNo && volumeNo) {
            complete.classList.remove('hidden');
        } else {
            complete.classList.add('hidden');
        }
    } else {
        preview.classList.add('hidden');
    }
}

function validateRegistrationDate(value) {
    if (!value) return;
    
    const selectedDate = new Date(value);
    const dayOfWeek = selectedDate.getDay();
    
    // Check if weekend (0 = Sunday, 6 = Saturday)
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        const nextMonday = new Date(selectedDate);
        const daysToAdd = dayOfWeek === 0 ? 1 : 2; // Sunday: +1, Saturday: +2
        nextMonday.setDate(selectedDate.getDate() + daysToAdd);
        
        const nextMondayStr = nextMonday.toISOString().split('T')[0];
        document.getElementById('regDate').value = nextMondayStr;
        
        showToast(`Weekend selected. Date moved to next Monday: ${nextMondayStr}`, 'warning');
    }
}

function validateRegistrationTime(value) {
    if (!value) return;
    
    const [hours, minutes] = value.split(':').map(Number);
    const timeInMinutes = hours * 60 + minutes;
    const startTime = 8 * 60; // 8:00 AM
    const endTime = 17 * 60;  // 5:00 PM
    
    if (timeInMinutes < startTime || timeInMinutes > endTime) {
        document.getElementById('regTime').value = '09:00';
        showToast('Time outside working hours (8:00 AM - 5:00 PM). Set to 9:00 AM.', 'warning');
    }
}

function getCurrentWorkingDate() {
    const today = new Date();
    const dayOfWeek = today.getDay();
    
    // If today is weekend, move to next Monday
    if (dayOfWeek === 0 || dayOfWeek === 6) {
        const daysToAdd = dayOfWeek === 0 ? 1 : 2;
        today.setDate(today.getDate() + daysToAdd);
    }
    
    return today.toISOString().split('T')[0];
}

function getCurrentWorkingTime() {
    const now = new Date();
    const hours = now.getHours();
    
    // If outside working hours, set to 9:00 AM
    if (hours < 8 || hours >= 17) {
        return '09:00';
    }
    
    return now.toTimeString().slice(0, 5);
}

// Initialize registration fields with current working date/time
document.addEventListener('DOMContentLoaded', function() {
    // Set default values
    const regDate = document.getElementById('regDate');
    const regTime = document.getElementById('regTime');
    
    if (regDate && !regDate.value) {
        regDate.value = getCurrentWorkingDate();
    }
    
    if (regTime && !regTime.value) {
        regTime.value = getCurrentWorkingTime();
    }
});
</script>