<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['status', 'detected_at', 'resloved_at', 'note'])]
class Incident extends Model
{
    
protected function casts(): array {
    return [
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
        ];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }

}
