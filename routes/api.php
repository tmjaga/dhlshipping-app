<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DhlController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/dhl', [DhlController::class,'makeShipment']);

Route::get('/geocountries', function () {
    $response = Http::get('http://api.geonames.org/countryInfoJSON', [
        'username' => 'tmjaga',
        'style' => 'SHORT'
    ]);

    return $response->body();
});

Route::get('/geocities', function (Request $request) {
    $url = 'http://api.geonames.org/searchJSON?username=tmjaga&featureCode=PPLA&featureCode=PPLC&country='
        . $request->country .'&style=SHORT&maxRows=1000';

    $response = Http::get($url);

    return $response->body();
});

Route::get('/geocode', function (Request $request) {
    $url = 'http://api.geonames.org/postalCodeSearchJSON?username=tmjaga&country='.$request->country.
        '&placename='.$request->place.'&style=SHORT&maxRows=1';

    $response = Http::get($url);

    return $response->body();
});
