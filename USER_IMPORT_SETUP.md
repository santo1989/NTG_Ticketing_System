# User Excel Import System - Setup Guide

## Installation

Since PhpSpreadsheet library is already included in Laravel, no additional installation is needed.

## How to Use

### 1. Access Import Page
Navigate to: `http://127.0.0.1:8000/users/import`

Or click the **"Import Users from Excel"** button on the Users List page.

### 2. Download Template
Click the **"Download Template"** button to get a sample Excel file with the correct format.

### 3. Prepare Your Excel File

Your Excel file must have these column headers (case-insensitive):
- **email** (required) - User's email address
- **name** (required) - User's full name
- **Group** (optional) - For reference only, not imported
- **Company** (optional) - Company name
- **Department** (optional) - Department name
- **Designation** (optional) - Designation/Job title

#### Example Excel Data:
```
email                   | name                      | Group | Company        | Department    | Designation
a.admin@ntg.com.bd     | Md. Al Amin               | NCL   | NCL Corporate  | Merchandising | Executive
abir.bashir@ntg.com.bd | Md Bashir Sarker (Abir)   | TIL   | TIL Corporate  | Washing       | Junior Executive
```

### 4. Upload and Import
1. Select your Excel file (.xlsx, .xls, or .csv)
2. Check the confirmation checkbox
3. Click **"Import Users"**

## Import Logic

### User Creation:
- **email** → Direct mapping to email field
- **name** → Direct mapping to name field
- **password** → Automatically set to "123" for all users
- **emp_id** → Generated from email (part before @)
- **role_id** → Always set to 5 (General role)
- **is_active** → Set to true

### Company Handling:
- If Company name exists → Use existing company_id
- If Company name doesn't exist → Create new company, then use its id
- If Company column is empty → company_id = null

### Department Handling:
- Requires Company to be set
- If Department name exists for that company → Use existing department_id
- If Department name doesn't exist → Create new department under that company, then use its id
- If Department column is empty → department_id = null

### Designation Handling:
- Requires Company to be set
- If Designation name exists for that company → Use existing designation_id
- If Designation name doesn't exist → Create new designation under that company, then use its id
- If Designation column is empty → designation_id = null

## Features

✅ Automatic Company/Department/Designation creation
✅ Duplicate email detection (skips existing users)
✅ Batch import with transaction rollback on errors
✅ Detailed error reporting per row
✅ Template download with sample data
✅ Support for Excel and CSV files
✅ File size limit: 10MB

## Default Values

- **Password**: 123 (password_text also stored)
- **Role**: General (role_id = 5)
- **is_active**: true
- **is_admin**: false
- **is_supervisor**: false
- **is_super_admin**: false

## Error Handling

The import will:
- Skip rows with existing emails (show warning)
- Skip rows missing required fields (email, name)
- Show detailed error messages with row numbers
- Complete partial import if some rows fail
- Rollback entire transaction if critical error occurs

## Success Metrics

After import, you'll see:
- Total users imported successfully
- Total rows skipped
- List of any errors with row numbers

## Testing

Test with the sample data in the template to verify the system works correctly before importing your full user list.
