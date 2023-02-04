<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

/**
 * @return array //Value Returned
 *@property $user
 * @property $performer
 * @property $reviews
 * @property object $category
 * @property $id
 * @property $name
 * @property $reviews_count
 * @property $responses_count
 * @property $status
 * @property $remote
 * @property $budget
 * @property $oplata
 * @property $addresses
 * @property $photos
 * @property $user_id
 * @property $category_id
 * @property $phone
 * @property $views
 * @property $start_date
 * @property $end_date End Date
 * @property $verify_code
 * @property $verify_expiration
 * @property $performer_id
 * @property $created_at
 * @property \Illuminate\Support\Carbon|mixed $deleted_at
 * @property mixed $deleted_by
 */
class Task extends Model
{

    use HasFactory, SoftDeletes;

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    protected $table = 'tasks';

    protected $mapping = [
        'properties' => [
            'id' => [
                "type" => "id"
            ],
            'name' => [
                "type" => "string"
            ],
        ]
    ];

    public const STATUS_OPEN = 1;
    public const STATUS_RESPONSE = 2;
    public const STATUS_IN_PROGRESS = 3;
    public const STATUS_COMPLETE = 4;
    public const STATUS_NOT_COMPLETED = 5;
    public const STATUS_CANCELLED = 6;

    protected $guarded = [];


}
