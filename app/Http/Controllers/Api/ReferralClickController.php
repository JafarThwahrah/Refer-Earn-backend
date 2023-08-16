<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReferralClick;
use App\Models\User;
use Illuminate\Http\Request;

class ReferralClickController extends Controller
{
    public function handle_referral_click(Request $request, $user_id)
    {
        // Get the referrer user
        $referrer_user = User::find($user_id);

        if (!$referrer_user) {
            return response()->json([
                'status' => 400,
                'message' => 'invalid referring user id',
                'data' => null
            ], 400);
        }

        // Record the click in the referral_clicks table
        ReferralClick::create([
            'referrer_id' => $referrer_user->id,
            'visitor_ip' => $request->ip(),
        ]);

        // check if the visitor is unique or not within 24 hours
        $check_unique_visitor = ReferralClick::where('referrer_id', $referrer_user->id)
            ->where('visitor_ip', $request->ip())
            ->count();

        //increment total views
        $referrer_user->referral_link->update([
            'total_points' => $referrer_user->referral_link->total_views += 1
        ]);
        if ($check_unique_visitor == 0) {
            //increment unique Views
            $referrer_user->referral_link->update([
                'unique_views' => $referrer_user->referral_link->unique_views += 1
            ]);
        }

        return response()->json([
            'status' => 201,
            "message" => 'View recoreded Successfully',
            "data" =>  $check_unique_visitor
        ], 201);
    }
}
