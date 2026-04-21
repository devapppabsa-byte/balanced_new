<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Norma extends Model
{
    protected $table= "norma";
    protected $fillable = [
        'nombre', 
        'descripcion',
        'ponderacion', 
        'id_departamento', 
        'meta_minima', 
        'meta_esperada',
        'id_objetivo_perspectiva',
        'autor'
    ];


    public function apartados(){

        return $this->hasMany(ApartadoNorma::class, 'id_norma');

    }

        
    public function departamento(){
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }


}
