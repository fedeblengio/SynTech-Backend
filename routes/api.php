<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\User;
use App\Http\Controllers;
use Carbon\Carbon;
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


Route::post('/login','App\Http\Controllers\loginController@connect');


Route::get('/test', function (){

 return "Estas en /test";
});


//USUARIOS
Route::get('/usuarios','App\Http\Controllers\usuariosController@index');
Route::get('/usuario','App\Http\Controllers\usuariosController@show');

Route::post('/usuario','App\Http\Controllers\usuariosController@create')->middleware('verificar_token');
Route::delete('/usuario','App\Http\Controllers\usuariosController@destroy')->middleware('verificar_token');
Route::put('/usuario','App\Http\Controllers\usuariosController@update')->middleware('verificar_token');


Route::post('/usuariosintoken','App\Http\Controllers\usuariosController@create');

//GRUPOS
Route::get('/grupos','App\Http\Controllers\gruposController@index');
Route::get('/grupo','App\Http\Controllers\gruposController@show');

Route::post('/grupo','App\Http\Controllers\gruposController@create');
Route::delete('/grupo','App\Http\Controllers\gruposController@destroy');
Route::put('/grupo','App\Http\Controllers\gruposController@update');

//ALUMNOS 
Route::get('/alumnos','App\Http\Controllers\agregarUsuarioGrupoController@index');

Route::post('/alumno','App\Http\Controllers\agregarUsuarioGrupoController@store');

Route::delete('/alumno','App\Http\Controllers\agregarUsuarioGrupoController@destroy');

//MATERIAS 

Route::get('/materias','App\Http\Controllers\agregarMateriaController@index');
Route::get('/materia','App\Http\Controllers\agregarMateriaController@show');

Route::post('/materia','App\Http\Controllers\agregarMateriaController@store');

Route::put('/materia','App\Http\Controllers\agregarMateriaController@update');

Route::delete('/materia','App\Http\Controllers\agregarMateriaController@destroy');


// PROFESOR
Route::get('/profesor','App\Http\Controllers\profesorDictaMateriaController@index');

Route::post('/profesor','App\Http\Controllers\profesorDictaMateriaController@store');

Route::delete('/profesor','App\Http\Controllers\profesorDictaMateriaController@destroy');




// CURSOS 

Route::post('/curso','App\Http\Controllers\gruposTienenProfesorController@store');

Route::get('/curso','App\Http\Controllers\gruposTienenProfesorController@show');

Route::delete('/curso','App\Http\Controllers\gruposTienenProfesorController@destroy');