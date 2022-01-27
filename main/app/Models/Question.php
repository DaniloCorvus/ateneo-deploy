<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $with = ['question_responses'];

    public function question_responses()
    {
        return $this->hasMany(QuestionResponse::class);
    }
}
