<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'recommended_items',
    ];

    protected $casts = [
        'recommended_items' => 'array', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
