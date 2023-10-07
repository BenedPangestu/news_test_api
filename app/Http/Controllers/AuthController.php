<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Auth\AuthLoginRequest;
use App\Http\Requests\Auth\AuthRegisterRequest;
use App\Models\User;
use App\Providers\ResponseBuilder;
use Exception;

class AuthController extends Controller
{
    public function unauthenticated(){
        return ResponseBuilder::success(401, "gagal", ['msg'=>"unauthenticated"], false);
    }
    public function login(AuthLoginRequest $request)
    {
        try {
            $data = [
                'grant_type' => 'password',
                'client_id' => '2',
                'client_secret' => 'I8MG4eTpxYYq5FQOfZFbMXZe0oAjb0mQqM6G8DDx',
                'username' => $request->username,
                'password' => $request->password
            ];
            $httpResponse = app()->handle(
                Request::create('oauth/token', 'POST', $data)
            );
            $result = json_decode($httpResponse->getContent());
            if ($httpResponse->getStatusCode() !== 200) {
                throw new Exception($result->message);
            }
            return ResponseBuilder::success(200, "berhasil", $result, true, true);
        } catch (Exception $ex) {
            return ResponseBuilder::success(500, "gagal", $ex, false);
        }
    }
    public function register(AuthRegisterRequest $request)
    {
        try {
            $result = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);
            return ResponseBuilder::success(200, "berhasil", $result, true, true);
        } catch (Exception $ex) {
            return ResponseBuilder::success(500, "gagal", $ex, false);
        }
    }
    public function me(Request $request)
    {
        try {
            // return response()->json($request->user('api'));
            return ResponseBuilder::success(200, "berhasil", $request->user('api'), true, true);
        } catch (Exception $ex) {
            return ResponseBuilder::success(500, "gagal", $ex, false);
        }
    }
    public function logout(Request $request)
    {
        try {
            $status = $request->user('api')->token()->revoke();
            return ResponseBuilder::success(200, "berhasil", [], $status);

        } catch (Exception $ex) {
            return ResponseBuilder::success(500, "gagal", $ex, false);

        }
    }
}
