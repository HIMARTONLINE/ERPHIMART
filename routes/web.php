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

Route::get('export-excel', 'ExcelCSVController@exportExcelCSV')->name('export-excel');


Route::get('/', 'Admin\HomeController@index')->name('home');

//Route::get('home', 'Admin\HomeController@index')->name('home');   // cargar el Dasboard después de haber iniciado sesión

//Rutas de Catalogo Productos
Route::resource('admin/productos','Admin\ProductoController')->parameters(['productos'=>'productos'])->names('admin.productos');
Route::get('filtro-productos','Admin\ProductoController@index')->name('filtro-productos');

//Rutas de ventas de producto
Route::resource('admin/ventas', 'Admin\VentasController')->parameters(['ventas' => 'ventas'])->names('admin.ventas');
Route::get('buscar', 'Admin\VentasController@index')->name('buscar.mes');
Route::get('admin/reportes', 'Admin\ReportController@periodSales')->name('admin.reportes');
Route::post('/confirmacion-p', 'Admin\ReportController@confirmacion_p');
Route::get('filtro-ventas','Admin\ReportController@periodSales')->name('filtro-ventas');