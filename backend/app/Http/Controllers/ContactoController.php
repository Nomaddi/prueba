<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Contacto;

class ContactoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contactos = Contacto::all();
        return $contactos;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $contacto = new Contacto();
            $contacto -> nombre     = $request->nombre;
            $contacto -> telefono   = $request->telefono;
            $contacto -> ciudad     = $request->ciudad;
            $contacto->save();

            return response()->json([
                'success' => true,
                'data' => $contacto,
            ], 200);

        } catch (Exception $e){
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        try{
        $contacto = Contacto::findOrFail($request->id);
        $contacto -> nombre     = $request->nombre;
        $contacto -> telefono   = $request->telefono;
        $contacto -> ciudad     = $request->ciudad;
        $res = $contacto->save();

        return response()->json([
            'success' => true,
            'data' => $contacto,
        ], 200);
        } catch(Exception $e){
            return response()->json([
                'success'  => false,
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $contacto = Contacto::destroy($request->id);
        return $contacto;
    }
}
