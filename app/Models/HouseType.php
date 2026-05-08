<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class HouseType extends Model
{

        use softDeletes;

        protected $guarded = [];
}
