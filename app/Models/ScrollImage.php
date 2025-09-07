<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrollImage extends Model
{
    
    use HasFactory;
    protected $fillable = ['img_url', 'order'];
}
