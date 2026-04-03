<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['name', 'level_number', 'min_xp', 'max_xp', 'icon_path', 'color'];
}
