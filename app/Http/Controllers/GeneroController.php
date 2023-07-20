<?php

namespace App\Http\Controllers;

use App\Models\Genero;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class GeneroController extends Controller
{
    //getAll
    //getById
    //create
    //edit
    //delete

    public function getAll(Request $request)
    {
        try {
            $generos = Genero::query()->get();

            return response()->json($generos, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getById(Request $request, int $id)
    {
        try {
            if ($id < 1)
                return response()->json("Id cannot be less than 1", 400);

            $genero = Genero::query()->find($id);
            return response()->json($genero, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Descripcion' => 'required',
            ]);

            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $genero = new Genero();
            $genero->Descripcion = $request->input('Descripcion');
            $genero->save();

            return response()->json($genero, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric|min:1',
                'Descripcion' => 'required',
            ]);

            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $genero = Genero::query()->find($request->input('id'));
            if ($genero == null)
                return response()->json("Genero not found", 404);

            $genero->Descripcion = $request->input('Descripcion');
            $genero->save();

            return response()->json($genero, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function delete(Request $request, int $id)
    {
        try {
            if ($id < 1)
                return response()->json("Id cannot be less than 1", 400);

            $genero = Genero::query()->find($id);
            if ($genero == null)
                return response()->json("Genero not found", 404);

            $genero->delete();

            return response()->json(true, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}