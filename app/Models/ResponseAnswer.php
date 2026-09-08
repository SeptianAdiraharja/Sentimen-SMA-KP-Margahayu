<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseAnswer extends Model
{
    protected $guarded = [];

    protected $casts = [
        'tokens' => 'array',
        'filtered_tokens' => 'array',
        'sentiment_details' => 'array',
    ];

    public function response()
    {
        return $this->belongsTo(QuestionnaireResponse::class, 'response_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function aspect()
    {
        return $this->belongsTo(Aspect::class);
    }
}
