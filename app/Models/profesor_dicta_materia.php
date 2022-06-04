<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class profesor_dicta_materia extends Model
{
    use HasFactory;
    protected $table = 'profesor_dicta_materia';
    use SoftDeletes;
}
