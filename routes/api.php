<?php

use Illuminate\Http\Request;
use App\Models\usuarios;
use Illuminate\Support\Facades\Route;
use LdapRecord\Models\ActiveDirectory\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers;
use Carbon\Carbon;
use App\Models\materia;

use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Route::middleware(['verificar_token'])->group(function () { */
    //USUARIOS
    Route::get('/usuario','App\Http\Controllers\usuariosController@index');
    Route::get('/usuario/{id}','App\Http\Controllers\usuariosController@show');
    Route::post('/usuario','App\Http\Controllers\usuariosController@store');
    Route::delete('/usuario/{id}','App\Http\Controllers\usuariosController@destroy');
    Route::put('/usuario/{id}','App\Http\Controllers\usuariosController@update');

    Route::get('/usuario/{id}/imagen-perfil','App\Http\Controllers\usuariosController@traerImagen');
    Route::post('/usuario/{id}/imagen-perfil','App\Http\Controllers\usuariosController@cambiarImagen'); // IF METHOD TYPE PUT ->hasFile() doesn't works correctly

    //MATERIAS

    Route::get('/materia','App\Http\Controllers\agregarMateriaController@index');
    Route::get('/materia/{id}','App\Http\Controllers\agregarMateriaController@show');
    Route::post('/materia','App\Http\Controllers\agregarMateriaController@store');
    Route::put('/materia/{id}','App\Http\Controllers\agregarMateriaController@update');
    Route::delete('/materia/{id}','App\Http\Controllers\agregarMateriaController@destroy');

    // GRUPOS
    Route::get('/grupo','App\Http\Controllers\gruposController@index');
    Route::get('/grupo/{id}', 'App\Http\Controllers\gruposController@show');
    Route::get('/grupo/{id}/alumnos','App\Http\Controllers\gruposController@AlumnosNoPertenecenGrupo');
    Route::post('/grupo','App\Http\Controllers\gruposController@store');
    Route::delete('/grupo/{id}','App\Http\Controllers\gruposController@destroy');
    Route::delete('/grupo/{id}/alumno/{idAlumno}','App\Http\Controllers\gruposController@eliminarAlumnoGrupo');
    Route::delete('/grupo/{id}/profesor/{idProfesor}','App\Http\Controllers\gruposController@eliminarProfesorGrupo');
    Route::put('/grupo/{id}','App\Http\Controllers\gruposController@update');

    //CARRERAS
    Route::get('/carrera', 'App\Http\Controllers\CarreraController@index');
    Route::get('/carrera/{id}', 'App\Http\Controllers\CarreraController@show');
    Route::post('/carrera', 'App\Http\Controllers\CarreraController@create');
    Route::put('/carrera/{id}', 'App\Http\Controllers\CarreraController@update');
    Route::delete('/carrera/{id}', 'App\Http\Controllers\CarreraController@destroy');
    Route::delete('/carrera/{id}/grado/{idGrado}','App\Http\Controllers\CarreraController@destroyGrado');
    //GRADO
    Route::put('/grado/{id}', 'App\Http\Controllers\GradoController@update');
    Route::get('/grado/{id}', 'App\Http\Controllers\GradoController@show');

    // PROFESOR
    Route::put('/profesor/{id}', 'App\Http\Controllers\ProfesorController@update');
    Route::get('/profesor/{id}', 'App\Http\Controllers\ProfesorController@show');
    Route::get('/profesor', 'App\Http\Controllers\ProfesorController@index');
/*     Route::get('/profesor','App\Http\Controllers\profesorDictaMateriaController@index'); */
    Route::get('/profesor/{id}/materias','App\Http\Controllers\profesorDictaMateriaController@materiasNoPertenecenProfesor');
/*     Route::post('/profesor','App\Http\Controllers\profesorDictaMateriaController@agregarListaDeProfesoresMateria');
    Route::delete('/profesor','App\Http\Controllers\profesorDictaMateriaController@destroy'); */

    //AlUMNOS
    Route::get('/alumno','App\Http\Controllers\AlumnoController@index');
    Route::get('/alumno/{id}','App\Http\Controllers\AlumnoController@show');
    Route::put('/alumno/{id}','App\Http\Controllers\AlumnoController@update');
    Route::get('/alumno/{id}/grupos','App\Http\Controllers\AlumnoController@gruposNoPertenecenAlumno');

    //BEDELIAS
    Route::get('/bedelia','App\Http\Controllers\BedeliaController@index');
    Route::get('/bedelia/{id}','App\Http\Controllers\BedeliaController@show');
    Route::put('/bedelia/{id}','App\Http\Controllers\BedeliaController@update');


   

/* }); */

Route::post('/login','App\Http\Controllers\loginController@connect');

Route::get('/test', function (){
    $materia = User::all();
    return $materia;
});
// FTP TRAER ARCHIVOS
Route::get('/traerArchivo','App\Http\Controllers\MaterialPublicoController@traerArchivo')->middleware('verificar_token');

Route::get('/historial','App\Http\Controllers\usuariosController@getFullHistory');


Route::put('/contrasenia','App\Http\Controllers\usuariosController@cambiarContrasenia')->middleware('verificar_token');

Route::get('/materiaSinGrupo','App\Http\Controllers\gruposTienenProfesorController@traerMateriasSinGrupo')->middleware('verificar_token');


//ALUMNOS
/* Route::get('/alumnos','App\Http\Controllers\agregarUsuarioGrupoController@index')->middleware('verificar_token');

Route::post('/alumno','App\Http\Controllers\agregarUsuarioGrupoController@store')->middleware('verificar_token');

Route::delete('/alumno','App\Http\Controllers\agregarUsuarioGrupoController@destroy')->middleware('verificar_token'); */


Route::get('/profesorMateria','App\Http\Controllers\gruposTienenProfesorController@mostrarProfesorMateria')->middleware('verificar_token');

// CURSOS

Route::post('/curso','App\Http\Controllers\gruposTienenProfesorController@store')->middleware('verificar_token');

Route::get('/curso','App\Http\Controllers\gruposTienenProfesorController@show')->middleware('verificar_token');

Route::delete('/curso','App\Http\Controllers\gruposTienenProfesorController@destroy')->middleware('verificar_token');


Route::get('/grupo-materia','App\Http\Controllers\gruposTienenProfesorController@index')->middleware('verificar_token');

//
Route::get('/integrantes-curso','App\Http\Controllers\gruposTienenProfesorController@listarIntegrantesDeUnGrupo')->middleware('verificar_token');


//FORO
Route::delete('/grupoForo','App\Http\Controllers\gruposTienenProfesorController@eliminarProfesorGrupoForo')->middleware('verificar_token');
Route::delete('/foro','App\Http\Controllers\gruposTienenProfesorController@eliminarForo')->middleware('verificar_token');

//NOTICIAS
Route::post('/noticia','App\Http\Controllers\MaterialPublicoController@store')->middleware('verificar_token');
Route::get('/noticia','App\Http\Controllers\MaterialPublicoController@index')->middleware('verificar_token');
Route::delete('/noticia','App\Http\Controllers\MaterialPublicoController@destroy')->middleware('verificar_token');



