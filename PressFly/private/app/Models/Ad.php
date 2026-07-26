<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast to native types.
     *
     * @see https://laravel.com/docs/master/eloquent-mutators#attribute-casting
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'size' => 'array',
    ];
}
