<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\EntidadFederativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EntidadFederativaController extends Controller
{
    //

    //getAll
    //getById
    //create
    //edit
    //delete

    public function getAll(Request $request)
    {
        try {
            $entFeds = DB::table('entidades_federativas')->get();
            return response()->json($entFeds, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function getById(Request $request, int $id)
    {
        try {
            if ($id < 1)
                return response()->json("Id cannot be less than 1", 400);

            $entFed = EntidadFederativa::query()->find($id);
            if ($entFed == null)
                return response()->json("Entidad Federativa Not Found", 404);

            return response()->json($entFed, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function create(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'Nombre' => 'required',
                'Clave' => 'required|string|size:2'
            ]);

            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $entFed = new EntidadFederativa();
            $entFed->Nombre = $request->input('Nombre');
            $entFed->Clave = $request->input('Clave');
            $entFed->save();

            return response()->json($entFed);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }



    }

    public function edit(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id' => 'required',
                'Nombre' => 'required',
                'Clave' => 'required|string|size:2'
            ]);
            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $entFed = EntidadFederativa::query()->find($request->input('id'));
            if ($entFed == null)
                return response()->json("Entidad Federativa Not Found", 404);


            $entFed->Nombre = $request->input('Nombre');
            $entFed->Clave = $request->input('Clave');
            $entFed->save();
            return response()->json($entFed);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }



    }

    public function delete(Request $request, int $id)
    {
        try {
            $entFed = EntidadFederativa::query()->find($request->input('id'))->first();
            if ($entFed == null)
                return response()->json("Entidad Federativa Not Found", 404);

            $entFed->delete();

            return response()->json("Entidad Federativa deleted", 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }
}