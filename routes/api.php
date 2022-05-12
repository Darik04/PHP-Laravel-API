<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

use App\Http\Controllers\API\ConcertController;
use App\Http\Controllers\API\TicketController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Concerts
Route::get('v1/concerts', [ConcertController::class, 'getConcerts']);
Route::get('v1/concerts/{id}', [ConcertController::class, 'getConcert']);
Route::get('v1/concerts/{concertId}/shows/{showId}/seating', [ConcertController::class, 'getSeating']);

Route::post('v1/concerts/{concertId}/shows/{showId}/reservation', [ConcertController::class, 'reservation']);
Route::post('v1/concerts/{concertId}/shows/{showId}/booking', [ConcertController::class, 'booking']);

// Tickets
Route::post('v1/tickets', [TicketController::class, 'getTickets']);
Route::post('v1/tickets/{id}/cancel', [TicketController::class, 'cancel']);
