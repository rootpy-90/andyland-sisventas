<?php

// Raíz → tienda pública
Route::get('/', function () {
    return redirect('/tienda');
});

// Autenticación estándar de Laravel
Auth::routes();
Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

// Recuperación de contraseña sin email
Route::get('/recuperar-password',         'RecuperarPasswordController@showForm')->name('recuperar.form');
Route::post('/recuperar-password',        'RecuperarPasswordController@verificar')->name('recuperar.verificar');
Route::get('/recuperar-password/nueva',   'RecuperarPasswordController@showNueva')->name('recuperar.nueva');
Route::post('/recuperar-password/nueva',  'RecuperarPasswordController@guardar')->name('recuperar.guardar');

/*
|--------------------------------------------------------------------------
| TIENDA PÚBLICA (sin login)
|--------------------------------------------------------------------------
*/
Route::get('/tienda', 'TiendaController@index')->name('tienda');
Route::get('/tienda/articulo/{id}', 'TiendaController@show');

/*
|--------------------------------------------------------------------------
| TIENDA PROTEGIDA (requiere login)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => 'auth'], function () {
    Route::get('/tienda/checkout',              'TiendaController@checkout')->name('tienda.checkout');
    Route::post('/tienda/pedido',               'TiendaController@store');
    Route::get('/tienda/comprobante/{id}',      'TiendaController@comprobantePago')->name('comprobante.pago');
    Route::post('/completar-perfil',            'TiendaController@completarPerfil');

    Route::get('/tienda/perfil',                    'PerfilController@index')->name('perfil');
    Route::post('/tienda/perfil',                  'PerfilController@update')->name('perfil.update');
    Route::post('/tienda/perfil/password',          'PerfilController@cambiarPassword')->name('perfil.password');
    Route::post('/tienda/perfil/email',             'PerfilController@actualizarEmail')->name('perfil.email');
    Route::delete('/tienda/perfil',                 'PerfilController@eliminarCuenta')->name('perfil.eliminar');
    Route::get('/tienda/mis-compras',                   'PerfilController@misCompras')->name('mis.compras');
    Route::post('/tienda/compra/{id}/cancelar',         'PerfilController@cancelarCompra')->name('compra.cancelar');
    Route::post('/tienda/compra/{id}/comprobante',      'PerfilController@subirComprobante')->name('compra.comprobante');
});

/*
|--------------------------------------------------------------------------
| PANEL ADMINISTRADOR (requiere login + isAdmin)
|--------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', 'isAdmin']], function () {

    Route::get('/home', 'HomeController@index')->name('home');

    Route::resource('almacen/categoria', 'CategoriaController');
    Route::resource('almacen/articulo',  'ArticuloController');
    Route::resource('ventas/cliente',    'ClienteController');
    Route::resource('compras/proveedor', 'ProveedorController');
    Route::resource('compras/ingreso',   'IngresoController');
    Route::resource('ventas/venta',      'VentaController');
    Route::resource('seguridad/usuario', 'UsuarioController');
    Route::resource('registros/registro','RegistroController');

    Route::get('ventas/venta/cambiarEstado/{id}', 'VentaController@cambiarEstado');
    Route::get('ventas/venta/comprobante/{id}',   'VentaController@comprobante');
    Route::get('ventas/reporte',                  'VentaController@reporte');

    Route::get('admin/fechas-entrega',                  'FechaEntregaController@index');
    Route::get('admin/fechas-entrega/informe',          'FechaEntregaController@informe');

    // Módulo Caja
    Route::get('admin/caja',                            'CajaController@index');
    Route::post('admin/caja/abrir',                     'CajaController@abrir');
    Route::post('admin/caja/cerrar/{id}',               'CajaController@cerrar');
    Route::post('admin/caja/movimiento',                'CajaController@addMovimiento');
    Route::delete('admin/caja/movimiento/{id}',         'CajaController@deleteMovimiento');
    Route::get('admin/caja/imprimir/{id}',              'CajaController@imprimir');
    Route::post('admin/fechas-entrega',                 'FechaEntregaController@store');
    Route::put('admin/fechas-entrega/{id}',             'FechaEntregaController@update');
    Route::post('admin/fechas-entrega/{id}/toggle',     'FechaEntregaController@toggleActivo');
    Route::delete('admin/fechas-entrega/{id}',          'FechaEntregaController@destroy');
});
