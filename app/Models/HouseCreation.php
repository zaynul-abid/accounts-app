<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class HouseCreation extends Model
{

    use softDeletes;

    protected $guarded = [];

    protected $casts = [
        'registration_date' => 'datetime',     // or 'date' if you don't need time
        // 'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
    ];

    public function place(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');

    }

    public function mahallu(): BelongsTo
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function houseType(): BelongsTo
    {
        return $this->belongsTo(HouseType::class, 'house_type_id');
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'house_id');
    }
}
