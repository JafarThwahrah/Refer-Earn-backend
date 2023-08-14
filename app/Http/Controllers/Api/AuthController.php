<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Point;
use App\Models\ReferrerReferred;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'is_admin' => false,
        ]);
        Wallet::create([
            'user_id' => $user->id,
        ]);


        if ($request->referrer_id) {
            $referrer_user = User::find($request->referrer_id);
            ReferrerReferred::create([
                'referrer_id' => $referrer_user->id,
                'referred_id' => $user->id
            ]);

            $referreds_per_user = ReferrerReferred::where('referrer_id', $referrer_user->id)->get()->count();
            if ($referreds_per_user <= 5)
                Point::create([
                    'wallet_id' => $referrer_user->wallet->id,
                    'points' => 5,
                ]);

            if ($referreds_per_user >= 6 && $referreds_per_user <= 10)
                Point::create([
                    'wallet_id' => $referrer_user->wallet->id,
                    'points' => 7,
                ]);

            if ($referreds_per_user > 10)
                Point::create([
                    'wallet_id' => $referrer_user->wallet->id,
                    'points' => 10,
                ]);
        }
        if ($request->hasFile('image')) {
            $user->addMedia($request->file('image'))->toMediaCollection('user');
        }
        $user->access_token = $user->createToken('authToken')->accessToken;
        return response()->json([
            'status' => 201,
            "message" => 'Registration Successful',
            "data" => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $request->email)->first();
        if (!$user || !auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'status' => 401,
                "message" => 'Invalid credentials',
                'data' => null
            ], 401);
        }
        $user->access_token = $user->createToken('authToken')->accessToken;
        return response()->json([
            'status' => 200,
            "message" => 'api.Login response',
            'data' => new UserResource($user),

        ], 200);
    }
}