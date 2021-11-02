<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;

class Request extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'category_id',
        'due_date',
        'manager_id',
        'user_id',
        'status',
        'priority',
        'content'
    ];

    /**
     * Relationships
     *
     * @return array
     */
    public function manager()
    {
        return $this->belongsTo('App\Models\User', 'manager_id');
    }

    /**
     * Relationships
     *
     * @return array
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Relationships
     *
     * @return array
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    /**
     * Relationships
     *
     * @return array
     */

    public function requestComments()
    {
        return $this->hasMany('App\Models\CommentHistory', 'request_id');
    }

    /**
     * Relationships
     *
     * @return array
     */

    public function comments()
    {
        return $this->hasMany('App\Models\Comment', 'request_id');
    }
}
