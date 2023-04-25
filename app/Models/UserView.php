<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property $id
 * @property $user_id ID of the user who viewed the profile
 * @property $performer_id ID of the performer whose profile was viewed
 * @property $created_at Viewed time
 */

class UserView extends Model {

    protected $table = 'user_views';
    protected $fillable = ['user_id','count'];
}
