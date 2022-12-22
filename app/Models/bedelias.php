<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class bedelias extends Model
{
    protected $fillable=[
        "cedula_bedelia",
        "cargo"
    ];
    use HasFactory;
    use SoftDeletes;
}
