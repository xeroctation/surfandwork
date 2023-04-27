<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Traits\Translatable;

/**
 * @property $id
 * @property $task_id  task id
 * @property $location $location
 * @property $latitude Address latitude
 * @property $longitude Address long
 * @property $default
 * @property $created_at  Address created time
 * @property $updated_at  Address updated time
 * @return array //Value Returned
 */

class Address extends Model
{
    use HasFactory;

    protected $guarded = [];


    public function task()
    {
        $this->belongsTo(Task::class);
    }



}
