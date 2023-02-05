<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class TaskResponse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'task_id', 'description', 'notificate', 'price', 'performer_id','not_free'];

    protected $with = ['user', 'task'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function performer(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function boot ()
    {
        parent::boot();

        self::deleting(function (TaskResponse $response) {
            /** @var Task $task */
            $task = $response->task;
            if ($task !== null){
                if ((int)$task->status === Task::STATUS_IN_PROGRESS) {
                    $task->update(['status' => Task::STATUS_CANCELLED]);
                }
            }
        });
    }
}
