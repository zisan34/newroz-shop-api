<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'created_at' => 'datetime:Y-m-d h:i:s A',
        'products' => 'json'
    ];

    public function notes()
    {
    	return $this->morphMany(ModelNotes::class, 'noteable');
    }
}
