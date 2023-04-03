<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class grupos_tienen_profesor extends Model
{
    use HasFactory;
    protected $table = 'grupos_tienen_profesor';
}
