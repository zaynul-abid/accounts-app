<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberReport extends Model
{
    use SoftDeletes;

    protected $table = 'member_reports';

    protected $guarded = [];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function receiptAccount()
    {
        return $this->belongsTo(ReceiptAccount::class, 'receipt_account_id');
    }

    /**
     * Scopes
     */
    public function scopeByMember($query, $memberId)
    {
        return $query->where('member_id', $memberId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    public function scopeByTransactionType($query, $type)
    {
        return $query->where('transaction_type', $type);
    }

    /**
     * Accessors & Mutators
     */
    public function getNetAmountAttribute()
    {
        $debit = $this->debit ?? 0;
        $credit = $this->credit ?? 0;
        return $debit - $credit;
    }

    /**
     * Generate Receipt Number
     */
    public static function generateReceiptNo()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        return 'MR-' . $date . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public static function generateSlNo()
    {
        $last = self::whereNotNull('sl_no')->latest('id')->value('sl_no');
        $next = 1;

        if ($last && preg_match('/(\d+)$/', $last, $matches)) {
            $next = ((int) $matches[1]) + 1;
        }

        return 'sl-' . str_pad((string) $next, 2, '0', STR_PAD_LEFT);
    }
}
