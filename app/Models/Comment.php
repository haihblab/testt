<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Comment extends Model
{
    use SoftDeletes;
    use  Notifiable;

    protected $table = 'comments';
    protected $fillable = ['content', 'user_id', 'request_id', 'title'];

    public function request()
    {
        return $this->belongsTo('App\Models\Request', 'request_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
