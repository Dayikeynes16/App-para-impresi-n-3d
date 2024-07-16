<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(){
    $role = Role::with('permissions')->get();
    
        return response()->json([
            'data' => $role
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'permission' => 'required|array',
            'permission.*' => 'integer'
        ]);
        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permission);

        return response()->json([
            'data' => $role
        ]);
    }

    public function show(Role $role)
    {
        return response()->json([
            'data' =>  $role,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Role $role, Request $request)
    {
   
        $role->update($request->all());
        $role->syncPermissions($request->permission);

        return response()->json([
            'data' => $role
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
       $role->delete();
       return response()->json(['data'=>200]);
    }

    

    

    
}
