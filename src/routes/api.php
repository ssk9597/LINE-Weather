<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

/************************ LINE *************************/
Route::post("/line/message", "LINEController@sendMessage");

Route::get("/", function () {
  return "Hello World!";
});
