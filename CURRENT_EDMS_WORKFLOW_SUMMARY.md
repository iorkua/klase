# Current EDMS Workflow - Complete Guide

## Overview

The Electronic Document Management System (EDMS) in the Klase application is a fully integrated, dynamic workflow system that manages land record documents through three interconnected stages. This system transforms static document management into a seamless, automated process.

## 🏗️ System Architecture

### Workflow Structure
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│  File Indexing  │───▶│    Scanning     │───▶│  Page Typing    │
│                 │    │                 │    │                 │
│ • Create index  │    │ • Upload docs   │    │ • Classify pages│
│ • Set metadata  │    │ • Add metadata  │    │ • Complete flow │
│ • Link to app   │    │ • Organize files│    │ • Final status  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
        │                       │                       │
        └───────────────────────┼───────────────────────┘
                                │
                    ┌─────────────────┐
                    │ file_indexings  │
                    │  (Central Hub)  │
                    └─────────────────┘
```

### Database Schema

#### 1. file_indexings (Central Table)
- **Purpose**: Main anchor linking applications to document workflow
- **Key Fields**:
  - `main_application_id` → Links to mother_applications
  - `subapplication_id` → Links to subapplications  
  - `recertification_application_id` → Links to recertification_applications
  - `file_number`, `file_title`, `land_use_type`
  - `plot_number`, `district`, `lga`
  - Boolean flags: `has_cofo`, `is_merged`, `has_transaction`, etc.

#### 2. scannings
- **Purpose**: Stores uploaded document files and metadata
- **Key Fields**:
  - `file_indexing_id` → FK to file_indexings
  - `document_path`, `original_filename`
  - `paper_size`, `document_type`, `notes`
  - `uploaded_by`, `status`

#### 3. pagetypings  
- **Purpose**: Page-level classification and metadata
- **Key Fields**:
  - `file_indexing_id` → FK to file_indexings
  - `scanning_id` → FK to scannings
  - `page_type`, `page_subtype`, `page_code`
  - `page_number`, `serial_number`
  - `typed_by`, `notes`, `is_important`

## 🎨 User Interface Design

### Modern Design System
The EDMS interface features a modern, gradient-based design with:

#### Visual Elements
- **Gradient Headers**: Purple-blue gradients (`#667eea` to `#764ba2`)
- **Clean Layout**: Maximum width containers with proper spacing
- **Card-Based Design**: Rounded corners and subtle shadows
- **Responsive Grid**: Adaptive layouts for different screen sizes
- **Subtle Textures**: SVG pattern overlays for visual depth

#### Dashboard Components
Each module features:
- **Statistics Cards**: Real-time counts with visual indicators
- **Progress Trackers**: Visual workflow progress indicators  
- **Action Buttons**: Prominent call-to-action elements
- **Status Badges**: Color-coded status indicators
- **Search Interface**: AJAX-powered search with instant results

#### Navigation Flow
- **Breadcrumb Navigation**: Clear workflow position indicators
- **Module Transitions**: Seamless navigation between stages
- **Progress Indicators**: Visual representation of completion status
- **Quick Actions**: Fast access to common operations

### Interface Components

#### File Indexing Interface
- **Application Search**: Dropdown with autocomplete
- **Property Forms**: Structured input fields with validation
- **File Number Display**: Prominent file number presentation
- **Status Overview**: Current indexing status display

#### Scanning Interface  
- **Upload Zone**: Drag-and-drop file upload area
- **Progress Tracking**: Real-time upload progress bars
- **Document Preview**: Thumbnail previews of uploaded files
- **Metadata Forms**: Document classification inputs

#### Page Typing Interface
- **Document Viewer**: Integrated PDF/image viewer
- **Classification Panel**: Page type selection controls
- **Navigation Controls**: Page-by-page navigation tools
- **Batch Operations**: Multi-page selection and operations

## 🔗 Application Integration

### Supported Application Types

#### 1. Primary Applications (Mother Applications)
- **Table**: `mother_applications`
- **Link Field**: `main_application_id` in `file_indexings`
- **Route**: `/edms/{applicationId}`
- **Features**: Full EDMS workflow support

#### 2. Unit Applications (Sub Applications)  
- **Table**: `subapplications`
- **Link Field**: `subapplication_id` in `file_indexings`
- **Route**: `/edms/sub/{applicationId}`
- **Features**: Unit-specific workflow handling

#### 3. Recertification Applications
- **Table**: `recertification_applications`
- **Link Field**: `recertification_application_id` in `file_indexings`
- **Route**: Custom recertification workflow
- **Features**: Recertification-specific processing

### Integration Points
- **Application Selection**: Dynamic dropdown populated from application tables
- **Data Inheritance**: Application details auto-populate file indexing
- **Status Synchronization**: EDMS status reflects back to application views
- **File Viewer Integration**: Completed files accessible from application pages

### Application Workflow Integration
```
Application Created → EDMS Available → File Indexing → Scanning → Page Typing → Files Viewable
      ↓                    ↓               ↓             ↓           ↓            ↓
   [Not Started]    [Button Enabled]  [In Progress] [In Progress] [In Progress] [Completed]
```

## 📁 File Structure & Organization

### Controller Structure
```
app/Http/Controllers/
├── EdmsController.php              # Main EDMS workflow orchestration
├── FileIndexController.php         # File indexing operations  
├── ScanningController.php          # Document upload and management
└── PageTypingController.php        # Page classification operations
```

### Model Structure
```
app/Models/
├── FileIndexing.php               # Central EDMS anchor model
├── Scanning.php                   # Document storage model
├── PageTyping.php                 # Page classification model
└── ApplicationMother.php          # Application integration
```

### View Structure
```
resources/views/
├── edms/                          # Main EDMS workflow views
│   ├── fileindexing.blade.php    # File indexing interface
│   ├── scanning.blade.php        # Document scanning interface
│   └── pagetyping.blade.php      # Page typing interface
├── fileindexing/                  # File indexing module views
│   ├── index.blade.php           # Dynamic dashboard
│   └── js/javascript.blade.php   # AJAX functionality
├── scanning/                      # Scanning module views
│   ├── index.blade.php           # Dynamic upload interface
│   └── assets/js.blade.php       # Upload functionality
└── pagetyping/                    # Page typing module views
    ├── index.blade.php           # Dynamic classification interface
    └── js/javascript.blade.php   # Classification functionality
```

### Route Structure
```
routes/
├── web.php                        # Main EDMS workflow routes (/edms/*)
└── apps2.php                      # Individual module routes
    ├── /fileindexing/*           # File indexing endpoints
    ├── /scanning/*               # Scanning endpoints
    └── /pagetyping/*             # Page typing endpoints
```

### Database Structure
```
Database Tables:
├── file_indexings                 # Central anchor table
├── scannings                      # Document storage
├── pagetypings                    # Page classifications
├── mother_applications            # Primary applications
├── subapplications               # Unit applications
└── recertification_applications   # Recertification applications
```

## 🔄 Complete Workflow Process

### Stage 1: File Indexing
**Route**: `/fileindexing`  
**Controller**: `FileIndexController`

#### Process:
1. **Application Selection**:
   - Choose from dropdown of existing applications
   - Or manually enter new file number
   - System validates against existing records

2. **Property Details**:
   - File title and land use type
   - Plot number, district, LGA
   - Property characteristics (COFO status, merger status, etc.)

3. **Index Creation**:
   - Creates record in `file_indexings` table
   - Assigns unique file indexing ID
   - Status: "Indexed"
   - Automatic progression to scanning stage

#### Key Features:
- Real-time statistics dashboard
- AJAX application search
- Duplicate prevention
- Smart file number generation

### Stage 2: Document Scanning  
**Route**: `/scanning`  
**Controller**: `ScanningController`

#### Process:
1. **File Selection**:
   - Auto-selected if coming from file indexing
   - Manual selection from indexed files dropdown

2. **Document Upload**:
   - Drag & drop or click to upload
   - Supports: PDF, JPG, PNG, TIFF
   - File size limit: 20MB per file
   - Automatic paper size detection

3. **Document Organization**:
   - Files linked to specific `file_indexing_id`
   - Metadata capture (document type, notes)
   - Progress tracking and status updates

4. **Completion**:
   - Status: "Scanned"
   - Automatic progression to page typing

#### Key Features:
- File-aware upload system
- Batch processing support
- Document metadata management
- Visual progress indicators

### Stage 3: Page Typing
**Route**: `/pagetyping`  
**Controller**: `PageTypingController`

#### Process:
1. **Document Loading**:
   - Loads scanned documents for selected file
   - Integrated document viewer
   - Page-by-page navigation

2. **Page Classification**:
   - Classify each page with type and subtype
   - Add page codes and serial numbers
   - Mark important pages
   - Add notes and metadata

3. **Data Entry Options**:
   - Individual page save
   - Batch save operations
   - Real-time progress tracking

4. **Workflow Completion**:
   - Status: "Completed" (when all pages typed)
   - Files become available in file viewer
   - Workflow officially complete

#### Key Features:
- Document viewer integration
- Page-level granular control
- Progress completion tracking
- Flexible save options

## 📊 Status System

### Status Logic (EDMS-Based)
The system uses actual EDMS data to determine status rather than manual flags:

#### 🔴 Not Started
- **Criteria**: No `file_indexings` record
- **Description**: Application not yet entered into EDMS
- **Action**: File indexing required

#### 🟡 In Progress  
- **Criteria**: Has `file_indexings` but incomplete workflow
- **Scenarios**:
  - File indexed, no documents uploaded
  - Documents scanned, no page typing
- **Action**: Continue to next stage

#### 🟢 Completed
- **Criteria**: All three stages complete
- **Requirements**:
  - ✅ File indexing record exists
  - ✅ Documents uploaded (scannings > 0)
  - ✅ Pages typed (pagetypings > 0)
- **Result**: File viewer enabled

### Status Calculation
```php
if ($file_indexing_id && $scanningCount > 0 && $pageTypingCount > 0) {
    $status = 'Completed';
} elseif ($file_indexing_id && $scanningCount > 0) {
    $status = 'In Progress'; // Scanned but not typed
} elseif ($file_indexing_id) {
    $status = 'In Progress'; // Indexed but no files
} else {
    $status = 'Not Started';
}
```

## 🛣️ API Endpoints

### File Indexing Endpoints
```
GET  /fileindexing                      - Dashboard with statistics
POST /fileindexing/store                - Create new file index
GET  /fileindexing/search/applications  - Search available applications  
GET  /fileindexing/list/file-indexings  - Get file indexing list
GET  /fileindexing/check/fileno         - Check file number status
```

### Scanning Endpoints
```
GET  /scanning                          - Dashboard
POST /scanning/upload                   - Upload documents
PUT  /scanning/update-details/{id}      - Update document metadata
GET  /scanning/list/scanned-files       - Get scanned files list
```

### Page Typing Endpoints
```
GET  /pagetyping                        - Dashboard
POST /pagetyping/store                  - Batch save page classifications
POST /pagetyping/save-single            - Save single page classification
GET  /pagetyping/list/page-typings      - Get page typings list
```

### EDMS Workflow Endpoints (Main)
```
GET  /edms/{applicationId}              - Main EDMS workflow
GET  /edms/sub/{applicationId}          - Sub-application workflow
GET  /edms/create-file-indexing/{id}    - Create file indexing
GET  /edms/scanning/{fileIndexingId}    - Scanning interface
GET  /edms/pagetyping/{fileIndexingId}  - Page typing interface
POST /edms/pagetyping/{id}/save-single  - Save single page
POST /edms/pagetyping/{id}/batch-save   - Batch save pages
GET  /edms/status/{applicationId}       - Get EDMS status
```

## 🔧 Technical Implementation

### Controllers
1. **EdmsController**: Main workflow orchestration
2. **FileIndexController**: File indexing operations
3. **ScanningController**: Document upload and management
4. **PageTypingController**: Page classification operations

### Models with Relationships
```php
// FileIndexing Model
public function scannings() {
    return $this->hasMany(Scanning::class, 'file_indexing_id');
}

public function pagetypings() {
    return $this->hasMany(PageTyping::class, 'file_indexing_id');
}

// Scanning Model  
public function fileIndexing() {
    return $this->belongsTo(FileIndexing::class, 'file_indexing_id');
}

public function pagetypings() {
    return $this->hasMany(PageTyping::class, 'scanning_id');
}

// PageTyping Model
public function fileIndexing() {
    return $this->belongsTo(FileIndexing::class, 'file_indexing_id');
}

public function scanning() {
    return $this->belongsTo(Scanning::class, 'scanning_id');
}
```

### Dynamic Features
- **Real-time Statistics**: Live database counts
- **AJAX Integration**: Seamless data loading
- **Progress Tracking**: Visual workflow indicators
- **Smart Navigation**: Automatic module progression
- **Search Functionality**: Powered by AJAX across modules

## 💾 Database Setup

### Required SQL Script
Execute `database_updates.sql` to:
- Add missing fields to existing tables
- Create foreign key relationships
- Add performance indexes
- Set up data integrity constraints

### Key Indexes for Performance
```sql
-- File indexing relationships
IX_file_indexings_main_application_id
IX_file_indexings_subapplication_id

-- Scanning relationships  
IX_scannings_file_indexing_id
IX_scannings_status

-- Page typing relationships
IX_pagetypings_file_indexing_id
IX_pagetypings_scanning_id
```

## 🎯 Usage Instructions

### For End Users

#### Starting a New File
1. Go to **File Indexing** (`/fileindexing`)
2. View dashboard with real-time statistics:
   - Pending files needing indexing
   - Files indexed today
   - Total indexed files
3. Click **"New File Index"**
4. Choose creation method:
   - **Application Selection**: Choose from existing applications dropdown
   - **Manual Entry**: Enter file number manually
5. Fill in property details:
   - File title and land use type
   - Plot number, district, LGA
   - Property characteristics (COFO, merger status, etc.)
6. Save → Auto-redirect to Scanning with `file_indexing_id`

#### Uploading Documents  
1. **Scanning** module opens with file pre-selected
2. View scanning dashboard statistics:
   - Documents uploaded today
   - Files pending page typing
   - Total scanned documents
3. Upload process:
   - Drag & drop or click to upload documents
   - Supported formats: PDF, JPG, PNG, TIFF (max 20MB each)
   - Real-time upload progress tracking
4. Add document metadata:
   - Document type classification
   - Paper size detection (A3/A4/A5/Letter/Legal)
   - Additional notes
5. Proceed to Page Typing when ready

#### Classifying Pages
1. **Page Typing** module loads with documents
2. View page typing dashboard statistics:
   - Files pending classification
   - Pages typed today
   - Completed classifications
3. Document viewing:
   - Integrated PDF/image viewer
   - Page-by-page navigation
   - Zoom and viewing controls
4. Page classification:
   - Select page type and subtype
   - Assign page codes and serial numbers
   - Mark important pages
   - Add classification notes
5. Save options:
   - **Individual Save**: Save single page classification
   - **Batch Save**: Save multiple pages at once
   - **Auto-save**: Periodic automatic saving
6. Complete workflow when all pages are classified

### For Administrators

#### Monitoring Workflow
- Real-time statistics on each module dashboard
- Status tracking across all applications
- Progress completion indicators

#### File Management
- Document organization by file indexing ID
- Metadata management and updates
- Status override capabilities

## 🔐 Security & Validation

### Access Control
- User authentication required
- Role-based access control
- Activity logging and audit trails
- Session management

### Input Validation
- File type restrictions (PDF, JPG, PNG, TIFF)
- File size limits (20MB per file)  
- Required field validation
- Data type validation
- Duplicate prevention

### Error Handling
- Graceful degradation for missing data
- User-friendly error messages
- Comprehensive error logging
- Recovery mechanisms

## 🚀 Performance Optimizations

### Database
- Foreign key indexes for joins
- Status indexes for filtering  
- Composite indexes for common queries
- Query optimization

### Caching
- Application data caching
- File metadata caching
- Statistics caching
- Session caching

### File Storage
- Organized directory structure
- Unique filename generation
- Storage path optimization
- File compression where applicable

## 📈 Future Enhancements

### Planned Features
- PDF page thumbnail generation
- OCR text extraction
- Advanced document search
- Workflow automation
- Reporting and analytics
- Document versioning
- Digital signatures
- Workflow notifications

### Integration Points
- External system APIs
- Document retention policies
- Backup and archival systems
- Mobile application support

## 🛠️ Troubleshooting

### Common Issues
1. **File Upload Fails**: Check file permissions and size limits
2. **Database Connection**: Verify SQL Server connection
3. **Missing Statistics**: Check database relationships
4. **Workflow Stuck**: Verify foreign key integrity

### Debugging Steps
1. Check error logs in `storage/logs`
2. Verify database connections
3. Test individual API endpoints
4. Validate file permissions
5. Review route configurations

## 📋 System Requirements

### Technical Requirements
- Laravel framework
- SQL Server database
- File storage system
- User authentication system
- AJAX/JavaScript support

### Browser Support
- Modern browsers with JavaScript enabled
- File upload API support
- PDF viewing capabilities

## 🎓 Best Practices

### Workflow Management
1. Always start with file indexing
2. Upload documents in logical batches
3. Use consistent naming conventions
4. Complete page typing promptly
5. Review and verify data accuracy

### Data Quality
- Validate application data before indexing
- Use standardized document types
- Maintain consistent page classification
- Regular data integrity checks

### Performance
- Monitor system resources
- Regular database maintenance
- File storage cleanup
- Performance monitoring

---

## 📋 Quick Reference Guide

### EDMS URLs
| Module | URL | Purpose |
|--------|-----|---------|
| Main EDMS | `/edms/{applicationId}` | Application-specific workflow |
| File Indexing | `/fileindexing` | Create and manage file indexes |
| Document Scanning | `/scanning` | Upload and organize documents |
| Page Typing | `/pagetyping` | Classify and type document pages |

### Status Progression
| Stage | Status | Description | Action Available |
|-------|--------|-------------|------------------|
| 0 | Not Started | No EDMS data | Start File Indexing |
| 1 | In Progress | File indexed, no docs | Upload Documents |
| 2 | In Progress | Docs uploaded, not typed | Type Pages |
| 3 | Completed | All stages done | View Files ✅ |

### Key Database Tables
| Table | Purpose | Key Fields |
|-------|---------|------------|
| `file_indexings` | Central anchor | `main_application_id`, `file_number` |
| `scannings` | Document storage | `file_indexing_id`, `document_path` |
| `pagetypings` | Page classification | `file_indexing_id`, `page_type` |

### File Upload Specifications
- **Supported Formats**: PDF, JPG, PNG, TIFF
- **Size Limit**: 20MB per file
- **Paper Sizes**: A3, A4, A5, Letter, Legal, Custom
- **Storage**: Organized by `file_indexing_id`

### API Endpoint Quick Reference
```
# File Indexing
POST /fileindexing/store                    # Create file index
GET  /fileindexing/search/applications      # Search applications

# Scanning  
POST /scanning/upload                       # Upload documents
GET  /scanning/list/scanned-files          # List files

# Page Typing
POST /pagetyping/store                      # Batch save pages
POST /pagetyping/save-single               # Save single page
```

---

## Summary

The current EDMS workflow is a fully implemented, dynamic system that provides:

✅ **Complete Integration** - Seamless workflow between all three modules  
✅ **Real-time Data** - Live statistics and status updates  
✅ **Robust Architecture** - Proper database relationships and constraints  
✅ **User-Friendly Interface** - Intuitive navigation and progress tracking  
✅ **Scalable Design** - Performance optimized with proper indexing  
✅ **Security Features** - Authentication, validation, and audit trails  

The system successfully transforms static document management into a dynamic, interconnected workflow that efficiently handles land record processing from initial indexing through final document classification and completion.