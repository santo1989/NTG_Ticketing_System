<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index()
    {
        $designations = Designation::all();
        return view('backend.library.designations.index', compact('designations'));
    }


    public function create()
    {
        return view('backend.library.designations.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2|max:191',
        ]);

        $designations = Designation::all();

        foreach ($designations as $designation) {
            if ($designation->name == $request->name) {
                return redirect()->route('designations.index')->withErrors('Designation already exists!');
            }
        }
        // Data insert
        $designation = new Designation;
        $designation->name = $request->name;

        $designation->save();

        // Redirect
        return redirect()->route('designations.index');
    }


    public function show($id)
    {
        $designation = Designation::findOrFail($id);
        return view('backend.library.designations.show', compact('designation'));
    }


    public function edit($id)
    {
        $designation = Designation::findOrFail($id);
        return view('backend.library.designations.edit', compact('designation'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|min:3|max:191',
        ]);

        // Data update
        $designation = Designation::findOrFail($id);
        $designations = Designation::all();

        if ($designation->name == $request->name) {
            return redirect()->route('designations.index');
        } else {
            foreach ($designations as $existing) {
                if ($existing->name == $request->name) {
                    return redirect()->route('designations.index')->withErrors('Designation already exists!');
                }
            }
        }
        $designation->name = $request->name;
        $designation->save();

        // Redirect
        return redirect()->route('designations.index');
    }


    public function destroy($id)
    {
        Designation::findOrFail($id)->delete();
        return redirect()->route('designations.index');
    }
}
