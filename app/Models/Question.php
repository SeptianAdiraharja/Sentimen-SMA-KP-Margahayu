<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $guarded = [];

    public function aspect()
    {
        return $this->belongsTo(Aspect::class);
    }

    public function answers()
    {
        return $this->hasMany(ResponseAnswer::class);
    }
}
