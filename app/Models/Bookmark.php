<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookmark extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'url',
        'created_by_id'
    ];

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
