<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if ($user && Hash::check($request->password, $user->password)) {
    //         // Generar token si es superusuario
    //         if ($user->role === 'superuser') {
    //             $token = $user->createToken('SuperUser Token')->plainTextToken;
    //             return response()->json(['token' => $token]);
    //         }

    //         return response()->json(['message' => 'No tienes permiso para acceder.'], 403);
    //     }

    //     return response()->json(['message' => 'Credenciales inválidas.'], 401);
    // }
}
