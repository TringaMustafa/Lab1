<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_id',
        'name',
        'phone',
        'date',
        'time',
        'end_time',
        'actual_end_time',
        'guests',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
    public function getEffectiveEndTimeAttribute()
    {
        return $this->actual_end_time ?? $this->end_time;
    }
}
