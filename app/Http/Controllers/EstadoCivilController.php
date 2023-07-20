<?php

namespace App\Http\Controllers;

use App\Models\EstadoCivil;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Validator;

class EstadoCivilController extends Controller
{
    //getAll
    //getById
    //create
    //edit
    //delete

    public function getAll(Request $request)
    {
        try {
            $edosCivil = EstadoCivil::query()->get();
            return response()->json($edosCivil, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function getById(Request $request, int $id)
    {
        try {
            if ($id < 1)
                return response()->json("Id cannot be less than 1", 400);

            $edoCivil = EstadoCivil::query()->find($id);
            return response()->json($edoCivil, 200);
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

            $edoCivil = new EstadoCivil();
            $edoCivil->Descripcion = $request->input('Descripcion');
            $edoCivil->save();

            return response()->json($edoCivil, 200);
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

            $edoCivil = EstadoCivil::query()->find($request->input('id'));
            if ($edoCivil == null)
                return response()->json('Estado Civil not found', 404);

            $edoCivil->Descripcion = $request->input('Descripcion');
            $edoCivil->save();

            return response()->json($edoCivil, 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function delete(Request $request, int $id)
    {
        try {
            if ($id < 1)
                return response()->json("Id cannot be less than 1", 400);

            $edoCivil = EstadoCivil::query()->find($id);
            if ($edoCivil == null)
                return response()->json('Estado Civil not found', 404);

            $edoCivil->delete();

            return response()->json(true, 200);

        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }
}