<?php

namespace FirebirdTests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestbenchUser extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $primaryKey = 'ID';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(TestbenchOrder::class);
    }
}