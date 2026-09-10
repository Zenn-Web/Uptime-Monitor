<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    public function monitor()
    {
        return $this->belongsTo(Monitor::class);
    }

}
