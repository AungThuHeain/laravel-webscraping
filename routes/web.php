<?php

use App\Http\Controllers\Api\MainController;
use App\Http\Controllers\CrawlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/test",[CrawlController::class,'index']);

Route::get("/test2",[MainController::class,'index']);
