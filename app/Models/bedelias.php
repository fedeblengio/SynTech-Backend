<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class bedelias extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable=[
        'Cedula_Bedelia',
        'cargo',
    ];

    public function usuario(){
        return $this->belongsTo(usuarios::class,'id', 'id');
    }


}
