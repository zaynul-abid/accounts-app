<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobLocation extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
