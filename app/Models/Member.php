<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $casts = [
        'date' => 'date',
        'dob' => 'date',
    ];

    /**
     * Relationships
     */
    public function house()
    {
        return $this->belongsTo(HouseCreation::class, 'house_id');
    }

    public function relation()
    {
        return $this->belongsTo(Relation::class);
    }

    public function qualification()
    {
        return $this->belongsTo(Qualification::class);
    }

    public function islamicQualification()
    {
        return $this->belongsTo(IslamicQualification::class);
    }

    public function occupation()
    {
        return $this->belongsTo(Occupation::class);
    }

    public function jobLocation()
    {
        return $this->belongsTo(JobLocation::class);
    }

    public function reports()
    {
        return $this->hasMany(MemberReport::class, 'member_id');
    }

    /**
     * Accessors
     */
    public function getAgeAttribute()
    {
        return $this->dob ? \Carbon\Carbon::parse($this->dob)->age : null;
    }
}
