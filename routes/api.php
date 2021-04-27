<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ManageActionsController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignOutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\MessageController;

use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\BidController;
use App\Http\Controllers\Api\UserDashboardController;
use App\Http\Controllers\Api\BiddersController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['prefix'=>'auth','namespace'=>'Auth'],function(){
   Route::post('signin',[SignInController::class,'SignIn']);
   Route::post('signout',[SignOutController::class,'SignOUt']);
   Route::get('me',[MeController::class,'Me']);
   Route::post('register',[RegisterController::class,'Register']);
   Route::post('changeUserPassword',[RegisterController::class,'changePassword']);
});


Route::group(['prefix'=>'user','namespace'=>'Api'],function(){
    Route::get('Auctions',[PostController::class,'all_Acutions']);
    Route::get('AuctionforHome',[PostController::class,'AuctionForHomePage']);
    Route::post('create',[PostController::class,'store']);
    Route::get('user_products/{id}',[Postcontroller::class,'index']);
    Route::get('posts/{id}/product',[Postcontroller::class,'show']);
    Route::post('update/{id}',[PostController::class,'update']);
    Route::post('delete/{id}',[PostController::class,'destroy']);


    Route::get('post/product/{id}',[BidController::class,'index']);
    Route::post('Store_Offer',[BidController::class,'store']);
    Route::post('store_price_update_time/{id}',[BidController::class,'update']);
    Route::post('removeAuction/{id}',[BidController::class,'removeAuction']);
    Route::post('saveWinner',[BidController::class,'saveWinner']);
    Route::post('completePayment/{id}',[BidController::class,'completePayment']);

    Route::get('winnerProfile',[BiddersController::class,'winner']);

    Route::post('store_profile',[ProfileController::class,'store']);
    Route::post('updateProfile/{id}',[ProfileController::class,'update']);
    Route::get('num_acutions/{id}',[ProfileController::class,'acutions']);
    Route::get('password',[ProfileController::class,'index']);
    Route::get('myProfile/{id}',[ProfileController::class,'show']);

    Route::get('userPendingAcutions/{id}',[UserDashboardController::class,'pendingAcutions']);
    Route::get('userApprovedAcutions/{id}',[UserDashboardController::class,'approvedAcutions']);
    Route::get('userRejectedAcutions/{id}',[UserDashboardController::class,'rejectedAcutions']);
    Route::get('myCart/{id}',[UserDashboardController::class,'MyCart']);

    Route::get('auction_time/{id}',[PostController::class,'acutionDate']);
    Route::post('bookAuction',[BiddersController::class,'store']);
    Route::post('checkUserBooked',[BiddersController::class,'check']);
    Route::post('repost/{id}',[PostController::class,'repostAuction']);
});
Route::group(['prefix'=>'Admin','namespace'=>'Admin'],function(){
    Route::post('addCategory',[CategoryController::class,'store']);
    Route::get('categories',[CategoryController::class,'index']);
    Route::post('deleteCategory/{id}',[CategoryController::class,'destroy']);
    Route::get('Acutions',[ManageActionsController::class,'index']);
    Route::post('manageAcution/{id}',[ManageActionsController::class,'update']);
    Route::post('rejectAcution',[ManageActionsController::class,'reject']);
    Route::get('user/{id}',[MessageController::class,'index']);
    Route::get('refund',[ManageActionsController::class,'completedAuctions']);
});
Route::get('kasu',[ManageActionsController::class,'create']);


