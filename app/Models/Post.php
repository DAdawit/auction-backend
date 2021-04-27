<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class post extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','price','category','description','image',
    ];
    public function user(){
        return $this->belongsTo('App\Models\User');
    }
    public function bids(){
        return $this->hasMany('App\Models\Bid');
    }
}
