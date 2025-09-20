<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class FormInput extends Model {

protected $casts = [
    'files' => 'array',
];


    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'files',
    ];
}
