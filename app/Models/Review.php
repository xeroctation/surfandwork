<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reviews';
    protected $fillable = ['user_id','description','good_bad','reviewer_id','task_id', 'as_performer'];
    protected $with = ['user', 'reviewer','task'];

    const TOP_USER = 20;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public static function boot ()
    {
        parent::boot();
        self::deleting(function (Review $review) {
            $user = $review->user;
            if ($user) {
                switch (true){
                    case (int)$review->good_bad === 1 && $user->review_good > 0 :
                        $user->decrement('review_good');
                        $user->decrement('reviews');
                        break;
                    case $user->review_bad > 0 :
                        $user->decrement('review_bad');
                        $user->decrement('reviews');
                        break;
                }
            }
        });
    }
}
