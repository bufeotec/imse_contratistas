<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\IntranetController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ComercialController;
use App\Http\Controllers\RecursoshumanosController;
use App\Http\Controllers\DespachosController;




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

route::get('/phpinfo', function(){
    phpinfo();
});
// XDEBUG
/* ----------------------------- RUTAS CONTROLLER INTRANET ---------------------------------*/
route::get('/',[IntranetController::class ,'intranet'])->name('intranet')->middleware('verifyUserStatus')->middleware('auth');
//route::get('test',[IntranetController::class ,'test'])->name('test');
route::get('perfil',[IntranetController::class ,'perfil'])->name('intranet.perfil')->middleware('verifyUserStatus')->middleware('auth');
/* ----------------------------- FIN RUTAS CONTROLLER INTRANET ---------------------------------*/


/* ----------------------------- RUTAS DEL LOGIN ------------------------------*/
route::get('login',[LoginController::class ,'login'])->name('login');

/* ----------------------------- FIN RUTAS DEL LOGIN ------------------------------*/

/* ----------------------------- RUTAS DE CONFIGURACIÓN ---------------------------------*/
Route::prefix('configuracion')->middleware('auth')->group(function () {
    /* MENÚ */
    route::get('/menus',[ConfigurationController::class ,'menus'])->name('configuracion.menus')->middleware('verifyUserStatus')->middleware('can:menus');
    route::get('/submenu',[ConfigurationController::class ,'submenu'])->name('configuracion.submenu')->middleware('verifyUserStatus')->middleware('can:submenu');
    route::get('/usuarios',[ConfigurationController::class ,'usuarios'])->name('configuracion.usuarios')->middleware('verifyUserStatus')->middleware('can:usuarios');
    route::get('/roles',[ConfigurationController::class ,'roles'])->name('configuracion.roles')->middleware('verifyUserStatus')->middleware('can:roles');
    route::get('/iconos',[ConfigurationController::class ,'iconos'])->name('configuracion.iconos')->middleware('verifyUserStatus')->middleware('can:iconos');
    route::get('/empresas',[ConfigurationController::class ,'empresas'])->name('configuracion.empresas')->middleware('verifyUserStatus')->middleware('can:empresas');
});

/* ----------------------------- RUTAS FINALES DE CONFIGURACIÓN ---------------------------------*/


//COMERCIAL
Route::prefix('Comercial')->middleware('auth')->group(function () {
    route::get('/clientes',[ComercialController::class ,'clientes'])->name('Comercial.clientes')->middleware('verifyUserStatus')->middleware('can:clientes');
});

//RECURSOS HUMANOS
Route::prefix('Recursoshumanos')->middleware('auth')->group(function () {
    route::get('/personales',[RecursoshumanosController::class ,'personales'])->name('Recursoshumanos.personales')->middleware('verifyUserStatus')->middleware('can:personales');
});

//DESPACHOS
Route::prefix('Despachos')->middleware('auth')->group(function () {
    route::get('/gestionar_despachos',[DespachosController::class ,'gestionar_despachos'])->name('Despachos.gestionar_despachos')->middleware('verifyUserStatus')->middleware('can:gestionar_despachos');
});
