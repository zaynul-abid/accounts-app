<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReceiptAccount extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function reports()
    {
        return $this->hasMany(MemberReport::class, 'receipt_account_id');
    }
}

