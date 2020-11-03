<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['signup', 'login']]);
    }

    public function all()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function signup(Request $signup)
    {
        $this->validate($signup, [
            'username' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $oparetion = User::create([
            'username' => $signup->username,
            'email' => $signup->email,
            'password' => Hash::make($signup->password)
        ]);
        if ($oparetion) {
            $code = 200;
            $data = [
                'status' => $code,
                'message' => 'User Created Successfully'
            ];
            return response()->json($data, 200);
        }
    }

    public function profile()
    {
        return response()->json(Auth::user(), 200);
    }

    public function login(Request $login)
    {
        $this->validate($login, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($token = Auth::attempt(['email' => $login->email, 'password' => $login->password])) {
            $token = $this->respondWithToken($token);
            $code = 200;
            $data = [
                'status' => $code,
                'message' => 'Login Success',
                'token' => $token
            ];
        } else {
            $code = 401;
            $data = [
                'status' => $code,
                'message' => 'Authentication fail'
            ];
        }
        return response()->json($data, $code);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 200,
            'message' => 'Logout Successfully'
        ], 200);
    }

    //
}
