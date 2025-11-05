<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::orderBy('id')->paginate(10);
        return view('role.index' , ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('role.create', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'unique:roles,name', 'max:100'],
            'permissions' => ['nullable', 'array'],
        ]);
        $role = Role::firstOrCreate(['name' => $validated['name'], 'guard_name' => 'web']);


        $permissions = $validated['permissions'] ?? [];

        $role->syncPermissions($permissions);



        return Redirect::route('roles.index')->with('success', 'Role created successfully');



    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all();
        return view('role.edit', ['role' => $role, 'permissions' => $permissions]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {

        $validated = $request->validate([
            'name' => [
                'required',
                'max:100',
                Rule::unique('roles', 'name')->ignore($role),
            ],
            'permissions' => ['nullable', 'array'],
        ]);

        $permissions = $validated['permissions'] ?? [];
        $role->syncPermissions($permissions);

        return Redirect::route('roles.index')->with('success', 'Role updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();

        return Redirect::route('roles.index')->with('success', 'Role deleted successfully!');
    }
}
