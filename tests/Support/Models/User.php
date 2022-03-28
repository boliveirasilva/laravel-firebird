<?php

namespace FirebirdTests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}