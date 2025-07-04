<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeType extends Model
{
    protected $table = 'income_types';

    protected $guarded = [];

    protected $casts = ['status' => 'boolean'];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }
}
