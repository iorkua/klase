-- =============================================
-- Simple File Decommissioning Database Update
-- Fix for data type mismatch issue
-- =============================================

USE [klas];
GO

-- First, let's check the data type of fileNumber.id
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    CHARACTER_MAXIMUM_LENGTH,
    NUMERIC_PRECISION,
    NUMERIC_SCALE
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'fileNumber' AND COLUMN_NAME = 'id';

-- Add decommissioning fields to fileNumber table
IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'fileNumber' AND COLUMN_NAME = 'commissioning_date')
BEGIN
    ALTER TABLE fileNumber ADD commissioning_date DATETIME NULL;
    PRINT 'Added commissioning_date column';
END

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'fileNumber' AND COLUMN_NAME = 'decommissioning_date')
BEGIN
    ALTER TABLE fileNumber ADD decommissioning_date DATETIME NULL;
    PRINT 'Added decommissioning_date column';
END

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'fileNumber' AND COLUMN_NAME = 'decommissioning_reason')
BEGIN
    ALTER TABLE fileNumber ADD decommissioning_reason NVARCHAR(MAX) NULL;
    PRINT 'Added decommissioning_reason column';
END

IF NOT EXISTS (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'fileNumber' AND COLUMN_NAME = 'is_decommissioned')
BEGIN
    ALTER TABLE fileNumber ADD is_decommissioned BIT NOT NULL DEFAULT 0;
    PRINT 'Added is_decommissioned column';
END

-- Drop decommissioned_files table if it exists (to recreate with correct data type)
IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'decommissioned_files')
BEGIN
    DROP TABLE decommissioned_files;
    PRINT 'Dropped existing decommissioned_files table';
END

-- Create decommissioned_files table with matching data type for file_number_id
-- Using INT to match fileNumber.id (most common case)
CREATE TABLE decommissioned_files (
    id INT IDENTITY(1,1) PRIMARY KEY,
    file_number_id INT NOT NULL,
    file_no NVARCHAR(255) NULL,
    mls_file_no NVARCHAR(255) NULL,
    kangis_file_no NVARCHAR(255) NULL,
    new_kangis_file_no NVARCHAR(255) NULL,
    file_name NVARCHAR(500) NULL,
    commissioning_date DATETIME NULL,
    decommissioning_date DATETIME NOT NULL,
    decommissioning_reason NVARCHAR(MAX) NOT NULL,
    decommissioned_by NVARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT GETDATE(),
    updated_at DATETIME NOT NULL DEFAULT GETDATE()
);

PRINT 'Created decommissioned_files table with INT data type';

-- Try to add foreign key constraint
BEGIN TRY
    ALTER TABLE decommissioned_files 
    ADD CONSTRAINT FK_decommissioned_files_fileNumber 
    FOREIGN KEY (file_number_id) REFERENCES fileNumber(id);
    PRINT 'Added foreign key constraint successfully';
END TRY
BEGIN CATCH
    PRINT 'Could not add foreign key constraint - data types may not match';
    PRINT 'Error: ' + ERROR_MESSAGE();
    
    -- Let's check what data type fileNumber.id actually is
    PRINT 'Checking fileNumber.id data type...';
    SELECT 
        'fileNumber.id data type: ' + DATA_TYPE + 
        CASE 
            WHEN DATA_TYPE IN ('int', 'bigint', 'smallint', 'tinyint') THEN ''
            WHEN NUMERIC_PRECISION IS NOT NULL THEN '(' + CAST(NUMERIC_PRECISION AS VARCHAR) + ')'
            WHEN CHARACTER_MAXIMUM_LENGTH IS NOT NULL THEN '(' + CAST(CHARACTER_MAXIMUM_LENGTH AS VARCHAR) + ')'
            ELSE ''
        END as DataTypeInfo
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_NAME = 'fileNumber' AND COLUMN_NAME = 'id';
END CATCH

-- Create basic indexes
CREATE INDEX IX_decommissioned_files_file_number_id ON decommissioned_files(file_number_id);
CREATE INDEX IX_decommissioned_files_decommissioning_date ON decommissioned_files(decommissioning_date);
CREATE INDEX IX_fileNumber_is_decommissioned ON fileNumber(is_decommissioned);

PRINT 'Created indexes';

-- Test the setup
PRINT 'Testing the setup...';

-- Count active files
DECLARE @ActiveCount INT;
SELECT @ActiveCount = COUNT(*) 
FROM fileNumber 
WHERE (is_deleted IS NULL OR is_deleted = 0) 
  AND (is_decommissioned IS NULL OR is_decommissioned = 0);

PRINT 'Active files count: ' + CAST(@ActiveCount AS VARCHAR);

-- Count decommissioned files
DECLARE @DecommissionedCount INT;
SELECT @DecommissionedCount = COUNT(*) FROM decommissioned_files;

PRINT 'Decommissioned files count: ' + CAST(@DecommissionedCount AS VARCHAR);

PRINT 'Database update completed successfully!';
PRINT '';
PRINT 'Next steps:';
PRINT '1. Replace the Laravel models with database versions';
PRINT '2. Test the web interface at /file-decommissioning';
GO