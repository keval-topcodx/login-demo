<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::orderBy('id')->paginate(10);
        return view('permission.index' , ['permissions' => $permissions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('permission.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'name' => Str::snake($request->input('name'))
        ]);

        $validated = $request->validate([
            'name' => ['required', 'unique:permissions,name', 'max:100'],
        ]);

        Permission::create($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(Permission $permission)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Permission $permission)
    {
        return view('permission.edit', ['permission' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permission $permission)
    {
        $request->merge([
            'name' => Str::snake($request->input('name'))
        ]);

        $validated = $request->validate([
            'name' => 'required|unique:permissions,name|max:100',
        ]);

        $permission->update($validated);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Permission $permission)
    {
        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully!');
    }
}
