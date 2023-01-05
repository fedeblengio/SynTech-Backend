<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $fillable=[
        'nombre',
        'plan',
        'categoria',
    ];

    use HasFactory;
    public function grado(){
        return $this->hasMany(Grado::class);
    }
    
}
