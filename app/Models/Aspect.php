<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aspect extends Model
{
    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function responseAnswers()
    {
        return $this->hasMany(ResponseAnswer::class);
    }
}
