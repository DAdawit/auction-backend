<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\Providers\Auth;

class UserDashboardController extends Controller
{
    public function pendingAcutions($id){
        $pendingAcutions=DB::table('posts')
            ->join('users','posts.user_id','=','users.id')
            ->join('categories','posts.category_id','=','categories.id')
            ->select('posts.id','posts.status','posts.name','posts.price','posts.thumbnail','posts.created_at','categories.category_name')
            ->where('posts.status','Pending')
            ->where('posts.user_id',$id)
            ->orderBy('posts.created_at','desc')
            ->get();
        return $pendingAcutions;
    }
    public function approvedAcutions($id){
        $approvedAcutions=DB::table('posts')
            ->join('users','posts.user_id','=','users.id')
            ->join('categories','posts.category_id','=','categories.id')
            ->select('posts.id','posts.name','posts.status','posts.created_at','posts.thumbnail','posts.price','posts.acution_date','categories.category_name')
            ->where('posts.status','Approved')
            ->where('posts.user_id',$id)
            ->get();
        return $approvedAcutions;
    }
    public function rejectedAcutions($id){
        $rejectedAcutions=DB::table('posts')
            ->join('users','posts.user_id','=','users.id')
            ->join('categories','posts.category_id','=','categories.id')
            ->join('messages','posts.id','=','messages.post_id')
            ->select('posts.id','posts.name','posts.status','posts.created_at','posts.thumbnail','posts.price','categories.category_name','messages.message')
            ->where('posts.status','Rejected')
            ->where('posts.user_id',$id)
            ->get();
        return $rejectedAcutions;
    }


    public function MyCart($id){
        $mywinnes=DB::table('winners')
            ->join('posts','winners.post_id','=','posts.id')
            ->join('categories','posts.category_id','=','categories.id')
            ->join('profiles','posts.user_id','=','profiles.user_id')
            ->where('winners.user_id','=',$id)
            ->where('winners.paid','=',false)
            ->select('winners.id','winners.user_id','posts.name','posts.price','posts.booking_price','posts.thumbnail','posts.description','categories.category_name','winners.created_at','profiles.book_number','profiles.phone_number','profiles.address','profiles.f_name','profiles.l_name','winners.winning_price')
            ->get();
        return $mywinnes;
    }
}
