<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $usersCollection = User::latest()->get();

        if (request()->has('role_id')) {
            $usersCollection = $usersCollection
                ->where('role_id', request('role_id'));
        }

        if (request('search')) {
            $usersCollection = $usersCollection
                ->where('name', 'like', '%' . request('search') . '%');
        }

        $users = $usersCollection;
        // dd($users);
        $roles = Role::all();

        return view('backend.users.index', [
            'users' => $users,
            'roles' => $roles
        ]);
    }

    public function edit(User $user)
    {
        $roles = Role::latest()->get();
        $companies = Company::all();
        $departments  = Department::all();
        $designations  = Designation::all();
        return view('backend.users.edit', [
            'user' => $user,
            'roles' => $roles,
            'companies' => $companies,
            'departments' => $departments,
            'designations' => $designations
        ]);
    }

    public function update(Request $request, User $user)
    {
        try {

            // dd($request->all());

            $requestData = [
                'name' => $request->name,
                'password' => $request->password ? Hash::make($request->password) : $user->password,
                'password_text' => $request->password, // for testing purpose only, will be removed in future
                'mobile' => $request->mobile,


            ];

            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                $file->move('images/users', $filename);
                $requestData['picture'] = $filename;
            }

            if ($request->role_id) {
                $requestData['role_id'] = $request->role_id;
            } else {
                $requestData['role_id'] = $user->role_id;
            }

            if ($request->company_id) {
                $requestData['company_id'] = $request->company_id;
            } else {
                $requestData['company_id'] = $user->company_id;
            }

            if ($request->department_id) {
                $requestData['department_id'] = $request->department_id;
            } else {
                $requestData['department_id'] = $user->department_id;
            }

            if ($request->designation_id) {
                $requestData['designation_id'] = $request->designation_id;
            } else {
                $requestData['designation_id'] = $user->designation_id;
            }

            $user->update($requestData);


            if (auth()->user()->role_id == 1) {
                return redirect()->route('users.index')->withMessage('Successfully Updated!');
            } else {
                return redirect()->route('users.show', $user->id)->withMessage('Successfully Updated!');
            }
        } catch (QueryException $e) {
            return redirect()->back()->withInput()->withErrors($e->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            if (auth()->user()->role_id == 1) {
                $user->delete();
                return redirect()->route('users.index')->withMessage('Successfully Deleted!');
            } else {
                return redirect()->route('users.index')->withErrors('You are not authorized to  delete this user!');
            }
        } catch (QueryException $e) {
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function show(User $user)
    {
        $user = User::find($user->id);
        $companies = Company::all();
        $departments  = Department::all();
        $designations  = Designation::all();
        return view('backend.users.profiles', ['user' => $user, 'companies' => $companies, 'departments' => $departments, 'designations' => $designations]);
    }

    public function onlineuserlist(Request $request)
    {
        $users = User::select("*")
            ->whereNotNull('last_seen')
            ->orderBy('last_seen', 'DESC')
            ->paginate(10);

        return view('backend.users.online', ['users' => $users]);
    }

    public function  user_active($id)
    {
        $user = User::findOrFail($id);
        if ($user->is_active == 0) {
            $user->is_active = 1;
        } else {
            $user->is_active = 0;
        }
        $user->save();
        return redirect()->route('users.index');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->role_id = $request->role_id;
        $user->save();

        return redirect()->back()->with('success', 'User role updated successfully.');
    }

    public function editAssignments(User $user)
    {
        // Ensure only admins can access
        if (Auth::user()->role_id != 1) {
            abort(403, 'Unauthorized access');
        }

        // Only allow assignments for Support role users
        if ($user->role_id != 4) {
            return redirect()->route('users.index')
                ->with('error', 'Assignments can only be managed for Support role users.');
        }

        $companies = Company::orderBy('name')->get();
        $assignedCompanyIds = $user->assignedCompanies()->pluck('companies.id')->toArray();
        $assignedSupportTypes = $user->assignedSupportTypes()->pluck('support_type')->toArray();

        $supportTypes = [
            'ERP Support',
            'IT Support',
            'Programmer Support'
        ];

        return view('backend.users.assignments', compact(
            'user',
            'companies',
            'assignedCompanyIds',
            'assignedSupportTypes',
            'supportTypes'
        ));
    }

    public function updateAssignments(Request $request, User $user)
    {
        // Ensure only admins can access
        if (Auth::user()->role_id != 1) {
            abort(403, 'Unauthorized access');
        }

        // Only allow assignments for Support role users
        if ($user->role_id != 4) {
            return redirect()->route('users.index')
                ->with('error', 'Assignments can only be managed for Support role users.');
        }

        try {
            // Sync company assignments
            $companyIds = $request->input('company_ids', []);
            $user->assignedCompanies()->sync($companyIds);

            // Sync support type assignments
            $supportTypes = $request->input('support_types', []);
            $user->assignedSupportTypes()->delete();

            foreach ($supportTypes as $type) {
                $user->assignedSupportTypes()->create([
                    'support_type' => $type
                ]);
            }

            return redirect()->route('users.index')
                ->with('success', 'User assignments updated successfully!');
        } catch (QueryException $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update assignments: ' . $e->getMessage());
        }
    }
}
