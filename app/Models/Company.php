<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';

    protected $guarded = [];


    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function expenseTypes()
    {
        return $this->hasMany(ExpenseType::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function incomeTypes()
    {
        return $this->hasMany(IncomeType::class);
    }

    public function openingBalances()
    {
        return $this->hasMany(OpeningBalance::class);
    }

    public function suppliers()
    {
        return $this->hasMany(Supplier::class);
    }

    public function supplierTransactions()
    {
        return $this->hasMany(SupplierTransaction::class);
    }
}
