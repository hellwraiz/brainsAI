<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;

    protected $primaryKey = 'type';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['content'];

    // In your Text model
    public function getRouteKeyName()
    {
        return 'type'; // Use 'type' instead of default 'id'
    }
}
