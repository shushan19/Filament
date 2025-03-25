<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
    ];

    public function country() : BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities() : HasMany
    {
        return $this->hasmany(City::class, 'state_id');
    }
}
