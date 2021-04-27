<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\post;
use Carbon\Carbon;
class ManageActionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $acutions = DB::table('posts')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->join('users','posts.user_id','=','users.id')
            ->join('profiles', 'posts.user_id', '=', 'profiles.user_id')
            ->select('posts.*', 'categories.category_name','users.email','profiles.f_name','profiles.l_name','profiles.org_name','profiles.po_box','profiles.usage','profiles.phone_number','profiles.address','profiles.profile_image')
            ->orderBy('posts.created_at', 'asc')
            ->where('posts.status','=','pending')
            ->get();
        return $acutions;
    }


    public function create()
    {
    $post=post::find(3);
    $p=$post->acution_date;
    $date=explode(' ',$p);
    $date_y_m_d=explode('-',$date[0]);
    $date_h_m_s=explode(':',$date[1]);
    $carbondate=Carbon::create($date_y_m_d[0], $date_y_m_d[1], $date_y_m_d[2], $date_h_m_s[0], $date_h_m_s[1], $date_h_m_s[2]);
    $current=$carbondate->toDateTimeString();
    $updated= $carbondate->toDateTimeString();
    $post->acution_date=$updated;
    $post->update();
    return [$current,$updated,$post];


    }

    public function update(Request $request, $id)
    {
//        return $request;
        $acution=post::find($id);
        $acution->acution_date=$request->acution_date;
        $start_date=explode(' ',$request->acution_date);
        $date_y_m_d=explode('-',$start_date[0]);
        $date_h_m_s=explode(':',$start_date[1]);
        $carbondate=Carbon::create($date_y_m_d[0], $date_y_m_d[1], $date_y_m_d[2], $date_h_m_s[0], $date_h_m_s[1], 00);
        $auction_due_time=$carbondate->toDateTimeString();
        $auction_start_time=$carbondate->subHour()->toDateTimeString();
        $acution->acution_start_date=$auction_start_time;
        $acution->acution_date=$auction_due_time;
        $acution->status=$request->status;
        $acution->update();

        return response()->json([200]);
    }

    public function reject(Request $request){
//        return $request;
        $message=new Message();
        $message->user_id=$request->user_id;
        $message->post_id=$request->post_id;
        $message->message=$request->message;
        $message->save();
        $post=post::find($request->post_id);
        $post->status=$request->status;
        $post->update();
        return 'success';
    }
        public function completedAuctions(){
            $auction=DB::table('winners')
                ->join('bidders','winners.post_id','=','bidders.post_id')
                ->where('winners.post_id','=','bidders.post_id')
                ->orderBy('winners.post_id','desc')
                ->get();
            return $auction;
        }

}
