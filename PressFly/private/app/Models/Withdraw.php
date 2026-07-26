<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    protected $guarded = ['id'];

    public const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
