- KLAES Land Admin System Database Inserts
- Generated from KLAES Land Admin System User Roles_V02.docx

--====================================================
- CLEAR EXISTING DATA (Delete in correct order for foreign keys)
--====================================================
DELETE FROM user_roles;
DELETE FROM user_types;
DELETE FROM departments;

- Reset identity columns if they exist
DBCC CHECKIDENT ('user_roles', RESEED, 0);
DBCC CHECKIDENT ('user_types', RESEED, 0);
DBCC CHECKIDENT ('departments', RESEED, 0);

--====================================================
-- - INSERT INTO user_types
--====================================================
INSERT INTO user_types ([name], [code], [description], [level_priority], [is_active], [created_at], [updated_at])
VALUES 
    ('User', 'USER', 'Basic user with lowest access level', 1, 1, GETDATE(), GETDATE()),
    ('Operations', 'OPS', 'Operational staff with high access level', 2, 1, GETDATE(), GETDATE()),
    ('Management', 'MGT', 'Management level with highest access', 3, 1, GETDATE(), GETDATE()),
    ('System', 'SYS', 'System administrator with highest privileges', 4, 1, GETDATE(), GETDATE());

--====================================================
-- INSERT INTO departments
--====================================================
INSERT INTO departments ([name], [code], [description], [parent_id], [is_active], [created_at], [updated_at])
VALUES 
    ('Customer Service Unit', 'CSU', 'Customer relationship management department', NULL, 1, GETDATE(), GETDATE()),
    ('Lands', 'LANDS', 'Land administration and management department', NULL, 1, GETDATE(), GETDATE()),
    ('Survey', 'SURVEY', 'Land surveying and mapping department', NULL, 1, GETDATE(), GETDATE()),
    ('Geographic Information Systems', 'GIS', 'GIS and spatial data management department', NULL, 1, GETDATE(), GETDATE()),
    ('Account/Finance', 'ACC', 'Accounting and financial management department', NULL, 1, GETDATE(), GETDATE()),
    ('Deeds', 'DEEDS', 'Property deeds and registration department', NULL, 1, GETDATE(), GETDATE()),
    ('Physical Planning', 'PP', 'Physical and urban planning department', NULL, 1, GETDATE(), GETDATE()),
    ('Cadastral', 'CAD', 'Cadastral mapping and records department', NULL, 1, GETDATE(), GETDATE()),
    ('Sectional Titling', 'ST', 'Sectional property titling department', NULL, 1, GETDATE(), GETDATE()),
    ('SLTR', 'SLTR', 'Systematic Land Titling and Registration department', NULL, 1, GETDATE(), GETDATE()),
    ('Information and Communication Technology', 'ICT', 'IT systems and administration department', NULL, 1, GETDATE(), GETDATE());

--====================================================
-- INSERT INTO user_roles
--====================================================
INSERT INTO user_roles ([name], [guard_name], [description], [department_id], [level], [user_type], [is_active], [created_at], [updated_at])
VALUES 
    -- Dashboard (Universal Access)
    ('Dashboard', 'web', 'Universal dashboard access for all users', NULL, 'Lowest', 'ALL', 1, GETDATE(), GETDATE()),
    
    -- Customer Relationship Management
    ('Person', 'web', 'Manage individual person records', NULL, 'Lowest', 'ALL', 1, GETDATE(), GETDATE()),
    ('Corporate', 'web', 'Manage corporate entity records', NULL, 'Lowest', 'ALL', 1, GETDATE(), GETDATE()),
    ('Customer Manager', 'web', 'Customer relationship management', 1, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Programs
    ('Allocation', 'web', 'Land allocation management', 2, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('Compensation/Resettlement', 'web', 'Handle compensation and resettlement processes', 3, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Recertification', 'web', 'Property recertification processes', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Conversion/Regularization', 'web', 'Land conversion and regularization', 2, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Land Property Enumeration', 'web', 'Enumerate and catalog land properties', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Data Repository', 'web', 'Manage central data repository', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Migrate Data', 'web', 'Handle data migration processes', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Information Products
    ('Letter of Administration/Grant/Offer Letter', 'web', 'Generate administrative letters and offers', 2, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('Occupancy Permit (OP)', 'web', 'Issue occupancy permits', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Site Plan/Parcel Plan', 'web', 'Create and manage site/parcel plans', 3, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Right of Occupancy', 'web', 'Manage right of occupancy documents', 2, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('Certificate of Occupancy', 'web', 'Issue certificates of occupancy', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Revenue Management
    ('Billing', 'web', 'Handle billing processes', 5, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Generate Receipt', 'web', 'Generate payment receipts', 5, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Land Use Charge (LUC)', 'web', 'Manage land use charges', 2, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Bill Balance', 'web', 'Manage billing balances', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Deeds
    ('Deeds - Property Records Assistant (Legacy Records)', 'web', 'Access legacy property records', 6, 'Lowest', 'User', 1, GETDATE(), GETDATE()),
    ('Deeds - Instrument Capture (New Records)', 'web', 'Capture new deed instruments', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Deeds - Instrument Registration (New Registration)', 'web', 'Register new deed instruments', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Deeds - Instrument Registration Reports', 'web', 'Generate deed registration reports', 6, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    
    -- Search
    ('Deeds - Official (for filing purpose)', 'web', 'Official deed searches for filing', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Deeds - On-Premise (Pay-Per-Search)', 'web', 'On-premise deed search services', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Deeds - Legal Search Reports', 'web', 'Generate legal search reports', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Lands
    ('Lands - File Tracker/Tracking - RFID', 'web', 'Track land files using RFID system', 2, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Lands - File Digital Archive - Doc-WARE', 'web', 'Access digital archive system', NULL, 'ALL', 'ALL', 1, GETDATE(), GETDATE()),
    ('EDMS', 'web', 'Electronic Document Management System', 2, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Physical Planning
    ('PP - Regular Applications', 'web', 'Process regular planning applications', 7, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('PP - ST Applications', 'web', 'Process sectional title planning applications', 7, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('PP - SLTR Applications', 'web', 'Process SLTR planning applications', 7, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('PP Reports', 'web', 'Generate physical planning reports', 7, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    
    -- Survey
    ('Survey - Records', 'web', 'Manage survey records', 3, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Survey - AI Digital Assistant', 'web', 'AI-powered survey assistance', 3, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Survey - GIS', 'web', 'Survey GIS operations', 3, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Survey - Approvals', 'web', 'Survey approval processes', 3, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('Survey - E-Registry', 'web', 'Electronic survey registry', 3, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Survey Reports', 'web', 'Generate survey reports', 3, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Cadastral
    ('Cad - Records', 'web', 'Manage cadastral records', 8, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Cad - AI Digital Assistant', 'web', 'AI-powered cadastral assistance', 8, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Cad - GIS', 'web', 'Cadastral GIS operations', 8, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Cad - Approvals', 'web', 'Cadastral approval processes', 8, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('Cad - E-Registry', 'web', 'Electronic cadastral registry', 8, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Cadastral Reports', 'web', 'Generate cadastral reports', 8, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- GIS
    ('GIS - Records', 'web', 'Manage GIS records', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('GIS - AI Digital Assistant', 'web', 'AI-powered GIS assistance', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('GIS - GIS', 'web', 'Core GIS operations', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('GIS - Approvals', 'web', 'GIS approval processes', 4, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('GIS - e-Registry', 'web', 'Electronic GIS registry', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('GIS Reports', 'web', 'Generate GIS reports', 4, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Sectional Titling
    ('ST - Overview', 'web', 'Sectional titling overview access', 9, 'Lowest', 'ALL', 1, GETDATE(), GETDATE()),
    ('ST - Applications', 'web', 'Process sectional titling applications', 9, 'Lowest', 'User', 1, GETDATE(), GETDATE()),
    ('ST - Field Data Integration', 'web', 'Integrate field data for sectional titling', 9, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('ST - Bills & Payments', 'web', 'Handle sectional titling billing and payments', 9, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('ST - Approvals (Other Departments)', 'web', 'Inter-departmental approvals for sectional titling', 9, 'Lowest', 'ALL', 1, GETDATE(), GETDATE()),
    ('ST - Director''s Approval', 'web', 'Director-level approvals for sectional titling', 9, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('ST - Certificate', 'web', 'Issue sectional titling certificates', 9, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('ST - e-Registry', 'web', 'Electronic sectional titling registry', 9, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('ST - GIS', 'web', 'Sectional titling GIS operations', 9, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('ST - Survey', 'web', 'Sectional titling survey operations', 9, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('ST - Sectional Titling BaseMap', 'web', 'Access sectional titling base maps', 9, 'Lowest', 'ALL', 1, GETDATE(), GETDATE()),
    ('ST - Reports', 'web', 'Generate sectional titling reports', 9, 'Lowest', 'ALL', 1, GETDATE(), GETDATE()),
    
    -- SLTR/First Registration
    ('SLTR - Overview', 'web', 'SLTR process overview', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Application', 'web', 'Process SLTR applications', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Claimants', 'web', 'Manage SLTR claimants', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Legacy Data', 'web', 'Handle SLTR legacy data', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Field Data', 'web', 'Manage SLTR field data', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Payments', 'web', 'Handle SLTR payments', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Approvals', 'web', 'SLTR approval processes', 10, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('SLTR - Planning Recommendation', 'web', 'Provide planning recommendations for SLTR', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Director SLTR', 'web', 'Director-level SLTR operations', 10, 'Highest', 'Management', 1, GETDATE(), GETDATE()),
    ('SLTR - Other Departments', 'web', 'Inter-departmental SLTR coordination', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Memo', 'web', 'Create and manage SLTR memos', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Certificate', 'web', 'Issue SLTR certificates', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - e-Registry', 'web', 'Electronic SLTR registry', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - GIS', 'web', 'SLTR GIS operations', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Map', 'web', 'SLTR mapping operations', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Survey', 'web', 'SLTR survey operations', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('SLTR - Reports', 'web', 'Generate SLTR reports', 10, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Systems
    ('Caveat', 'web', 'Manage property caveats', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    ('Encumbrance', 'web', 'Manage property encumbrances', 6, 'High', 'Operations', 1, GETDATE(), GETDATE()),
    
    -- Legacy Systems
    ('Legacy System', 'web', 'Access and manage legacy systems', 11, 'Highest', 'System', 1, GETDATE(), GETDATE()),
    
    -- System Admin
    ('User Account', 'web', 'Manage user accounts', 11, 'High', 'System', 1, GETDATE(), GETDATE()),
    ('Departments', 'web', 'Manage departments', 11, 'High', 'System', 1, GETDATE(), GETDATE()),
    ('User Roles', 'web', 'Manage user roles and permissions', 11, 'High', 'System', 1, GETDATE(), GETDATE()),
    ('System Settings', 'web', 'Configure system settings', 11, 'High', 'System', 1, GETDATE(), GETDATE());
