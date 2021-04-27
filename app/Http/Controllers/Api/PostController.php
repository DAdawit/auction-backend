<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Winner;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
Use Carbon\Carbon;
use App\Models\Post;
use App\Models\User;
use File;
use Auth;
use App\Events\NewProduct;

use Intervention\Image\ImageManager;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all_Acutions(){
        $current = new Carbon();
        $acutions = DB::table('posts')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->join('users','posts.user_id','=','users.id')
            ->select('posts.id','posts.user_id','posts.name','posts.acution_start_date','posts.price','posts.thumbnail','categories.category_name','posts.acution_date')
            ->where('posts.status','=','Approved')
            ->orderBy('posts.acution_date', 'asc')
            ->get();

     $data=collect($acutions);
     return $data->map(function ($item){
         $dt     = Carbon::now();
         return [
             'id'=>$item->id,
             'name'=>$item->name,
             'price'=>$item->price,
             'start'=>Carbon::parse($item->acution_start_date,'GMT+3')->diffForHumans($dt),
             'end'=>Carbon::parse($item->acution_date,'GMT+3')->diffForHumans($dt),
             'thumbnail'=>$item->thumbnail,
             'category_name'=>$item->category_name
         ];
     });
    }

    public function AuctionForHomePage(){
        $acutions = DB::table('posts')
            ->join('categories', 'posts.category_id', '=', 'categories.id')
            ->join('users','posts.user_id','=','users.id')
            ->select('posts.id','posts.user_id','posts.name','posts.acution_start_date','posts.price','posts.thumbnail','categories.category_name','posts.acution_date')
            ->where('posts.status','=','Approved')
            ->orderBy('posts.acution_date', 'asc')
            ->get();
//return $acutions;
        $data=collect($acutions);
        return $data->map(function ($item){
            $dt=Carbon::now();
            return [
                'id'=>$item->id,
                'name'=>$item->name,
                'price'=>$item->price,
                'start'=>Carbon::parse($item->acution_start_date,'GMT+3')->diffForHumans($dt),
                'end'=>Carbon::parse($item->acution_date,'GMT+3')->diffForHumans($dt),
                'thumbnail'=>$item->thumbnail,
                'category_name'=>$item->category_name
            ];
        });
    }



    public function index($id)
    {
        $userpost=DB::table('posts')
             ->join('categories','posts.category_id','=','categories.id')
            ->select('posts.id','posts.name','posts.acution_start_date','posts.price','posts.status','posts.description','posts.thumbnail','posts.image','categories.category_name','posts.category_id','posts.created_at')
            ->where('user_id',$id)
            ->orderBy('posts.created_at', 'desc')
            ->get();
        return $userpost;
    }


    public function store(Request $request)
    {

        $this->validate($request, [
            'name' => 'required',
            'image'=>'required',
        ]);

        if($request->image){
            $imageName = time().'.' . explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
            \Image::make($request->image)->save(public_path('../../frontend/public/products/').$imageName);
            
            $thumbnail=time().'_tumbnail_'.$imageName;
            \Image::make($request->image)->fit(600,600)->save(public_path('../../frontend/public/products/').$thumbnail);
            $request->merge(['image' => $thumbnail]);
        }
        $booking_price=0;
        if($request->price > 1 && $request->price < 1000){
            $booking_price=10;
        }elseif ($request->price > 1000 && $request->price < 10000){
            $booking_price=$booking_price=30;
        }elseif ($request->price > 10000 && $request->price < 25000){
            $booking_price=$booking_price=50;
        } elseif ($request->price > 25000 && $request->price < 50000){
            $booking_price=$booking_price=75;
        }elseif ($request->price > 50000 && $request->price < 100000){
            $booking_price=$booking_price=100;
        }elseif ($request->price > 100000){
            $booking_price=$booking_price=150;
        }

        $post=new Post();
        $post->name=$request->name;
        $post->price=$request->price;
        $post->booking_price=$booking_price;
        $post->description=$request->description;
        $post->category_id=$request->category;
        $post->user_id=Auth::user()->id;
        $post->image='/products/'.$imageName;
        $post->thumbnail='/products/'.$thumbnail;
        $post->product_id=mt_rand(1000000000000000000,1999999999999999999);
        $post->save();
        broadcast(new NewProduct($post))->toOthers();
        return response()->json(['status'=>'success'],200);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $posts=DB::table('posts')
        ->join('categories','posts.category_id','=','categories.id')
        ->select('posts.id','posts.user_id','posts.name','posts.image','posts.booking_price','posts.description','posts.product_id','posts.acution_date','posts.price','categories.category_name','posts.created_at')
        ->where('posts.id',$id)
        ->get();
        return response()->json($posts);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = post::find($id);
        return response()->json($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // $post = post::find($id);

  if($request->image===null){
    $post = post::find($id);
    $post->name=$request->name;
    $post->price=$request->price;
    $post->description=$request->description;
    $post->category_id=$request->category_id;
    $post->update();
    return response()->json('The product successfully updated');
  }else{
    $post = post::find($id);
    $oldimage=post::find($id);
    $image1='../../frontend/public'.$oldimage->image;
    $image2='../../frontend/public'.$oldimage->thumbnail;

    unlink($image1);
    unlink($image2);
   
    $imageName = time().'.' . explode('/', explode(':', substr($request->image, 0, strpos($request->image, ';')))[1])[1];
    \Image::make($request->image)->save(public_path('../../frontend/public/products/').$imageName);
    

    $thumbnail=time().'_tumbnail_'.$imageName;
    \Image::make($request->image)->fit(195,170)->save(public_path('../../frontend/public/products/').$thumbnail);
    $request->merge(['image' => $thumbnail]);
    

        $post->name=$request->name;
        $post->price=$request->price;
        $post->description=$request->description;
        $post->category_id=$request->category_id;
        $post->image='/products/'.$imageName;
        $post->thumbnail='/products/'.$thumbnail;
        $post->save();
      return response()->json('can not be updated');
  }

        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = post::find($id);
        $image1='../../frontend/public'.$post->image;
        $image2='../../frontend/public'.$post->thumbnail;
        unlink($image1);
        unlink($image2);
        $post->delete();

        return response()->json('The product successfully deleted');
    }
public function acutionDate($id){
        $date=Post::find($id);
        return $date->acution_date;
}


public function repostAuction($id){
        $auction=Post::find($id);
        $auction->status='Pending';
        $auction->update();

        $rejected=Message::find($id);
        $rejected->delete();
        return response()->json('success',200);
}
}
