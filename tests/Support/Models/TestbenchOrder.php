<?php

namespace FirebirdTests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestbenchOrder extends Model
{
    use SoftDeletes;

    public $incrementing = false;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(TestbenchUser::class);
    }
}