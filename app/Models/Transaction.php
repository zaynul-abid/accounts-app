<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    /**
     * Get the income associated with the transaction.
     */
    public function income()
    {
        return $this->belongsTo(Income::class, 'income_id');
    }

    /**
     * Get the expense associated with the transaction.
     */
    public function expense()
    {
        return $this->belongsTo(Expense::class, 'expense_id');
    }

    /**
     * Get the bank associated with the transaction.
     */
    public function bank()
    {
        return $this->belongsTo(BankAccount::class, 'bank_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
