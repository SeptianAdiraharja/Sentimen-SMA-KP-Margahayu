<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireResponse extends Model
{
    protected $guarded = [];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function answers()
    {
        return $this->hasMany(ResponseAnswer::class, 'response_id');
    }
}
