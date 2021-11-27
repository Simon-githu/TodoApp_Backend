<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'title',
        'date',
        'user_id',
        'completed',
        'active',
    ];
// Many todos are assigned to their specific user
    public function user() {
        return $this->belongsTo(User::class);
    }
}
