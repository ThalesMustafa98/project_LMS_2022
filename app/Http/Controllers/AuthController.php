<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    /**
     * @throws AuthenticationException
     */
    public function login(Request $request)
        /** Login User **/
    {
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8']
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $info = request(['email', 'password']);
        if (!Auth::attempt($info)) {
            throw new AuthenticationException();
        }
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        /** Create Token **/
        $user_data["user"] = $user;
        $user_data["token_type"] = 'Bearer';
        $user_data["access_token"] = $tokenResult->accessToken;
        return response()->json($user_data, Response::HTTP_OK);
    }

    public function createAccount(Request $request)
        /** Create User **/
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $request['password'] = Hash::make($request['password']);
        $user = User::query()->create([
            'first_name' => $request->name,
            'last_name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        $tokenResult = $user->createToken('Personal Access Token');
        /** Create Token **/
        $user_data["user"] = $user;
        $user_data["token_type"] = 'Bearer';
        $user_data["access_token"] = $tokenResult->accessToken;
        return response()->json($user_data, Response::HTTP_OK);
    }

}
