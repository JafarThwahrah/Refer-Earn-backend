<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\ReferrerReferred;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function get_users(Request $request)
    {
        $name = $request->input('name');

        if ($name) {
            // Filter users whose names are like the provided name
            $users = User::where('name', 'like', '%' . $name . '%')->get();
        } else {
            $users = User::all();
        }


        $users_data = $users->map(function ($item) {
            $points_sum = Point::where('wallet_id', $item->wallet->id)->sum('points');
            $num_of_referd_users = ReferrerReferred::where('referrer_id', $item->id)->count();
            $data = [
                'name' => $item->name,
                'email' => $item->email,
                'registration_date' => $item->created_at->format('d/m/Y'),
                'total_points' => $points_sum,
                'num_of_referred_users' => $num_of_referd_users
            ];
            return $data;
        });

        return response()->json([
            'status' => 200,
            'message' => "users data obtained successfully",
            'data' => $users_data
        ]);
    }


    public function get_overview()
    {

        $data = [
            'total_users' => User::all()->count(),
            'total_points' => Point::all()->sum('points'),

        ];
        return response()->json([
            'status' => 200,
            'message' => "over view data obtained successfully",
            'data' => $data
        ]);
    }
}
