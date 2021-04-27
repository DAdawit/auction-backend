<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Bidder;

class BiddersController extends Controller
{
    public function check(Request $request){
//        return $request;
        $user=Bidder::select("*")
            ->where([
                ["user_id","=",$request->user_id],
                ["post_id","=",$request->post_id]
            ])
            ->exists();
        if($user){
            return 1;
        }else{
            return 0;
        }
    }


    public function store(Request $request){
      $user=Bidder::select("*")
          ->where([
              ["user_id","=",$request->user_id],
              ["post_id","=",$request->post_id]
             ])
        ->count();

        if($user==1){
            return 'user exist';
//            return response()->json('paid',200,$user);
        }else{
            $bidder=new Bidder();
            $bidder->user_id=$request->user_id;
            $bidder->post_id=$request->post_id;
            $bidder->bookingPrice=$request->booking_price;
            $bidder->save();
            return response()->json('saved',200);
        }
    }
    public function winner(Request $request){
        $winner=DB::table('bids')
//            ->join('profiles','bids.user_id','=','profiles.user_id')
            ->where('bids.user_id','=',$request->user_id)
            ->where('bids.post_id','=',$request->post_id)
            ->max('bids.price');
//            ->select('profiles.f_name','profiles.l_name','profiles.profile_image','profiles.id','bids.price')
//            ->orderBy('bids.price', 'desc')
//            ->get();
//       return  json_decode($winner,true)[0];
            return $winner;
    }
}
