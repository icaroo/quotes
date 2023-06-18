<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'age',
        'currency_id',
        'start_date',
        'end_date'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
