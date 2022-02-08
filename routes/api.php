<?php

use App\Http\Controllers\GameRoomController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth.api', 'prefix' => '/game-room'], function () {
    Route::post('/{id}/join', [GameRoomController::class, 'joinRoom']);
    Route::post('/start-game', [GameRoomController::class, 'startGame']);
    Route::post('/restart-game', [GameRoomController::class, 'restartGame']);
    Route::post('/select-domino/{id}', [GameRoomController::class, 'selectDomino']);
    Route::post('/select-extra-domino/{id}', [GameRoomController::class, 'selectExtraDomino']);
    Route::post('/place-domino/{id}', [GameRoomController::class, 'placeDomino']);
});
