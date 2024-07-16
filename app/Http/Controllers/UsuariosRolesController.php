<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsuariosRolesController extends Controller
{
    public function update(User $user,Request $request){
        $user->update($request->only(['name', 'email']));
        $user->syncRoles($request->input('rol'));
        
        
        return response()->json([
            'data' => $user
        ]);

    }

    public function delete(User $user){
        $user->delete();

        return response()->json(['data' => $user]);
    }
    
}
