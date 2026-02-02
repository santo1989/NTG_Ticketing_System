<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserImportController extends Controller
{
    public function showImportForm()
    {
        return view('backend.users.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $fileExtension = $file->getClientOriginalExtension();

            // Parse file based on extension
            if (in_array($fileExtension, ['xlsx', 'xls'])) {
                $rows = $this->parseExcelFile($file);
            } else {
                $rows = $this->parseCSVFile($file);
            }

            if (empty($rows)) {
                DB::rollBack();
                return redirect()->route('users.import.form')
                    ->with('error', 'No data found in file. Please check the file format.');
            }

            // Extract headers
            $headers = array_shift($rows);

            // Find column indexes (case-insensitive)
            $emailIndex = array_search('email', array_map('strtolower', $headers));
            $nameIndex = array_search('name', array_map('strtolower', $headers));
            $companyIndex = array_search('company', array_map('strtolower', $headers));
            $departmentIndex = array_search('department', array_map('strtolower', $headers));
            $designationIndex = array_search('designation', array_map('strtolower', $headers));

            if ($emailIndex === false || $nameIndex === false) {
                DB::rollBack();
                return redirect()->route('users.import.form')
                    ->with('error', 'Required columns not found! Make sure you have "email" and "name" columns.');
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            foreach ($rows as $index => $row) {
                $rowNumber = $index + 2; // +2 because we removed header and file rows start from 1

                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }

                $email = trim($row[$emailIndex] ?? '');
                $name = trim($row[$nameIndex] ?? '');
                $companyName = trim($row[$companyIndex] ?? '');
                $departmentName = trim($row[$departmentIndex] ?? '');
                $designationName = trim($row[$designationIndex] ?? '');

                // Validate required fields
                if (empty($email) || empty($name)) {
                    $errors[] = "Row {$rowNumber}: Email and Name are required";
                    $skipped++;
                    continue;
                }

                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Row {$rowNumber}: Invalid email format: {$email}";
                    $skipped++;
                    continue;
                }

                // Check if user already exists
                if (User::where('email', $email)->exists()) {
                    $errors[] = "Row {$rowNumber}: User with email {$email} already exists";
                    $skipped++;
                    continue;
                }

                // Generate emp_id from email
                $empId = explode('@', $email)[0];

                // Check if emp_id already exists
                if (User::where('emp_id', $empId)->exists()) {
                    $errors[] = "Row {$rowNumber}: Employee ID {$empId} already exists";
                    $skipped++;
                    continue;
                }

                // Find or Create Company
                $companyId = null;
                if (!empty($companyName)) {
                    $company = Company::firstOrCreate(
                        ['name' => $companyName]
                    );
                    $companyId = $company->id;
                }

                // Find or Create Department
                $departmentId = null;
                if (!empty($departmentName) && $companyId) {
                    $department = Department::firstOrCreate(
                        [
                            'name' => $departmentName,
                            'company_id' => $companyId
                        ]
                    );
                    $departmentId = $department->id;
                }

                // Find or Create Designation
                $designationId = null;
                if (!empty($designationName) && $companyId) {
                    $designation = Designation::firstOrCreate(
                        [
                            'name' => $designationName
                        ]
                    );
                    $designationId = $designation->id;
                }

                // Create User
                try {
                    User::create([
                        'name' => $name,
                        'emp_id' => $empId,
                        'email' => $email,
                        'password' => Hash::make('123'),
                        'password_text' => '123',
                        'company_id' => $companyId,
                        'department_id' => $departmentId,
                        'designation_id' => $designationId,
                        'role_id' => 5, // General role
                        'is_active' => true,
                        'is_admin' => false,
                        'is_supervisor' => false,
                        'is_super_admin' => false,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$rowNumber}: Failed to create user - " . $e->getMessage();
                    $skipped++;
                }
            }

            DB::commit();

            $message = "Import completed! {$imported} users imported successfully.";
            if ($skipped > 0) {
                $message .= " {$skipped} rows skipped.";
            }

            return redirect()->route('users.import.form')
                ->with('success', $message)
                ->with('errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('users.import.form')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    private function parseCSVFile($file)
    {
        $rows = [];
        $handle = fopen($file->getPathname(), 'r');

        if ($handle) {
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        return $rows;
    }

    private function parseExcelFile($file)
    {
        $rows = [];

        try {
            // Try to use PhpSpreadsheet if available
            if (class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getPathname());
                $worksheet = $spreadsheet->getActiveSheet();
                $rows = $worksheet->toArray();
            } else {
                // Fallback: treat as CSV
                $rows = $this->parseCSVFile($file);
            }
        } catch (\Exception $e) {
            // If PhpSpreadsheet fails, try CSV fallback
            $rows = $this->parseCSVFile($file);
        }

        return $rows;
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="user_import_template.csv"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            // BOM for UTF-8 (helps with Excel on Windows)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            fputcsv($file, ['email', 'name', 'Group', 'Company', 'Department', 'Designation']);

            // Sample data
            fputcsv($file, ['a.admin@ntg.com.bd', 'Md. Al Amin', 'NCL', 'NCL Corporate', 'Merchandising', 'Executive']);
            fputcsv($file, ['abir.bashir@ntg.com.bd', 'Md Bashir Sarker (Abir)', 'TIL', 'TIL Corporate', 'Washing', 'Junior Executive']);
            fputcsv($file, ['a.rahim@ntg.com.bd', 'Md. Abdur Rahim', 'NCL', 'NCL Corporate', 'Supply Chain', 'Assistant Manager']);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
