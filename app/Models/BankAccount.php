<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BankAccount extends Model
{
    protected $table = 'bank_accounts';

    protected $guarded = [];

    public function incomes(): HasMany
    {
        return $this->hasMany(Income::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'bank_id');
    }



}
