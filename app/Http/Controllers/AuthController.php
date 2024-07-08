<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB as FacadesDB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    function verusuarios(){
        $usuarios = User::all();
        return response()->json(['data'=>$usuarios]);
    }


    function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|'
        ]);
        $credentials = $request->only('email', 'password');
        

        if (Auth::attempt($credentials)) {
          
            $request->session()->regenerate();

            return response()->json([
                'data' => Auth::user(), 'code'=>200

            ]);
        }
    
        
        return response()->json(['data'=>'El email o la contraseña no son correctas.', 'code'=>422]);
        
    }

    public function addPermissionsToRole(Request $request)
    {
        $request->validate([
            'role' => 'required',
            'permission' => 'required'
        ]);
        $role = Role::find($request->role);
        $role->hasPermissionTo($request->permission);
        return response()->json(['data'=>$role]);

    }

    public function get_user(Request $request){
        $id = $request->user()->id;
        $user = User::find($id);
        $user->getPermissionsViaRoles();

        return response()->json(['data' => $user]);

    }

    public function cerrarSesion(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

    }

    public function auth(Request $request){
        return Auth::check();
      
    }
    
    public function enviarRecuperarContraseña(Request $request)
    {
        Log::info('Iniciando proceso de recuperación de contraseña');

        try {
       
            $request->validate([
                'email' => 'required|email|exists:users',
            ]);
            Log::info('Validación de email completada para: ' . $request->email);

      
            $token = Str::random(64);
            Log::info('Token generado: ' . $token);

         
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            Log::info('Tokens existentes eliminados para el email: ' . $request->email);

 
            DB::table('password_reset_tokens')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);
            Log::info('Nuevo token insertado en la base de datos');


            Mail::send('email.recuperar-contrasenia', ['token' => $token], function ($message) use ($request) {
                $message->to($request->email);
                $message->subject('Recuperar Contraseña');
            });
            Log::info('Correo de recuperación enviado a: ' . $request->email);

            return response()->json(['message' => 'Te hemos enviado un email con las instrucciones para que recuperes tu contraseña']);
        } catch (\Exception $e) {
            Log::error('Error en enviarRecuperarContraseña: ' . $e->getMessage());
            return response()->json(['message' => 'Ocurrió un error al procesar la solicitud.'], 500);
        }
    }

    public function formularioActualizacion($token)
    {
        return view('formulario-actualizacion', ['token' => $token]);
    }


    public function actualizarContrasenia(Request $request)
    {

        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required'
        ]);

        $updatePassword = DB::table('password_reset_tokens')
            ->where([
                'email' => $request->email,
                'token' => $request->token
            ])
            ->first();

    
        if (!$updatePassword) {
            return back()->withInput()->with('error', 'Token inválido');
        }


        $user = User::where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);
        DB::table('password_reset_tokens')->where(['email' => $request->email])->delete();

        return redirect('/login')->with('message', 'Tu contraseña se ha cambiado correctamente');
    }


    public function addRole(User $user, Request $request){

        $user->syncRoles($request->all());

        return response()->json(['user'=>$user]);

    }
}

