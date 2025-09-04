# Print Label System Implementation

## Overview
This implementation adds a complete backend and frontend system for managing print label batches in the KLAS system. The system allows batching of 30 files per batch and tracks which files have labels generated.

## Components Created

### 1. Database Tables (SQL Script)
**File:** `database_scripts/07_create_print_label_batches_table.sql`

#### Tables Created:
- **print_label_batches**: Main batch tracking table
  - Tracks batch numbers, sizes, formats, status
  - Supports up to 100 files per batch (configurable, defaulted to 30)
  - Status tracking: pending, generated, printed, completed

- **print_label_batch_items**: Individual file labels in batches
  - Links files to batches
  - Stores QR code and barcode data
  - Tracks printing status per item

#### Features:
- Automatic batch number generation (PLB-YYYY-MM-XXX format)
- Triggers for updating counts and timestamps
- Comprehensive indexing for performance
- Foreign key constraints for data integrity

### 2. Laravel Models

#### PrintLabelBatch Model
**File:** `app/Models/PrintLabelBatch.php`
- Manages label batches
- Auto-generates unique batch numbers
- Provides status management methods
- Relationship with batch items and users

#### PrintLabelBatchItem Model  
**File:** `app/Models/PrintLabelBatchItem.php`
- Manages individual file labels
- Auto-generates QR and barcode data
- Tracks printing status
- Relationship with batches and file indexings

#### Updated FileIndexing Model
**File:** `app/Models/FileIndexing.php` (updated)
- Added relationship to print label batch items
- Added helper methods for checking label status

### 3. Controller Updates

#### PrintLabelController
**File:** `app/Http/Controllers/PrintLabelController.php` (completely updated)

#### New API Endpoints:
- `GET /printlabel/api/files` - Get available files for labeling
- `POST /printlabel/api/batch` - Create new label batch
- `GET /printlabel/api/batches` - Get generated batches (with filters)
- `GET /printlabel/api/batch/{id}` - Get batch details
- `PATCH /printlabel/api/batch/{id}/print` - Mark batch as printed
- `DELETE /printlabel/api/batch/{id}` - Delete batch
- `GET /printlabel/api/statistics` - Get printing statistics

### 4. Routes Updates
**File:** `routes/apps2.php` (updated)
- Added all new API routes under `/printlabel/api/` prefix
- Maintained existing route for main page

### 5. Frontend Updates

#### Updated Blade Template
**File:** `resources/views/printlabel/index.blade.php` (extensively updated)

#### New Features:
- **Generated Batches Tab**: View and manage created batches
- **Backend Integration**: All file loading from database via API
- **Real-time Statistics**: Live updates of counts and status
- **Batch Management**: Create, view, print, and delete batches
- **Search & Filter**: Search files and filter batches by status
- **Status Tracking**: Visual status indicators for batches

#### JavaScript Updates:
- Complete API integration for all operations
- Async/await pattern for API calls
- Error handling and user feedback
- Real-time UI updates
- Batch creation workflow

## Key Features Implemented

### 1. Batch Management
- **30 files per batch limit** (configurable)
- **Unique batch numbering** (PLB-YYYY-MM-XXX)
- **Status workflow**: pending → generated → printed → completed
- **Automatic QR and barcode generation**

### 2. File Filtering
- Only shows files with `batch_no` (indexed files)
- Excludes files that already have labels printed
- Search functionality across multiple fields
- Real-time filtering and pagination

### 3. Label Status Tracking
- Tracks which files have labels generated
- Prevents duplicate label generation
- Status-based filtering and reporting
- User activity logging

### 4. User Interface
- **Tabbed interface**: Select Files, Generated Batches, Settings, Preview
- **Statistics dashboard**: Shows counts and status breakdowns
- **Batch operations**: View, print, delete batches
- **Real-time updates**: Automatic refresh of data
- **Responsive design**: Works on mobile and desktop

## Database Query Examples

### Get available files for labeling:
```sql
SELECT * FROM file_indexings 
WHERE batch_no IS NOT NULL 
AND id NOT IN (SELECT file_indexing_id FROM print_label_batch_items)
```

### Get batch statistics:
```sql
SELECT 
    COUNT(*) as total_batches,
    SUM(CASE WHEN status = 'generated' THEN 1 ELSE 0 END) as generated_count,
    SUM(CASE WHEN status = 'printed' THEN 1 ELSE 0 END) as printed_count
FROM print_label_batches
```

## Installation Steps

1. **Run SQL Script**: Execute `database_scripts/07_create_print_label_batches_table.sql`
2. **Models**: Already created and imported properly
3. **Controller**: Updated with all API endpoints
4. **Routes**: API routes added to `routes/apps2.php`
5. **Frontend**: Blade template updated with new functionality

## Usage Workflow

1. **Navigate to Print Labels**: Access via existing route `/printlabel`
2. **Select Files**: Choose up to 30 files from available indexed files
3. **Configure Settings**: Set label format and orientation
4. **Create Batch**: Click "Print Labels" to create a new batch
5. **View Generated**: Switch to "Generated Batches" tab to see created batches
6. **Manage Batches**: View details, mark as printed, or delete batches
7. **Monitor Statistics**: Track overall printing activity

## Benefits

- **Organized Batching**: Clear batch management with size limits
- **Prevent Duplicates**: Files can only be included in one batch
- **Track Progress**: Complete audit trail of label generation
- **User-Friendly**: Intuitive interface with real-time feedback
- **Scalable**: Can handle large numbers of files and batches
- **Reportable**: Built-in statistics and filtering capabilities

## Security & Performance

- **CSRF Protection**: All API calls include CSRF tokens
- **Input Validation**: Server-side validation for all inputs
- **Database Optimization**: Proper indexing and foreign keys
- **Error Handling**: Comprehensive error handling and logging
- **Pagination**: Efficient data loading with pagination
- **Background Processing**: Ready for queue-based processing if needed

This implementation provides a complete, production-ready print label management system that integrates seamlessly with the existing KLAS infrastructure.
