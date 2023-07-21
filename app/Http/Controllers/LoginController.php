<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    //
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Email' => 'required|email:filter',
                'Password' => 'required|string'
            ]);
            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $user = Usuario::query()->where('Email', '=', $request->input('Email'))->where('Password', '=', $request->input('Password'))->exists();
            if (!$user)
                return response()->json("Usuario not found", 404);

            $user = Usuario::query()->where('Email', '=', $request->input('Email'))->where('Password', '=', $request->input('Password'))->get()[0];
            unset($user->Password);

            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Email' => 'required|email:filter',
                'Password' => 'required|string',
                'isAdmin' => 'required|boolean',
            ]);
            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $user = new Usuario();
            $user->Email = $request->input('Email');
            $user->Password = $request->input('Password');
            $user->isAdmin = $request->input('isAdmin');

            $user->save();
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function delete(Request $request, string $email)
    {
        try {
            if ($email == '')
                return response()->json("Email is not valid", 400);

            $user = Usuario::query()->where('Email', '=', $email)->exists();
            if (!$user)
                return response()->json("Usuario not found", 404);

            $user = Usuario::query()->where('Email', '=', $email)->get();

            $user[0]->delete();
            return response()->json("Usuario has been removed from the database", 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}