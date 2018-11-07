<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\SignUpClientRequest;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
	/**
	 * Create user
	 *
	 * @param  [string] email
	 * @param  [string] password
	 * @param  [string] password_confirmation
	 * @return [string] message
	 */
	public function signUp(SignUpClientRequest $req)
	{
		$user = new User([
			'email' => $req->email,
			'password' => bcrypt($req->password)
		]);
		$user->save();
		$user->client()->create([
			'firstname' => $req->firstname,
			'lastname' => $req->lastname,
			'address' => $req->address,
			'country' => $req->country,
			'phone' => $req->phone,
			'client_type' => $req->client_type,
		])->save();

		return response()->json(['message' => 'Successfully created user!'], 201);
	}

	/**
	 * Login user and generate token and Scope by [ClientType]
	 * @var LoginUser $request
	 * @param  String email
	 * @param  String password
	 * @param  Boolean remember_me
	 *
	 * @return [string] access_token
	 * @return [string] token_type
	 * @return [string] expires_at
	 */
	public function login(LoginUserRequest $request)
	{
		$credentials = request(['email', 'password']);
		if (!Auth::attempt($credentials))
			return response()->json(['message' => 'Unauthorized'], 401);

		$user = $request->user();
		// Check type and Add it like a Scope
		$clientType = $user->checkClientType();
		$tokenResult = $user->createToken('Personal Access Token', [$clientType]);

		$token = $tokenResult->token;

		if ($request->remember_me) {
			$token->expires_at = Carbon::now()->addWeeks(1);
		}

		$token->save();

		return response()->json([
			'access_token' => $tokenResult->accessToken,
			'token_type' => 'Bearer',
			'expires_at' => Carbon::parse(
				$tokenResult->token->expires_at
			)->toDateTimeString()
		]);
	}


	/**
	 * Logout user (Revoke the token)
	 *
	 * @return [string] message
	 */
	public function logout(Request $request)
	{
		$request->user()->token()->revoke();
		return response()->json([
			'message' => 'Successfully logged out'
		], 200);
	}

	/**
	 * Get the authenticated User
	 *
	 * @return [json] user object with client Infos
	 */
	public function user(Request $request)
	{
		$user = $request->user()->load('client');
//		$user = $request->user()->checkClientType();
		return response()->json($user);
	}
}
