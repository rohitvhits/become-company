<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class FeedBackQuestion extends Model
{
    protected $table = "client_review_feedback_question";
    protected $guarded = ["id"];
}
