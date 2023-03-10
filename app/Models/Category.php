<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCG\Voyager\Traits\Translatable;



class Category extends Model
{
    use HasFactory,SoftDeletes;
    use Translatable;
    protected array $translatable = ['name'];

    protected $table = "categories";

    public function parent(){
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function childs(){
        return $this->hasMany(self::class,'parent_id','id');
    }

    public function custom_fields(){
        return $this->hasMany(CustomField::class);
    }

    public function customFieldsInCustom(){
        return $this->hasMany(CustomField::class)->where('route', CustomField::ROUTE_CUSTOM);
    }
}
