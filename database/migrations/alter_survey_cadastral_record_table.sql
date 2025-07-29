ALTER TABLE [klas].[dbo].[surveyCadastralRecord]
ADD
    -- Primary Survey ID fields for unit surveys
    [PrimarysurveyId] NVARCHAR(255) NULL,

    -- Unit Information (Note: STFileNo is similar to existing Sectional_Title_File_No)
    [section_no] NVARCHAR(255) NULL,
    [unit_number] NVARCHAR(255) NULL,
    [unit_id] NVARCHAR(255) NULL,
    [height] NVARCHAR(255) NULL,
    [app_id] NVARCHAR(255) NULL,
    [landuse] NVARCHAR(255) NULL,
    [section_attribute] NVARCHAR(255) NULL,
    [base] NVARCHAR(255) NULL,
    [floor] NVARCHAR(255) NULL,
    
    -- Unit Control Beacon Information
    [UnitControlBeaconNo] NVARCHAR(255) NULL,
    [UnitControlBeaconX] NVARCHAR(255) NULL,
    [UnitControlBeaconY] NVARCHAR(255) NULL,
    
    -- Unit Dimensions and Position
    [UnitSize] NVARCHAR(255) NULL,
    [UnitDemsion] NVARCHAR(255) NULL,
    [UnitPosition] NVARCHAR(255) NULL,
    
    -- Additional Information
    [tpreport] NVARCHAR(MAX) NULL;
