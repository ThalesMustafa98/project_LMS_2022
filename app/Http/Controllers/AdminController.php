<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * @throws AuthenticationException
     */
    public function login(Request $request)
        /** Login Admin **/
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
        $admin = $request->admin();
        $tokenResult = $admin->createToken('Personal Access Token');
        /** Create Token **/
        $admin_data["admin"] = $admin;
        $admin_data["token_type"] = 'Bearer';
        $admin_data["access_token"] = $tokenResult->accessToken;
        return response()->json($admin_data, Response::HTTP_OK);
    }

    public function createAccount(Request $request)
        /** Create Admin **/
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'string', 'max:255', Rule::unique('admons', 'email')],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $request['password'] = Hash::make($request['password']);
        $admin = admin::query()->create([
            'first_name' => $request->name,
            'last_name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);
        $tokenResult = $admin->createToken('Personal Access Token');
        /** Create Token **/
        $admin_data["admin"] = $admin;
        $admin_data["token_type"] = 'Bearer';
        $admin_data["access_token"] = $tokenResult->accessToken;
        return response()->json($admin_data, Response::HTTP_OK);
    }

}
