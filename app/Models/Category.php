<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    use SoftDeletes;
    use  Notifiable;

    protected $fillable = ['name', 'user_id', 'status',];

    public function requests()
    {
        return $this->hasMany('App\Models\Request', 'category_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
