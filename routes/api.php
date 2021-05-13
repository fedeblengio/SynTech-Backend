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
    

    
    // JSON_encode() -> JSON , ARRAY 
    // base64_encode() -> pasa a base64 STRINGS ONLY

    // JSON_decode() -> JSON , ARRAY 
    // base64_decode() -> pasa de base64 a STRINGS  ONLY
    // @DISCOVER BY FEFECAST base64_encode(json_encode($json));
    $fecha1=Carbon::now();
    $fecha2=Carbon::now()->addMinutes(-2);

    if($fecha1->gt($fecha2)){

        return "ES MAYOR";
    }
    else{

        return "ES MENOR";

    };

});



Route::get('/1', function (){
    return  'hola';
})->middleware('verificar_token');




Route::get('/usuarios','App\Http\Controllers\usuariosController@index');
Route::get('/usuario','App\Http\Controllers\usuariosController@show');
Route::post('/usuario','App\Http\Controllers\usuariosController@create');
Route::delete('/usuario','App\Http\Controllers\usuariosController@destroy');
Route::put('/usuario','App\Http\Controllers\usuariosController@update');