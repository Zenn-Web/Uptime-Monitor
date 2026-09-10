<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;       
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[Fillable(['name', 'url', 'expected_status'])]
class Monitor extends Model
{
    use HasFactory;
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    protected $casts = [
        'is_up' => 'boolean',
        'last_checked_at' => 'datetime',
    ];
    
}
