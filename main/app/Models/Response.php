<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    public function question_response()
    {
        return $this->hasMany(QuestionResponse::class);
    }
}
