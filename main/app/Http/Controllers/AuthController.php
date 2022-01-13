<?php

namespace App\Http\Controllers;

// use App\CustomModels\Patient;

use App\Models\Patient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Usuario;
use App\Response;
use App\Traits\ApiResponser;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
class AuthController extends Controller
{

    use ApiResponser;
    /**
     * Login usuario y retornar el token
     * @return token
     */
    public function __construct()
    {
    }

    public function index()
    {
        // implement users on line
    }

    public function paginate()
    {
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->only('user', 'password');
            $data['usuario'] = $credentials['user'];
            $data['password'] = $credentials['password'];
            if (!$token = JWTAuth::attempt([
                'usuario' => $data['usuario'],
                'password' => $data['password']
            ])) {
                return response()->json(['error' => 'Unauthoriz55ed'], 401);
            }

            return response()->json(['status' => 'success', 'token' => $this->respondWithToken($token)], 200)->header('Authorization', $token)
                ->withCookie(
                    'token',
                    $token,
                    config('jwt.ttl'),
                    '/'
                );
        } catch (\Throwable $th) {
            return  $this->errorResponse([$th->getMessage(), $th->getFile(), $th->getLine()]);
        }
    }

    public function register()
    {
        $validador = Validator::make(request()->all(), [
            'nombres' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validador->fails()) {
            return response()->json($validador->errors()->toJson(), 400);
        }

        $usuario = Usuario::create([
            'nombres' => request('nombres'),
            'apellidos' => request('apellidos'),
            'identificacion' => request('identificacion'),
            'usuario' => request('identificacion'),
            'password' => bcrypt(request('password')),
        ]);

        $usuario->save();

        $token = $this->guard()->login($usuario);

        return response()->json(['message' => 'User created successfully', 'token' => $token], 201);
    }

    /**
     * Logout usuario
     *
     * @return void
     */

    public function logout()
    {
        auth()->logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out Successfully.'
        ], 200);
    }

    /**
     * Obtener el usuario autenticado
     *
     * @return Usuario
     */


    /**
     * Refrescar el token por uno nuevo
     *
     * @return token
     */

    public function me()
    {
        return response()->json(
            Patient::firstWhere('identifier',  auth()->user()->usuario)
        );
    }

    public function refresh()
    {
        if ($token = $this->guard()->refresh()) {
            return response()->json()
                ->json(['status' => 'successs'], 200)
                ->header('Authorization', $token);
        }
        return response()->json(['error' => 'refresh_token_error'], 401);
    }

    public function renew()
    {
        try {
            //code...
            if (!$token = $this->guard()->refresh()) {
                return response()->json(['error' => 'refresh_token_error'], 401);
            }

            $user = auth()->user();

            $user = Usuario::with(
                [
                    'person' => function ($q) {
                        $q->select('*');
                    },
                    'permissions' => function ($q) {
                        $q->select('*');
                    }
                ]
            )->find($user->id);

            return response()
                ->json(['status' => 'successs', 'token' => $token, 'user' => $user], 200)
                ->header('Authorization', $token);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'refresh_token_error' . $th->getMessage()], 401);
        }
    }

    /**
     * Retornar el guard
     *
     * @return Guard
     */
    private function guard()
    {
        return Auth::guard();
    }


    protected function respondWithToken($token)
    {
        auth()->factory()->getTTL() * 60;

        return $token;
        // return response()->json([
        //     'access_token' => $token,
        //     'token_type' => 'bearer',
        //     'expires_in' => auth()->factory()->getTTL() * 60
        // ]);
    }
    public function changePassword()
    {

        //  try {
        //code...
        // $user = Usuario::find(auth()->user()->id);
        if (!auth()->user()) {
            return response()->json(['error' => 'refresh_token_error'], 401);
        }

        $user = Usuario::find(auth()->user()->id);
        $user->password = Hash::make(Request()->get('newPassword'));
        $user->change_password = 0;
        $user->save();
        return Response()->json(['status' => 'successs', 200]);
        // } catch (\Throwable $th) {
        //     return Response()->json(['status' => 'successs', 400]);
        //throw $th;
        // }
    }
}
