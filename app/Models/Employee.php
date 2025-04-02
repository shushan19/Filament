<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
        use HasFactory;

        protected $guarded = [];

        protected $fillable = [
            'first_name',
            'last_name',
            'middle_name',
            'address',
            'zip_code',
            'birth_of_date',
            'country_id',
            'state_id',
            'city_id',
            'department_id',
        ];

        public function country()
        {
            return $this->belongsTo(Country::class, 'country_id');
        }
        public function state() : BelongsTo
        {
            return $this->belongsTo(State::class, 'state_id');
        }
        public function city() : BelongsTo
        {
            return $this->belongsTo(City::class, 'city_id');
        }
        public function department() : BelongsTo
        {
            return $this->belongsTo(Department::class, 'department_id');
        }

        public  function team(): BelongsTo
        {
            return $this->belongsTo(Team::class);
        }
}
