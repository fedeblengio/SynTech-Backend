<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class usuarios extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function bedelias(){
        return $this->belongsTo(bedelias::class,'id', 'id');
    }
}
