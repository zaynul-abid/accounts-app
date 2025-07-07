<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{


    protected $table = 'suppliers';

    protected $guarded = [];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function transactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
