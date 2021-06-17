<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['title','link','description','pub_date','author','image_link'];
}
