<?php

namespace App\Http\Controllers;

use App\Models\EntidadFederativa;
use App\Models\EstadoCivil;
use App\Models\Genero;
use Illuminate\Http\Request;
use App\Models\Usuario;
use Illuminate\Support\Facades\Validator;
use Exception;


class UsersController extends Controller
{
    //getAll
    //getById
    //create
    //edit
    //delete
    //login
    //generarCurp
    //generaDigitoNoRepetido
    //generarDigitoVerificador

    public function getAll(Request $request)
    {
        try {
            $users = Usuario::query()->get();

            foreach ($users as $user) {
                unset($user->Password);
                //Creo que esto se puede hacer con un join.
                $enFed = EntidadFederativa::query()->find($user->EntidadFederativaNacimiento)->first();
                $user->EntidadFederativaNacimiento = $enFed;
                $edoCivil = EstadoCivil::query()->find($user->EstadoCivil)->first();
                $user->EstadoCivil = $edoCivil;
                $genero = EntidadFederativa::query()->find($user->Genero)->first();
                $user->Genero = $genero;
            }

            return response()->json($users, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function getUserByCURP(Request $request, string $curp)
    {
        try {
            if ($curp == '')
                return response()->json("CURP is empty", 400);

            if (strlen($curp) != 18)
                return response()->json("CURP is not valid", 400);

            $user = Usuario::query()->where('CURP', '=', $curp)->first();
            if ($user == null)
                return response()->json("Usuario not found", 404);

            unset($user->Password);
            //Creo que esto se puede hacer con un join.
            $enFed = EntidadFederativa::query()->find($user->EntidadFederativaNacimiento)->first();
            $user->EntidadFederativaNacimiento = $enFed;
            $edoCivil = EstadoCivil::query()->find($user->EstadoCivil)->first();
            $user->EstadoCivil = $edoCivil;
            $genero = EntidadFederativa::query()->find($user->Genero)->first();
            $user->Genero = $genero;
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function create(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Nombre' => 'string|max:128',
                'ApellidoPaterno' => 'string|max:64',
                'ApellidoMaterno' => 'string|max:64',
                'FechaNacimiento' => 'date_format:Y-m-d',
                'EntidadFederativaNacimiento' => 'required|numeric',
                'EstadoCivil' => 'required|numeric',
                'Genero' => 'required|numeric',
                'Email' => 'required|email:filter',
                'Password' => 'required|string',
                'isAdmin' => 'boolean',
            ]);
            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $user = new Usuario();
            $user->Nombre = $request->input('Nombre');
            $user->ApellidoPaterno = $request->input('ApellidoPaterno');
            $user->ApellidoMaterno = $request->input('ApellidoMaterno');
            $user->FechaNacimiento = $request->input('FechaNacimiento');
            #El obtener algo de otra tabla debería de estar en su respectivo Repository, yo creo que no se debería de hacer así
            #pero por cuestiones de tiempo lo haré así.
            $entidadFederativa = EntidadFederativa::query()->find($request->input('EntidadFederativaNacimiento'));
            if ($entidadFederativa == null)
                return response()->json("Entidad Federativa not found, Usuario not created", 404);
            $user->EntidadFederativaNacimiento = $entidadFederativa;
            $estadoCivil = EstadoCivil::query()->find($request->input('EstadoCivil'));
            $user->EstadoCivil = $estadoCivil;
            $genero = Genero::query()->find($request->input('Genero'));
            if ($genero == null)
                return response()->json("Genero not found, Usuario not created", 404);
            $user->Genero = $genero;
            $user->Email = $request->input('Email');
            $user->Password = $request->input('Password');
            if ($request->input('isAdmin') == null) {
                $user->isAdmin = false;
            } else {
                $user->isAdmin = $request->input('isAdmin');
            }
            $user->CURP = implode($this->generarCURP($user));

            //Me di cuenta que las foreign keys se guardan solo con el ID, como uso el modelo para generar el CURP, regreso su valor a solo ID
            //Aqui ya se hizo la validacion de que existen, entonces no me preocupo por volver a hacerla
            //Estoy seguro que hay mejores maneras de hacer esto pero por el tiempo, lo dejaré así.
            $user->EntidadFederativaNacimiento = $entidadFederativa->id;
            $user->EstadoCivil = $estadoCivil->id;
            $user->Genero = $genero->id;
            $user->save();

            //Como regreso el objeto creado, quito los valores que no son necesarios.
            unset($user->Password);
            unset($user->id);
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json($e->__toString(), 500);
        }

    }

    public function edit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'CURP' => 'required|string|size:18',
                'Nombre' => 'string|max:128',
                'ApellidoPaterno' => 'string|max:64',
                'ApellidoMaterno' => 'string|max:64',
                'FechaNacimiento' => 'date_format:Y-m-d',
                'EntidadFederativaNacimiento' => 'required|numeric',
                'EstadoCivil' => 'required|numeric',
                'Genero' => 'required|numeric',
                'Email' => 'required|email:filter',
            ]);
            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $userToEdit = Usuario::query()->where('CURP', '=', $request->input('CURP'))->first();
            if ($userToEdit == null)
                return response()->json("Usuario not found", 404);

            $userToEdit->Nombre = $request->input('Nombre');
            $userToEdit->ApellidoPaterno = $request->input('ApellidoPaterno');
            $userToEdit->ApellidoMaterno = $request->input('ApellidoMaterno');
            $userToEdit->FechaNacimiento = $request->input('FechaNacimiento');
            $enFed = EntidadFederativa::query()->find($request->input('EntidadFederativaNAcimiento'))->exists();
            if (!$enFed)
                return response()->json("Entidad Federativa not found, User not updated", 404);
            $userToEdit->EntidadFederativaNacimiento = $request->input('EntidadFederativaNacimiento');
            $edoCivil = EstadoCivil::query()->find($request->input('EstadoCivil'))->exists();
            if (!$edoCivil)
                return response()->json("Estado Civil not found, User not updated", 404);
            $userToEdit->EstadoCivil = $request->input('Estado Civil');
            $genero = Genero::query()->find($request->input('Genero'))->exists();
            if (!$genero)
                return response()->json("Estado Civil not found, User not updated", 404);
            $userToEdit->Genero = $request->input('Genero');
            $userToEdit->Email = $request->input('Email');


            $userToEdit->save();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function delete(Request $request, string $curp)
    {
        try {
            //Como las llaves foraneas solo estan en la tabla de users no me preocupare por lo demas,
            //Y solo borarré todo el row del usuario.
            if (strlen($curp) != 18)
                return response()->json("CURP is not valid", 400);

            $userToDelete = Usuario::query()->where('CURP', '=', $request->input('CURP'))->first();
            if ($userToDelete == null)
                return response()->json("Usuario not found", 404);

            $userToDelete->delete();
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'Email' => 'required|email:filter',
                'Password' => 'required|string'
            ]);
            if ($validator->fails())
                return response()->json($validator->failed(), 400);

            $user = Usuario::query()->where('Email', '=', $request->input('Email'))->where('Password', '=', $request->input('Password'))->first();
            if ($user == null)
                return response()->json("Usuario not found", 404);

            unset($user->Password);
            //Creo que esto se puede hacer con un join.
            $enFed = EntidadFederativa::query()->find($user->EntidadFederativaNacimiento)->first();
            $user->EntidadFederativaNacimiento = $enFed;
            $edoCivil = EstadoCivil::query()->find($user->EstadoCivil)->first();
            $user->EstadoCivil = $edoCivil;
            $genero = EntidadFederativa::query()->find($user->Genero)->first();
            $user->Genero = $genero;
            return response()->json($user, 200);
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function generarCURP(Usuario $user)
    {
        $curp = [];
        // Función para limpiar acentos y caracteres especiales
        function limpiarTexto($texto)
        {
            $texto = mb_strtoupper($texto, 'UTF-8');
            $texto = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú'], ['A', 'E', 'I', 'O', 'U'], $texto);
            $texto = preg_replace('/[^A-Z]/', '', $texto);
            return $texto;
        }

        $nombre = limpiarTexto($user->Nombre);
        $apPaterno = limpiarTexto($user->ApellidoPaterno);
        $apMaterno = limpiarTexto($user->ApellidoMaterno);

        // Extraer la primera letra y la primera vocal interna del primer apellido
        $curp[] = $apPaterno[0];
        preg_match('/[AEIOU]/', substr($apPaterno, 1), $vocalInterna);
        $curp[] = $vocalInterna[0];

        // Extraer la primera letra del apellido materno
        $curp[] = $apMaterno[0];

        // Extraer la primera letra del primer nombre
        $curp[] = $nombre[0];

        // Formatear la fecha de nacimiento en el formato YYMMDD
        $fechaNacimiento = str_replace('-', '', $user->FechaNacimiento);
        $curp[] = substr($fechaNacimiento, 2);

        $genero = limpiarTexto($user->Genero->Descripcion);
        $curp[] = $genero === 'HOMBRE' ? 'H' : ($genero === 'MUJER' ? 'M' : 'X');

        // Convertir las dos letras de la entidad federativa a mayúsculas
        $entidadFederativa = limpiarTexto($user->EntidadFederativaNacimiento->Clave);
        $curp[] = substr($entidadFederativa, 0, 2);
        //13

        //Extraer la primer consonante interna del apellido paterno excluyendo la primer letra
        preg_match('/[^AEIOU]/', substr($apPaterno, 1), $consonanteInterna);
        $curp[] = $consonanteInterna[0];

        //Extraer la primer consonante interna del apellido materno
        preg_match('/[^AEIOU]/', substr($apMaterno, 1), $consonanteInternaMat);
        $curp[] = $consonanteInternaMat[0];

        //Extraer la primer consonante interna del nombre de pila
        preg_match('/[^AEIOU]/', substr($nombre, 1), $consonanteInternaNom);
        $curp[] = $consonanteInternaNom[0];

        //Genera Digito para evitar repetidos
        $curp[] = $this->generaDigitoNoRepetido(implode($curp), $user->FechaNacimiento);

        $curp[] = $this->generarDigitoVerificador(implode($curp));

        return $curp;
    }

    public function generaDigitoNoRepetido(string $curpInc, string $fechaNacimiento)
    {
        function generarDigitoFechaNacimiento($fNacimiento)
        {
            // Formatear la fecha de nacimiento en el formato YYYY-MM-DD
            $fNacimiento = date('Y-m-d', strtotime($fNacimiento));

            // Obtener el año de nacimiento
            $yearNacimiento = intval(substr($fNacimiento, 0, 4));

            // Generar un dígito aleatorio del 0 al 9 para fechas hasta el año 1999
            if ($yearNacimiento < 2000) {
                $digito = mt_rand(0, 9);
            } else {
                // Generar un carácter aleatorio de la A a la Z para fechas a partir del año 2000
                $digito = chr(65 + mt_rand(0, 25));
            }
            return $digito;
        }


        $users = Usuario::query()->where('CURP', 'LIKE', $curpInc . '%')->get();
        $curpValid = false;
        $lastDig = null;
        while (!$curpValid) {
            $lastDig = generarDigitoFechaNacimiento($fechaNacimiento);
            $curpFound = false;
            foreach ($users as $user) {
                $curpFound = str_contains($user->CURP, $curpInc . $lastDig);
                if ($curpFound)
                    return;
            }

            $curpValid = !$curpFound;
        }

        return $lastDig;
    }


    public function generarDigitoVerificador($curp)
    {
        // Tabla de valores para calcular el dígito verificador
        $valores = array(
            '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4,
            '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
            'A' => 10, 'B' => 11, 'C' => 12, 'D' => 13, 'E' => 14, 'F' => 15, 'G' => 16,
            'H' => 17, 'I' => 18, 'J' => 19, 'K' => 20, 'L' => 21, 'M' => 22, 'N' => 23,
            'Ñ' => 24, 'O' => 25, 'P' => 26, 'Q' => 27, 'R' => 28, 'S' => 29, 'T' => 30,
            'U' => 31, 'V' => 32, 'W' => 33, 'X' => 34, 'Y' => 35, 'Z' => 36
        );

        // Suma ponderada de los caracteres del CURP
        $suma = 0;
        for ($i = 0; $i < 17; $i++) {
            $caracter = strtoupper($curp[$i]);
            $valor = isset($valores[$caracter]) ? $valores[$caracter] : 0;
            $suma += $valor * (18 - $i);
        }

        // Obtener el dígito verificador
        $digitoVerificador = (10 - ($suma % 10)) % 10;

        return $digitoVerificador;
    }
}