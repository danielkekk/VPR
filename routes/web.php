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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/accessdenied', 'AccessDeniedController@index')->name('accessdenied');

Auth::routes();

/*Admin*/

//csv feltöltés
Route::get('/loadkepviseloadatokview', 'AdminController@loadKepviseloAdatokView')->name('loadkepviseloadatokview')->middleware('admin');
Route::post('/loadkepviseloadatok', 'AdminController@loadKepviseloAdatok')->name('loadkepviseloadatok')->middleware('admin');



Route::get('/ujkepviselo', 'AdminController@ujKepviselo')->name('ujkepviselo')->middleware('admin');
Route::get('/ujogykepviselo', 'AdminController@ujOgyKepviselo')->name('ujogykepviselo')->middleware('admin');
Route::post('/createkepviselo', 'AdminController@createKepviselo')->name('createkepviselo')->middleware('admin');
Route::post('/createogykepviselo', 'AdminController@createOgyKepviselo')->name('createogykepviselo')->middleware('admin');
Route::get('/ujkepviseloposzt', 'AdminController@ujKepviseloPoszt')->name('ujkepviseloposzt')->middleware('admin');
Route::get('/ujogykepviseloposzt', 'AdminController@ujOgyKepviseloPoszt')->name('ujogykepviseloposzt')->middleware('admin');
Route::post('/createkepviseloposzt', 'AdminController@createKepviseloPoszt')->name('createkepviseloposzt')->middleware('admin');
Route::post('/createogykepviseloposzt', 'AdminController@createOgyKepviseloPoszt')->name('createogykepviseloposzt')->middleware('admin');
Route::get('/kepviseloposztok', 'AdminController@getKepviseloPosztok')->name('kepviseloposztok')->middleware('admin');
Route::get('/ogykepviseloposztok', 'AdminController@getOgyKepviseloPosztok')->name('ogykepviseloposztok')->middleware('admin');
Route::get('/deletekepviseloposzt/{id}', 'AdminController@deleteKepviseloPoszt')->name('deletekepviseloposzt')->middleware('admin');
Route::get('/deleteogykepviseloposzt/{id}', 'AdminController@deleteOgyKepviseloPoszt')->name('deleteogykepviseloposzt')->middleware('admin');
Route::get('/editkepviseloposzt/{id}', 'AdminController@editKepviseloPosztView')->name('editkepviseloposzt')->middleware('admin');
Route::post('/editkepviseloposztsave', 'AdminController@editKepviseloPoszt')->name('editkepviseloposztsave')->middleware('admin');
Route::get('/deletekepviseloposztbyid/{kpid}/{pid}', 'AdminController@deleteKepviseloPosztById')->name('deletekepviseloposztbyid')->middleware('admin');
Route::get('/editogykepviseloposzt/{id}', 'AdminController@editOgyKepviseloPosztView')->name('editogykepviseloposzt')->middleware('admin');
Route::post('/editogykepviseloposztsave', 'AdminController@editOgyKepviseloPoszt')->name('editogykepviseloposztsave')->middleware('admin');
Route::get('/deleteogykepviseloposztbyid/{kpid}/{pid}', 'AdminController@deleteOgyKepviseloPosztById')->name('deleteogykepviseloposztbyid')->middleware('admin');
Route::get('/ujfrakcio', 'AdminController@ujFrakcio')->name('ujfrakcio')->middleware('admin');
Route::post('/createfrakcio', 'AdminController@createFrakcio')->name('createfrakcio')->middleware('admin');
Route::get('/deletefrakcio/{id}', 'AdminController@deleteFrakcio')->name('deletefrakcio')->middleware('admin');

Route::get('/ujorszmedia', 'AdminController@ujOrszMedia')->name('ujorszmedia')->middleware('admin');
Route::post('/createorszmedia', 'AdminController@createOrszMedia')->name('createorszmedia')->middleware('admin');
Route::get('/ujorszmediaposzt', 'AdminController@ujOrszMediaPoszt')->name('ujorszmediaposzt')->middleware('admin');
Route::post('/createorszmediaposzt', 'AdminController@createOrszMediaPoszt')->name('createorszmediaposzt')->middleware('admin');
Route::get('/orszmediaposztok', 'AdminController@getOrszMediaPosztok')->name('orszmediaposztok')->middleware('admin');
Route::get('/deleteorszmediaposzt/{id}', 'AdminController@deleteOrszMediaPoszt')->name('deleteorszmediaposzt')->middleware('admin');
Route::get('/editorszmediaposzt/{id}', 'AdminController@editOrszMediaPosztView')->name('editorszmediaposzt')->middleware('admin');
Route::post('/editorszmediaposztsave', 'AdminController@editOrszMediaPoszt')->name('editorszmediaposztsave')->middleware('admin');
Route::get('/deleteorszmediaposztbyid/{kpid}/{pid}', 'AdminController@deleteOrszMediaPosztById')->name('deleteorszmediaposztbyid')->middleware('admin');


/*FrakcioAdmin*/
Route::get('/fra-ujkepviselo', 'FrakcioAdminController@ujKepviselo')->name('fra-ujkepviselo')->middleware('frakcioadmin');
Route::post('/fra-createkepviselo', 'FrakcioAdminController@createKepviselo')->name('fra-createkepviselo')->middleware('frakcioadmin');
Route::get('/fra-ujkepviseloposzt', 'FrakcioAdminController@ujKepviseloPoszt')->name('fra-ujkepviseloposzt')->middleware('frakcioadmin');
Route::post('/fra-createkepviseloposzt', 'FrakcioAdminController@createKepviseloPoszt')->name('fra-createkepviseloposzt')->middleware('frakcioadmin');
Route::get('/fra-kepviseloposztok', 'FrakcioAdminController@getKepviseloPosztok')->name('fra-kepviseloposztok')->middleware('frakcioadmin');
Route::get('/fra-deletekepviseloposzt/{id}', 'FrakcioAdminController@deleteKepviseloPoszt')->name('fra-deletekepviseloposzt')->middleware('frakcioadmin');
Route::get('/fra-editkepviseloposzt/{id}', 'FrakcioAdminController@editKepviseloPosztView')->name('fra-editkepviseloposzt')->middleware('frakcioadmin');
Route::post('/fra-editkepviseloposztsave', 'FrakcioAdminController@editKepviseloPoszt')->name('fra-editkepviseloposztsave')->middleware('frakcioadmin');
Route::get('/fra-deletekepviseloposztbyid/{kpid}/{pid}', 'FrakcioAdminController@deleteKepviseloPosztById')->name('fra-deletekepviseloposztbyid')->middleware('frakcioadmin');

Route::get('/fra-ujlocalmedia', 'FrakcioAdminController@ujLocalMedia')->name('fra-ujlocalmedia')->middleware('frakcioadmin');
Route::post('/fra-createlocalmedia', 'FrakcioAdminController@createLocalMedia')->name('fra-createlocalmedia')->middleware('frakcioadmin');
Route::get('/fra-ujlocalmediaposzt', 'FrakcioAdminController@ujLocalMediaPoszt')->name('fra-ujlocalmediaposzt')->middleware('frakcioadmin');
Route::post('/fra-createlocalmediaposzt', 'FrakcioAdminController@createLocalMediaPoszt')->name('fra-createlocalmediaposzt')->middleware('frakcioadmin');
Route::get('/fra-localmediaposztok', 'FrakcioAdminController@getLocalMediaPosztok')->name('fra-localmediaposztok')->middleware('frakcioadmin');
Route::get('/fra-deletelocalmediaposzt/{id}', 'FrakcioAdminController@deleteLocalMediaPoszt')->name('fra-deletelocalmediaposzt')->middleware('frakcioadmin');
Route::get('/fra-editlocalmediaposzt/{id}', 'FrakcioAdminController@editLocalMediaPosztView')->name('fra-editlocalmediaposzt')->middleware('frakcioadmin');
Route::post('/fra-editlocalmediaposztsave', 'FrakcioAdminController@editLocalMediaPoszt')->name('fra-editlocalmediaposztsave')->middleware('frakcioadmin');
Route::get('/fra-deletelocalmediaposztbyid/{kpid}/{pid}', 'FrakcioAdminController@deleteLocalMediaPosztById')->name('fra-deletelocalmediaposztbyid')->middleware('frakcioadmin');


/*frakciovezeto cuccok*/
Route::get('/statisztika', 'FrakciovezetoController@getKepviselokStatisztika')->name('statisztika')->middleware('frakciovezeto');
Route::post('/kepviselo-poszt', 'StatisztikaController@kepviseloPoszt')->name('kepviselo-poszt')->middleware('frakciovezeto');
Route::get('/statisztika-havi', 'FrakciovezetoController@getHaviStatisztika')->name('statisztika-havi')->middleware('frakciovezeto');
Route::post('/statisztika-havi-kimutatas', 'StatisztikaController@haviKimutatas')->name('statisztika-havi-kimutatas')->middleware('frakciovezeto');
Route::get('/statisztika-frakciovezeto', 'FrakciovezetoController@getFrakciovezetoStatisztika')->name('statisztika-frakciovezeto')->middleware('frakciovezeto');
Route::get('/statisztika-frakciovezeto-poszt/{kepviselo}', 'StatisztikaController@frakciovezetoPoszt')->name('statisztika-frakciovezeto-poszt')->middleware('frakciovezeto');
Route::get('/statisztika-ogykepviselo', 'FrakciovezetoController@getOgyKepviselokStatisztika')->name('statisztika-ogykepviselo')->middleware('frakciovezeto');
Route::get('/statisztika-ogykepviselo-poszt/{kepviselo}', 'StatisztikaController@ogyKepviseloPoszt')->name('statisztika-ogykepviselo-poszt')->middleware('frakciovezeto');
Route::get('/statisztika-orszmedia', 'FrakciovezetoController@getOrszMediakStatisztika')->name('statisztika-orszmedia')->middleware('frakciovezeto');
Route::get('/statisztika-orszmedia-poszt/{orszmedia}', 'StatisztikaController@orszMediaPoszt')->name('statisztika-orszmedia-poszt')->middleware('frakciovezeto');
Route::get('/statisztika-localmedia', 'FrakciovezetoController@getLocalMediakStatisztika')->name('statisztika-localmedia')->middleware('frakciovezeto');
Route::get('/statisztika-localmedia-poszt/{localmedia}', 'StatisztikaController@localMediaPoszt')->name('statisztika-localmedia-poszt')->middleware('frakciovezeto');
Route::get('/statisztika-top', 'FrakciovezetoController@getTopStatisztika')->name('statisztika-top')->middleware('frakciovezeto');
Route::post('/statisztika-top-kimutatas', 'StatisztikaController@topKimutatas')->name('statisztika-top-kimutatas')->middleware('frakciovezeto');


/*userguest*/
Route::get('/admin', 'AdminController@index')->name('admin')->middleware('admin');
Route::get('/frakcioadmin', 'FrakcioAdminController@index')->name('frakcioadmin')->middleware('frakcioadmin');
Route::get('/frakciovezeto', 'FrakciovezetoController@index')->name('frakciovezeto')->middleware('frakciovezeto');
Route::get('/kepviselo', 'KepviseloController@index')->name('kepviselo')->middleware('kepviselo');
Route::get('/home', 'HomeController@index')->name('home');


/*authed user*/
Route::get('/newpassword', 'Auth\JelszoController@index')->name('newpassword')->middleware('auth');
Route::post('/changepassword', 'Auth\JelszoController@changePassword')->name('changepassword')->middleware('auth');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
