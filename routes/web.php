<?php

use App\Http\Controllers\CrawlController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get("/test",[CrawlController::class,'index']);
