<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\Point;
use App\Models\ReferralLink;
use App\Models\ReferrerReferred;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        //validation with RegisterRequest

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'birth_date' => $request->birth_date,
            'is_admin' => false,
        ]);

        //create wallet and referralLink instances for the user
        Wallet::create([
            'user_id' => $user->id,
        ]);
        ReferralLink::create([
            'user_id' => $user->id,
        ]);
        //handling image using spatie
        if ($request->hasFile('image')) {
            $user->addMedia($request->file('image'))->toMediaCollection('user');
        }

        //generating access token
        $user->access_token = $user->createToken('authToken')->accessToken;

        //calculating points for the user
        if ($request->referrer_id) {
            $referrer_user = User::find($request->referrer_id);
            if ($referrer_user) {
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
        }

        return response()->json([
            'status' => 201,
            "message" => 'Registration Successful',
            "data" => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request)
    {
        //authentication process
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 401,
                'message' => 'Invalid credentials',
                'data' => null
            ], 401);
        }

        $token = $user->createToken('authToken');
        $user->access_token = $token->accessToken;

        return response()->json([
            'status' => 200,
            'message' => 'Login successful',
            'data' => new UserResource($user),
        ], 200);
    }

    public function logout()
    {
        Auth::guard('api')->logout();

        return response()->json([
            'status' => 200,
            'message' => 'logout successful',
            'data' => null,
        ], 200);
    }
}
