<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    protected $table='profiles';
    protected $fillable = ['user_id','org_name','f_name','l_name','phone_number','region','city','book_number','about','profile_image'
    ];
}
