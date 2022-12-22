<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class profesores extends Model
{
    protected $fillable=[
        "cedula_profesor"
    ];
    use HasFactory;
    use SoftDeletes;
}
