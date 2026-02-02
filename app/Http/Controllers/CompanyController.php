<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function index()
    {
        $companies = Company::all();
        return view('backend.library.companies.index', compact('companies'));
    }


    public function create()
    {
        $companies = Company::all();
        return view('backend.library.companies.create', compact('companies'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:191',
        ]);

        $companies = Company::all();

        foreach ($companies as $company) {
            if ($company->name == $request->name) {
                return redirect()->route('companies.index')->withErrors('Company already exists!');
            }
        }
        // Data insert
        $company = new Company;
        $company->name = $request->name;
        $company->save();

        // Redirect
        return redirect()->route('companies.index');
    }


    public function show($id)
    {
        $company = Company::findOrFail($id);
        return view('backend.library.companies.show', compact('company'));
    }


    public function edit($id)
    {
        $company = Company::findOrFail($id);
        return view('backend.library.companies.edit', compact('company'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|max:191',
        ]);

        // Data update
        $company = Company::findOrFail($id);
        $companies = Company::all();

        if ($company->name == $request->name) {
            return redirect()->route('companies.index')->withErrors('No changes made!');
        } else {
            foreach ($companies as $company) {
                if ($company->name == $request->name) {
                    return redirect()->route('companies.index')->withErrors('Company already exists!');
                }
            }
        }
        $company->name = $request->name;
        $company->save();

        // Redirect
        return redirect()->route('companies.index');
    }


    public function destroy($id)
    {
        Company::findOrFail($id)->delete();
        return redirect()->route('companies.index');
    }

    public function getCompanyDesignations($id)
    {
        $company = Company::where('id', $id)->get();
        $designations = Designation::where('company_id', $id)->get();
        return response()->json([
            'company' => $company,
            'designations' => $designations
        ]);
    }

    public function getdepartments($id)
    {
        $company = Company::findOrFail($id);
        $departments = Department::where('company_id', $id)->get();
        return response()->json([
            'departments' => $departments
        ]);
    }
}
