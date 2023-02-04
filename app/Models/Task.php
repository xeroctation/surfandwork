<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;


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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function custom_field_values()
    {
        return $this->hasMany(CustomFieldsValue::class);
    }
}
