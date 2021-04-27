<?php

namespace App\Http\Controllers\Api;

use App\Events\NewOffer;
use App\Events\UpdateTime;
use App\Http\Controllers\Controller;
use App\Models\Winner;
use Illuminate\Http\Request;
use App\Models\Bid;
use App\Models\User;
use App\Models\Post;
use Carbon\Carbon;
use Auth;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id,Request $request)
    {

        $bids=DB::table('bids')
            ->orderBy('price','desc')
            ->select('user_id','name','price','created_at','post_id')
            ->where('post_id',$id)->get();
        return $bids;
//        $post=Post::find($id);
//        return $post->bids()->orderBy('price', 'desc')->get();

//        return response()->json($post->bids()->latest()->get());
    }


    public function store(Request $request)
    {
        $pricExist=DB::table('bids')
            ->where('bids.post_id','=',$request->post_id)
            ->max('bids.price');
        if($request->price==$pricExist){
            return response()->json('price exist',401);
        }else{
            $bid=new Bid();
            $bid->user_id=Auth::user()->id;
            $bid->price=$request->price;
            $bid->name=$request->name;
            $bid->post_id=$request->post_id;
            $bid->save();
            broadcast(new NewOffer($bid))->toOthers();
            return response()->json('success',$pricExist);
        }

        return $pricExist;
    }


    public function update(Request $request, $id)
    {


        $pricExist=DB::table('bids')
            ->where('bids.post_id','=',$id)
            ->max('bids.price');
        if($request->price==$pricExist){
            return response()->json('price exist',401);
        }else{
            $post=Post::find($id);
            $p=$post->acution_date;
            $date=explode(' ',$p);
            $date_y_m_d=explode('-',$date[0]);
            $date_h_m_s=explode(':',$date[1]);
            $carbondate=Carbon::create($date_y_m_d[0], $date_y_m_d[1], $date_y_m_d[2], $date_h_m_s[0], $date_h_m_s[1], $date_h_m_s[2]);
            $updatedDate=$carbondate->addMinute()->toDateTimeString();
            $olddate=$post->acution_date;
            $post->acution_date=$updatedDate;
            $post->update();
            broadcast(new UpdateTime($post))->toOthers();

            $bid=new Bid();
//            $bid->user_id=Auth::user()->id;
          $bid->user_id=$request->user_id;
            $bid->price=$request->price;
            $bid->name=$request->name;
            $bid->post_id=$request->post_id;
            $bid->save();
            broadcast(new NewOffer($bid))->toOthers();
            return response()->json(['success',$olddate,$post->acution_date,$bid->price],200);
        }


//
//
//
//
//        $post=post::find($id);
//        $p=$post->acution_date;
//        $date=explode(' ',$p);
//        $date_y_m_d=explode('-',$date[0]);
//        $date_h_m_s=explode(':',$date[1]);
//        $carbondate=Carbon::create($date_y_m_d[0], $date_y_m_d[1], $date_y_m_d[2], $date_h_m_s[0], $date_h_m_s[1], $date_h_m_s[2]);
//        $currentDate=$carbondate->toDateTimeString();
//        $updatedDate=$carbondate->addMinute()->toDateTimeString();
//        $post->acution_date=$updatedDate;
//        $post->update();
//        broadcast(new UpdateTime($post))->toOthers();
//
//        $bid=new Bid();
//        $bid->user_id=Auth::user()->id;
//        $bid->price=$request->price;
//        $bid->name=$request->name;
//        $bid->post_id=$request->post_id;
//        $bid->save();
//        broadcast(new NewOffer($bid))->toOthers();
//        return response()->json(['success',$post->acution_date,$updatedDate],200);
//     return response()->json(['success'],200);
    }


    public function removeAuction($id){
        $post=Post::find($id);
        $post->status='Sold';
        $post->update();
        return response()->json('success',200);
    }

    public function saveWinner(Request $request){
        $check=DB::table('winners')
            ->where('winners.post_id','=',$request->post_id)
            ->where('winners.user_id','=',$request->user_id)
            ->exists();
        if($check){
            return response()->json('data exists',400);
        }else{
            $winner=new Winner();
            $winner->post_id=$request->post_id;
            $winner->user_id=$request->user_id;
            $winner->winning_price=$request->price;
            $winner->save();
            return response()->json('success',200);
        }
    }
        public function completePayment($id){
            $winner=Winner::find($id);
            $winner->paid=true;
            $winner->update();
            return response()->json('success',200);
        }
}
