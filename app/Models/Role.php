<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name'
    ];

    /**
     * Relationships
     *
     * @return array
     */
    public function users()
    {
        return $this->hasMany('App\Models\User', 'role_id');
    }
}
