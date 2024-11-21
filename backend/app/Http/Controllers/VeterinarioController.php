<?php

namespace App\Http\Controllers;

use App\Models\Veterinario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class VeterinarioController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'second_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'second_last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:veterinarios',
            'password' => 'required|string|min:8',
            'telefono' => 'required|string|max:255',
            'especialidad' => 'required|string|max:255',
            'tipo_identidad' => 'required|string',
            'numero_identidad' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }



        $veterinario = Veterinario::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'last_name' => $request->last_name,
            'second_last_name' => $request->second_last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telefono' => $request->telefono,
            'especialidad' => $request->especialidad,
            'tipo_identidad' => $request->tipo_identidad,
            'numero_identidad' => $request->numero_identidad,
        ]);

        $token = $veterinario->createToken('Veterinario Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Veterinario creado correctamente',
            'data' => $veterinario,
            'token' => $token
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $veterinario = Veterinario::where('email', $request->email)->first();

        if (!$veterinario || !Hash::check($request->password, $veterinario->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $token = $veterinario->createToken('Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Veterinario autenticado correctamente',
            'data' => $veterinario,
            'token' => $token
        ]);
    }
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json([
            'success' => true,
            'message' => 'Sesión cerrada correctamente'
        ]);
    }
}
