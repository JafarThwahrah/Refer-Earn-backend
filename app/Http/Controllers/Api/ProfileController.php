<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\ReferralLink;
use App\Models\ReferrerReferred;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function get_user_data()
    {
        $user = User::find(auth('api')->user()->id);
        $referal_link = ReferralLink::where("user_id", $user->id)->first();
        $points_sum = Point::where('wallet_id', $user->wallet->id)->sum('points');
        //return the needed data for the profile page
        $data = [
            'total_views' => $referal_link->total_views,
            'unique_views' => $referal_link->unique_views,
            'total_points' => $points_sum,
            //the referral url equals the register url + the user id 
            'referral_link' => env('REGISTER_URL') . $user->id,
            'user_image' => $user->image,
            'user_name' => $user->name,
            'level' => $user->level,
            'is_admin' => $user->is_admin
        ];

        //get points grouped by date for the last 14 days for userprofile points chart
        $today = Carbon::today();
        $startDate = $today->subDays(14);
        $data['points_per_day'] = Point::where('wallet_id', $user->wallet->id)
            ->where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(points) as total_points')
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        return response()->json([
            'status' => 200,
            'message' => 'user information obtained successfully',
            'data' => $data
        ]);
    }

    public function get_referrals_tree()
    {
        $user_id = auth('api')->user()->id;
        $family_tree = $this->build_family_tree($user_id);

        return response()->json([
            'status' => 200,
            'message' => "user referees tree obtained successfully",
            'data' => $family_tree
        ], 200);
    }

    private function build_family_tree($user_id)
    {
        $user = User::find($user_id);

        // Base case: if the user doesn't have any referred users
        if (!$user) {
            return [];
        }

        $referees = ReferrerReferred::where('referrer_id', $user_id)->get();

        $tree_node = [
            'id' => $user->id,
            'name' => $user->name,
            'referees' => [],
        ];

        foreach ($referees as $referee) {
            $tree_node['referees'][] = $this->build_family_tree($referee->referred_id);
        }

        return $tree_node;
    }
}
