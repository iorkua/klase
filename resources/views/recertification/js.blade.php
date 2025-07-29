<script>
  // Sample applications data
  const sampleApplications = [
    {
      id: "1",
      applicantName: "FATIMA AHMED IBRAHIM",
      plotNumber: "Plot 123, Block A",
      fileNumber: "KN/F/2020/456",
      lga: "Kano Municipal",
      status: "approved",
      submissionDate: "2024-01-15",
      approvalDate: "2024-01-25",
      reason: "Certificate Lost",
      phoneNumber: "08012345678",
      email: "fatima.ahmed@email.com",
    },
    {
      id: "2",
      applicantName: "IBRAHIM YUSUF MOHAMMED",
      plotNumber: "Plot 45, Block C",
      fileNumber: "KN/F/2019/789",
      lga: "Fagge",
      status: "approved",
      submissionDate: "2024-01-10",
      approvalDate: "2024-01-20",
      reason: "Name Correction",
      phoneNumber: "08087654321",
      email: "ibrahim.yusuf@email.com",
    },
    {
      id: "3",
      applicantName: "KHADIJA MUSA ALI",
      plotNumber: "Plot 67, Block B",
      fileNumber: "KN/F/2021/234",
      lga: "Gwale",
      status: "approved",
      submissionDate: "2024-01-05",
      approvalDate: "2024-01-18",
      reason: "Certificate Damaged",
      phoneNumber: "08098765432",
      email: "khadija.musa@email.com",
    },
    {
      id: "4",
      applicantName: "SANI ABDULLAHI HASSAN",
      plotNumber: "Plot 89, Block D",
      fileNumber: "KN/F/2018/567",
      lga: "Dala",
      status: "approved",
      submissionDate: "2024-01-12",
      approvalDate: "2024-01-28",
      reason: "Certificate Renewal",
      phoneNumber: "08076543210",
      email: "sani.abdullahi@email.com",
    },
    {
      id: "5",
      applicantName: "AMINA GARBA USMAN",
      plotNumber: "Plot 12, Block E",
      fileNumber: "KN/F/2022/890",
      lga: "Tarauni",
      status: "approved",
      submissionDate: "2024-01-08",
      approvalDate: "2024-01-22",
      reason: "Replacement",
      phoneNumber: "08065432109",
      email: "amina.garba@email.com",
    },
    {
      id: "6",
      applicantName: "USMAN MOHAMMED BELLO",
      plotNumber: "Plot 34, Block F",
      fileNumber: "KN/F/2017/345",
      lga: "Nassarawa",
      status: "approved",
      submissionDate: "2024-01-03",
      approvalDate: "2024-01-16",
      reason: "Certificate Update",
      phoneNumber: "08054321098",
      email: "usman.mohammed@email.com",
    },
    {
      id: "7",
      applicantName: "ZAINAB IBRAHIM YAKUBU",
      plotNumber: "Plot 56, Block G",
      fileNumber: "KN/F/2016/678",
      lga: "Ungogo",
      status: "approved",
      submissionDate: "2024-01-01",
      approvalDate: "2024-01-14",
      reason: "Certificate Reissuance",
      phoneNumber: "08043210987",
      email: "zainab.ibrahim@email.com",
    },
    {
      id: "8",
      applicantName: "ALIYU HASSAN MUSA",
      plotNumber: "Plot 78, Block H",
      fileNumber: "KN/F/2015/901",
      lga: "Kumbotso",
      status: "approved",
      submissionDate: "2023-12-28",
      approvalDate: "2024-01-12",
      reason: "Certificate Correction",
      phoneNumber: "08032109876",
      email: "aliyu.hassan@email.com",
    },
  ];
  
  // Global state
  let filteredApplications = [...sampleApplications];
  let searchTerm = '';
  let ocrMode = false;
  
  // Initialize the application
  document.addEventListener('DOMContentLoaded', function() {
    // Initialize Lucide icons
    lucide.createIcons();
    
    // Set up event listeners
    setupEventListeners();
    
    // Render initial data
    renderApplicationsTable();
  });
  
  function setupEventListeners() {
    // Search functionality
    document.getElementById('search-input').addEventListener('input', handleSearch);
    
    // OCR mode toggle
    document.getElementById('ocr-mode-toggle').addEventListener('change', toggleOcrMode);
    document.getElementById('back-from-ocr').addEventListener('click', () => toggleOcrMode(false));
    
    // Modal controls
    document.getElementById('new-application-btn').addEventListener('click', openNewApplicationModal);
    document.getElementById('close-details-modal').addEventListener('click', closeDetailsModal);
    
    // Close modals on backdrop click
    document.getElementById('details-modal').addEventListener('click', function(e) {
      if (e.target === this) closeDetailsModal();
    });
  }
  
  function handleSearch(event) {
    searchTerm = event.target.value.toLowerCase();
    filterApplications();
  }
  
  function filterApplications() {
    filteredApplications = sampleApplications.filter(app => {
      return app.applicantName.toLowerCase().includes(searchTerm) ||
             app.plotNumber.toLowerCase().includes(searchTerm);
    });
    
    renderApplicationsTable();
  }
  
  function renderApplicationsTable() {
    const tbody = document.getElementById('applications-table-body');
    const noResults = document.getElementById('no-results');
    const noResultsMessage = document.getElementById('no-results-message');
    const applicationsCount = document.getElementById('applications-count');
    
    applicationsCount.textContent = filteredApplications.length;
    
    if (filteredApplications.length === 0) {
      tbody.innerHTML = '';
      noResultsMessage.textContent = searchTerm ? 
        'Try adjusting your search criteria' : 
        'No approved applications available';
      noResults.classList.remove('hidden');
      return;
    }
    
    noResults.classList.add('hidden');
    
    tbody.innerHTML = filteredApplications.map(application => `
      <tr class="border-b table-row">
        <td class="p-4">
          <div>
            <div class="font-medium">${application.applicantName}</div>
            <div class="text-sm text-gray-600">${application.phoneNumber}</div>
          </div>
        </td>
        <td class="p-4">
          <div>
            <div class="font-medium">${application.plotNumber}</div>
            <div class="text-sm text-gray-600">${application.fileNumber}</div>
          </div>
        </td>
        <td class="p-4">${application.lga}</td>
        <td class="p-4">
          <div class="flex items-center gap-2">
            <i data-lucide="calendar" class="h-4 w-4 text-gray-400"></i>
            ${new Date(application.approvalDate).toLocaleDateString()}
          </div>
        </td>
        <td class="p-4">
          <div class="flex items-center gap-2">
            <button onclick="viewApplicationDetails('${application.id}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-2 py-1 transition-all cursor-pointer bg-transparent text-gray-700 hover:bg-gray-100">
              <i data-lucide="eye" class="h-4 w-4"></i>
            </button>
            <button onclick="editApplication('${application.id}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-2 py-1 transition-all cursor-pointer bg-transparent text-gray-700 hover:bg-gray-100">
              <i data-lucide="edit" class="h-4 w-4"></i>
            </button>
            <button onclick="downloadApplication('${application.id}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-2 py-1 transition-all cursor-pointer bg-transparent text-gray-700 hover:bg-gray-100">
              <i data-lucide="download" class="h-4 w-4"></i>
            </button>
          </div>
        </td>
      </tr>
    `).join('');
    
    // Re-initialize Lucide icons
    lucide.createIcons();
  }
  
  function toggleOcrMode(force = null) {
    const toggle = document.getElementById('ocr-mode-toggle');
    const mainView = document.querySelector('.container');
    const ocrView = document.getElementById('ocr-mode-view');
    
    if (force !== null) {
      ocrMode = force;
      toggle.checked = force;
    } else {
      ocrMode = toggle.checked;
    }
    
    if (ocrMode) {
      mainView.style.display = 'none';
      ocrView.classList.remove('hidden');
    } else {
      mainView.style.display = 'block';
      ocrView.classList.add('hidden');
    }
  }
  
  function openNewApplicationModal() {
    document.getElementById('new-recertification-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
  }
  
  
  function viewApplicationDetails(applicationId) {
    const application = sampleApplications.find(app => app.id === applicationId);
    if (!application) return;
    
    const detailsContent = document.getElementById('application-details-content');
    
    detailsContent.innerHTML = `
      <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Application Information</h4>
            <div class="space-y-3">
              <div>
                <label class="text-sm font-medium text-gray-600">Status</label>
                <p><span class="badge badge-success">Approved</span></p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-600">Submission Date</label>
                <p>${new Date(application.submissionDate).toLocaleDateString()}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-600">Approval Date</label>
                <p>${new Date(application.approvalDate).toLocaleDateString()}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-600">Reason</label>
                <p>${application.reason}</p>
              </div>
            </div>
          </div>
          
          <div class="space-y-4">
            <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Applicant Information</h4>
            <div class="space-y-3">
              <div>
                <label class="text-sm font-medium text-gray-600">Full Name</label>
                <p class="font-medium">${application.applicantName}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-600">Phone Number</label>
                <p>${application.phoneNumber}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-600">Email</label>
                <p>${application.email}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-600">LGA</label>
                <p>${application.lga}</p>
              </div>
            </div>
          </div>
        </div>
        
        <div class="space-y-4">
          <h4 class="text-lg font-semibold text-gray-900 border-b pb-2">Property Information</h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label class="text-sm font-medium text-gray-600">Plot Number</label>
              <p class="font-medium">${application.plotNumber}</p>
            </div>
            <div>
              <label class="text-sm font-medium text-gray-600">File Number</label>
              <p class="font-mono text-sm">${application.fileNumber}</p>
            </div>
          </div>
        </div>
        
        <div class="flex justify-end gap-3 pt-4 border-t">
          <button onclick="downloadApplication('${application.id}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer bg-transparent border border-gray-300 text-gray-700 hover:bg-gray-50 gap-2">
            <i data-lucide="download" class="h-4 w-4"></i>
            Download Certificate
          </button>
          <button onclick="editApplication('${application.id}')" class="inline-flex items-center justify-center rounded-md font-medium text-sm px-4 py-2 transition-all cursor-pointer border-0 bg-blue-600 text-white hover:bg-blue-700 gap-2">
            <i data-lucide="edit" class="h-4 w-4"></i>
            Edit Application
          </button>
        </div>
      </div>
    `;
    
    document.getElementById('details-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Re-initialize Lucide icons
    lucide.createIcons();
  }
  
  function closeDetailsModal() {
    document.getElementById('details-modal').classList.add('hidden');
    document.body.style.overflow = 'auto';
  }
  
  function editApplication(applicationId) {
    showToast('Edit functionality would be implemented here', 'info');
  }
  
  function downloadApplication(applicationId) {
    const application = sampleApplications.find(app => app.id === applicationId);
    if (!application) return;
    
    showToast(`Downloading certificate for ${application.applicantName}...`, 'success');
    
    // Simulate download
    setTimeout(() => {
      showToast('Certificate downloaded successfully!', 'success');
    }, 2000);
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
    
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `${typeClasses[type]} px-4 py-2 rounded-md shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300 fade-in`;
    toast.innerHTML = `
      <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : type === 'warning' ? 'alert-triangle' : 'info'}" class="h-4 w-4"></i>
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
  </script>