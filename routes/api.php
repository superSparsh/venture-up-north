<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeDataController;
use App\Http\Controllers\Api\EditorUploadController;
use App\Http\Controllers\Api\LinkPreviewController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Frontend\EventController;
use App\Http\Controllers\Frontend\CollectionController;
use App\Http\Controllers\Frontend\ThingsToDoCategoryController;
use App\Http\Controllers\Frontend\TourTileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/home', [HomeDataController::class, 'index']);

Route::post('/uploads/editor-image', [EditorUploadController::class, 'store']);

Route::match(['GET', 'POST'], '/link-metadata', [LinkPreviewController::class, 'fetch']);

Route::get('/search', [SearchController::class, 'index']);

Route::get('/category/{slug}/listings', [ThingsToDoCategoryController::class, 'listingsFetch']);

Route::get('/events', [EventController::class, 'eventsFetch']);

Route::get('/tours', [TourTileController::class, 'toursFetch']);

Route::get('/collections', [CollectionController::class, 'collectionsFetch']);
