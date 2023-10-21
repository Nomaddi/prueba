<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = Tag::all();
        return $tags;
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
        try {
            $tags = new Tag();
            $tags->nombre         = $request->nombre;
            $tags->descripcion    = $request->descripcion;
            $tags->color          = $request->color;
            $tags->save();

            return response()->json([
                'success' => true,
                'data' => $tags,
            ], 200);
        } catch (Exception $e) {
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
    public function update(Request $request, $id)
    {
        try {
            $tag = Tag::findOrFail($id); // Corrige aquí el uso de $id
            $tag->nombre = $request->nombre;
            $tag->descripcion = $request->descripcion;
            $tag->color = $request->color;
            $tag->save();

            return response()->json([
                'success' => true,
                'data' => $tag,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
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
        $tag = Tag::findOrFail($request->id);

        // Verifica si la etiqueta está relacionada con algún contacto
        if ($tag->contactos->count() > 0) {
            // Si hay contactos que dependen de esta etiqueta, muestra un mensaje de advertencia
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar la etiqueta porque está relacionada con contactos.',
                'related_contacts' => $tag->contactos, // Puedes enviar información sobre los contactos relacionados
            ], 400);
        }

        // Si no hay contactos relacionados, elimina la etiqueta
        $tag->delete();

        return response()->json([
            'success' => true,
            'message' => 'Etiqueta eliminada correctamente.',
        ], 200);
    }
}
