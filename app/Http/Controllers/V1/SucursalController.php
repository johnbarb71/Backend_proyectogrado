<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sucursal;
use App\Models\User_sucursal;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class SucursalController extends Controller
{
    protected $user;
    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');
        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }

    //Listamos todos los roles
    public function index()
    {
        return Sucursal::get();
    }

    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('nombre');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el sucursal en la BD
        $product = Sucursal::create([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Sucursal creado',
            'data' => $product
        ], Response::HTTP_OK);
    }

    public function show($id)
    {
        //Bucamos el role
        $sucursal = Sucursal::find($id);
        //Si el role no existe devolvemos error no encontrado
        if (!$sucursal) {
            return response()->json([
                'message' => 'Sucursal no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $sucursal;
    }
    
    public function update(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('nombre');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string'
        ]);
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el sucursal
        $sucursal = Sucursal::findOrfail($id);
        //Actualizamos el role.
        $sucursal->update([
            'nombre' => $request->nombre,
            'direccion' => $request->direccion,
            'telefono' => $request->telefono
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Sucursal actualizado correctamente',
            'data' => $sucursal
        ], Response::HTTP_OK);
    }

    public function getUserSuc($id_user)
    {
        //Bucamos sucursal-usuario
        $sucursal_user = User_sucursal::where('id_user',$id_user)->get();
        //Si el sucursal-usuario no existe devolvemos error no encontrado
        if (!$sucursal_user) {
            return response()->json([
                'message' => 'Sucursal-usuario no encontrado.'
            ], 404);
        }
        //Si hay sucursal-usuario lo devolvemos
        return $sucursal_user;
    }

    public function existeSucUSu(Request $request)
    {
        //Validamos los datos
        $data = $request->only('id_user','id_sucursal');
        $validator = Validator::make($data, [
            'id_user' => 'required|numeric',
            'id_sucursal' => 'required|numeric'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el sucursal en la BD
        $product = User_sucursal::where('id_user',$request->id_user)->where('id_sucursal',$request->id_sucursal)->get();
        if (empty($product{0})){
            return response()->json([
            'message' => 'No Existe'
        ], Response::HTTP_OK);
        }
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'existe'
        ], Response::HTTP_OK);
    }

    public function guardarSucUSu(Request $request)
    {
        //Validamos los datos
        $data = $request->only('id_user','id_sucursal');
        $validator = Validator::make($data, [
            'id_user' => 'required|numeric',
            'id_sucursal' => 'required|numeric'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el sucursal en la BD
        $product = User_sucursal::create([
            'id_user' => $request->id_user,
            'id_sucursal' => $request->id_sucursal
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Sucursal-Usuario creado',
            'data' => $product
        ], Response::HTTP_OK);
    }


    public function borrarUserSuc(Request $request)
    {
        //Validamos los datos
        $data = $request->only('id_user','id_sucursal');
        $validator = Validator::make($data, [
            'id_user' => 'required|numeric',
            'id_sucursal' => 'required|numeric'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        $id_user = $request->id_user;
        $id_sucursal = $request->id_sucursal;
        //Bucamos sucursal-usuario
        $sucursal_user = User_sucursal::where('id_user',$id_user)
            ->where('id_sucursal',$id_sucursal)
            ->delete();
        //Si el sucursal-usuario no existe devolvemos error no encontrado
        if (!$sucursal_user) {
            return response()->json([
                'message' => 'Sucursal-usuario no encontrado.'
            ], 404);
        }
        //Si hay sucursal-usuario lo devolvemos
        return response()->json([
                'message' => 'Sucursal-usuario borrado.'
            ], 200);
    }


}
