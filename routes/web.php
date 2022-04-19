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
// Exportar clientes
Route::get('export-clientes', 'ExcelCSVController@exportClientesCSV')->name('export-clientes');

// Exportar reporte de ventas
Route::get('export-ventas', 'ExcelCSVController@exportVentasCSV')->name('export-ventas');

// Exportar inventario
Route::get('export-excel', 'ExcelCSVController@exportExcelCSV')->name('export-excel');
Route::post('import-excel', 'ExcelCSVController@importExcelCSV')->name('import-excel');

Route::get('/', 'Admin\HomeController@index')->name('home');

//Route::get('home', 'Admin\HomeController@index')->name('home');   // cargar el Dasboard después de haber iniciado sesión

//Rutas de Catalogo Productos
Route::resource('admin/productos','Admin\ProductoController')->parameters(['productos'=>'productos'])->names('admin.productos');
Route::get('filtro-productos','Admin\ProductoController@index')->name('filtro-productos');

// Clientes 
Route::resource('admin/clientes', 'Admin\ClientesController')->parameters(['clientes' => 'clientes'])->names('admin.clientes');

// Reportes
Route::get('admin/reportes', 'Admin\ReportController@periodSales')->name('admin.reportes');
Route::post('/confirmacion-p', 'Admin\ReportController@confirmacion_p');
Route::get('filtro-ventas','Admin\ReportController@periodSales')->name('filtro-ventas');
Route::get('admin/poco-stock', 'Admin\ReportController@poco_stock')->name('admin.poco-stock');
Route::get('admin/pocas-ventas', 'Admin\ReportController@pocas_ventas')->name('admin.pocas-ventas');

//Facturas
Route::resource('admin/facturas', 'Admin\FacturasController')->parameters(['facturas' => 'facturas'])->names('admin.facturas');

// Días no laborables
Route::resource('admin/festivos', 'Admin\HolidayController')->names('admin.festivos');

/*// Días de vacaciones
Route::resource('/vacaciones', 'VacationController')->names('vacaciones');
Route::match(array('GET', 'POST'), '/vacaciones/getregistros', 'VacationController@getRegistros')->name('vacaciones.getregistros');*/
Route::match(array('GET', 'POST'), '/home/vacacionando', 'Admin\HomeController@getBloqVac')->name('home.vacacionando');

// Personal
Route::resource('admin/personal', 'Admin\CrewController')->names('admin.personal');

Route::get('/contacto_delete/{id}','Admin\CrewController@delete_item_cart');


