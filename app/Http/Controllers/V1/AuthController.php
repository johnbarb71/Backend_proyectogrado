<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //Función que utilizaremos para registrar al usuario
    public function register(Request $request)
    {
        //Indicamos que solo queremos recibir name, email y password de la request
        $data = $request->only('name', 'email', 'password');
        //Realizamos las validaciones
        $validator = Validator::make($data, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|max:50',
        ]);
        //Devolvemos un error si fallan las validaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el nuevo usuario
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
        //Nos guardamos el usuario y la contraseña para realizar la petición de token a JWTAuth
        $credentials = $request->only('email', 'password');
        //Devolvemos la respuesta con el token del usuario
        return response()->json([
            'message' => 'User created',
            'token' => JWTAuth::attempt($credentials),
            'user' => $user
        ], Response::HTTP_OK);
    }
    //Funcion que utilizaremos para hacer login
    public function authenticate(Request $request)
    {
        //Indicamos que solo queremos recibir email y password de la request
        $credentials = $request->only('email', 'password');
        //Validaciones
        $validator = Validator::make($credentials, [
            'email' => 'required|email',
            'password' => 'required|string|min:6|max:50'
        ]);
        //Devolvemos un error de validación en caso de fallo en las verificaciones
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Intentamos hacer login
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                //Credenciales incorrectas.
                return response()->json([
                    'message' => 'Login failed',
                ], 401);
            }
        } catch (JWTException $e) {
            //Error de usuario o contraseña
            return response()->json([
                'message' => 'Error',
            ], 500);
        }
        //Verificamos el estado del usuario
        if(Auth::user()->estado == 0){
            return response()->json([
                'message' => 'Usuario no activo',
            ], 500);
        }
        //retorna el usuario valido y activo
        return response()->json([
            'token' => $token,
            'user' => Auth::user()
        ]);
    }
    //Función que utilizaremos para eliminar el token y desconectar al usuario
    public function logout(Request $request)
    {
        //Validamos que se nos envie el token
        $validator = Validator::make($request->only('token'), [
            'token' => 'required'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        try {
            //Si el token es valido eliminamos el token desconectando al usuario.
            JWTAuth::invalidate($request->token);
            return response()->json([
                'success' => true,
                'message' => 'User disconnected'
            ]);
        } catch (JWTException $exception) {
            //Error interno de BBDD o de conexión
            return response()->json([
                'success' => false,
                'message' => 'Error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    //Función que utilizaremos para obtener los datos del usuario y validar si el token a expirado.
    public function getUser(Request $request)
    {
        //Validamos que la request tenga el token
        $this->validate($request, [
            'token' => 'required'
        ]);
        //Realizamos la autentificación
        $user = JWTAuth::authenticate($request->token);
        //Si no hay usuario es que el token no es valido o que ha expirado
        if(!$user)
            return response()->json([
                'message' => 'Invalid token / token expired',
            ], 401);
        //Devolvemos los datos del usuario 
        return response()->json(['user' => $user]);
    }
    //Funcion para cambiar estado de un usuario
    public function updEstUser(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('nombre', 'email', 'estado');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string',
            'email' => 'required|max:250|string',
            'estado' => 'required|numeric'
        ]);
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el usuario
        $user = User::findOrfail($id);
        //Actualizamos el usuario.
        $user->update([
            'name' => $request->nombre,
            'email' => $request->email,
            'estado' => $request->estado,
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $user
        ], Response::HTTP_OK);
    }
    //Funcion para cambiar password de un usuario
    public function updPassUser(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('password');
        $validator = Validator::make($data, [
            'password' => 'required|min:6|max:50|string'
        ]);
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el usuario
        $user = User::findOrfail($id);
        //Actualizamos el usuario.
        $user->update([
            'password' => bcrypt($request->password)
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Usuario actualizado correctamente',
            'data' => $user
        ], Response::HTTP_OK);
    }
    //Eliminar usuario
    public function destroy($id)
    {
        //Buscamos el usuario
        $user = User::findOrfail($id);
        //Comprobamos el usuario
        if($user->role == 2){
            //Devolvemos la respuesta
            return response()->json([
                'message' => 'No se puede eliminar este usuario, por favor consulte al administrador del sistema.'
            ], Response::HTTP_OK);
        }
        //Elimina usuario
        $user->delete();
        return response()->json([
            'message' => 'Usuario eliminado.'
        ], Response::HTTP_OK);
    }
}
