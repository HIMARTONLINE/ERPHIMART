<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', 'Admin\HomeController@index')->name('home');

//Route::get('home', 'Admin\HomeController@index')->name('home');   // cargar el Dasboard después de haber iniciado sesión

//Rutas de Catalogo Productos
Route::resource('admin/productos','Admin\ProductoController')->parameters(['productos'=>'productos'])->names('admin.productos');
Route::get('filtro-productos','Admin\ProductoController@index')->name('filtro-productos');

Route::get('/home', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
