<?php

namespace FirebirdTests\Support\Models;

use Illuminate\Database\Eloquent\Model;

class TestAutoIncrementUser extends \Firebird\Model
{
    protected $table = 'testbench_users';
    protected $primaryKey = 'ID';

    public $incrementing = false;
    // public $timestamps = false;
    protected $guarded = [];

}