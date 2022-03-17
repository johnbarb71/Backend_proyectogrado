<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use JWTAuth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{
    protected $user;
    public function __construct(Request $request)
    {
        $token = $request->header('Authorization');
        if($token != '')
            //En caso de que requiera autentifiación la ruta obtenemos el usuario y lo almacenamos en una variable, nosotros no lo utilizaremos.
            $this->user = JWTAuth::parseToken()->authenticate();
    }

    public function index()
    {
        //Listamos todos los productos
        return Product::get();
    }

    public function store(Request $request)
    {
        //Validamos los datos
        $data = $request->only('codigo1','nombre','cantidad','estado','linea');
        $validator = Validator::make($data, [
            'codigo1' => 'required|numeric',
            'nombre' => 'required|max:250|string',
            'cantidad' => 'required|numeric',
            'estado' => 'required|numeric',
            'linea' => 'required|numeric'
        ]);
        //Si falla la validación
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Creamos el producto en la BD
        $product = Product::create([
            /* 'codigo' => $request->codigo, */
            'codigo1' => $request->codigo1,
            'nombre' => $request->nombre,
            'cantidad' => $request->cantidad,
            'estado' => $request->estado,
            'linea' => $request->linea
        ]);
        //Respuesta en caso de que todo vaya bien.
        return response()->json([
            'message' => 'Producto creado',
            'data' => $product
        ], Response::HTTP_OK);
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Bucamos el producto
        $product = Product::find($id);
        //Si el producto no existe devolvemos error no encontrado
        if (!$product) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $product;
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Validación de datos
        $data = $request->only('nombre', 'cantidad', 'estado');
        $validator = Validator::make($data, [
            'nombre' => 'required|max:250|string',
            'cantidad' => 'required|numeric',
            'estado' => 'required|numeric'
        ]);
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el producto
        $product = Product::findOrfail($id);
        //Actualizamos el producto.
        $product->update([
            'nombre' => $request->nombre,
            'cantidad' => $request->cantidad,
            'estado' => $request->estado,
        ]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'data' => $product
        ], Response::HTTP_OK);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Buscamos el producto
        $product = Product::findOrfail($id);
        //Eliminamos el producto
        $product->delete();
        //Devolvemos la respuesta
        return response()->json([
            'message' => 'Producto eliminado correctamente'
        ], Response::HTTP_OK);
    }
    /**
     * Mostrar Producto por Código.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function getProdCod($codigo1)
    {
        //Bucamos el producto
        $product = Product::where('codigo1',$codigo1)->get();
        //Si el producto no existe devolvemos error no encontrado
        if ($product->isEmpty()) {
            return response()->json([
                'message' => 'Producto no encontrado.'
            ], 404);
        }
        //Si hay producto lo devolvemos
        return $product;
    }
    /**
     * Método para ingresar cantidades de Gondola y Bodega.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function ActProdCod(Request $request, $codigo1)
    {
        //Validación de datos
        $data = $request->only('gondola','bodega');
        $validator = Validator::make($data, [
            'gondola' => 'required|integer|min:0',
            'bodega' => 'required|integer|min:0'
        ]);
        //Creación de variables y suma de estos para llenar campo en tabla BBDD
        $bod=$request->bodega;
        $gon=$request->gondola;
        $result=$bod+$gon;
        //Si falla la validación error.
        if ($validator->fails()) {
            return response()->json(['error' => $validator->messages()], 400);
        }
        //Buscamos el producto, actualizamos campos
        $product = Product::where('codigo1',$codigo1)->update(['gondola' => $request->gondola, 'bodega'=>$request->bodega,'resultado'=>$result]);
        //Devolvemos los datos actualizados.
        return response()->json([
            'message' => 'Producto actualizado correctamente',
            'data' => $product
        ], Response::HTTP_OK);
    }
}
