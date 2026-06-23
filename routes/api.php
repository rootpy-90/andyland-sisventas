<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Artículos
Route::get('/articulos',              'Api\ArticuloController@index');
Route::get('/articulos/stock-bajo',   'Api\ArticuloController@stockBajo');
Route::get('/articulos/{id}',         'Api\ArticuloController@show');

// Categorías
Route::get('/categorias',             'Api\CategoriaController@index');

// Ventas
Route::get('/ventas',                 'Api\VentaController@index');
Route::get('/ventas/{id}',            'Api\VentaController@show');

// Dashboard
Route::get('/dashboard/stats',                  'Api\DashboardController@stats');
Route::get('/dashboard/productos-mas-vendidos', 'Api\DashboardController@productosMasVendidos');
Route::get('/dashboard/ventas-por-mes',         'Api\DashboardController@ventasPorMes');
