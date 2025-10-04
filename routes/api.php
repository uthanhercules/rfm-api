<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'api'], function () {
  Route::group(['prefix' => 'sync'], function () {
    Route::post('/clients', [App\Http\Controllers\SyncController::class, 'syncClients']);
    Route::post('/clients/recent', [App\Http\Controllers\SyncController::class, 'syncRecentClients']);
  });

  Route::group(['prefix' => 'clients'], function () {
    Route::get('/categories', [App\Http\Controllers\ClientsController::class, 'countClientsGroupedByCategory']);
    Route::get('/categories/{category_code}', [App\Http\Controllers\ClientsController::class, 'getClientByCategory']);
  });

  Route::get('/categories', [App\Http\Controllers\CategoriesController::class, 'listCategories']);

  Route::group(['prefix' => 'summary'], function () {
    Route::post('/generate', [App\Http\Controllers\SyncController::class, 'generateClientOrderSummary']);
  });

  // Tests
  Route::get('/ping', function () {
    return response()->json(['message' => 'Pong!']);
  });
});
