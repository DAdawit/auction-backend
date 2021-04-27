<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use Auth;
use File;
use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\isEmpty;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $posts=DB::table('posts')
             ->join('categories','posts.user_id','categories.id')
             ->join('profiles','posts.user_id','profiles.user_id')
             ->select('posts.id','posts.name','posts.price','posts.thumbnail','posts.description'
                 ,'posts.created_at','categories.category_name','posts.created_at','profiles.*')
             ->latest('posts.created_at')
             ->get();
        return response()->json($posts);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request,[
            'usage'=>'required',
            'phone_number'=>'required',
            'address'=>'required',
            'book_number'=>'required'
        ]);

        if($request->profile_image!==null) {
            $imageName = time() . '.' . explode('/', explode(':', substr($request->profile_image, 0, strpos($request->profile_image, ';')))[1])[1];
            \Image::make($request->profile_image)->fit(600, 600)->save(public_path('../../frontend/public/profile_pics/') . $imageName);
        }
        $profile=new Profile();
        $profile->user_id=Auth::user()->id;
        $profile->usage=$request->usage;
        $profile->f_name=$request->f_name;
        $profile->l_name=$request->l_name;
        $profile->org_name=$request->org_name;
        $profile->po_box=$request->po_box;
        $profile->address=$request->address;
        $profile->phone_number=$request->phone_number;
        $profile->book_number=$request->book_number;
        $profile->about=$request->about;
        if($request->profile_image===null){
            $profile->profile_image='/no_image.png';
        }else{
            $profile->profile_image='/profile_pics/'.$imageName;
        }
        $profile->save();
         return response()->json('success');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $profile=DB::table('profiles')
            ->where('user_id',$id)
            ->get();
        return $profile;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $profile=Profile::find($id);

         if($request->profile_image===null){
             if($profile->usage==='personal'){
                 $profile->f_name=$request->f_name;
                 $profile->l_name=$request->l_name;
                 $profile->address=$request->address;
                 $profile->phone_number=$request->phone_number;
                 $profile->book_number=$request->book_number;
                 $profile->about=$request->about;
                 $profile->update();
                 return 'updated successfully';
             }else{
                 $profile->org_name=$request->org_name;
                 $profile->po_box=$request->po_box;
                 $profile->region=$request->region;
                 $profile->city=$request->city;
                 $profile->phone_number=$request->phone_number;
                 $profile->book_number=$request->book_number;
                 $profile->about=$request->about;
                 $profile->update();
                 return 'updated successfully';
             }
         }else{
             $imageName = time().'.' . explode('/', explode(':', substr($request->profile_image, 0, strpos($request->profile_image, ';')))[1])[1];
             \Image::make($request->profile_image)->fit(600,600)->save(public_path('../../frontend/public/profile_pics/').$imageName);

             $image1='../../frontend/public'.$profile->profile_image;
             unlink($image1);

             if($profile->usage==='personal'){
                 $profile->f_name=$request->f_name;
                 $profile->l_name=$request->l_name;
                 $profile->region=$request->region;
                 $profile->city=$request->city;
                 $profile->phone_number=$request->phone_number;
                 $profile->book_number=$request->book_number;
                 $profile->about=$request->about;
                 $profile->profile_image='/profile_pics/'.$imageName;
                 $profile->update();
                 return 'updated successfully';
             }else{
                 $profile->org_name=$request->org_name;
                 $profile->po_box=$request->po_box;
                 $profile->region=$request->region;
                 $profile->city=$request->city;
                 $profile->phone_number=$request->phone_number;
                 $profile->book_number=$request->book_number;
                 $profile->about=$request->about;
                 $profile->profile_image='/profile_pics/'.$imageName;
                 $profile->update();
                 return 'updated successfully';
             }

         }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function acutions($id){
        $acution=DB::table('posts')->where('user_id',$id)->count();
        return $acution;
    }
    public function destroy($id)
    {
        //
    }
}
